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
                    {{ __('Salesmen Profile') }}
                @endif
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Profile Identity Sidebar -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="rounded-3xl bg-indigo-600 overflow-hidden relative group shadow-2xl shadow-indigo-200">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10 pointer-events-none">
                            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <path d="M0 0 L100 0 L100 100 L0 100 Z" fill="url(#grid)"></path>
                                <defs>
                                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"></path>
                                    </pattern>
                                </defs>
                            </svg>
                        </div>
                        
                        <div class="relative p-8 flex flex-col items-center text-center">
                            @if($salesmen->profile_picture)
                                <img src="{{ asset('storage/' . $salesmen->profile_picture) }}" class="w-36 h-36 rounded-3xl object-cover border border-white/30 shadow-xl mb-6 group-hover:scale-105 transition-transform duration-500" alt="Avatar">
                            @else
                                <div class="w-36 h-36 rounded-3xl bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center text-5xl font-black text-white shadow-xl mb-6 group-hover:scale-105 transition-transform duration-500">
                                    {{ strtoupper(substr($salesmen->name, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="text-xl font-black text-white tracking-tight mb-6">{{ $salesmen->name }}</h3>
                            
                            <div class="w-full pt-6 border-t border-white/10 flex justify-center">
                                <div class="text-center">
                                    <span class="block text-white font-black text-lg leading-none">Active</span>
                                    <span class="text-[10px] text-indigo-200 uppercase font-black tracking-tighter">Status</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info Card -->
                    <div class="premium-card bg-white dark:bg-slate-800 p-6 space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Tactical Credentials</h4>
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            <span class="text-xs font-bold">{{ $salesmen->username }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold">{{ $salesmen->email }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="text-xs font-bold">{{ $salesmen->phone_number ?? 'Tiada nombor telefon' }}</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-400">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-xs font-bold">{{ $salesmen->address ?? 'No address registered' }}</span>
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
                        <form method="POST" action="{{ route('salesmen.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-6">

                                <!-- Full Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Full Name')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <input id="name" name="name" type="text"
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner"
                                           value="{{ old('name', $salesmen->name) }}" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Username (auto-generated, read-only) -->
                                <div>
                                    <x-input-label for="username" :value="__('Username')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <input id="username" name="username" type="text" readonly
                                           class="w-full px-5 py-3.5 bg-slate-100 dark:bg-slate-800 border-none rounded-2xl text-sm font-bold text-slate-500 dark:text-slate-400 shadow-inner cursor-not-allowed"
                                           value="{{ old('username', $salesmen->username) }}" />
                                    <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Auto-generated from Full Name. Cannot be edited manually.</p>
                                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                                </div>

                                <div class="flex items-center gap-6">
                                    @if($salesmen->profile_picture)
                                        <img id="profile-preview" src="{{ asset('storage/' . $salesmen->profile_picture) }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-md" alt="Preview">
                                    @else
                                        <div id="profile-preview-placeholder" class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-500 font-black text-xl shadow-inner">
                                            {{ strtoupper(substr($salesmen->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <x-input-label for="profile_picture" :value="__('Upload Profile Picture')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                        <input id="profile_picture" name="profile_picture" type="file" accept="image/*"
                                               class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                                        <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                                        <p class="text-slate-400 text-[10px] mt-1.5 font-medium">Accepts JPEG, PNG, JPG, or GIF up to 2MB.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="email" :value="__('Corporate Email')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                        <input id="email" name="email" type="email" readonly
                                               class="w-full px-5 py-3.5 bg-slate-100 dark:bg-slate-900 border-none rounded-2xl text-sm font-bold text-slate-500 dark:text-slate-400 shadow-inner cursor-not-allowed" 
                                               value="{{ old('email', $salesmen->email) }}" required />
                                        <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Email tidak boleh ditukar.</p>
                                    </div>
                                    <div>
                                        <x-input-label for="phone_number" :value="__('Phone Number')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                        <input id="phone_number" name="phone_number" type="text" required
                                               class="form-control w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner" 
                                               value="{{ old('phone_number', $salesmen->phone_number) }}" placeholder="e.g. 012-3456789 or 012-34567890" />
                                        <small class="text-muted">
                                            Format: 012-3456789 or 012-34567890 (numbers and dash only).
                                        </small>
                                        @if($errors->has('phone_number'))
                                            <small class="text-danger">
                                                {{ $errors->first('phone_number') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Address -->
                                <div>
                                    <x-input-label for="address" :value="__('Geographic Domicile (Alamat)')" class="font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 mb-2" />
                                    <textarea id="address" name="address" rows="2" 
                                           class="w-full px-5 py-3.5 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-bold text-slate-700 dark:text-slate-200 shadow-inner" 
                                           required>{{ old('address', $salesmen->address) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <div class="pt-6 border-t border-slate-50 dark:border-slate-700/50">
                                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 italic">Security Access Refresh</h3>
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
                                <h3 class="text-lg font-black text-slate-800 dark:text-white tracking-tight mb-2 uppercase italic">Withdraw Enrollment</h3>
                                <p class="text-xs font-medium text-slate-500 leading-relaxed max-w-md"> Disconnecting your account will terminate all active field deployments. Historical transaction reports associated with your identity will be archived. </p>
                            </div>
                            <form method="POST" action="{{ route('salesmen.profile.destroy') }}" id="salesmen-delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" id="salesmen-delete-btn" class="w-full md:w-auto bg-red-500 text-white font-black text-[10px] uppercase tracking-[0.2em] px-8 py-4 rounded-2xl hover:bg-red-600 shadow-xl shadow-red-100 transition-all italic">
                                    Revoke Account
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

            // Live image preview
            const pictureInput = document.getElementById('profile_picture');
            if (pictureInput) {
                pictureInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            let previewEl = document.getElementById('profile-preview');
                            if (!previewEl) {
                                const placeholder = document.getElementById('profile-preview-placeholder');
                                if (placeholder) {
                                    previewEl = document.createElement('img');
                                    previewEl.id = 'profile-preview';
                                    previewEl.className = 'w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-slate-700 shadow-md';
                                    placeholder.parentNode.replaceChild(previewEl, placeholder);
                                }
                            }
                            if (previewEl) {
                                previewEl.src = e.target.result;
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Auto-generate username from Full Name
            const nameInput = document.getElementById('name');
            const usernameInput = document.getElementById('username');
            if (nameInput && usernameInput) {
                nameInput.addEventListener('input', function () {
                    usernameInput.value = this.value.toLowerCase().replace(/\s+/g, '');
                });
            }

            // Delete account confirmation
            const deleteStaffBtn = document.getElementById('salesmen-delete-btn');
            if (deleteStaffBtn) {
                deleteStaffBtn.addEventListener('click', function () {
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
                            document.getElementById('salesmen-delete-form').submit();
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
