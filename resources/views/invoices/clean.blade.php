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
            line-height: 1.4;
        }

        .wrapper {
            width: 100%;
        }

        .header {
            background-color: #f8f9fa;
            padding: 40px 50px;
            border-bottom: 1px solid #e9ecef;
        }

        .logo-area {
            font-size: 28px;
            font-weight: bold;
            color: #2d3748;
            letter-spacing: -1px;
        }

        .invoice-details {
            margin-top: 10px;
            font-size: 12px;
            color: #718096;
        }

        .content {
            padding: 40px 50px;
        }

        .columns {
            width: 100%;
            margin-bottom: 40px;
        }

        .columns td {
            vertical-align: top;
        }

        .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #a0aec0;
            letter-spacing: 1px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .party-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2d3748;
        }

        .party-details {
            font-size: 12px;
            color: #4a5568;
        }

        .status-badge {
            background: #48bb78;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .status-badge.unpaid {
            background: #ed8936;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            text-align: left;
            padding: 12px 10px;
            background-color: #2d3748;
            color: #fff;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #edf2f7;
        }

        .items-table tr:nth-child(even) {
            background-color: #f7fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-container {
            width: 100%;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 0;
            text-align: right;
        }

        .total-row td {
            padding-top: 15px;
            border-top: 2px solid #2d3748;
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px 50px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 10px;
            color: #718096;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <table style="width: 100%">
            <tr>
                <td style="width: 60%">
                    <div class="logo-area">{{ $invoice->seller->name }}</div>
                    <div style="margin-top: 5px;">{{ $invoice->seller->email }}</div>
                </td>
                <td style="width: 40%; text-align: right;">
                    <span class="label">Invoice Number</span>
                    <div style="font-size: 16px; font-weight: bold;">{{ $invoice->number }}</div>
                    <div style="margin-top: 5px;">
                            <span class="status-badge {{ $invoice->status->value === 'paid' ? '' : 'unpaid' }}">
                                {{ ucfirst($invoice->status->value) }}
                            </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        <table class="columns">
            <tr>
                <td style="width: 33%">
                    <span class="label">From</span>
                    <div class="party-name">{{ $invoice->seller->name }}</div>
                    <div class="party-details">
                        {{ $invoice->seller->address_line_1 }}<br>
                        {{ $invoice->seller->city }}, {{ $invoice->seller->country }}<br>
                        @if($invoice->seller->tax_id)
                            Tax ID: {{ $invoice->seller->tax_id }}
                        @endif
                    </div>
                </td>
                <td style="width: 33%">
                    <span class="label">Bill To</span>
                    <div class="party-name">{{ $invoice->buyer->name }}</div>
                    <div class="party-details">
                        {{ $invoice->buyer->address_line_1 }}<br>
                        {{ $invoice->buyer->city }}, {{ $invoice->buyer->country }}<br>
                        {{ $invoice->buyer->email }}<br>
                        @if($invoice->buyer->tax_id)
                            Tax ID: {{ $invoice->buyer->tax_id }}
                        @endif
                    </div>
                </td>
                <td style="width: 33%; text-align: right;">
                    <span class="label">Dates</span>
                    <div class="party-details">
                        <strong>Issue:</strong> {{ $invoice->issue_date->format('Y-m-d') }}<br>
                        <strong>Due:</strong> {{ $invoice->due_date->format('Y-m-d') }}
                        @if($invoice->paid_date)
                            <br><strong>Paid:</strong> {{ $invoice->paid_date->format('Y-m-d') }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
            <tr>
                <th width="40%">Description</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Price</th>
                <th width="10%" class="text-right">Tax</th>
                <th width="20%" class="text-right">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->name }}</strong>
                        @if($item->description)
                            <div
                                style="font-size: 10px; color: #718096; margin-top: 2px;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }} {{ $item->unit->value }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->net_total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <table class="totals-container">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    @if($invoice->bank_account_number)
                        <div style="margin-bottom: 15px;">
                            <span class="label">Bank Account</span>
                            <div style="font-size: 12px;">{{ $invoice->bank_account_number }}</div>
                        </div>
                    @endif
                    @if($invoice->terms)
                        <div style="margin-bottom: 15px;">
                            <span class="label">Terms</span>
                            <div style="font-size: 12px;">{{ $invoice->terms }}</div>
                        </div>
                    @endif
                    @if($invoice->notes)
                        <div style="margin-bottom: 15px;">
                            <span class="label">Notes</span>
                            <div style="font-size: 12px; font-style: italic;">{{ $invoice->notes }}</div>
                        </div>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top;">
                    <table class="totals-table">
                        <tr>
                            <td>Subtotal</td>
                            <td style="font-weight: bold;">{{ number_format($invoice->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td>{{ number_format($invoice->items->sum('tax_amount'), 2) }}</td>
                        </tr>
                        @if($invoice->items->sum('discount') > 0)
                            <tr>
                                <td>Discount</td>
                                <td>-{{ number_format($invoice->items->sum('discount'), 2) }}</td>
                            </tr>
                        @endif
                        <tr class="total-row">
                            <td>Total</td>
                            <td>{{ number_format($invoice->items->sum('net_total'), 2) }} {{ $invoice->currency }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Thank you for your business. | {{ $invoice->seller->name }} | {{ $invoice->seller->email }}
    </div>
</div>
</body>
</html>
