<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
                {{ __('Market Association Discovery') }}
            </h2>
            <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Market Basket Analysis
                </span>
                <span class="text-slate-300">|</span>
                <span>{{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if($analysisRan && $totalTransactions === 0)
                <div class="rounded-2xl bg-amber-50 border border-amber-200 p-5 flex items-start gap-4">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="font-bold text-amber-800 text-sm">No multi-item transactions found</p>
                        <p class="text-amber-600 text-xs mt-0.5">Apriori requires transactions containing at least 2 different items. Add more sales data and try again.</p>
                    </div>
                </div>
            @endif

            {{-- ── KPI STATS ─────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">

                {{-- Total Transactions --}}
                <div class="premium-card bg-white p-5 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-indigo-50 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45C5.09 14.32 5 14.65 5 15c0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63H19c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Transactions</p>
                    <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['total_sales']) }}</h3>
                    <p class="text-[10px] text-indigo-600 font-bold mt-2">Sales history analysed</p>
                </div>

                {{-- Items Tracked --}}
                <div class="premium-card bg-white p-5 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-emerald-50 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Items Tracked</p>
                    <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['total_products']) }}</h3>
                    <p class="text-[10px] text-emerald-600 font-bold mt-2">Products in inventory</p>
                </div>

                {{-- Rules Found --}}
                <div class="premium-card bg-white p-5 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-amber-50 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Rules Found</p>
                    <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['rules_count']) }}</h3>
                    <p class="text-[10px] text-amber-600 font-bold mt-2">Above confidence threshold</p>
                </div>

                {{-- Max Lift --}}
                <div class="premium-card bg-white p-5 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-rose-50 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Max Lift</p>
                    <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['top_lift'], 2) }}</h3>
                    <p class="text-[10px] text-rose-600 font-bold mt-2">Strongest association</p>
                </div>

                {{-- Avg Confidence --}}
                <div class="premium-card bg-white p-5 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-purple-50 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Avg Confidence</p>
                    <h3 class="text-3xl font-black text-slate-800 tracking-tight">{{ number_format($stats['avg_confidence'] * 100, 1) }}%</h3>
                    <p class="text-[10px] text-purple-600 font-bold mt-2">Mean rule certainty</p>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- ── SIDEBAR: FILTERS ──────────────────────────────────────────── --}}
                <div class="lg:col-span-3 space-y-6">
                    <div class="premium-card bg-white p-6 sticky top-6">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest mb-6 border-b border-slate-100 pb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m10 4a2 2 0 100-4m0 4a2 2 0 110-4M14 4h6m-6 8h6m-6 8h6m-14 0h6m-14-8h6m-14-4h6"></path></svg>
                            Analysis Settings
                        </h3>

                        <form method="GET" action="{{ route('analysis.index') }}" class="space-y-6">
                            {{-- Event Filter --}}
                            <div class="space-y-2">
                                <x-input-label for="event_name" :value="__('Specific Event')" class="font-bold text-[10px] uppercase text-slate-400" />
                                <select name="event_name" id="event_name" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 shadow-inner">
                                    <option value="">{{ __('All Events') }}</option>
                                    @foreach($eventNames as $name)
                                        <option value="{{ $name }}" {{ $eventName == $name ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Salesman Filter --}}
                            <div class="space-y-2">
                                <x-input-label for="salesman_id" :value="__('Sales Representative')" class="font-bold text-[10px] uppercase text-slate-400" />
                                <select name="salesman_id" id="salesman_id" class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 shadow-inner">
                                    <option value="">{{ __('All Staff') }}</option>
                                    @foreach($salesmen as $id => $name)
                                        <option value="{{ $id }}" {{ $salesmanId == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Min Support Slider --}}
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <x-input-label for="support" :value="__('Min Support')" class="font-bold text-[10px] uppercase text-slate-400" />
                                    <span id="support-val" class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-[10px] font-black tabular-nums">{{ $minSupport }}</span>
                                </div>
                                <input type="range" name="support" id="support" min="0.01" max="0.5" step="0.01"
                                    value="{{ $minSupport }}"
                                    class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600"
                                    oninput="document.getElementById('support-val').innerText = parseFloat(this.value).toFixed(2)">
                                <p class="text-[10px] text-slate-400">% of transactions that must contain the pair</p>
                            </div>

                            {{-- Min Confidence Slider --}}
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <x-input-label for="confidence" :value="__('Min Confidence')" class="font-bold text-[10px] uppercase text-slate-400" />
                                    <span id="confidence-val" class="px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-md text-[10px] font-black tabular-nums">{{ $minConfidence }}</span>
                                </div>
                                <input type="range" name="confidence" id="confidence" min="0.1" max="1" step="0.05"
                                    value="{{ $minConfidence }}"
                                    class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-emerald-500"
                                    oninput="document.getElementById('confidence-val').innerText = parseFloat(this.value).toFixed(2)">
                                <p class="text-[10px] text-slate-400">Probability that B is bought when A is bought</p>
                            </div>

                            <input type="hidden" name="refresh" value="1">
                            <x-primary-button class="w-full h-12 rounded-2xl flex items-center justify-center gap-2 text-xs font-black shadow-lg shadow-indigo-100 uppercase tracking-widest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                {{ __('Run Analysis') }}
                            </x-primary-button>

                            {{-- Quick presets --}}
                            <div class="pt-2 border-t border-slate-100 space-y-2">
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Quick Presets</p>
                                <button type="button" onclick="setPreset(0.3, 0.7)"
                                    class="w-full text-left px-3 py-2 rounded-xl bg-slate-50 hover:bg-indigo-50 text-[10px] font-bold text-slate-600 hover:text-indigo-700 transition-colors">
                                    🔥 Strict (0.3 / 0.7) — Reliable bundles
                                </button>
                                <button type="button" onclick="setPreset(0.1, 0.5)"
                                    class="w-full text-left px-3 py-2 rounded-xl bg-slate-50 hover:bg-indigo-50 text-[10px] font-bold text-slate-600 hover:text-indigo-700 transition-colors">
                                    ⚡ Balanced (0.1 / 0.5) — Default
                                </button>
                                <button type="button" onclick="setPreset(0.03, 0.3)"
                                    class="w-full text-left px-3 py-2 rounded-xl bg-slate-50 hover:bg-indigo-50 text-[10px] font-bold text-slate-600 hover:text-indigo-700 transition-colors">
                                    🔍 Explore (0.03 / 0.3) — Discover niche
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ── MAIN CONTENT ──────────────────────────────────────────────── --}}
                <div class="lg:col-span-9 space-y-8">

                    {{-- ── Frequent 2-Itemsets Table ─────────────────────────────── --}}
                    @if($analysisRan)
                    <div class="premium-card bg-white overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Frequent 2-Item Pairings</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Products that appear together above the support threshold</p>
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-[10px] font-bold w-fit">
                                {{ count($frequentItemsets) }} Pairs Found
                            </span>
                        </div>

                        @if(count($frequentItemsets) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4">#</th>
                                        <th class="px-6 py-4">Item A</th>
                                        <th class="px-6 py-4">Item B</th>
                                        <th class="px-6 py-4 text-center">Co-Occurrences</th>
                                        <th class="px-6 py-4 text-right">Support</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @php $rank = 1; @endphp
                                    @foreach($frequentItemsets as $pairKey => $count)
                                        @php
                                            $pair    = explode(',', $pairKey);
                                            $support = $stats['total_sales'] > 0
                                                ? ($count / $stats['total_sales']) * 100
                                                : 0;
                                        @endphp
                                        <tr class="hover:bg-indigo-50/30 transition-colors group">
                                            <td class="px-6 py-4 text-xs font-black text-slate-400">{{ $rank++ }}</td>
                                            <td class="px-6 py-4">
                                                <span class="font-bold text-slate-700 text-sm">{{ $items[$pair[0]] ?? 'ID: '.$pair[0] }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="font-bold text-slate-700 text-sm">{{ $items[$pair[1]] ?? 'ID: '.$pair[1] }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                                    {{ $count }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex flex-col items-end gap-1">
                                                    <div class="w-24 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                        <div class="bg-indigo-600 h-1.5 rounded-full transition-all" style="width: {{ min($support, 100) }}%"></div>
                                                    </div>
                                                    <span class="text-[10px] font-black text-slate-500 tabular-nums">{{ number_format($support, 2) }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <div class="p-10 text-center text-slate-400 text-sm italic">
                                No pairs met the support threshold. Try lowering Min Support.
                            </div>
                        @endif
                    </div>

                    {{-- ── Frequent 3-Itemsets Table ─────────────────────────────── --}}
                    @if(count($frequent3Itemsets) > 0)
                    <div class="premium-card bg-white overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Frequent 3-Item Bundles</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Triplets that commonly appear in the same transaction</p>
                            </div>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-[10px] font-bold w-fit">
                                {{ count($frequent3Itemsets) }} Triplets Found
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="px-6 py-4">#</th>
                                        <th class="px-6 py-4">Item A</th>
                                        <th class="px-6 py-4">Item B</th>
                                        <th class="px-6 py-4">Item C</th>
                                        <th class="px-6 py-4 text-center">Co-Occurrences</th>
                                        <th class="px-6 py-4 text-right">Support</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @php $rank3 = 1; @endphp
                                    @foreach($frequent3Itemsets as $trioKey => $count)
                                        @php
                                            $trio    = explode(',', $trioKey);
                                            $support = $stats['total_sales'] > 0
                                                ? ($count / $stats['total_sales']) * 100
                                                : 0;
                                        @endphp
                                        <tr class="hover:bg-purple-50/30 transition-colors">
                                            <td class="px-6 py-4 text-xs font-black text-slate-400">{{ $rank3++ }}</td>
                                            <td class="px-6 py-4 font-bold text-slate-700 text-sm">{{ $items[$trio[0]] ?? 'ID: '.$trio[0] }}</td>
                                            <td class="px-6 py-4 font-bold text-slate-700 text-sm">{{ $items[$trio[1]] ?? 'ID: '.$trio[1] }}</td>
                                            <td class="px-6 py-4 font-bold text-slate-700 text-sm">{{ $items[$trio[2]] ?? 'ID: '.$trio[2] }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700">{{ $count }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex flex-col items-end gap-1">
                                                    <div class="w-24 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ min($support, 100) }}%"></div>
                                                    </div>
                                                    <span class="text-[10px] font-black text-slate-500 tabular-nums">{{ number_format($support, 2) }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    @endif {{-- end analysisRan --}}

                    {{-- ── Association Rules / Bundle Cards ─────────────────────── --}}
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">Recommended Event Bundles</h3>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">
                                    Association rules stored in database
                                    @if($eventName) — filtered by <span class="font-bold text-indigo-600">{{ $eventName }}</span> @endif
                                </p>
                            </div>
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold">
                                {{ $results->total() }} total rules
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @forelse($results as $rule)
                                @php
                                    $antIds   = $rule->antecedentIds();   // array of item_id(s)
                                    $isMulti  = $rule->isMultiAntecedent();
                                    $confPct  = round($rule->confidence * 100);
                                    $suppPct  = round(($rule->support ?? 0) * 100, 2);

                                    // Colour coding by confidence
                                    $confColor = $confPct >= 70 ? 'emerald' : ($confPct >= 50 ? 'indigo' : 'amber');
                                @endphp
                                <div class="premium-card p-6 bg-white border-l-4 border-{{ $confColor }}-500 relative group overflow-hidden">

                                    {{-- Top badge row --}}
                                    <div class="flex items-center justify-between mb-5">
                                        <div class="flex items-center gap-2">
                                            @if($isMulti)
                                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[9px] font-bold rounded-full uppercase tracking-wider">3-item bundle</span>
                                            @else
                                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[9px] font-bold rounded-full uppercase tracking-wider">2-item rule</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xl font-black text-{{ $confColor }}-600 tabular-nums">{{ $confPct }}%</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Confidence</p>
                                        </div>
                                    </div>

                                    {{-- Antecedent → Consequent --}}
                                    <div class="grid grid-cols-11 items-center gap-2 mb-5">

                                        {{-- Antecedent (may be 1 or 2 items) --}}
                                        <div class="col-span-5 p-3 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center justify-center min-h-[90px] group-hover:bg-white transition-colors">
                                            <p class="text-[8px] text-slate-400 font-bold uppercase mb-2 tracking-widest">
                                                {{ $isMulti ? 'When buying (A+B)' : 'Base Item' }}
                                            </p>
                                            @foreach($antIds as $antId)
                                                <p class="text-xs font-black text-slate-800 text-center leading-tight">
                                                    {{ $items[$antId] ?? 'Item #'.$antId }}
                                                </p>
                                                @if(!$loop->last)
                                                    <span class="text-[8px] text-slate-300 font-bold my-0.5">+ AND +</span>
                                                @endif
                                            @endforeach
                                        </div>

                                        {{-- Arrow --}}
                                        <div class="col-span-1 flex flex-col items-center justify-center">
                                            <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100 group-hover:scale-125 transition-transform">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </div>
                                        </div>

                                        {{-- Consequent --}}
                                        <div class="col-span-5 p-3 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col items-center justify-center min-h-[90px] group-hover:bg-white transition-colors">
                                            <p class="text-[8px] text-slate-400 font-bold uppercase mb-2 tracking-widest">Also buys</p>
                                            <p class="text-xs font-black text-emerald-600 text-center leading-tight">
                                                {{ $items[$rule->consequent] ?? 'Item #'.$rule->consequent }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Metrics row --}}
                                    <div class="grid grid-cols-3 gap-3 pt-4 border-t border-slate-50">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">Support</span>
                                            <span class="text-sm font-black text-slate-700 tabular-nums">{{ $suppPct }}%</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">Confidence</span>
                                            <span class="text-sm font-black text-{{ $confColor }}-600 tabular-nums">{{ $confPct }}%</span>
                                        </div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">Lift</span>
                                            <span class="text-sm font-black text-slate-700 tabular-nums">{{ number_format($rule->lift, 3) }}</span>
                                        </div>
                                    </div>

                                    {{-- Manager action --}}
                                    @if(Auth::guard('manager')->check())
                                        <div class="mt-4 pt-3 border-t border-slate-50">
                                            <a href="{{ route('promotions.create', ['rule_id' => $rule->rule_id]) }}"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold transition-all shadow-md shadow-indigo-100 hover:shadow-indigo-200 group/btn w-full justify-center">
                                                Create Promotion from Rule
                                                <svg class="w-3 h-3 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="md:col-span-2 premium-card p-16 text-center bg-slate-50/50 border-2 border-dashed border-slate-200">
                                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
                                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                    </div>
                                    <h4 class="text-xl font-bold text-slate-800 mb-2">No Rules Generated Yet</h4>
                                    <p class="text-sm text-slate-500 max-w-sm mx-auto mb-8">
                                        Click <b>Run Analysis</b> on the left to discover association rules from your sales data.
                                        Try lowering <b>Min Support</b> or <b>Min Confidence</b> if no results appear.
                                    </p>
                                    <button type="button" onclick="setPreset(0.03, 0.3); document.querySelector('form').submit();"
                                        class="text-indigo-600 font-bold text-xs hover:underline uppercase tracking-widest">
                                        Try Explore Preset (0.03 / 0.3)
                                    </button>
                                </div>
                            @endforelse
                        </div>

                        {{-- Pagination --}}
                        @if($results->hasPages())
                            <div class="mt-6">{{ $results->links() }}</div>
                        @endif
                    </div>

                </div>{{-- end main content --}}
            </div>
        </div>
    </div>

    <script>
        function setPreset(support, confidence) {
            const sInp = document.getElementById('support');
            const cInp = document.getElementById('confidence');
            sInp.value = support;
            cInp.value = confidence;
            document.getElementById('support-val').innerText    = support;
            document.getElementById('confidence-val').innerText = confidence;
        }
    </script>
</x-app-layout>
