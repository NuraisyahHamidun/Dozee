<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-lg text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </span>
                {{ __('Edit Transaction Record') }}
            </h2>
        </div>
    </x-slot>

    {{-- Pass PHP data to JS --}}
    @php
        $promoDataMapped = $promotions->map(function($p) {
            return [
                'id' => $p->promo_id,
                'name' => $p->promo_name,
                'product_ids' => $p->products->pluck('item_id')->toArray()
            ];
        })->values();

        $existingSaleItemsMapped = $sale->saleItems->map(function($si) {
            return [
                'detail_id'   => $si->detail_id,
                'product_id'  => $si->item_id,
                'product_name'=> $si->product?->item_name,
                'volume'      => $si->product?->volume,
                'price'       => $si->product?->price,
                'stock_qty'   => $si->product?->stock_qty,
                'quantity'    => $si->quantity,
                'promo_id'    => $si->promo_id,
                'promo_name'  => $si->promotion?->promo_name,
            ];
        })->values();
    @endphp

    <script>
        window.bundleItemsBaseUrl = "{{ url('/promotions') }}";
        window.csrfToken = "{{ csrf_token() }}";
        window.promoData = @json($promoDataMapped);

        // Pre-existing sale items from DB (for initialising rows)
        window.existingSaleItems = @json($existingSaleItemsMapped);
    </script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('sales.update', $sale) }}" id="sale-form">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Items Section -->
                    <div class="lg:col-span-9 space-y-6">
                        <div class="premium-card bg-white dark:bg-slate-800 border-none p-8 min-h-[600px]">
                            <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-8 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                Transaction Header
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                                <div>
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Event Name / Session</label>
                                    <div class="event-combobox relative">
                                        <input type="text" name="event_name" id="event_name_input" autocomplete="off"
                                               placeholder="e.g. Roadshow Midvalley, Morning Session" value="{{ $sale->event_name }}"
                                               class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner">
                                        
                                        <!-- Suggestions Panel -->
                                        <div class="event-suggestions hidden absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-2xl shadow-2xl shadow-slate-200/60 border border-slate-100 dark:border-slate-700 overflow-hidden">
                                            <ul class="max-h-60 overflow-y-auto py-2">
                                                @foreach($existingEvents as $event)
                                                    <li class="suggestion-item px-5 py-3 text-sm font-bold text-slate-700 dark:text-slate-200 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors border-b border-slate-50 dark:border-slate-700/30 last:border-0"
                                                        data-value="{{ $event }}">
                                                        {{ $event }}
                                                    </li>
                                                @endforeach
                                                <li class="no-suggestions hidden px-5 py-3 text-xs text-slate-400 font-medium italic">No matches found.</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <p class="text-[9px] text-slate-400 mt-2 italic">Optional: Tag this sale to a specific event for pattern analysis.</p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Transaction Date</label>
                                    <input type="datetime-local" name="sale_date" id="sale_date_input" value="{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d\TH:i') }}" min="{{ date('Y-m-d\T00:00') }}" required
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner">
                                    <p class="text-[10px] text-slate-400 mt-2 font-medium">Allowed time: 8:00 AM – 8:00 PM only</p>
                                    <p id="date-validation-msg" class="text-[10px] text-rose-500 mt-1 font-bold hidden"></p>
                                </div>
                            </div>

                            <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-8 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Line Items
                            </h3>

                            <div id="items-container" class="space-y-3">
                                {{-- Rows are rendered by JS from window.existingSaleItems --}}
                            </div>

                            <div class="mt-8 flex gap-3">
                                <button type="button" id="add-item" class="w-full py-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-center gap-3 text-slate-400 hover:text-indigo-600 hover:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/5 transition-all group">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                    <span class="text-xs font-black uppercase tracking-widest">Add Item</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Sidebar -->
                    <div class="lg:col-span-3 space-y-6">
                        <div class="premium-card bg-white dark:bg-slate-800 p-8 border-none sticky top-24 shadow-xl shadow-indigo-50/50">
                            <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-6">Valuation Overview</h3>
                            
                            <div class="space-y-4 mb-10">
                                <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl">
                                    <div class="flex justify-between items-center text-slate-500 mb-1">
                                        <span class="text-[9px] font-black uppercase tracking-widest">Gross Estimation</span>
                                        <span class="font-bold text-xs" id="subtotal-display">RM 0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Strategies</span>
                                        <span class="font-black text-[9px] uppercase text-indigo-500">Auto-Applied</span>
                                    </div>
                                </div>

                                <div class="px-2 pt-4">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-800 dark:text-white">Total Payable</span>
                                    </div>
                                    <div class="text-4xl font-black text-indigo-600 tracking-tighter" id="total-estimation">RM 0.00</div>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-indigo-600 text-white font-black text-xs uppercase tracking-[0.2em] py-5 rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Update Transaction
                            </button>
                            
                            <a href="{{ route('sales.index') }}" class="block w-full text-center mt-6 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Cancel</a>
                        </div>
                        
                        <div class="premium-card p-6 bg-slate-900 text-white">
                             <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Strategic Compliance</h4>
                             <p class="text-[11px] font-medium leading-relaxed text-slate-400">
                                This sale is being recorded at point-of-entry. All logic is tracked for end-of-day intelligence reports.
                             </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Item row HTML template (hidden) -->
    <template id="item-row-template">
        <div class="sale-item-row group grid grid-cols-1 md:grid-cols-12 gap-4 items-end pb-4 border-b border-slate-50 dark:border-slate-700/50 last:border-0 rounded-xl px-3 py-3 transition-all" data-promo-id="" data-promo-name="">
            <!-- detail_id (for existing items) -->
            <input type="hidden" name="items[__IDX__][detail_id]" class="detail-id-input" value="">
            <!-- Bundle badge (hidden for single items) -->
            <div class="md:col-span-12 bundle-badge hidden">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                    <span class="text-[9px] font-black uppercase tracking-widest text-violet-500 bundle-badge-name"></span>
                </div>
            </div>
            <div class="md:col-span-4">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Item Selection</label>
                <div class="item-combobox relative" data-index="__IDX__">
                    <input type="hidden" name="items[__IDX__][product_id]" class="product-id-input" required>
                    <button type="button"
                        class="combobox-trigger w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner flex items-center justify-between gap-2 text-left">
                        <span class="combobox-label truncate text-slate-400 font-medium">Select an item...</span>
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0 transition-transform combobox-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <!-- Dropdown Panel -->
                    <div class="combobox-panel hidden absolute z-50 mt-2 w-full bg-white dark:bg-slate-800 rounded-2xl shadow-2xl shadow-slate-200/60 border border-slate-100 dark:border-slate-700 overflow-hidden">
                        <div class="p-3 border-b border-slate-50 dark:border-slate-700">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <input type="text" class="combobox-search w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 border-none focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Search item...">
                            </div>
                        </div>
                        <ul class="combobox-list max-h-[400px] overflow-y-auto py-1">
                            @foreach($products as $product)
                            <li class="combobox-option px-5 py-4 flex items-center justify-between cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors group/opt border-b border-slate-50 dark:border-slate-700/30 last:border-0"
                                data-value="{{ $product->item_id }}"
                                data-price="{{ $product->price }}"
                                data-stock="{{ $product->stock_qty }}"
                                data-label="{{ $product->item_name }} {{ $product->volume }} (RM {{ number_format($product->price, 2) }})">
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="item-name-text text-sm font-bold text-slate-700 dark:text-slate-200 truncate">{{ $product->item_name }}</span>
                                        <span class="item-volume-text text-[10px] px-2 py-0.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 rounded-md font-black uppercase">{{ $product->volume }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium">Stock: {{ $product->stock_qty }}</span>
                                </div>
                                <span class="item-price-text text-sm font-black text-indigo-500 flex-shrink-0 ml-4">RM {{ number_format($product->price, 2) }}</span>
                            </li>
                            @endforeach
                            <li class="combobox-empty hidden px-4 py-8 text-center text-xs text-slate-400 font-medium italic">No items found.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="md:col-span-3">
                <div class="item-type-container hidden mb-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Item Type</label>
                    <select class="item-type-select w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-xs font-bold text-slate-700 dark:text-slate-200 shadow-inner">
                        <option value="single">Single Item</option>
                        <option value="bundle">Bundle / Promotion</option>
                    </select>
                </div>
                <div class="promo-select-container hidden">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Strategy Link</label>
                    <select name="items[__IDX__][promo_id]" class="promo-select w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-xs font-bold text-slate-700 dark:text-slate-200 shadow-inner">
                        <option value="">Select Bundle...</option>
                    </select>
                </div>
            </div>
            <div class="md:col-span-4">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Units</label>
                <input name="items[__IDX__][quantity]" type="number" min="1" value="1" required
                       class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner quantity-input text-center">
            </div>
            <div class="md:col-span-1 flex justify-end">
                <button type="button" class="remove-item p-3.5 text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-xl transition-all hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    </template>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const container    = document.getElementById('items-container');
        const addBtn       = document.getElementById('add-item');
        const totalDisp    = document.getElementById('total-estimation');
        const subtotalDisp = document.getElementById('subtotal-display');
        const template     = document.getElementById('item-row-template');
        let itemIndex      = 0;

        // ── Event Combobox Logic ────────────────────────────────────────────
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

        eventInput.addEventListener('focus', () => {
            if (suggestionItems.length > 0) { eventPanel.classList.remove('hidden'); filterEvents(eventInput.value); }
        });
        eventInput.addEventListener('input', () => { eventPanel.classList.remove('hidden'); filterEvents(eventInput.value); });
        suggestionItems.forEach(item => {
            item.addEventListener('click', () => { eventInput.value = item.dataset.value; eventPanel.classList.add('hidden'); });
        });
        document.addEventListener('click', (e) => { if (!eventCombobox.contains(e.target)) eventPanel.classList.add('hidden'); });

        // ── Row Builder ─────────────────────────────────────────────────────
        function buildRow(opts = {}) {
            // opts: { detailId, promoId, promoName, productId, productName, volume, price, qty, lockCombobox, showRemove }
            const idx  = itemIndex++;
            const frag = template.content.cloneNode(true);
            const row  = frag.querySelector('.sale-item-row');

            row.innerHTML = row.innerHTML.replaceAll('__IDX__', idx);

            // detail_id for existing records
            if (opts.detailId) {
                row.querySelector('.detail-id-input').value = opts.detailId;
            }

            // Bundle badge
            if (opts.promoId) {
                row.dataset.promoId   = opts.promoId;
                row.dataset.promoName = opts.promoName || '';
                row.classList.add('border-l-4', 'border-violet-400', 'bg-violet-50/40', 'dark:bg-violet-500/5');
                const badge = row.querySelector('.bundle-badge');
                badge.classList.remove('hidden');
                badge.querySelector('.bundle-badge-name').textContent = opts.promoName || '';

                // Lock strategy link dropdown (hide both dropdown containers)
                const promoSelect = row.querySelector('.promo-select');
                promoSelect.value = opts.promoId;
                row.querySelector('.promo-select-container').classList.add('hidden');
                row.querySelector('.item-type-container').classList.add('hidden');
            }

            // Pre-fill product
            if (opts.productId) {
                const hiddenInput = row.querySelector('.product-id-input');
                hiddenInput.value           = opts.productId;
                hiddenInput.dataset.price   = opts.price || 0;
                const lbl = row.querySelector('.combobox-label');
                lbl.textContent = opts.productName ? `${opts.productName} (${opts.volume || ''})` : 'Select an item...';
                lbl.classList.remove('text-slate-400');
                lbl.classList.add('text-slate-700', 'dark:text-slate-200');
            }

            // Lock combobox for bundle items
            if (opts.lockCombobox) {
                row.querySelector('.combobox-trigger').disabled = true;
                row.querySelector('.combobox-trigger').classList.add('opacity-60', 'cursor-not-allowed');
            }

            // Set promo_id on select
            if (opts.promoId) {
                const sel = row.querySelector('.promo-select');
                sel.value = opts.promoId;
            }

            // Quantity
            if (opts.qty) {
                row.querySelector('.quantity-input').value = opts.qty;
            }

            // Remove button
            if (opts.showRemove !== false) {
                row.querySelector('.remove-item').classList.remove('hidden');
            }

            return row;
        }

        // ── Init Combobox ────────────────────────────────────────────────────
        function initCombobox(combobox) {
            const trigger  = combobox.querySelector('.combobox-trigger');
            const panel    = combobox.querySelector('.combobox-panel');
            const search   = combobox.querySelector('.combobox-search');
            const list     = combobox.querySelector('.combobox-list');
            const label    = combobox.querySelector('.combobox-label');
            const arrow    = combobox.querySelector('.combobox-arrow');
            const hidden   = combobox.querySelector('.product-id-input');
            const emptyMsg = combobox.querySelector('.combobox-empty');

            function openPanel() {
                panel.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
                search.value = '';
                filterOptions('');
                search.focus();
            }
            function closePanel() {
                panel.classList.add('hidden');
                arrow.style.transform = '';
            }

            trigger.addEventListener('click', (e) => {
                if (trigger.disabled) return;
                e.stopPropagation();
                panel.classList.contains('hidden') ? openPanel() : closePanel();
            });

            search.addEventListener('input', () => filterOptions(search.value.trim()));

            function filterOptions(query) {
                const q = query.toLowerCase();
                let anyVisible = false;
                list.querySelectorAll('.combobox-option').forEach(opt => {
                    const match = opt.dataset.label.toLowerCase().includes(q);
                    opt.style.display = match ? '' : 'none';
                    if (match) anyVisible = true;
                });
                emptyMsg.classList.toggle('hidden', anyVisible);
            }

            list.querySelectorAll('.combobox-option').forEach(opt => {
                opt.addEventListener('click', () => {
                    hidden.value           = opt.dataset.value;
                    hidden.dataset.price   = opt.dataset.price;
                    const name = opt.querySelector('.item-name-text').textContent.trim();
                    const vol  = opt.querySelector('.item-volume-text').textContent.trim();
                    label.textContent = `${name} (${vol})`;
                    label.classList.remove('text-slate-400');
                    label.classList.add('text-slate-700', 'dark:text-slate-200');
                    closePanel();
                    const row = combobox.closest('.sale-item-row');
                    handleProductChange(row, opt.dataset.value);
                    calculateTotal();
                });
            });

            document.addEventListener('click', (e) => { if (!combobox.contains(e.target)) closePanel(); });
        }

        // ── Smart Item Type & Strategy link display logic ───────────────────
        function handleProductChange(row, productId) {
            const itemTypeContainer = row.querySelector('.item-type-container');
            const itemTypeSelect    = row.querySelector('.item-type-select');
            const promoSelectContainer = row.querySelector('.promo-select-container');
            const promoSelect       = row.querySelector('.promo-select');

            if (!productId) {
                itemTypeContainer.classList.add('hidden');
                promoSelectContainer.classList.add('hidden');
                promoSelect.value = '';
                return;
            }

            const pid = parseInt(productId);
            // Find active promotions containing this product
            const matchingPromos = window.promoData.filter(promo => promo.product_ids.includes(pid));
            const hasBundle = matchingPromos.length > 0;

            if (hasBundle) {
                // Show option select container
                itemTypeContainer.classList.remove('hidden');
                itemTypeSelect.value = 'single';
                
                // Populate Strategy Link select with matching promos
                promoSelect.innerHTML = '<option value="">Select Bundle...</option>';
                matchingPromos.forEach(promo => {
                    const opt = document.createElement('option');
                    opt.value = promo.id;
                    opt.textContent = promo.name;
                    promoSelect.appendChild(opt);
                });
                
                promoSelectContainer.classList.add('hidden');
                promoSelect.value = '';
            } else {
                // Hide bundle section completely
                itemTypeContainer.classList.add('hidden');
                itemTypeSelect.value = 'single';
                promoSelectContainer.classList.add('hidden');
                promoSelect.innerHTML = '<option value="">None</option>';
                promoSelect.value = '';
            }
        }

        // ── Row Listeners ────────────────────────────────────────────────────
        function attachRowListeners(row) {
            row.querySelector('.quantity-input').addEventListener('input', calculateTotal);
            const removeBtn = row.querySelector('.remove-item');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => { row.remove(); updateRemoveButtons(); calculateTotal(); });
            }

            // Item type selector logic
            const itemTypeSelect = row.querySelector('.item-type-select');
            const promoSelectContainer = row.querySelector('.promo-select-container');
            const promoSelect = row.querySelector('.promo-select');

            if (itemTypeSelect) {
                itemTypeSelect.addEventListener('change', function () {
                    if (this.value === 'bundle') {
                        promoSelectContainer.classList.remove('hidden');
                    } else {
                        promoSelectContainer.classList.add('hidden');
                        promoSelect.value = '';
                        calculateTotal();
                    }
                });
            }

            // Strategy Link change → bundle expansion (for single item rows only)
            if (promoSelect && !row.dataset.promoId) {
                promoSelect.addEventListener('change', function () {
                    const promoId = this.value;
                    if (promoId) expandBundle(promoId, row);
                });
            }
        }

        // ── Calculate Total ──────────────────────────────────────────────────
        function calculateTotal() {
            let total = 0;
            container.querySelectorAll('.sale-item-row').forEach(row => {
                const hidden   = row.querySelector('.product-id-input');
                const qtyInput = row.querySelector('.quantity-input');
                if (hidden && hidden.value && hidden.dataset.price && qtyInput.value) {
                    total += parseFloat(hidden.dataset.price) * parseInt(qtyInput.value);
                }
            });
            const formatted = `RM ${total.toFixed(2)}`;
            totalDisp.innerText    = formatted;
            subtotalDisp.innerText = formatted;
        }

        // ── Update remove button visibility ──────────────────────────────────
        function updateRemoveButtons() {
            const rows = container.querySelectorAll('.sale-item-row');
            rows.forEach(row => {
                const btn = row.querySelector('.remove-item');
                if (btn) btn.classList.toggle('hidden', rows.length <= 1);
            });
        }

        // ── Add single item row ──────────────────────────────────────────────
        function addSingleRow() {
            const rowEl = buildRow({ showRemove: true });
            container.appendChild(rowEl);
            initCombobox(rowEl.querySelector('.item-combobox'));
            attachRowListeners(rowEl);
            updateRemoveButtons();
            calculateTotal();
        }

        // ── Bundle Expansion (AJAX) ──────────────────────────────────────────
        async function expandBundle(promoId, triggerRow) {
            const promoName = triggerRow?.querySelector('.promo-select')?.selectedOptions[0]?.text || triggerRow?.dataset?.promoName || '';
            try {
                const res = await fetch(`${window.bundleItemsBaseUrl}/${promoId}/bundle-items`, {
                    headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (!data.items || data.items.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Products Found',
                        text: 'This promotion has no associated products. It may need to be linked to an association rule first.',
                        confirmButtonColor: '#4f46e5'
                    });
                    if (triggerRow && triggerRow.isConnected) triggerRow.querySelector('.promo-select').value = '';
                    return;
                }

                if (triggerRow && triggerRow.isConnected) triggerRow.remove();

                data.items.forEach(item => {
                    const rowEl = buildRow({
                        promoId:      promoId,
                        promoName:    promoName,
                        productId:    item.item_id,
                        productName:  item.item_name,
                        volume:       item.volume,
                        price:        item.price,
                        qty:          1,
                        lockCombobox: true,
                        showRemove:   true,
                    });
                    container.appendChild(rowEl);
                    initCombobox(rowEl.querySelector('.item-combobox'));
                    attachRowListeners(rowEl);
                });

                updateRemoveButtons();
                calculateTotal();

                Swal.fire({
                    icon: 'success',
                    title: `Bundle Added`,
                    text: `${data.items.length} item(s) from "${promoName}" added.`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });

            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load bundle items. Please try again.', confirmButtonColor: '#4f46e5' });
            }
        }

        // ── Seed existing sale items from DB ─────────────────────────────────
        if (window.existingSaleItems && window.existingSaleItems.length > 0) {
            window.existingSaleItems.forEach(si => {
                const isBundle = !!si.promo_id;
                const rowEl = buildRow({
                    detailId:     si.detail_id,
                    promoId:      si.promo_id || null,
                    promoName:    si.promo_name || '',
                    productId:    si.product_id,
                    productName:  si.product_name,
                    volume:       si.volume,
                    price:       si.price,
                    qty:          si.quantity,
                    lockCombobox: isBundle,
                    showRemove:   true,
                });
                container.appendChild(rowEl);
                initCombobox(rowEl.querySelector('.item-combobox'));
                attachRowListeners(rowEl);
                if (!isBundle) {
                    handleProductChange(rowEl, si.product_id);
                }
            });
            updateRemoveButtons();
            calculateTotal();
        } else {
            // Fallback: show one blank row
            const firstRow = buildRow({ showRemove: false });
            container.appendChild(firstRow);
            initCombobox(firstRow.querySelector('.item-combobox'));
            attachRowListeners(firstRow);
        }

        // ── Add Item Button ─────────────────────────────────────────────────
        addBtn.addEventListener('click', addSingleRow);

        const saleDateInput = document.getElementById('sale_date_input');
        const validationMsg = document.getElementById('date-validation-msg');

        function validateDateTime() {
            if (!saleDateInput.value) return true;
            
            const selectedDateTime = new Date(saleDateInput.value);
            const today = new Date();
            today.setHours(0,0,0,0);
            
            const selectedDateOnly = new Date(selectedDateTime);
            selectedDateOnly.setHours(0,0,0,0);
            
            if (selectedDateOnly < today) {
                validationMsg.textContent = "Invalid date. You cannot select a past transaction date.";
                validationMsg.classList.remove('hidden');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'Invalid date. You cannot select a past transaction date.',
                    confirmButtonColor: '#4f46e5'
                });
                
                // Reset to today
                const now = new Date();
                let hour = now.getHours();
                if (hour < 8 || hour > 20) {
                    now.setHours(12, 0, 0, 0);
                }
                const formattedNow = now.getFullYear() + '-' + 
                    String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(now.getDate()).padStart(2, '0') + 'T' + 
                    String(now.getHours()).padStart(2, '0') + ':' + 
                    String(now.getMinutes()).padStart(2, '0');
                
                saleDateInput.value = formattedNow;
                return false;
            }
            
            const hour = selectedDateTime.getHours();
            const minute = selectedDateTime.getMinutes();
            const timeValue = hour * 60 + minute;
            const startLimit = 8 * 60; // 8:00 AM
            const endLimit = 20 * 60;  // 8:00 PM
            
            if (timeValue < startLimit || timeValue > endLimit) {
                validationMsg.textContent = "Transaction time must be between 8:00 AM and 8:00 PM only.";
                validationMsg.classList.remove('hidden');
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Time',
                    text: 'Transaction time must be between 8:00 AM and 8:00 PM only.',
                    confirmButtonColor: '#4f46e5'
                });
                
                if (hour < 8) {
                    selectedDateTime.setHours(8, 0, 0, 0);
                } else {
                    selectedDateTime.setHours(20, 0, 0, 0);
                }
                
                const formattedTime = selectedDateTime.getFullYear() + '-' + 
                    String(selectedDateTime.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(selectedDateTime.getDate()).padStart(2, '0') + 'T' + 
                    String(selectedDateTime.getHours()).padStart(2, '0') + ':' + 
                    String(selectedDateTime.getMinutes()).padStart(2, '0');
                
                saleDateInput.value = formattedTime;
                return false;
            }
            
            validationMsg.classList.add('hidden');
            return true;
        }

        if (saleDateInput) {
            saleDateInput.addEventListener('change', validateDateTime);
        }

        // ── Form Submit Validation ────────────────────────────────────────────
        document.getElementById('sale-form').addEventListener('submit', function (e) {
            if (!validateDateTime()) {
                e.preventDefault();
                return;
            }

            const rows = container.querySelectorAll('.sale-item-row');
            let hasUnfilled = false;
            const pairs = [];
            let hasDuplicate = false;

            rows.forEach(row => {
                const pid   = row.querySelector('.product-id-input')?.value?.trim();
                const promoSelect = row.querySelector('.promo-select');
                const promoId = promoSelect ? promoSelect.value : '';
                if (!pid) { hasUnfilled = true; return; }
                const key = pid + '|' + promoId;
                if (pairs.includes(key)) hasDuplicate = true;
                pairs.push(key);
            });

            if (hasUnfilled) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Incomplete Items', text: 'Please select a product for all line items.', confirmButtonColor: '#4f46e5' });
                return;
            }
            if (hasDuplicate) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Duplicate Item', text: 'The same product cannot appear more than once under the same strategy link. Adjust quantities instead.', confirmButtonColor: '#4f46e5' });
            }
        });
    });
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `<ul style="list-style: none; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                           </ul>`,
                    confirmButtonColor: '#e11d48'
                });
            @endif
        });
    </script>
</x-app-layout>
