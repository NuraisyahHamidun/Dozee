<x-app-layout>
<style>
    /* ─── Google Font ──────────────────────────────────────────────── */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    body { font-family: 'Inter', sans-serif; }

    /* ─── Global micro-interactions ────────────────────────────────── */
    .ar-btn {
        transition: transform 150ms cubic-bezier(0.16,1,0.3,1),
                    box-shadow 150ms cubic-bezier(0.16,1,0.3,1),
                    background-color 150ms ease,
                    opacity 150ms ease;
    }
    .ar-btn:hover  { transform: translateY(-1px); }
    .ar-btn:active { transform: translateY(0) scale(0.97); }

    .ar-card {
        transition: transform 280ms cubic-bezier(0.16,1,0.3,1),
                    box-shadow 280ms cubic-bezier(0.16,1,0.3,1),
                    border-color 280ms ease;
    }
    .ar-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px -8px rgba(139,92,246,.14), 0 0 24px -6px rgba(139,92,246,.08);
    }

    /* ─── Strength badge keyframe ───────────────────────────────────── */
    @keyframes badge-pulse-red {
        0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.15); }
        50%      { box-shadow: 0 0 10px 4px rgba(239,68,68,.2); }
    }
    .badge-high { animation: badge-pulse-red 2.8s infinite ease-in-out; }

    /* ─── Table rows ───────────────────────────────────────────────── */
    .rule-row { transition: background-color 120ms; }
    .rule-row:hover { background-color: #faf5ff !important; }

    /* ─── Clickable sort headers ────────────────────────────────────── */
    .sort-th a {
        display: inline-flex; align-items: center; gap: 3px;
        transition: color 120ms;
    }
    .sort-th a:hover { color: #7c3aed; }
    .sort-th a.active { color: #7c3aed; font-weight: 900; }

    /* ─── Pagination ────────────────────────────────────────────────── */
    .pg-link {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; border-radius: 8px;
        font-size: 11px; font-weight: 700;
        border: 1px solid #e2e8f0; color: #64748b; padding: 0 6px;
        transition: all 130ms;
    }
    .pg-link:hover  { background:#f5f3ff; border-color:#c4b5fd; color:#7c3aed; }
    .pg-link.on     { background:#7c3aed; border-color:#7c3aed; color:#fff; }
    .pg-link.off    { opacity:.35; pointer-events:none; }

    /* ─── Filter bar inputs ─────────────────────────────────────────── */
    .fc { @apply px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700; }
    .fc:focus { @apply outline-none ring-2 ring-purple-500 border-purple-400 bg-white; }

    /* ─── Toggle switch ─────────────────────────────────────────────── */
    .tog-track { transition: background-color 200ms; }
    input.tog:checked ~ .tog-track { background-color:#7c3aed; }
    input.tog:checked ~ .tog-track .tog-thumb { transform:translateX(16px); }
    .tog-thumb { transition: transform 200ms cubic-bezier(0.16,1,0.3,1); }

    /* ─── Modal (Apple-spring) ──────────────────────────────────────── */
    .ar-backdrop {
        opacity:0; pointer-events:none;
        backdrop-filter:blur(0px);
        transition: opacity 300ms cubic-bezier(0.16,1,0.3,1),
                    backdrop-filter 300ms cubic-bezier(0.16,1,0.3,1);
    }
    .ar-backdrop.open { opacity:1; pointer-events:all; backdrop-filter:blur(14px); }
    .ar-modal {
        transform:scale(0.92); opacity:0;
        transition: transform 300ms cubic-bezier(0.16,1,0.3,1),
                    opacity 300ms cubic-bezier(0.16,1,0.3,1);
    }
    .ar-backdrop.open .ar-modal { transform:scale(1); opacity:1; }

    /* ─── Sticky sidebar ────────────────────────────────────────────── */
    @media (min-width:1280px) { .sidebar-sticky { position:sticky; top:24px; } }

    /* ─── Section divider label ──────────────────────────────────────── */
    .sec-label {
        display:flex; align-items:center; gap:8px;
        font-size:10px; font-weight:900; color:#94a3b8;
        text-transform:uppercase; letter-spacing:.12em;
    }
    .sec-label::before {
        content:''; display:block; width:5px; height:14px;
        background:#7c3aed; border-radius:9999px;
    }

    /* ─── Bottom action bar ─────────────────────────────────────────── */
    .action-bar {
        background: linear-gradient(135deg,#ffffff 0%,#faf5ff 100%);
    }
</style>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{--  HEADER SLOT                                                        --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<x-slot name="header">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
        <div>
            {{-- Breadcrumb / back --}}
            <a href="{{ route('analysis.weka') }}"
               class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-400 hover:text-purple-600 transition-colors uppercase tracking-wider mb-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Apriori algorithm
                <span class="text-slate-300 mx-0.5">/</span>
                <span class="text-purple-600">Apriori Algorithm (WEKA)</span>
            </a>

            <h2 class="flex items-center gap-3 font-extrabold text-2xl text-slate-900 leading-tight">
                <span class="p-2 bg-gradient-to-br from-purple-600 to-violet-700 rounded-xl text-white shadow-lg shadow-purple-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </span>
                Apriori Algorithm (WEKA)
            </h2>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                Complete dataset of Apriori-generated rules for filtering, sorting, and business insights.
            </p>
        </div>

        {{-- Status badge --}}
        <div class="flex items-center gap-2 shrink-0 bg-white border border-slate-100 shadow-sm rounded-2xl px-4 py-2.5">
            <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-500">
                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                WEKA Association Engine
            </span>
            <span class="w-px h-4 bg-slate-200"></span>
            <span class="text-xs font-bold text-slate-500">Date: {{ now()->format('M d, Y') }}</span>
            <span class="w-px h-4 bg-slate-200"></span>
            <span class="text-xs font-black text-purple-700">{{ number_format($stats['total']) }} Rules</span>
        </div>
    </div>
</x-slot>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{--  PAGE BODY                                                          --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="py-7 bg-slate-50 min-h-screen">
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 space-y-7">

{{-- ─────────────────────────────────────────────────────────────────── --}}
{{--  SECTION 1 : KPI SUMMARY CARDS                                      --}}
{{-- ─────────────────────────────────────────────────────────────────── --}}
<div>
    <p class="sec-label mb-4">Summary</p>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

        {{-- Total Sales / Transactions --}}
        <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm p-4 ar-card overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-t-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Sales</p>
                    <p class="mt-2 text-3xl font-black text-slate-900 tabular-nums leading-none">{{ number_format(\App\Models\Sale::count()) }}</p>
                    <p class="mt-1 text-[9px] font-semibold text-slate-400">Transactions analysed</p>
                </div>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-xl shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Items Tracked --}}
        <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm p-4 ar-card overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-t-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Items Tracked</p>
                    <p class="mt-2 text-3xl font-black text-slate-900 tabular-nums leading-none">{{ number_format(\App\Models\Product::count()) }}</p>
                    <p class="mt-1 text-[9px] font-semibold text-slate-400">Products in database</p>
                </div>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Rules Found --}}
        <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm p-4 ar-card overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-t-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Rules Found</p>
                    <p class="mt-2 text-3xl font-black text-slate-900 tabular-nums leading-none">{{ number_format($stats['total']) }}</p>
                    <p class="mt-1 text-[9px] font-semibold text-slate-400">WEKA Apriori rules</p>
                </div>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Max Lift --}}
        <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm p-4 ar-card overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 to-red-500 rounded-t-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Max Lift</p>
                    <p class="mt-2 text-3xl font-black text-rose-600 tabular-nums leading-none">{{ number_format($stats['max_lift'], 2) }}<span class="text-xl font-bold">×</span></p>
                    <p class="mt-1 text-[9px] font-semibold text-slate-400">Strongest association</p>
                </div>
                <span class="p-2 bg-rose-50 text-rose-600 rounded-xl shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </span>
            </div>
        </div>

        {{-- Avg Confidence --}}
        <div class="relative bg-white rounded-2xl border border-slate-100 shadow-sm p-4 ar-card overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-t-2xl"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg Confidence</p>
                    <p class="mt-2 text-3xl font-black text-emerald-600 tabular-nums leading-none">{{ number_format($stats['avg_confidence'] * 100, 1) }}<span class="text-xl font-bold">%</span></p>
                    <p class="mt-1 text-[9px] font-semibold text-slate-400">Mean rule certainty</p>
                </div>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </span>
            </div>
        </div>

    </div>
</div>

{{-- ─────────────────────────────────────────────────────────────────── --}}
{{--  SECTION 2 : FILTER / CONTROL BAR                                   --}}
{{-- ─────────────────────────────────────────────────────────────────── --}}
<div>
    <p class="sec-label mb-4">Filters &amp; Controls</p>
    <form method="GET" action="{{ route('analysis.weka.allRules') }}" id="filter-form">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">

                {{-- Search --}}
                <div class="xl:col-span-2 space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Search Rule</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" id="search-input" value="{{ $search }}"
                               placeholder="Item name or rule ID…"
                               class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-purple-400 focus:bg-white transition-colors placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                </div>

                {{-- Support filter --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Support</label>
                    <select name="support_filter" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition-colors">
                        <option value=""       {{ $filterSupp===''       ? 'selected':'' }}>All Support</option>
                        <option value="high"   {{ $filterSupp==='high'   ? 'selected':'' }}>High (≥ 30%)</option>
                        <option value="medium" {{ $filterSupp==='medium' ? 'selected':'' }}>Medium (10–30%)</option>
                        <option value="low"    {{ $filterSupp==='low'    ? 'selected':'' }}>Low (&lt; 10%)</option>
                    </select>
                </div>

                {{-- Confidence filter --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Confidence</label>
                    <select name="confidence_filter" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition-colors">
                        <option value=""       {{ $filterConf===''       ? 'selected':'' }}>All Confidence</option>
                        <option value="high"   {{ $filterConf==='high'   ? 'selected':'' }}>High (≥ 80%)</option>
                        <option value="medium" {{ $filterConf==='medium' ? 'selected':'' }}>Medium (50–80%)</option>
                        <option value="low"    {{ $filterConf==='low'    ? 'selected':'' }}>Low (&lt; 50%)</option>
                    </select>
                </div>

                {{-- Sort --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Sort By</label>
                    <select name="sort" onchange="this.form.submit()"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition-colors">
                        <option value="lift"       {{ $sortBy==='lift'       ? 'selected':'' }}>Lift</option>
                        <option value="confidence" {{ $sortBy==='confidence' ? 'selected':'' }}>Confidence</option>
                        <option value="support"    {{ $sortBy==='support'    ? 'selected':'' }}>Support</option>
                    </select>
                </div>

                {{-- Actions row: toggle + buttons --}}
                <div class="flex items-end gap-2">
                    {{-- High Impact toggle --}}
                    <label class="flex flex-col gap-1 cursor-pointer shrink-0">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">High Impact</span>
                        <div class="flex items-center gap-2 py-2.5">
                            <input type="checkbox" name="high_impact" value="1" {{ $highImpact ? 'checked':'' }}
                                   onchange="this.form.submit()" class="tog sr-only peer">
                            <div class="tog-track w-9 h-5 bg-slate-200 rounded-full relative peer-checked:bg-purple-600">
                                <div class="tog-thumb absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow"></div>
                            </div>
                        </div>
                    </label>

                    {{-- Search button --}}
                    <button type="submit"
                            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-extrabold shadow-sm ar-btn">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search
                    </button>

                    @if($search || $filterSupp || $filterConf || $highImpact)
                    <a href="{{ route('analysis.weka.allRules') }}"
                       class="flex items-center justify-center px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold ar-btn shrink-0"
                       title="Clear all filters">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Active filter pills --}}
            @if($search || $filterSupp || $filterConf || $highImpact || $sortBy !== 'lift')
            <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-slate-50">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider self-center">Active:</span>
                @if($search) <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 text-purple-700 text-[10px] font-bold rounded-full border border-purple-100">"{{ $search }}"</span> @endif
                @if($filterSupp) <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-full border border-blue-100">Support: {{ ucfirst($filterSupp) }}</span> @endif
                @if($filterConf) <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-full border border-emerald-100">Confidence: {{ ucfirst($filterConf) }}</span> @endif
                @if($highImpact) <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 text-[10px] font-bold rounded-full border border-red-100">🔥 High Impact Only</span> @endif
                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 text-slate-600 text-[10px] font-bold rounded-full border border-slate-200">
                    Sort: {{ ucfirst($sortBy) }} ({{ $sortDir === 'desc' ? '↓' : '↑' }})
                </span>
            </div>
            @endif
        </div>
    </form>
</div>

{{-- ─────────────────────────────────────────────────────────────────── --}}
{{--  SECTION 3 : MAIN TABLE + RIGHT SIDEBAR                             --}}
{{-- ─────────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

    {{-- ── LEFT: Rules Table ──────────────────────────────────────── --}}
    <div class="xl:col-span-8">
        <p class="sec-label mb-4">Association Rules</p>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            {{-- Table toolbar --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50">
                <div class="flex items-center gap-3">
                    <h3 class="text-sm font-black text-slate-900">Rules Table</h3>
                    <span class="px-2.5 py-0.5 bg-purple-50 text-purple-600 text-[10px] font-black rounded-full border border-purple-100">
                        {{ $rules->total() }} Total
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-semibold text-slate-400">
                        {{ $rules->firstItem() ?? 0 }}–{{ $rules->lastItem() ?? 0 }} of {{ $rules->total() }}
                    </span>
                    {{-- Direction toggle --}}
                    <form method="GET" action="{{ route('analysis.weka.allRules') }}" class="inline">
                        @foreach(request()->except(['dir','page']) as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <input type="hidden" name="dir" value="{{ $sortDir === 'desc' ? 'asc' : 'desc' }}">
                        <button type="submit"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-lg text-[10px] font-bold ar-btn">
                            {{ $sortDir === 'desc' ? '↓ Desc' : '↑ Asc' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-50 to-slate-50/60 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-6 py-3.5 text-left w-10">#</th>
                            <th class="px-4 py-3.5 text-left">Rule (X → Y)</th>
                            <th class="px-4 py-3.5 text-center sort-th">
                                <a href="{{ route('analysis.weka.allRules', array_merge(request()->except(['sort','dir','page']), ['sort'=>'support','dir'=>($sortBy==='support'&&$sortDir==='desc')?'asc':'desc'])) }}"
                                   class="{{ $sortBy==='support'?'active':'' }}">
                                   Support
                                   <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ ($sortBy==='support'&&$sortDir==='asc') ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                   </svg>
                                </a>
                            </th>
                            <th class="px-4 py-3.5 text-center sort-th">
                                <a href="{{ route('analysis.weka.allRules', array_merge(request()->except(['sort','dir','page']), ['sort'=>'confidence','dir'=>($sortBy==='confidence'&&$sortDir==='desc')?'asc':'desc'])) }}"
                                   class="{{ $sortBy==='confidence'?'active':'' }}">
                                   Confidence
                                   <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ ($sortBy==='confidence'&&$sortDir==='asc') ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                   </svg>
                                </a>
                            </th>
                            <th class="px-4 py-3.5 text-center sort-th">
                                <a href="{{ route('analysis.weka.allRules', array_merge(request()->except(['sort','dir','page']), ['sort'=>'lift','dir'=>($sortBy==='lift'&&$sortDir==='desc')?'asc':'desc'])) }}"
                                   class="{{ $sortBy==='lift'?'active':'' }}">
                                   Lift
                                   <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ ($sortBy==='lift'&&$sortDir==='asc') ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                   </svg>
                                </a>
                            </th>
                            <th class="px-4 py-3.5 text-center">Strength</th>
                            <th class="px-4 py-3.5 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-50">
                    @forelse($rules as $i => $rule)
                        @php
                            $antName       = $rule->antecedentProduct->item_name ?? $rule->antecedent;
                            $conName       = $rule->consequentProduct->item_name  ?? $rule->consequent;
                            
                            $antCode       = '';
                            if ($rule->isMultiAntecedent()) {
                                $ids = $rule->antecedentIds();
                                $codes = array_map(fn($id) => $itemCodes[$id] ?? 'Item #' . $id, $ids);
                                $antCode = implode(' + ', $codes);
                            } else {
                                $antCode = $itemCodes[$rule->antecedent] ?? 'Item #' . $rule->antecedent;
                            }
                            $conCode       = $itemCodes[$rule->consequent] ?? 'Item #' . $rule->consequent;

                            $lift          = (float) $rule->lift;
                            $strength      = $lift >= 10 ? 'High' : ($lift >= 3 ? 'Medium' : 'Low');
                            $badgeStyle    = match($strength) {
                                'High'   => 'bg-red-50 text-red-600 border border-red-200 badge-high',
                                'Medium' => 'bg-orange-50 text-orange-600 border border-orange-200',
                                default  => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            };
                            $conviction    = isset($rule->conviction) ? number_format($rule->conviction, 4) : 'N/A';
                            $rulePayload   = json_encode([
                                'antecedent' => $antName,
                                'consequent' => $conName,
                                'antecedent_code' => $antCode,
                                'consequent_code' => $conCode,
                                'support'    => number_format($rule->support * 100, 2),
                                'confidence' => number_format($rule->confidence * 100, 2),
                                'lift'       => number_format($lift, 4),
                                'conviction' => $conviction,
                                'strength'   => $strength,
                            ]);
                        @endphp
                        <tr class="rule-row {{ $i % 2 === 0 ? 'bg-white' : 'bg-slate-50/40' }}">
                            {{-- # --}}
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black text-slate-300 tabular-nums">{{ $rules->firstItem() + $i }}</span>
                            </td>

                            {{-- Rule --}}
                            <td class="px-4 py-4 max-w-xs">
                                <div class="flex items-center gap-2 flex-wrap" title="{{ $antName }} → {{ $conName }}">
                                    <span class="font-bold text-slate-800 leading-tight">{{ $antCode }}</span>
                                    <span class="shrink-0 inline-flex items-center justify-center w-5 h-5 bg-purple-100 rounded-full">
                                        <svg class="w-2.5 h-2.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                        </svg>
                                    </span>
                                    <span class="font-bold text-purple-700 leading-tight">{{ $conCode }}</span>
                                </div>
                            </td>

                            {{-- Support --}}
                            <td class="px-4 py-4 text-center">
                                <span class="text-xs font-bold text-slate-600 tabular-nums">{{ number_format($rule->support * 100, 1) }}%</span>
                            </td>

                            {{-- Confidence --}}
                            <td class="px-4 py-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-xs font-bold text-slate-600 tabular-nums">{{ number_format($rule->confidence * 100, 1) }}%</span>
                                    <div class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-400 rounded-full" style="width:{{ number_format($rule->confidence * 100, 0) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Lift --}}
                            <td class="px-4 py-4 text-center">
                                <span class="text-sm font-black text-purple-700 tabular-nums">{{ number_format($lift, 2) }}×</span>
                            </td>

                            {{-- Strength badge --}}
                            <td class="px-4 py-4 text-center">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black {{ $badgeStyle }}">
                                    @if($strength === 'High')   🔴
                                    @elseif($strength === 'Medium') 🟠
                                    @else 🟢
                                    @endif
                                    {{ $strength }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- View button --}}
                                    <button onclick="openRuleModal({{ $rulePayload }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 rounded-lg text-[10px] font-bold ar-btn transition-colors"
                                            title="View: {{ $antName }} → {{ $conName }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </button>

                                    {{-- Create Bundle button --}}
                                    @if(Auth::guard('manager')->check())
                                    <button onclick="openBundleModal(
                                                '{{ $rule->rule_id }}',
                                                '{{ addslashes($antName) }} ==> {{ addslashes($conName) }}',
                                                '{{ number_format($rule->support * 100, 2) }}',
                                                '{{ number_format($rule->confidence * 100, 2) }}',
                                                '{{ number_format($lift, 3) }}'
                                            )"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-[10px] font-bold ar-btn shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        Create Bundle
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <span class="p-5 bg-slate-50 rounded-2xl">
                                        <svg class="w-9 h-9 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                                        </svg>
                                    </span>
                                    <div>
                                        <p class="text-sm font-black text-slate-400">No rules found</p>
                                        <p class="text-xs text-slate-400 mt-1">
                                            Try adjusting your filters or
                                            <a href="{{ route('analysis.weka.allRules') }}" class="text-purple-600 font-bold hover:underline">clear all</a>
                                        </p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Pagination ──────────────────────────────────────────── --}}
            @if($rules->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-6 py-4 border-t border-slate-50 bg-gradient-to-r from-slate-50/40 to-white">
                <p class="text-[10px] font-semibold text-slate-400">
                    Showing <span class="font-black text-slate-600">{{ $rules->firstItem() }}–{{ $rules->lastItem() }}</span> of <span class="font-black text-slate-600">{{ $rules->total() }}</span> rules
                    · Page <span class="font-black text-slate-600">{{ $rules->currentPage() }}</span> of <span class="font-black text-slate-600">{{ $rules->lastPage() }}</span>
                </p>

                <div class="flex items-center gap-1">
                    {{-- First --}}
                    @if(!$rules->onFirstPage())
                    <a href="{{ $rules->url(1) }}" class="pg-link" title="First page">«</a>
                    @endif

                    {{-- Prev --}}
                    @if($rules->onFirstPage())
                        <span class="pg-link off">‹</span>
                    @else
                        <a href="{{ $rules->previousPageUrl() }}" class="pg-link">‹</a>
                    @endif

                    @php
                        $cp    = $rules->currentPage();
                        $lp    = $rules->lastPage();
                        $start = max(1, $cp - 2);
                        $end   = min($lp, $cp + 2);
                    @endphp

                    @if($start > 1)
                        <a href="{{ $rules->url(1) }}" class="pg-link">1</a>
                        @if($start > 2) <span class="pg-link off border-transparent">…</span> @endif
                    @endif

                    @for($p = $start; $p <= $end; $p++)
                        <a href="{{ $rules->url($p) }}" class="pg-link {{ $p===$cp?'on':'' }}">{{ $p }}</a>
                    @endfor

                    @if($end < $lp)
                        @if($end < $lp - 1) <span class="pg-link off border-transparent">…</span> @endif
                        <a href="{{ $rules->url($lp) }}" class="pg-link">{{ $lp }}</a>
                    @endif

                    {{-- Next --}}
                    @if($rules->hasMorePages())
                        <a href="{{ $rules->nextPageUrl() }}" class="pg-link">›</a>
                    @else
                        <span class="pg-link off">›</span>
                    @endif

                    {{-- Last --}}
                    @if($rules->hasMorePages())
                    <a href="{{ $rules->url($rules->lastPage()) }}" class="pg-link" title="Last page">»</a>
                    @endif
                </div>
            </div>
            @endif

        </div>{{-- /table card --}}
    </div>{{-- /left col --}}

    {{-- ── RIGHT SIDEBAR ────────────────────────────────────────────── --}}
    <div class="xl:col-span-4">
        <div class="sidebar-sticky space-y-5">
            <p class="sec-label">Insights</p>

            {{-- ① Best Rule Insight --}}
            @php
                $bestRule = \App\Models\AprioriAnalysis::orderByDesc('lift')->first();
            @endphp
            @if($bestRule)
                @php
                    $bestConv = '1.00';
                    $bestCleanText = $bestRule->rule_text;
                    if (preg_match('/\[conv:(.*?)\]/', $bestRule->rule_text, $match)) {
                        $bestConv = $match[1];
                        $bestCleanText = trim(str_replace($match[0], '', $bestRule->rule_text));
                    }
                    $parts = explode('==>', $bestCleanText);
                    $anteText = trim($parts[0] ?? 'Unknown');
                    $consText = trim($parts[1] ?? 'Unknown');
                    
                    $anteIds = explode('+', $bestRule->antecedent);
                    $anteNames = array_map(fn($id) => $items[$id] ?? 'Item #' . $id, $anteIds);
                    $anteNameStr = implode(' + ', $anteNames);
                    $consNameStr = $items[$bestRule->consequent] ?? 'Item #' . $bestRule->consequent;
                    
                    $anteCodes = array_map(fn($id) => $itemCodes[$id] ?? 'Item #' . $id, $anteIds);
                    $anteCodeStr = implode(' + ', $anteCodes);
                    $consCodeStr = $itemCodes[$bestRule->consequent] ?? 'Item #' . $bestRule->consequent;
                @endphp
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 relative overflow-hidden group ar-card">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-amber-500 to-yellow-400 rounded-t-2xl"></div>
                    <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Best Rule Insight
                    </h4>
                    <div class="space-y-4">
                        <div class="p-3.5 bg-amber-50/50 rounded-xl border border-amber-100/50">
                            <p class="text-[9px] font-black text-amber-600 uppercase tracking-wider mb-1">Optimal Combination</p>
                            <p class="text-xs font-bold text-slate-800 leading-tight" title="{{ $anteNameStr }} ➔ {{ $consNameStr }}">
                                {{ $anteCodeStr }} <span class="text-amber-500 font-extrabold">➔</span> {{ $consCodeStr }}
                            </p>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400 font-semibold">Support</span>
                                <span class="font-extrabold text-slate-800 tabular-nums">{{ round($bestRule->support * 100, 2) }}%</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400 font-semibold">Confidence</span>
                                <span class="font-extrabold text-purple-600 tabular-nums">{{ round($bestRule->confidence * 100) }}%</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-50 pb-1.5">
                                <span class="text-slate-400 font-semibold">Lift</span>
                                <span class="font-extrabold text-slate-800 tabular-nums">{{ number_format($bestRule->lift, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400 font-semibold">Conviction</span>
                                <span class="font-extrabold text-slate-800 tabular-nums">{{ $bestConv }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ② Strength Distribution --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-purple-600 rounded-full"></span>
                    Strength Distribution
                </h4>

                @php
                    $distHigh   = \App\Models\AprioriAnalysis::where('lift','>=',10)->count();
                    $distMed    = \App\Models\AprioriAnalysis::whereBetween('lift',[3,10])->count();
                    $distLow    = \App\Models\AprioriAnalysis::where('lift','<',3)->count();
                    $distTotal  = $stats['total'] ?: 1;
                @endphp

                <div class="space-y-4">
                    {{-- High --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                <span class="text-[11px] font-bold text-slate-700">High (≥ 10×)</span>
                            </div>
                            <span class="text-[11px] font-black text-red-600">{{ $distHigh }} <span class="font-semibold text-slate-400">({{ $distTotal > 0 ? round(($distHigh/$distTotal)*100) : 0 }}%)</span></span>
                        </div>
                        <div class="w-full bg-red-50 rounded-full h-2.5">
                            <div class="bg-red-400 h-2.5 rounded-full transition-all duration-700" style="width:{{ $distTotal>0 ? round(($distHigh/$distTotal)*100) : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Medium --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-400"></span>
                                <span class="text-[11px] font-bold text-slate-700">Medium (3–10×)</span>
                            </div>
                            <span class="text-[11px] font-black text-orange-600">{{ $distMed }} <span class="font-semibold text-slate-400">({{ $distTotal>0 ? round(($distMed/$distTotal)*100) : 0 }}%)</span></span>
                        </div>
                        <div class="w-full bg-orange-50 rounded-full h-2.5">
                            <div class="bg-orange-400 h-2.5 rounded-full transition-all duration-700" style="width:{{ $distTotal>0 ? round(($distMed/$distTotal)*100) : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Low --}}
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                <span class="text-[11px] font-bold text-slate-700">Low (&lt; 3×)</span>
                            </div>
                            <span class="text-[11px] font-black text-emerald-700">{{ $distLow }} <span class="font-semibold text-slate-400">({{ $distTotal>0 ? round(($distLow/$distTotal)*100) : 0 }}%)</span></span>
                        </div>
                        <div class="w-full bg-emerald-50 rounded-full h-2.5">
                            <div class="bg-emerald-400 h-2.5 rounded-full transition-all duration-700" style="width:{{ $distTotal>0 ? round(($distLow/$distTotal)*100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ③ Reading Guide --}}
            <div class="bg-gradient-to-br from-purple-50 via-violet-50 to-slate-50 rounded-2xl border border-purple-100 shadow-sm p-5">
                <h4 class="text-xs font-black text-purple-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="p-1 bg-purple-100 text-purple-600 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    Reading Guide
                </h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 w-2 h-2 rounded-full bg-red-400 shrink-0"></span>
                        <p class="text-[11px] font-semibold text-purple-900 leading-relaxed">
                            <span class="font-black">Lift &gt; 10×</span> — Extremely strong co-purchase. Top priority for bundle promotions.
                        </p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 w-2 h-2 rounded-full bg-orange-400 shrink-0"></span>
                        <p class="text-[11px] font-semibold text-purple-900 leading-relaxed">
                            <span class="font-black">Lift 3–10×</span> — Meaningful association. Good for cross-sell campaigns.
                        </p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                        <p class="text-[11px] font-semibold text-purple-900 leading-relaxed">
                            <span class="font-black">Lift &lt; 3×</span> — Weak signal. Useful when confidence is high.
                        </p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 w-2 h-2 rounded-full bg-purple-400 shrink-0"></span>
                        <p class="text-[11px] font-semibold text-purple-900 leading-relaxed">
                            <span class="font-black">Confidence</span> — % of transactions with X that also include Y.
                        </p>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-1 w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                        <p class="text-[11px] font-semibold text-purple-900 leading-relaxed">
                            <span class="font-black">Support</span> — % of all transactions containing both X and Y.
                        </p>
                    </li>
                </ul>
            </div>

            {{-- ④ Quick Filters --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-purple-600 rounded-full"></span>
                    Quick Filters
                </h4>
                <div class="space-y-2">
                    <a href="{{ route('analysis.weka.allRules', ['sort'=>'lift','dir'=>'desc','high_impact'=>1]) }}"
                       class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-red-200 hover:bg-red-50 transition-all group ar-btn">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 bg-red-50 group-hover:bg-red-100 rounded-lg flex items-center justify-center text-sm transition-colors">🔥</span>
                            <span class="text-xs font-bold text-slate-700 group-hover:text-red-700">Top High Impact</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('analysis.weka.allRules', ['sort'=>'confidence','dir'=>'desc','confidence_filter'=>'high']) }}"
                       class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50 transition-all group ar-btn">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 bg-emerald-50 group-hover:bg-emerald-100 rounded-lg flex items-center justify-center text-sm transition-colors">✅</span>
                            <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-700">Highest Confidence</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('analysis.weka.allRules', ['sort'=>'support','dir'=>'desc','support_filter'=>'high']) }}"
                       class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-amber-200 hover:bg-amber-50 transition-all group ar-btn">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 bg-amber-50 group-hover:bg-amber-100 rounded-lg flex items-center justify-center text-sm transition-colors">📊</span>
                            <span class="text-xs font-bold text-slate-700 group-hover:text-amber-700">Highest Support</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <a href="{{ route('analysis.weka.allRules', ['sort'=>'lift','dir'=>'asc']) }}"
                       class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50 transition-all group ar-btn">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 bg-blue-50 group-hover:bg-blue-100 rounded-lg flex items-center justify-center text-sm transition-colors">📉</span>
                            <span class="text-xs font-bold text-slate-700 group-hover:text-blue-700">Lowest Lift (Explore)</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>{{-- /sidebar-sticky --}}
    </div>{{-- /right col --}}

</div>{{-- /grid --}}

{{-- ─────────────────────────────────────────────────────────────────── --}}
{{--  SECTION 4 : BOTTOM ACTION BAR                                      --}}
{{-- ─────────────────────────────────────────────────────────────────── --}}
<div>
    <p class="sec-label mb-4">Actions</p>
    <div class="action-bar rounded-2xl border border-slate-100 shadow-sm px-6 py-5">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            {{-- Left: export buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest shrink-0">Export:</span>

                {{-- Export CSV --}}
                <button onclick="alert('CSV export coming soon. Connect to /analysis/weka/all-rules/export?format=csv')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 rounded-xl text-xs font-bold shadow-sm ar-btn transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </button>

                {{-- Export PDF --}}
                <button onclick="alert('PDF export coming soon. Connect to /analysis/weka/all-rules/export?format=pdf')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:border-red-400 hover:bg-red-50 text-slate-700 hover:text-red-700 rounded-xl text-xs font-bold shadow-sm ar-btn transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Export PDF
                </button>
            </div>

            {{-- Right: navigation buttons --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Run New Analysis --}}
                <a href="{{ route('analysis.weka') }}#weka-engine"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-purple-200 hover:bg-purple-50 text-purple-700 rounded-xl text-xs font-bold shadow-sm ar-btn transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Run New Analysis
                </a>

                {{-- Back to Dashboard --}}
                <a href="{{ route('analysis.weka') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-extrabold shadow-sm ar-btn transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

</div>{{-- /max-w container --}}
</div>{{-- /page --}}

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{--  RULE DETAIL MODAL                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div id="ruleDetailBackdrop"
     class="ar-backdrop fixed inset-0 z-50 flex items-center justify-center bg-black/25"
     style="display:none !important;">
    <div class="ar-modal bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-gradient-to-br from-purple-600 to-violet-700 rounded-xl text-white shadow-md shadow-purple-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                </span>
                <div>
                    <h3 class="font-black text-slate-900 text-sm">Rule Details</h3>
                    <p class="text-[10px] text-slate-400 font-semibold">Association Rule Analysis</p>
                </div>
            </div>
            <button onclick="closeRuleModal()"
                    class="p-2 rounded-xl hover:bg-slate-100 transition-colors text-slate-400 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Rule expression --}}
        <div class="px-6 py-5 bg-gradient-to-br from-purple-50 to-violet-50 border-b border-purple-100">
            <p class="text-[10px] font-black text-purple-400 uppercase tracking-widest mb-2">Antecedent → Consequent</p>
            <div class="flex items-center gap-3 flex-wrap">
                <span id="modal-ant" class="px-3 py-1.5 bg-white rounded-xl border border-purple-100 font-black text-sm text-slate-800 shadow-sm"></span>
                <span class="inline-flex items-center justify-center w-7 h-7 bg-purple-600 rounded-full shrink-0">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
                <span id="modal-con" class="px-3 py-1.5 bg-purple-600 rounded-xl font-black text-sm text-white shadow-sm"></span>
            </div>
        </div>

        {{-- KPI grid --}}
        <div class="px-6 py-5 grid grid-cols-2 gap-3">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Support</p>
                <p id="modal-support" class="text-2xl font-black text-slate-800 tabular-nums"></p>
                <p class="text-[10px] text-slate-400 font-medium mt-0.5">Transaction coverage</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Confidence</p>
                <p id="modal-confidence" class="text-2xl font-black text-emerald-600 tabular-nums"></p>
                <p class="text-[10px] text-emerald-400 font-medium mt-0.5">Prediction reliability</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                <p class="text-[10px] font-black text-purple-400 uppercase tracking-widest mb-1">Lift</p>
                <p id="modal-lift" class="text-2xl font-black text-purple-700 tabular-nums"></p>
                <p class="text-[10px] text-purple-400 font-medium mt-0.5">Co-occurrence strength</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                <p class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1">Conviction</p>
                <p id="modal-conviction" class="text-2xl font-black text-amber-600 tabular-nums"></p>
                <p class="text-[10px] text-amber-400 font-medium mt-0.5">Rule dependence</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 pb-5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 uppercase">Strength:</span>
                <span id="modal-strength-badge" class="px-3 py-1 rounded-full text-[10px] font-black"></span>
            </div>
            <button onclick="closeRuleModal()"
                    class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-extrabold shadow-sm ar-btn">
                Close
            </button>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{--  CREATE BUNDLE FROM RULE MODAL                                      --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div id="bundleModalBackdrop"
     class="ar-backdrop fixed inset-0 z-50 flex items-center justify-center bg-black/30"
     style="display:none !important;">
    <div class="ar-modal bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-4 overflow-hidden">

        {{-- ── Modal Header ─────────────────────────────────────────── --}}
        <div class="flex items-center justify-between px-6 py-5 bg-gradient-to-r from-purple-600 to-violet-700 text-white">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-white/20 backdrop-blur-sm rounded-xl">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </span>
                <div>
                    <h3 class="font-extrabold text-base leading-tight">Create Bundle from Rule</h3>
                    <p class="text-white/70 text-[10px] font-semibold mt-0.5">Auto-filled from WEKA Apriori result</p>
                </div>
            </div>
            <button onclick="closeBundleModal()"
                    class="p-2 rounded-xl hover:bg-white/20 transition-colors">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Form ─────────────────────────────────────────────────── --}}
        <form method="POST" action="{{ route('promotions.store') }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="rule_id"     id="bm_rule_id"  value="">
            <input type="hidden" name="rule_ids[]"  id="bm_rule_id_arr" value="">
            <input type="hidden" name="start_date"  value="{{ now()->format('Y-m-d') }}">
            <input type="hidden" name="end_date"    value="{{ now()->addDays(30)->format('Y-m-d') }}">

            {{-- Rule summary card --}}
            <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl border border-purple-100 p-4">
                <p class="text-[10px] font-black text-purple-400 uppercase tracking-widest mb-3">Selected Rule</p>
                <div class="flex items-center gap-2 flex-wrap mb-4">
                    <span id="bm_ant" class="px-3 py-1.5 bg-white rounded-xl border border-purple-100 font-black text-sm text-slate-800 shadow-sm"></span>
                    <span class="inline-flex items-center justify-center w-7 h-7 bg-purple-600 rounded-full shrink-0">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </span>
                    <span id="bm_con" class="px-3 py-1.5 bg-purple-600 rounded-xl font-black text-sm text-white shadow-sm"></span>
                </div>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="bg-white rounded-xl p-2.5 border border-slate-100 shadow-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Support</p>
                        <p id="bm_support" class="text-sm font-black text-slate-800"></p>
                    </div>
                    <div class="bg-white rounded-xl p-2.5 border border-slate-100 shadow-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Confidence</p>
                        <p id="bm_confidence" class="text-sm font-black text-emerald-600"></p>
                    </div>
                    <div class="bg-white rounded-xl p-2.5 border border-slate-100 shadow-sm">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-0.5">Lift</p>
                        <p id="bm_lift" class="text-sm font-black text-purple-700"></p>
                    </div>
                </div>
            </div>

            {{-- Bundle Name --}}
            <div class="space-y-1.5">
                <label for="bm_promo_name" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Bundle Name <span class="text-red-400">*</span></label>
                <input type="text" name="promo_name" id="bm_promo_name" required
                       placeholder="e.g. Apriori Bundle: Item A + Item B"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-400 focus:bg-white transition-colors">
            </div>

            {{-- Discount Type + Value --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="bm_discount_type" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Discount Type</label>
                    <select name="discount_type" id="bm_discount_type"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-colors">
                        <option value="Percentage">Percentage (%)</option>
                        <option value="Fixed">Fixed Amount (RM)</option>
                        <option value="BOGO">Buy 1 Get 1</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label for="bm_discount_value" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Discount Value</label>
                    <div class="relative">
                        <input type="number" name="discount_value" id="bm_discount_value"
                               min="1" max="100" value="10"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-colors">
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="space-y-1.5">
                <label for="bm_description" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Description <span class="text-red-400">*</span></label>
                <textarea name="description" id="bm_description" required rows="3"
                          placeholder="Describe this bundle promotion…"
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-colors resize-none"></textarea>
            </div>

            {{-- Footer buttons --}}
            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <button type="button" onclick="closeBundleModal()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold ar-btn transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-extrabold shadow-sm ar-btn">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Bundle Promotion
                </button>
            </div>
        </form>

    </div>
</div>


<script>
// ── Rule Detail Modal ────────────────────────────────────────────────
const backdrop = document.getElementById('ruleDetailBackdrop');

function openRuleModal(rule) {
    document.getElementById('modal-ant').textContent        = rule.antecedent_code + ' (' + rule.antecedent + ')';
    document.getElementById('modal-con').textContent        = rule.consequent_code + ' (' + rule.consequent + ')';
    document.getElementById('modal-support').textContent    = rule.support + '%';
    document.getElementById('modal-confidence').textContent = rule.confidence + '%';
    document.getElementById('modal-lift').textContent       = rule.lift + '×';
    document.getElementById('modal-conviction').textContent = rule.conviction;

    const badge = document.getElementById('modal-strength-badge');
    const styleMap = {
        'High':   'bg-red-50 text-red-600 border border-red-200',
        'Medium': 'bg-orange-50 text-orange-600 border border-orange-200',
        'Low':    'bg-emerald-50 text-emerald-700 border border-emerald-200',
    };
    badge.className  = `px-3 py-1 rounded-full text-[10px] font-black ${styleMap[rule.strength] ?? 'bg-slate-100 text-slate-500'}`;
    badge.textContent = rule.strength;

    backdrop.style.removeProperty('display');
    requestAnimationFrame(() => requestAnimationFrame(() => backdrop.classList.add('open')));
}

function closeRuleModal() {
    backdrop.classList.remove('open');
    setTimeout(() => backdrop.style.setProperty('display', 'none', 'important'), 320);
}

backdrop.addEventListener('click', e => { if (e.target === backdrop) closeRuleModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRuleModal(); });

// ── Create Bundle Modal ─────────────────────────────────────────────
function openBundleModal(ruleId, ruleText, support, confidence, lift) {
    document.getElementById('bm_rule_id').value        = ruleId;
    document.getElementById('bm_rule_id_arr').value    = ruleId;

    const parts = ruleText.split('==>').map(s => s.trim());
    const ant   = parts[0] ?? '';
    const con   = parts[1] ?? '';

    // Populate rule preview
    document.getElementById('bm_ant').textContent        = ant;
    document.getElementById('bm_con').textContent        = con;
    document.getElementById('bm_support').textContent    = support + '%';
    document.getElementById('bm_confidence').textContent = confidence + '%';
    document.getElementById('bm_lift').textContent       = lift + '×';

    // Smart defaults
    document.getElementById('bm_promo_name').value   = 'Apriori Bundle: ' + ant + ' + ' + con;
    document.getElementById('bm_description').value  =
        'Special bundle: get a discount on ' + con + ' when purchased with ' + ant +
        '. Discovered by WEKA Apriori (Lift: ' + lift + '×).';

    // Open with Apple spring animation
    const bkdp = document.getElementById('bundleModalBackdrop');
    bkdp.style.removeProperty('display');
    requestAnimationFrame(() => requestAnimationFrame(() => bkdp.classList.add('open')));
}

function closeBundleModal() {
    const bkdp = document.getElementById('bundleModalBackdrop');
    bkdp.classList.remove('open');
    setTimeout(() => bkdp.style.setProperty('display','none','important'), 320);
}

document.getElementById('bundleModalBackdrop').addEventListener('click', function(e) {
    if (e.target === this) closeBundleModal();
});

// ── Search on Enter ───────────────────────────────────────────────────
document.getElementById('search-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') document.getElementById('filter-form').submit();
});

// ── ESC closes both modals ────────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (document.getElementById('bundleModalBackdrop').classList.contains('open')) closeBundleModal();
});
</script>
</x-app-layout>
