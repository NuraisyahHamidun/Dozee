<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #4F46E5; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #F1F5F9; color: #333; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #EEF2FF; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Do'zee - Monthly Sales Report</h2>
        <p>Period: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Salesman</th>
                <th class="text-right">Amount (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->sale_date }}</td>
                <td>TXN-{{ str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $sale->salesman->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Revenue</td>
                <td class="text-right">{{ number_format($totalSalesRevenue, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
