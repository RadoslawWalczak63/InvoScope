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
            color: #1f2937;
            line-height: 1.5;
        }

        .w-full {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .text-gray {
            color: #6b7280;
        }

        .header-bar {
            height: 8px;
            width: 100%;
            background-color: #4f46e5;
        }

        .container {
            padding: 40px 50px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
        }

        .invoice-badge {
            font-size: 36px;
            font-weight: bold;
            color: #e5e7eb;
        }

        .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }

        .info-box {
            margin-top: 30px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .info-box td {
            vertical-align: top;
            padding: 15px;
        }

        .bill-to-container {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 4px;
        }

        .items-table {
            margin-top: 40px;
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            padding: 12px 0;
            border-bottom: 2px solid #e5e7eb;
            font-size: 11px;
            text-transform: uppercase;
            color: #4b5563;
        }

        .items-table td {
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .item-name {
            font-weight: bold;
        }

        .item-desc {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .summary-section {
            margin-top: 30px;
            width: 100%;
        }

        .payment-info {
            font-size: 11px;
            color: #6b7280;
            padding-right: 50px;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-table td {
            padding: 8px 0;
            text-align: right;
        }

        .total-label {
            color: #6b7280;
            padding-right: 20px;
        }

        .grand-total-row td {
            padding-top: 15px;
            border-top: 2px solid #4f46e5;
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
        }

        .thank-you {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

<div class="header-bar"></div>

<div class="container">

    <table class="w-full">
        <tr>
            <td width="60%">
                <div class="company-name">{{ $invoice->seller->name }}</div>
                <div class="text-gray" style="font-size: 11px;">{{ $invoice->seller->email }}</div>
                <div class="text-gray" style="font-size: 11px;">
                    {{ $invoice->seller->address_line_1 }},
                    {{ $invoice->seller->city }},
                    {{ $invoice->seller->country }}
                </div>
                @if($invoice->seller->tax_id)
                    <div class="text-gray" style="font-size: 11px;">Tax ID: {{ $invoice->seller->tax_id }}</div>
                @endif
            </td>
            <td width="40%" class="text-right">
                <div class="invoice-badge">INVOICE</div>
                <div class="text-gray">#{{ $invoice->number }}</div>
                <div class="text-gray">Status: {{ ucfirst($invoice->status->value) }}</div>
            </td>
        </tr>
    </table>

    <table class="info-box">
        <tr>
            <td width="35%">
                <div>
                    <span class="label">Issue Date</span>
                    <span class="font-bold">{{ $invoice->issue_date->format('Y-m-d') }}</span>
                </div>
                <div style="margin-top: 10px;">
                    <span class="label">Due Date</span>
                    <span class="font-bold">{{ $invoice->due_date->format('Y-m-d') }}</span>
                </div>
                @if($invoice->paid_date)
                    <div style="margin-top: 10px;">
                        <span class="label">Paid Date</span>
                        <span class="font-bold">{{ $invoice->paid_date->format('Y-m-d') }}</span>
                    </div>
                @endif
            </td>

            <td width="65%" class="bill-to-container">
                <span class="label">Billed To</span>
                <div class="font-bold">{{ $invoice->buyer->name }}</div>
                <div style="font-size: 12px;">
                    {{ $invoice->buyer->address_line_1 }}<br>
                    {{ $invoice->buyer->city }}, {{ $invoice->buyer->country }}<br>
                    {{ $invoice->buyer->email }}
                </div>
                @if($invoice->buyer->tax_id)
                    <div style="font-size: 11px;">Tax ID: {{ $invoice->buyer->tax_id }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
        <tr>
            <th width="40%">Description</th>
            <th width="15%" class="text-right">Unit Price</th>
            <th width="10%" class="text-center">Qty</th>
            <th width="10%" class="text-center">Unit</th>
            <th width="10%" class="text-right">Tax</th>
            <th width="15%" class="text-right">Line Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item->name }}</div>
                    @if($item->description)
                        <div class="item-desc">{{ $item->description }}</div>
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->unit->value }}</td>
                <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
                <td class="text-right font-bold">{{ number_format($item->net_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="summary-section">
        <tr>
            <td width="55%" class="payment-info">
                <span class="label">Payment Details</span>
                <p><strong>Bank Account:</strong><br>{{ $invoice->bank_account_number ?? 'N/A' }}</p>
                @if($invoice->terms)
                    <p><strong>Payment Terms:</strong><br>{{ $invoice->terms }}</p>
                @endif
                @if($invoice->notes)
                    <p><strong>Notes:</strong><br>{{ $invoice->notes }}</p>
                @endif
            </td>

            <td width="45%">
                <table class="total-table">
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td>{{ number_format($invoice->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="total-label">Tax</td>
                        <td>{{ number_format($invoice->items->sum('tax_amount'), 2) }}</td>
                    </tr>
                    @if($invoice->items->sum('discount') > 0)
                        <tr>
                            <td class="total-label">Discount</td>
                            <td>-{{ number_format($invoice->items->sum('discount'), 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>Total Due</td>
                        <td>{{ number_format($invoice->items->sum('net_total'), 2) }} {{ $invoice->currency }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="thank-you">Thank you for your business</div>

</div>
</body>
</html>
