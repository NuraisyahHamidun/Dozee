<x-guest-layout>
    <div class="mb-8">
        <h2 class="heading-font text-2xl font-black text-slate-800 tracking-tight">Manager Registration</h2>
        <p class="text-xs font-medium text-slate-400 uppercase tracking-widest mt-1">Create a manager account to access the system</p>
    </div>

    <form method="POST" action="{{ route('manager.register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
            <input id="name" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="e.g. Nur Aisyah" />
            <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Enter your full name</p>
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
            <input id="email" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="e.g. nuraisyahsiti793@gmail.com" />
            <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Enter your email address</p>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
            <input id="username" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner" type="text" name="username" value="{{ old('username') }}" required autocomplete="nickname" placeholder="e.g. aisyah_88" />
            <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Auto created from name or enter manually</p>
            <x-input-error :messages="$errors->get('username')" class="mt-1" />
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" :value="__('Address')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
            <textarea id="address" name="address" required rows="2" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner" placeholder="e.g. No. 12, Jalan Merdeka, 50000 Kuala Lumpur">{{ old('address') }}</textarea>
            <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Enter your address</p>
            <x-input-error :messages="$errors->get('address')" class="mt-1" />
        </div>

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone_number" :value="__('Phone Number')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
            <input id="phone_number" class="form-control w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner" type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="e.g. 0123456789 or 012-3456789" />
            <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Format: 0123456789 or 012-3456789</p>
            <div id="phone-error" class="text-red-500 text-xs font-semibold mt-1 hidden">
                Invalid phone number format. Only numbers allowed (10–11 digits). Example: 0123456789 or 012-3456789
            </div>
            @if($errors->has('phone_number'))
                <small class="text-red-500 text-xs font-semibold mt-1 block laravel-error">
                    {{ $errors->first('phone_number') }}
                </small>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
                <div class="relative">
                    <input id="password" class="w-full pl-5 pr-12 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner"
                                    type="password"
                                    name="password"
                                    required autocomplete="new-password" placeholder="••••••••" minlength="8" />
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-500 transition-colors toggle-password" data-target="password">
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a9.953 9.953 0 015.71-2.29c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </button>
                </div>
                <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Create a password</p>

                <!-- Live Checklist -->
                <div id="pw-checklist" class="mt-2 space-y-1 hidden">
                    <p data-rule="min"    class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> At least 8 characters</p>
                    <p data-rule="lower"  class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One lowercase letter (a-z)</p>
                    <p data-rule="upper"  class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One uppercase letter (A-Z)</p>
                    <p data-rule="number" class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One number (0-9)</p>
                    <p data-rule="symbol" class="pw-rule flex items-center gap-1.5 text-[9px] font-bold text-slate-400"><span class="w-3 h-3 rounded-full bg-slate-200 flex-shrink-0"></span> One symbol (@$!%*?&)</p>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
                <div class="relative">
                    <input id="password_confirmation" class="w-full pl-5 pr-12 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner"
                                    type="password"
                                    name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" minlength="8" />
                    <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-500 transition-colors toggle-password" data-target="password_confirmation">
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a9.953 9.953 0 015.71-2.29c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </button>
                </div>
                <p class="text-[9px] text-slate-400 font-medium mt-1 ml-1">Re-enter your password</p>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>
        </div>

        <div class="pt-4 flex flex-col gap-4">
            <button type="submit" class="w-full bg-indigo-600 text-white font-black text-xs uppercase tracking-[0.2em] py-5 rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">
                {{ __('Create Manager Account') }}
            </button>
            
            <div class="flex items-center justify-between mt-2">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('Cancel') }}
                </a>
                <a class="text-[10px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-800" href="{{ route('manager.login') }}">
                    {{ __('Already have an account? Login here') }}
                </a>
            </div>
        </div>
    </form>

    <script>
        // Show/Hide password toggle
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const eyeIcon = this.querySelector('.eye-icon');
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

        // Auto-generate Username
        const nameInput = document.getElementById('name');
        const usernameInput = document.getElementById('username');
        if (nameInput && usernameInput) {
            nameInput.addEventListener('input', function() {
                usernameInput.value = this.value.toLowerCase().replace(/\s+/g, '');
            });
        }

        // Live password strength checklist
        const pwInput    = document.getElementById('password');
        const checklist  = document.getElementById('pw-checklist');

        const rules = {
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
                if (rules[key](val)) {
                    dot.classList.replace('bg-slate-200', 'bg-emerald-500');
                    rule.classList.replace('text-slate-400', 'text-emerald-600');
                } else {
                    dot.classList.replace('bg-emerald-500', 'bg-slate-200');
                    rule.classList.replace('text-emerald-600', 'text-slate-400');
                }
            });
        });
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const phoneInput = document.getElementById('phone_number');
        const form = phoneInput.closest('form');
        const errorDiv = document.getElementById('phone-error');
        const backendError = phoneInput.parentNode.querySelector('.laravel-error');

        function validatePhone() {
            const value = phoneInput.value;

            if (backendError) {
                backendError.classList.add('hidden');
            }

            if (value === '') {
                errorDiv.classList.add('hidden');
                phoneInput.classList.remove('border', 'border-red-500', 'focus:ring-red-500', 'border-emerald-500', 'focus:ring-emerald-500');
                phoneInput.classList.add('border-none');
                return true;
            }

            // Must contain only numbers and optional dash (-)
            const charsValid = /^[0-9-]+$/.test(value);

            // Must be 10–11 digits (excluding dash)
            const digitsOnly = value.replace(/-/g, '');
            const digitsLengthValid = digitsOnly.length >= 10 && digitsOnly.length <= 11;

            const isValid = charsValid && digitsLengthValid;

            if (!isValid) {
                errorDiv.classList.remove('hidden');
                phoneInput.classList.remove('border-none', 'border-emerald-500', 'focus:ring-emerald-500');
                phoneInput.classList.add('border', 'border-red-500', 'focus:ring-red-500');
                return false;
            } else {
                errorDiv.classList.add('hidden');
                phoneInput.classList.remove('border-none', 'border-red-500', 'focus:ring-red-500');
                phoneInput.classList.add('border', 'border-emerald-500', 'focus:ring-emerald-500');
                return true;
            }
        }

        phoneInput.addEventListener("input", validatePhone);
        phoneInput.addEventListener("change", validatePhone);
        phoneInput.addEventListener("blur", validatePhone);

        form.addEventListener('submit', function (e) {
            if (!validatePhone()) {
                e.preventDefault();
                phoneInput.focus();
            }
        });
    });
    </script>
</x-guest-layout>

