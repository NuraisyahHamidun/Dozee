@php $isManager = $isManager ?? Auth::guard('manager')->check(); @endphp
@forelse($sales as $sale)
    @php
        $status = $sale->status ?? 'Pending';
        $badgeClass = match($status) {
            'Approved' => 'badge-approved',
            'Pending'  => 'badge-pending',
            'Rejected' => 'badge-rejected',
            default    => 'badge-default',
        };
    @endphp
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
        @if($isManager)
            <td class="px-5 py-4">
                @if($status === 'Pending')
                    <input type="checkbox" name="sale_ids[]" value="{{ $sale->transaction_id }}"
                           class="sale-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                @else
                    <input type="checkbox" disabled
                           class="rounded border-slate-200 bg-slate-100 dark:bg-slate-800 cursor-not-allowed opacity-40 w-4 h-4">
                @endif
            </td>
        @endif
        <td class="px-5 py-4 whitespace-nowrap text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">
            TXN-{{ str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT) }}
        </td>
        <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
            @if($sale->event_name)
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 text-xs font-semibold rounded-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $sale->event_name }}
                </span>
            @else
                <span class="text-slate-400 italic text-xs">—</span>
            @endif
        </td>
        <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
            {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
            <span class="block text-xs text-slate-400">{{ \Carbon\Carbon::parse($sale->sale_date)->format('H:i') }}</span>
        </td>
        <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
            @foreach($sale->saleItems as $item)
                <span class="inline-block px-2 py-0.5 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-lg mr-1 mb-1 border border-slate-100 dark:border-slate-700">
                    {{ $item->product->item_name ?? 'N/A' }} <span class="text-indigo-500">×{{ $item->quantity }}</span>
                </span>
            @endforeach
        </td>
        <td class="px-5 py-4 whitespace-nowrap text-sm font-bold text-indigo-600 text-right tabular-nums">
            RM {{ number_format($sale->total_amount, 2) }}
        </td>
        <td class="px-5 py-4 whitespace-nowrap text-center">
            <span class="{{ $badgeClass }}">{{ $status }}</span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ $isManager ? 7 : 6 }}" class="py-14 text-center text-slate-500 dark:text-slate-400 font-medium">
            <div class="flex flex-col items-center gap-2">
                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>No sale records found matching the filters.</span>
            </div>
        </td>
    </tr>
@endforelse
