<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceTemplate;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;

class PreviewInvoiceController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice): Response
    {
        $request->validate([
            'template' => ['required', 'string', new Enum(InvoiceTemplate::class)],
            'download' => ['sometimes', 'boolean'],
        ]);

        $invoice->load(['buyer', 'seller', 'items']);

        $view = 'invoices.'.Str::lower($request->input('template'));
        $fileName = 'invoice_'.preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '',
            Str::snake($invoice->number)
        );

        $pdf = Pdf::loadView($view, ['invoice' => $invoice]);
        $pdf->setPaper('a4');

        if ($request->boolean('download')) {
            return $pdf->download("{$fileName}.pdf");
        }

        return $pdf->stream("{$fileName}.pdf");
    }
}
