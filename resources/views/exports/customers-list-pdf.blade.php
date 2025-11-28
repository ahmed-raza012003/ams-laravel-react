<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers List</title>
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
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        tbody tr:hover {
            background: #f9fafb;
        }
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($company['logo'])
                <img src="{{ $company['logo'] }}" alt="Company Logo" class="company-logo">
            @endif
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="report-title">CUSTOMERS REPORT</div>
            <div class="report-date">Generated on {{ date('d/m/Y H:i') }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>Postcode</th>
                    <th>Country</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->name ?? '' }}</td>
                    <td>{{ $customer->email ?? '' }}</td>
                    <td>{{ $customer->phone ?? '' }}</td>
                    <td>{{ $customer->address ?? '' }}</td>
                    <td>{{ $customer->city ?? '' }}</td>
                    <td>{{ $customer->postcode ?? '' }}</td>
                    <td>{{ $customer->country ?? '' }}</td>
                    <td>{{ \App\Services\ExportService::formatDate($customer->created_at ?? '') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <strong>Total Customers: {{ count($customers) }}</strong>
        </div>

        <div class="footer">
            <p>{{ $company['name'] }} - Confidential Report</p>
        </div>
    </div>
</body>
</html>

