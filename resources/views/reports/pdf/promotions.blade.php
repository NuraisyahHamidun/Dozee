<!DOCTYPE html>
<html>
<head>
    <title>Promotions Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #4F46E5; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #F1F5F9; color: #333; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Do'zee - Promotions Report</h2>
        <p>Period: {{ $startDate }} to {{ $endDate }} | Status: {{ $promoStatus }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Promo ID</th>
                <th>Name</th>
                <th>Period</th>
                <th>Status</th>
                <th class="text-right">Discount (%)</th>
                <th class="text-right">Sales (Qty)</th>
                <th class="text-right">Revenue (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($promotions as $promo)
            <tr>
                <td>PRM-{{ str_pad($promo->promo_id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $promo->promo_name }}</td>
                <td>{{ \Carbon\Carbon::parse($promo->start_date)->format('d M y') }} - {{ \Carbon\Carbon::parse($promo->end_date)->format('d M y') }}</td>
                <td>{{ $promo->status }}</td>
                <td class="text-right">{{ $promo->final_discount ?? 10 }}%</td>
                <td class="text-right">{{ $promoRevenue[$promo->promo_id]['sales_count'] ?? 0 }}</td>
                <td class="text-right">{{ number_format($promoRevenue[$promo->promo_id]['revenue'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
