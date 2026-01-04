<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $request->validate([
            'currency' => ['sometimes', new Enum(Currency::class)],
            'startDate' => ['sometimes', 'date'],
            'endDate' => ['sometimes', 'date'],
        ]);

        $userId = $request->user()->id;
        $startDate = $request->input('startDate', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('endDate', Carbon::now()->endOfMonth()->toDateString());

        $availableCurrencies = Invoice::query()
            ->where('user_id', $userId)
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->distinct()
            ->pluck('currency')
            ->map(fn ($value) => $value->value);

        $currency = $availableCurrencies->intersect(Arr::wrap($request->input('currency')))->first() ?: $availableCurrencies->first() ?: Currency::PLN->value;

        $aggregates = Invoice::where('user_id', $userId)
            ->where('currency', $currency)
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw('invoices.type, SUM(invoice_items.net_total) as total_amount')
            ->groupBy('invoices.type')
            ->pluck('total_amount', 'type');

        $income = $aggregates->get(InvoiceType::INCOME->value) ?? 0;
        $expense = $aggregates->get(InvoiceType::EXPENSE->value) ?? 0;

        $overdue = Invoice::where('user_id', $userId)
            ->where('currency', $currency)
            ->where('status', InvoiceStatus::OVERDUE)
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->selectRaw('SUM(invoice_items.net_total) as total_overdue')
            ->value('total_overdue') ?? 0;

        $stats = [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'profit' => (float) ($income - $expense),
            'overdue' => (float) $overdue,
        ];

        $monthlyData = Invoice::where('user_id', $userId)
            ->where('currency', $currency)
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw("
                DATE_FORMAT(issue_date, '%Y-%m') as month_key,
                DATE_FORMAT(issue_date, '%b') as month_label,
                invoices.type,
                SUM(invoice_items.net_total) as total
            ")
            ->groupBy('month_key', 'month_label', 'type')
            ->orderBy('month_key')
            ->get();

        $months = $monthlyData->pluck('month_label')->unique()->values()->toArray();
        $monthlyPerformance = [
            'labels' => $months,
            'income' => $this->mapChartData($months, $monthlyData, InvoiceType::INCOME),
            'expense' => $this->mapChartData($months, $monthlyData, InvoiceType::EXPENSE),
        ];

        $expenseCategoriesData = Invoice::query()
            ->where('invoices.user_id', $userId)
            ->where('invoices.type', InvoiceType::EXPENSE)
            ->where('invoices.currency', $currency)
            ->whereBetween('invoices.issue_date', [$startDate, $endDate])
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('entities as sellers', 'invoices.seller_id', '=', 'sellers.id')
            ->selectRaw("
                COALESCE(NULLIF(sellers.company_name, ''), CONCAT(sellers.first_name, ' ', sellers.last_name)) AS name,
                SUM(invoice_items.net_total) AS total
            ")
            ->groupBy('sellers.id', 'sellers.company_name', 'sellers.first_name', 'sellers.last_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $typeDistribution = Invoice::where('user_id', $userId)
            ->where('currency', $currency)
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        $recentInvoices = Invoice::where('user_id', $userId)
            ->where('currency', $currency)
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->with(['buyer', 'seller'])
            ->withSum('items', 'net_total')
            ->orderBy('issue_date', 'desc')
            ->limit(5)
            ->get()
            ->toBase()
            ->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'client_name' => $invoice->type === InvoiceType::INCOME ? $invoice->buyer->name : $invoice->seller->name,
                'issue_date' => $invoice->issue_date->toDateString(),
                'amount' => (float) $invoice->items_sum_net_total,
                'status' => $invoice->status,
                'type' => $invoice->type->value,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'charts' => [
                'monthly' => $monthlyPerformance,
                'expenses' => [
                    'labels' => $expenseCategoriesData->pluck('name'),
                    'data' => $expenseCategoriesData->pluck('total'),
                ],
                'status' => [
                    'labels' => $typeDistribution->pluck('type'),
                    'data' => $typeDistribution->pluck('count'),
                ],
            ],
            'recentInvoices' => $recentInvoices,
            'selectedCurrency' => $currency,
            'currencies' => $availableCurrencies,
            'filters' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
        ]);
    }

    private function mapChartData(array $labels, $dataCollection, InvoiceType $type): array
    {
        return array_map(function ($label) use ($dataCollection, $type) {
            $record = $dataCollection->first(fn ($item) => $item->month_label === $label && $item->type === $type);

            return $record ? (float) $record->total : 0;
        }, $labels);
    }
}
