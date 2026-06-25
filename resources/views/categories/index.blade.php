<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </span>
                {{ __('Manage Categories') }}
            </h2>
            <a href="{{ route('categories.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                {{ __('Add Category') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages handled by SweetAlert2 -->

            <!-- Mobile Card Layout (Hidden on MD and up) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($categories as $category)
                    <div class="premium-card bg-white dark:bg-slate-800 p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <h4 class="text-md font-black text-slate-800 dark:text-white">{{ $category->name }}</h4>
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 text-[10px] font-bold">
                                {{ $category->products_count }} {{ Str::plural('Item', $category->products_count) }}
                            </span>
                        </div>
                        
                        @if(Auth::guard('manager')->check())
                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-50 dark:border-slate-700/50">
                                <a href="{{ route('categories.edit', $category) }}" class="p-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-rose-50 dark:bg-rose-500/10 text-rose-600 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-12 text-center bg-white rounded-3xl border border-dashed border-slate-200 text-slate-400 text-xs italic">No categories found.</div>
                @endforelse
            </div>

            <!-- Desktop Table Layout (Hidden on Mobile) -->
            <div class="hidden md:block premium-card bg-white dark:bg-slate-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-700">
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">#</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Category Name</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-center">Items Count</th>
                                @if(Auth::guard('manager')->check())
                                    <th class="px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700">
                            @foreach($categories as $category)
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-900/50 transition-colors duration-300">
                                <td class="px-8 py-6">
                                    <span class="text-xs font-black text-slate-400">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</span>
                                </td>
                                <td class="px-8 py-6 text-sm font-bold text-slate-700 dark:text-slate-200">{{ $category->name }}</td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 text-[10px] font-bold">
                                        {{ $category->products_count }} {{ Str::plural('Item', $category->products_count) }}
                                    </span>
                                </td>
                                @if(Auth::guard('manager')->check())
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-3 transition-all duration-300">
                                            <a href="{{ route('categories.edit', $category) }}" 
                                               class="p-2 bg-indigo-600 text-white hover:bg-indigo-700 border border-transparent rounded-xl transition-all shadow-md shadow-indigo-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 bg-rose-600 text-white hover:bg-rose-700 border border-transparent rounded-xl transition-all shadow-md shadow-rose-100">
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

            @if($categories->hasPages())
                <div class="mt-6 px-8 py-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- SweetAlert2 popup script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#4f46e5',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#e11d48'
                });
            @endif
            
            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
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
