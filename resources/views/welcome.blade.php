<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Do'zee | Premium Strategy Intelligence</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,900&family=outfit:400,500,600,700,800&display=swap" rel="stylesheet" />
        
        <!-- Vite/Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            .heading-font { font-family: 'Outfit', sans-serif; }
            
            .mesh-bg {
                background-color: #ffffff;
                background-image: 
                    radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.18) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.18) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(236, 72, 153, 0.12) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(59, 130, 246, 0.1) 0px, transparent 50%);
                background-attachment: fixed;
            }
            
            .floating {
                animation: floating 6s ease-in-out infinite;
            }
            
            .floating-slow {
                animation: floating 9s ease-in-out infinite;
            }
            
            @keyframes floating {
                0% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(1deg); }
                100% { transform: translateY(0px) rotate(0deg); }
            }

            .glass-card {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(25px) saturate(180%);
                -webkit-backdrop-filter: blur(25px) saturate(180%);
                border: 1px solid rgba(255, 255, 255, 0.4);
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            }

            .bento-card {
                @apply transition-all duration-500 hover:scale-[1.02] hover:-translate-y-2;
            }

            .text-glow {
                text-shadow: 0 0 30px rgba(99, 102, 241, 0.3);
            }
        </style>
    </head>
    <body class="antialiased mesh-bg min-h-screen overflow-x-hidden">

        <!-- Navigation -->
        <nav class="fixed top-0 inset-x-0 z-50 py-6">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="glass-card px-8 py-4 rounded-[2rem] flex items-center justify-between border-white/40 shadow-[0_10px_30px_rgba(0,0,0,0.02)] backdrop-blur-xl">
                    <div class="flex items-center gap-3 group cursor-pointer">
                        <span class="p-2.5 bg-slate-900 rounded-2xl text-white shadow-xl group-hover:scale-110 transition-transform duration-500">
                             <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </span>
                        <span class="heading-font font-black text-2xl tracking-tighter text-slate-900 uppercase">Do'zee</span>
                    </div>
                    
                    <div class="flex items-center gap-8">
                        <div class="hidden md:flex items-center gap-8">
                            <a href="#features" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-indigo-600 transition-colors">Intelligence</a>
                            <a href="#access" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-indigo-600 transition-colors">Portals</a>
                        </div>
                        <div class="h-6 w-px bg-slate-100 hidden md:block"></div>
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-[10px] font-black uppercase tracking-[0.2em] bg-indigo-600 text-white px-6 py-3 rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Console</a>
                        @else
                            <a href="#access" class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 hover:scale-105 transition-transform">Get Started</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 px-6">
            <div class="max-w-7xl mx-auto text-center lg:text-left grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-full bg-white/40 backdrop-blur-md border border-white/60 text-indigo-700 text-[10px] font-black uppercase tracking-[0.25em] mb-10 animate-fade-in shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse shadow-[0_0_10px_rgba(99,102,241,0.5)]"></span>
                        Next-Gen Strategy Intelligence
                    </div>
                    <h1 class="heading-font text-6xl lg:text-[7rem] font-black text-slate-900 leading-[0.95] tracking-tighter mb-10">
                        Marketing <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 drop-shadow-sm font-black">Redefined.</span>
                    </h1>
                    <p class="text-xl text-slate-500 font-medium mb-12 max-w-xl leading-relaxed">
                        Harness the power of the <span class="text-indigo-600 font-bold">Apriori Algorithm</span> to uncover hidden market patterns and optimize your promotional strategy with data-driven precision.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <a href="#access" class="w-full sm:w-auto bg-slate-900 text-white font-black px-12 py-6 rounded-[2rem] hover:bg-slate-800 shadow-[0_20px_50px_rgba(0,0,0,0.2)] hover:-translate-y-1 transition-all text-center">
                            Get Early Access
                        </a>
                        <a href="#features" class="w-full sm:w-auto font-black px-10 py-6 rounded-[2rem] text-slate-600 hover:text-slate-900 transition-all text-center">
                            Explore Features &rarr;
                        </a>
                    </div>
                </div>
                
                <div class="lg:col-span-5 relative hidden lg:block">
                    <div class="relative z-10 p-1 bg-gradient-to-tr from-white/40 to-indigo-500/20 rounded-[3rem] shadow-2xl overflow-hidden backdrop-blur-sm border border-white/40">
                        <div class="glass-card p-10 rounded-[2.8rem] floating-slow">
                            <div class="flex items-center justify-between mb-10">
                                <div class="flex gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                                </div>
                                <span class="p-2.5 bg-indigo-50 rounded-2xl text-indigo-600 text-[10px] font-black tracking-widest uppercase">Live Analysis</span>
                            </div>
                            <div class="space-y-8">
                                <div class="flex items-end gap-3 group">
                                    <div class="w-12 h-32 bg-indigo-50 group-hover:bg-indigo-600 transition-all duration-500 rounded-2xl"></div>
                                    <div class="w-12 h-44 bg-indigo-100 group-hover:bg-indigo-500 transition-all duration-500 rounded-2xl"></div>
                                    <div class="w-12 h-24 bg-indigo-50 group-hover:bg-indigo-400 transition-all duration-500 rounded-2xl"></div>
                                    <div class="w-12 h-40 bg-indigo-200 group-hover:bg-indigo-600 transition-all duration-500 rounded-2xl shadow-xl shadow-indigo-100"></div>
                                </div>
                                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Growth Factor</p>
                                        <p class="text-2xl font-black text-slate-900">+42.8%</p>
                                    </div>
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Background Glow -->
                    <div class="absolute -top-20 -right-20 w-80 h-80 bg-indigo-400 rounded-full blur-[120px] opacity-25"></div>
                    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-pink-400 rounded-full blur-[120px] opacity-25"></div>
                </div>
            </div>
        </main>

        <!-- Feature Bento Grid -->
        <section id="features" class="py-32 px-6 overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div class="text-center lg:text-left mb-20 max-w-2xl">
                    <h2 class="heading-font text-4xl lg:text-5xl font-black text-slate-900 mb-6 tracking-tight">Built for Precision. <br/>Designed for Growth.</h2>
                    <p class="text-lg text-slate-500 font-medium leading-relaxed">Stop guessing what your customers want. Let Do'zee's intelligence layer predict their next move.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-6 md:grid-rows-2">
                    <!-- Large Card: Apriori -->
                    <div class="md:col-span-4 lg:col-span-4 bento-card glass-card p-12 flex flex-col justify-between overflow-hidden relative group">
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-indigo-600 rounded-[2rem] flex items-center justify-center text-white mb-10 shadow-2xl shadow-indigo-100 group-hover:rotate-12 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h3 class="heading-font text-3xl font-black text-slate-900 mb-4">Apriori Logic</h3>
                            <p class="text-slate-500 font-medium max-w-sm mb-8 leading-relaxed">Our implementation of the Apriori algorithm identifies frequent itemsets and association rules with surgical accuracy.</p>
                        </div>
                        <div class="absolute -right-20 bottom-0 top-0 w-1/2 flex items-center gap-4 opacity-50 group-hover:opacity-100 transition-opacity">
                            <div class="flex flex-col gap-4 animate-bounce" style="animation-duration: 3s">
                                <div class="w-24 h-24 bg-indigo-50 rounded-3xl"></div>
                                <div class="w-24 h-24 bg-indigo-100 rounded-3xl"></div>
                            </div>
                            <div class="flex flex-col gap-4 animate-bounce pt-12" style="animation-duration: 4s">
                                <div class="w-24 h-24 bg-indigo-500 rounded-3xl shadow-xl"></div>
                                <div class="w-24 h-24 bg-indigo-50 rounded-3xl"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Small Card: Analytics -->
                    <div class="md:col-span-2 lg:col-span-2 bento-card bg-slate-900 text-white p-12 flex flex-col justify-between rounded-[3rem]">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="heading-font text-2xl font-black mb-2">Real-time Stats</h3>
                            <p class="text-slate-400 text-sm font-medium">Instant dashboard updates on every sale.</p>
                        </div>
                    </div>

                    <!-- Medium Card: Performance -->
                    <div class="md:col-span-3 lg:col-span-3 bento-card glass-card p-12 bg-gradient-to-br from-indigo-50/50 to-white/50 rounded-[3rem]">
                         <div class="text-6xl mb-8">🚀</div>
                         <h3 class="heading-font text-2xl font-black text-slate-900 mb-2">High Lift</h3>
                         <p class="text-slate-500 text-sm font-medium">Optimize product placement to increase sales lift by up to 35%.</p>
                    </div>

                    <!-- Medium Card: Security -->
                    <div class="md:col-span-3 lg:col-span-3 bento-card glass-card p-12 bg-gradient-to-br from-purple-50/50 to-white/50 rounded-[3rem]">
                         <div class="text-6xl mb-8">🛡️</div>
                         <h3 class="heading-font text-2xl font-black text-slate-900 mb-2">Secure Roles</h3>
                         <p class="text-slate-500 text-sm font-medium">Strict separation between Manager and Salesman authentication guards.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Access Portals -->
        <section id="access" class="py-32 bg-white relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-indigo-50/50 via-transparent to-transparent opacity-50"></div>
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-24">
                    <h2 class="heading-font text-4xl font-black text-slate-900 mb-6 tracking-tight">Enterprise Access</h2>
                    <p class="text-slate-500 font-medium max-w-lg mx-auto">Authorized personnel only. Please select your dedicated access point below.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-5xl mx-auto">
                    <!-- Manager Card -->
                    <div class="glass-card p-12 rounded-[3.5rem] group hover:-translate-y-4 transition-all duration-700 border-white/60 relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl group-hover:bg-indigo-500/20 transition-all"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-[2rem] flex items-center justify-center text-white mb-10 shadow-3xl shadow-indigo-200 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="heading-font text-3xl font-black text-slate-900 mb-4">Manager Portal</h3>
                        <p class="text-slate-500 font-medium mb-10 leading-relaxed">Full administrative oversight, strategy generation, and personnel management.</p>
                        <div class="flex flex-col sm:flex-row gap-4 pt-10 border-t border-slate-100">
                            <a href="{{ route('manager.login') }}" class="flex-1 bg-slate-900 text-white font-black text-xs uppercase tracking-widest py-5 rounded-[1.5rem] text-center hover:bg-slate-800 shadow-xl transition-all">Sign In</a>
                            <a href="{{ route('manager.register') }}" class="flex-1 bg-white text-slate-900 font-black text-xs uppercase tracking-widest py-5 rounded-[1.5rem] text-center border border-slate-200 hover:border-slate-800 transition-all">Register</a>
                        </div>
                    </div>

                    <!-- Salesman Card -->
                    <div class="glass-card p-12 rounded-[3.5rem] group hover:-translate-y-4 transition-all duration-700 border-white/60 relative overflow-hidden">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-950 rounded-[2rem] flex items-center justify-center text-white mb-10 shadow-3xl shadow-slate-200 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="heading-font text-3xl font-black text-slate-900 mb-4">Sales Portal</h3>
                        <p class="text-slate-500 font-medium mb-10 leading-relaxed">Daily operation logs, sales recording, and promotion request tracking.</p>
                        <div class="pt-10 border-t border-slate-100">
                            <a href="{{ route('salesman.login') }}" class="block w-full bg-slate-100 text-slate-900 font-black text-xs uppercase tracking-widest py-5 rounded-[1.5rem] text-center hover:bg-slate-900 hover:text-white shadow-sm transition-all">Sales Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-24 border-y border-slate-100 bg-white/50 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-12 text-center">
                    <div class="group">
                        <div class="heading-font text-5xl font-black text-slate-900 mb-3 tracking-tighter group-hover:text-indigo-600 transition-colors">99.9%</div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Rule Accuracy</p>
                    </div>
                    <div class="group">
                        <div class="heading-font text-5xl font-black text-slate-900 mb-3 tracking-tighter group-hover:text-purple-600 transition-colors">12k+</div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Market Rules</p>
                    </div>
                    <div class="group">
                        <div class="heading-font text-5xl font-black text-slate-900 mb-3 tracking-tighter group-hover:text-pink-600 transition-colors">45%</div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Avg. Sales Lift</p>
                    </div>
                    <div class="group">
                        <div class="heading-font text-5xl font-black text-slate-900 mb-3 tracking-tighter group-hover:text-blue-600 transition-colors">300ms</div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Calc. Latency</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-8 border-t border-slate-50 pt-12">
                <div class="flex items-center gap-2 grayscale brightness-50">
                    <span class="p-1.5 bg-slate-900 rounded-lg text-white">
                         <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    <span class="heading-font font-black text-lg tracking-tight text-slate-900 uppercase">Do'zee</span>
                </div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                    &copy; {{ date('Y') }} Intelligence Layer. Powered by Apriori v2.
                </p>
            </div>
        </footer>
    </body>
</html>
