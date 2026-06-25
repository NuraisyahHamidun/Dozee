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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </span>
                {{ __('Staff List') }}
            </h2>
            @if(Auth::guard('manager')->check())
                <a href="{{ route('accounts.create') }}" class="btn-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    {{ __('Add New Staff') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 rounded-2xl flex items-center gap-3 animate-fade-in shadow-sm">
                    <div class="p-1.5 bg-emerald-500 rounded-lg text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-emerald-600 tracking-tight">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Mobile Card Layout (Hidden on MD and up) -->
            <div class="grid grid-cols-1 gap-4 md:hidden">
                @forelse($salesmen as $salesman)
                    <div class="premium-card bg-white dark:bg-slate-800 p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black shadow-sm {{ $getLetterColor($salesman->name) }}">
                                    {{ strtoupper(substr($salesman->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="text-md font-black text-slate-800 dark:text-white leading-tight uppercase">{{ $salesman->name }}</h4>
                                    @if($salesman->staff_code)
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                            {{ $salesman->staff_code }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1 text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ $salesman->email }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $salesman->phone_number ?? '—' }}
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $salesman->address ?? '—' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-50 dark:border-slate-700/50">
                            <a href="{{ route('accounts.edit', $salesman->salesman_id) }}" class="p-2 text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('accounts.destroy', $salesman->salesman_id) }}" method="POST" onsubmit="return confirm('Remove access?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-500/10 rounded-xl">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-white rounded-3xl border border-dashed border-slate-200 text-slate-400 text-xs italic">No staff found.</div>
                @endforelse
            </div>

            <!-- Desktop Table Layout (Hidden on Mobile) -->
            <div class="hidden md:block premium-card overflow-hidden bg-white dark:bg-slate-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700">
                        <thead class="bg-slate-50/50 dark:bg-slate-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Staff Code</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Profile</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Name</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Phone</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Address</th>
                                <th scope="col" class="px-6 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Started On</th>
                                <th scope="col" class="px-6 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($salesmen as $salesman)
                                <tr class="group hover:bg-slate-50/30 dark:hover:bg-slate-900/30 transition-all duration-300">

                                    {{-- Staff Code --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if($salesman->staff_code)
                                            <span class="inline-block px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-widest bg-slate-100 text-slate-700 dark:bg-slate-900/50 dark:text-slate-300">
                                                {{ $salesman->staff_code }}
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400 italic">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Profile Picture --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if($salesman->profile_picture)
                                            <img src="{{ asset('storage/' . $salesman->profile_picture) }}"
                                                 alt="{{ $salesman->name }}"
                                                 class="w-11 h-11 rounded-2xl object-cover shadow-sm border border-slate-100 dark:border-slate-700">
                                        @else
                                            <div class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm shadow-sm {{ $getLetterColor($salesman->name) }}">
                                                {{ strtoupper(substr($salesman->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Name & Username --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="text-sm font-black text-slate-800 dark:text-white tracking-tight uppercase">{{ $salesman->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold tracking-widest uppercase mt-0.5">@ {{ $salesman->username }}</div>
                                    </td>

                                    {{-- Email --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300">
                                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ $salesman->email }}
                                        </div>
                                    </td>

                                    {{-- Phone --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        @if($salesman->phone_number)
                                            <div class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-300">
                                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                {{ $salesman->phone_number }}
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-slate-400 italic">—</span>
                                        @endif
                                    </td>

                                    {{-- Address --}}
                                    <td class="px-6 py-5">
                                        @if($salesman->address)
                                            <span class="text-sm font-medium text-slate-600 dark:text-slate-300 block max-w-[180px] truncate" title="{{ $salesman->address }}">
                                                {{ $salesman->address }}
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-slate-400 italic">—</span>
                                        @endif
                                    </td>

                                    {{-- Started On --}}
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $salesman->created_at->format('d M Y') }}</span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3 transition-all duration-300">
                                            <a href="{{ route('accounts.edit', $salesman->salesman_id) }}" class="p-2 bg-indigo-600 text-white hover:bg-indigo-700 border border-transparent rounded-xl transition-all shadow-md shadow-indigo-100" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('accounts.destroy', $salesman->salesman_id) }}" method="POST" class="delete-staff-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="delete-staff-btn p-2 bg-rose-600 text-white hover:bg-rose-700 border border-transparent rounded-xl transition-all shadow-md shadow-rose-100" title="Remove"
                                                    data-name="{{ $salesman->name }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 Delete Confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-staff-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const staffName = this.getAttribute('data-name');
                    const form = this.closest('.delete-staff-form');
                    Swal.fire({
                        title: 'Remove Staff?',
                        html: `<p style="font-size:0.85rem;color:#64748b;">You are about to remove <strong>${staffName}</strong>.<br>This action <strong>cannot be undone</strong>.</p>`,
                        icon: 'warning',
                        iconColor: '#ef4444',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Yes, Remove',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'rounded-xl font-bold text-xs uppercase tracking-widest px-6 py-3',
                            cancelButton: 'rounded-xl font-bold text-xs uppercase tracking-widest px-6 py-3',
                        },
                        buttonsStyling: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
