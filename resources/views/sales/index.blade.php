<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </span>
                {{ __('Sales Records') }}
            </h2>
            @if(Auth::guard('salesmen')->check())
                <a href="{{ route('sales.create') }}" class="btn-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Add New Sale') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filters -->
            <div class="premium-card bg-white p-8 mb-8">
                <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    <div class="md:col-span-10">
                        <x-input-label for="search" :value="__('Find a sale')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="Search by Sale ID or Salesmen Name..." class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" />
                        </div>
                    </div>
                    <div class="md:col-span-2 flex gap-3">
                        <button type="submit" class="flex-1 bg-slate-800 dark:bg-white dark:text-slate-900 text-white font-bold text-xs uppercase tracking-widest py-3 rounded-full hover:opacity-90 transition-opacity">
                            {{ __('Search') }}
                        </button>
                        @if(request()->filled('search'))
                            <a href="{{ route('sales.index') }}" class="p-3 bg-slate-100 dark:bg-slate-700 rounded-2xl text-slate-500 hover:bg-slate-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>



            <!-- Mobile Card Layout (Hidden on MD and up) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($sales as $sale)
                    <div class="premium-card bg-white dark:bg-slate-800 p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">TXN-{{ str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-sm font-black text-emerald-600">RM {{ number_format($sale->total_amount, 2) }}</span>
                                @if($sale->status === 'Approved')
                                    <span class="px-2 py-0.5 text-[8px] font-black uppercase tracking-widest rounded bg-emerald-50 text-emerald-600">Approved</span>
                                @elseif($sale->status === 'Rejected')
                                    <span class="px-2 py-0.5 text-[8px] font-black uppercase tracking-widest rounded bg-rose-50 text-rose-600">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 text-[8px] font-black uppercase tracking-widest rounded bg-amber-50 text-amber-600">Pending</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-2 pt-2 border-t border-slate-50 dark:border-slate-700/50">
                            <div class="text-[10px] text-slate-400"><span class="font-bold uppercase">Date Create:</span> {{ $sale->date_create ? $sale->date_create->format('d M Y, H:i A') : '-' }}</div>
                            <div class="text-[10px] text-slate-400"><span class="font-bold uppercase">Modified:</span> {{ $sale->date_modifier ? $sale->date_modifier->format('d M Y, H:i A') : '-' }}</div>
                            <div class="text-[10px] text-slate-400"><span class="font-bold uppercase">Verified:</span> {{ $sale->date_verify ? $sale->date_verify->format('d M Y, H:i A') : '-' }}</div>
                            @if(Auth::guard('manager')->check())
                                <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tight italic">Sold by {{ $sale->salesmen->name }}</div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-50 dark:border-slate-700/50">
                            <span class="px-2 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500">
                                {{ $sale->saleItems->count() }} items
                            </span>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('sales.show', $sale) }}" class="p-2 text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl" title="View Receipt">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @if(Auth::guard('manager')->check())
                                    @if($sale->status === 'Pending')
                                        <form action="{{ route('sales.approve', $sale) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="p-2 text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl" title="Approve">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </button>
                                        </form>
                                        <form action="{{ route('sales.reject', $sale) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject this sale and return items to stock?');">
                                            @csrf
                                            <button type="submit" class="p-2 text-amber-600 bg-amber-50 dark:bg-amber-500/10 rounded-xl" title="Reject">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('sales.edit', $sale) }}" class="p-2 text-violet-600 bg-violet-50 dark:bg-violet-500/10 rounded-xl" title="Edit Record">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Delete this sale record?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-500/10 rounded-xl" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @else
                                    @if($sale->status === 'Approved')
                                        <a href="{{ route('sales.edit', $sale) }}" class="p-2 text-violet-600 bg-violet-50 dark:bg-violet-500/10 rounded-xl" title="Edit Record">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    @else
                                        <button type="button" class="p-2 text-slate-300 bg-slate-50 dark:bg-slate-800 rounded-xl cursor-not-allowed" title="Editing requires Manager approval" disabled>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-white rounded-3xl border border-dashed border-slate-200 text-slate-400 text-xs italic">No transactions found.</div>
                @endforelse
            </div>

            <!-- Desktop Table Layout (Hidden on Mobile) -->
            <div class="hidden md:block premium-card overflow-hidden bg-white/50 dark:bg-slate-800/50 glass-effect border border-white/20">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/50 dark:bg-slate-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Sale ID</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Ante Create</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Date Modifier (Salesmen)</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Date Verify (Manager)</th>
                                @if(Auth::guard('manager')->check())
                                    <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Sold by</th>
                                @endif
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Items</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Price</th>
                                <th scope="col" class="px-6 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($sales as $sale)
                                <tr class="group hover:bg-slate-50/30 dark:hover:bg-slate-900/30 transition-all duration-300">
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                            <span class="text-xs font-black text-slate-800 dark:text-white tracking-widest uppercase">TXN-{{ str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        @if($sale->status === 'Approved')
                                            <span class="px-2.5 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                                Approved
                                            </span>
                                        @elseif($sale->status === 'Rejected')
                                            <span class="px-2.5 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                                                Rejected
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        @if($sale->date_create)
                                            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $sale->date_create->format('d M Y') }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium tracking-tight uppercase">{{ $sale->date_create->format('H:i A') }}</div>
                                        @else
                                            <span class="text-slate-400 text-xs font-bold">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        @if($sale->date_modifier)
                                            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $sale->date_modifier->format('d M Y') }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium tracking-tight uppercase">{{ $sale->date_modifier->format('H:i A') }}</div>
                                        @else
                                            <span class="text-slate-400 text-xs font-bold">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        @if($sale->date_verify)
                                            <div class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $sale->date_verify->format('d M Y') }}</div>
                                            <div class="text-[10px] text-slate-400 font-medium tracking-tight uppercase">{{ $sale->date_verify->format('H:i A') }}</div>
                                        @else
                                            <span class="text-slate-400 text-xs font-bold">-</span>
                                        @endif
                                    </td>
                                    @if(Auth::guard('manager')->check())
                                        <td class="px-6 py-6 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-xs font-black text-indigo-500">
                                                    {{ substr($sale->salesmen->name, 0, 1) }}
                                                </div>
                                                <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $sale->salesmen->name }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <span class="px-3 py-1.5 inline-flex text-[10px] font-black uppercase tracking-widest rounded-xl bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                            {{ $sale->saleItems->count() }} items
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap">
                                        <span class="text-sm font-black text-emerald-600 tracking-tight">RM {{ number_format($sale->total_amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-6 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3 transition-all duration-300">
                                            <a href="{{ route('sales.show', $sale) }}" class="p-2 bg-indigo-600 text-white hover:bg-indigo-700 border border-transparent rounded-xl transition-all shadow-md shadow-indigo-100" title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            
                                            @if(Auth::guard('manager')->check())
                                                @if($sale->status === 'Pending')
                                                    <form action="{{ route('sales.approve', $sale) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="p-2 bg-emerald-600 text-white hover:bg-emerald-700 border border-transparent rounded-xl transition-all shadow-md shadow-emerald-100" title="Approve Sale">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('sales.reject', $sale) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject this sale and return items to stock?');">
                                                        @csrf
                                                        <button type="submit" class="p-2 bg-amber-600 text-white hover:bg-amber-700 border border-transparent rounded-xl transition-all shadow-md shadow-amber-100" title="Reject Sale">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <a href="{{ route('sales.edit', $sale) }}" class="p-2 bg-violet-600 text-white hover:bg-violet-700 border border-transparent rounded-xl transition-all shadow-md shadow-violet-100" title="Edit Record">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                
                                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this sale record?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 bg-rose-600 text-white hover:bg-rose-700 border border-transparent rounded-xl transition-all shadow-md shadow-rose-100" title="Remove">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @else
                                                @if($sale->status === 'Approved')
                                                    <a href="{{ route('sales.edit', $sale) }}" class="p-2 bg-violet-600 text-white hover:bg-violet-700 border border-transparent rounded-xl transition-all shadow-md shadow-violet-100" title="Edit Record">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </a>
                                                @else
                                                    <button type="button" class="p-2 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-xl cursor-not-allowed" title="Editing requires Manager approval" disabled>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($sales->hasPages())
                    <div class="px-8 py-4 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
