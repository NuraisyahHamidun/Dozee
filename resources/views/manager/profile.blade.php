@php
    $user = $manager;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-lg text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </span>
                @if(Auth::guard('manager')->check())
                    {{ __('Manager Profile') }}
                @else
                    {{ __('Staff Profile') }}
                @endif
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Profile Identity Sidebar -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="premium-card overflow-hidden relative group shadow-2xl shadow-purple-300 bg-gradient-to-br from-purple-600 to-purple-400">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path d="M0 0 L100 0 L100 100 L0 100 Z" fill="url(#mgr-grid)"></path>
                                <defs>
                                    <pattern id="mgr-grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"></path>
                                    </pattern>
                                </defs>
                            </svg>
                        </div>
                        
                        <div class="relative p-8 flex flex-col items-center text-center">
                            @if ($manager->profile_picture)
                                <img src="{{ asset('storage/' . $manager->profile_picture) }}" alt="Profile Picture" class="w-36 h-36 rounded-3xl object-cover shadow-xl mb-6 group-hover:scale-105 transition-transform duration-500 border border-white/30">
                            @else
                                <div class="w-36 h-36 rounded-3xl flex items-center justify-center text-5xl font-black text-white shadow-xl mb-6 group-hover:scale-105 transition-transform duration-500" style="background-color: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);">
                                    {{ strtoupper(substr($manager->name, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="text-xl font-black text-white tracking-tight mb-6">{{ $manager->name }}</h3>
                            
                            <div class="w-full pt-6" style="border-top: 1px solid rgba(255,255,255,0.15);">
                                <div class="text-center">
                                    <span class="block font-black text-2xl leading-none text-white">{{ $manager->salesmen()->count() }}</span>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-purple-200">Staff</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info Card -->
                    <div class="premium-card bg-white dark:bg-slate-800 p-6 space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Institutional Access</h4>
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold">{{ $manager->email }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="text-xs font-bold">{{ $manager->phone_number ?? 'Tiada nombor telefon' }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold italic">Enrolled Since {{ $manager->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Settings Forms Area -->
                <div class="lg:col-span-8 space-y-8">
                    @if(session('success'))
                        <div class="premium-card bg-emerald-500 text-white p-4 flex items-center gap-3 animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Account Details Card -->
                    <div class="premium-card bg-white dark:bg-slate-800 p-8 shadow-xl border-none">
                        <form method="POST" action="{{ route('manager.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-6">
                                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                                    Administrative Credentials
                                </h3>

                                <!-- Staff Code (Read-Only) -->
                                <div class="form-group">
                                    <x-input-label for="staff_code" :value="__('Staff Code')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <input id="staff_code" type="text" 
                                           class="form-control w-full px-5 py-3.5 bg-slate-100 dark:bg-slate-900/50 border-none rounded-2xl text-sm font-bold text-slate-500 dark:text-slate-400 shadow-inner cursor-not-allowed" 
                                           value="{{ $user->staff_code }}" readonly />
                                </div>

                                <!-- Row 1: Full Identity | Username -->
                                <!-- Full Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Full Name')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <input id="name" name="name" type="text"
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner"
                                           value="{{ old('name', $manager->name) }}" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Username (auto-generated, read-only) -->
                                <div>
                                    <x-input-label for="username" :value="__('Username')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <input id="username" name="username" type="text" readonly
                                           class="w-full px-5 py-3.5 bg-slate-100 dark:bg-slate-800 border-none rounded-2xl text-sm font-bold text-slate-500 dark:text-slate-400 shadow-inner cursor-not-allowed"
                                           value="{{ old('username', $manager->username) }}" />
                                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Auto-generated from Full Name. Cannot be edited manually.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Corporate Email')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <input id="email" name="email" type="email" readonly
                                           class="w-full px-5 py-3.5 bg-slate-100 dark:bg-slate-900 border-none rounded-2xl text-sm font-bold text-slate-500 dark:text-slate-400 shadow-inner cursor-not-allowed" 
                                           value="{{ old('email', $manager->email) }}" required />
                                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Email tidak boleh ditukar.</p>
                                </div>

                                <!-- Phone Number -->
                                <div>
                                    <x-input-label for="phone_number" :value="__('Phone Number')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <input id="phone_number" name="phone_number" type="text" required
                                           class="form-control w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-purple-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner"
                                           value="{{ old('phone_number', $user->phone_number) }}" placeholder="e.g. 012-3456789 or 012-34567890" />
                                    <small class="text-muted">
                                        Format: 012-3456789 or 012-34567890 (numbers and dash only).
                                    </small>
                                    @if($errors->has('phone_number'))
                                        <small class="text-danger">
                                            {{ $errors->first('phone_number') }}
                                        </small>
                                    @endif
                                </div>

                                <!-- Row 3: Address (full width) -->
                                <div>
                                    <x-input-label for="address" :value="__('Address')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <textarea id="address" name="address" rows="2" 
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner" 
                                           required>{{ old('address', $manager->address) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <!-- Profile Picture -->
                                <div class="form-group">
                                    <x-input-label for="profile_picture" :value="__('Profile Picture')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    @if ($manager->profile_picture)
                                        <div class="mb-3 flex items-center gap-4">
                                            <img src="{{ asset('storage/' . $manager->profile_picture) }}" alt="Current Profile Picture" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-sm">
                                            <span class="text-xs text-slate-400 font-bold">Current Image</span>
                                        </div>
                                    @endif
                                    <input id="profile_picture" name="profile_picture" type="file" 
                                           class="form-control w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-500 dark:text-slate-400 shadow-inner" 
                                           accept="image/png, image/jpeg, image/jpg, image/gif" />
                                    <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Accepted formats: JPG, JPEG, PNG, GIF. Max file size: 2MB.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                                </div>

                                <div class="pt-6 border-t border-slate-50 dark:border-slate-700/50">
                                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 italic">Security Genesis Refresh</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <x-input-label for="password" :value="__('New Cryptokey')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                            <input id="password" name="password" type="password" autocomplete="new-password"
                                                   class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner" 
                                                   placeholder="Leave blank for persistence" />
                                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                        </div>
                                        <div>
                                            <x-input-label for="password_confirmation" :value="__('Verify Cryptokey')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                                   class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner" 
                                                   placeholder="Re-enter to confirm" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-slate-50 dark:border-slate-700/50">
                                <button type="submit" class="bg-indigo-600 text-white font-black text-[10px] uppercase tracking-[0.2em] px-10 py-4 rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Termination Card -->
                    <div class="premium-card p-10 bg-slate-50 dark:bg-slate-900/50 border-2 border-dashed border-red-100 dark:border-red-900/20">
                        <div class="flex flex-col md:flex-row items-center gap-8">
                            <div class="p-4 bg-red-100 dark:bg-red-900/20 rounded-3xl shrink-0">
                                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div class="flex-1 text-center md:text-left">
                                <h3 class="text-lg font-black text-slate-800 dark:text-white tracking-tight mb-2 uppercase italic">High-Magnitude Action</h3>
                                <p class="text-xs font-medium text-slate-500 leading-relaxed max-w-md"> Account termination is final.Delete this account cannot access to this system.</p>
                            </div>
                            <form method="POST" action="{{ route('manager.profile.destroy') }}" id="manager-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" id="manager-delete-btn" class="w-full md:w-auto bg-red-500 text-white font-black text-[10px] uppercase tracking-[0.2em] px-8 py-4 rounded-2xl hover:bg-red-600 shadow-xl shadow-red-100 transition-all italic">
                                    Delete Account
                                </button>
                            </form>
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

            // Auto-generate username from Full Name
            const nameInput = document.getElementById('name');
            const usernameInput = document.getElementById('username');
            if (nameInput && usernameInput) {
                nameInput.addEventListener('input', function () {
                    usernameInput.value = this.value.toLowerCase().replace(/\s+/g, '');
                });
            }

            // Delete account confirmation
            const deleteBtn = document.getElementById('manager-delete-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    Swal.fire({
                        title: 'Delete Account?',
                        html: '<p style="font-size:0.85rem;color:#64748b;">This action <strong>cannot be undone</strong>.<br>All account data will be permanently deleted.</p>',
                        icon: 'warning',
                        iconColor: '#ef4444',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="display:inline;margin-right:6px;"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6h14z" stroke="white" stroke-width="2" fill="none"/></svg> Yes, Delete',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'rounded-3xl',
                            confirmButton: 'rounded-xl font-bold text-xs uppercase tracking-widest px-6 py-3',
                            cancelButton: 'rounded-xl font-bold text-xs uppercase tracking-widest px-6 py-3',
                        },
                        buttonsStyling: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('manager-delete-form').submit();
                        }
                    });
                });
            }
        });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const inputs = document.querySelectorAll('input[name="phone_number"]');

        inputs.forEach(function(input){
            input.addEventListener("input", function () {
                let value = input.value;

                // remove non numeric
                value = value.replace(/[^0-9]/g, '');

                // limit max 11 digits
                value = value.substring(0, 11);

                // apply dash format
                if (value.length > 3) {
                    value = value.substring(0,3) + '-' + value.substring(3);
                }

                input.value = value;
            });
        });
    });
    </script>
</x-app-layout>
