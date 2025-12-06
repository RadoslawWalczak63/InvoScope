<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceType;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
        ]);

        $invoices = QueryBuilder::for(Invoice::class)
            ->defaultSort('-issue_date')
            ->allowedSorts(['number', 'type', 'issue_date', 'created_at'])
            ->allowedFilters([
                AllowedFilter::partial('number'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('issue_date'),
                AllowedFilter::callback('buyer', function (Builder $query, $value) {
                    $query->whereHas('buyer', function (Builder $q) use ($value) {
                        $q->where('company_name', 'like', "%{$value}%")
                            ->orWhere('first_name', 'like', "%{$value}%")
                            ->orWhere('last_name', 'like', "%{$value}%");
                    });
                }),
            ])
            ->with(['buyer', 'seller'])
            ->where('user_id', $request->user()->id)
            ->paginate($request->input('per_page', 20))
            ->withQueryString();

        return Inertia::render('Invoice/Index', [
            'invoices' => InvoiceResource::collection($invoices),
            'invoiceTypes' => InvoiceType::cases(),
            'state' => [
                'filters' => $request->input('filter', []),
                'sort' => $request->input('sort', '-issue_date'),
            ],
        ]);
    }

    public function show(Request $request, Invoice $invoice): Response
    {
        $invoice->load(['buyer', 'seller', 'items']);

        return Inertia::render('Invoice/Show', [
            'invoice' => new InvoiceResource($invoice),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        // TODO: Fix updateing invoice items

        $request->validate([
            'number' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'type' => ['required', new Enum(InvoiceType::class)],
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.description' => 'required|string|max:1000',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_amount' => 'required|numeric|min:0',
            'items.*.discount' => 'required|numeric|min:0',
        ]);

        $invoice->update([
            'number' => $request->input('number'),
            'issue_date' => $request->input('issue_date'),
            'type' => $request->input('type'),
        ]);

        $invoice->items()->delete();

        foreach ($request->input('items') as $itemData) {
            $invoice->items()->create([
                'name' => $itemData['name'],
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
                'tax_amount' => $itemData['tax_amount'],
                'discount' => $itemData['discount'],
            ]);
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
