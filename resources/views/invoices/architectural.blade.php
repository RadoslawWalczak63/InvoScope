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
            color: #111;
            margin: 0;
            padding: 40px 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            font-size: 40px;
            font-weight: bold;
            letter-spacing: -1px;
            margin-bottom: 5px;
        }

        .header-sub {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #555;
            border-bottom: 2px solid #111;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .meta-grid td {
            padding: 10px 0;
            vertical-align: top;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #777;
            letter-spacing: 1px;
            margin-bottom: 4px;
            display: block;
        }

        .meta-val {
            font-size: 13px;
            font-weight: bold;
        }

        .address-row {
            margin-top: 30px;
            margin-bottom: 40px;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
        }

        .address-row td {
            padding: 20px 0;
            vertical-align: top;
        }

        .items-table {
            margin-top: 30px;
        }

        .items-table th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #777;
            border-bottom: 1px solid #111;
            padding-bottom: 10px;
        }

        .items-table td {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            margin-top: 30px;
        }

        .total-row td {
            padding: 5px 0;
            text-align: right;
        }

        .grand-total {
            font-size: 20px;
            font-weight: bold;
            padding-top: 15px;
            border-top: 2px solid #111;
            margin-top: 10px;
            display: inline-block;
            width: 100%;
        }

        .footer-info {
            margin-top: 50px;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>

<div class="header-title">INVOICE</div>
<div class="header-sub">#{{ $invoice->number }}</div>

<table class="meta-grid">
    <tr>
        <td width="33%">
            <span class="meta-label">Issue Date</span>
            <span class="meta-val">{{ $invoice->issue_date->format('Y-m-d') }}</span>
        </td>
        <td width="33%">
            <span class="meta-label">Due Date</span>
            <span class="meta-val">{{ $invoice->due_date->format('Y-m-d') }}</span>
        </td>
        <td width="33%">
            <span class="meta-label">Status</span>
            <span class="meta-val">{{ ucfirst($invoice->status->value) }}</span>
        </td>
    </tr>
</table>

<table class="address-row">
    <tr>
        <td width="50%">
            <span class="meta-label">From (Seller)</span>
            <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">{{ $invoice->seller->name }}</div>
            <div style="color: #555;">
                {{ $invoice->seller->address_line_1 }}<br>
                {{ $invoice->seller->city }}, {{ $invoice->seller->country }}<br>
                {{ $invoice->seller->email }}
            </div>
            @if($invoice->seller->tax_id)
                <div style="margin-top: 5px; font-size: 11px;">Tax ID: {{ $invoice->seller->tax_id }}</div>
            @endif
        </td>
        <td width="50%" style="padding-left: 20px;">
            <span class="meta-label">To (Buyer)</span>
            <div style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">{{ $invoice->buyer->name }}</div>
            <div style="color: #555;">
                {{ $invoice->buyer->address_line_1 }}<br>
                {{ $invoice->buyer->city }}, {{ $invoice->buyer->country }}
            </div>
            @if($invoice->buyer->tax_id)
                <div style="margin-top: 5px; font-size: 11px;">Tax ID: {{ $invoice->buyer->tax_id }}</div>
            @endif
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
    <tr>
        <th width="40%">Description</th>
        <th width="15%" class="text-center">Quantity</th>
        <th width="15%" class="text-right">Unit Price</th>
        <th width="10%" class="text-right">Tax</th>
        <th width="20%" class="text-right">Amount</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->items as $item)
        <tr>
            <td>
                <div style="font-weight: bold;">{{ $item->name }}</div>
                @if($item->description)
                    <div style="font-size: 11px; color: #777; margin-top: 3px;">{{ $item->description }}</div>
                @endif
            </td>
            <td class="text-center">{{ $item->quantity }} {{ $item->unit->value }}</td>
            <td class="text-right">{{ number_format($item->price, 2) }}</td>
            <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
            <td class="text-right" style="font-weight: bold;">{{ number_format($item->net_total, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="total-section">
    <tr>
        <td width="60%" style="vertical-align: top; padding-right: 40px; text-align: left;">
            @if($invoice->notes)
                <div style="margin-bottom: 15px;">
                    <span class="meta-label">Notes</span>
                    <div style="font-size: 11px; color: #555;">{{ $invoice->notes }}</div>
                </div>
            @endif
            @if($invoice->bank_account_number)
                <div>
                    <span class="meta-label">Payment Information</span>
                    <div style="font-size: 11px;">Bank Account: {{ $invoice->bank_account_number }}</div>
                    @if($invoice->terms)
                        <div style="font-size: 11px;">Terms: {{ $invoice->terms }}</div>
                    @endif
                </div>
            @endif
        </td>
        <td width="40%">
            <table>
                <tr class="total-row">
                    <td>Subtotal</td>
                    <td width="100">{{ number_format($invoice->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Tax</td>
                    <td>{{ number_format($invoice->items->sum('tax_amount'), 2) }}</td>
                </tr>
                @if($invoice->items->sum('discount') > 0)
                    <tr class="total-row">
                        <td>Discount</td>
                        <td>-{{ number_format($invoice->items->sum('discount'), 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" class="text-right">
                        <div
                            class="grand-total">{{ number_format($invoice->items->sum('net_total'), 2) }} {{ $invoice->currency }}</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="footer-info text-center">
    Thank you for your business. Generated by {{ $invoice->seller->name }}.
</div>

</body>
</html>
