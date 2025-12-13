<?php

namespace App\Http\Controllers;

use App\Console\Commands\ProcessInvoiceImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UploadInvoiceController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png,bmp,tiff',
            'model_id' => 'required|string',
        ]);

        foreach ($request->file('files') as $file) {
            $request->user()->queuedJobs()->create([
                'job' => ProcessInvoiceImport::class,
                'arguments' => [
                    'file_path' => $file->store('invoices'),
                    'model_id' => $request->model_id,
                ],
            ]);
        }

        return response()
            ->redirectToRoute('invoices.index')
            ->with('status', 'Invoices are being processed and will appear shortly.');
    }
}
