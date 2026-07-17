<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
            <span class="p-2 bg-purple-600 rounded-2xl text-white shadow-lg shadow-purple-100 dark:shadow-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </span>
            {{ __('Sales Report System') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Filters Card -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white heading-font">Personal Performance</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Filter and analyze your sales transactions</p>
                    </div>
                    
                    <form action="{{ route('reports.salesmen_personal.index') }}" method="GET" class="w-full md:w-auto flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Month Selector</label>
                            <input type="month" name="month" value="{{ $monthInput }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">From</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">To</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Item Filter</label>
                            <select name="item_id" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-purple-500 focus:border-purple-500">
                                <option value="">All Items</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->item_id }}" {{ $itemIdInput == $product->item_id ? 'selected' : '' }}>
                                        [{{ $product->item_code }}] {{ $product->item_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                                Filter
                            </button>
                            <a href="{{ route('reports.salesmen_personal.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-bold rounded-xl transition-all">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Section & Export Section -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- KPI Summary Cards -->
                <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- My Total Sales -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 truncate">My Total Sales</p>
                            <h3 class="text-2xl font-black text-purple-600 truncate">RM {{ number_format($myTotalSales, 2) }}</h3>
                        </div>
                        <div class="p-3 bg-purple-50 dark:bg-purple-500/10 rounded-2xl text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- My Total Transactions -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 truncate">My Transactions</p>
                            <h3 class="text-2xl font-black text-slate-800 dark:text-white truncate">{{ $myTotalTransactions }}</h3>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-700/50 rounded-2xl text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- My Items Sold -->
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1 truncate">My Items Sold</p>
                            <h3 class="text-2xl font-black text-emerald-600 truncate">{{ $myItemsSold }} units</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl text-emerald-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Export Cards Panel -->
                <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm flex flex-col justify-center gap-3">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center">Export Actions</p>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('reports.salesmen_personal.export', ['format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate, 'month' => $monthInput, 'item_id' => $itemIdInput]) }}" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-rose-200 dark:shadow-none flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg> PDF
                        </a>
                        <a href="{{ route('reports.salesmen_personal.export', ['format' => 'excel', 'start_date' => $startDate, 'end_date' => $endDate, 'month' => $monthInput, 'item_id' => $itemIdInput]) }}" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-purple-200 dark:shadow-none flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg> Excel
                        </a>
                    </div>
                </div>
            </div>



            <!-- Detailed Report Table -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="mb-6">
                    <h3 class="text-md font-bold text-slate-800 dark:text-white heading-font">Sales Breakdown</h3>
                    <p class="text-xs text-slate-400">Matched transactions list</p>
                </div>

                @if($saleItems->isEmpty())
                    <div class="py-12 text-center text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                        No transactions recorded for the selected period.
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                            <thead class="bg-purple-50/50 dark:bg-purple-500/5">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Transaction ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Item Code</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Price (RM)</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Total Price (RM)</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($saleItems as $item)
                                    @php
                                        $unitPrice = $item->product->price ?? 0;
                                        $totalPrice = $unitPrice * $item->quantity;
                                    @endphp
                                    <tr class="hover:bg-purple-50/30 dark:hover:bg-purple-500/5 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800 dark:text-white">
                                            TXN-{{ str_pad($item->transaction_id, 6, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                            {{ $item->product->item_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 font-mono">
                                            {{ $item->product->item_code ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-slate-600 dark:text-slate-300">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-600 dark:text-slate-300">
                                            {{ number_format($unitPrice, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-purple-600 text-right">
                                            {{ number_format($totalPrice, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                            {{ $item->sale->sale_date ? $item->sale->sale_date->format('Y-m-d H:i') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($saleItems->hasPages())
                        <div class="mt-6">
                            {{ $saleItems->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

</x-app-layout>
