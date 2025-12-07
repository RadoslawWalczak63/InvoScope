<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceType;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $userId = $request->user()->id;

        $aggregates = Invoice::where('user_id', $userId)
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw('
                invoices.type,
                SUM(invoice_items.net_total) as total_amount
            ')
            ->groupBy('invoices.type')
            ->pluck('total_amount', 'type');

        $income = $aggregates->get(InvoiceType::INCOME->value) ?? 0;
        $expense = $aggregates->get(InvoiceType::EXPENSE->value) ?? 0;

        $activeClients = Invoice::where('user_id', $userId)
            ->where('type', InvoiceType::INCOME)
            ->distinct('buyer_id')
            ->count('buyer_id');

        $stats = [
            'income' => (float) $income,
            'expense' => (float) $expense,
            'profit' => (float) ($income - $expense),
            'active_clients' => $activeClients,
        ];

        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();

        $monthlyData = Invoice::where('user_id', $userId)
            ->where('issue_date', '>=', $sixMonthsAgo)
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
        $monthlyIncome = $this->mapChartData($months, $monthlyData, InvoiceType::INCOME);
        $monthlyExpense = $this->mapChartData($months, $monthlyData, InvoiceType::EXPENSE);

        $monthlyPerformance = [
            'labels' => $months,
            'income' => $monthlyIncome,
            'expense' => $monthlyExpense,
        ];

        $expenseCategoriesData = Invoice::query()
            ->where('invoices.user_id', $userId)
            ->where('invoices.type', InvoiceType::EXPENSE)
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('entities as sellers', 'invoices.seller_id', '=', 'sellers.id')
            ->selectRaw("
                COALESCE(
                    NULLIF(sellers.company_name, ''),
                    CONCAT(sellers.first_name, ' ', sellers.last_name)
                ) AS name,
                SUM(invoice_items.net_total) AS total
            ")
            ->groupBy(
                'sellers.id',
                'sellers.company_name',
                'sellers.first_name',
                'sellers.last_name'
            )
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $expenseCategories = [
            'labels' => $expenseCategoriesData->pluck('name'),
            'data' => $expenseCategoriesData->pluck('total'),
        ];

        $typeDistribution = Invoice::where('user_id', $userId)
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        $invoiceStatus = [
            'labels' => $typeDistribution->pluck('type'),
            'data' => $typeDistribution->pluck('count'),
        ];

        $recentInvoices = Invoice::where('user_id', $userId)
            ->with(['buyer', 'seller'])
            ->withSum('items', 'net_total')
            ->orderBy('issue_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function (Invoice $invoice) {
                // TODO: Move to Resource
                return [
                    'id' => $invoice->id,
                    'number' => $invoice->number,
                    'client_name' => $invoice->type === InvoiceType::INCOME
                        ? $invoice->buyer->name
                        : $invoice->seller->name,
                    'issue_date' => $invoice->issue_date->toDateString(),
                    'amount' => (float) $invoice->items_sum_net_total,
                    'status' => $invoice->status,
                    'type' => $invoice->type->value,
                ];
            });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'charts' => [
                'monthly' => $monthlyPerformance,
                'expenses' => $expenseCategories,
                'status' => $invoiceStatus,
            ],
            'recentInvoices' => $recentInvoices,
        ]);
    }

    private function mapChartData(array $labels, $dataCollection, InvoiceType $type): array
    {
        return array_map(function ($label) use ($dataCollection, $type) {
            $record = $dataCollection->first(function ($item) use ($label, $type) {

                return $item->month_label === $label && $item->type === $type;
            });

            return $record ? (float) $record->total : 0;
        }, $labels);
    }
}
