<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Http\Resources\EntityResource;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\CopilotService;
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
    public function index(Request $request, CopilotService $copilotService): Response
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
        ]);

        $invoices = QueryBuilder::for(Invoice::class)
            ->defaultSort('-issue_date')
            ->allowedSorts(['number', 'type', 'issue_date', 'due_date', 'paid_date', 'created_at'])
            ->allowedFilters([
                AllowedFilter::partial('number'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('issue_date'),
                AllowedFilter::exact('due_date'),
                AllowedFilter::exact('paid_date'),
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

        $models = $copilotService
            ->getModels()
            ->filter(function ($model) {
                return in_array('image', $model->supported_input_modalities);
            })
            ->map(function ($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->name,
                ];
            })
            ->values();

        return Inertia::render('Invoice/Index', [
            'invoices' => InvoiceResource::collection($invoices),
            'entities' => EntityResource::collection($request->user()->entities),
            'models' => $models,
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
            'entities' => EntityResource::collection($request->user()->entities),
            'statuses' => InvoiceStatus::cases(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'number' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'paid_date' => 'nullable|date',
            'type' => ['required', new Enum(InvoiceType::class)],
            'status' => ['required', new Enum(InvoiceStatus::class)],
            'buyer_id' => 'required|exists:entities,id',
            'seller_id' => 'required|exists:entities,id',
            'items' => 'sometimes|array',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string|max:1000',
            'items.*.sku' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'nullable|string|max:100',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_amount' => 'required|numeric|min:0',
            'items.*.discount' => 'required|numeric|min:0',
        ]);

        $status = $request->input('status');
        if ($request->filled('paid_date')) {
            $status = InvoiceStatus::PAID;
        }

        $invoice->update([
            'number' => $request->input('number'),
            'issue_date' => $request->input('issue_date'),
            'due_date' => $request->input('due_date'),
            'paid_date' => $request->input('paid_date'),
            'type' => $request->input('type'),
            'status' => $status,
            'buyer_id' => $request->input('buyer_id'),
            'seller_id' => $request->input('seller_id'),
        ]);

        $inputItems = collect($request->input('items'));
        $inputItemIds = $inputItems->pluck('id')->filter()->toArray();

        $invoice->items()->whereNotIn('id', $inputItemIds)->delete();

        foreach ($inputItems as $itemData) {
            $invoice->items()->updateOrCreate(
                ['id' => $itemData['id'] ?? null],
                [
                    'name' => $itemData['name'],
                    'description' => $itemData['description'] ?? '',
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'price' => $itemData['price'],
                    'tax_amount' => $itemData['tax_amount'],
                    'discount' => $itemData['discount'],
                ]
            );
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'type' => ['required', new Enum(InvoiceType::class)],
            'buyer_id' => 'required|exists:entities,id',
            'seller_id' => 'required|exists:entities,id',
        ]);

        $invoice = Invoice::create($validated);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function destroy(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
