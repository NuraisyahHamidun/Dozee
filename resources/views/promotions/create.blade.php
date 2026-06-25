<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-lg text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                </span>
                {{ __('Event Promotion Builder') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="premium-card bg-white dark:bg-slate-800 p-6 md:p-10 border-none shadow-xl">
                <form method="POST" action="{{ route('promotions.store') }}" class="space-y-8">
                    @csrf
                    <input type="hidden" name="rule_id" value="{{ $rule_id }}">

                    <div class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Event Title -->
                            <div class="md:col-span-1">
                                <x-input-label for="promo_name" :value="__('Promotion Title')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                <input id="promo_name" name="promo_name" type="text" 
                                       value="{{ old('promo_name') }}" 
                                       class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner" 
                                       required autofocus placeholder="e.g. Raya Roadshow Special Bundle" />
                                <x-input-error class="mt-2" :messages="$errors->get('promo_name')" />
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-1">
                                @if(Auth::guard('manager')->check())
                                    <x-input-label for="status" :value="__('Launch Status')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <div class="relative">
                                        <select id="status" name="status" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner appearance-none cursor-pointer">
                                            <option value="Active" selected>Ready to Launch</option>
                                            <option value="Expired">Archived</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                @else
                                    <div class="h-full flex flex-col justify-end pb-1">
                                        <span class="px-4 py-4 bg-amber-50 dark:bg-amber-500/10 text-amber-600 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2 border border-amber-100 dark:border-amber-500/20">
                                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                            Status: Awaiting Manager Approval
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Multi-Bundle Selection -->
                        <div class="p-6 md:p-8 bg-indigo-50 dark:bg-indigo-500/5 rounded-3xl border border-indigo-100 dark:border-indigo-500/20">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                                <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-indigo-600 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    Select Event Bundles (Multiple Allowed)
                                </h3>
                                <div class="w-full md:w-64 relative">
                                    <input type="text" id="bundleSearch" placeholder="Search bundles..." class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all text-xs font-medium text-slate-700 dark:text-slate-200 shadow-sm" onkeyup="filterBundles()">
                                    <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 max-h-[320px] overflow-y-auto pr-2" id="bundleContainer" style="scrollbar-width: thin; scrollbar-color: #c7d2fe transparent;">
                                @foreach($allRules as $r)
                                    <label class="bundle-item relative flex items-start p-4 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border-2 border-transparent hover:border-indigo-200 transition-all cursor-pointer group" data-search="{{ strtolower(($products[$r->antecedent] ?? 'Item A') . ' ' . ($products[$r->consequent] ?? 'Item B') . ' ' . number_format($r->confidence * 100, 0) . '% ' . number_format($r->lift, 2) . 'x') }}">
                                        <div class="flex items-center h-5 mr-3">
                                            <input type="checkbox" name="rule_ids[]" value="{{ $r->rule_id }}" 
                                                   {{ $rule_id == $r->rule_id ? 'checked' : '' }}
                                                   class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                                                <span>{{ $products[$r->antecedent] ?? 'Item A' }}</span>
                                                <svg class="w-3 h-3 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                                <span>{{ $products[$r->consequent] ?? 'Item B' }}</span>
                                            </p>
                                            <p class="text-[9px] text-slate-400 mt-1 font-medium italic">Confidence: {{ number_format($r->confidence * 100, 0) }}% | Lift: {{ number_format($r->lift, 2) }}x</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-[9px] text-slate-400 mt-4 italic leading-relaxed">* You can select multiple product pairs to be included in this single event promotion.</p>
                        </div>

                        <!-- Strategy Details -->
                        <div>
                            <x-input-label for="description" :value="__('Promotion Strategy')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200 shadow-inner" 
                                      placeholder="Explain the goal of this event bundle...">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Availability Period -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <x-input-label for="start_date" :value="__('Promotion Start')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                <input id="start_date" name="start_date" type="date" 
                                       value="{{ old('start_date', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200 shadow-inner" 
                                       required onchange="updateEndDate(this.value)" />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('Promotion End (Auto 2 Days)')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                <input id="end_date" name="end_date" type="date" 
                                       value="{{ old('end_date', date('Y-m-d', strtotime('+2 days'))) }}" 
                                       class="w-full px-5 py-4 bg-slate-100 dark:bg-slate-700 border-none rounded-2xl text-sm font-medium text-slate-500 dark:text-slate-400 shadow-inner cursor-not-allowed" 
                                       required readonly />
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-10 border-t border-slate-50 dark:border-slate-700">
                        <a href="{{ route('promotions.index') }}" class="w-full sm:w-auto text-center text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors py-4">Cancel</a>
                        <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white font-black text-xs uppercase tracking-[0.2em] px-10 py-5 rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 dark:shadow-none transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Save Event Promotion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateEndDate(startDateVal) {
            if (!startDateVal) return;
            
            const startDate = new Date(startDateVal);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 2);
            
            // Format to YYYY-MM-DD
            const yyyy = endDate.getFullYear();
            const mm = String(endDate.getMonth() + 1).padStart(2, '0');
            const dd = String(endDate.getDate()).padStart(2, '0');
            
            document.getElementById('end_date').value = `${yyyy}-${mm}-${dd}`;
        }

        function filterBundles() {
            const searchInput = document.getElementById('bundleSearch').value.toLowerCase();
            const items = document.querySelectorAll('.bundle-item');
            
            items.forEach(item => {
                const searchText = item.getAttribute('data-search');
                if (searchText.includes(searchInput)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
    <style>
        /* Custom scrollbar for webkit */
        #bundleContainer::-webkit-scrollbar {
            width: 6px;
        }
        #bundleContainer::-webkit-scrollbar-track {
            background: transparent;
        }
        #bundleContainer::-webkit-scrollbar-thumb {
            background-color: #c7d2fe;
            border-radius: 20px;
        }
        .dark #bundleContainer::-webkit-scrollbar-thumb {
            background-color: #4f46e5;
        }
    </style>
    <!-- SweetAlert2 popup script -->
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
