<x-app-layout>
    @php
        $bundleItems = $sale->saleItems->whereNotNull('promo_id')->groupBy('promo_id');
        $singleItems = $sale->saleItems->whereNull('promo_id');
        
        // Calculate gross total (original price sum before discount)
        $grossTotal = 0;
        foreach($sale->saleItems as $item) {
            $grossTotal += $item->product->price * $item->quantity;
        }
        
        // Subtotal (the actual discounted price sum)
        $subtotal = 0;
        foreach($sale->saleItems as $item) {
            if ($item->promo_id && $item->promotion) {
                $discountPercent = $item->promotion->final_discount ?? 10;
                $discountedPrice = $item->product->price * (1 - ($discountPercent / 100));
                $subtotal += $discountedPrice * $item->quantity;
            } else {
                $subtotal += $item->product->price * $item->quantity;
            }
        }
        
        $tax = $subtotal * 0.06;
        $totalPayable = $subtotal + $tax;
    @endphp
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                {{ __('Transaction Receipt') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to History
                </a>
                
                @if(Auth::guard('manager')->check() || $sale->status === 'Approved')
                    <a href="{{ route('sales.edit', $sale) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-2 shadow-md shadow-indigo-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Record
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="premium-card bg-white dark:bg-slate-800 overflow-hidden relative">
                <!-- Decorative Top Bar -->
                <div class="h-2 bg-indigo-600 w-full"></div>
                
                <div class="p-8 md:p-12">
                    <!-- Receipt Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start gap-8 mb-12 pb-8 border-b border-slate-100 dark:border-slate-700/50">
                        <div>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Transaction ID</span>
                            <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-widest mb-4 uppercase">TXN-{{ str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT) }}</h3>
                            
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="text-sm font-bold">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d F Y, H:i A') }}</span>
                            </div>
                        </div>
                        
                        <div class="md:text-right">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block text-left md:text-right">Issued By</span>
                            <div class="flex items-center md:justify-end gap-3">
                                <div class="text-left md:text-right">
                                    <p class="text-sm font-black text-slate-800 dark:text-white uppercase">{{ $sale->salesmen->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold tracking-widest uppercase">Sales Personnel</p>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-500 font-bold">
                                    {{ substr($sale->salesmen->name, 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status & Timeline Block -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 bg-slate-50 dark:bg-slate-900/40 rounded-2xl mb-8 border border-slate-100 dark:border-slate-700/30">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status</span>
                            @if($sale->status === 'Approved')
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 inline-block">
                                    Approved
                                </span>
                            @elseif($sale->status === 'Rejected')
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 inline-block">
                                    Rejected
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 inline-block">
                                    Pending Approval
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Date Create</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">
                                {{ $sale->date_create ? $sale->date_create->format('d M Y, H:i A') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Last Modified</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">
                                {{ $sale->date_modifier ? $sale->date_modifier->format('d M Y, H:i A') : '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Manager Verify</span>
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">
                                {{ $sale->date_verify ? $sale->date_verify->format('d M Y, H:i A') : '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Order Breakdown</h4>
                    
                    <!-- Mobile Card View for Items -->
                    <div class="space-y-6 md:hidden mb-8">
                        @if($bundleItems->count() > 0)
                            <div>
                                <h5 class="text-[10px] font-black uppercase tracking-wider text-violet-750 mb-3 block">Selected Bundles</h5>
                                <div class="space-y-4">
                                    @foreach($bundleItems as $promoId => $items)
                                        @php
                                            $promo = $items->first()->promotion;
                                            $promoName = $promo ? $promo->promo_name : 'Bundle Promotion';
                                            $discountPercent = $promo ? ($promo->final_discount ?? 10) : 10;
                                        @endphp
                                        <div class="bg-violet-50/10 border border-violet-100 rounded-2xl p-4">
                                            <div class="flex items-center justify-between mb-3 pb-1.5 border-b border-violet-100">
                                                <span class="text-[10px] font-black text-violet-850 uppercase">{{ $promoName }}</span>
                                                <span class="px-1.5 py-0.5 bg-violet-100 text-violet-700 text-[8px] font-black uppercase rounded">{{ $discountPercent }}% OFF</span>
                                            </div>
                                            <div class="space-y-3">
                                                @foreach($items as $item)
                                                    @php
                                                        $discountedPrice = $item->product->price * (1 - ($discountPercent / 100));
                                                        $amount = $discountedPrice * $item->quantity;
                                                    @endphp
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <p class="text-xs font-black text-slate-800 uppercase leading-none mb-1">{{ $item->product->item_name }}</p>
                                                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest">{{ $item->product->category->category_name ?? 'General' }}</p>
                                                        </div>
                                                        <span class="text-xs font-black text-indigo-650">x{{ $item->quantity }}</span>
                                                    </div>
                                                    <div class="flex justify-between items-end pt-2 border-t border-dashed border-slate-100">
                                                        <span class="text-[9px] text-slate-400 font-bold uppercase">
                                                            <span class="line-through">RM{{ number_format($item->product->price, 2) }}</span>
                                                            <span class="text-violet-650 ml-1">RM{{ number_format($discountedPrice, 2) }}</span>
                                                        </span>
                                                        <span class="text-xs font-black text-slate-800">RM{{ number_format($amount, 2) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($singleItems->count() > 0)
                            <div>
                                <h5 class="text-[10px] font-black uppercase tracking-wider text-slate-650 mb-3 block">Additional Single Items</h5>
                                <div class="space-y-3">
                                    @foreach($singleItems as $item)
                                        @php
                                            $amount = $item->product->price * $item->quantity;
                                        @endphp
                                        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <p class="text-xs font-black text-slate-800 dark:text-white uppercase leading-none mb-1">{{ $item->product->item_name }}</p>
                                                    <p class="text-[8px] text-slate-400 font-bold tracking-widest uppercase">{{ $item->product->category->category_name ?? 'Item' }}</p>
                                                </div>
                                                <span class="text-xs font-black text-indigo-600">x{{ $item->quantity }}</span>
                                            </div>
                                            <div class="flex justify-between items-end pt-2 border-t border-slate-100 dark:border-slate-700/50">
                                                <span class="text-[9px] text-slate-400 font-bold uppercase">RM{{ number_format($item->product->price, 2) }} / unit</span>
                                                <span class="text-xs font-black text-slate-800 dark:text-white">RM{{ number_format($amount, 2) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Desktop View for Items -->
                    <div class="hidden md:block overflow-hidden mb-12">
                        @if($bundleItems->count() > 0)
                            <div class="mb-8">
                                <h5 class="text-xs font-black uppercase tracking-wider text-violet-700 dark:text-violet-400 mb-4 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-violet-600 animate-pulse"></span>
                                    Selected Bundles
                                </h5>
                                <div class="border border-violet-100 dark:border-violet-900/30 rounded-2xl overflow-hidden bg-violet-50/10 dark:bg-violet-950/5 p-4 mb-6">
                                    @foreach($bundleItems as $promoId => $items)
                                        @php
                                            $promo = $items->first()->promotion;
                                            $promoName = $promo ? $promo->promo_name : 'Bundle Promotion';
                                            $discountPercent = $promo ? ($promo->final_discount ?? 10) : 10;
                                        @endphp
                                        <div class="mb-6 last:mb-0">
                                            <div class="flex items-center justify-between mb-3 pb-2 border-b border-violet-100 dark:border-violet-900/30">
                                                <span class="text-xs font-black uppercase text-violet-750 dark:text-violet-300">Bundle: {{ $promoName }}</span>
                                                <span class="px-2 py-0.5 bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-400 text-[9px] font-black uppercase tracking-wider rounded-md">{{ $discountPercent }}% OFF Bundle Items</span>
                                            </div>
                                            <table class="w-full">
                                                <thead>
                                                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100/50 dark:border-slate-700/30">
                                                        <th class="text-left pb-2 w-[45%]">Description</th>
                                                        <th class="text-center pb-2">Original Price</th>
                                                        <th class="text-center pb-2">Discounted Rate</th>
                                                        <th class="text-center pb-2">Qty</th>
                                                        <th class="text-right pb-2">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-50 dark:divide-slate-750/30">
                                                    @foreach($items as $item)
                                                        @php
                                                            $discountedPrice = $item->product->price * (1 - ($discountPercent / 100));
                                                            $amount = $discountedPrice * $item->quantity;
                                                        @endphp
                                                        <tr>
                                                            <td class="py-4">
                                                                <div class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase leading-none mb-1">{{ $item->product->item_name }}</div>
                                                                <div class="text-[9px] text-slate-400 font-bold tracking-widest uppercase">{{ $item->product->category->category_name ?? 'General' }}</div>
                                                            </td>
                                                            <td class="py-4 text-center text-sm font-bold text-slate-400 line-through tabular-nums">RM {{ number_format($item->product->price, 2) }}</td>
                                                            <td class="py-4 text-center text-sm font-black text-violet-650 dark:text-violet-400 tabular-nums">RM {{ number_format($discountedPrice, 2) }}</td>
                                                            <td class="py-4 text-center text-sm font-black text-slate-800 dark:text-white tabular-nums">{{ $item->quantity }}</td>
                                                            <td class="py-4 text-right text-sm font-black text-slate-800 dark:text-white tabular-nums">RM {{ number_format($amount, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($singleItems->count() > 0)
                            <div>
                                <h5 class="text-xs font-black uppercase tracking-wider text-slate-650 dark:text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    Additional Single Items
                                </h5>
                                <div class="border border-slate-100 dark:border-slate-700/50 rounded-2xl overflow-hidden p-4 bg-slate-50/30 dark:bg-slate-900/10">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100/50 dark:border-slate-700/30">
                                                <th class="text-left pb-2 w-[45%]">Description</th>
                                                <th class="text-center pb-2">Rate</th>
                                                <th class="text-center pb-2">Qty</th>
                                                <th class="text-right pb-2">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50 dark:divide-slate-750/30">
                                            @foreach($singleItems as $item)
                                                @php
                                                    $amount = $item->product->price * $item->quantity;
                                                @endphp
                                                <tr>
                                                    <td class="py-4">
                                                        <div class="text-sm font-black text-slate-700 dark:text-slate-200 uppercase leading-none mb-1">{{ $item->product->item_name }}</div>
                                                        <div class="text-[9px] text-slate-400 font-bold tracking-widest uppercase">{{ $item->product->category->category_name ?? 'General' }}</div>
                                                    </td>
                                                    <td class="py-4 text-center text-sm font-bold text-slate-500 dark:text-slate-400 tabular-nums">RM {{ number_format($item->product->price, 2) }}</td>
                                                    <td class="py-4 text-center text-sm font-black text-slate-800 dark:text-white tabular-nums">{{ $item->quantity }}</td>
                                                    <td class="py-4 text-right text-sm font-black text-slate-800 dark:text-white tabular-nums">RM {{ number_format($amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Financial Summary -->
                    <div class="flex justify-end mb-8">
                        <div class="w-full md:w-80 space-y-3 bg-slate-50/50 dark:bg-slate-900/30 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                                <span class="text-[10px] font-black uppercase tracking-wider">Subtotal</span>
                                <span class="font-bold text-sm tabular-nums">RM {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                                <span class="text-[10px] font-black uppercase tracking-wider">Estimated Tax (6%)</span>
                                <span class="font-bold text-sm tabular-nums">RM {{ number_format($tax, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Receipt Footer / Total -->
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-[2rem] p-8 md:p-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="text-center md:text-left">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Total Amount Payable</span>
                            <p class="text-[11px] text-slate-500 font-medium italic">Thank you for choosing Do'zee Professional Service.</p>
                        </div>
                        <div class="text-center md:text-right">
                            <h2 class="text-4xl font-black text-indigo-600 tracking-tighter">RM {{ number_format($sale->total_amount, 2) }}</h2>
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 rounded-full">Paid & Cleared</span>
                        </div>
                    </div>

                    @if(Auth::guard('manager')->check() && $sale->status === 'Pending')
                        <div class="mt-8 p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700/50 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="text-center md:text-left">
                                <h5 class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-200">Pending Verification</h5>
                                <p class="text-[10px] text-slate-400 mt-1">Review transaction details and verify this sale record.</p>
                            </div>
                            <div class="flex gap-3 w-full md:w-auto">
                                <form action="{{ route('sales.approve', $sale) }}" method="POST" class="flex-1 md:flex-none">
                                    @csrf
                                    <button type="submit" class="w-full px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition-all shadow-md shadow-emerald-100 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('sales.reject', $sale) }}" method="POST" class="flex-1 md:flex-none" onsubmit="return confirm('Reject this sale and return items to stock?');">
                                    @csrf
                                    <button type="submit" class="w-full px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition-all shadow-md shadow-rose-100 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Print Note -->
                <div class="p-6 bg-slate-900 text-center md:flex md:justify-between md:items-center">
                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-4 md:mb-0 block">Digital Receipt #TXN-{{ $sale->transaction_id }}</span>
                    <button onclick="window.print()" class="text-[10px] font-black text-white uppercase tracking-widest hover:text-indigo-400 transition-colors flex items-center justify-center gap-2 mx-auto md:mx-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
