<x-app-layout>
    @php
        $getLetterColor = function($name) {
            $firstLetter = strtoupper(substr(trim($name), 0, 1));
            switch($firstLetter) {
                case 'A': case 'H': case 'O': case 'V':
                    return 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20';
                case 'B': case 'I': case 'P': case 'W':
                    return 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20';
                case 'C': case 'J': case 'Q': case 'X':
                    return 'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20';
                case 'D': case 'K': case 'R': case 'Y':
                    return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20';
                case 'E': case 'L': case 'S': case 'Z':
                    return 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400 border border-purple-100 dark:border-purple-500/20';
                case 'F': case 'M': case 'T':
                    return 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400 border border-cyan-100 dark:border-cyan-500/20';
                default:
                    return 'bg-pink-50 text-pink-600 dark:bg-pink-500/10 dark:text-pink-400 border border-pink-100 dark:border-pink-500/20';
            }
        };
    @endphp
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </span>
                {{ __('Items List') }}
            </h2>
            @if(Auth::guard('manager')->check())
                <a href="{{ route('products.create') }}" class="btn-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Add New Item') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search & Filters -->
            <div class="premium-card bg-white p-8 mb-8">
                <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    <div class="md:col-span-5">
                        <x-input-label for="search" :value="__('Find item')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="Search by name..." class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" />
                        </div>
                    </div>
                    <div class="md:col-span-4">
                        <x-input-label for="category" :value="__('Category')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                        <select id="category" name="category" class="w-full py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200">
                            <option value="">{{ __('All Collections') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3 flex gap-3">
                        <button type="submit" class="flex-1 bg-slate-800 dark:bg-white dark:text-slate-900 text-white font-bold text-xs uppercase tracking-widest py-3 rounded-full hover:opacity-90 transition-opacity">
                            {{ __('Show') }}
                        </button>
                        @if(request()->anyFilled(['search', 'category']))
                            <a href="{{ route('products.index') }}" class="p-3 bg-slate-100 dark:bg-slate-700 rounded-2xl text-slate-500 hover:bg-slate-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 rounded-2xl flex items-center gap-3 animate-fade-in">
                    <div class="p-1.5 bg-emerald-500 rounded-lg text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-600 tracking-tight">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Mobile Card Layout (Hidden on MD and up) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($products as $product)
                    <div class="premium-card bg-white dark:bg-slate-800 p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black {{ $getLetterColor($product->item_name) }}">
                                    {{ strtoupper(substr($product->item_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-md font-black text-slate-800 dark:text-white leading-tight uppercase flex flex-wrap items-center gap-2">
                                        {{ $product->item_name }}
                                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                                            {{ $product->item_code }}
                                        </a>
                                    </h4>
                                </div>
                            </div>
                            <span class="text-sm font-black text-indigo-600">RM{{ number_format($product->price, 2) }}</span>
                        </div>
                        
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                    {{ $product->categoryRelation->name ?? $product->category }}
                                </span>
                                <span class="px-2 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500">
                                    {{ $product->volume }}
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium italic">{{ Str::limit($product->description, 60) }}</p>
                        </div>

                        @if(Auth::guard('manager')->check())
                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-50 dark:border-slate-700/50">
                                <a href="{{ route('products.edit', $product) }}" class="p-2 text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Remove this item?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-500/10 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-12 text-center bg-white rounded-3xl border border-dashed border-slate-200 text-slate-400 text-xs italic">No items found.</div>
                @endforelse
            </div>

            <!-- Desktop Table Layout (Hidden on Mobile) -->
            <div class="hidden md:block premium-card overflow-hidden bg-white dark:bg-slate-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/50 dark:bg-slate-900/50">
                            <tr>
                                <th scope="col" class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Item Code</th>
                                <th scope="col" class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Name & Info</th>
                                <th scope="col" class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Volume</th>
                                <th scope="col" class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Price</th>
                                @if(Auth::guard('manager')->check())
                                    <th scope="col" class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($products as $product)
                                <tr class="group hover:bg-slate-50/30 dark:hover:bg-slate-900/30 transition-all duration-300">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-indigo-50 hover:bg-indigo-600 hover:text-white dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-600 dark:hover:text-white border border-indigo-100 dark:border-indigo-500/20 transition-all cursor-pointer">
                                            {{ $product->item_code }}
                                        </a>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black shadow-sm {{ $getLetterColor($product->item_name) }}">
                                                {{ strtoupper(substr($product->item_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-3 mb-1">
                                                    <div class="text-sm font-black text-slate-800 dark:text-white tracking-tight uppercase">{{ $product->item_name }}</div>
                                                    <span class="px-2 py-0.5 text-[8px] font-black uppercase tracking-widest rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                                                        {{ $product->categoryRelation->name ?? $product->category }}
                                                    </span>
                                                </div>
                                                <div class="text-[10px] text-slate-400 font-medium max-w-[250px] truncate">{{ $product->description ?: 'No description provided for this item' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">{{ $product->volume }}</span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span class="text-sm font-black text-slate-800 dark:text-white">RM {{ number_format($product->price, 2) }}</span>
                                    </td>
                                    @if(Auth::guard('manager')->check())
                                        <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3 transition-all duration-300">
                                                <a href="{{ route('products.edit', $product) }}" class="p-2 bg-indigo-600 text-white hover:bg-indigo-700 border border-transparent rounded-xl transition-all shadow-md shadow-indigo-100" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('Remove this item?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="p-2 bg-rose-600 text-white hover:bg-rose-700 border border-transparent rounded-xl transition-all shadow-md shadow-rose-100" title="Remove">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($products->hasPages())
                <div class="mt-8 px-8 py-4 bg-white/50 dark:bg-slate-800/50 glass-effect border border-white/20 rounded-2xl shadow-sm">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
