<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </span>
                {{ __('Edit Item') }}: {{ $product->item_name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="premium-card bg-white p-10">
                <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Item Code -->
                            <div>
                                <x-input-label for="item_code" :value="__('Item Code')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                <input id="item_code" name="item_code" type="text" readonly
                                       class="w-full px-5 py-4 bg-slate-100 dark:bg-slate-800 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-semibold text-slate-500 cursor-not-allowed" 
                                       value="{{ old('item_code', $product->item_code) }}" />
                                <x-input-error class="mt-2" :messages="$errors->get('item_code')" />

                                <!-- Advanced Settings details block -->
                                <details class="mt-3 group border border-slate-100 dark:border-slate-850 rounded-2xl overflow-hidden bg-slate-50/50 dark:bg-slate-900/30" {{ old('override_item_code') ? 'open' : '' }}>
                                    <summary class="flex items-center justify-between px-5 py-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors list-none select-none">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-3 h-3 text-slate-400 group-open:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                            Advanced Settings
                                        </span>
                                    </summary>
                                    <div class="p-5 border-t border-slate-100 dark:border-slate-800 space-y-4">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" id="override_item_code" name="override_item_code" value="1" {{ old('override_item_code') ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                                            <label for="override_item_code" class="text-xs font-semibold text-slate-600 dark:text-slate-400 cursor-pointer select-none">Manually override Item Code</label>
                                        </div>
                                    </div>
                                </details>
                            </div>

                            <!-- Name -->
                            <div>
                                <x-input-label for="item_name" :value="__('Item Name')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                <input id="item_name" name="item_name" type="text" 
                                       class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" 
                                       value="{{ old('item_name', $product->item_name) }}" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('item_name')" />
                            </div>

                            <!-- Volume -->
                            <div>
                                <x-input-label for="volume" :value="__('Volume (e.g. 500ml, 1L)')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                <input id="volume" name="volume" type="text" 
                                       class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" 
                                       value="{{ old('volume', $product->volume) }}" required placeholder="e.g. 500ml" />
                                <x-input-error class="mt-2" :messages="$errors->get('volume')" />
                            </div>

                            <!-- Category -->
                            <div>
                                <x-input-label for="category_id" :value="__('Category')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                <select id="category_id" name="category_id" 
                                        class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200 cursor-pointer" 
                                        required>
                                    <option value="" disabled>{{ __('Select Category') }}</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Price -->
                            <div>
                                <x-input-label for="price" :value="__('Price (RM)')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                        <span class="text-slate-400 font-bold text-sm">RM</span>
                                    </div>
                                    <input id="price" name="price" type="number" step="0.01" min="0"
                                           class="w-full pl-12 pr-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" 
                                           value="{{ old('price', $product->price) }}" required />
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('price')" />
                            </div>

                            <!-- Stock Quantity -->
                            <div>
                                <x-input-label for="stock_qty" :value="__('Stock Quantity')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                <input id="stock_qty" name="stock_qty" type="number" min="0"
                                       class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" 
                                       value="{{ old('stock_qty', $product->stock_qty) }}" required />
                                <x-input-error class="mt-2" :messages="$errors->get('stock_qty')" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                            <textarea id="description" name="description" rows="4" 
                                      class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" 
                                      placeholder="Update item description...">{{ old('description', $product->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-8 border-t border-slate-50">
                        <a href="{{ route('products.index') }}" class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Cancel</a>
                        <button type="submit" class="btn-primary px-10 py-4">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- SweetAlert2 popup script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Override Item Code Toggle
            const toggle = document.getElementById('override_item_code');
            const input = document.getElementById('item_code');
            if (toggle && input) {
                const handleToggle = function() {
                    if (toggle.checked) {
                        input.removeAttribute('readonly');
                        input.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'cursor-not-allowed', 'text-slate-500');
                        input.classList.add('bg-slate-50', 'dark:bg-slate-900', 'text-slate-700', 'dark:text-slate-200');
                        input.focus();
                    } else {
                        input.setAttribute('readonly', true);
                        input.classList.add('bg-slate-100', 'dark:bg-slate-800', 'cursor-not-allowed', 'text-slate-500');
                        input.classList.remove('bg-slate-50', 'dark:bg-slate-900', 'text-slate-700', 'dark:text-slate-200');
                        input.value = "{{ old('item_code', $product->item_code) }}";
                    }
                };
                toggle.addEventListener('change', handleToggle);
                // Trigger on load in case old input has values
                handleToggle();
            }

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
