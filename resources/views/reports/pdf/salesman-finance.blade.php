<!DOCTYPE html>
<html>
<head>
    <title>Salesman Finance Report – {{ $salesman->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 18px; }
        .header h2 { margin: 0; color: #4F46E5; font-size: 16px; }
        .header p  { margin: 4px 0; color: #666; font-size: 11px; }
        .meta-info { margin-bottom: 14px; }
        .meta-info table { width: 100%; border: none; margin-bottom: 8px; }
        .meta-info td { border: none; padding: 3px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        th, td { border: 1px solid #ddd; padding: 5px 6px; text-align: left; }
        th { background-color: #F1F5F9; color: #1e293b; font-weight: bold; font-size: 9px; text-transform: uppercase; letter-spacing: 0.04em; }
        td { font-size: 9.5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { font-weight: bold; background-color: #EEF2FF; }
        .badge-approved  { background: #d1fae5; color: #065f46; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: bold; }
        .badge-pending   { background: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: bold; }
        .badge-rejected  { background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: bold; }
        .badge-default   { background: #f1f5f9; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Do'Zee — Salesman Finance Report</h2>
        <p>Period: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width:15%;font-weight:bold;">Salesman Name:</td>
                <td style="width:35%;">{{ $salesman->name }}</td>
                <td style="width:15%;font-weight:bold;">Export Date:</td>
                <td style="width:35%;">{{ now()->format('d M Y, h:i A') }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Username:</td>
                <td>{{ $salesman->username }}</td>
                <td style="font-weight:bold;">Email:</td>
                <td>{{ $salesman->email }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Sale Date</th>
                <th>Txn ID</th>
                <th>Customer / Event</th>
                <th>Item Name</th>
                <th>Volume</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price (RM)</th>
                <th>Promotion</th>
                <th class="text-right">Total Price (RM)</th>
                <th class="text-right">Total Sale (RM)</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($saleItems as $item)
                @php
                    $unitPrice         = $item->product->price ?? 0;
                    $quantity          = $item->quantity;
                    $totalPricePerItem = $unitPrice * $quantity;
                    $status            = $item->sale->status ?? 'N/A';
                    $badgeClass        = match($status) {
                        'Approved' => 'badge-approved',
                        'Pending'  => 'badge-pending',
                        'Rejected' => 'badge-rejected',
                        default    => 'badge-default',
                    };
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($item->sale->sale_date)->format('Y-m-d H:i') }}</td>
                    <td>TXN-{{ str_pad($item->sale->transaction_id ?? 0, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $item->sale->event_name ?? 'N/A' }}</td>
                    <td>{{ $item->product->item_name ?? 'N/A' }}</td>
                    <td>{{ $item->product->volume ?? 'N/A' }}</td>
                    <td class="text-right">{{ $quantity }}</td>
                    <td class="text-right">{{ number_format($unitPrice, 2) }}</td>
                    <td>{{ $item->promotion->promo_name ?? 'None' }}</td>
                    <td class="text-right">{{ number_format($totalPricePerItem, 2) }}</td>
                    <td class="text-right">{{ number_format($item->sale->total_amount ?? 0, 2) }}</td>
                    <td class="text-center"><span class="{{ $badgeClass }}">{{ $status }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:15px;color:#94a3b8;">No transactions recorded.</td>
                </tr>
            @endforelse

            @if($saleItems->isNotEmpty())
                <tr class="total-row">
                    <td colspan="5" class="text-right">Grand Total</td>
                    <td class="text-right">{{ $totalQuantity }}</td>
                    <td colspan="2"></td>
                    <td class="text-right">RM {{ number_format($totalPrice, 2) }}</td>
                    <td class="text-right">RM {{ number_format($totalSaleAmount, 2) }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
