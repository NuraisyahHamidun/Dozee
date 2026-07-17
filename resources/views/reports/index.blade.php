<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
            <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </span>
            {{ __('Comprehensive Reports') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{ tab: '{{ request()->get('tab', 'sales') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs Navigation -->
            <div class="flex space-x-1 bg-white dark:bg-slate-800 p-1.5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 mb-6 overflow-x-auto">
                <button @click="tab = 'sales'" :class="{'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold': tab === 'sales', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': tab !== 'sales'}" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap">
                    Monthly Sales
                </button>
                <button @click="tab = 'promotions'" :class="{'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold': tab === 'promotions', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': tab !== 'promotions'}" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap">
                    Promotions
                </button>
                <button @click="tab = 'apriori'" :class="{'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold': tab === 'apriori', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': tab !== 'apriori'}" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap">
                    Market Basket Analysis
                </button>
                <button @click="tab = 'salesman'" :class="{'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold': tab === 'salesman', 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300': tab !== 'salesman'}" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 whitespace-nowrap">
                    Salesman Reports
                </button>
            </div>

            <!-- Tab 1: Sales -->
            <div x-show="tab === 'sales'" class="space-y-6" x-transition.opacity>
                <div class="premium-card bg-white dark:bg-slate-800 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Sales Report</h3>
                            <p class="text-xs text-slate-400 mt-1">Detailed breakdown of transactions</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-2">
                                <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500">
                                <span class="text-slate-400 font-bold">to</span>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500">
                                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition-colors">Filter</button>
                            </form>
                            <a href="{{ route('reports.sales.export', ['format'=>'excel', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Excel
                            </a>
                            <a href="{{ route('reports.sales.export', ['format'=>'pdf', 'start_date'=>$startDate, 'end_date'=>$endDate]) }}" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-200 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Revenue</p>
                            <p class="text-xl font-black text-indigo-600">RM {{ number_format($totalSalesRevenue, 2) }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Transactions</p>
                            <p class="text-xl font-black text-slate-800 dark:text-white">{{ $totalSalesCount }}</p>
                        </div>
                    </div>

                    @if($sales->isEmpty())
                        <div class="py-8 text-center text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                            No sales found in this period.
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Txn ID</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Salesman</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Amount (RM)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($sales as $sale)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $sale->sale_date }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-800 dark:text-white">TXN-{{ str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">{{ $sale->salesman->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-indigo-600 text-right">{{ number_format($sale->total_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($sales->hasPages())
                            <div class="mt-6 px-4 py-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                                {{ $sales->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Tab 2: Promotions -->
            <div x-show="tab === 'promotions'" style="display: none;" class="space-y-6" x-transition.opacity>
                <div class="premium-card bg-white dark:bg-slate-800 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Promotions Report</h3>
                            <p class="text-xs text-slate-400 mt-1">Performance of active & past deals</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-2">
                                <input type="hidden" name="tab" value="promotions">
                                <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500">
                                <span class="text-slate-400 font-bold">to</span>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500">
                                <select name="promo_status" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 focus:ring-indigo-500">
                                    <option value="All" {{ $promoStatus === 'All' ? 'selected' : '' }}>All Status</option>
                                    <option value="Active" {{ $promoStatus === 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Pending" {{ $promoStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Rejected" {{ $promoStatus === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="Expired" {{ $promoStatus === 'Expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition-colors">Filter</button>
                            </form>
                            <a href="{{ route('reports.promotions.export', ['format'=>'excel', 'start_date'=>$startDate, 'end_date'=>$endDate, 'promo_status'=>$promoStatus]) }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Excel
                            </a>
                            <a href="{{ route('reports.promotions.export', ['format'=>'pdf', 'start_date'=>$startDate, 'end_date'=>$endDate, 'promo_status'=>$promoStatus]) }}" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-200 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF
                            </a>
                        </div>
                    </div>

                    @if($promotions->isEmpty())
                        <div class="py-8 text-center text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                            No promotions match the criteria.
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Promo</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Period</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Discount</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Sales (Qty)</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($promotions as $promo)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $promo->promo_name }}</div>
                                            <div class="text-[10px] text-slate-400 uppercase">PRM-{{ str_pad($promo->promo_id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                            {{ \Carbon\Carbon::parse($promo->start_date)->format('d M y') }} - {{ \Carbon\Carbon::parse($promo->end_date)->format('d M y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest rounded-full 
                                                @if($promo->status == 'Active') bg-emerald-100 text-emerald-700 
                                                @elseif($promo->status == 'Pending') bg-orange-100 text-orange-700
                                                @else bg-slate-100 text-slate-700 @endif">
                                                {{ $promo->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-slate-600 dark:text-slate-300">
                                            RM {{ number_format($promo->discount_amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-slate-800 dark:text-white text-right">
                                            {{ $promoRevenue[$promo->promo_id]['sales_count'] ?? 0 }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-indigo-600 text-right">
                                            RM {{ number_format($promoRevenue[$promo->promo_id]['revenue'] ?? 0, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($promotions->hasPages())
                            <div class="mt-6 px-8 py-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                                {{ $promotions->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Tab 3: Apriori -->
            <div x-show="tab === 'apriori'" style="display: none;" class="space-y-6" x-transition.opacity>
                <div class="premium-card bg-white dark:bg-slate-800 p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Market Basket Analysis Report</h3>
                            <p class="text-xs text-slate-400 mt-1">Customer purchase patterns and product bundling insights</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('reports.apriori.export', ['format'=>'excel']) }}" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-200 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Excel
                            </a>
                            <a href="{{ route('reports.apriori.export', ['format'=>'pdf']) }}" class="px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-200 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> PDF
                            </a>
                        </div>
                    </div>

                    @if($aprioriRules->isEmpty())
                        <div class="py-8 text-center text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                            No market basket rules found. Please generate analysis first.
                        </div>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-700">
                            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Combination</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Support</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Confidence</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Lift</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Recommendation</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach($aprioriRules as $rule)
                                    @php
                                        $antecedentNames = '';
                                        if ($rule->isMultiAntecedent()) {
                                            $ids = $rule->antecedentIds();
                                            $names = \App\Models\Product::whereIn('item_id', $ids)->get()->map(function($p) {
                                                return $p->item_code ? $p->item_code . ' (' . $p->item_name . ')' : $p->item_name;
                                            })->toArray();
                                            $antecedentNames = implode(' + ', $names);
                                        } else {
                                            $p = \App\Models\Product::find($rule->antecedent);
                                            $antecedentNames = $p ? ($p->item_code ? $p->item_code . ' (' . $p->item_name . ')' : $p->item_name) : $rule->antecedent;
                                        }
                                        $p2 = \App\Models\Product::find($rule->consequent);
                                        $consequentName = $p2 ? ($p2->item_code ? $p2->item_code . ' (' . $p2->item_name . ')' : $p2->item_name) : $rule->consequent;

                                        // Determine recommendation
                                        if ($rule->confidence >= 0.8) {
                                            $recommendation = 'Strong Bundle Potential';
                                            $badgeClass = 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400';
                                        } elseif ($rule->lift >= 1.5) {
                                            $recommendation = 'Suitable for Combo Promotion';
                                            $badgeClass = 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400';
                                        } else {
                                            $recommendation = 'High Customer Association';
                                            $badgeClass = 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400';
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                                <span>{{ $antecedentNames }}</span>
                                                <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                                <span>{{ $consequentName }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 text-right">
                                            {{ number_format($rule->support * 100, 2) }}%
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-emerald-600 font-bold text-right">
                                            {{ number_format($rule->confidence * 100, 1) }}%
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-indigo-600 font-bold text-right">
                                            {{ number_format($rule->lift, 2) }}x
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider {{ $badgeClass }}">
                                                {{ $recommendation }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($aprioriRules->hasPages())
                            <div class="mt-6 px-8 py-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                                {{ $aprioriRules->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Tab 4: Salesman Reports -->
            <div x-show="tab === 'salesman'" style="display: none;" class="space-y-6" x-transition.opacity>
                <div class="premium-card bg-white dark:bg-slate-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Salesmen Financial Reports</h3>
                        <p class="text-xs text-slate-400 mt-1">Select a salesman to view their detailed performance report or export directly</p>
                    </div>

                    @if($salesmen->isEmpty())
                        <div class="py-12 text-center text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-800/30 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                            No salesmen accounts found.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($salesmen as $sm)
                                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-2xl border border-slate-100 dark:border-slate-700/60 p-5 hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center gap-3 mb-3">
                                            <div class="p-2.5 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl text-indigo-600 dark:text-indigo-400">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="font-bold text-slate-800 dark:text-white">{{ $sm->name }}</h4>
                                                    @if(isset($pendingCounts[$sm->salesman_id]) && $pendingCounts[$sm->salesman_id] > 0)
                                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">
                                                            {{ $pendingCounts[$sm->salesman_id] }} Pending
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-slate-400">@ {{ $sm->username }}</p>
                                            </div>
                                        </div>
                                        <div class="space-y-1 text-xs text-slate-500 dark:text-slate-400 mb-6">
                                            <p class="flex items-center gap-1.5"><span class="font-semibold text-slate-600 dark:text-slate-300">Email:</span> {{ $sm->email }}</p>
                                            <p class="flex items-center gap-1.5"><span class="font-semibold text-slate-600 dark:text-slate-300">Address:</span> {{ $sm->address ?? 'No address listed' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700/60">
                                        <div class="flex gap-2">
                                            <a href="{{ route('reports.salesman', ['id' => $sm->salesman_id]) }}" class="flex-1 text-center py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                View
                                            </a>
                                            <a href="{{ route('reports.salesman.export', ['id' => $sm->salesman_id, 'format' => 'excel']) }}" class="px-2.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl transition-colors flex items-center justify-center gap-1 text-xs font-bold" title="Download Excel">
                                                📄 Excel
                                            </a>
                                            <a href="{{ route('reports.salesman.export', ['id' => $sm->salesman_id, 'format' => 'pdf']) }}" class="px-2.5 py-2 bg-rose-500 hover:bg-rose-600 text-white rounded-xl transition-colors flex items-center justify-center gap-1 text-xs font-bold" title="Download PDF">
                                                📄 PDF
                                            </a>
                                        </div>
                                        @if(Auth::guard('manager')->check())
                                            @if(isset($pendingCounts[$sm->salesman_id]) && $pendingCounts[$sm->salesman_id] > 0)
                                                <a href="{{ route('reports.salesman', ['id' => $sm->salesman_id]) }}"
                                                   class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow-md shadow-amber-100 dark:shadow-none">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    Approve Sales ({{ $pendingCounts[$sm->salesman_id] }} Pending)
                                                </a>
                                            @else
                                                <button disabled
                                                    class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 text-xs font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    No Pending Sales
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <script>
        // Set initial tab from query string if present
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                document.querySelector('[x-data]').__x.$data.tab = tabParam;
            }
        });
    </script>
</x-app-layout>
