<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight">
                    {{ __('Promotion Management') }}
                </h2>
                <p class="text-xs text-slate-400 font-medium mt-1">Track and optimize your active marketing promotions.</p>
            </div>
            <div class="flex-1 max-w-md hidden lg:block mx-8">
                <form action="{{ route('promotions.index') }}" method="GET" class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search promotions..." class="w-full pl-11 pr-10 py-2.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-xs font-medium text-slate-600 dark:text-slate-300">
                    @if(request('search'))
                        <a href="{{ route('promotions.index') }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>
            <div class="flex items-center gap-4">
                @if(Auth::guard('manager')->check() || Auth::guard('salesmen')->check())
                    <a href="{{ route('promotions.create') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-indigo-100 dark:shadow-none flex items-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ Auth::guard('manager')->check() ? __('Create New Promotion') : __('Suggest New Promotion') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- KPI Statistics (Already responsive) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Active Promotions -->
                <div class="premium-card bg-white dark:bg-slate-800 p-6 relative group overflow-hidden">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Active Promotions</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums">{{ $stats['active_count'] }}</h3>
                        <span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-wider">+12%</span>
                    </div>
                </div>

                <!-- Avg Confidence -->
                <div class="premium-card bg-white dark:bg-slate-800 p-6 relative group overflow-hidden">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Avg Rule Strength</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums">{{ round($stats['avg_confidence'] * 100, 1) }}%</h3>
                        <span class="px-2 py-1 bg-slate-50 text-slate-400 rounded-lg text-[10px] font-black uppercase tracking-wider">Flat</span>
                    </div>
                </div>

                <!-- Pending Approval -->
                <div class="premium-card bg-white dark:bg-slate-800 p-6 relative group overflow-hidden">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Pending Approval</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums">{{ str_pad($stats['pending_count'], 2, '0', STR_PAD_LEFT) }}</h3>
                        @if($stats['pending_count'] > 0)
                            <span class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-black uppercase tracking-wider">Urgent</span>
                        @endif
                    </div>
                </div>

                <!-- Discovery Items -->
                <div class="premium-card bg-white dark:bg-slate-800 p-6 relative group overflow-hidden">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Discovery Items</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-4xl font-black text-slate-800 dark:text-white tabular-nums">{{ $stats['total_count'] }}</h3>
                        <span class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-wider">+22%</span>
                    </div>
                </div>
            </div>

            <!-- Mobile Card Layout (Hidden on MD and up) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($promotions as $promotion)
                    <div class="premium-card bg-white dark:bg-slate-800 p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">PR-{{ str_pad($promotion->promo_id, 4, '0', STR_PAD_LEFT) }}</span>
                                <h4 class="text-lg font-black text-slate-800 dark:text-white mt-1">{{ $promotion->promo_name }}</h4>
                            </div>
                            @php
                                $statusStyle = [
                                    'Active' => 'bg-emerald-50 text-emerald-600',
                                    'Pending' => 'bg-amber-50 text-amber-600',
                                    'Rejected' => 'bg-rose-50 text-rose-600',
                                    'Expired' => 'bg-slate-50 text-slate-500',
                                ][$promotion->status] ?? 'bg-slate-50 text-slate-500';
                            @endphp
                            <span class="px-3 py-1 text-[9px] font-black uppercase tracking-widest rounded-full {{ $statusStyle }}">
                                {{ $promotion->status === 'Pending' ? 'Draft' : $promotion->status }}
                            </span>
                        </div>
                        
                        <p class="text-xs text-slate-500 italic">{{ $promotion->description }}</p>

                        <div class="flex items-center gap-2 py-3 border-y border-slate-50 dark:border-slate-700/50">
                            <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">
                                @if($promotion->associationRules->count() > 1)
                                    {{ $promotion->associationRules->count() }} Strategy Bundles
                                @elseif($promotion->analysis)
                                    Bundle: {{ $promotion->analysis->antecedentProduct->item_name ?? 'Item' }} + {{ $promotion->analysis->consequentProduct->item_name ?? 'Item' }}
                                @else
                                    None
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[10px] font-medium text-slate-400">{{ $promotion->start_date }} → {{ $promotion->end_date }}</span>
                            <div class="flex items-center gap-3">
                                @if(Auth::guard('manager')->check() && $promotion->status === 'Pending')
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('promotions.approve', $promotion) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 font-black text-[10px] uppercase tracking-widest">
                                                {{ $promotion->salesmen_id ? __('Approve') : __('Activate') }}
                                            </button>
                                        </form>
                                        @if($promotion->salesmen_id)
                                            <form action="{{ route('promotions.reject', $promotion) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-rose-600 font-black text-[10px] uppercase tracking-widest ml-2" onclick="return confirm('Reject this proposal?')">Reject</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                                @if(Auth::guard('manager')->check())
                                    <a href="{{ route('promotions.edit', $promotion) }}" class="text-indigo-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    <form action="{{ route('promotions.destroy', $promotion) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-rose-600" onclick="return confirm('Delete this promotion?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-white rounded-3xl border border-dashed border-slate-200 text-slate-400 text-xs italic">No active promotions found.</div>
                @endforelse
            </div>

            <!-- Mobile Pagination Links -->
            <div class="mt-4 md:hidden">
                {{ $promotions->links() }}
            </div>

            <!-- Desktop Table Layout (Hidden on Mobile) -->
            <div class="hidden md:block premium-card bg-white dark:bg-slate-800 overflow-hidden">
                <div class="p-6 border-b border-slate-50 dark:border-slate-700/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white heading-font">Promotion Records</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 dark:bg-slate-900/50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Identity</th>
                                <th class="px-6 py-4">Event Details</th>
                                <th class="px-6 py-4">Strategy Bundles</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                @if(Auth::guard('manager')->check())
                                    <th class="px-6 py-4 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                            @forelse($promotions as $promotion)
                                <tr class="hover:bg-slate-50/30 dark:hover:bg-slate-900/30 transition-colors">
                                    <td class="px-6 py-6 whitespace-nowrap text-[11px] font-bold text-slate-400 uppercase tracking-tighter">PR-{{ str_pad($promotion->promo_id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-6 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-800 dark:text-white leading-tight">{{ $promotion->promo_name }}</span>
                                            <span class="text-[9px] text-slate-400 mt-0.5 line-clamp-1 italic">{{ $promotion->description }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        @if($promotion->associationRules->count() > 1 || $promotion->analysis)
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg dark:bg-indigo-500/10 dark:text-indigo-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                </div>
                                                <span class="text-[11px] font-black text-slate-700 dark:text-slate-200">
                                                    @if($promotion->associationRules->count() > 1)
                                                        {{ $promotion->associationRules->count() }} Strategy Bundles
                                                    @else
                                                        Bundle: {{ $promotion->analysis->antecedentProduct->item_name ?? 'Item' }} + {{ $promotion->analysis->consequentProduct->item_name ?? 'Item' }}
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg dark:bg-rose-500/10 dark:text-rose-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                </div>
                                                <span class="text-[11px] font-black text-slate-800 dark:text-slate-200">
                                                    None
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6 text-center">
                                        @php
                                            $statusStyle = [
                                                'Active' => 'bg-emerald-50 text-emerald-600',
                                                'Pending' => 'bg-amber-50 text-amber-600',
                                                'Rejected' => 'bg-rose-50 text-rose-600',
                                                'Expired' => 'bg-slate-50 text-slate-500',
                                            ][$promotion->status] ?? 'bg-slate-50 text-slate-500';
                                        @endphp
                                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full {{ $statusStyle }}">
                                            {{ $promotion->status === 'Pending' ? 'Draft' : $promotion->status }}
                                        </span>
                                    </td>
                                    @if(Auth::guard('manager')->check())
                                        <td class="px-6 py-6 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                 @if($promotion->status === 'Pending')
                                                     <div class="flex items-center gap-1.5">
                                                         <form action="{{ route('promotions.approve', $promotion) }}" method="POST" class="inline">
                                                             @csrf
                                                             <button type="submit" class="px-3 py-1.5 bg-emerald-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-100">
                                                                 {{ $promotion->salesmen_id ? __('Approve') : __('Activate') }}
                                                             </button>
                                                         </form>
                                                         @if($promotion->salesmen_id)
                                                             <form action="{{ route('promotions.reject', $promotion) }}" method="POST" class="inline">
                                                                 @csrf
                                                                 <button type="submit" class="px-3 py-1.5 bg-rose-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all shadow-lg shadow-rose-100" onclick="return confirm('Reject this proposal?')">Reject</button>
                                                             </form>
                                                         @endif
                                                     </div>
                                                 @endif
                                                <a href="{{ route('promotions.edit', $promotion) }}" class="p-2 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl transition-all border border-transparent shadow-md shadow-indigo-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                                <form action="{{ route('promotions.destroy', $promotion) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 bg-rose-600 text-white hover:bg-rose-700 rounded-xl transition-all border border-transparent shadow-md shadow-rose-100" onclick="return confirm('Delete this promotion?')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-xs italic">No promotion records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-50 dark:border-slate-700/50">
                    {{ $promotions->links() }}
                </div>
            </div>


        </div>
    </div>
</x-app-layout>

