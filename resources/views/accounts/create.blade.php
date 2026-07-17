<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </span>
                {{ __('Add New Salesmen') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

                <!-- Form Body -->
                <div class="px-10 py-10">
                    <form method="POST" action="{{ route('accounts.store') }}" id="staffForm" autocomplete="off" novalidate>
                        @csrf

                        <div class="space-y-8">

                            <!-- ── NAME ───────────────────────────── -->
                            <div>
                                <label for="name" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Full Name</label>
                                <input id="name" name="name" type="text"
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-base font-medium text-slate-700 placeholder-slate-300"
                                       value="{{ old('name') }}" required autofocus placeholder="e.g. Muhammad Ali bin Abu" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <!-- ── EMAIL ────────────────────────────── -->
                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Email Address</label>
                                <input id="email" name="email" type="email"
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-base font-medium text-slate-700 placeholder-slate-300"
                                       value="{{ old('email') }}" required placeholder="email@example.com" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <!-- ── USERNAME ─────────────────────────── -->
                            <div>
                                <label for="username" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Username</label>
                                <input id="username" name="username" type="text" readonly
                                       class="w-full px-6 py-4 bg-slate-100 border border-slate-200 rounded-2xl cursor-not-allowed transition-all text-base font-medium text-slate-500 placeholder-slate-300"
                                       value="{{ old('username') }}" required placeholder="e.g. johndoe"
                                       autocomplete="off" />
                                <x-input-error class="mt-2" :messages="$errors->get('username')" />
                                <span id="err-username" class="text-red-500 text-xs mt-1 block"></span>
                                <p class="text-slate-400 text-xs mt-1.5">Automatically generated from name (lowercase, no spaces).</p>
                            </div>
                            


                            <!-- ── DIVIDER ─────────────────────────── -->
                            <div class="border-t border-slate-100 pt-2">
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-6">Security Credentials</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Password -->
                                    <div>
                                        <label for="password" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Password</label>
                                        <div class="relative">
                                            <input id="password" name="password" type="password"
                                                   class="w-full pl-6 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-base font-medium text-slate-700 placeholder-slate-300"
                                                   required placeholder="••••••••" minlength="8"
                                                   autocomplete="new-password" />
                                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-500 transition-colors toggle-password" data-target="password">
                                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a9.953 9.953 0 015.71-2.29c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                            </button>
                                        </div>

                                        <!-- Live Checklist -->
                                        <div id="pw-checklist" class="mt-2 space-y-1 hidden">
                                            <p data-rule="min"    class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> At least 8 characters</p>
                                            <p data-rule="lower"  class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One lowercase letter (a-z)</p>
                                            <p data-rule="upper"  class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One uppercase letter (A-Z)</p>
                                            <p data-rule="number" class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One number (0-9)</p>
                                            <p data-rule="symbol" class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One symbol (@$!%*?&amp;)</p>
                                        </div>

                                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                        <span id="err-password" class="text-red-500 text-xs mt-1 block"></span>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div>
                                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-2">Confirm Password</label>
                                        <div class="relative">
                                            <input id="password_confirmation" name="password_confirmation" type="password"
                                                   class="w-full pl-6 pr-12 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all text-base font-medium text-slate-700 placeholder-slate-300"
                                                   required placeholder="••••••••" minlength="8"
                                                   autocomplete="new-password" />
                                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-500 transition-colors toggle-password" data-target="password_confirmation">
                                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a9.953 9.953 0 015.71-2.29c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                            </button>
                                        </div>
                                        <p class="text-[9px] text-slate-400 font-medium mt-1">Type the password again to verify.</p>
                                        <span id="err-confirm" class="text-red-500 text-xs mt-1 block"></span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ── ACTIONS ─────────────────────────── -->
                        <div class="flex items-center justify-end gap-6 mt-10 pt-8 border-t border-slate-100">
                            <a href="{{ route('accounts.index') }}"
                               class="text-sm font-bold uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary px-10 py-4 flex items-center gap-3 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                Add Salesmen
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `<ul style="list-style:none;padding:0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                           </ul>`,
                    confirmButtonColor: '#e11d48'
                });
            @endif

            // ── Toggle Show/Hide Password (sama macam manager) ───────────
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId  = this.getAttribute('data-target');
                    const input     = document.getElementById(targetId);
                    const eyeIcon    = this.querySelector('.eye-icon');
                    const eyeOffIcon = this.querySelector('.eye-off-icon');

                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeIcon.classList.add('hidden');
                        eyeOffIcon.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        eyeIcon.classList.remove('hidden');
                        eyeOffIcon.classList.add('hidden');
                    }
                });
            });

            // ── Live Password Checklist (sama macam manager) ─────────────
            const pwInput   = document.getElementById('password');
            const checklist = document.getElementById('pw-checklist');

            const pwRules = {
                min:    v => v.length >= 8,
                lower:  v => /[a-z]/.test(v),
                upper:  v => /[A-Z]/.test(v),
                number: v => /[0-9]/.test(v),
                symbol: v => /[@$!%*?&]/.test(v),
            };

            pwInput.addEventListener('focus', () => checklist.classList.remove('hidden'));

            pwInput.addEventListener('input', () => {
                const val = pwInput.value;
                document.querySelectorAll('.pw-rule').forEach(rule => {
                    const key = rule.getAttribute('data-rule');
                    const dot = rule.querySelector('span');
                    if (pwRules[key](val)) {
                        dot.classList.replace('bg-slate-200', 'bg-emerald-500');
                        rule.classList.replace('text-slate-400', 'text-emerald-600');
                    } else {
                        dot.classList.replace('bg-emerald-500', 'bg-slate-200');
                        rule.classList.replace('text-emerald-600', 'text-slate-400');
                    }
                });
            });

            // ── Auto-generate Username ───────────────────────────
            const nameInput = document.getElementById('name');
            const usernameInput = document.getElementById('username');
            if (nameInput && usernameInput) {
                nameInput.addEventListener('input', function () {
                    // Convert to lowercase and remove spaces
                    usernameInput.value = this.value.toLowerCase().replace(/\s+/g, '');
                });
            }

            // ── Frontend Validation ───────────────────────────────────────
            const form = document.getElementById('staffForm');

            form.addEventListener('submit', function (e) {
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

                // Validasi Password — ikut rules Laravel
                const password = document.getElementById('password').value;
                const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
                if (password && !passwordRegex.test(password)) {
                    document.getElementById('err-password').textContent =
                        'Password must be min 8 characters and include uppercase, lowercase, number & symbol (@$!%*?&).';
                    isValid = false;
                }

                // Validasi Confirm Password — mesti sama
                const confirmPassword = document.getElementById('password_confirmation').value;
                if (confirmPassword && password !== confirmPassword) {
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
