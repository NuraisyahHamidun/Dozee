<x-app-layout>
    @php
        $getLetterColor = function($name) {
            $firstLetter = strtoupper(substr(trim($name), 0, 1));
            switch($firstLetter) {
                case 'A': case 'H': case 'O': case 'V':
                    return 'from-rose-400 to-rose-600';
                case 'B': case 'I': case 'P': case 'W':
                    return 'from-indigo-400 to-indigo-600';
                case 'C': case 'J': case 'Q': case 'X':
                    return 'from-amber-400 to-amber-600';
                case 'D': case 'K': case 'R': case 'Y':
                    return 'from-emerald-400 to-emerald-600';
                case 'E': case 'L': case 'S': case 'Z':
                    return 'from-purple-400 to-purple-600';
                case 'F': case 'M': case 'T':
                    return 'from-cyan-400 to-cyan-600';
                default:
                    return 'from-pink-400 to-pink-600';
            }
        };
    @endphp

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </span>
                {{ __('Items Catalogue') }}
            </h2>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600 border border-indigo-100">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                View Only
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ── SEARCH & FILTER BAR ──────────────────────────────────── --}}
            <div class="premium-card bg-white dark:bg-slate-800 p-6">
                <form action="{{ route('salesmen.items.index') }}" method="GET"
                      class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                    {{-- Name / keyword search --}}
                    <div class="md:col-span-5">
                        <label for="search" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Search Item</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input id="search" name="search" type="text"
                                   value="{{ request('search') }}"
                                   placeholder="Search by name, category, description…"
                                   class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200 placeholder-slate-300" />
                        </div>
                    </div>

                    {{-- Category filter --}}
                    <div class="md:col-span-4">
                        <label for="category" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Category</label>
                        <select id="category" name="category"
                                class="w-full py-3 px-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Action buttons --}}
                    <div class="md:col-span-3 flex gap-3">
                        <button type="submit"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-[10px] uppercase tracking-widest py-3 rounded-2xl transition-all shadow-lg shadow-indigo-100 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Search
                        </button>
                        @if(request()->anyFilled(['search', 'category']))
                            <a href="{{ route('salesmen.items.index') }}"
                               class="p-3 bg-slate-100 dark:bg-slate-700 rounded-2xl text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors flex items-center justify-center"
                               title="Clear filters">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>

                </form>

                {{-- Active search badge --}}
                @if(request()->anyFilled(['search', 'category']))
                    <div class="mt-4 flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"></path></svg>
                        Showing <strong class="text-indigo-600">{{ $products->total() }}</strong> result(s)
                        @if(request('search'))
                            for <span class="italic">"{{ request('search') }}"</span>
                        @endif
                        @if(request('category'))
                            in <span class="font-black text-indigo-600">{{ request('category') }}</span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- ── ITEMS GRID ───────────────────────────────────────────── --}}
            @forelse($products as $product)

                {{-- Each card is clickable → opens read-only modal --}}
                <div style="display:none"><!-- spacer --></div>

            @empty
            @endforelse

            @if($products->isEmpty())
                <div class="premium-card bg-white dark:bg-slate-800 p-16 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400">No items found.</p>
                    <p class="text-xs text-slate-300 mt-1">Try adjusting your search filters.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach($products as $product)
                        {{-- Clickable item card --}}
                        <button type="button"
                                onclick="openItemModal({{ $product->item_id }})"
                                class="text-left premium-card bg-white dark:bg-slate-800 p-5 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 group cursor-pointer w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-3xl">

                            {{-- Colour avatar header --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $getLetterColor($product->item_name) }} flex items-center justify-center font-black text-lg text-white shadow-lg">
                                    {{ strtoupper(substr($product->item_name, 0, 1)) }}
                                </div>
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="inline-block px-2 py-1 text-[8px] font-black uppercase tracking-widest rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                                        {{ $product->categoryRelation->name ?? $product->category ?? 'Uncategorised' }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-750 text-slate-600 dark:text-slate-350 border border-slate-200/50 dark:border-slate-700">
                                        {{ $product->item_code }}
                                    </span>
                                </div>
                            </div>

                            {{-- Name --}}
                            <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tight leading-snug mb-1 group-hover:text-indigo-600 transition-colors">
                                {{ $product->item_name }}
                            </h3>

                            {{-- Description snippet --}}
                            <p class="text-[10px] text-slate-400 font-medium leading-relaxed mb-4 line-clamp-2">
                                {{ $product->description ?: 'No description available.' }}
                            </p>

                            {{-- Footer: price + stock --}}
                            <div class="flex items-center justify-between pt-3 border-t border-slate-50 dark:border-slate-700/50">
                                <span class="text-base font-black text-indigo-600">RM {{ number_format($product->price, 2) }}</span>
                                <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-lg
                                    {{ $product->stock_qty > 10
                                        ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400'
                                        : ($product->stock_qty > 0
                                            ? 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'
                                            : 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400') }}">
                                    @if($product->stock_qty > 10)
                                        In Stock ({{ $product->stock_qty }})
                                    @elseif($product->stock_qty > 0)
                                        Low Stock ({{ $product->stock_qty }})
                                    @else
                                        Out of Stock
                                    @endif
                                </span>
                            </div>

                            {{-- View detail hint --}}
                            <div class="mt-3 flex items-center gap-1 text-[9px] font-black uppercase tracking-widest text-slate-300 group-hover:text-indigo-400 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                View Details
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="mt-4 px-6 py-4 bg-white/50 dark:bg-slate-800/50 glass-effect border border-white/20 rounded-2xl shadow-sm">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>

    {{-- ── READ-ONLY ITEM DETAIL MODAL ──────────────────────────────────── --}}
    <div id="itemModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
         role="dialog" aria-modal="true" aria-labelledby="modalItemName">

        {{-- Backdrop --}}
        <div id="modalBackdrop"
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300"
             onclick="closeItemModal()"></div>

        {{-- Panel --}}
        <div id="modalPanel"
             class="relative bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 scale-95 opacity-0">

            {{-- Gradient top bar --}}
            <div id="modalTopBar" class="h-1.5 w-full bg-gradient-to-r from-indigo-500 to-purple-500"></div>

            {{-- Close button --}}
            <button onclick="closeItemModal()"
                    class="absolute top-4 right-4 w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500 hover:bg-rose-50 hover:text-rose-500 transition-all"
                    aria-label="Close modal">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="p-7">

                {{-- Header --}}
                <div class="flex items-center gap-4 mb-6">
                    <div id="modalAvatar"
                         class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center font-black text-2xl text-white shadow-lg flex-shrink-0">
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-400 mb-1 flex items-center gap-2">
                            Item Details
                            <span id="modalItemCode" class="px-2 py-0.5 text-[8px] font-black tracking-normal uppercase bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 rounded-lg"></span>
                        </p>
                        <h2 id="modalItemName" class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight leading-tight"></h2>
                    </div>
                </div>

                {{-- Read-only badge --}}
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-700 text-slate-500 mb-5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Read-only — view access only
                </div>

                {{-- Data grid --}}
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Price</p>
                        <p id="modalPrice" class="text-lg font-black text-indigo-600"></p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Stock</p>
                        <p id="modalStock" class="text-lg font-black text-slate-800 dark:text-white"></p>
                        <p id="modalStockLabel" class="text-[9px] font-bold uppercase tracking-widest mt-0.5"></p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Category</p>
                        <p id="modalCategory" class="text-sm font-black text-slate-700 dark:text-slate-200"></p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4">
                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Volume / Size</p>
                        <p id="modalVolume" class="text-sm font-black text-slate-700 dark:text-slate-200"></p>
                    </div>
                </div>

                {{-- Description --}}
                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4">
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">Description</p>
                    <p id="modalDescription" class="text-sm font-medium text-slate-600 dark:text-slate-300 leading-relaxed"></p>
                </div>

            </div>
        </div>
    </div>

    {{-- Pre-compute items JSON in PHP so Blade doesn't try to parse the closure --}}
    @php
        $itemsJson = $products->keyBy('item_id')->map(function($p) {
            return [
                'id'          => $p->item_id,
                'code'        => $p->item_code,
                'name'        => $p->item_name,
                'price'       => number_format($p->price, 2),
                'stock'       => $p->stock_qty,
                'category'    => optional($p->categoryRelation)->name ?? $p->category ?? 'Uncategorised',
                'volume'      => $p->volume,
                'description' => $p->description ?: 'No description provided for this item.',
            ];
        });
    @endphp

    {{-- ── PRODUCT DATA + MODAL SCRIPT ─────────────────────────────────── --}}
    <script>
        // Embed all product data as JSON to avoid extra AJAX calls
        const itemsData = {!! json_encode($itemsJson) !!};

        const gradients = [
            'from-rose-400 to-rose-600',
            'from-indigo-400 to-indigo-600',
            'from-amber-400 to-amber-600',
            'from-emerald-400 to-emerald-600',
            'from-purple-400 to-purple-600',
            'from-cyan-400 to-cyan-600',
            'from-pink-400 to-pink-600',
        ];

        function getGradient(name) {
            const letter = name.charAt(0).toUpperCase();
            const groups = ['AHOV','BIPW','CJQX','DKRY','ELSZ','FMT'];
            for (let i = 0; i < groups.length; i++) {
                if (groups[i].includes(letter)) return gradients[i];
            }
            return gradients[6];
        }

        function openItemModal(id) {
            const item   = itemsData[id];
            if (!item) return;

            // Populate fields
            document.getElementById('modalItemCode').textContent    = item.code;
            document.getElementById('modalItemName').textContent    = item.name;
            document.getElementById('modalAvatar').textContent      = item.name.charAt(0).toUpperCase();
            document.getElementById('modalPrice').textContent       = 'RM ' + item.price;
            document.getElementById('modalStock').textContent       = item.stock;
            document.getElementById('modalCategory').textContent    = item.category;
            document.getElementById('modalVolume').textContent      = item.volume || '—';
            document.getElementById('modalDescription').textContent = item.description;

            // Stock label & colour
            const stockLabel = document.getElementById('modalStockLabel');
            const stockEl    = document.getElementById('modalStock');
            if (item.stock > 10) {
                stockLabel.textContent = 'In Stock';
                stockLabel.className   = 'text-[9px] font-bold uppercase tracking-widest mt-0.5 text-emerald-500';
                stockEl.className      = 'text-lg font-black text-emerald-600';
            } else if (item.stock > 0) {
                stockLabel.textContent = 'Low Stock';
                stockLabel.className   = 'text-[9px] font-bold uppercase tracking-widest mt-0.5 text-amber-500';
                stockEl.className      = 'text-lg font-black text-amber-600';
            } else {
                stockLabel.textContent = 'Out of Stock';
                stockLabel.className   = 'text-[9px] font-bold uppercase tracking-widest mt-0.5 text-rose-500';
                stockEl.className      = 'text-lg font-black text-rose-600';
            }

            // Avatar gradient
            const avatar = document.getElementById('modalAvatar');
            avatar.className = avatar.className.replace(/from-\S+ to-\S+/, '');
            avatar.classList.add(...getGradient(item.name).split(' '));

            // Show modal with animation
            const modal  = document.getElementById('itemModal');
            const panel  = document.getElementById('modalPanel');
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                panel.classList.remove('scale-95', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });

            document.body.classList.add('overflow-hidden');
        }

        function closeItemModal() {
            const modal = document.getElementById('itemModal');
            const panel = document.getElementById('modalPanel');
            panel.classList.add('scale-95', 'opacity-0');
            panel.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 200);
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeItemModal();
        });
    </script>

</x-app-layout>
