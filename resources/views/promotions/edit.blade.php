<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-lg text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </span>
                {{ __('Refine Promotion') }}: {{ $promotion->promo_name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Main Form -->
                <div class="lg:col-span-8">
                    <div class="premium-card bg-white dark:bg-slate-800 glass-effect p-8 border border-white/20">
                        <form method="POST" action="{{ route('promotions.update', $promotion) }}" class="space-y-8">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Campaign Name -->
                                    <div class="md:col-span-1">
                                        <x-input-label for="promo_name" :value="__('Promotion Identity')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                        <input id="promo_name" name="promo_name" type="text" 
                                               value="{{ old('promo_name', $promotion->promo_name) }}" 
                                               class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner" 
                                               required />
                                        <x-input-error class="mt-2" :messages="$errors->get('promo_name')" />
                                    </div>

                                    <!-- Status -->
                                    <div class="md:col-span-1">
                                        <x-input-label for="status" :value="__('Operational Status')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                        <select id="status" name="status" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner appearance-none cursor-pointer">
                                            <option value="Pending" {{ old('status', $promotion->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Active" {{ old('status', $promotion->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                            <option value="Expired" {{ old('status', $promotion->status) == 'Expired' ? 'selected' : '' }}>Expired</option>
                                            <option value="Rejected" {{ old('status', $promotion->status) == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <x-input-label for="description" :value="__('Strategy Refinement')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <textarea id="description" name="description" rows="4" 
                                              class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200 shadow-inner">{{ old('description', $promotion->description) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                                </div>

                                <!-- Dates Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="start_date" :value="__('Deployment Date')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $promotion->start_date) }}" 
                                               class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200 shadow-inner" 
                                               required />
                                        <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                                    </div>
                                    <div>
                                        <x-input-label for="end_date" :value="__('Expiration Date')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $promotion->end_date) }}" 
                                               class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200 shadow-inner" 
                                               required />
                                        <x-input-error class="mt-2" :messages="$errors->get('end_date')" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100 dark:border-slate-700/50">
                                <a href="{{ route('promotions.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Discard Changes</a>
                                <button type="submit" class="bg-indigo-600 text-white font-black text-xs uppercase tracking-[0.2em] px-8 py-4 rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all">
                                    Commit Refinements
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Guidance Sidebar -->
                <div class="lg:col-span-4 space-y-8">
                    @if($promotion->analysis)
                        <div class="premium-card p-8 bg-indigo-600 text-white shadow-xl shadow-indigo-100 dark:shadow-none">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-200 mb-6 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Associated Data Rule
                            </h4>
                            <div class="space-y-4">
                                <div class="p-4 bg-white/10 rounded-2xl border border-white/10">
                                    <span class="block text-[8px] font-black uppercase tracking-widest text-indigo-200 mb-1">Market Connection</span>
                                    <p class="text-sm font-black">{{ $promotion->analysis->antecedent }} <span class="mx-1 opacity-50">→</span> {{ $promotion->analysis->consequent }}</p>
                                </div>
                                <div class="p-4 bg-white/10 rounded-2xl border border-white/10">
                                    <span class="block text-[8px] font-black uppercase tracking-widest text-indigo-200 mb-1">Confidence Score</span>
                                    <p class="text-sm font-black">{{ number_format($promotion->analysis->confidence * 100, 1) }}% Factor</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="premium-card p-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Promotion Analytics</h4>
                        <p class="text-xs font-medium text-slate-500 leading-relaxed mb-4">
                            Last modified on {{ $promotion->updated_at->format('M d, Y') }} at {{ $promotion->updated_at->format('H:i') }}.
                        </p>
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/50">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 text-center italic">Evolutionary Growth System</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
