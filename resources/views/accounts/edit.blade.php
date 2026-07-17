<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </span>
                {{ __('Edit Salesmen') }}: {{ $salesmen->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Main Form Card -->
                <div class="lg:col-span-8">
                    <div class="premium-card bg-white p-10">
                        <form method="POST" action="{{ route('accounts.update', $salesmen->salesmen_id) }}" class="space-y-8" id="editStaffForm" autocomplete="off" novalidate>
                            @csrf
                            @method('PATCH')

                            <div class="space-y-6">
                                <!-- Staff Code -->
                                <div>
                                    <x-input-label for="staff_code" :value="__('Staff Code')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                    <input id="staff_code" name="staff_code" type="text" readonly
                                           class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-2xl cursor-not-allowed text-sm font-medium text-slate-500 dark:text-slate-400"
                                           value="{{ $salesmen->staff_code ?? 'Not Generated' }}" />
                                    <p class="text-slate-400 text-xs mt-1">This code is automatically generated and cannot be modified.</p>
                                </div>

                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                    <input id="name" name="name" type="text"
                                           class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200"
                                           value="{{ old('name', $salesmen->name) }}" required placeholder="Enter full name" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Email & Username Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="email" :value="__('Email')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                        <input id="email" name="email" type="email" readonly
                                               class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-2xl cursor-not-allowed text-sm font-medium text-slate-500 dark:text-slate-400"
                                               value="{{ old('email', $salesmen->email) }}" required placeholder="email@example.com" />
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>
                                    <div>
                                        <x-input-label for="username" :value="__('Username')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                        <input id="username" name="username" type="text"
                                               class="w-full px-5 py-3 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200"
                                               value="{{ old('username', $salesmen->username) }}" required placeholder="e.g. johndoe"
                                               autocomplete="off" />
                                        <x-input-error class="mt-2" :messages="$errors->get('username')" />
                                        <span id="err-username" class="text-red-500 text-xs mt-1 block"></span>
                                        <p class="text-slate-400 text-xs mt-1">Lowercase letters and numbers only, no spaces or symbols.</p>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div>
                                    <x-input-label for="address" :value="__('Address')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                    <textarea id="address" name="address" readonly required rows="1"
                                              class="w-full px-5 py-3 bg-slate-100 dark:bg-slate-900 border-none rounded-2xl cursor-not-allowed text-sm font-medium text-slate-500 dark:text-slate-400"
                                              placeholder="Enter living address">{{ old('address', $salesmen->address) }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                @if(false)
                                <!-- Password Section -->
                                <div class="pt-4 border-t border-slate-100">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-4">Change Password <span class="normal-case font-normal text-slate-300">(leave blank to keep current)</span></p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- New Password -->
                                        <div>
                                            <x-input-label for="password" :value="__('Password')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                            <div class="relative">
                                                <input id="password" name="password" type="password"
                                                       class="w-full px-5 py-3 pr-12 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200"
                                                       placeholder="Min. 8 characters"
                                                       autocomplete="new-password" />
                                                <button type="button" onclick="togglePassword('password', 'eye-password')"
                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-500 transition-colors focus:outline-none">
                                                    <svg id="eye-password" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                            <span id="err-password" class="text-red-500 text-xs mt-1 block"></span>
                                            <p class="text-slate-400 text-xs mt-1">Min 8 chars: uppercase, lowercase, number &amp; symbol (@$!%*?&amp;).</p>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div>
                                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-bold text-[10px] uppercase tracking-widest text-slate-400 mb-2" />
                                            <div class="relative">
                                                <input id="password_confirmation" name="password_confirmation" type="password"
                                                       class="w-full px-5 py-3 pr-12 bg-slate-50 dark:bg-slate-900 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 dark:text-slate-200"
                                                       placeholder="Re-enter password"
                                                       autocomplete="new-password" />
                                                <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-500 transition-colors focus:outline-none">
                                                    <svg id="eye-confirm" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <span id="err-confirm" class="text-red-500 text-xs mt-1 block"></span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-end gap-6 pt-8 border-t border-slate-50">
                                <a href="{{ route('accounts.index') }}" class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">Cancel</a>
                                <button type="submit" class="btn-primary px-10 py-4 flex items-center gap-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-4 space-y-8">
                    <div class="premium-card p-8 bg-slate-900 text-white shadow-2xl">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center font-black text-white text-xl">
                                {{ strtoupper(substr($salesmen->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-xs font-black uppercase tracking-widest text-white">{{ $salesmen->name }}</h4>
                                @if($salesmen->staff_code)
                                    <p class="text-[10px] font-bold text-indigo-400 tracking-wider mt-0.5">{{ $salesmen->staff_code }}</p>
                                @endif
                                <p class="text-[10px] font-medium text-slate-400 mt-0.5">{{ $salesmen->email }}</p>
                            </div>
                        </div>
                        <p class="text-[11px] font-medium text-slate-400 leading-relaxed">
                            Changes to salesmen information will affect all linked records in sales and reports.
                        </p>
                    </div>

                    <div class="premium-card p-8 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">System Logs</h4>
                        <div class="space-y-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('Enrolled') }}</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $salesmen->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex flex-col gap-1 pt-2 border-t border-slate-100 dark:border-slate-700/50">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('Phone Number') }}</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $salesmen->phone_number ?? 'Not registered' }}</span>
                            </div>
                            <div class="flex flex-col gap-1 pt-2 border-t border-slate-100 dark:border-slate-700/50">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('Profile Picture') }}</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    @if($salesmen->profile_picture)
                                        <span class="text-emerald-500 font-black">✓ Uploaded</span>
                                    @else
                                        <span class="text-slate-400">Not uploaded</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
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

            // ── Toggle Show/Hide Password ─────────────────────────────────
            window.togglePassword = function(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon  = document.getElementById(iconId);
                const isHidden = input.type === 'password';

                input.type = isHidden ? 'text' : 'password';

                icon.innerHTML = isHidden
                    ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`
                    : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            };

            // ── Frontend Validation ───────────────────────────────────────
            const form = document.getElementById('editStaffForm');

            form.addEventListener('submit', function(e) {
                // Reset error spans
                ['err-username', 'err-password', 'err-confirm'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = '';
                });

                let isValid = true;

                // Validasi Username — huruf kecil dan nombor sahaja
                const username = document.getElementById('username').value.trim();
                if (username && !/^[a-z0-9]+$/.test(username)) {
                    document.getElementById('err-username').textContent =
                        'Username must be lowercase letters and numbers only, no spaces or symbols.';
                    isValid = false;
                }

                // Validasi Password (hanya jika user isi)
                const passwordEl = document.getElementById('password');
                const password = passwordEl ? passwordEl.value : null;
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                if (password && !passwordRegex.test(password)) {
                    document.getElementById('err-password').textContent =
                        'Password must be min 8 characters and include uppercase, lowercase, number & symbol (@$!%*?&).';
                    isValid = false;
                }

                // Validasi Confirm Password (hanya jika password diisi)
                const confirmPasswordEl = document.getElementById('password_confirmation');
                const confirmPassword = confirmPasswordEl ? confirmPasswordEl.value : null;
                if (password && confirmPassword && password !== confirmPassword) {
                    document.getElementById('err-confirm').textContent =
                        'Passwords do not match.';
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</x-app-layout>
