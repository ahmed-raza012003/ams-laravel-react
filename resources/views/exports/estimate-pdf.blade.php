<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate {{ $estimate->estimate_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2ca48b;
        }
        .company-info {
            flex: 1;
        }
        .company-logo {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2ca48b;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }
        .estimate-info {
            text-align: right;
        }
        .estimate-title {
            font-size: 32px;
            font-weight: bold;
            color: #2ca48b;
            margin-bottom: 10px;
        }
        .estimate-details {
            font-size: 11px;
        }
        .estimate-details p {
            margin: 3px 0;
        }
        .estimate-details strong {
            color: #333;
        }
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .billing-box {
            flex: 1;
            padding: 15px;
            background: #f9fafb;
            border-radius: 5px;
        }
        .billing-box:first-child {
            margin-right: 15px;
        }
        .billing-box:last-child {
            margin-left: 15px;
        }
        .billing-box h3 {
            font-size: 12px;
            color: #2ca48b;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .billing-box p {
            margin: 3px 0;
            font-size: 11px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table thead {
            background: #2ca48b;
            color: white;
        }
        .items-table th {
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .items-table tbody tr:hover {
            background: #f9fafb;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .totals-box {
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 11px;
        }
        .total-row.total-final {
            border-top: 2px solid #2ca48b;
            margin-top: 5px;
            padding-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 5px;
        }
        .notes-section h3 {
            font-size: 12px;
            color: #2ca48b;
            margin-bottom: 8px;
        }
        .notes-section p {
            font-size: 11px;
            color: #666;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-DRAFT { background: #e5e7eb; color: #374151; }
        .status-SENT { background: #dbeafe; color: #1e40af; }
        .status-ACCEPTED { background: #d1fae5; color: #065f46; }
        .status-REJECTED { background: #fee2e2; color: #991b1b; }
        .status-EXPIRED { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-info">
                @if($company['logo'])
                    <img src="{{ $company['logo'] }}" alt="Company Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $company['name'] }}</div>
                @if($company['address'])
                    <div class="company-details">{{ $company['address'] }}</div>
                @endif
                @if($company['phone'] || $company['email'])
                    <div class="company-details">
                        @if($company['phone']){{ $company['phone'] }}@endif
                        @if($company['phone'] && $company['email']) | @endif
                        @if($company['email']){!! $company['email'] !!}@endif
                    </div>
                @endif
                @if($company['website'])
                    <div class="company-details">{{ $company['website'] }}</div>
                @endif
            </div>
            <div class="estimate-info">
                <div class="estimate-title">ESTIMATE</div>
                <div class="estimate-details">
                    <p><strong>Estimate #:</strong> {{ $estimate->estimate_number }}</p>
                    <p><strong>Issue Date:</strong> {{ \App\Services\ExportService::formatDate($estimate->issue_date) }}</p>
                    <p><strong>Expiry Date:</strong> {{ \App\Services\ExportService::formatDate($estimate->expiry_date) }}</p>
                    <p><strong>Status:</strong> 
                        <span class="status-badge status-{{ $estimate->status }}">{{ $estimate->status }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="billing-section">
            <div class="billing-box">
                <h3>Estimate For</h3>
                <p><strong>{{ $customer->name ?? '' }}</strong></p>
                @if($customer->email)
                    <p>{{ $customer->email }}</p>
                @endif
                @if($customer->phone)
                    <p>{{ $customer->phone }}</p>
                @endif
                @if($customer->address)
                    <p>{{ $customer->address }}</p>
                @endif
                @if($customer->city || $customer->postcode)
                    <p>
                        @if($customer->city){{ $customer->city }}, @endif
                        @if($customer->postcode){{ $customer->postcode }}@endif
                    </p>
                @endif
                @if($customer->country)
                    <p>{{ $customer->country }}</p>
                @endif
            </div>
            <div class="billing-box">
                <h3>From</h3>
                <p><strong>{{ $company['name'] }}</strong></p>
                @if($company['address'])
                    <p>{{ $company['address'] }}</p>
                @endif
                @if($company['phone'])
                    <p>{{ $company['phone'] }}</p>
                @endif
                @if($company['email'])
                    <p>{!! $company['email'] !!}</p>
                @endif
                @if($company['website'])
                    <p>{{ $company['website'] }}</p>
                @endif
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: center;">Tax</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estimate->items ?? [] as $item)
                <tr>
                    <td>{{ $item->description ?? '' }}</td>
                    <td style="text-align: center;">{{ number_format($item->quantity ?? 0, 2) }}</td>
                    <td style="text-align: right;">{{ \App\Services\ExportService::formatCurrency($item->unit_price ?? 0, $currency) }}</td>
                    <td style="text-align: center;">{{ number_format($item->tax_rate ?? 0, 2) }}%</td>
                    <td style="text-align: right;">{{ \App\Services\ExportService::formatCurrency($item->total ?? 0, $currency) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>{{ \App\Services\ExportService::formatCurrency($estimate->subtotal ?? 0, $currency) }}</span>
                </div>
                <div class="total-row">
                    <span>Tax Amount:</span>
                    <span>{{ \App\Services\ExportService::formatCurrency($estimate->tax_amount ?? 0, $currency) }}</span>
                </div>
                <div class="total-row total-final">
                    <span>Total:</span>
                    <span>{{ \App\Services\ExportService::formatCurrency($estimate->total ?? 0, $currency) }}</span>
                </div>
            </div>
        </div>

        @if($estimate->notes)
        <div class="notes-section">
            <h3>Notes</h3>
            <p>{!! nl2br(e($estimate->notes)) !!}</p>
        </div>
        @endif

        <div class="footer">
            <p>This estimate is valid until {{ \App\Services\ExportService::formatDate($estimate->expiry_date) }}</p>
            @if($company['name'])
                <p>{{ $company['name'] }} - Generated on {{ date('d/m/Y H:i') }}</p>
            @endif
        </div>
    </div>
</body>
</html>

