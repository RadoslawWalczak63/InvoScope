<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header-bg {
            background-color: #1e293b;
            color: #fff;
            padding: 40px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-meta {
            margin-top: 10px;
            font-size: 12px;
            color: #94a3b8;
        }

        .container {
            padding: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .w-half {
            width: 50%;
            vertical-align: top;
        }

        .w-full {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }

        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .address-block {
            line-height: 1.6;
            font-size: 13px;
        }

        .items-table {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .items-table th {
            text-align: left;
            padding: 12px 10px;
            background-color: #f1f5f9;
            color: #475569;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            border-bottom: 2px solid #e2e8f0;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .total-section {
            width: 100%;
            border-top: 2px solid #1e293b;
        }

        .total-row td {
            padding: 8px 0;
            text-align: right;
        }

        .total-label {
            padding-right: 20px;
            color: #64748b;
        }

        .total-value {
            font-weight: bold;
            width: 120px;
        }

        .grand-total {
            font-size: 12px;
            color: #1e293b;
            padding-top: 15px;
        }

        .notes-section {
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 20px;
            border-left: 3px solid #cbd5e1;
            padding-left: 15px;
        }

        .footer {
            position: fixed;
            bottom: 40px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="header-bg">
    <table class="w-full">
        <tr>
            <td class="w-half">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">#{{ $invoice->number }}</div>
            </td>
            <td class="w-half text-right">
                <div style="font-size: 20px; font-weight: bold;">{{ $invoice->seller->name }}</div>
                <div style="color: #94a3b8; margin-top: 5px;">{{ $invoice->seller->email }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="container">
    <table class="w-full" style="margin-bottom: 30px;">
        <tr>
            <td class="w-half">
                <div class="section-title">From</div>
                <div class="address-block">
                    {{ $invoice->seller->address_line_1 }}<br>
                    {{ $invoice->seller->city }}, {{ $invoice->seller->country }}<br>
                    @if($invoice->seller->tax_id)
                        Tax ID: {{ $invoice->seller->tax_id }}
                    @endif
                </div>
            </td>
            <td class="w-half">
                <div class="section-title">Bill To</div>
                <div class="address-block text-bold">
                    {{ $invoice->buyer->name }}
                </div>
                <div class="address-block">
                    {{ $invoice->buyer->address_line_1 }}<br>
                    {{ $invoice->buyer->city }}, {{ $invoice->buyer->country }}<br>
                    {{ $invoice->buyer->email }}<br>
                    @if($invoice->buyer->tax_id)
                        Tax ID: {{ $invoice->buyer->tax_id }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="w-full" style="margin-bottom: 30px;">
        <tr>
            <td width="50%">
                <divf class="section-title">Issue Date</divf>
                <div class="text-bold">{{ $invoice->issue_date->format('Y-m-d') }}</div>
            </td>
            <td width="50%">
                <div class="section-title">Due Date</div>
                <div class="text-bold">{{ $invoice->due_date->format('Y-m-d') }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table w-full">
        <thead>
        <tr>
            <th width="45%">Description</th>
            <th width="10%" class="text-center">Qty</th>
            <th width="15%" class="text-right">Price</th>
            <th width="15%" class="text-right">Tax</th>
            <th width="15%" class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>
                    <div class="text-bold">{{ $item->name }}</div>
                    @if($item->description)
                        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $item->description }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }} {{ $item->unit->value }}</td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
                <td class="text-right text-bold">{{ number_format($item->net_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="w-full">
        <tr>
            <td class="w-half" style="padding-right: 40px;">
                @if($invoice->bank_account_number)
                    <div class="section-title">Payment Info</div>
                    <div style="margin-bottom: 10px;">Bank Account: {{ $invoice->bank_account_number }}</div>
                @endif
                @if($invoice->terms)
                    <div class="section-title">Terms</div>
                    <div style="margin-bottom: 10px;">{{ $invoice->terms }}</div>
                @endif
                @if($invoice->notes)
                    <div class="notes-section">{{ $invoice->notes }}</div>
                @endif
            </td>
            <td class="w-half">
                <table class="total-section">
                    <tr class="total-row">
                        <td class="total-label">Subtotal</td>
                        <td class="total-value">{{ number_format($invoice->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="total-label">Tax</td>
                        <td class="total-value">{{ number_format($invoice->items->sum('tax_amount'), 2) }}</td>
                    </tr>
                    @if($invoice->items->sum('discount') > 0)
                        <tr class="total-row">
                            <td class="total-label">Discount</td>
                            <td class="total-value">-{{ number_format($invoice->items->sum('discount'), 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row grand-total">
                        <td class="total-label" style="color: #1e293b;">Total Due</td>
                        <td class="total-value">{{ number_format($invoice->items->sum('net_total'), 2) }} {{ $invoice->currency }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    Thank you for your business. {{ $invoice->seller->name }}
</div>

</body>
</html>
