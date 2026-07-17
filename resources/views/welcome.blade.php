<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Do’Zee | Market Basket Analysis System</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
        
        <!-- Vite/Tailwind CSS -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { 
                font-family: 'Inter', sans-serif; 
                background-color: #F5F7FC;
                color: #475569;
            }
            .heading-font { font-family: 'Outfit', sans-serif; }
            
            /* CSS PRIORITY FOR HERO SHELL - Dark navy gradient backdrop */
            body .dozee-hero-shell {
                background:
                    radial-gradient(circle at 75% 35%, rgba(39, 72, 255, 0.32), transparent 34%),
                    radial-gradient(circle at 92% 15%, rgba(169, 41, 255, 0.22), transparent 30%),
                    linear-gradient(115deg, #020716 0%, #06133c 52%, #08145c 100%) !important;
                background-color: #020716 !important;
            }
            
            /* Grid Overlay inside Hero Shell */
            .hero-grid {
                background-image: 
                    linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
                background-size: 32px 32px;
            }

            /* Custom text gradient */
            .dozee-gradient-text {
                background: linear-gradient(90deg, #8b35ff, #e53bb8, #ff7139) !important;
                -webkit-background-clip: text !important;
                -webkit-text-fill-color: transparent !important;
                background-clip: text !important;
                color: transparent !important;
                display: inline-block;
            }

            /* Header Login button with pure CSS gradient border and hover glow */
            .header-login-btn {
                position: relative;
                width: 120px;
                height: 46px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: rgba(0, 0, 0, 0.3) !important;
                border-radius: 10px !important;
                color: #FFFFFF !important;
                font-weight: 700 !important;
                font-size: 13px !important;
                text-transform: uppercase !important;
                letter-spacing: 0.1em !important;
                transition: all 0.3s ease !important;
                z-index: 10;
            }
            .header-login-btn::before {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: 10px;
                padding: 1.5px;
                background: linear-gradient(90deg, #248BFF, #8038FF);
                -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
                -webkit-mask-composite: xor;
                mask-composite: exclude;
                pointer-events: none;
            }
            .header-login-btn:hover {
                background: rgba(0, 0, 0, 0.5) !important;
                box-shadow: 0 0 15px rgba(128, 56, 255, 0.5) !important;
            }

            /* Large Video Card with glowing gradient frame and soft shadows */
            .dozee-video-card {
                position: relative;
                width: 100%;
                max-w: 690px;
                aspect-ratio: 16 / 9;
                border-radius: 22px;
                padding: 3px;
                background: linear-gradient(135deg, #25a7ff, #7847ff, #e23cff);
                box-shadow:
                    0 0 25px rgba(35, 142, 255, 0.42),
                    0 0 35px rgba(207, 52, 255, 0.28);
                overflow: hidden;
            }

            /* CTA button gradient, rounded corners, soft glow and hover lift */
            .dozee-cta-btn {
                width: 220px;
                height: 58px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                background: linear-gradient(90deg, #8b35ff, #e53bb8, #ff7139) !important;
                border-radius: 12px !important;
                color: #FFFFFF !important;
                font-weight: 700 !important;
                font-size: 14px !important;
                box-shadow: 0 0 20px rgba(229, 59, 184, 0.3) !important;
                transition: all 0.3s ease !important;
            }
            .dozee-cta-btn:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 0 30px rgba(229, 59, 184, 0.5) !important;
            }

            /* Feature Card Transitions */
            .feature-card-hover {
                transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .feature-card-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px -15px rgba(109, 40, 217, 0.08);
            }

            /* Custom Manager Card */
            .manager-portal-card {
                background: linear-gradient(135deg, #200A52 0%, #0A0D2A 100%);
                border: 1.5px solid rgba(128, 56, 255, 0.25);
                box-shadow: 0 0 30px rgba(128, 56, 255, 0.05), 0 30px 60px -15px rgba(0, 0, 0, 0.3);
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .manager-portal-card:hover {
                transform: translateY(-4px);
                border-color: rgba(128, 56, 255, 0.5);
                box-shadow: 0 0 45px rgba(128, 56, 255, 0.15), 0 30px 60px -15px rgba(0, 0, 0, 0.35);
            }

            /* Custom Salesmen Card */
            .salesmen-portal-card {
                background: linear-gradient(135deg, #064E3B 0%, #0A0D2A 100%);
                border: 1.5px solid rgba(22, 216, 135, 0.25);
                box-shadow: 0 0 30px rgba(22, 216, 135, 0.05), 0 30px 60px -15px rgba(0, 0, 0, 0.3);
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .salesmen-portal-card:hover {
                transform: translateY(-4px);
                border-color: rgba(22, 216, 135, 0.5);
                box-shadow: 0 0 45px rgba(22, 216, 135, 0.15), 0 30px 60px -15px rgba(0, 0, 0, 0.35);
            }
        </style>
    </head>
    <body class="antialiased min-h-screen overflow-x-hidden">        <!-- HERO WRAPPER (Deep dark navy hero + transparent header) -->
        <div class="relative dozee-hero-shell hero-grid text-white overflow-hidden">
            
            <!-- Background Decorative Wave & Cube Elements -->
            <div class="absolute bottom-0 left-0 right-0 h-28 overflow-hidden pointer-events-none opacity-30 z-0">
                <svg viewBox="0 0 1440 74" fill="none" class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 30 C 240 70, 480 0, 720 30 C 960 60, 1200 10, 1440 30 L 1440 74 L 0 74 Z" fill="rgba(36, 139, 255, 0.05)"/>
                    <path d="M0 45 C 300 15, 600 65, 900 45 C 1200 25, 1350 55, 1440 45" stroke="rgba(128, 56, 255, 0.2)" stroke-width="1.5" stroke-dasharray="10 10"/>
                </svg>
            </div>
            <div class="absolute bottom-12 left-[44%] w-16 h-16 pointer-events-none opacity-40 animate-pulse z-0">
                <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M32 8 L54 20 L54 44 L32 56 L10 44 L10 20 Z" stroke="rgba(128, 56, 255, 0.4)" stroke-width="1.5" />
                    <path d="M32 8 L32 56" stroke="rgba(128, 56, 255, 0.4)" stroke-width="1.5" />
                    <path d="M10 20 L32 32 L54 20" stroke="rgba(128, 56, 255, 0.4)" stroke-width="1.5" />
                </svg>
            </div>

            <!-- 1. Header -->
            <header class="relative z-50 max-w-[1440px] mx-auto px-6 sm:px-12 lg:px-16 py-7">
                <div class="flex items-center justify-between">
                    <!-- Left: Logo & Tagline -->
                    <div class="flex flex-col select-none w-[145px] sm:w-[160px] transition-all">
                        <div class="flex items-center gap-1.5">
                            <span class="heading-font font-black text-2xl sm:text-3xl tracking-tight text-white">Do’<span class="text-[#E53BB8]">Zee</span></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#E53BB8] mt-1.5"></span>
                        </div>
                        <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1 whitespace-nowrap">MARKET BASKET ANALYSIS SYSTEM</span>
                    </div>
                    
                    <!-- Right: Dashboard Button (if logged in) -->
                    @if(Auth::guard('manager')->check() || Auth::guard('salesmen')->check())
                        <div>
                            <a href="{{ route('dashboard') }}" class="header-login-btn">
                                <span class="relative flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    <span>Dashboard</span>
                                </span>
                            </a>
                        </div>
                    @endif
                </div>
            </header>

            <!-- 2. Hero Body -->
            <section class="max-w-[1440px] mx-auto px-6 sm:px-12 lg:px-16 pt-16 pb-[65px] min-h-[520px] flex items-center">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center w-full">
                    
                    <!-- Left Column (45%) -->
                    <div class="lg:col-span-5 text-center lg:text-left relative z-10">
                        <span class="inline-flex items-center gap-2 px-[18px] py-[10px] rounded-full bg-gradient-to-r from-[#8b35ff] to-[#e53bb8] text-white text-[10px] font-black uppercase tracking-widest">
                            ✦ AI-POWERED SYSTEM
                        </span>
                        
                        <h1 class="heading-font text-5xl sm:text-6xl lg:text-[4rem] font-black leading-[1.05] tracking-tight mt-6 mb-6">
                            Smart Analysis.<br/>
                            Better <span class="dozee-gradient-text font-black">Promotions.</span>
                        </h1>
                        
                        <p class="text-[18px] text-[#c7d0e5] font-medium leading-[1.7] max-w-[580px] mx-auto lg:mx-0 my-8">
                            AI-powered Market Basket engine to discover customer buying patterns and generate bundle promotions.
                        </p>
                        
                        <!-- CTA gradient with hover shift and soft glow -->
                        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                            <a href="#access" class="dozee-cta-btn group select-none">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <span>Go to Access</span>
                                <svg class="w-4 h-4 text-white translate-x-0 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right Column (55% / Large Glowing Video Card) -->
                    <div class="lg:col-span-7 flex justify-center lg:justify-end">
                        <div class="dozee-video-card">
                            <!-- Inner card with dark navy background -->
                            <div class="relative w-full h-full rounded-[19px] overflow-hidden bg-[#030B2D]">
                                
                                <!-- Custom Overlay Controls (Top-Right) -->
                                <div class="absolute top-4 right-4 z-30 flex items-center gap-2">
                                    <!-- Play/Pause Button -->
                                    <button id="custom-play-pause-btn" class="w-9 h-9 rounded-full bg-black/60 backdrop-blur text-white flex items-center justify-center hover:bg-black/85 hover:shadow-[0_0_15px_rgba(128,56,255,0.6)] hover:scale-105 active:scale-95 transition-all duration-200" title="Play/Pause">
                                        <!-- Pause Icon (Default playing) -->
                                        <svg id="ctrl-pause-icon" class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                            <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                        </svg>
                                        <!-- Play Icon (Hidden) -->
                                        <svg id="ctrl-play-icon" class="w-3.5 h-3.5 fill-current hidden" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Mute/Unmute Button -->
                                    <button id="custom-mute-btn" class="w-9 h-9 rounded-full bg-black/60 backdrop-blur text-white flex items-center justify-center hover:bg-black/85 hover:shadow-[0_0_15px_rgba(36,139,255,0.6)] hover:scale-105 active:scale-95 transition-all duration-200" title="Mute/Unmute">
                                        <!-- Muted Icon (Default muted) -->
                                        <svg id="ctrl-muted-icon" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                            <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.21.05-.42.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                        </svg>
                                        <!-- Unmuted Icon (Hidden) -->
                                        <svg id="ctrl-unmuted-icon" class="w-4 h-4 fill-current hidden" viewBox="0 0 24 24">
                                            <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- White overlay title (non-blocking) -->
                                <div class="absolute top-4 left-4 z-10 text-[9px] font-black uppercase text-white tracking-widest pointer-events-none bg-[#030B2D]/60 backdrop-blur px-2.5 py-1 rounded">
                                    DO’ZEE SYSTEM OVERVIEW
                                </div>
                                
                                <!-- Vimeo Iframe directly accessible with autoplay & mute enabled -->
                                <iframe 
                                    id="vimeo-iframe"
                                    class="w-full h-full rounded-[18px] border-none overflow-hidden"
                                    src="https://player.vimeo.com/video/1170857415?autoplay=1&muted=1&loop=1&background=0&controls=1" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </section>
        </div>

        <!-- 3. FEATURE CARDS SECTION (Clean white/light-grey background) -->
        <section class="py-20 px-6 bg-[#F5F7FC]">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Card 1 (Purple Accent) -->
                    <div class="bg-white p-8 rounded-[1.8rem] shadow-sm border border-gray-100 flex flex-col justify-between relative overflow-hidden group feature-card-hover cursor-pointer">
                        <div>
                            <!-- Coloured Icon Container -->
                            <div class="w-12 h-12 rounded-2xl bg-[#8038FF]/10 text-[#8038FF] flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <circle cx="18" cy="5" r="3" />
                                    <circle cx="6" cy="12" r="3" />
                                    <circle cx="18" cy="19" r="3" />
                                    <line x1="8.5" y1="10.5" x2="15.5" y2="6.5" stroke-width="2" />
                                    <line x1="8.5" y1="13.5" x2="15.5" y2="17.5" stroke-width="2" />
                                </svg>
                            </div>
                            <h3 class="heading-font text-lg font-bold text-slate-900 mb-2">Smart Market Basket Engine</h3>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                                Discover associations automatically using advanced algorithms.
                            </p>
                        </div>
                        <!-- Underline Accent and Arrow -->
                        <div class="flex items-end justify-between mt-8 pt-4 border-t border-gray-50/50">
                            <!-- Small accent underline -->
                            <div class="w-10 h-1 bg-[#8038FF] rounded-full"></div>
                            <!-- Small Arrow -->
                            <svg class="w-4 h-4 text-[#8038FF] translate-x-0 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </div>
                    </div>

                    <!-- Card 2 (Blue Accent) -->
                    <div class="bg-white p-8 rounded-[1.8rem] shadow-sm border border-gray-100 flex flex-col justify-between relative overflow-hidden group feature-card-hover cursor-pointer">
                        <div>
                            <!-- Coloured Icon Container -->
                            <div class="w-12 h-12 rounded-2xl bg-[#248BFF]/10 text-[#248BFF] flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.42 1.42 0 002.008 0l4.318-4.318a1.42 1.42 0 000-2.008L11.16 3.659A2.25 2.25 0 009.568 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                </svg>
                            </div>
                            <h3 class="heading-font text-lg font-bold text-slate-900 mb-2">Bundle Promotion</h3>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                                Convert rules into attractive and effective bundle promotions.
                            </p>
                        </div>
                        <!-- Underline Accent and Arrow -->
                        <div class="flex items-end justify-between mt-8 pt-4 border-t border-gray-50/50">
                            <!-- Small accent underline -->
                            <div class="w-10 h-1 bg-[#248BFF] rounded-full"></div>
                            <!-- Small Arrow -->
                            <svg class="w-4 h-4 text-[#248BFF] translate-x-0 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </div>
                    </div>

                    <!-- Card 3 (Orange Accent) -->
                    <div class="bg-white p-8 rounded-[1.8rem] shadow-sm border border-gray-100 flex flex-col justify-between relative overflow-hidden group feature-card-hover cursor-pointer">
                        <div>
                            <!-- Coloured Icon Container -->
                            <div class="w-12 h-12 rounded-2xl bg-[#FF7A30]/10 text-[#FF7A30] flex items-center justify-center mb-6 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                            </div>
                            <h3 class="heading-font text-lg font-bold text-slate-900 mb-2">Insights & Reports</h3>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                                Visualize patterns and analytics to make smarter decisions.
                            </p>
                        </div>
                        <!-- Underline Accent and Arrow -->
                        <div class="flex items-end justify-between mt-8 pt-4 border-t border-gray-50/50">
                            <!-- Small accent underline -->
                            <div class="w-10 h-1 bg-[#FF7A30] rounded-full"></div>
                            <!-- Small Arrow -->
                            <svg class="w-4 h-4 text-[#FF7A30] translate-x-0 group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 4. ENTERPRISE ACCESS SECTION (Clean white/light-grey background) -->
        <section id="access" class="py-24 px-6 bg-[#F5F7FC]">
            <div class="max-w-7xl mx-auto">
                
                <!-- Section Header -->
                <div class="text-center mb-16 select-none">
                    <h2 class="heading-font text-4xl sm:text-5xl font-black text-slate-900 tracking-tight">Enterprise Access</h2>
                    
                    <!-- Decorative Underline -->
                    <div class="flex items-center justify-center gap-1.5 mt-4">
                        <div class="w-12 h-1.5 bg-[#8038FF] rounded-full"></div>
                        <div class="w-12 h-1.5 bg-[#248BFF] rounded-full"></div>
                    </div>
                    
                    <p class="text-slate-500 font-semibold text-sm mt-5 tracking-wide">
                        Authorized personnel only. Please select your access point.
                    </p>
                </div>
                
                <!-- Access Portals (Manager + Salesmen) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 max-w-6xl mx-auto">
                    
                    <!-- MANAGER PORTAL CARD (Purple theme) -->
                    <div class="manager-portal-card p-8 md:p-10 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group">
                        <!-- Neon decoration glow overlay inside card -->
                        <div class="absolute -top-20 -right-20 w-44 h-44 bg-[#8038FF]/10 rounded-full blur-3xl group-hover:scale-150 transition-all duration-500 pointer-events-none"></div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
                            <!-- Left: Screen illustration -->
                            <div class="md:col-span-5 flex justify-center">
                                <svg viewBox="0 0 240 180" class="w-full max-w-[200px] h-auto drop-shadow-[0_15px_30px_rgba(128,56,255,0.25)]" xmlns="http://www.w3.org/2000/svg">
                                  <!-- Monitor Base/Stand -->
                                  <path d="M100 135 L140 135 L130 155 L110 155 Z" fill="#4B5563" />
                                  <rect x="80" y="155" width="80" height="6" rx="3" fill="#374151" />
                                  
                                  <!-- Monitor Frame -->
                                  <rect x="30" y="20" width="180" height="115" rx="8" fill="#1e1b4b" stroke="#8038FF" stroke-width="2" />
                                  <!-- Inner Screen -->
                                  <rect x="36" y="26" width="168" height="92" rx="4" fill="#0c0a21" />
                                  
                                  <!-- Charts on Screen -->
                                  <path d="M45 100 L60 80 L75 90 L90 60 L105 75 L120 45" fill="none" stroke="#E53BB8" stroke-width="2" stroke-linecap="round" />
                                  <path d="M45 100 L60 80 L75 90 L90 60 L105 75 L120 45 L120 110 L45 110 Z" fill="rgba(229,59,184,0.08)" />
                                  
                                  <rect x="135" y="70" width="8" height="40" rx="2" fill="#248BFF" />
                                  <rect x="150" y="55" width="8" height="55" rx="2" fill="#8038FF" />
                                  <rect x="165" y="80" width="8" height="30" rx="2" fill="#FF7A30" />
                                  <rect x="180" y="65" width="8" height="45" rx="2" fill="#16D887" />

                                  <circle cx="60" cy="50" r="14" fill="#0f172a" stroke="rgba(255,255,255,0.1)" stroke-width="1.5" />
                                  <path d="M60 36 A14 14 0 0 1 74 50 L60 50 Z" fill="#248BFF" />
                                  <path d="M60 50 L74 50 A14 14 0 0 1 60 64 Z" fill="#8038FF" />

                                  <!-- Keyboard -->
                                  <path d="M60 160 L180 160 L170 172 L70 172 Z" fill="#374151" stroke="#4B5563" stroke-width="1" />
                                  <line x1="72" y1="164" x2="168" y2="164" stroke="#6b7280" stroke-width="1.5" stroke-dasharray="2 2" />
                                  <line x1="76" y1="168" x2="164" y2="168" stroke="#6b7280" stroke-width="1.5" stroke-dasharray="4 2" />

                                  <!-- Desk Plant -->
                                  <g transform="translate(195, 120)">
                                    <path d="M5 25 L15 25 L12 40 L8 40 Z" fill="#FF7A30" />
                                    <path d="M10 25 C5 15 2 12 5 8 C8 12 9 18 10 25 Z" fill="#16D887" />
                                    <path d="M10 25 C15 15 18 12 15 8 C12 12 11 18 10 25 Z" fill="#10B981" />
                                    <path d="M10 25 C10 10 10 5 10 5 C10 5 13 10 10 25 Z" fill="#059669" />
                                  </g>

                                  <!-- Mouse -->
                                  <ellipse cx="185" cy="168" rx="4" ry="6" fill="#4B5563" />
                                </svg>
                            </div>
                            
                            <!-- Right: Details -->
                            <div class="md:col-span-7">
                                <!-- User Icon + Portal Title -->
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-[#8038FF]/20 border border-[#8038FF]/40 text-[#8038FF] flex items-center justify-center shadow-[0_0_15px_rgba(128,56,255,0.3)] select-none">
                                        <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="heading-font text-2xl font-black text-white">Manager Portal</h3>
                                        <p class="text-[9px] font-black text-[#E53BB8] tracking-widest uppercase mt-0.5">FULL ADMIN CONTROL</p>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-300 font-medium leading-relaxed mb-8">
                                    Manage categories, store inventory, association rule algorithms, and overall business operations.
                                </p>
                            </div>
                        </div>

                        <!-- Buttons (Manager login + register) -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 mt-6 border-t border-white/10 relative z-10">
                            <!-- Solid Purple Gradient Login -->
                            <a href="{{ route('manager.login') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl text-white font-bold text-xs uppercase tracking-wider bg-gradient-to-r from-[#8038FF] to-[#E53BB8] hover:shadow-[0_0_20px_rgba(128,56,255,0.45)] hover:scale-102 active:scale-95 transition-all duration-200">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <span>Manager Login</span>
                            </a>
                            <!-- Outline Register -->
                            <a href="{{ route('manager.register') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl border-2 border-white/20 text-white font-bold text-xs uppercase tracking-wider hover:bg-white/5 hover:border-white/60 hover:scale-102 active:scale-95 transition-all duration-200">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V19C19 17.9391 18.5786 16.9217 17.8284 16.1716C17.0783 15.4214 16.0609 15 15 15H9C7.93913 15 6.92172 15.4214 6.17157 16.1716C5.42143 16.9217 5 17.9391 5 19V21" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <span>Register Manager</span>
                            </a>
                        </div>
                    </div>

                    <!-- SALESMAN PORTAL CARD (Green theme) -->
                    <div class="salesmen-portal-card p-8 md:p-10 rounded-[2.5rem] flex flex-col justify-between relative overflow-hidden group">
                        <!-- Neon decoration glow overlay inside card -->
                        <div class="absolute -top-20 -right-20 w-44 h-44 bg-[#16D887]/10 rounded-full blur-3xl group-hover:scale-150 transition-all duration-500 pointer-events-none"></div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
                            <!-- Left: Screen illustration -->
                            <div class="md:col-span-5 flex justify-center">
                                <svg viewBox="0 0 240 180" class="w-full max-w-[200px] h-auto drop-shadow-[0_15px_30px_rgba(22,216,135,0.25)]" xmlns="http://www.w3.org/2000/svg">
                                  <!-- POS Stand/Body -->
                                  <path d="M70 110 L170 110 L160 145 L80 145 Z" fill="#374151" stroke="#4B5563" stroke-width="1.5" />
                                  <!-- Cash Drawer Base -->
                                  <rect x="60" y="145" width="120" height="15" rx="4" fill="#1f2937" />
                                  <rect x="110" y="152" width="20" height="4" rx="2" fill="#4b5563" />

                                  <!-- Tablet / Screen -->
                                  <rect x="115" y="70" width="10" height="45" fill="#4b5563" />
                                  <rect x="50" y="30" width="140" height="90" rx="8" fill="#111827" stroke="#16D887" stroke-width="2" transform="rotate(-5, 120, 75)" />
                                  <rect x="56" y="36" width="128" height="78" rx="4" fill="#061e14" transform="rotate(-5, 120, 75)" />
                                  
                                  <!-- POS UI on Screen -->
                                  <g transform="rotate(-5, 120, 75)">
                                    <circle cx="120" cy="70" r="18" fill="rgba(22, 216, 135, 0.15)" stroke="#16D887" stroke-width="1.5" />
                                    <path d="M112 64 H116 L120 74 H128 L132 66" stroke="#16D887" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                                    <circle cx="120" cy="78" r="2" fill="#16D887" />
                                    <circle cx="128" cy="78" r="2" fill="#16D887" />
                                    
                                    <rect x="70" y="90" width="40" height="5" rx="2.5" fill="rgba(255,255,255,0.15)" />
                                    <rect x="70" y="100" width="60" height="5" rx="2.5" fill="rgba(255,255,255,0.08)" />
                                    <rect x="140" y="90" width="30" height="15" rx="4" fill="#16D887" />
                                  </g>

                                  <!-- Paper Receipt -->
                                  <path d="M90 140 L90 175 C90 178, 93 180, 95 180 L145 180 C147 180, 150 178, 150 175 L150 140 Z" fill="#f8fafc" stroke="#e2e8f0" stroke-width="1" />
                                  <line x1="100" y1="148" x2="140" y2="148" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="2 2" />
                                  <line x1="100" y1="154" x2="130" y2="154" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="3 1" />
                                  <line x1="100" y1="160" x2="140" y2="160" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="1 3" />
                                  <line x1="100" y1="166" x2="135" y2="166" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="2 2" />
                                  
                                  <!-- Desk Plant -->
                                  <g transform="translate(190, 110)">
                                    <path d="M5 25 L15 25 L12 40 L8 40 Z" fill="#a855f7" />
                                    <path d="M10 25 C5 15 2 12 5 8 C8 12 9 18 10 25 Z" fill="#16D887" />
                                    <path d="M10 25 C15 15 18 12 15 8 C12 12 11 18 10 25 Z" fill="#34D399" />
                                  </g>
                                </svg>
                            </div>
                            
                            <!-- Right: Details -->
                            <div class="md:col-span-7">
                                <!-- Cart Icon + Portal Title -->
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-[#16D887]/20 border border-[#16D887]/40 text-[#16D887] flex items-center justify-center shadow-[0_0_15px_rgba(22,216,135,0.3)] select-none">
                                        <svg class="w-6 h-6 text-[#16D887]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="heading-font text-2xl font-black text-white">Salesmen Portal</h3>
                                        <p class="text-[10px] font-black text-[#16D887] tracking-widest uppercase mt-0.5">SALES TRANSACTION MODULE</p>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-300 font-medium leading-relaxed mb-8">
                                    Record daily store sales, view real-time item recommendation rules, and print bundle discounts.
                                </p>
                            </div>
                        </div>

                        <!-- Buttons: Salesmen Login only (Full-width / wide button) -->
                        <div class="pt-6 mt-6 border-t border-white/10 relative z-10">
                            <!-- Solid Green Gradient Login -->
                            <a href="{{ route('salesmen.login') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl text-white font-bold text-xs uppercase tracking-wider bg-gradient-to-r from-[#16D887] to-[#10B981] hover:shadow-[0_0_20px_rgba(22,216,135,0.45)] hover:scale-101 active:scale-95 transition-all duration-200">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                <span>Salesmen Login</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 5. FOOTER -->
        <footer class="py-12 bg-white border-t border-gray-100 relative z-10 select-none">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center text-slate-400 text-xs font-bold uppercase tracking-wider">
                &copy; 2026 Do’Zee. All rights reserved.
            </div>
        </footer>

        <!-- Vimeo SDK and Custom Playback Controls Script -->
        <script src="https://player.vimeo.com/api/player.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const iframe = document.getElementById('vimeo-iframe');
                const player = new Vimeo.Player(iframe);

                const muteBtn = document.getElementById('custom-mute-btn');
                const playPauseBtn = document.getElementById('custom-play-pause-btn');

                const ctrlMutedIcon = document.getElementById('ctrl-muted-icon');
                const ctrlUnmutedIcon = document.getElementById('ctrl-unmuted-icon');
                const ctrlPlayIcon = document.getElementById('ctrl-play-icon');
                const ctrlPauseIcon = document.getElementById('ctrl-pause-icon');

                let isMuted = true;
                let isPlaying = true;

                // Sync mute button click
                muteBtn.addEventListener('click', () => {
                    isMuted = !isMuted;
                    player.setMuted(isMuted).then(() => {
                        if (isMuted) {
                            ctrlMutedIcon.classList.remove('hidden');
                            ctrlUnmutedIcon.classList.add('hidden');
                        } else {
                            ctrlMutedIcon.classList.add('hidden');
                            ctrlUnmutedIcon.classList.remove('hidden');
                        }
                    }).catch(error => {
                        console.error('Error toggling mute:', error);
                    });
                });

                // Sync play/pause button click
                playPauseBtn.addEventListener('click', () => {
                    isPlaying = !isPlaying;
                    if (isPlaying) {
                        player.play().then(() => {
                            ctrlPauseIcon.classList.remove('hidden');
                            ctrlPlayIcon.classList.add('hidden');
                        }).catch(error => {
                            console.error('Error playing video:', error);
                        });
                    } else {
                        player.pause().then(() => {
                            ctrlPauseIcon.classList.add('hidden');
                            ctrlPlayIcon.classList.remove('hidden');
                        }).catch(error => {
                            console.error('Error pausing video:', error);
                        });
                    }
                });

                // Listen to native events from the player to sync custom button states
                player.on('play', () => {
                    isPlaying = true;
                    ctrlPauseIcon.classList.remove('hidden');
                    ctrlPlayIcon.classList.add('hidden');
                });
                player.on('pause', () => {
                    isPlaying = false;
                    ctrlPauseIcon.classList.add('hidden');
                    ctrlPlayIcon.classList.remove('hidden');
                });
                player.on('volumechange', (data) => {
                    player.getMuted().then(muted => {
                        isMuted = muted || data.volume === 0;
                        if (isMuted) {
                            ctrlMutedIcon.classList.remove('hidden');
                            ctrlUnmutedIcon.classList.add('hidden');
                        } else {
                            ctrlMutedIcon.classList.add('hidden');
                            ctrlUnmutedIcon.classList.remove('hidden');
                        }
                    });
                });
            });
        </script>
    </body>
</html>
