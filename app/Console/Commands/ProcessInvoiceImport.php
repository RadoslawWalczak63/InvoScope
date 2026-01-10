<?php

namespace App\Console\Commands;

use App\Enums\EntityType;
use App\Enums\InvoiceStatus;
use App\Models\Entity;
use App\Models\Invoice;
use App\Models\QueuedJob;
use App\Services\CopilotService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Imagick;
use Spatie\PdfToText\Pdf;
use Throwable;

class ProcessInvoiceImport extends Command
{
    protected $signature = 'invoice:process {--queuedJobId=}';

    protected $description = 'Process invoice import';

    private ?QueuedJob $queuedJob = null;

    private ?CopilotService $copilotService = null;

    public function handle(): void
    {
        $this->copilotService = app(CopilotService::class);

        $this->queuedJob = QueuedJob::findOrFail($this->option('queuedJobId'));
        $this->queuedJob->started();

        [$success, $imagePath, $pdfText] = $this->getImage();

        if (! $success) {
            $this->queuedJob->failed();

            return;
        }

        $this->queuedJob->addLog('Processing image: '.$imagePath);

        try {
            $this->processImage($imagePath, $pdfText);

            $this->queuedJob->finished();
            $this->queuedJob->addLog(
                'Invoice imported in '.
                $this->queuedJob->finished_at->diffForHumans(
                    $this->queuedJob->started_at,
                    short: true,
                    parts: 3,
                )
            );
        } catch (Throwable $e) {
            report($e);
            $this->queuedJob->addLog('Error processing invoice: '.$e->getMessage());
            $this->queuedJob->failed();
        }
    }

    private function getImage(): array
    {
        $filePath = Storage::path($this->queuedJob->arguments->file_path);
        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'bmp', 'tiff'])) {
            return [true, $filePath, null];
        }

        $basePdfName = pathinfo($filePath, PATHINFO_FILENAME);
        $directory = pathinfo($filePath, PATHINFO_DIRNAME);

        $imagePath = $directory.'/'.$basePdfName.'.png';

        try {
            $imagick = new Imagick;
            $imagick->setResolution(300, 300);
            $imagick->readImage($filePath.'[0]');
            $imagick->setImageFormat('png');
            $imagick->writeImage($imagePath);
            $imagick->clear();
        } catch (Exception $e) {
            $this->queuedJob->addLog('Failed to extract images from PDF: '.$e->getMessage());

            return [false, null];
        }

        $this->queuedJob->addLog('Extracted image from PDF to: '.$imagePath);

        return [true, $imagePath, Pdf::getText($filePath)];
    }

    /**
     * @throws Throwable
     */
    private function processImage(string $imageFullPath, ?string $pdfText): void
    {
        $imageBase64 = 'data:image/png;base64,'.base64_encode(file_get_contents($imageFullPath));

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an expert data extraction assistant. Extract all invoice data exactly as it appears on the image. Do not attempt to guess.',
            ],
            [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => 'Extract the data from this invoice.'.($pdfText ? ' Here is the extracted text from the PDF: '.$pdfText : '')],
                    ['type' => 'image_url', 'image_url' => ['url' => $imageBase64]],
                ],
            ],
        ];

        $response = $this->copilotService->getResponse(
            $this->queuedJob->arguments->model_id,
            $messages,
            $this->copilotService->getInvoiceJsonScheme(),
        );

        $content = data_get($response, 'choices.0.message.content');

        if (! $content) {
            throw new Exception('Empty response received from AI service.');
        }

        $invoiceData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to decode AI JSON response: '.json_last_error_msg());
        }

        logger($invoiceData);
        $this->saveInvoiceToDatabase($invoiceData);
    }

    /**
     * @throws Throwable
     */
    private function saveInvoiceToDatabase(array $data): void
    {
        DB::transaction(function () use ($data) {
            $buyer = $this->resolveEntity($data['buyer_details']);
            $seller = $this->resolveEntity($data['seller_details']);

            $invoice = Invoice::updateOrCreate(
                [
                    'number' => $data['number'],
                ],
                [
                    'user_id' => $this->queuedJob->user_id,
                    'buyer_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'issue_date' => $data['issue_date'],
                    'due_date' => $data['due_date'],
                    'paid_date' => $data['paid_date'],
                    'currency' => $data['currency'],
                    'type' => $data['type'],
                    'status' => $data['status'] ?? InvoiceStatus::DRAFT,
                    'file_path' => $this->queuedJob->arguments->file_path,
                    'bank_account_number' => $data['bank_account_number'],
                ]
            );

            $invoice->items()->delete();

            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'sku' => $item['sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'price' => $item['price'],
                    'tax_amount' => $item['tax_amount'],
                    'discount' => $item['discount'],
                ]);
            }

            $this->queuedJob->addLog("Invoice {$invoice->number} saved successfully with ".count($data['items']).' items.');
        });
    }

    /**
     * @throws Throwable
     */
    private function resolveEntity(array $details): Entity
    {
        if (empty($details['name'])) {
            throw new Exception('Cannot resolve entity: Name is missing.');
        }

        if (! empty($details['tax_id'])) {
            $entity = Entity::query()
                ->where('tax_id', $details['tax_id'])
                ->where('user_id', $this->queuedJob->user_id)
                ->first();

            if ($entity) {
                return $entity;
            }
        }

        $entity = Entity::query()
            ->where('user_id', $this->queuedJob->user_id)
            ->where(function ($query) use ($details) {
                $query
                    ->where('company_name', 'LIKE', $details['name'])
                    ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', $details['name']);
            })
            ->first();

        if ($entity) {
            return $entity;
        }

        return Entity::create([
            'user_id' => $this->queuedJob->user_id,
            'type' => EntityType::COMPANY,
            'company_name' => $details['name'],
            'tax_id' => $details['tax_id'] ?? null,
            'address_line_1' => $details['address'] ?? null,
            'city' => $this->extractCity($details['address'] ?? ''),
        ]);
    }

    private function extractCity(string $address): ?string
    {
        if (empty($address)) {
            return null;
        }

        $parts = explode(',', $address);

        return count($parts) > 1 ? trim(end($parts)) : null;
    }
}
