<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        .container {
            padding: 40px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #222;
            text-transform: uppercase;
        }

        .invoice-details {
            text-align: right;
            vertical-align: top;
        }

        .invoice-details h1 {
            margin: 0;
            font-size: 20px;
            color: #555;
            text-transform: uppercase;
        }

        .status-badge {
            display: inline-block;
            background: #eee;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
            text-transform: uppercase;
        }

        .address-table {
            width: 100%;
            margin-bottom: 40px;
        }

        .address-table td {
            vertical-align: top;
            width: 50%;
        }

        .box-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #777;
            margin-bottom: 5px;
            border-bottom: 1px solid #eee;
            display: inline-block;
            padding-bottom: 2px;
            min-width: 200px;
        }

        .address-content {
            margin-top: 5px;
        }

        .address-content strong {
            font-size: 13px;
            color: #000;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            color: #555;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .item-sku {
            font-size: 10px;
            color: #777;
        }

        .item-desc {
            font-size: 10px;
            color: #666;
            margin-top: 2px;
        }

        .totals-container {
            width: 100%;
            margin-top: 10px;
            clear: both;
        }

        .totals-table {
            width: 45%;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 0;
            border-bottom: 1px solid #eee;
        }

        .totals-table .label {
            color: #777;
        }

        .totals-table .amount {
            font-weight: bold;
            text-align: right;
        }

        .grand-total td {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            font-size: 14px;
            font-weight: bold;
            padding: 10px 0;
            color: #000;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 30px 40px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <table class="header-table">
        <tr>
            <td width="50%">
                <div class="logo-text">{{ $invoice->seller->name }}</div>
                <div style="font-size: 11px; color: #666;">
                    {{ $invoice->seller->email }}<br>
                    {{ $invoice->seller->phone ?? '' }}
                </div>
                @if($invoice->seller->tax_id)
                    <div style="font-size: 11px;">Tax ID: {{ $invoice->seller->tax_id }}</div>
                @endif
            </td>
            <td width="50%" class="invoice-details">
                <h1>Invoice</h1>
                <div>#{{ $invoice->number }}</div>
                <div class="status-badge">{{ strtoupper($invoice->status->value) }}</div>
                <div style="margin-top: 10px; font-size: 11px;">
                    <div><strong>Issue Date:</strong> {{ $invoice->issue_date->format('Y-m-d') }}</div>
                    <div><strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}</div>
                    @if($invoice->paid_date)
                        <div><strong>Paid Date:</strong> {{ $invoice->paid_date->format('Y-m-d') }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="address-table">
        <tr>
            <td>
                <span class="box-label">From</span>
                <div class="address-content">
                    <strong>{{ $invoice->seller->name }}</strong><br>
                    {{ $invoice->seller->address_line_1 }}<br>
                    {{ $invoice->seller->city }}, {{ $invoice->seller->postal_code }}<br>
                    {{ $invoice->seller->country }}
                </div>
            </td>
            <td>
                <span class="box-label">Bill To</span>
                <div class="address-content">
                    <strong>{{ $invoice->buyer->name }}</strong><br>
                    {{ $invoice->buyer->address_line_1 }}<br>
                    {{ $invoice->buyer->city }}, {{ $invoice->buyer->postal_code }}<br>
                    {{ $invoice->buyer->country }}<br>
                    {{ $invoice->buyer->email }}
                    @if($invoice->buyer->tax_id)
                        <br>Tax ID: {{ $invoice->buyer->tax_id }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
        <tr>
            <th width="30%">Description</th>
            <th width="10%">SKU</th>
            <th width="10%" class="text-center">Qty</th>
            <th width="10%" class="text-center">Unit</th>
            <th width="10%" class="text-right">Price</th>
            <th width="10%" class="text-right">Tax</th>
            <th width="10%" class="text-right">Disc.</th>
            <th width="10%" class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->name }}</strong>
                    @if($item->description)
                        <div class="item-desc">{{ $item->description }}</div>
                    @endif
                </td>
                <td>
                    {{ $item->sku ?? '-' }}
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->unit->value }}</td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
                <td class="text-right">
                    {{ $item->discount > 0 ? '-' . number_format($item->discount, 2) : '-' }}
                </td>
                <td class="text-right">
                    {{ number_format($item->net_total, 2) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal</td>
                <td class="amount">
                    {{ number_format($invoice->items->sum(fn($i) => $i->price * $i->quantity), 2) }}
                </td>
            </tr>
            <tr>
                <td class="label">Tax</td>
                <td class="amount">{{ number_format($invoice->items->sum('tax_amount'), 2) }}</td>
            </tr>
            @if($invoice->items->sum('discount') > 0)
                <tr>
                    <td class="label">Discount</td>
                    <td class="amount" style="color:#c00;">
                        -{{ number_format($invoice->items->sum('discount'), 2) }}
                    </td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>Total Due</td>
                <td class="amount">
                    {{ number_format($invoice->items->sum('net_total'), 2) }} {{ $invoice->currency }}
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="footer">
    <strong>Payment Details</strong><br>
    Bank Account: {{ $invoice->bank_account_number ?? 'N/A' }}<br>
    @if($invoice->terms)
        {{ $invoice->terms }}<br>
    @endif
    Please ensure payment is made by {{ $invoice->due_date->format('Y-m-d') }}.
</div>

</body>
</html>
