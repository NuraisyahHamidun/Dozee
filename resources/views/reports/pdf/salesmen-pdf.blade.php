<!DOCTYPE html>
<html>
<head>
    <title>My Sales Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #8B5CF6; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #8B5CF6; font-size: 18px; }
        .header p { margin: 4px 0; color: #555; font-size: 11px; }
        
        .kpis { width: 100%; margin-bottom: 20px; }
        .kpi-card { border: 1px solid #E9D5FF; padding: 10px; background-color: #FAF5FF; text-align: center; border-radius: 6px; }
        .kpi-title { font-size: 9px; font-weight: bold; color: #7C3AED; text-transform: uppercase; margin-bottom: 4px; }
        .kpi-val { font-size: 15px; font-weight: bold; color: #111; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #E9D5FF; padding: 6px 8px; text-align: left; }
        th { background-color: #F5F3FF; color: #6D28D9; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #FAF5FF; color: #6D28D9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Do'zee - Sales Report (Salesmen View Only)</h2>
        <p>Salesmen: <strong>{{ $salesmen->name }} ({{ $salesmen->staff_code }})</strong></p>
        <p>Period: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <!-- Summary Section -->
    <table class="kpis">
        <tr>
            <td class="kpi-card" style="width: 33.33%;">
                <div class="kpi-title">My Total Sales</div>
                <div class="kpi-val">RM {{ number_format($myTotalSales, 2) }}</div>
            </td>
            <td class="kpi-card" style="width: 33.33%;">
                <div class="kpi-title">My Transactions</div>
                <div class="kpi-val">{{ $myTotalTransactions }}</div>
            </td>
            <td class="kpi-card" style="width: 33.33%;">
                <div class="kpi-title">My Items Sold</div>
                <div class="kpi-val">{{ $myItemsSold }} units</div>
            </td>
        </tr>
    </table>

    <!-- Table breakdown -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Item Name</th>
                <th>Item Code</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Unit Price (RM)</th>
                <th class="text-right">Total Price (RM)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saleItems as $item)
                @php
                    $unitPrice = $item->product->price ?? 0;
                    $totalPrice = $unitPrice * $item->quantity;
                @endphp
                <tr>
                    <td>{{ $item->sale->sale_date ? $item->sale->sale_date->format('Y-m-d H:i') : 'N/A' }}</td>
                    <td>TXN-{{ str_pad($item->transaction_id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->product->item_name ?? 'N/A' }}</td>
                    <td>{{ $item->product->item_code ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($unitPrice, 2) }}</td>
                    <td class="text-right">{{ number_format($totalPrice, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No sales transactions found in this period.</td>
                </tr>
            @endforelse
            
            @if($saleItems->isNotEmpty())
                <tr class="total-row">
                    <td colspan="4" class="text-right">Total Summary</td>
                    <td class="text-center">{{ $myItemsSold }}</td>
                    <td></td>
                    <td class="text-right">RM {{ number_format($myTotalSales, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
