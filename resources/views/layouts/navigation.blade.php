<nav x-data="{ open: false }" class="glass-effect sticky top-4 z-50 mx-4 mt-4 rounded-3xl border border-white/20 dark:border-white/10 shadow-lg transition-all duration-300">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
        <div class="flex justify-between h-18 py-3">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center hover-scale">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 no-underline">
                        <span class="p-2 bg-indigo-600 rounded-xl text-white shadow-lg shadow-indigo-200">
                             <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </span>
                        <span class="heading-font font-bold text-xl tracking-tight text-slate-800 dark:text-white">Do'zee</span>
                    </a>
                </div>
 
                <!-- Navigation Links -->
                <div class="hidden space-x-2 sm:-my-px sm:ms-12 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('dashboard') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>
 
                    @if(Auth::guard('manager')->check())
                        <x-nav-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('accounts.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Salesmen') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::guard('manager')->check())
                        <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('categories.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Category') }}
                        </x-nav-link>
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('products.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Items') }}
                        </x-nav-link>
                        <x-nav-link :href="route('promotions.index')" :active="request()->routeIs('promotions.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('promotions.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Promotion') }}
                        </x-nav-link>
                        <x-nav-link :href="route('analysis.weka')" :active="request()->routeIs('analysis.weka') || request()->routeIs('analysis.index')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('analysis.weka') || request()->routeIs('analysis.index') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Apriori algorithm') }}
                        </x-nav-link>
                    @elseif(Auth::guard('salesman')->check())
                        <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('sales.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Sales') }}
                        </x-nav-link>
                        <x-nav-link :href="route('promotions.index')" :active="request()->routeIs('promotions.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('promotions.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Promotion') }}
                        </x-nav-link>
                        <x-nav-link :href="route('salesman.items.index')" :active="request()->routeIs('salesman.items.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('salesman.items.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Items') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reports.salesman_personal.index')" :active="request()->routeIs('reports.salesman_personal.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('reports.salesman_personal.*') ? 'bg-purple-50/50 dark:bg-purple-500/10 shadow-sm text-purple-600 dark:text-purple-400' : '' }}">
                            {{ __('Report') }}
                        </x-nav-link>
                    @endif
                    
                    @if(Auth::guard('manager')->check())
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')" class="px-4 py-2 rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest {{ request()->routeIs('reports.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 shadow-sm' : '' }}">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-slate-200 dark:border-white/10 text-sm leading-4 font-semibold rounded-2xl text-slate-600 dark:text-slate-300 bg-white/50 dark:bg-white/5 hover:bg-white dark:hover:bg-white/10 focus:outline-none transition-all duration-200 shadow-sm">
                            <div class="flex items-center gap-3">
                                @if(Auth::guard('manager')->check() && Auth::guard('manager')->user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::guard('manager')->user()->profile_picture) }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-white/10" alt="Avatar">
                                @elseif(Auth::guard('salesman')->check() && Auth::guard('salesman')->user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::guard('salesman')->user()->profile_picture) }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-white/10" alt="Avatar">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-gradient-premium flex items-center justify-center text-white text-[10px] font-black">
                                        {{ substr(Auth::guard('manager')->check() ? Auth::guard('manager')->user()->name : Auth::guard('salesman')->user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="text-left hidden lg:block">
                                    <p class="text-[10px] font-black leading-none mb-1 text-slate-800 dark:text-white">
                                        @if(Auth::guard('manager')->check())
                                            {{ Auth::guard('manager')->user()->name }}
                                        @elseif(Auth::guard('salesman')->check())
                                            {{ Auth::guard('salesman')->user()->name }}
                                        @endif
                                    </p>
                                    <p class="text-[8px] text-slate-400 font-bold uppercase tracking-[0.2em]">
                                        {{ Auth::guard('manager')->check() ? 'Manager' : 'Salesman' }}
                                    </p>
                                </div>
                                <svg class="fill-current h-4 w-4 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        @if(Auth::guard('manager')->check())
                            <x-dropdown-link :href="route('manager.profile.edit')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('manager.logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('manager.logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        @elseif(Auth::guard('salesman')->check())
                            <x-dropdown-link :href="route('salesman.profile.edit')">
                                {{ __('My Profile') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('salesman.logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('salesman.logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        @endif
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @if(Auth::guard('manager')->check())
                <x-responsive-nav-link :href="route('accounts.index')" :active="request()->routeIs('accounts.*')">
                    {{ __('Salesmen') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::guard('manager')->check())
                <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                    {{ __('Category') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                    {{ __('Items') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('promotions.index')" :active="request()->routeIs('promotions.*')">
                    {{ __('Promotion') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('analysis.weka')" :active="request()->routeIs('analysis.weka') || request()->routeIs('analysis.index')">
                    {{ __('Apriori algorithm') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @elseif(Auth::guard('salesman')->check())
                <x-responsive-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                    {{ __('Sales') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('promotions.index')" :active="request()->routeIs('promotions.*')">
                    {{ __('Promotion') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('salesman.items.index')" :active="request()->routeIs('salesman.items.*')">
                    {{ __('Items') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.salesman_personal.index')" :active="request()->routeIs('reports.salesman_personal.*')">
                    {{ __('Report') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                @if(Auth::guard('manager')->check())
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::guard('manager')->user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::guard('manager')->user()->email }}</div>
                @elseif(Auth::guard('salesman')->check())
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::guard('salesman')->user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::guard('salesman')->user()->email }}</div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                @if(Auth::guard('manager')->check())
                    <x-responsive-nav-link :href="route('manager.profile.edit')">
                        {{ __('My Profile') }}
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('manager.logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('manager.logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                @elseif(Auth::guard('salesman')->check())
                    <form method="POST" action="{{ route('salesman.logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('salesman.logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                @endif
            </div>
        </div>
    </div>
</nav>
