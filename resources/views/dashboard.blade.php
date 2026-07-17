<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        /* Custom Swiper pagination bullets */
        .mySwiper .swiper-pagination-bullet {
            width: 8px; height: 8px;
            background: #cbd5e1;
            opacity: 1;
            transition: all 0.3s;
        }
        .mySwiper .swiper-pagination-bullet-active {
            background: #4f46e5;
            width: 24px;
            border-radius: 4px;
        }
        /* Filter input label style */
        .filter-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 4px;
            display: block;
        }
        /* Slide counter — hidden per design spec */
        #slideCounter {
            display: none !important;
        }
        /* Carousel wrapper — flush with filter bar */
        .carousel-wrapper {
            width: 100%;
        }
        /* Subtle background styling for chart wrappers */
        .chart-slide-wrapper {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #f1f5f9;
            border-radius: 1.5rem; /* 24px */
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
            padding: 1.25rem;
            position: relative;
        }
        /* Dark mode variant */
        .dark .chart-slide-wrapper {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.35) 0%, rgba(15, 23, 42, 0.15) 100%);
            border-color: rgba(51, 65, 85, 0.35);
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.15);
        }
        /* Glassmorphic pastel gradient wrapper for Monthly Sales */
        .monthly-chart-wrapper {
            background: linear-gradient(135deg, rgba(209, 250, 229, 0.25) 0%, rgba(243, 244, 246, 0.5) 100%);
            border: 1px solid rgba(16, 185, 129, 0.18);
            border-radius: 1.75rem; /* 28px */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px -5px rgba(16, 185, 129, 0.05), inset 0 2px 4px 0 rgba(255, 255, 255, 0.4);
            padding: 1.5rem;
            position: relative;
        }
        /* Dark mode variant for Monthly Sales wrapper */
        .dark .monthly-chart-wrapper {
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.15) 0%, rgba(15, 23, 42, 0.35) 100%);
            border-color: rgba(16, 185, 129, 0.25);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3), inset 0 1px 2px 0 rgba(255, 255, 255, 0.05);
        }
        /* Glassmorphic pastel gradient wrapper for Top Selling Items */
        .top-items-chart-wrapper {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(243, 244, 246, 0.45) 100%);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 1.75rem; /* 28px */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 10px 30px -5px rgba(99, 102, 241, 0.05), inset 0 2px 4px 0 rgba(255, 255, 255, 0.4);
            padding: 1.5rem;
            position: relative;
        }
        /* Dark mode variant for Top Selling Items wrapper */
        .dark .top-items-chart-wrapper {
            background: linear-gradient(135deg, rgba(67, 56, 202, 0.15) 0%, rgba(15, 23, 42, 0.35) 100%);
            border-color: rgba(99, 102, 241, 0.28);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.3), inset 0 1px 2px 0 rgba(255, 255, 255, 0.05);
        }
    </style>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
            <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            </span>
            {{ __('Overview') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="mb-10 animate-fade-in">
                <div class="flex items-center gap-4">
                    @if(Auth::guard('manager')->check() && Auth::guard('manager')->user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::guard('manager')->user()->profile_picture) }}" class="w-16 h-16 rounded-3xl object-cover border border-slate-200 dark:border-slate-700 shadow-xl" alt="Avatar">
                    @elseif(Auth::guard('salesmen')->check() && Auth::guard('salesmen')->user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::guard('salesmen')->user()->profile_picture) }}" class="w-16 h-16 rounded-3xl object-cover border border-slate-200 dark:border-slate-700 shadow-xl" alt="Avatar">
                    @else
                        <div class="w-16 h-16 rounded-3xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100 dark:shadow-none">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-3xl font-black text-slate-800 dark:text-white leading-tight">
                            {{ __('Welcome back') }}, 
                            <span class="text-indigo-600">
                                @if(Auth::guard('manager')->check())
                                    {{ Auth::guard('manager')->user()->name }}
                                @else
                                    {{ Auth::guard('salesmen')->user()->name }}
                                @endif
                            </span>!
                        </h1>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">
                            {{ Auth::guard('manager')->check() ? __('Managerial Control Suite') : __('Sales Strategic Dashboard') }}
                        </p>
                    </div>
                </div>
            </di            <!-- Stats Overview -->
            @if(Auth::guard('manager')->check())
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Money Made</p>
                                <h3 class="text-xl md:text-2xl font-black text-indigo-600 truncate">RM {{ number_format($totalRevenue, 2) }}</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl md:rounded-2xl text-indigo-500 hidden sm:block">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-emerald-500">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            <span class="truncate">Doing great!</span>
                        </div>
                    </div>
                    
                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Total Sales</p>
                                <h3 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white truncate">{{ $salesCount }}</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl md:rounded-2xl text-emerald-500 hidden sm:block">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-slate-400">
                            <span class="truncate">All time total</span>
                        </div>
                    </div>

                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Available Items</p>
                                <h3 class="text-xl md:text-2xl font-black text-slate-800 dark:text-white truncate">{{ $productCount }}</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-blue-50 dark:bg-blue-500/10 rounded-xl md:rounded-2xl text-blue-500 hidden sm:block">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-indigo-500">
                            <a href="{{ route('products.index') }}" class="hover:underline truncate">View Stock &rarr;</a>
                        </div>
                    </div>

                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Active Deals</p>
                                <h3 class="text-xl md:text-2xl font-black text-orange-600 truncate">{{ $activePromotions }}</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-orange-50 dark:bg-orange-500/10 rounded-xl md:rounded-2xl text-orange-500 hidden sm:block">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-orange-500">
                            <span class="truncate">Current offers</span>
                        </div>
                    </div>
                </div>
            @else
                <!-- Stats Overview (Staff specific) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8 animate-fade-in">
                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Total Sales</p>
                                <h3 class="text-xl md:text-2xl font-black text-indigo-600 truncate">{{ $salesCount }}</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl md:rounded-2xl text-indigo-500 hidden sm:block">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-slate-400">
                            <span class="truncate">Your transactions count</span>
                        </div>
                    </div>
                    
                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Items Sold</p>
                                <h3 class="text-xl md:text-2xl font-black text-emerald-600 truncate">{{ $itemsSold }} units</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl md:rounded-2xl text-emerald-500 hidden sm:block">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-slate-400">
                            <span class="truncate">Total quantities sold</span>
                        </div>
                    </div>

                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Active Deals</p>
                                <h3 class="text-xl md:text-2xl font-black text-blue-600 truncate">{{ $activeDeals }} active</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-blue-50 dark:bg-blue-500/10 rounded-xl md:rounded-2xl text-blue-500 hidden sm:block">
                                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-slate-400">
                            <span class="truncate">Current active promos</span>
                        </div>
                    </div>

                    <div class="premium-card bg-white dark:bg-slate-800 p-5 md:p-6 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 truncate">Performance Score</p>
                                <h3 class="text-xl md:text-2xl font-black text-purple-600 truncate">{{ $performanceScore }}%</h3>
                            </div>
                            <div class="p-2 md:p-3 bg-purple-50 dark:bg-purple-500/10 rounded-xl md:rounded-2xl text-purple-500 hidden sm:block">
                                <span class="text-xs font-black text-purple-600">
                                    @if($performanceScore >= 90) A+
                                    @elseif($performanceScore >= 80) A
                                    @elseif($performanceScore >= 70) B
                                    @elseif($performanceScore >= 50) C
                                    @else Pass
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-[9px] font-bold text-slate-400">
                            <span class="truncate">
                                @if($performanceScore >= 90) Outstanding job!
                                @elseif($performanceScore >= 70) On track to target
                                @elseif($performanceScore >= 50) Standard performance
                                @else Keep pushing!
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filters -->
            <div class="mb-6 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-5">
                    <!-- Label + live badge -->
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Charts</p>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 leading-none">Dashboard Filters</p>
                        </div>
                        <!-- Live update badge -->
                        <span id="liveIndicator" class="hidden items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-[9px] font-black uppercase tracking-widest text-indigo-500">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse inline-block"></span>
                            Updating…
                        </span>
                    </div>

                    <!-- Divider (desktop only) -->
                    <div class="hidden md:block w-px h-8 bg-slate-100 dark:bg-slate-700"></div>

                    <!-- Filter controls -->
                    <div class="flex flex-wrap items-end gap-4">
                        <!-- From Date -->
                        <div class="flex flex-col">
                            <label class="filter-label dark:text-slate-500" for="filterStartDate">From</label>
                            <input type="date" id="filterStartDate"
                                value="{{ \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}"
                                class="text-sm font-semibold rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 transition-all">
                        </div>

                        <!-- Arrow separator -->
                        <div class="pb-2 text-slate-300 dark:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </div>

                        <!-- To Date -->
                        <div class="flex flex-col">
                            <label class="filter-label dark:text-slate-500" for="filterEndDate">To</label>
                            <input type="date" id="filterEndDate"
                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                class="text-sm font-semibold rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 transition-all">
                        </div>

                        <!-- Divider -->
                        <div class="pb-2 w-px h-8 bg-slate-100 dark:bg-slate-700 hidden sm:block"></div>

                        <!-- View type -->
                        <div class="flex flex-col">
                            <label class="filter-label dark:text-slate-500" for="filterSortBy">View Type</label>
                            <select id="filterSortBy"
                                class="text-sm font-semibold rounded-xl border border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2 transition-all">
                                <option value="quantity">Top Items by Qty</option>
                                <option value="revenue">Top Items by Revenue</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::guard('manager')->check())
                <!-- Swiper Slider Container — Manager View -->
                <div class="carousel-wrapper relative mb-8 animate-fade-in">

                    <!-- Loading Overlay -->
                    <div id="chartLoadingOverlay" class="hidden absolute inset-0 z-20 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-3xl flex items-center justify-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-8 h-8 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span class="text-xs font-bold text-indigo-500 uppercase tracking-widest">Loading…</span>
                        </div>
                    </div>

                    <!-- Swiper Element -->
                    <div class="swiper mySwiper bg-white dark:bg-slate-800 shadow-xl shadow-slate-200/60 dark:shadow-none border border-slate-100 dark:border-slate-700/50 rounded-3xl">
                        <div class="swiper-wrapper">
                            <!-- Slide 1: Daily Sales Trend -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-500 dark:bg-indigo-500/10 mb-2">Line Chart</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Daily Sales Trend</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('daily', 'daily_sales.png')" class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="chart-slide-wrapper h-80 md:h-96"><canvas id="dailyChart"></canvas></div>
                                </div>
                            </div>

                            <!-- Slide 2: Monthly Sales -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 mb-2">Bar Chart</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Monthly Sales</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('monthly', 'monthly_sales.png')" class="p-2 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <!-- KPI Cards for Monthly Sales -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md p-4 rounded-2xl border border-emerald-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300">
                                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Total Sales</p>
                                            <h4 class="text-sm font-black text-emerald-600 dark:text-emerald-400" id="kpiMonthlyTotal">RM 0.00</h4>
                                        </div>
                                        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md p-4 rounded-2xl border border-emerald-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300">
                                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Average / Month</p>
                                            <h4 class="text-sm font-black text-slate-800 dark:text-white" id="kpiMonthlyAverage">RM 0.00</h4>
                                        </div>
                                        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md p-4 rounded-2xl border border-emerald-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300">
                                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Highest Month</p>
                                            <h4 class="text-sm font-black text-indigo-600 dark:text-indigo-400 truncate" id="kpiMonthlyHighest">N/A</h4>
                                        </div>
                                        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md p-4 rounded-2xl border border-emerald-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300">
                                            <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Lowest Month</p>
                                            <h4 class="text-sm font-black text-rose-600 dark:text-rose-400 truncate" id="kpiMonthlyLowest">N/A</h4>
                                        </div>
                                    </div>

                                    <div class="monthly-chart-wrapper h-80 md:h-96"><canvas id="monthlyChart"></canvas></div>
                                </div>
                            </div>

                            <!-- Slide 3: Promotion Performance -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 dark:bg-amber-500/10 mb-2">Horizontal Bar</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Promotion Performance</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('promo', 'promo_performance.png')" class="p-2 text-slate-400 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="chart-slide-wrapper h-80 md:h-96"><canvas id="promoChart"></canvas></div>
                                </div>
                            </div>

                            <!-- Slide 4: Single vs Combo Sales -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-violet-50 text-violet-600 dark:bg-violet-500/10 mb-2">Doughnut</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Single vs Combo Sales</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('combo', 'single_vs_combo.png')" class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="chart-slide-wrapper h-80 md:h-96 flex justify-center"><canvas id="comboChart"></canvas></div>
                                </div>
                            </div>

                            <!-- Slide 5: Top Selling Items -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 dark:bg-blue-500/10 mb-2">Bar Chart</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Top Selling Items</h3>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-400 font-medium" id="topItemsSubtext">By quantity</span>
                                            <button type="button" onclick="downloadChart('topItems', 'top_items.png')" class="p-2 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-xl transition-all" title="Download Chart">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- KPI Cards for Top Selling Items -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                        <div class="bg-white/85 dark:bg-slate-800/85 backdrop-blur-md p-4 rounded-2xl border border-indigo-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300 flex items-center gap-3">
                                            <div class="p-2 bg-blue-50 dark:bg-blue-500/10 rounded-xl text-blue-500 flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5">Total Sold</p>
                                                <h4 class="text-xs md:text-sm font-black text-blue-600 dark:text-blue-400 truncate" id="kpiTopItemsTotal">0</h4>
                                            </div>
                                        </div>
                                        <div class="bg-white/85 dark:bg-slate-800/85 backdrop-blur-md p-4 rounded-2xl border border-indigo-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300 flex items-center gap-3">
                                            <div class="p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl text-indigo-500 flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5" id="kpiTopItemsAverageLabel">Avg / Item</p>
                                                <h4 class="text-xs md:text-sm font-black text-slate-800 dark:text-white truncate" id="kpiTopItemsAverage">0</h4>
                                            </div>
                                        </div>
                                        <div class="bg-white/85 dark:bg-slate-800/85 backdrop-blur-md p-4 rounded-2xl border border-indigo-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300 flex items-center gap-3">
                                            <div class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-xl text-amber-500 flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.969 0 1.371 1.24.588 1.81l-3.97 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.888a1 1 0 00-1.175 0l-3.97 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.97-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5">Top Item</p>
                                                <h4 class="text-xs md:text-sm font-black text-amber-600 dark:text-amber-400 truncate" id="kpiTopItemsTop">N/A</h4>
                                            </div>
                                        </div>
                                        <div class="bg-white/85 dark:bg-slate-800/85 backdrop-blur-md p-4 rounded-2xl border border-indigo-500/10 shadow-sm transition-all hover:scale-[1.02] duration-300 flex items-center gap-3">
                                            <div class="p-2 bg-teal-50 dark:bg-teal-500/10 rounded-xl text-teal-500 flex-shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-0.5">Unique Items</p>
                                                <h4 class="text-xs md:text-sm font-black text-teal-600 dark:text-teal-400 truncate" id="kpiTopItemsUnique">0</h4>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="top-items-chart-wrapper h-80 md:h-96"><canvas id="topItemsChart"></canvas></div>
                                </div>
                            </div>


                        </div>

                        <!-- Navigation Controls -->
                        <div class="swiper-button-next !text-indigo-600 !w-10 !h-10 !bg-white dark:!bg-slate-700 !rounded-full !shadow-lg !border !border-slate-100 dark:!border-slate-600 hover:!bg-indigo-600 hover:!text-white transition-all after:!text-sm"></div>
                        <div class="swiper-button-prev !text-indigo-600 !w-10 !h-10 !bg-white dark:!bg-slate-700 !rounded-full !shadow-lg !border !border-slate-100 dark:!border-slate-600 hover:!bg-indigo-600 hover:!text-white transition-all after:!text-sm"></div>

                        <!-- Pagination dots only -->
                        <div class="swiper-pagination !bottom-3"></div>
                    </div>
                </div>
            @else
                <!-- Swiper Slider Container — Staff View -->
                <div class="carousel-wrapper relative mb-8 animate-fade-in">
                    <!-- Loading Overlay -->
                    <div id="chartLoadingOverlay" class="hidden absolute inset-0 z-20 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-3xl flex items-center justify-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-8 h-8 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span class="text-xs font-bold text-indigo-500 uppercase tracking-widest">Loading…</span>
                        </div>
                    </div>

                    <!-- Swiper Element -->
                    <div class="swiper staffChartsSwiper bg-white dark:bg-slate-800 shadow-xl shadow-indigo-100/40 dark:shadow-none border border-slate-100 dark:border-slate-700/50 rounded-3xl">
                        <div class="swiper-wrapper">
                            <!-- Slide 1: Sales Performance -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-purple-50 text-purple-600 dark:bg-purple-500/10 mb-2">Bar Chart</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Sales Performance</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('staffSalesPerformance', 'sales_performance.png')" class="p-2 text-slate-400 hover:text-purple-600 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="chart-slide-wrapper h-80 md:h-96 relative">
                                        <div id="staffSalesPerformanceEmpty" class="hidden absolute inset-0 z-10 bg-white/95 dark:bg-slate-800/95 flex flex-col items-center justify-center p-4 rounded-2xl">
                                            <div class="text-center">
                                                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">No data available yet</p>
                                            </div>
                                        </div>
                                        <canvas id="staffSalesPerformanceChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 2: Top Selling Items -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 mb-2">Bar Chart</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Top Selling Items</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('staffTopItems', 'top_selling_items.png')" class="p-2 text-slate-400 hover:text-indigo-600 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="chart-slide-wrapper h-80 md:h-96 relative">
                                        <div id="staffTopItemsEmpty" class="hidden absolute inset-0 z-10 bg-white/95 dark:bg-slate-800/95 flex flex-col items-center justify-center p-4 rounded-2xl">
                                            <div class="text-center">
                                                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">No data available yet</p>
                                            </div>
                                        </div>
                                        <canvas id="staffTopItemsChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 3: Transaction Trend -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 dark:bg-blue-500/10 mb-2">Line Chart</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Transaction Trend</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('staffTrend', 'transaction_trend.png')" class="p-2 text-slate-400 hover:text-blue-600 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="chart-slide-wrapper h-80 md:h-96 relative">
                                        <div id="staffTrendEmpty" class="hidden absolute inset-0 z-10 bg-white/95 dark:bg-slate-800/95 flex flex-col items-center justify-center p-4 rounded-2xl">
                                            <div class="text-center">
                                                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">No data available yet</p>
                                            </div>
                                        </div>
                                        <canvas id="staffTrendChart"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Slide 4: Category Distribution -->
                            <div class="swiper-slide">
                                <div class="px-8 pt-7 pb-10">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-pink-50 text-pink-600 dark:bg-pink-500/10 mb-2">Pie Chart</span>
                                            <h3 class="text-xl font-bold text-slate-800 dark:text-white heading-font">Category Distribution</h3>
                                        </div>
                                        <button type="button" onclick="downloadChart('staffCategory', 'category_distribution.png')" class="p-2 text-slate-400 hover:text-pink-600 rounded-xl transition-all" title="Download Chart">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </button>
                                    </div>
                                    <div class="chart-slide-wrapper h-80 md:h-96 flex justify-center relative">
                                        <div id="staffCategoryEmpty" class="hidden absolute inset-0 z-10 bg-white/95 dark:bg-slate-800/95 flex flex-col items-center justify-center p-4 rounded-2xl">
                                            <div class="text-center">
                                                <svg class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">No data available yet</p>
                                            </div>
                                        </div>
                                        <canvas id="staffCategoryChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Controls -->
                        <div class="swiper-button-next staff-swiper-button-next !text-purple-600 !w-10 !h-10 !bg-white dark:!bg-slate-700 !rounded-full !shadow-lg !border !border-slate-100 dark:!border-slate-600 hover:!bg-purple-600 hover:!text-white transition-all after:!text-sm"></div>
                        <div class="swiper-button-prev staff-swiper-button-prev !text-purple-600 !w-10 !h-10 !bg-white dark:!bg-slate-700 !rounded-full !shadow-lg !border !border-slate-100 dark:!border-slate-600 hover:!bg-purple-600 hover:!text-white transition-all after:!text-sm"></div>

                        <!-- Pagination dots -->
                        <div class="swiper-pagination staff-swiper-pagination !bottom-3"></div>
                    </div>
                </div>
            @endif         </div>

            @if(Auth::guard('manager')->check())
                <div class="premium-card p-8 bg-white dark:bg-slate-800 border border-slate-50 dark:border-slate-700/50 mt-8 mb-8">
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-6 heading-font">Our Top Salesmen</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @forelse($salesmenPerformance as $perf)
                            <div class="relative">
                                <div class="flex justify-between items-end mb-2">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Active Partner</span>
                                        <span class="text-sm font-black text-slate-800 dark:text-white uppercase leading-tight">{{ $perf->name }}</span>
                                    </div>
                                    <span class="text-xs font-black text-slate-800 dark:text-white">RM {{ number_format($perf->total, 2) }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(99,102,241,0.5)]" 
                                         style="width: {{ ($totalRevenue > 0) ? ($perf->total / $totalRevenue * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-xs italic font-medium">No performance data available yet.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Swiper.js -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartInstances = {};
            if (typeof ChartDataLabels !== 'undefined') {
                Chart.defaults.plugins.datalabels = { display: false };
            }
            const defaultFont = { family: 'Inter', size: 11, weight: '600' };
            const defaultTooltip = {
                backgroundColor: '#1e293b',
                titleFont: { family: 'Outfit', size: 14 },
                bodyFont: { family: 'Inter', size: 13 },
                padding: 12, cornerRadius: 12, displayColors: true
            };

            const isManager = {{ Auth::guard('manager')->check() ? 'true' : 'false' }};

            if (isManager) {
                // --- MANAGER CHART INITIALIZATION ---
                const promoColors = [
                    'rgba(79, 70, 229, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)', 
                    'rgba(239, 68, 68, 0.8)', 'rgba(139, 92, 246, 0.8)', 'rgba(6, 182, 212, 0.8)', 
                    'rgba(236, 72, 153, 0.8)', 'rgba(20, 184, 166, 0.8)', 'rgba(249, 115, 22, 0.8)', 
                    'rgba(99, 102, 241, 0.8)',
                ];
                const promoBorderColors = [
                    '#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#14b8a6', '#f97316', '#6366f1'
                ];

                const ctxDaily = document.getElementById('dailyChart');
                if (ctxDaily) {
                    const gradDaily = ctxDaily.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradDaily.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
                    gradDaily.addColorStop(1, 'rgba(79, 70, 229, 0)');
                    chartInstances.daily = new Chart(ctxDaily, {
                        type: 'line',
                        data: { labels: [], datasets: [{ label: 'Revenue (RM)', data: [], borderColor: '#4f46e5', backgroundColor: gradDaily, borderWidth: 4, fill: true, tension: 0.4 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: defaultTooltip }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { font: defaultFont } }, x: { grid: { display: false }, ticks: { font: defaultFont } } } }
                    });
                }

                const ctxMonthly = document.getElementById('monthlyChart');
                if (ctxMonthly) {
                    const gradMonthly = ctxMonthly.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradMonthly.addColorStop(0, 'rgba(52, 211, 153, 0.85)');
                    gradMonthly.addColorStop(1, 'rgba(16, 185, 129, 0.25)');
                    chartInstances.monthly = new Chart(ctxMonthly, {
                        type: 'bar',
                        plugins: [ChartDataLabels],
                        data: { labels: [], datasets: [{ label: 'Monthly Revenue (RM)', data: [], backgroundColor: gradMonthly, borderColor: '#10b981', borderWidth: 2, borderRadius: 8 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: defaultTooltip, datalabels: { display: true, anchor: 'end', align: 'top', offset: 4, formatter: v => parseFloat(v) > 0 ? 'RM ' + parseFloat(v).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '', font: { family: 'Inter', weight: 'bold', size: 9 }, color: c => c.dataset.borderColor ? c.dataset.borderColor[c.dataIndex] : '#10b981' } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { font: defaultFont }, grace: '12%' }, x: { grid: { display: false }, ticks: { font: defaultFont } } } }
                    });
                }

                const ctxPromo = document.getElementById('promoChart');
                if (ctxPromo) {
                    chartInstances.promo = new Chart(ctxPromo, {
                        type: 'bar',
                        data: { labels: [], datasets: [{ label: 'Revenue (RM)', data: [], backgroundColor: promoColors, borderColor: promoBorderColors, borderWidth: 1.5, borderRadius: 4 }] },
                        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: defaultTooltip }, scales: { x: { beginAtZero: true, ticks: { font: defaultFont } }, y: { grid: { display: false }, ticks: { font: defaultFont } } } }
                    });
                }

                const ctxCombo = document.getElementById('comboChart');
                if (ctxCombo) {
                    chartInstances.combo = new Chart(ctxCombo, {
                        type: 'doughnut',
                        data: { labels: [], datasets: [{ data: [], backgroundColor: ['#3b82f6', '#ef4444'], borderWidth: 0, hoverOffset: 4 }] },
                        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { font: defaultFont, color: '#64748b' } }, tooltip: defaultTooltip } }
                    });
                }

                const ctxTopItems = document.getElementById('topItemsChart');
                if (ctxTopItems) {
                    const gradTopItems = ctxTopItems.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradTopItems.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
                    gradTopItems.addColorStop(1, 'rgba(99, 102, 241, 0.05)');
                    chartInstances.topItems = new Chart(ctxTopItems, {
                        type: 'bar',
                        plugins: [ChartDataLabels],
                        data: { labels: [], datasets: [{ label: 'Value', data: [], backgroundColor: gradTopItems, borderColor: '#6366f1', borderWidth: 2, borderRadius: 8 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { ...defaultTooltip, callbacks: { label: c => (c.dataset.label || '') + ': ' + (document.getElementById('filterSortBy').value === 'revenue' ? 'RM ' + parseFloat(c.parsed.y).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : parseFloat(c.parsed.y).toLocaleString()) } }, datalabels: { display: true, anchor: 'end', align: 'top', offset: 4, formatter: v => parseFloat(v) <= 0 ? '' : (document.getElementById('filterSortBy').value === 'revenue' ? 'RM ' + parseFloat(v).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) : parseFloat(v).toLocaleString()), font: { family: 'Inter', weight: 'bold', size: 9 }, color: c => c.dataset.borderColor ? c.dataset.borderColor[c.dataIndex] : '#6366f1' } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { font: defaultFont }, grace: '15%' }, x: { grid: { display: false }, ticks: { font: defaultFont } } } }
                    });
                }



                // Swiper
                const swiper = new Swiper('.mySwiper', {
                    loop: true,
                    autoplay: { delay: 3000, disableOnInteraction: false, pauseOnMouseEnter: true },
                    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                    pagination: { el: '.swiper-pagination', clickable: true },
                });
                swiper.on('slideChange', function () {
                    const activeSlide = swiper.slides[swiper.activeIndex];
                    if (activeSlide) {
                        activeSlide.querySelectorAll('canvas').forEach(canvas => {
                            const chart = chartInstances[canvas.id.replace('Chart', '')];
                            if (chart) { chart.resize(); chart.update(); }
                        });
                    }
                });
            } else {
                // --- STAFF CHART INITIALIZATION ---
                const ctxStaffSalesPerformance = document.getElementById('staffSalesPerformanceChart');
                if (ctxStaffSalesPerformance) {
                    const gradPurple = ctxStaffSalesPerformance.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradPurple.addColorStop(0, 'rgba(139, 92, 246, 0.85)'); // violet-500
                    gradPurple.addColorStop(1, 'rgba(99, 102, 241, 0.25)'); // indigo-500
                    chartInstances.staffSalesPerformance = new Chart(ctxStaffSalesPerformance, {
                        type: 'bar',
                        data: { labels: [], datasets: [{ label: 'Sales (RM)', data: [], backgroundColor: gradPurple, borderColor: '#8b5cf6', borderWidth: 2, borderRadius: 8 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: defaultTooltip }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { font: defaultFont } }, x: { grid: { display: false }, ticks: { font: defaultFont } } } }
                    });
                }

                const ctxStaffTrend = document.getElementById('staffTrendChart');
                if (ctxStaffTrend) {
                    const gradBlue = ctxStaffTrend.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradBlue.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // blue-500
                    gradBlue.addColorStop(1, 'rgba(99, 102, 241, 0)');
                    chartInstances.staffTrend = new Chart(ctxStaffTrend, {
                        type: 'line',
                        data: { labels: [], datasets: [{ label: 'Revenue (RM)', data: [], borderColor: '#3b82f6', backgroundColor: gradBlue, borderWidth: 4, fill: true, tension: 0.4 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: defaultTooltip }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { font: defaultFont } }, x: { grid: { display: false }, ticks: { font: defaultFont } } } }
                    });
                }

                const ctxStaffTopItems = document.getElementById('staffTopItemsChart');
                if (ctxStaffTopItems) {
                    const gradIndigo = ctxStaffTopItems.getContext('2d').createLinearGradient(0, 0, 0, 400);
                    gradIndigo.addColorStop(0, 'rgba(99, 102, 241, 0.85)'); // indigo-500
                    gradIndigo.addColorStop(1, 'rgba(139, 92, 246, 0.25)'); // violet-500
                    chartInstances.staffTopItems = new Chart(ctxStaffTopItems, {
                        type: 'bar',
                        data: { labels: [], datasets: [{ label: 'Quantity', data: [], backgroundColor: gradIndigo, borderColor: '#6366f1', borderWidth: 2, borderRadius: 8 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: defaultTooltip }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { font: defaultFont } }, x: { grid: { display: false }, ticks: { font: defaultFont } } } }
                    });
                }

                const ctxStaffCategory = document.getElementById('staffCategoryChart');
                if (ctxStaffCategory) {
                    chartInstances.staffCategory = new Chart(ctxStaffCategory, {
                        type: 'pie',
                        data: { labels: [], datasets: [{ data: [], backgroundColor: ['#8b5cf6', '#3b82f6', '#ec4899', '#10b981', '#f59e0b'], borderWidth: 0, hoverOffset: 4 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: defaultFont, color: '#64748b' } }, tooltip: defaultTooltip } }
                    });
                }

                // Swiper Carousel for Staff Charts
                const staffChartsSwiper = new Swiper('.staffChartsSwiper', {
                    loop: true,
                    autoplay: { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
                    navigation: { nextEl: '.staff-swiper-button-next', prevEl: '.staff-swiper-button-prev' },
                    pagination: { el: '.staff-swiper-pagination', clickable: true },
                });
                staffChartsSwiper.on('slideChange', function () {
                    const activeSlide = staffChartsSwiper.slides[staffChartsSwiper.activeIndex];
                    if (activeSlide) {
                        activeSlide.querySelectorAll('canvas').forEach(canvas => {
                            const instanceKey = canvas.id.replace('Chart', '');
                            const chart = chartInstances[instanceKey];
                            if (chart) { chart.resize(); chart.update(); }
                        });
                    }
                });
            }

            window.downloadChart = function(chartId, filename) {
                const chart = chartInstances[chartId];
                if (chart) {
                    const imageURI = chart.toBase64Image();
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = imageURI;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            };

            // Loading helpers
            function showLoading() {
                const loader = document.getElementById('chartLoadingOverlay');
                if (loader) { loader.classList.remove('hidden'); loader.classList.add('flex'); }
                const indicator = document.getElementById('liveIndicator');
                if (indicator) { indicator.classList.remove('hidden'); indicator.classList.add('flex'); }
            }
            function hideLoading() {
                const loader = document.getElementById('chartLoadingOverlay');
                if (loader) { loader.classList.add('hidden'); loader.classList.remove('flex'); }
                const indicator = document.getElementById('liveIndicator');
                if (indicator) { indicator.classList.add('hidden'); indicator.classList.remove('flex'); }
            }

            // Debounce helper
            function debounce(fn, delay) {
                let timer;
                return function(...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            // Fetch and Update
            function updateDashboard() {
                const startDate = document.getElementById('filterStartDate').value;
                const endDate = document.getElementById('filterEndDate').value;
                const sortBy = document.getElementById('filterSortBy').value;

                showLoading();

                fetch(`{{ route('dashboard.data') }}?start_date=${startDate}&end_date=${endDate}&sort_by=${sortBy}`)
                    .then(res => res.json())
                    .then(data => {
                        if (isManager) {
                            // --- MANAGER UPDATE LOGIC ---
                            if (chartInstances.daily) {
                                chartInstances.daily.data.labels = data.daily.labels;
                                chartInstances.daily.data.datasets[0].data = data.daily.values;
                                chartInstances.daily.update();
                            }

                            if (chartInstances.monthly) {
                                chartInstances.monthly.data.labels = data.monthly.labels;
                                chartInstances.monthly.data.datasets[0].data = data.monthly.values;
                                const monthColors = ['rgba(79, 70, 229, 0.75)', 'rgba(16, 185, 129, 0.75)', 'rgba(245, 158, 11, 0.75)', 'rgba(239, 68, 68, 0.75)', 'rgba(139, 92, 246, 0.75)', 'rgba(6, 182, 212, 0.75)', 'rgba(236, 72, 153, 0.75)', 'rgba(20, 184, 166, 0.75)', 'rgba(249, 115, 22, 0.75)', 'rgba(99, 102, 241, 0.75)', 'rgba(168, 85, 247, 0.75)', 'rgba(74, 85, 104, 0.75)'];
                                const monthBorderColors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#14b8a6', '#f97316', '#6366f1', '#a855f7', '#4a5568'];
                                chartInstances.monthly.data.datasets[0].backgroundColor = data.monthly.labels.map((_, i) => monthColors[i % monthColors.length]);
                                chartInstances.monthly.data.datasets[0].borderColor = data.monthly.labels.map((_, i) => monthBorderColors[i % monthBorderColors.length]);
                                chartInstances.monthly.update();

                                const kpiTotalEl = document.getElementById('kpiMonthlyTotal');
                                const kpiAvgEl = document.getElementById('kpiMonthlyAverage');
                                const kpiHighEl = document.getElementById('kpiMonthlyHighest');
                                const kpiLowEl = document.getElementById('kpiMonthlyLowest');
                                if (kpiTotalEl && data.monthly.values && data.monthly.values.length > 0) {
                                    const values = data.monthly.values.map(v => parseFloat(v));
                                    const labels = data.monthly.labels;
                                    const total = values.reduce((a, b) => a + b, 0);
                                    const average = total / values.length;
                                    let hVal = -Infinity, hIdx = 0, lVal = Infinity, lIdx = 0;
                                    for (let i = 0; i < values.length; i++) {
                                        if (values[i] > hVal) { hVal = values[i]; hIdx = i; }
                                        if (values[i] < lVal) { lVal = values[i]; lIdx = i; }
                                    }
                                    kpiTotalEl.textContent = 'RM ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    kpiAvgEl.textContent = 'RM ' + average.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    kpiHighEl.textContent = (labels[hIdx] || 'N/A') + ' (RM ' + hVal.toLocaleString(undefined, {maximumFractionDigits: 0}) + ')';
                                    kpiLowEl.textContent = (labels[lIdx] || 'N/A') + ' (RM ' + lVal.toLocaleString(undefined, {maximumFractionDigits: 0}) + ')';
                                } else if (kpiTotalEl) {
                                    kpiTotalEl.textContent = 'RM 0.00'; kpiAvgEl.textContent = 'RM 0.00'; kpiHighEl.textContent = 'N/A'; kpiLowEl.textContent = 'N/A';
                                }
                            }

                            if (chartInstances.promo) {
                                chartInstances.promo.data.labels = data.promo.labels;
                                chartInstances.promo.data.datasets[0].data = data.promo.values;
                                chartInstances.promo.update();
                            }

                            if (chartInstances.combo) {
                                chartInstances.combo.data.labels = data.combo.labels;
                                chartInstances.combo.data.datasets[0].data = data.combo.values;
                                chartInstances.combo.update();
                            }

                            if (chartInstances.topItems) {
                                const subtext = document.getElementById('topItemsSubtext');
                                if (subtext) { subtext.innerText = sortBy === 'quantity' ? 'By quantity' : 'By revenue'; }
                                chartInstances.topItems.data.labels = data.topItems.labels.map(name => {
                                    const emojiMap = { 'dynamo': '🧴', 'comfort': '🌸', 'downy': '🌸', 'clorox': '🧪', 'vanish': '✨' };
                                    const lower = name.toLowerCase();
                                    let emoji = '📦';
                                    for (const key in emojiMap) { if (lower.includes(key)) { emoji = emojiMap[key]; break; } }
                                    return emoji + ' ' + name;
                                });
                                chartInstances.topItems.data.datasets[0].data = sortBy === 'quantity' ? data.topItems.quantities : data.topItems.revenues;
                                chartInstances.topItems.data.datasets[0].label = sortBy === 'quantity' ? 'Quantity' : 'Revenue (RM)';
                                const topItemsColors = ['rgba(99, 102, 241, 0.75)', 'rgba(16, 185, 129, 0.75)', 'rgba(245, 158, 11, 0.75)', 'rgba(239, 68, 68, 0.75)', 'rgba(139, 92, 246, 0.75)', 'rgba(59, 130, 246, 0.75)'];
                                const topItemsBorderColors = ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#3b82f6'];
                                chartInstances.topItems.data.datasets[0].backgroundColor = data.topItems.labels.map((_, i) => topItemsColors[i % topItemsColors.length]);
                                chartInstances.topItems.data.datasets[0].borderColor = data.topItems.labels.map((_, i) => topItemsBorderColors[i % topItemsBorderColors.length]);
                                chartInstances.topItems.update();

                                const kpiTopTotalEl = document.getElementById('kpiTopItemsTotal');
                                const kpiTopAvgEl = document.getElementById('kpiTopItemsAverage');
                                const kpiTopAvgLabelEl = document.getElementById('kpiTopItemsAverageLabel');
                                const kpiTopTopEl = document.getElementById('kpiTopItemsTop');
                                const kpiTopUniqueEl = document.getElementById('kpiTopItemsUnique');
                                if (kpiTopTotalEl && data.topItems.labels && data.topItems.labels.length > 0) {
                                    const labels = data.topItems.labels;
                                    const quantities = data.topItems.quantities.map(q => parseInt(q) || 0);
                                    const revenues = data.topItems.revenues.map(r => parseFloat(r) || 0);
                                    const uniqueCount = labels.length;
                                    const totalSold = quantities.reduce((a, b) => a + b, 0);
                                    const totalRevenue = revenues.reduce((a, b) => a + b, 0);
                                    if (sortBy === 'quantity') {
                                        kpiTopTotalEl.textContent = totalSold.toLocaleString();
                                        kpiTopAvgEl.textContent = (totalSold / uniqueCount).toFixed(1);
                                        if (kpiTopAvgLabelEl) kpiTopAvgLabelEl.textContent = 'Avg Qty / Item';
                                        let maxQty = -1, maxIdx = 0;
                                        for (let i = 0; i < quantities.length; i++) { if (quantities[i] > maxQty) { maxQty = quantities[i]; maxIdx = i; } }
                                        kpiTopTopEl.textContent = (labels[maxIdx] || 'N/A') + ` (${maxQty})`;
                                    } else {
                                        kpiTopTotalEl.textContent = 'RM ' + totalRevenue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        kpiTopAvgEl.textContent = 'RM ' + (totalRevenue / uniqueCount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                        if (kpiTopAvgLabelEl) kpiTopAvgLabelEl.textContent = 'Avg Rev / Item';
                                        let maxRev = -1, maxIdx = 0;
                                        for (let i = 0; i < revenues.length; i++) { if (revenues[i] > maxRev) { maxRev = revenues[i]; maxIdx = i; } }
                                        kpiTopTopEl.textContent = (labels[maxIdx] || 'N/A') + ` (RM ` + maxRev.toLocaleString(undefined, {maximumFractionDigits: 0}) + `)`;
                                    }
                                    kpiTopUniqueEl.textContent = uniqueCount.toLocaleString();
                                } else if (kpiTopTotalEl) {
                                    kpiTopTotalEl.textContent = '0'; kpiTopAvgEl.textContent = '0'; kpiTopTopEl.textContent = 'N/A'; kpiTopUniqueEl.textContent = '0';
                                }
                            }


                        } else {
                            // --- STAFF UPDATE LOGIC ---
                            if (chartInstances.staffSalesPerformance) {
                                chartInstances.staffSalesPerformance.data.labels = data.daily.labels;
                                chartInstances.staffSalesPerformance.data.datasets[0].data = data.daily.values;
                                chartInstances.staffSalesPerformance.update();

                                const hasSalesPerformance = data.daily.values && data.daily.values.length > 0 && data.daily.values.some(v => parseFloat(v) > 0);
                                const performanceEmptyEl = document.getElementById('staffSalesPerformanceEmpty');
                                if (performanceEmptyEl) {
                                    performanceEmptyEl.classList.toggle('hidden', hasSalesPerformance);
                                    performanceEmptyEl.classList.toggle('flex', !hasSalesPerformance);
                                }
                            }

                            if (chartInstances.staffTrend) {
                                chartInstances.staffTrend.data.labels = data.daily.labels;
                                chartInstances.staffTrend.data.datasets[0].data = data.daily.values;
                                chartInstances.staffTrend.update();

                                const hasTrend = data.daily.values && data.daily.values.length > 0 && data.daily.values.some(v => parseFloat(v) > 0);
                                const trendEmptyEl = document.getElementById('staffTrendEmpty');
                                if (trendEmptyEl) {
                                    trendEmptyEl.classList.toggle('hidden', hasTrend);
                                    trendEmptyEl.classList.toggle('flex', !hasTrend);
                                }
                            }

                            if (chartInstances.staffTopItems) {
                                chartInstances.staffTopItems.data.labels = data.topItems.labels;
                                chartInstances.staffTopItems.data.datasets[0].data = sortBy === 'quantity' ? data.topItems.quantities : data.topItems.revenues;
                                chartInstances.staffTopItems.data.datasets[0].label = sortBy === 'quantity' ? 'Quantity' : 'Revenue (RM)';
                                chartInstances.staffTopItems.update();

                                const hasTopItems = data.topItems.labels && data.topItems.labels.length > 0;
                                const topItemsEmptyEl = document.getElementById('staffTopItemsEmpty');
                                if (topItemsEmptyEl) {
                                    topItemsEmptyEl.classList.toggle('hidden', hasTopItems);
                                    topItemsEmptyEl.classList.toggle('flex', !hasTopItems);
                                }
                            }

                            if (chartInstances.staffCategory) {
                                chartInstances.staffCategory.data.labels = data.categoryDistribution.labels;
                                chartInstances.staffCategory.data.datasets[0].data = data.categoryDistribution.values;
                                chartInstances.staffCategory.update();

                                const hasCategory = data.categoryDistribution.labels && data.categoryDistribution.labels.length > 0;
                                const categoryEmptyEl = document.getElementById('staffCategoryEmpty');
                                if (categoryEmptyEl) {
                                    categoryEmptyEl.classList.toggle('hidden', hasCategory);
                                    categoryEmptyEl.classList.toggle('flex', !hasCategory);
                                }
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Dashboard fetch error:', err);
                    })
                    .finally(() => {
                        hideLoading();
                    });
            }

            const debouncedUpdate = debounce(updateDashboard, 300);
            document.getElementById('filterStartDate').addEventListener('change', debouncedUpdate);
            document.getElementById('filterEndDate').addEventListener('change', debouncedUpdate);
            document.getElementById('filterSortBy').addEventListener('change', updateDashboard);

            updateDashboard();
        });
    </script>
</x-app-layout>