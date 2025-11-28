<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices List</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2ca48b;
        }
        .company-logo {
            max-width: 120px;
            max-height: 60px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2ca48b;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
        .report-date {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background: #2ca48b;
            color: white;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        th:last-child, td:last-child {
            text-align: right;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tbody tr:hover {
            background: #f9fafb;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-DRAFT { background: #e5e7eb; color: #374151; }
        .status-SENT { background: #dbeafe; color: #1e40af; }
        .status-PAID { background: #d1fae5; color: #065f46; }
        .status-OVERDUE { background: #fee2e2; color: #991b1b; }
        .status-CANCELLED { background: #f3f4f6; color: #6b7280; }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 5px;
            text-align: right;
        }
        .summary-row {
            display: flex;
            justify-content: flex-end;
            margin: 5px 0;
        }
        .summary-label {
            margin-right: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($company['logo'])
                <img src="{{ $company['logo'] }}" alt="Company Logo" class="company-logo">
            @endif
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="report-title">INVOICES REPORT</div>
            <div class="report-date">Generated on {{ date('d/m/Y H:i') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number ?? '' }}</td>
                    <td>{{ $invoice->customer_name ?? '' }}</td>
                    <td>{{ \App\Services\ExportService::formatDate($invoice->issue_date ?? '') }}</td>
                    <td>{{ \App\Services\ExportService::formatDate($invoice->due_date ?? '') }}</td>
                    <td><span class="status-badge status-{{ $invoice->status ?? 'DRAFT' }}">{{ $invoice->status ?? 'DRAFT' }}</span></td>
                    <td>{{ \App\Services\ExportService::formatCurrency($invoice->total ?? 0, $currency) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <span class="summary-label">Total Invoices:</span>
                <span>{{ count($invoices) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Amount:</span>
                <span>{{ \App\Services\ExportService::formatCurrency(collect($invoices)->sum('total'), $currency) }}</span>
            </div>
        </div>

        <div class="footer">
            <p>{{ $company['name'] }} - Confidential Report</p>
        </div>
    </div>
</body>
</html>

