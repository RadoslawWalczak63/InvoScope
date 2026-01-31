<?php

namespace App\Http\Controllers;

use App\Enums\Currency;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
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
        $startDate = $request->input('startDate', Carbon::now()->startOfMonth()->subMonth()->toDateString());
        $endDate = $request->input('endDate', Carbon::now());

        $availableCurrencies = Invoice::query()
            ->where('user_id', $userId)
            ->distinct()
            ->pluck('currency');

        $currency = $availableCurrencies->contains($request->input('currency'))
            ? $request->input('currency')
            : ($availableCurrencies->first() ?? Currency::USD->value);

        $baseQuery = Invoice::where('invoices.user_id', $userId)
            ->where('invoices.currency', $currency)
            ->whereBetween('invoices.issue_date', [$startDate, $endDate]);

        $totals = (clone $baseQuery)
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw('
                SUM(CASE WHEN type = ? THEN invoice_items.net_total ELSE 0 END) as income,
                SUM(CASE WHEN type = ? THEN invoice_items.net_total ELSE 0 END) as expense
            ', [InvoiceType::INCOME, InvoiceType::EXPENSE])
            ->first();

        $dailyData = (clone $baseQuery)
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw('
                DATE(invoices.issue_date) as date,
                SUM(CASE WHEN type = ? THEN invoice_items.net_total ELSE 0 END) as income,
                SUM(CASE WHEN type = ? THEN invoice_items.net_total ELSE 0 END) as expense
            ', [InvoiceType::INCOME, InvoiceType::EXPENSE])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartData = [
            'labels' => [],
            'income' => [],
            'expense' => [],
            'net_cumulative' => [],
        ];

        $period = CarbonPeriod::create($startDate, $endDate);
        $runningTotal = 0;

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $dayData = $dailyData->get($formattedDate);

            $inc = $dayData ? (float) $dayData->income : 0;
            $exp = $dayData ? (float) $dayData->expense : 0;
            $runningTotal += ($inc - $exp);

            $chartData['labels'][] = $date->format('M d');
            $chartData['income'][] = $inc;
            $chartData['expense'][] = $exp;
            $chartData['net_cumulative'][] = $runningTotal;
        }

        $monthlyStats = (clone $baseQuery)
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw("
                DATE_FORMAT(invoices.issue_date, '%Y-%m') as month_key,
                DATE_FORMAT(invoices.issue_date, '%b') as month_label,
                SUM(CASE WHEN type = ? THEN invoice_items.net_total ELSE 0 END) as income,
                SUM(CASE WHEN type = ? THEN invoice_items.net_total ELSE 0 END) as expense
            ", [InvoiceType::INCOME, InvoiceType::EXPENSE])
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key')
            ->get();

        $topExpenses = (clone $baseQuery)
            ->where('invoices.type', InvoiceType::EXPENSE)
            ->join('entities', 'invoices.seller_id', '=', 'entities.id')
            ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->selectRaw("
                COALESCE(NULLIF(entities.company_name, ''), CONCAT(entities.first_name, ' ', entities.last_name)) as name,
                SUM(invoice_items.net_total) as total
            ")
            ->groupBy('entities.id', 'name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentInvoices = (clone $baseQuery)
            ->select('invoices.*')
            ->with(['buyer', 'seller'])
            ->withSum('items', 'net_total')
            ->orderBy('invoices.issue_date', 'desc')
            ->limit(7)
            ->get()
            ->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'client_name' => $invoice->type === InvoiceType::INCOME ? $invoice->buyer->company_name ?? $invoice->buyer->first_name : $invoice->seller->company_name ?? $invoice->seller->first_name,
                'issue_date' => $invoice->issue_date->toDateString(),
                'amount' => (float) $invoice->items_sum_net_total,
                'status' => $invoice->status,
                'type' => $invoice->type->value,
            ]);

        return Inertia::render('Dashboard', [
            'kpis' => [
                'income' => (float) ($totals->income ?? 0),
                'expense' => (float) ($totals->expense ?? 0),
                'profit' => (float) (($totals->income ?? 0) - ($totals->expense ?? 0)),
            ],
            'chart' => $chartData,
            'monthlyChart' => [
                'labels' => $monthlyStats->pluck('month_label'),
                'income' => $monthlyStats->pluck('income'),
                'expense' => $monthlyStats->pluck('expense'),
            ],
            'expenseBreakdown' => [
                'labels' => $topExpenses->pluck('name'),
                'data' => $topExpenses->pluck('total'),
            ],
            'recentInvoices' => $recentInvoices,
            'filters' => [
                'currency' => $currency,
                'currencies' => $availableCurrencies->values(),
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
        ]);
    }
}
