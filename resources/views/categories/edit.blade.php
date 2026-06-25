<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
            <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </span>
            {{ __('Edit Category') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="premium-card bg-white p-10">
                <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" :value="__('Category Name')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                        <input id="name" name="name" type="text" 
                               class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200" 
                               value="{{ old('name', $category->name) }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        <p class="mt-2 text-xs text-slate-400 font-medium leading-relaxed">Updating this name will reflect across all products assigned to this category.</p>
                    </div>

                    <div class="flex items-center justify-end gap-6 pt-8 border-t border-slate-50">
                        <a href="{{ route('categories.index') }}" class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Cancel</a>
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
