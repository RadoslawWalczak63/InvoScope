<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceTemplate;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class PreviewInvoiceController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice): Response|RedirectResponse
    {
        $request->validate([
            'template' => ['required', 'string', new Enum(InvoiceTemplate::class)],
            'download' => ['sometimes', 'boolean'],
        ]);

        $invoice->load(['buyer', 'seller', 'items']);

        if ($invoice->file_path) {
            return response()->redirectToRoute('invoices.download', [
                'download' => $request->boolean('download'),
                'invoice' => $invoice->id,
            ]);
        }

        // If the invoice was already downloaded with a specific template, use that template
        if ($invoice->template) {
            $request->merge([
                'template' => $invoice->template->value,
            ]);
        }

        $view = 'invoices.'.Str::lower($request->input('template'));
        $fileName = 'invoice_'.preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '',
            Str::snake($invoice->number)
        );

        $pdf = Pdf::loadView($view, ['invoice' => $invoice]);
        $pdf->setPaper('a4');

        if ($request->boolean('download')) {
            $invoice->update([
                'template' => $request->input('template'),
            ]);

            return $pdf->download("{$fileName}.pdf");
        }

        return $pdf->stream("{$fileName}.pdf");
    }
}
