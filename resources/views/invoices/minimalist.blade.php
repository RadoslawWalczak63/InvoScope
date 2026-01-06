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
            font-size: 13px;
            line-height: 1.4;
            color: #111;
            background: #fff;
        }

        .container {
            padding: 40px 60px;
            max-width: 800px;
            margin: 0 auto;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #888;
        }

        .text-small {
            font-size: 11px;
        }

        .uppercase {
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-section {
            margin-bottom: 60px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .invoice-details {
            margin-bottom: 40px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .bill-to-section {
            margin-bottom: 50px;
        }

        .label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .items-table th {
            text-align: left;
            padding: 10px 0;
            border-bottom: 1px solid #000;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .items-table td {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .items-table .text-right {
            text-align: right;
        }

        .totals-section {
            margin-top: 30px;
        }

        .totals-table td {
            padding: 5px 0;
            text-align: right;
        }

        .total-label {
            padding-right: 20px;
            color: #666;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            padding-top: 10px;
            border-top: 1px solid #000;
        }

        .footer {
            margin-top: 80px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #888;
        }
    </style>
</head>
<body>

<div class="container">

    <table class="header-section">
        <tr>
            <td class="w-half" style="vertical-align: top;">
                <div class="company-name">{{ $invoice->seller->name }}</div>
                <div class="text-small text-muted">
                    {{ $invoice->seller->address_line_1 }}<br>
                    {{ $invoice->seller->city }}, {{ $invoice->seller->country }}<br>
                    {{ $invoice->seller->email }}
                </div>
            </td>
            <td class="w-half text-right" style="vertical-align: top;">
                <div class="invoice-title">Invoice</div>
                <div class="text-bold">#{{ $invoice->number }}</div>
                <div class="text-small text-muted">{{ $invoice->issue_date->format('F d, Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="bill-to-section">
        <tr>
            <td class="w-half" style="vertical-align: top;">
                <span class="label">Billed To</span>
                <div class="text-bold">{{ $invoice->buyer->name }}</div>
                <div class="text-small text-muted">
                    {{ $invoice->buyer->address_line_1 }}<br>
                    {{ $invoice->buyer->city }}, {{ $invoice->buyer->country }}
                </div>
                @if($invoice->buyer->tax_id)
                    <div class="text-small text-muted">ID: {{ $invoice->buyer->tax_id }}</div>
                @endif
            </td>
            <td class="w-half text-right" style="vertical-align: top;">
                <div style="margin-top: 15px;">
                    <span class="label">Due Date</span>
                    <div class="text-small">{{ $invoice->due_date->format('F d, Y') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
        <tr>
            <th width="50%">Description</th>
            <th width="15%" class="text-right">Price</th>
            <th width="10%" class="text-right">Qty</th>
            <th width="25%" class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>
                    <span class="text-bold">{{ $item->name }}</span>
                    @if($item->description)
                        <br><span class="text-small text-muted">{{ $item->description }}</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right text-bold">{{ number_format($item->net_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals-section">
        <tr>
            <td width="60%"></td>
            <td width="40%">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td>{{ number_format($invoice->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</td>
                    </tr>
                    @if($invoice->items->sum('tax_amount') > 0)
                        <tr>
                            <td class="total-label">Tax</td>
                            <td>{{ number_format($invoice->items->sum('tax_amount'), 2) }}</td>
                        </tr>
                    @endif
                    @if($invoice->items->sum('discount') > 0)
                        <tr>
                            <td class="total-label">Discount</td>
                            <td>-{{ number_format($invoice->items->sum('discount'), 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="total-label" style="padding-top: 10px;">Total</td>
                        <td class="grand-total">{{ number_format($invoice->items->sum('net_total'), 2) }} {{ $invoice->currency }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <div class="w-full">
            <span class="text-bold">Payment Details:</span> {{ $invoice->bank_account_number ?? 'N/A' }}
            @if($invoice->notes)
                <br><br>{{ $invoice->notes }}
            @endif
        </div>
    </div>

</div>

</body>
</html>
