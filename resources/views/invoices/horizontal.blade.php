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
            color: #2d3748;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .full-width {
            width: 100%;
        }

        .strip-header {
            background-color: #2d3748;
            color: #fff;
            padding: 60px 50px 40px 50px;
            position: relative;
        }

        .strip-dates {
            background-color: #e2e8f0;
            padding: 15px 50px;
            border-bottom: 1px solid #cbd5e1;
        }

        .strip-addresses {
            padding: 40px 50px;
        }

        .strip-items {
            padding: 0 50px;
            margin-bottom: 30px;
        }

        .strip-total {
            background-color: #f7fafc;
            border-top: 2px solid #2d3748;
            padding: 30px 50px;
        }

        .seller-name {
            font-size: 28px;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }

        .invoice-label {
            font-size: 60px;
            font-weight: 900;
            opacity: 0.08;
            position: absolute;
            top: 20px;
            right: 50px;
            color: #fff;
            z-index: 0;
        }

        .date-item {
            display: inline-block;
            margin-right: 40px;
        }

        .date-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            color: #4a5568;
        }

        .date-val {
            font-size: 14px;
            font-weight: bold;
        }

        .col-half {
            width: 50%;
            vertical-align: top;
        }

        .addr-label {
            font-size: 11px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 8px;
            display: block;
            color: #4a5568;
        }

        .table-clean {
            width: 100%;
            border-collapse: collapse;
        }

        .table-clean th {
            text-align: left;
            padding: 12px 0;
            border-bottom: 2px solid #2d3748;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table-clean td {
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .text-right {
            text-align: right;
        }

        .big-total-label {
            font-size: 12px;
            color: #4a5568;
        }

        .big-total-val {
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }

        .pay-details {
            font-size: 11px;
            color: #718096;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="strip-header">
    <table class="full-width">
        <tr>
            <td>
                <div class="seller-name">{{ $invoice->seller->name }}</div>
            </td>
            <td class="text-right">
                <div style="font-size: 24px; font-weight: bold;">#{{ $invoice->number }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="strip-dates">
    <div class="date-item">
        <span class="date-label">Issue Date</span><br>
        <span class="date-val">{{ $invoice->issue_date->format('Y-m-d') }}</span>
    </div>
    <div class="date-item">
        <span class="date-label">Due Date</span><br>
        <span class="date-val">{{ $invoice->due_date->format('Y-m-d') }}</span>
    </div>
</div>

<div class="strip-addresses">
    <table class="full-width">
        <tr>
            <td class="col-half" style="padding-right: 20px;">
                <span class="addr-label">Bill To</span>
                <div style="font-size: 16px; font-weight: bold;">{{ $invoice->buyer->name }}</div>
                <div style="margin-top: 5px;">
                    {{ $invoice->buyer->address_line_1 }}<br>
                    {{ $invoice->buyer->city }}, {{ $invoice->buyer->country }}
                </div>
                @if($invoice->buyer->tax_id)
                    <div style="margin-top: 5px; color: #718096;">ID: {{ $invoice->buyer->tax_id }}</div>
                @endif
            </td>
            <td class="col-half" style="padding-left: 20px;">
                <span class="addr-label">From</span>
                <div style="color: #2d3748;">
                    {{ $invoice->seller->address_line_1 }}<br>
                    {{ $invoice->seller->city }}, {{ $invoice->seller->country }}<br>
                    Tax ID: {{ $invoice->seller->tax_id ?? 'N/A' }}
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="strip-items">
    <table class="table-clean">
        <thead>
        <tr>
            <th width="45%">Description</th>
            <th width="15%" class="text-right">Unit Price</th>
            <th width="10%" style="text-align: center;">Qty</th>
            <th width="15%" class="text-right">Tax</th>
            <th width="15%" class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->name }}</strong>
                    @if($item->description)
                        <br><span style="font-size: 11px; color: #718096;">{{ $item->description }}</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->tax_amount, 2) }}</td>
                <td class="text-right"><strong>{{ number_format($item->net_total, 2) }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="strip-total">
    <table class="full-width">
        <tr>
            <td width="60%" class="pay-details">
                <strong>Payment Details</strong><br>
                Bank: {{ $invoice->bank_account_number ?? 'N/A' }}<br>
                @if($invoice->notes)
                    <br><em>{{ $invoice->notes }}</em>
                @endif
            </td>
            <td width="40%" class="text-right">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding-bottom: 5px; color: #718096;">Subtotal</td>
                        <td style="padding-bottom: 5px;">{{ number_format($invoice->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 5px; color: #718096;">Tax</td>
                        <td style="padding-bottom: 5px;">{{ number_format($invoice->items->sum('tax_amount'), 2) }}</td>
                    </tr>
                    @if($invoice->items->sum('discount') > 0)
                        <tr>
                            <td style="padding-bottom: 15px; color: #718096;">Discount</td>
                            <td style="padding-bottom: 15px;">
                                -{{ number_format($invoice->items->sum('discount'), 2) }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="big-total-label" style="padding-top: 10px;">Total Due</td>
                        <td class="big-total-val" style="padding-top: 10px;">
                            {{ number_format($invoice->items->sum('net_total'), 2) }} {{ $invoice->currency }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
