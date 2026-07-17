<x-guest-layout>
    <style>
        body {
            background-image: linear-gradient(rgba(30, 27, 75, 0.65), rgba(15, 23, 42, 0.85)), url('{{ asset('images/dozee-bg.png') }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-attachment: fixed !important;
            background-repeat: no-repeat !important;
        }
        
        /* Make the logo text and brand name white to stand out against the dark background */
        span.heading-font.text-slate-900 {
            color: #ffffff !important;
        }
        
        /* Adjust footer and logo text colors outside the glass container */
        .text-slate-400, .italic {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        /* Force all texts, labels, and links inside the glass container to be black */
        .glass-effect h2,
        .glass-effect p,
        .glass-effect label,
        .glass-effect span,
        .glass-effect a:not(.btn-custom-back):not(.btn-custom-action),
        .glass-effect button.toggle-password {
            color: #000000 !important;
        }

        /* Custom Button Styling - BACK */
        .btn-custom-back {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            height: 38px !important;
            padding: 0 16px !important;
            background-color: #ffffff !important; /* white background */
            border: 1px solid #d1d5db !important; /* light grey border */
            border-radius: 9999px !important; /* rounded pill shape */
            font-size: 10px !important;
            font-weight: 500 !important; /* not too bold */
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            transition: all 0.2s ease-in-out !important;
            text-decoration: none !important;
            color: #4b5563 !important; /* dark grey text */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        .btn-custom-back:hover {
            background-color: #f9fafb !important;
            border-color: #9ca3af !important;
            color: #1f2937 !important;
            transform: translateY(-1px);
        }

        /* Custom Button Styling - ACTION (Create Account) */
        .btn-custom-action {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 38px !important;
            padding: 0 16px !important;
            background-color: #f5f3ff !important; /* very light lavender background */
            border: 1px solid #c7d2fe !important; /* light purple border */
            border-radius: 9999px !important; /* rounded pill shape */
            font-size: 11px !important; /* clear and readable */
            font-weight: 600 !important;
            text-transform: none !important; /* allow normal capitalization */
            letter-spacing: 0.02em !important;
            transition: all 0.2s ease-in-out !important;
            text-decoration: none !important;
            color: #6366f1 !important; /* purple text */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        .btn-custom-action:hover {
            background-color: #eedffc !important; /* slightly darker lavender */
            border-color: #a5b4fc !important;
            color: #4f46e5 !important;
            transform: translateY(-1px);
        }
    </style>

    <div class="mb-8">
        <h2 class="heading-font text-2xl font-black text-slate-800 tracking-tight">Manager Login</h2>
        <p class="text-xs font-medium text-slate-400 uppercase tracking-widest mt-1">Access your Do’Zee account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('manager.login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
            <input id="email" class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2" />
            <div class="relative">
                <input id="password" class="w-full pl-5 pr-12 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition-all text-sm font-medium text-slate-700 shadow-inner"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="Enter your password" />
                <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-indigo-500 transition-colors toggle-password" data-target="password">
                    <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a9.953 9.953 0 015.71-2.29c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Forgot Password -->
        <div class="flex items-center justify-end">
            @if (Route::has('password.request'))
                <a class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div class="pt-4 flex flex-col gap-4">
            <button type="submit" class="w-full bg-indigo-600 text-white font-black text-xs uppercase tracking-[0.2em] py-5 rounded-2xl hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">
                {{ __('Login') }}
            </button>
            
            <div class="flex items-center justify-between mt-2">
                <a href="{{ url('/') }}" class="btn-custom-back">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('Back') }}
                </a>
                <a class="btn-custom-action" href="{{ route('manager.register') }}">
                    {{ __('Create account') }}
                </a>
            </div>
        </div>
    </form>

    <script>
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
    </script>
</x-guest-layout>
