<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadInvoiceController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice)
    {
        $disk = Storage::disk('local');
        abort_if(! $invoice->file_path || ! $disk->exists($invoice->file_path), 404, 'Invoice file not found.');
        $fileExtension = pathinfo($invoice->file_path, PATHINFO_EXTENSION);

        $fileName = 'invoice_'.preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '',
            Str::snake($invoice->number)
        ).'.'.$fileExtension;

        return $disk->download($invoice->file_path, $fileName);
    }
}
