<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <span class="p-2.5 bg-violet-600 rounded-2xl text-white shadow-lg shadow-violet-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </span>
                <div>
                    <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight">{{ __('Record Sales') }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Record single items or bundle sales for market basket analysis</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('sales.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-wider rounded-xl transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m0 0l3-3m-3 3l3 3"/></svg>
                    View Recent
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Data for JS --}}
    @php
        $promoDataMapped = $promotions->map(function($p) {
            $productIds = collect();
            if ($p->rule_id && $p->analysis) {
                $productIds = $productIds->merge($p->analysis->antecedentIds());
                $productIds->push($p->analysis->consequent);
            }
            foreach ($p->associationRules as $rule) {
                $productIds = $productIds->merge($rule->antecedentIds());
                $productIds->push($rule->consequent);
            }
            $uniqueIds = $productIds->unique()->filter()->values()->toArray();

            return [
                'id' => $p->promo_id,
                'name' => $p->promo_name,
                'discount' => $p->final_discount ?? 10,
                'product_ids' => $uniqueIds
            ];
        })->values();
    @endphp
    <script>
        window.bundleItemsBaseUrl = "{{ url('/promotions') }}";
        window.csrfToken = "{{ csrf_token() }}";
        window.promoData = @json($promoDataMapped);
    </script>

    <style>
        /* Force containers to overflow visible to avoid clipping */
        .sale-item-row, .item-combobox, #items-container, form, .max-w-7xl {
            overflow: visible !important;
        }
        /* Style for sticky header layout */
        #cbp-search-wrap {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
        }
    </style>

    <div class="pt-6 pb-64" x-data="{
        saleMode: 'bundle',
        itemCount: 0,
        totalQty: 0,
        subtotal: 0,
        tax: 0,
        total: 0,
        updateSummary() {
            const rows = document.querySelectorAll('.sale-item-row');
            let qty = 0, sub = 0, cnt = 0;
            rows.forEach(row => {
                const hidden = row.querySelector('.product-id-input');
                const qtyInput = row.querySelector('.quantity-input');
                if (hidden && hidden.value && qtyInput) {
                    const q = parseInt(qtyInput.value) || 0;
                    const p = parseFloat(hidden.dataset.price) || 0;
                    qty += q;
                    sub += p * q;
                    cnt++;
                }
            });
            this.itemCount = cnt;
            this.totalQty = qty;
            this.subtotal = sub;
            this.tax = sub * 0.06;
            this.total = sub + this.tax;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- MODE SWITCH                                                  --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div class="mb-6">
                <div class="inline-flex items-center bg-white rounded-2xl shadow-sm border border-slate-100 p-1.5 gap-1">
                    <button type="button"
                        @click="saleMode = 'single'; $dispatch('saleMode:change', 'single')"
                        :class="saleMode === 'single'
                            ? 'bg-violet-600 text-white shadow-md shadow-violet-200'
                            : 'text-slate-500 hover:text-slate-700'"
                        class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Single Item
                    </button>
                    <button type="button"
                        @click="saleMode = 'bundle'; $dispatch('saleMode:change', 'bundle')"
                        :class="saleMode === 'bundle'
                            ? 'bg-violet-600 text-white shadow-md shadow-violet-200'
                            : 'text-slate-500 hover:text-slate-700'"
                        class="flex items-center gap-2.5 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Bundle / Promotion
                        <span class="bg-violet-400/30 text-[9px] px-1.5 py-0.5 rounded-md font-black tracking-wide uppercase"
                              :class="saleMode === 'bundle' ? 'text-violet-100' : 'text-violet-500'">MBA</span>
                    </button>
                </div>

                {{-- Mode hint chip --}}
                <div class="mt-2 flex items-center gap-2">
                    <div x-show="saleMode === 'single'" class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                        <span class="text-[10px] font-bold text-slate-500">Single item transaction — individual product recording</span>
                    </div>
                    <div x-show="saleMode === 'bundle'" class="flex items-center gap-2 px-3 py-1.5 bg-violet-50 border border-violet-200 rounded-xl">
                        <div class="w-1.5 h-1.5 rounded-full bg-violet-500 animate-pulse"></div>
                        <span class="text-[10px] font-bold text-violet-600">Bundle mode — data collected for Market Basket Analysis</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('sales.store') }}" id="sale-form">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    {{-- ═══════════════════════════════════════════════════ --}}
                    {{-- LEFT MAIN PANEL                                     --}}
                    {{-- ═══════════════════════════════════════════════════ --}}
                    <div class="lg:col-span-8 space-y-5">

                        {{-- ─── TRANSACTION DETAILS ─────────────────────── --}}
                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm" style="overflow:visible;">
                            {{-- Section header --}}
                            <div class="px-7 py-4 border-b border-slate-50 flex items-center gap-3">
                                <div class="w-8 h-8 bg-violet-50 rounded-xl flex items-center justify-center">
                                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-700">Transaction Details</h3>
                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">Session and date configuration</p>
                                </div>
                                {{-- Transaction type badge --}}
                                <div class="ml-auto">
                                    <span x-show="saleMode === 'single'"
                                          class="px-3 py-1 bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                        Single Transaction
                                    </span>
                                    <span x-show="saleMode === 'bundle'"
                                          class="px-3 py-1 bg-violet-100 text-violet-700 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                        Bundle Transaction
                                    </span>
                                </div>
                            </div>

                            <div class="px-7 py-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Event Name --}}
                                    <div>
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">
                                            Event Name / Session
                                        </label>
                                        <div class="event-combobox relative">
                                            <div class="relative">
                                                <div class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                                </div>
                                                <input type="text" name="event_name" id="event_name_input" autocomplete="off"
                                                       placeholder="e.g. Roadshow Midvalley, Morning Session"
                                                       class="w-full pl-10 pr-5 py-3.5 bg-slate-50 border border-slate-100 hover:border-violet-200 rounded-xl focus:ring-2 focus:ring-violet-400 focus:border-violet-400 transition-all text-sm font-semibold text-slate-700 placeholder-slate-300">
                                            </div>
                                            {{-- Suggestions Panel --}}
                                            <div class="event-suggestions hidden absolute z-50 mt-2 w-full bg-white rounded-2xl shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
                                                <ul class="max-h-56 overflow-y-auto py-2">
                                                    @foreach($existingEvents as $event)
                                                        <li class="suggestion-item px-5 py-3 text-sm font-semibold text-slate-700 cursor-pointer hover:bg-violet-50 hover:text-violet-700 transition-colors border-b border-slate-50 last:border-0 flex items-center gap-2"
                                                            data-value="{{ $event }}">
                                                            <svg class="w-3.5 h-3.5 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            {{ $event }}
                                                        </li>
                                                    @endforeach
                                                    <li class="no-suggestions hidden px-5 py-3 text-xs text-slate-400 font-medium italic">No matches found.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-2 italic">Optional: Tag this sale to a specific event for pattern analysis.</p>
                                    </div>

                                    {{-- Transaction Date --}}
                                    <div>
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">
                                            Transaction Date &amp; Time
                                        </label>
                                        <div class="relative">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                            <input type="datetime-local" name="sale_date" id="sale_date_input"
                                                   value="{{ date('Y-m-d\TH:i') }}"
                                                   min="{{ date('Y-m-d\T00:00') }}"
                                                   required
                                                   class="w-full pl-10 pr-5 py-3.5 bg-slate-50 border border-slate-100 hover:border-violet-200 rounded-xl focus:ring-2 focus:ring-violet-400 focus:border-violet-400 transition-all text-sm font-semibold text-slate-700">
                                        </div>
                                        <div class="mt-2 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <p class="text-[10px] text-amber-600 font-bold">Allowed time: 8:00 AM – 8:00 PM only</p>
                                        </div>
                                        <p id="date-validation-msg" class="text-[10px] text-rose-500 mt-1 font-bold hidden"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ─── ACTIVE BUNDLE PROMOTIONS (PROACTIVE APRIORI RECS) ──── -->
                        <div x-show="saleMode === 'bundle'" x-transition class="bg-white rounded-3xl border border-slate-100 shadow-sm p-7 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-50 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-black uppercase tracking-widest text-slate-700">Market Basket Analysis Recommendations</h3>
                                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Select a bundle to pre-populate items, or build custom items below</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 text-[9px] bg-emerald-100 text-emerald-800 font-bold uppercase tracking-wider rounded-lg">Active Recommendations</span>
                            </div>

                            <!-- Carousel or Grid of Promotions -->
                            <div id="proactive-promotions-list" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="py-4 text-center text-slate-400 text-xs italic col-span-2">
                                    Loading active recommendations...
                                </div>
                            </div>

                            <!-- Pagination Controls -->
                            <div id="proactive-pagination" class="flex items-center justify-between pt-4 border-t border-slate-100/60 hidden">
                                <button type="button" id="prev-page-btn" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    Previous
                                </button>
                                <span id="page-indicator" class="text-[10px] font-bold text-slate-500">
                                    Page 1 of 1
                                </span>
                                <button type="button" id="next-page-btn" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    Next
                                </button>
                            </div>
                        </div>

                        {{-- ─── ITEMS SECTION ───────────────────────────── --}}
                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm" style="overflow:visible;">
                            {{-- Section header --}}
                            <div class="px-7 py-4 border-b border-slate-50 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                                     :class="saleMode === 'bundle' ? 'bg-violet-50' : 'bg-slate-50'">
                                    <svg class="w-4 h-4" :class="saleMode === 'bundle' ? 'text-violet-600' : 'text-slate-500'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-700"
                                        x-text="saleMode === 'bundle' ? 'Bundle Items (Multiple Products)' : 'Select Product'"></h3>
                                    <p class="text-[10px] text-slate-400 font-medium mt-0.5"
                                       x-text="saleMode === 'bundle' ? 'Add multiple items to create a bundle transaction for MBA tracking' : 'Select one item for this transaction'"></p>
                                </div>
                                {{-- Item counter badge --}}
                                <div class="ml-auto flex items-center gap-2">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Items:</span>
                                    <span id="item-count-badge" class="min-w-[1.5rem] h-6 px-2 bg-violet-600 text-white text-xs font-black rounded-lg flex items-center justify-center">0</span>
                                </div>
                            </div>

                            {{-- Items Container --}}
                            <div class="px-7 py-6 space-y-6">
                                {{-- Section 1: Selected Bundles --}}
                                <div x-show="saleMode === 'bundle'" x-transition class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Selected Bundles</h4>
                                        <span class="px-2 py-0.5 bg-violet-100 text-violet-700 text-[8px] font-black uppercase tracking-wider rounded-md">Bundle Discount Applies</span>
                                    </div>
                                    <div id="bundles-container" class="space-y-4" style="overflow:visible !important;">
                                        {{-- Bundle groups will be inserted here --}}
                                        <div class="bundles-empty-state py-8 text-center border-2 border-dashed border-slate-100 rounded-2xl text-xs text-slate-400 font-medium italic">
                                            No bundles applied. Select a bundle from recommendations above to add.
                                        </div>
                                    </div>
                                </div>

                                {{-- Section 2: Additional Single Items --}}
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400"
                                            x-text="saleMode === 'bundle' ? 'Additional Single Items' : 'Select Product'"></h4>
                                        <span x-show="saleMode === 'bundle'" class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[8px] font-black uppercase tracking-wider rounded-md">Standard Price</span>
                                    </div>
                                    <div id="singles-container" class="space-y-3" style="overflow:visible !important;">
                                        {{-- Single items will be inserted here --}}
                                    </div>
                                    
                                    {{-- Add Single Item button (only visible in Bundle mode) --}}
                                    <div class="mt-3" x-show="saleMode === 'bundle'" x-transition>
                                        <button type="button" id="add-single-item-btn"
                                                 class="w-full py-4 border-2 border-dashed border-violet-200 rounded-2xl flex items-center justify-center gap-3 text-violet-400 hover:text-violet-600 hover:border-violet-400 hover:bg-violet-50 transition-all group">
                                            <span class="w-7 h-7 bg-violet-100 group-hover:bg-violet-200 rounded-xl flex items-center justify-center transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </span>
                                            <span class="text-xs font-black uppercase tracking-widest">Add Additional Single Item</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Single mode note --}}
                                <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200" x-show="saleMode === 'single'" x-transition>
                                    <div class="flex items-start gap-3">
                                        <svg class="w-4 h-4 text-slate-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-[10px] text-slate-500 font-semibold leading-relaxed">
                                            Single Item mode records one product per transaction. Switch to <strong class="text-violet-600">Bundle mode</strong> to record multiple products that will feed into the Market Basket Analysis engine.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- ─── BOTTOM ACTION BAR ───────────────────────── --}}
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a href="{{ route('sales.index') }}"
                               class="flex items-center gap-2 px-5 py-3 bg-white border border-slate-200 hover:border-violet-300 hover:bg-violet-50 rounded-2xl text-xs font-black uppercase tracking-wider text-slate-500 hover:text-violet-700 transition-all shadow-sm group">
                                <svg class="w-4 h-4 group-hover:text-violet-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                View Recent
                            </a>
                            <button type="button" id="save-draft-btn"
                                    class="flex items-center gap-2 px-5 py-3 bg-white border border-slate-200 hover:border-amber-300 hover:bg-amber-50 rounded-2xl text-xs font-black uppercase tracking-wider text-slate-500 hover:text-amber-700 transition-all shadow-sm group">
                                <svg class="w-4 h-4 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                Save as Draft
                            </button>
                            <div class="flex-1 hidden lg:block"></div>
                            <button type="submit" form="sale-form"
                                    class="flex items-center gap-2.5 px-6 py-3 bg-violet-600 hover:bg-violet-700 text-white rounded-2xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-violet-200 group">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Record Sale
                            </button>
                        </div>


                    </div>

                    {{-- ═══════════════════════════════════════════════════ --}}
                    {{-- RIGHT SUMMARY PANEL                                 --}}
                    {{-- ═══════════════════════════════════════════════════ --}}
                    <div class="lg:col-span-4">
                        <div class="sticky top-24 space-y-4">

                            {{-- Summary Card --}}
                            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm" style="overflow:visible;">
                                <div class="bg-gradient-to-br from-violet-600 to-violet-700 px-6 py-5">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="text-[10px] font-black uppercase tracking-widest text-violet-200">Transaction Summary</h3>
                                        <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M13 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/></svg>
                                        </div>
                                    </div>
                                    {{-- Transaction type --}}
                                    <div class="inline-flex items-center gap-1.5 mt-2 bg-white/20 rounded-xl px-2.5 py-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white"
                                             :class="saleMode === 'bundle' ? 'animate-pulse' : ''"></div>
                                        <span class="text-[10px] font-black text-white uppercase tracking-widest"
                                              x-text="saleMode === 'bundle' ? 'Bundle / Promotion' : 'Single Item'"></span>
                                    </div>
                                </div>

                                <div class="px-6 py-5 space-y-3">
                                    {{-- Stats Row --}}
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3 bg-slate-50 rounded-2xl text-center">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block">Total Items</span>
                                            <span class="text-xl font-black text-slate-800 mt-0.5 block" id="summary-item-count" x-text="itemCount">0</span>
                                        </div>
                                        <div class="p-3 bg-slate-50 rounded-2xl text-center">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 block">Total Qty</span>
                                            <span class="text-xl font-black text-slate-800 mt-0.5 block" id="summary-total-qty" x-text="totalQty">0</span>
                                        </div>
                                    </div>

                                    {{-- Selected Bundles Section --}}
                                    <div x-show="saleMode === 'bundle'" x-transition class="border-t border-slate-100 pt-3">
                                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Selected Bundles</h4>
                                        <div id="summary-bundles-list" class="space-y-2 max-h-48 overflow-y-auto">
                                            <div class="text-[10px] text-slate-400 font-medium italic">No bundles selected</div>
                                        </div>
                                    </div>

                                    {{-- Additional Single Items Section --}}
                                    <div x-show="saleMode === 'bundle'" x-transition class="border-t border-slate-100 pt-3">
                                        <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Additional Single Items</h4>
                                        <div id="summary-singles-list" class="space-y-2 max-h-48 overflow-y-auto">
                                            <div class="text-[10px] text-slate-400 font-medium italic">No single items added</div>
                                        </div>
                                    </div>

                                    {{-- Subtotal row --}}
                                    <div class="flex items-center justify-between py-2 border-t border-slate-100">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Subtotal</span>
                                        <span class="text-sm font-black text-slate-700" id="summary-subtotal"
                                              x-text="'RM ' + subtotal.toFixed(2)">RM 0.00</span>
                                    </div>

                                    {{-- Tax row --}}
                                    <div class="flex items-center justify-between py-2 border-t border-slate-50">
                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                            Estimated Tax <span class="font-black text-slate-400">(6%)</span>
                                        </span>
                                        <span class="text-sm font-black text-slate-700" id="summary-tax"
                                              x-text="'RM ' + tax.toFixed(2)">RM 0.00</span>
                                    </div>

                                    {{-- Total Payable --}}
                                    <div class="pt-3 border-t-2 border-dashed border-violet-100">
                                        <div class="p-4 bg-gradient-to-br from-violet-50 to-indigo-50 rounded-2xl border border-violet-100">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-violet-500 block mb-1">Total Payable</span>
                                            <span class="text-3xl font-black text-violet-700 tracking-tight" id="total-estimation"
                                                  x-text="'RM ' + total.toFixed(2)">RM 0.00</span>
                                            {{-- Hidden for old calculateTotal() compat --}}
                                            <span id="subtotal-display" class="hidden">RM 0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit"
                                    class="w-full py-4 bg-gradient-to-r from-violet-600 to-violet-700 hover:from-violet-700 hover:to-violet-800 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-violet-200 transition-all flex items-center justify-center gap-3 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Record Sale
                            </button>

                            <a href="{{ route('sales.index') }}"
                               class="block w-full text-center py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                                Cancel &amp; Go Back
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ITEM ROW TEMPLATE (hidden, cloned by JS)                            --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <template id="item-row-template">
        <div class="sale-item-row group bg-white border border-slate-100 hover:border-violet-200 rounded-2xl p-4 transition-all" data-promo-id="" data-promo-name="">
            {{-- Bundle badge --}}
            <div class="bundle-badge hidden mb-3 pb-3 border-b border-violet-100">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-violet-600">Bundle:</span>
                    <span class="text-[10px] font-bold text-violet-500 bundle-badge-name"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                {{-- Product selector --}}
                <div class="md:col-span-5">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Product</label>
                    <div class="item-combobox relative" data-index="__IDX__">
                        <input type="hidden" name="items[__IDX__][product_id]" class="product-id-input" required>

                        {{-- ══ TRIGGER BUTTON (default & selected states) ══ --}}
                        <button type="button"
                            class="combobox-trigger w-full bg-white border-2 border-slate-100 hover:border-violet-300 focus:border-violet-400 focus:ring-4 focus:ring-violet-50 rounded-2xl transition-all duration-200 text-left group/trigger">

                            {{-- DEFAULT STATE: shown when nothing selected --}}
                            <div class="trigger-default flex items-center gap-3 px-4 py-3.5">
                                <div class="w-10 h-10 bg-slate-100 group-hover/trigger:bg-violet-50 rounded-xl flex items-center justify-center flex-shrink-0 transition-colors border-2 border-dashed border-slate-200 group-hover/trigger:border-violet-200">
                                    <svg class="w-5 h-5 text-slate-300 group-hover/trigger:text-violet-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="combobox-label block text-sm font-medium text-slate-400 group-hover/trigger:text-slate-500 transition-colors">Select a product...</span>
                                    <span class="text-[10px] text-slate-300 font-medium">Click to browse the product catalogue</span>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-[9px] font-black uppercase tracking-wider text-violet-400 bg-violet-50 px-2 py-1 rounded-lg hidden group-hover/trigger:block">Browse</span>
                                    <svg class="combobox-arrow w-4 h-4 text-slate-300 group-hover/trigger:text-violet-400 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>

                            {{-- SELECTED STATE: shown after a product is picked --}}
                            <div class="trigger-selected hidden items-center gap-3 px-4 py-3 pr-3">
                                {{-- Product avatar --}}
                                <div class="selected-avatar w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-xs font-black shadow-sm" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);"></div>
                                {{-- Product info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="selected-name text-sm font-black text-slate-800 truncate"></span>
                                        <span class="selected-volume text-[9px] px-1.5 py-0.5 bg-violet-100 text-violet-600 rounded-md font-black uppercase flex-shrink-0"></span>
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="selected-code text-[10px] font-bold text-slate-400"></span>
                                        <span class="w-1 h-1 rounded-full bg-slate-200 flex-shrink-0"></span>
                                        <span class="selected-stock-badge text-[9px] font-black px-1.5 py-0.5 rounded-md"></span>
                                    </div>
                                </div>
                                {{-- Price + arrow --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <div class="text-right">
                                        <span class="selected-price block text-sm font-black text-violet-600"></span>
                                        <span class="text-[9px] text-slate-400 font-medium">per unit</span>
                                    </div>
                                    <div class="w-7 h-7 bg-slate-50 rounded-lg flex items-center justify-center">
                                        <svg class="combobox-arrow w-3.5 h-3.5 text-slate-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </button>

                        {{-- ══ HIDDEN DATA SOURCE (cloned by portal JS) ══ --}}
                        <div class="combobox-data-store" style="display:none;" aria-hidden="true">
                            @foreach($products as $product)
                            @php
                                $initials      = strtoupper(substr($product->item_name, 0, 2));
                                $stockClass    = $product->stock_qty > 20 ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($product->stock_qty > 5 ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-rose-50 text-rose-500 border border-rose-200');
                                $stockLabel    = $product->stock_qty > 20 ? 'In Stock' : ($product->stock_qty > 5 ? 'Low Stock' : 'Critical');
                                $stockBg       = $product->stock_qty > 20 ? '#ecfdf5' : ($product->stock_qty > 5 ? '#fffbeb' : '#fff1f2');
                                $stockTxt      = $product->stock_qty > 20 ? '#059669' : ($product->stock_qty > 5 ? '#d97706' : '#e11d48');
                                $stockBorder   = $product->stock_qty > 20 ? '#6ee7b7' : ($product->stock_qty > 5 ? '#fcd34d' : '#fda4af');
                                $avatarPalette = ['#7c3aed','#6d28d9','#4f46e5','#0891b2','#059669','#d97706','#dc2626'];
                                $avatarColor   = $avatarPalette[abs(crc32($product->item_name)) % count($avatarPalette)];
                            @endphp
                            <li class="combobox-option combobox-option-data"
                                data-value="{{ $product->item_id }}"
                                data-price="{{ $product->price }}"
                                data-stock="{{ $product->stock_qty }}"
                                data-code="{{ $product->item_code }}"
                                data-volume="{{ $product->volume }}"
                                data-stock-label="{{ $stockLabel }}"
                                data-stock-class="{{ $stockClass }}"
                                data-avatar-color="{{ $avatarColor }}"
                                data-initials="{{ $initials }}"
                                data-label="{{ $product->item_name }} {{ $product->item_code }} {{ $product->volume }} {{ $product->category }} (RM {{ number_format($product->price, 2) }})"
                                style="display:block; padding:10px 16px; cursor:pointer; border-bottom:1px solid #f8fafc; transition:background .12s;">
                                <div style="display:grid; grid-template-columns:1fr 90px 90px; gap:8px; align-items:center;">
                                    {{-- Avatar + name + code --}}
                                    <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                                        <div style="width:40px; height:40px; border-radius:12px; background:{{ $avatarColor }}; flex-shrink:0; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:900; box-shadow:0 2px 8px {{ $avatarColor }}55; transition:transform .15s;">{{ $initials }}</div>
                                        <div style="min-width:0; flex:1;">
                                            <div><span class="item-name-text" style="font-size:13px; font-weight:700; color:#1e293b; line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; max-width:200px;">{{ $product->item_name }}</span></div>
                                            <div style="display:flex; align-items:center; gap:6px; margin-top:3px;">
                                                <span style="font-size:10px; font-weight:800; color:#94a3b8; font-family:monospace;">{{ $product->item_code }}</span>
                                                <span style="width:3px; height:3px; border-radius:50%; background:#cbd5e1;"></span>
                                                <span class="item-volume-text" style="font-size:9px; font-weight:900; text-transform:uppercase; padding:2px 6px; background:#f5f3ff; color:#7c3aed; border-radius:5px; border:1px solid #ddd6fe;">{{ $product->volume }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Stock --}}
                                    <div style="text-align:center;">
                                        <div style="font-size:14px; font-weight:900; color:#1e293b; line-height:1;">{{ $product->stock_qty }}</div>
                                        <span style="display:inline-block; margin-top:4px; font-size:9px; font-weight:800; padding:2px 8px; border-radius:20px; background:{{ $stockBg }}; color:{{ $stockTxt }}; border:1px solid {{ $stockBorder }}; white-space:nowrap;">{{ $stockLabel }}</span>
                                    </div>
                                    {{-- Price --}}
                                    <div style="text-align:right;">
                                        <span class="item-price-text" style="font-size:14px; font-weight:900; color:#7c3aed; display:block; line-height:1;">RM {{ number_format($product->price, 2) }}</span>
                                        <span style="font-size:9px; color:#94a3b8; font-weight:600;">/unit</span>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                            {{-- Empty state --}}
                            <li class="combobox-empty combobox-empty-data" style="display:none; padding:40px 16px; text-align:center;">
                                <div style="width:52px; height:52px; background:#f8fafc; border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                                    <svg width="24" height="24" fill="none" stroke="#cbd5e1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <p style="font-size:14px; font-weight:700; color:#94a3b8; margin:0 0 4px;">No products found</p>
                                <p style="font-size:12px; color:#cbd5e1; margin:0;">Try a different search term</p>
                            </li>
                        </div>

                    </div>
                </div>

                {{-- Item Type & Strategy --}}
                <div class="md:col-span-3 space-y-2">
                    <div class="item-type-container hidden">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Item Type</label>
                        <select class="item-type-select w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-violet-400 transition-all text-xs font-bold text-slate-700">
                            <option value="single">Single Item</option>
                            <option value="bundle">Bundle / Promotion</option>
                        </select>
                    </div>
                    <div class="promo-select-container hidden">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Bundle Link</label>
                        <select name="items[__IDX__][promo_id]" class="promo-select w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-violet-400 transition-all text-xs font-bold text-slate-700">
                            <option value="">Select Bundle...</option>
                        </select>
                    </div>
                </div>

                {{-- Quantity --}}
                <div class="md:col-span-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Quantity</label>
                    <div class="flex items-center gap-1 bg-slate-50 border border-slate-100 rounded-xl p-1">
                        <button type="button" class="qty-minus w-9 h-9 bg-white rounded-lg flex items-center justify-center text-slate-500 hover:text-violet-600 hover:bg-violet-50 transition-all border border-slate-100 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                        </button>
                        <input name="items[__IDX__][quantity]" type="number" min="1" value="1" required
                               class="quantity-input flex-1 bg-transparent border-none text-center text-sm font-black text-slate-700 focus:outline-none focus:ring-0">
                        <button type="button" class="qty-plus w-9 h-9 bg-white rounded-lg flex items-center justify-center text-slate-500 hover:text-violet-600 hover:bg-violet-50 transition-all border border-slate-100 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Delete button --}}
                <div class="md:col-span-1 flex justify-end items-end">
                    <button type="button"
                            class="remove-item w-10 h-10 text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all flex items-center justify-center hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>

            {{-- Subtotal row --}}
            <div class="mt-3 pt-3 border-t border-slate-50 flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Line Subtotal</span>
                <span class="text-sm font-black text-violet-600 row-subtotal">RM 0.00</span>
            </div>
        </div>
    </template>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- JAVASCRIPT                                                           --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const bundlesContainer = document.getElementById('bundles-container');
        const singlesContainer = document.getElementById('singles-container');
        const addSingleBtn     = document.getElementById('add-single-item-btn');
        const totalDisp        = document.getElementById('total-estimation');
        const subtotalDisp     = document.getElementById('subtotal-display');
        const template         = document.getElementById('item-row-template');
        const itemCountBadge   = document.getElementById('item-count-badge');
        let itemIndex          = 0;

        // ── Alpine.js instance helper ────────────────────────────────────
        function getAlpine() {
            const el = document.querySelector('[x-data]');
            return el ? el._x_dataStack?.[0] : null;
        }

        // ── Event Combobox Logic ─────────────────────────────────────────
        const eventCombobox   = document.querySelector('.event-combobox');
        const eventInput      = document.getElementById('event_name_input');
        const eventPanel      = document.querySelector('.event-suggestions');
        const suggestionItems = document.querySelectorAll('.suggestion-item');
        const noSuggestions   = document.querySelector('.no-suggestions');

        function filterEvents(query) {
            const q = query.toLowerCase();
            let anyVisible = false;
            suggestionItems.forEach(item => {
                const match = item.dataset.value.toLowerCase().includes(q);
                item.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });
            noSuggestions.classList.toggle('hidden', anyVisible || q === '');
            eventPanel.classList.toggle('hidden', !anyVisible && q === '');
        }

        if (eventInput) {
            eventInput.addEventListener('focus', () => {
                if (suggestionItems.length > 0) { eventPanel.classList.remove('hidden'); filterEvents(eventInput.value); }
            });
            eventInput.addEventListener('input', () => { eventPanel.classList.remove('hidden'); filterEvents(eventInput.value); });
            suggestionItems.forEach(item => {
                item.addEventListener('click', () => { eventInput.value = item.dataset.value; eventPanel.classList.add('hidden'); });
            });
            document.addEventListener('click', (e) => { if (eventCombobox && !eventCombobox.contains(e.target)) eventPanel.classList.add('hidden'); });
        }

        // ── Row Builder ──────────────────────────────────────────────────
        function buildRow(opts = {}) {
            const idx  = itemIndex++;
            const frag = template.content.cloneNode(true);
            const row  = frag.querySelector('.sale-item-row');
            row.innerHTML = row.innerHTML.replaceAll('__IDX__', idx);

            // Bundle configurations
            if (opts.promoId) {
                row.dataset.promoId   = opts.promoId;
                row.dataset.promoName = opts.promoName || '';
                row.classList.add('border-violet-200', 'bg-violet-50/40');
                
                // Hide bundle options in the row template because we group them externally
                row.querySelector('.bundle-badge').classList.add('hidden');
                
                const promoSelect = row.querySelector('.promo-select');
                if (promoSelect) {
                    promoSelect.innerHTML = `<option value="${opts.promoId}">${opts.promoName}</option>`;
                    promoSelect.value = opts.promoId;
                }
                row.querySelector('.promo-select-container').classList.add('hidden');
                row.querySelector('.item-type-container').classList.add('hidden');
            }

            // Pre-fill product if given
            if (opts.productId) {
                const hiddenInput = row.querySelector('.product-id-input');
                hiddenInput.value           = opts.productId;
                hiddenInput.dataset.price   = opts.price || 0;
                const lbl = row.querySelector('.combobox-label');
                lbl.textContent = opts.productName ? `${opts.productName} (${opts.volume || ''})` : 'Select a product...';
                lbl.classList.remove('text-slate-400');
                lbl.classList.add('text-slate-700', 'font-bold');
                
                if (opts.promoId) {
                    const trigger = row.querySelector('.combobox-trigger');
                    trigger.disabled = true;
                    trigger.classList.add('opacity-75', 'cursor-not-allowed', 'bg-slate-50');
                    trigger.classList.remove('hover:border-violet-300');
                }
            }

            // Quantity
            if (opts.qty) { row.querySelector('.quantity-input').value = opts.qty; }

            // Set promo_id on select
            if (opts.promoId) {
                const sel = row.querySelector('.promo-select');
                if (sel) sel.value = opts.promoId;
                // Add hidden promo_id input just to be safe
                const hiddenPromoInput = document.createElement('input');
                hiddenPromoInput.type = 'hidden';
                hiddenPromoInput.name = `items[${idx}][promo_id]`;
                hiddenPromoInput.value = opts.promoId;
                row.appendChild(hiddenPromoInput);
            }

            // Remove button
            if (opts.showRemove !== false) { row.querySelector('.remove-item').classList.remove('hidden'); }

            return row;
        }

        // ── Combobox Portal (fixed, body-level, immune to overflow clipping) ──
        const PORTAL_TOTAL = {{ $products->count() }};

        const cbPortal = document.createElement('div');
        cbPortal.id = 'cb-portal';
        cbPortal.style.cssText = [
            'position:fixed',
            'z-index:999999',
            'background:#fff',
            'border-radius:20px',
            'border:1.5px solid #ede9fe',
            'box-shadow:0 28px 72px -8px rgba(109,40,217,0.22), 0 10px 32px -4px rgba(0,0,0,0.12)',
            'overflow:hidden',
            'display:none',
            'flex-direction:column',
            'transform-origin:top left',
            'min-width:340px',
            'width:480px',
            'max-width:96vw',
        ].join(';');

        cbPortal.innerHTML = `
        <div id="cbp-search-wrap" style="flex-shrink:0; padding:14px 16px 10px; background:#fff; border-bottom:1.5px solid #ede9fe;">
            <div style="position:relative;">
                <div style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#7c3aed; pointer-events:none;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input id="cbp-search" type="text"
                    placeholder="Search by name, code, or category..."
                    style="width:100%; padding:10px 14px 10px 38px; font-size:13px; font-weight:600; color:#1e293b; background:#f8f7ff; border:1.5px solid #ddd6fe; border-radius:12px; outline:none; box-sizing:border-box; transition:border-color .15s,box-shadow .15s;"
                    autocomplete="off"
                >
            </div>
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:8px; padding:0 2px;">
                <span id="cbp-count" style="font-size:10px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em;">${PORTAL_TOTAL} products</span>
                <span style="font-size:10px; font-weight:700; color:#a78bfa; background:#f5f3ff; padding:2px 8px; border-radius:8px;">Standard Price</span>
            </div>
        </div>
        <div style="flex-shrink:0; display:grid; grid-template-columns:1fr 90px 90px; gap:8px; padding:7px 16px; background:#faf9ff; border-bottom:1px solid #f1f5f9;">
            <span style="font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.12em; color:#94a3b8;">Product</span>
            <span style="font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.12em; color:#94a3b8; text-align:center;">Stock</span>
            <span style="font-size:9px; font-weight:900; text-transform:uppercase; letter-spacing:.12em; color:#94a3b8; text-align:right;">Price</span>
        </div>
        <ul id="cbp-list" style="flex:1; overflow-y:auto; overflow-x:hidden; overscroll-behavior:contain; padding:4px 0; scroll-behavior:smooth; max-height:420px;"></ul>
        <div style="flex-shrink:0; padding:8px 16px; background:#faf9ff; border-top:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:9px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.08em;">Product Catalogue</span>
            <span style="font-size:9px; font-weight:800; color:#7c3aed; background:#f5f3ff; padding:3px 8px; border-radius:8px; border:1px solid #ddd6fe;">Esc to close</span>
        </div>`;

        document.body.appendChild(cbPortal);

        const cbpSearch = cbPortal.querySelector('#cbp-search');
        const cbpList   = cbPortal.querySelector('#cbp-list');
        const cbpCount  = cbPortal.querySelector('#cbp-count');

        let activeCombobox   = null;
        let activeOnSelect   = null;
        let portalOpen       = false;

        function positionPortal(triggerEl) {
            const rect       = triggerEl.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom - 8;
            const spaceAbove = rect.top - 8;
            const minPanelH  = 260;

            const w = Math.max(rect.width, 380);
            cbPortal.style.width    = Math.min(w, window.innerWidth - 16) + 'px';
            cbPortal.style.maxWidth = (window.innerWidth - 16) + 'px';

            const left = Math.min(rect.left, window.innerWidth - w - 8);
            cbPortal.style.left = Math.max(8, left) + 'px';

            const cbpListEl = cbPortal.querySelector('#cbp-list');

            if (spaceBelow >= minPanelH || spaceBelow >= spaceAbove) {
                cbPortal.style.top         = (rect.bottom + 8) + 'px';
                cbPortal.style.bottom      = 'auto';
                cbPortal.style.maxHeight   = (spaceBelow - 4) + 'px';
                cbpListEl.style.maxHeight  = Math.max(120, spaceBelow - 120) + 'px';
            } else {
                cbPortal.style.top         = 'auto';
                cbPortal.style.bottom      = (window.innerHeight - rect.top + 8) + 'px';
                cbPortal.style.maxHeight   = (spaceAbove - 4) + 'px';
                cbpListEl.style.maxHeight  = Math.max(120, spaceAbove - 120) + 'px';
            }
        }

        function openPortal(triggerEl, combobox, onSelectFn) {
            activeCombobox = combobox;
            activeOnSelect = onSelectFn;

            cbpList.innerHTML = '';
            combobox.querySelectorAll('.combobox-option-data').forEach(opt => {
                cbpList.appendChild(opt.cloneNode(true));
            });
            
            const emptyClone = combobox.querySelector('.combobox-empty-data');
            if (emptyClone) cbpList.appendChild(emptyClone.cloneNode(true));

            cbpList.querySelectorAll('.combobox-option').forEach(opt => {
                opt.style.display = 'block';
                opt.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    onSelectFn(opt);
                    closePortal();
                });
                opt.addEventListener('mouseenter', () => opt.style.background = '#f5f3ff');
                opt.addEventListener('mouseleave', () => opt.style.background = 'transparent');
            });

            cbpSearch.value = '';
            cbpCount.textContent = PORTAL_TOTAL + ' products';
            cbpList.querySelectorAll('.combobox-option').forEach(o => o.style.display = 'block');
            const emptyEl = cbpList.querySelector('.combobox-empty');
            if (emptyEl) emptyEl.style.display = 'none';

            positionPortal(triggerEl);

            document.body.style.overflow = 'hidden';

            cbPortal.style.display    = 'flex';
            cbPortal.style.opacity    = '0';
            cbPortal.style.transform  = 'scaleY(0.94) translateY(-6px)';
            cbPortal.style.transition = 'opacity .18s ease, transform .18s cubic-bezier(.22,.68,0,1.2)';
            
            cbpList.scrollTop = 0;

            cbPortal.offsetHeight;
            cbPortal.style.opacity   = '1';
            cbPortal.style.transform = 'scaleY(1) translateY(0)';
            portalOpen = true;

            combobox.querySelectorAll('.combobox-arrow').forEach(a => a.style.transform = 'rotate(180deg)');

            setTimeout(() => cbpSearch.focus(), 60);

            const reposition = () => { if (portalOpen) positionPortal(triggerEl); };
            window.addEventListener('scroll', reposition, { passive: true, capture: true });
            window.addEventListener('resize', reposition, { passive: true });
            cbPortal._cleanup = () => {
                window.removeEventListener('scroll', reposition, { capture: true });
                window.removeEventListener('resize', reposition);
            };
        }

        function closePortal() {
            if (!portalOpen) return;
            portalOpen = false;
            
            document.body.style.overflow = '';

            cbPortal.style.transition = 'opacity .14s ease, transform .14s ease';
            cbPortal.style.opacity    = '0';
            cbPortal.style.transform  = 'scaleY(0.96) translateY(-4px)';
            setTimeout(() => { cbPortal.style.display = 'none'; cbPortal.style.transform = ''; }, 150);
            if (activeCombobox) {
                activeCombobox.querySelectorAll('.combobox-arrow').forEach(a => a.style.transform = '');
            }
            if (cbPortal._cleanup) { cbPortal._cleanup(); cbPortal._cleanup = null; }
            activeCombobox = null;
        }

        cbpSearch.addEventListener('input', () => {
            const q = cbpSearch.value.trim().toLowerCase();
            let count = 0;
            cbpList.querySelectorAll('.combobox-option').forEach(opt => {
                const match = opt.dataset.label.toLowerCase().includes(q);
                opt.style.display = match ? 'block' : 'none';
                if (match) count++;
            });
            const emptyEl = cbpList.querySelector('.combobox-empty');
            if (emptyEl) emptyEl.style.display = count === 0 ? 'block' : 'none';
            cbpCount.textContent = q ? `${count} result${count !== 1 ? 's' : ''}` : `${PORTAL_TOTAL} products`;
        });

        cbpSearch.addEventListener('keydown', e => { if (e.key === 'Escape') closePortal(); });

        document.addEventListener('mousedown', (e) => {
            if (portalOpen && !cbPortal.contains(e.target) && !e.target.closest('.combobox-trigger')) {
                closePortal();
            }
        });

        // ── Combobox Init ────────────────────────────────────────────────
        function initCombobox(combobox) {
            const trigger  = combobox.querySelector('.combobox-trigger');
            const label    = combobox.querySelector('.combobox-label');
            const hidden   = combobox.querySelector('.product-id-input');

            trigger.addEventListener('click', (e) => {
                if (trigger.disabled) return;
                e.stopPropagation();

                if (portalOpen && activeCombobox === combobox) {
                    closePortal();
                    return;
                }
                if (portalOpen) closePortal();

                openPortal(trigger, combobox, (opt) => {
                    hidden.value         = opt.dataset.value;
                    hidden.dataset.price = opt.dataset.price;

                    const name       = opt.querySelector('.item-name-text')?.textContent.trim() || '';
                    const vol        = opt.querySelector('.item-volume-text')?.textContent.trim() || '';
                    const code       = opt.dataset.code       || '';
                    const price      = opt.dataset.price      || '0';
                    const stock      = opt.dataset.stock      || '0';
                    const stockLabel = opt.dataset.stockLabel || 'In Stock';
                    const stockCls   = opt.dataset.stockClass || 'bg-emerald-50 text-emerald-600 border border-emerald-200';
                    const initials   = opt.dataset.initials   || name.substring(0, 2).toUpperCase();
                    const avatarCol  = opt.dataset.avatarColor|| '#7c3aed';

                    const defState = combobox.querySelector('.trigger-default');
                    const selState = combobox.querySelector('.trigger-selected');
                    const avatar   = selState.querySelector('.selected-avatar');
                    avatar.textContent = initials;
                    avatar.style.background = `linear-gradient(135deg, ${avatarCol}, ${avatarCol}cc)`;
                    selState.querySelector('.selected-name').textContent   = name;
                    selState.querySelector('.selected-volume').textContent = vol;
                    selState.querySelector('.selected-code').textContent   = code;
                    const stockBadge = selState.querySelector('.selected-stock-badge');
                    stockBadge.textContent = `${stockLabel} (${stock})`;
                    stockBadge.className   = 'selected-stock-badge text-[9px] font-black px-1.5 py-0.5 rounded-md ' + stockCls;
                    selState.querySelector('.selected-price').textContent  = `RM ${parseFloat(price).toFixed(2)}`;

                    defState.classList.add('hidden');
                    selState.classList.remove('hidden');
                    selState.classList.add('flex');
                    trigger.classList.remove('border-slate-100', 'hover:border-violet-300');
                    trigger.classList.add('border-violet-300', 'bg-violet-50/30');

                    if (label) {
                        label.textContent = `${name} (${vol})`;
                        label.classList.remove('text-slate-400', 'font-medium');
                        label.classList.add('text-slate-700', 'font-bold');
                    }

                    const row = combobox.closest('.sale-item-row');
                    calculateTotal();
                });
            });

            document.addEventListener('click', (e) => {
                if (activeCombobox === combobox && !combobox.contains(e.target) && !cbPortal.contains(e.target)) {
                    closePortal();
                }
            });
        }

        // ── Row Listeners ────────────────────────────────────────────────
        function attachRowListeners(row) {
            const qtyInput = row.querySelector('.quantity-input');
            const qtyMinus = row.querySelector('.qty-minus');
            const qtyPlus  = row.querySelector('.qty-plus');

            qtyInput.addEventListener('input', calculateTotal);

            if (qtyMinus) {
                qtyMinus.addEventListener('click', () => {
                    const val = parseInt(qtyInput.value) || 1;
                    if (val > 1) { qtyInput.value = val - 1; calculateTotal(); }
                });
            }
            if (qtyPlus) {
                qtyPlus.addEventListener('click', () => {
                    qtyInput.value = (parseInt(qtyInput.value) || 0) + 1;
                    calculateTotal();
                });
            }

            const removeBtn = row.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    row.remove();
                    reindexRows();
                    calculateTotal();
                });
            }
        }

        // ── Re-index rows ────────────────────────────────────────────────
        function reindexRows() {
            const allRows = document.querySelectorAll('.sale-item-row');
            allRows.forEach((row, i) => {
                row.querySelectorAll('[name]').forEach(el => {
                    el.setAttribute('name', el.getAttribute('name').replace(/items\[\d+\]/g, `items[${i}]`));
                });
                // Update hidden inputs if any
                row.querySelectorAll('input[type="hidden"]').forEach(el => {
                    if (el.name) {
                        el.name = el.name.replace(/items\[\d+\]/g, `items[${i}]`);
                    }
                });
                const cb = row.querySelector('.item-combobox');
                if (cb) cb.dataset.index = i;
            });
        }

        // ── Add single row ───────────────────────────────────────────────
        function addSingleRow(opts = {}) {
            const rowEl = buildRow({
                showRemove: opts.showRemove !== false,
                productId: opts.productId || null,
                productName: opts.productName || null,
                volume: opts.volume || null,
                price: opts.price || null,
                qty: opts.qty || 1
            });
            singlesContainer.appendChild(rowEl);
            initCombobox(rowEl.querySelector('.item-combobox'));
            attachRowListeners(rowEl);
            updateRemoveButtons();
            calculateTotal();
        }

        // ── Get or Create Bundle visual group container ──────────────────
        function getOrCreateBundleGroup(promoId, promoName) {
            let groupEl = bundlesContainer.querySelector(`.bundle-group[data-promo-id="${promoId}"]`);
            if (!groupEl) {
                const empty = bundlesContainer.querySelector('.bundles-empty-state');
                if (empty) empty.classList.add('hidden');

                const promo = window.promoData.find(p => p.id == promoId);
                const discount = promo ? (promo.discount ?? 10) : 10;

                groupEl = document.createElement('div');
                groupEl.className = 'bundle-group bg-violet-50/20 border border-violet-100 rounded-2xl p-4 mb-4';
                groupEl.dataset.promoId = promoId;
                groupEl.dataset.promoName = promoName;
                groupEl.style.overflow = 'visible';
                
                groupEl.innerHTML = `
                    <div class="flex items-center justify-between mb-3 pb-3 border-b border-violet-100">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-violet-600 animate-pulse"></div>
                            <span class="text-xs font-black uppercase tracking-widest text-violet-750">Bundle: ${promoName}</span>
                            <span class="px-2 py-0.5 bg-violet-100 text-violet-700 text-[9px] font-black uppercase tracking-wider rounded-md">${discount}% Off</span>
                        </div>
                        <button type="button" class="remove-bundle-btn text-rose-500 hover:text-rose-700 text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remove Bundle
                        </button>
                    </div>
                    <div class="bundle-items-rows space-y-3" style="overflow:visible !important;"></div>
                `;

                groupEl.querySelector('.remove-bundle-btn').addEventListener('click', () => {
                    groupEl.remove();
                    
                    const remaining = bundlesContainer.querySelectorAll('.bundle-group');
                    if (remaining.length === 0) {
                        const empty = bundlesContainer.querySelector('.bundles-empty-state');
                        if (empty) empty.classList.remove('hidden');
                    }
                    
                    reindexRows();
                    calculateTotal();
                });

                bundlesContainer.appendChild(groupEl);
            }
            return groupEl.querySelector('.bundle-items-rows');
        }

        // ── Bundle Expansion ─────────────────────────────────────────────
        async function expandBundle(promoId, triggerRow) {
            const promo = window.promoData.find(p => p.id == promoId);
            const promoName = promo ? promo.name : '';
            try {
                const res = await fetch(`${window.bundleItemsBaseUrl}/${promoId}/bundle-items`, {
                    headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!data.items || data.items.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'No Products Found', text: 'This promotion has no associated products.', confirmButtonColor: '#7c3aed' });
                    return;
                }

                if (triggerRow) triggerRow.remove();

                const groupRowsContainer = getOrCreateBundleGroup(promoId, promoName);

                data.items.forEach(item => {
                    const rowEl = buildRow({
                        promoId: promoId, promoName: promoName,
                        productId: item.item_id, productName: item.item_name,
                        volume: item.volume, price: item.price,
                        stock: item.stock_qty, qty: 1, showRemove: true,
                    });
                    groupRowsContainer.appendChild(rowEl);
                    initCombobox(rowEl.querySelector('.item-combobox'));
                    attachRowListeners(rowEl);
                });

                reindexRows();
                updateRemoveButtons();
                calculateTotal();

                Swal.fire({
                    icon: 'success', title: 'Bundle Added',
                    text: `${data.items.length} item(s) from "${promoName}" added.`,
                    timer: 2000, showConfirmButton: false, toast: true, position: 'top-end'
                });
            } catch (err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load bundle items.', confirmButtonColor: '#7c3aed' });
            }
        }

        // ── Calculate Total ──────────────────────────────────────────────
        function calculateTotal() {
            let total = 0, totalQty = 0, itemCnt = 0;
            const summaryBundlesList = document.getElementById('summary-bundles-list');
            const summarySinglesList = document.getElementById('summary-singles-list');

            const bundlesData = {};
            const singlesData = [];

            // 1. Process bundle items
            const bundleRows = bundlesContainer.querySelectorAll('.sale-item-row');
            bundleRows.forEach(row => {
                const promoId = row.dataset.promoId;
                const promoName = row.dataset.promoName;
                const hidden   = row.querySelector('.product-id-input');
                const qtyInput = row.querySelector('.quantity-input');
                const rowSubEl = row.querySelector('.row-subtotal');
                if (hidden && hidden.value && hidden.dataset.price && qtyInput) {
                    const price = parseFloat(hidden.dataset.price);
                    const qty   = parseInt(qtyInput.value) || 0;
                    const productName = row.querySelector('.combobox-label')?.textContent || 'Product';
                    
                    const promo = window.promoData.find(p => p.id == promoId);
                    const discountPercent = promo ? (promo.discount ?? 10) : 10;
                    const discountedPrice = price * (1 - (discountPercent / 100));
                    const sub = discountedPrice * qty;

                    total += sub;
                    totalQty += qty;
                    itemCnt++;
                    
                    if (rowSubEl) {
                        rowSubEl.innerHTML = `
                            <span class="line-through text-[11px] text-slate-400 mr-1.5">RM ${(price * qty).toFixed(2)}</span>
                            <span class="text-violet-650 font-black">RM ${sub.toFixed(2)}</span>
                            <span class="text-[9px] text-emerald-500 font-bold ml-1">(${discountPercent}% Off)</span>
                        `;
                    }

                    if (!bundlesData[promoId]) {
                        bundlesData[promoId] = {
                            name: promoName,
                            discount: discountPercent,
                            items: []
                        };
                    }
                    bundlesData[promoId].items.push({
                        name: productName,
                        qty: qty,
                        subtotal: sub
                    });
                }
            });

            // 2. Process single items
            const singleRows = singlesContainer.querySelectorAll('.sale-item-row');
            singleRows.forEach(row => {
                const hidden   = row.querySelector('.product-id-input');
                const qtyInput = row.querySelector('.quantity-input');
                const rowSubEl = row.querySelector('.row-subtotal');
                if (hidden && hidden.value && hidden.dataset.price && qtyInput) {
                    const price = parseFloat(hidden.dataset.price);
                    const qty   = parseInt(qtyInput.value) || 0;
                    const productName = row.querySelector('.combobox-label')?.textContent || 'Product';
                    const sub   = price * qty;
                    
                    total += sub;
                    totalQty += qty;
                    itemCnt++;
                    
                    if (rowSubEl) rowSubEl.textContent = `RM ${sub.toFixed(2)}`;

                    singlesData.push({
                        name: productName,
                        qty: qty,
                        subtotal: sub
                    });
                }
            });

            // 3. Render Bundles in Summary list
            if (summaryBundlesList) {
                summaryBundlesList.innerHTML = '';
                const promoIds = Object.keys(bundlesData);
                if (promoIds.length === 0) {
                    summaryBundlesList.innerHTML = '<div class="text-[10px] text-slate-400 font-medium italic">No bundles selected</div>';
                } else {
                    promoIds.forEach(id => {
                        const b = bundlesData[id];
                        let bHtml = `
                            <div class="bg-violet-50/50 border border-violet-100 rounded-xl p-2.5">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-[10px] font-black text-violet-750 truncate max-w-[70%]">${b.name}</span>
                                    <span class="text-[8px] font-black bg-violet-100 text-violet-700 px-1.5 py-0.5 rounded-md">${b.discount}% OFF</span>
                                </div>
                                <div class="space-y-1 pl-2 border-l border-violet-150">
                        `;
                        let bSum = 0;
                        b.items.forEach(item => {
                            bSum += item.subtotal;
                            bHtml += `
                                <div class="flex justify-between text-[9px] text-slate-500 font-semibold">
                                    <span class="truncate max-w-[70%]">${item.qty}x ${item.name}</span>
                                    <span class="font-bold">RM ${item.subtotal.toFixed(2)}</span>
                                </div>
                            `;
                        });
                        bHtml += `
                                </div>
                                <div class="flex justify-between text-[9px] font-black text-violet-700 mt-1.5 pt-1.5 border-t border-dashed border-violet-150">
                                    <span>Bundle Total</span>
                                    <span>RM ${bSum.toFixed(2)}</span>
                                </div>
                            </div>
                        `;
                        summaryBundlesList.innerHTML += bHtml;
                    });
                }
            }

            // 4. Render Singles in Summary list
            if (summarySinglesList) {
                summarySinglesList.innerHTML = '';
                if (singlesData.length === 0) {
                    summarySinglesList.innerHTML = '<div class="text-[10px] text-slate-400 font-medium italic">No single items added</div>';
                } else {
                    singlesData.forEach(item => {
                        summarySinglesList.innerHTML += `
                            <div class="flex justify-between items-center text-[10px] text-slate-650 font-semibold bg-slate-50 p-2 rounded-xl border border-slate-100">
                                <span class="truncate max-w-[65%]">${item.qty}x ${item.name}</span>
                                <span class="font-black text-slate-800">RM ${item.subtotal.toFixed(2)}</span>
                            </div>
                        `;
                    });
                }
            }

            const tax   = total * 0.06;
            const grand = total + tax;
            const fmt   = `RM ${grand.toFixed(2)}`;

            if (totalDisp)    totalDisp.innerText    = fmt;
            if (subtotalDisp) subtotalDisp.innerText = `RM ${total.toFixed(2)}`;
            if (itemCountBadge) itemCountBadge.textContent = itemCnt;

            const al = getAlpine();
            if (al) {
                al.itemCount = itemCnt;
                al.totalQty  = totalQty;
                al.subtotal  = total;
                al.tax       = tax;
                al.total     = grand;
            }
        }

        // ── Remove button visibility ─────────────────────────────────────
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.sale-item-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-item');
                // Allow removing item if there is more than 1 item overall
                if (btn) btn.classList.toggle('hidden', rows.length <= 1);
            });
        }

        // ── Add Item Button ──────────────────────────────────────────────
        if (addSingleBtn) {
            addSingleBtn.addEventListener('click', () => addSingleRow({ showRemove: true }));
        }

        // ── Date Validation ──────────────────────────────────────────────
        const saleDateInput = document.getElementById('sale_date_input');
        const validationMsg = document.getElementById('date-validation-msg');

        function validateDateTime() {
            if (!saleDateInput.value) return true;
            const selectedDateTime = new Date(saleDateInput.value);
            const today = new Date(); today.setHours(0,0,0,0);
            const selectedDateOnly = new Date(selectedDateTime); selectedDateOnly.setHours(0,0,0,0);
            if (selectedDateOnly < today) {
                validationMsg.textContent = "Invalid date. You cannot select a past transaction date.";
                validationMsg.classList.remove('hidden');
                Swal.fire({ icon: 'error', title: 'Invalid Date', text: 'Invalid date. You cannot select a past transaction date.', confirmButtonColor: '#7c3aed' });
                const now = new Date();
                if (now.getHours() < 8 || now.getHours() > 20) now.setHours(12,0,0,0);
                saleDateInput.value = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0') + 'T' + String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');
                return false;
            }
            const hour = selectedDateTime.getHours(), minute = selectedDateTime.getMinutes();
            const timeValue = hour * 60 + minute;
            if (timeValue < 480 || timeValue > 1200) {
                validationMsg.textContent = "Transaction time must be between 8:00 AM and 8:00 PM only.";
                validationMsg.classList.remove('hidden');
                Swal.fire({ icon: 'warning', title: 'Invalid Time', text: 'Transaction time must be between 8:00 AM and 8:00 PM only.', confirmButtonColor: '#7c3aed' });
                selectedDateTime.setHours(hour < 8 ? 8 : 20, 0, 0, 0);
                saleDateInput.value = selectedDateTime.getFullYear() + '-' + String(selectedDateTime.getMonth()+1).padStart(2,'0') + '-' + String(selectedDateTime.getDate()).padStart(2,'0') + 'T' + String(selectedDateTime.getHours()).padStart(2,'0') + ':' + String(selectedDateTime.getMinutes()).padStart(2,'0');
                return false;
            }
            validationMsg.classList.add('hidden');
            return true;
        }
        if (saleDateInput) saleDateInput.addEventListener('change', validateDateTime);

        // ── Form Submit Validation ───────────────────────────────────────
        document.getElementById('sale-form').addEventListener('submit', function (e) {
            if (!validateDateTime()) { e.preventDefault(); return; }
            const rows = document.querySelectorAll('.sale-item-row');
            let hasUnfilled = false;
            const pairs = []; let hasDuplicate = false;
            rows.forEach(row => {
                const pid   = row.querySelector('.product-id-input')?.value?.trim();
                // Find promo ID
                let promoId = row.dataset.promoId || '';
                if (!pid) { hasUnfilled = true; return; }
                const key = pid + '|' + promoId;
                if (pairs.includes(key)) hasDuplicate = true;
                pairs.push(key);
            });
            if (hasUnfilled) { e.preventDefault(); Swal.fire({ icon: 'error', title: 'Incomplete Items', text: 'Please select a product for all line items.', confirmButtonColor: '#7c3aed' }); return; }
            if (hasDuplicate) { e.preventDefault(); Swal.fire({ icon: 'error', title: 'Duplicate Item', text: 'The same product cannot appear more than once under the same strategy link.', confirmButtonColor: '#7c3aed' }); }
        });

        // ── Save as Draft ────────────────────────────────────────────────
        const draftBtn = document.getElementById('save-draft-btn');
        if (draftBtn) {
            draftBtn.addEventListener('click', () => {
                Swal.fire({
                    icon: 'info', title: 'Draft Saved',
                    text: 'Your current transaction details have been noted. Submit the form to commit the transaction.',
                    confirmButtonColor: '#7c3aed'
                });
            });
        }

        // ── Mode watcher: remove extra rows in single mode ───────────────
        document.querySelectorAll('[x-data]').forEach(el => {
            el.addEventListener('saleMode:change', (e) => {
                if (e.detail === 'single') {
                    // Clear all bundles
                    bundlesContainer.querySelectorAll('.bundle-group').forEach(b => b.remove());
                    const empty = bundlesContainer.querySelector('.bundles-empty-state');
                    if (empty) empty.classList.remove('hidden');
                    
                    // Keep only 1 single row
                    const rows = singlesContainer.querySelectorAll('.sale-item-row');
                    rows.forEach((row, i) => { if (i > 0) row.remove(); });
                    if (rows.length === 0) {
                        addSingleRow({ showRemove: false });
                    }
                    updateRemoveButtons();
                    calculateTotal();
                } else {
                    // Switch to bundle mode
                    calculateTotal();
                }
            });
        });

        // ── Proactive Load Promotions ─────────────────────────────────────
        let activePromotionsList = [];
        let currentPromotionsPage = 1;
        const promotionsPerPage = 4;

        async function loadProactivePromotions() {
            const promoListContainer = document.getElementById('proactive-promotions-list');
            if (!promoListContainer) return;

            try {
                const res = await fetch("{{ route('api.promotions') }}");
                const data = await res.json();

                activePromotionsList = data.filter(p => p.status === 'Active');

                if (activePromotionsList.length === 0) {
                    promoListContainer.innerHTML = `
                        <div class="py-4 text-center text-slate-400 text-xs italic col-span-2">
                            No active bundle promotions available at the moment.
                        </div>
                    `;
                    document.getElementById('proactive-pagination')?.classList.add('hidden');
                    return;
                }

                currentPromotionsPage = 1;
                renderPromotionsPage();

            } catch (err) {
                console.error("Failed to load proactive promotions:", err);
                promoListContainer.innerHTML = `
                    <div class="py-4 text-center text-rose-500 text-xs italic col-span-2">
                        Failed to load active promotions.
                    </div>
                `;
                document.getElementById('proactive-pagination')?.classList.add('hidden');
            }
        }

        function renderPromotionsPage() {
            const promoListContainer = document.getElementById('proactive-promotions-list');
            const paginationContainer = document.getElementById('proactive-pagination');
            const prevBtn = document.getElementById('prev-page-btn');
            const nextBtn = document.getElementById('next-page-btn');
            const pageIndicator = document.getElementById('page-indicator');

            if (!promoListContainer) return;

            const totalPages = Math.ceil(activePromotionsList.length / promotionsPerPage);
            
            if (activePromotionsList.length <= promotionsPerPage) {
                paginationContainer?.classList.add('hidden');
            } else {
                paginationContainer?.classList.remove('hidden');
            }

            const startIndex = (currentPromotionsPage - 1) * promotionsPerPage;
            const endIndex = startIndex + promotionsPerPage;
            const pagePromos = activePromotionsList.slice(startIndex, endIndex);

            promoListContainer.innerHTML = '';
            pagePromos.forEach(promo => {
                const idStr = 'PR-' + String(promo.promo_id).padStart(4, '0');
                
                let itemsStr = '';
                if (promo.analysis) {
                    const ante = promo.analysis.antecedent_product ? promo.analysis.antecedent_product.item_name : promo.analysis.antecedent;
                    const cons = promo.analysis.consequent_product ? promo.analysis.consequent_product.item_name : promo.analysis.consequent;
                    itemsStr = `${ante} + ${cons}`;
                } else {
                    itemsStr = promo.promo_name;
                }

                const discountPercent = (promo.final_discount !== undefined && promo.final_discount !== null) ? parseInt(promo.final_discount) : 10;
                const discountStr = `${discountPercent}% Off`;
                const sourceLabel = promo.rule_id ? 'Market Basket Analysis AI' : 'Manual';

                const card = document.createElement('div');
                card.className = "premium-card p-4 bg-slate-50 hover:bg-violet-50/50 border border-slate-100 hover:border-violet-200 rounded-2xl flex flex-col justify-between transition-all duration-200 group shadow-sm";
                card.innerHTML = `
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black text-indigo-650">${idStr}</span>
                            <span class="px-2 py-0.5 bg-violet-100 text-violet-750 text-[8px] font-black uppercase tracking-wider rounded-md">${sourceLabel}</span>
                        </div>
                        <h4 class="text-xs font-black text-slate-700 mb-1 line-clamp-2">${itemsStr}</h4>
                        <p class="text-[10px] text-slate-400 font-medium mb-3">${promo.description || 'Special bundle recommendation.'}</p>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100/60">
                        <span class="text-xs font-black text-emerald-650">${discountStr}</span>
                        <button type="button" class="apply-promo-btn px-3.5 py-1.5 bg-violet-600 hover:bg-violet-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-md shadow-violet-100 hover:shadow-violet-200"
                                data-id="${promo.promo_id}">
                            Apply Bundle
                        </button>
                    </div>
                `;
                promoListContainer.appendChild(card);

                card.querySelector('.apply-promo-btn').addEventListener('click', function() {
                    const promoId = this.dataset.id;
                    
                    // If we have an empty single item row and nothing else, remove it
                    const singleRows = singlesContainer.querySelectorAll('.sale-item-row');
                    if (singleRows.length === 1 && !singleRows[0].querySelector('.product-id-input').value) {
                        singleRows[0].remove();
                    }
                    
                    expandBundle(promoId);
                });
            });

            if (prevBtn && nextBtn && pageIndicator) {
                prevBtn.disabled = currentPromotionsPage === 1;
                nextBtn.disabled = currentPromotionsPage === totalPages;
                pageIndicator.innerText = `Page ${currentPromotionsPage} of ${totalPages}`;
            }
        }

        // ── Init: add first row & load promotions proactive ──────────────
        loadProactivePromotions();
        addSingleRow({ showRemove: false });

        // Attach pagination listeners
        const prevBtn = document.getElementById('prev-page-btn');
        const nextBtn = document.getElementById('next-page-btn');
        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                if (currentPromotionsPage > 1) {
                    currentPromotionsPage--;
                    renderPromotionsPage();
                }
            });
            nextBtn.addEventListener('click', () => {
                const totalPages = Math.ceil(activePromotionsList.length / promotionsPerPage);
                if (currentPromotionsPage < totalPages) {
                    currentPromotionsPage++;
                    renderPromotionsPage();
                }
            });
        }
    });
    </script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `<ul style="list-style:none;padding:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                           </ul>`,
                    confirmButtonColor: '#7c3aed'
                });
            @endif
        });
    </script>
</x-app-layout>
