<x-app-layout>
    @php
    if (!function_exists('getRuleWithCodes')) {
        function getRuleWithCodes($rule, $itemCodes) {
            $antecedents = explode('+', $rule->antecedent);
            $antecedentCodes = [];
            foreach ($antecedents as $antId) {
                $antecedentCodes[] = $itemCodes[$antId] ?? 'Item #' . $antId;
            }
            $consequentCode = $itemCodes[$rule->consequent] ?? 'Item #' . $rule->consequent;
            return implode(' + ', $antecedentCodes) . ' → ' . $consequentCode;
        }
    }
    @endphp
    <style>
        /* Apple-Style Modal Backdrop Blur & Fade */
        .apple-backdrop {
            transition: opacity 320ms cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 320ms cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(0px);
            opacity: 0;
        }
        .apple-backdrop:not(.hidden) {
            display: flex !important;
        }
        .apple-backdrop.active {
            opacity: 1;
            backdrop-filter: blur(16px);
        }

        /* Apple-Style Modal Container Zoom & Fade */
        .apple-modal-content {
            transform: scale(0.92);
            opacity: 0;
            transition: transform 320ms cubic-bezier(0.16, 1, 0.3, 1), opacity 320ms cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.15), 0 0 50px -10px rgba(139, 92, 246, 0.1);
        }
        .apple-modal-content.active {
            transform: scale(1);
            opacity: 1;
        }

        /* Micro interactions for Buttons */
        .apple-btn {
            transition: transform 150ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 150ms cubic-bezier(0.16, 1, 0.3, 1), background-color 150ms ease;
        }
        .apple-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(139, 92, 246, 0.25);
        }
        .apple-btn:active {
            transform: translateY(0) scale(0.98);
        }

        /* Micro interactions for KPI Cards */
        .apple-kpi-card {
            transition: transform 300ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 300ms cubic-bezier(0.16, 1, 0.3, 1), border-color 300ms ease;
        }
        .apple-kpi-card:hover {
            transform: translateY(-3px);
            border-color: rgba(139, 92, 246, 0.3) !important;
            box-shadow: 0 12px 24px -8px rgba(139, 92, 246, 0.15), 0 0 20px -5px rgba(139, 92, 246, 0.08);
        }

        /* Rule Highlight with Premium Gradient & Subtle Glow Pulse */
        .apple-rule-highlight {
            background: linear-gradient(135deg, rgba(245, 243, 255, 0.8) 0%, rgba(237, 233, 254, 0.8) 100%);
            animation: applePulse 3.5s infinite ease-in-out;
            border: 1px solid rgba(139, 92, 246, 0.15) !important;
        }

        @keyframes applePulse {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(139, 92, 246, 0.02);
            }
            50% {
                box-shadow: 0 4px 20px 4px rgba(139, 92, 246, 0.08);
            }
        }
        .weka-log-container {
            display: flex;
            flex-direction: column;
            min-height: auto;
            height: auto;
        }

        .weka-log-output {
            flex-grow: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .weka-config-container {
            min-height: auto;
            height: auto;
            display: flex;
            flex-direction: column;
            justify-content: start;
        }

        /* FIX: WEKA RUN BUTTON VISIBILITY + SPACING */

        /* 1. Tambah ruang bawah section filters */
        .weka-parameters {
            margin-bottom: 0px;
            padding-bottom: 0px;
        }

        /* 2. Besarkan jarak antara sliders dan button */
        .weka-controls {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* 3. FIX RUN BUTTON supaya tak overlap */
        .run-weka-button {
            margin-top: 20px;
            position: relative;
            z-index: 10;
            padding: 12px 18px;
            background: #6C5CE7;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* 4. Kalau button masih tersembunyi */
        .weka-left-panel {
            min-height: auto;
            overflow: visible;
        }

        /* 5. Jarak bawah weka-container */
        .weka-container {
            padding-bottom: 40px;
        }

        /* Upload Component Styles */
        .upload-dragover {
            border-color: #6C5CE7 !important;
            background-color: rgba(108, 92, 231, 0.08) !important;
            transform: scale(1.015);
            box-shadow: 0 4px 16px rgba(108, 92, 231, 0.15), 0 0 0 4px rgba(108, 92, 231, 0.08);
        }
        .fade-in {
            animation: fadeIn 300ms cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Primary Purple Override to #6C5CE7 */
        .bg-purple-600, .bg-purple-500, .bg-indigo-600 { background-color: #6C5CE7 !important; }
        .hover\:bg-purple-700:hover, .hover\:bg-purple-600:hover, .hover\:bg-indigo-700:hover { background-color: #5b4ec7 !important; }
        .text-purple-600, .text-purple-500, .text-indigo-600 { color: #6C5CE7 !important; }
        .border-purple-600, .border-purple-500, .border-indigo-600 { border-color: #6C5CE7 !important; }
        .focus\:ring-purple-500:focus, .focus\:ring-indigo-500:focus { --tw-ring-color: #6C5CE7 !important; }
        .accent-purple-600, .accent-indigo-600 { accent-color: #6C5CE7 !important; }
    </style>

    <!-- Top Header -->
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4 bg-white">
            <div>
                <!-- Breadcrumb -->
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight flex items-center gap-3">
                    <span class="p-2 bg-[#6C5CE7] rounded-xl text-white shadow-md shadow-purple-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </span>
                    Apriori algorithm
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-1">Market basket analysis using WEKA Apriori engine</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100 shadow-sm">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-[#6C5CE7] animate-pulse"></span>
                    WEKA Association Engine
                </span>
                <span class="text-slate-300">|</span>
                <span>Date: Jun 22, 2026</span>
            </div>
        </div>
    </x-slot>

    <script>
        document.title = "Apriori algorithm (WEKA)";
    </script>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 flex items-start gap-3 shadow-sm animate-fade-in-down">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold text-emerald-800 text-xs">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ── SECTION 2: KPI SUMMARY (MUST BE AT TOP) ────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                {{-- Total Transactions --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group apple-kpi-card flex items-center gap-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="w-5 h-5 text-[#6C5CE7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Total Sales</p>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($stats['total_sales']) }}</h3>
                    </div>
                </div>

                {{-- Items Tracked --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group apple-kpi-card flex items-center gap-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="w-5 h-5 text-[#6C5CE7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Items Tracked</p>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($stats['total_products']) }}</h3>
                    </div>
                </div>

                {{-- Rules Found --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group apple-kpi-card flex items-center gap-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="w-5 h-5 text-[#6C5CE7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A1 1 0 0112 3.414L16.586 8a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Rules Found</p>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($stats['rules_count']) }}</h3>
                    </div>
                </div>

                {{-- Max Lift --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group apple-kpi-card flex items-center gap-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="w-5 h-5 text-[#6C5CE7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Max Lift</p>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($stats['top_lift'], 2) }}</h3>
                    </div>
                </div>

                {{-- Avg Confidence --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden group apple-kpi-card flex items-center gap-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                        <svg class="w-5 h-5 text-[#6C5CE7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Avg Confidence</p>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($stats['avg_confidence'] * 100, 1) }}%</h3>
                    </div>
                </div>
            </div>

            {{-- ── SECTIONS 3 & 4: WEKA PARAMETERS + UPLOAD (LEFT) & WEKA CLI OUTPUT LOG (RIGHT) ────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 weka-container">
                {{-- Parameters & Upload Data --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-6 weka-config-container weka-left-panel">
                    <div class="flex items-center gap-2 border-b border-slate-50 pb-3">
                        <span class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-4 h-4 text-[#6C5CE7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m10 4a2 2 0 100-4m0 4a2 2 0 110-4M14 4h6m-6 8h6m-6 8h6m-14 0h6m-14-8h6m-14-4h6"></path></svg>
                        </span>
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">WEKA Parameters & Data Upload</h4>
                    </div>

                    {{-- Upload transaction data --}}
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-black text-slate-400">Upload Transaction Data</label>
                        
                        <!-- Hidden File Input -->
                        <input type="file" id="weka-file-input" accept=".csv, .arff" class="hidden">
                        
                        <!-- Drop Zone -->
                        <div id="drop-zone" class="border-2 border-dashed border-slate-200 rounded-xl p-3.5 text-center cursor-pointer transition-all duration-300 bg-slate-50/50 hover:bg-slate-50 hover:border-purple-500 group select-none relative overflow-hidden flex flex-col justify-center min-h-[110px]">
                            
                            <!-- Default Upload View -->
                            <div id="upload-default-view" class="space-y-1.5 transition-all duration-300">
                                <!-- Upload Icon -->
                                <svg class="w-6 h-6 text-slate-400 group-hover:text-purple-500 mx-auto transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                
                                <!-- Instructions -->
                                <div>
                                    <span class="text-[11px] font-bold text-slate-600 block group-hover:text-[#6C5CE7] transition-colors">Drag & drop or click to upload file</span>
                                    <span class="text-[9px] text-slate-400 block mt-0.5">Accepts only .csv or .arff files</span>
                                </div>
                                
                                <!-- Browse Button -->
                                <button type="button" id="browse-btn" class="px-3 py-1 bg-white border border-slate-200 text-slate-700 hover:text-purple-600 hover:border-purple-300 text-[10px] font-bold rounded-lg shadow-sm transition-all duration-200 apple-btn">
                                    Browse File
                                </button>
                            </div>

                            <!-- File Preview / Success View (Hidden by default) -->
                            <div id="upload-success-view" class="hidden opacity-0 flex flex-col items-center justify-center space-y-1.5 transition-all duration-300">
                                <!-- Success/Document Icon -->
                                <div class="relative">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white shadow">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                </div>

                                <div class="text-center px-2">
                                    <p id="file-name-preview" class="text-xs font-bold text-slate-800 truncate max-w-[180px]"></p>
                                    <p id="file-size-preview" class="text-[9px] text-slate-400 font-bold mt-0.5"></p>
                                </div>

                                <button type="button" id="remove-file-btn" class="px-2 py-0.5 text-[9px] font-extrabold text-rose-500 hover:bg-rose-50 rounded transition-colors focus:outline-none">
                                    Remove File
                                </button>
                            </div>
                        </div>
                        
                        <!-- Error Message -->
                        <div id="upload-error-msg" class="hidden text-[10px] text-rose-500 font-bold flex items-center gap-1 px-1 mt-1 transition-all duration-300">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="error-text">Invalid file format. Please upload CSV or ARFF file only.</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('analysis.weka.run') }}" class="weka-controls">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 weka-parameters">
                            {{-- Event --}}
                            <div class="space-y-1">
                                <label for="event_name" class="text-[10px] uppercase font-black text-slate-400">Specific Event</label>
                                <select name="event_name" id="event_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">All Events</option>
                                    @foreach($eventNames as $name)
                                        <option value="{{ $name }}" {{ $eventName == $name ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Representative --}}
                            <div class="space-y-1">
                                <label for="salesman_id" class="text-[10px] uppercase font-black text-slate-400">Sales Representative</label>
                                <select name="salesman_id" id="salesman_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">All Staff</option>
                                    @foreach($salesmen as $id => $name)
                                        <option value="{{ $id }}" {{ $salesmanId == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Rules Output Count --}}
                        <div class="space-y-1">
                            <label for="num_rules" class="text-[10px] uppercase font-black text-slate-400">Rule Limit Input (Limit calculation scope)</label>
                            <input type="number" name="num_rules" id="num_rules" min="1" max="100" value="{{ $numRules }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Min Support --}}
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label for="support" class="text-[10px] uppercase font-black text-slate-400">Min Support</label>
                                    <span id="support-val" class="px-1.5 py-0.5 bg-purple-50 text-purple-600 rounded text-[10px] font-black tabular-nums">{{ number_format($minSupport, 2) }}</span>
                                </div>
                                <input type="range" name="support" id="support" min="0.01" max="0.5" step="0.01" value="{{ $minSupport }}" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-purple-600" oninput="document.getElementById('support-val').innerText = parseFloat(this.value).toFixed(2)">
                            </div>

                            {{-- Min Confidence --}}
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <label for="confidence" class="text-[10px] uppercase font-black text-slate-400">Min Confidence</label>
                                    <span id="confidence-val" class="px-1.5 py-0.5 bg-purple-50 text-purple-600 rounded text-[10px] font-black tabular-nums">{{ number_format($minConfidence, 2) }}</span>
                                </div>
                                <input type="range" name="confidence" id="confidence" min="0.1" max="1.0" step="0.05" value="{{ $minConfidence }}" class="w-full h-1 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-purple-600" oninput="document.getElementById('confidence-val').innerText = parseFloat(this.value).toFixed(2)">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-[#6C5CE7] hover:bg-[#5b4ec7] text-white rounded-xl flex items-center justify-center gap-2 text-xs font-extrabold shadow-sm transition-all uppercase tracking-wider apple-btn run-weka-button">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            Run WEKA Apriori Engine
                        </button>
                    </form>
                </div>

                {{-- CLI log display --}}
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between weka-log-container">
                    <div class="flex items-center justify-between border-b border-slate-50 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 bg-purple-50 text-[#6C5CE7] rounded-lg">
                                <svg class="w-4 h-4 text-[#6C5CE7]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A1 1 0 0112 3.414L16.586 8a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">WEKA CLI Output Log</h4>
                        </div>
                        <button onclick="openWekaLogModal()" class="text-[10px] font-black uppercase text-purple-600 bg-purple-50 hover:bg-purple-100 px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 apple-btn">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            Expand View
                        </button>
                    </div>
                    <div onclick="openWekaLogModal()" class="group relative cursor-pointer rounded-xl overflow-hidden shadow-inner mt-4 flex-grow weka-log-output">
                        <pre class="bg-slate-900 text-slate-100 p-4 text-[10px] font-mono h-full leading-relaxed overflow-y-auto select-none">{{ $wekaLog }}</pre>
                        <div class="absolute inset-0 bg-slate-950/0 group-hover:bg-slate-950/10 transition-colors flex items-center justify-center">
                            <span class="opacity-0 group-hover:opacity-100 bg-slate-900/90 text-white text-[10px] font-bold px-3 py-2 rounded-xl shadow-md border border-slate-800 transition-opacity flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Click to Expand Fullscreen
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── SECTIONS 5 & 6: TOP 3 RULES TABLE (LEFT) & BEST RULE INSIGHT (RIGHT) ────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                {{-- Section 5: Association Rules Table --}}
                <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-50">
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">TOP 3 WEKA APRIORI ASSOCIATION RULES</h3>
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-600 rounded-full text-[9px] font-black">
                                Sorted by highest Lift descending
                            </span>
                        </div>

                        <div class="overflow-x-auto" id="tab-rules">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                        <th class="py-3 px-2">No</th>
                                        <th class="py-3 px-2">Rule (X → Y)</th>
                                        <th class="py-3 px-2 text-center">Support</th>
                                        <th class="py-3 px-2 text-center">Confidence</th>
                                        <th class="py-3 px-2 text-center">Lift</th>
                                        <th class="py-3 px-2 text-center">Conviction</th>
                                        <th class="py-3 px-2 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 text-xs">
                                    @php $rank = 1; @endphp
                                    @forelse($results->sortByDesc('lift')->take(3) as $rule)
                                        @php
                                            $convValue = '1.00';
                                            $cleanRuleText = $rule->rule_text;
                                            if (preg_match('/\[conv:(.*?)\]/', $rule->rule_text, $convMatch)) {
                                                $convValue = $convMatch[1];
                                                $cleanRuleText = trim(str_replace($convMatch[0], '', $rule->rule_text));
                                            }
                                            // Convert arrow to → inside rule text for display
                                            $cleanRuleText = str_replace('==>', '→', $cleanRuleText);
                                        @endphp
                                        <tr class="odd:bg-slate-50/40 even:bg-white hover:bg-purple-50/20 transition-colors">
                                            <td class="py-4 px-2 font-black text-slate-400">{{ $rank++ }}</td>
                                            <td class="py-4 px-2">
                                                <span class="font-bold text-slate-800 block" title="{{ $cleanRuleText }}">{{ getRuleWithCodes($rule, $itemCodes) }}</span>
                                            </td>
                                            <td class="py-4 px-2 text-center font-bold text-slate-700">{{ round($rule->support * 100, 2) }}%</td>
                                            <td class="py-4 px-2 text-center font-bold text-purple-600">{{ round($rule->confidence * 100) }}%</td>
                                            <td class="py-4 px-2 text-center font-bold text-slate-700">{{ number_format($rule->lift, 2) }}</td>
                                            <td class="py-4 px-2 text-center font-bold text-slate-700">{{ $convValue }}</td>
                                            <td class="py-4 px-2 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button onclick="openRuleDetailsModal('{{ $rule->rule_id }}', '{{ addslashes($cleanRuleText) }}', '{{ addslashes(getRuleWithCodes($rule, $itemCodes)) }}', '{{ round($rule->support * 100, 2) }}%', '{{ round($rule->confidence * 100) }}%', '{{ number_format($rule->lift, 2) }}', '{{ $convValue }}')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded text-[9px] uppercase tracking-wider transition-colors apple-btn" title="View: {{ $cleanRuleText }}">
                                                        View
                                                    </button>
                                                    @if(Auth::guard('manager')->check() && $rule->antecedent && $rule->consequent)
                                                        <button onclick="openBundleModal('{{ $rule->rule_id }}', '{{ addslashes($cleanRuleText) }}', '{{ round($rule->support * 100, 2) }}', '{{ round($rule->confidence * 100) }}', '{{ number_format($rule->lift, 3) }}')" class="px-2.5 py-1 bg-[#6C5CE7] hover:bg-[#5b4ec7] text-white font-bold rounded text-[9px] uppercase tracking-wider transition-colors apple-btn">
                                                            Create Bundle
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-slate-400 italic">No association rules calculated yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Section 6: Best Rule Insight --}}
                <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest border-b border-slate-50 pb-2.5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            Best Rule Insight
                        </h3>
                        @if($bestRule)
                            @php
                                $bestConv = '1.00';
                                $bestCleanText = $bestRule->rule_text;
                                if (preg_match('/\[conv:(.*?)\]/', $bestRule->rule_text, $match)) {
                                    $bestConv = $match[1];
                                    $bestCleanText = trim(str_replace($match[0], '', $bestRule->rule_text));
                                }
                                $bestCodeText = getRuleWithCodes($bestRule, $itemCodes);
                                $bestParts = explode('→', $bestCodeText);
                                $bestAnteCode = trim($bestParts[0] ?? 'Unknown');
                                $bestConsCode = trim($bestParts[1] ?? 'Unknown');
                            @endphp
                            <div class="space-y-4 mt-4">
                                <div class="p-3.5 bg-purple-50/50 rounded-xl border border-purple-100/50">
                                    <p class="text-[9px] font-black text-purple-600 uppercase tracking-wider mb-1">Optimal Rule Combination</p>
                                    <p class="text-xs font-bold text-slate-800 leading-tight">
                                        {{ $bestAnteCode }} <span class="text-purple-600">➔</span> {{ $bestConsCode }}
                                    </p>
                                </div>
                                <div class="space-y-2 text-xs">
                                    <div class="flex justify-between border-b border-slate-50 pb-1.5">
                                        <span class="text-slate-400 font-semibold">Support</span>
                                        <span class="font-extrabold text-slate-800 tabular-nums">{{ round($bestRule->support * 100, 2) }}%</span>
                                    </div>
                                    <div class="flex justify-between border-b border-slate-50 pb-1.5">
                                        <span class="text-slate-400 font-semibold">Confidence</span>
                                        <span class="font-extrabold text-purple-600 tabular-nums">{{ round($bestRule->confidence * 100) }}%</span>
                                    </div>
                                    <div class="flex justify-between border-b border-slate-50 pb-1.5">
                                        <span class="text-slate-400 font-semibold">Lift</span>
                                        <span class="font-extrabold text-slate-800 tabular-nums">{{ number_format($bestRule->lift, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-400 font-semibold">Conviction</span>
                                        <span class="font-extrabold text-slate-800 tabular-nums">{{ $bestConv }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic mt-4">No rules calculated yet.</p>
                        @endif
                    </div>
                    @if($bestRule)
                        <button type="button" onclick="openRuleDetailsModal('{{ $bestRule->rule_id }}', '{{ addslashes($bestCleanText) }}', '{{ addslashes(getRuleWithCodes($bestRule, $itemCodes)) }}', '{{ round($bestRule->support * 100, 2) }}%', '{{ round($bestRule->confidence * 100) }}%', '{{ number_format($bestRule->lift, 2) }}', '{{ $bestConv }}')" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-[10px] rounded-lg tracking-wider uppercase transition-colors apple-btn mt-4" title="View: {{ $bestCleanText }}">
                            View Rule Details
                        </button>
                    @endif
                </div>
            </div>

            {{-- Visualization charts (Top Rules Visualization) --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-6">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest border-b border-slate-50 pb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    Market Rules Analytics Charts
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Lift Chart --}}
                    <div class="space-y-2">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Top Rules by Lift</h4>
                        <div class="relative h-48">
                            <canvas id="liftBarChart"></canvas>
                        </div>
                    </div>

                    {{-- Scatter Plot --}}
                    <div class="space-y-2">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Support vs Confidence Scatter</h4>
                        <div class="relative h-48">
                            <canvas id="scatterPlot"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── SECTION 7: BOTTOM NAVIGATION ────────────────────────────────── --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('analysis.weka.allRules') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#6C5CE7] hover:bg-[#5b4ec7] text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-md shadow-purple-100 hover:shadow-purple-200 apple-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                        View All Rules
                    </a>
                    <button type="button" onclick="exportTableCSV()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-colors apple-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </button>
                    <button type="button" onclick="exportTablePDF()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-colors apple-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </button>
                </div>
                <div>
                    <button type="button" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); setTimeout(() => document.getElementById('weka-file-input').click(), 400);" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-50 hover:bg-purple-100 text-[#6C5CE7] text-xs font-black uppercase tracking-wider rounded-xl transition-colors apple-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Run New Analysis
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- ── WEKA LOG MODAL (FULLSCREEN EXPANDED VIEW) ──────────────────── --}}
    <div id="wekaLogModal" class="fixed inset-0 bg-slate-900/80 z-50 flex items-center justify-center hidden apple-backdrop" onclick="closeWekaLogModalOnOutsideClick(event)">
        <div class="bg-slate-900 text-slate-100 rounded-2xl overflow-hidden apple-modal-content w-[95%] h-[90%] border border-slate-800 flex flex-col" id="wekaLogModalContent">
            {{-- Header --}}
            <div class="bg-slate-950 px-6 py-4 text-white flex justify-between items-center border-b border-slate-800">
                <h3 class="font-extrabold text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A1 1 0 0112 3.414L16.586 8a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    WEKA Output Log (Expanded View)
                </h3>
                <div class="flex items-center gap-3">
                    <button onclick="copyWekaLog()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 apple-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        Copy
                    </button>
                    <button onclick="downloadWekaLog()" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 apple-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download
                    </button>
                    <button onclick="closeWekaLogModal()" class="text-slate-400 hover:text-white transition-colors apple-btn ml-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-6 flex-1 overflow-hidden flex flex-col">
                <div class="bg-slate-950 rounded-xl border border-slate-800 p-6 flex-1 overflow-auto">
                    <pre id="expanded_log_content" class="text-xs text-slate-300 font-mono leading-relaxed whitespace-pre-wrap selection:bg-purple-500/30 select-text"></pre>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-slate-950 border-t border-slate-800 flex justify-end">
                <button type="button" onclick="closeWekaLogModal()" class="h-10 px-6 bg-slate-800 hover:bg-slate-700 text-white font-extrabold rounded-xl shadow-sm uppercase tracking-wider text-xs apple-btn">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- ── ALL ASSOCIATION RULES MODAL ──────────────────── --}}
    <div id="allRulesModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center hidden apple-backdrop" onclick="closeAllRulesModalOnOutsideClick(event)">
        <div class="bg-white rounded-2xl overflow-hidden apple-modal-content w-[95%] md:w-[850px] border border-slate-100/50 flex flex-col h-[80vh]" id="allRulesModalContent">
            {{-- Header --}}
            <div class="bg-purple-600 px-6 py-5 text-white flex justify-between items-center">
                <h3 class="font-extrabold text-lg flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586A1 1 0 0112 3.414L16.586 8a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    All Discovered Association Rules ({{ $allRules->count() }})
                </h3>
                <button onclick="closeAllRulesModal()" class="text-white/80 hover:text-white transition-colors apple-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Table container --}}
            <div class="p-6 flex-1 overflow-y-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-2">No</th>
                            <th class="py-3 px-2">Rule (X => Y)</th>
                            <th class="py-3 px-2 text-center">Support</th>
                            <th class="py-3 px-2 text-center">Confidence</th>
                            <th class="py-3 px-2 text-center">Lift</th>
                            <th class="py-3 px-2 text-center">Conviction</th>
                            <th class="py-3 px-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php $allRank = 1; @endphp
                        @foreach($allRules as $rule)
                            @php
                                $convVal = '1.00';
                                $cleanText = $rule->rule_text;
                                if (preg_match('/\[conv:(.*?)\]/', $rule->rule_text, $match)) {
                                    $convVal = $match[1];
                                    $cleanText = trim(str_replace($match[0], '', $rule->rule_text));
                                }
                            @endphp
                            <tr class="odd:bg-slate-50/40 even:bg-white hover:bg-purple-50/20 transition-colors">
                                <td class="py-3.5 px-2 font-black text-slate-400">{{ $allRank++ }}</td>
                                <td class="py-3.5 px-2 font-bold text-slate-800" title="{{ $cleanText }}">{{ getRuleWithCodes($rule, $itemCodes) }}</td>
                                <td class="py-3.5 px-2 text-center font-bold text-slate-700">{{ round($rule->support * 100, 2) }}%</td>
                                <td class="py-3.5 px-2 text-center font-bold text-purple-600">{{ round($rule->confidence * 100) }}%</td>
                                <td class="py-3.5 px-2 text-center font-bold text-slate-700">{{ number_format($rule->lift, 2) }}</td>
                                <td class="py-3.5 px-2 text-center font-bold text-slate-700">{{ $convVal }}</td>
                                <td class="py-3.5 px-2 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button onclick="openRuleDetailsModal('{{ $rule->rule_id }}', '{{ addslashes($cleanText) }}', '{{ addslashes(getRuleWithCodes($rule, $itemCodes)) }}', '{{ round($rule->support * 100, 2) }}%', '{{ round($rule->confidence * 100) }}%', '{{ number_format($rule->lift, 2) }}', '{{ $convVal }}')" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded text-[9px] uppercase tracking-wider transition-colors apple-btn" title="View: {{ $cleanText }}">
                                            VIEW
                                        </button>
                                        @if(Auth::guard('manager')->check() && $rule->antecedent && $rule->consequent)
                                            <button onclick="openBundleModal('{{ $rule->rule_id }}', '{{ addslashes($cleanText) }}', '{{ round($rule->support * 100, 2) }}', '{{ round($rule->confidence * 100) }}', '{{ number_format($rule->lift, 3) }}')" class="px-2 py-1 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded text-[9px] uppercase tracking-wider transition-colors apple-btn">
                                                Create Bundle
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closeAllRulesModal()" class="h-10 px-6 bg-slate-200 hover:bg-slate-300 text-slate-700 font-extrabold rounded-xl shadow-sm uppercase tracking-wider text-xs apple-btn">
                    Back to Dashboard
                </button>
            </div>
        </div>
    </div>

    {{-- ── CUSTOM MODAL (RULE DETAILS) ──────────────────── --}}
    <div id="ruleDetailsModal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center hidden apple-backdrop" onclick="closeRuleDetailsModalOnOutsideClick(event)">
        <div class="bg-white rounded-2xl overflow-hidden apple-modal-content w-[90%] md:w-[600px] border border-slate-100/50" id="ruleDetailsModalContent">
            {{-- Header --}}
            <div class="bg-purple-600 px-6 py-5 text-white flex justify-between items-center">
                <h3 class="font-extrabold text-lg flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Rule Details
                </h3>
                <button onclick="closeRuleDetailsModal()" class="text-white/80 hover:text-white transition-colors apple-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-6 text-sm">
                <div class="p-6 rounded-2xl space-y-3 apple-rule-highlight relative">
                    <div class="absolute top-4 right-4 flex items-center gap-1.5 bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-200 text-[9px] font-black">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        PERSISTED IN DB
                    </div>
                    <p class="text-[10px] font-black uppercase text-purple-600 tracking-wider">Discovered Buying Pattern</p>
                    <p id="rule_details_code_text" class="text-base font-black text-[#6C5CE7] leading-relaxed text-center break-words mt-2"></p>
                    <p id="rule_details_text" class="text-xs font-bold text-slate-500 leading-relaxed text-center break-words mt-1"></p>
                </div>

                {{-- KPI Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center text-xs">
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 flex flex-col justify-between apple-kpi-card shadow-sm">
                        <span class="text-slate-400 font-bold block uppercase text-[9px] mb-1">Support</span>
                        <span id="rule_details_support" class="text-sm font-black text-slate-700"></span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 flex flex-col justify-between apple-kpi-card shadow-sm">
                        <span class="text-slate-400 font-bold block uppercase text-[9px] mb-1">Confidence</span>
                        <span id="rule_details_confidence" class="text-sm font-black text-purple-600"></span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 flex flex-col justify-between apple-kpi-card shadow-sm">
                        <span class="text-slate-400 font-bold block uppercase text-[9px] mb-1">Lift</span>
                        <span id="rule_details_lift" class="text-sm font-black text-slate-700"></span>
                    </div>
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 flex flex-col justify-between apple-kpi-card shadow-sm">
                        <span class="text-slate-400 font-bold block uppercase text-[9px] mb-1">Conviction</span>
                        <span id="rule_details_conviction" class="text-sm font-black text-slate-700"></span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                @if(Auth::guard('manager')->check())
                    <button type="button" id="details_modal_create_bundle_btn" onclick="triggerBundleFromDetailsModal()" class="h-10 px-5 bg-purple-600 hover:bg-purple-700 text-white font-extrabold rounded-xl shadow-sm uppercase tracking-wider text-xs shadow-md shadow-purple-100 hover:shadow-purple-200 apple-btn">
                        Create Bundle
                    </button>
                @endif
                <button type="button" onclick="closeRuleDetailsModal()" class="h-10 px-6 bg-slate-200 hover:bg-slate-300 text-slate-700 font-extrabold rounded-xl shadow-sm uppercase tracking-wider text-xs apple-btn">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- ── SECTION 7: POPUP MODAL (CREATE BUNDLE FORM) ──────────────────── --}}
    <div id="bundleModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl w-[90%] md:w-[750px] lg:w-[850px] overflow-hidden animate-fade-in-up">
            <div class="bg-purple-600 px-8 py-6 text-white flex justify-between items-center">
                <h3 class="font-extrabold text-xl md:text-2xl flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Create Bundle from Rule
                </h3>
                <button onclick="closeBundleModal()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('promotions.store') }}" class="p-8 space-y-6 text-sm">
                @csrf
                <input type="hidden" name="rule_id" id="modal_rule_id" value="">
                <input type="hidden" name="rule_ids[]" id="modal_rule_id_array" value="">
                <input type="hidden" name="start_date" value="{{ now()->format('Y-m-d') }}">
                <input type="hidden" name="end_date" value="{{ now()->addDays(30)->format('Y-m-d') }}">

                {{-- Selected Rule Summary --}}
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 space-y-4">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Selected Rule Relationship</p>
                    <p id="modal_rule_text" class="text-sm font-black text-slate-800 leading-relaxed text-center break-words"></p>
                    <div class="grid grid-cols-3 gap-4 text-center text-xs pt-2">
                        <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-slate-400 font-bold block uppercase text-[9px] mb-0.5">Support</span>
                            <span id="modal_support" class="text-sm font-black text-slate-700"></span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-slate-400 font-bold block uppercase text-[9px] mb-0.5">Confidence</span>
                            <span id="modal_confidence" class="text-sm font-black text-purple-600"></span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                            <span class="text-slate-400 font-bold block uppercase text-[9px] mb-0.5">Lift</span>
                            <span id="modal_lift" class="text-sm font-black text-slate-700"></span>
                        </div>
                    </div>
                </div>

                {{-- Form fields --}}
                <div class="space-y-1">
                    <label for="promo_name" class="text-[11px] uppercase font-black tracking-widest text-slate-400 mb-1 ml-1 block">Bundle Name</label>
                    <input type="text" name="promo_name" id="promo_name" required class="w-full h-12 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="discount_type" class="text-[11px] uppercase font-black tracking-widest text-slate-400 mb-1 ml-1 block">Discount Type</label>
                        <select name="discount_type" id="discount_type" class="w-full h-12 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="Percentage">Percentage (%)</option>
                            <option value="Fixed">Fixed Amount</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label for="discount_value" class="text-[11px] uppercase font-black tracking-widest text-slate-400 mb-1 ml-1 block">Discount Value</label>
                        <input type="number" name="discount_value" id="discount_value" min="1" value="10" class="w-full h-12 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="description" class="text-[11px] uppercase font-black tracking-widest text-slate-400 mb-1 ml-1 block">Description</label>
                    <textarea name="description" id="description" required class="w-full h-28 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-5 border-t border-slate-100">
                    <button type="button" onclick="closeBundleModal()" class="h-12 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-xl transition-all uppercase tracking-wider text-xs">
                        Cancel
                    </button>
                    <button type="submit" class="h-12 px-8 bg-purple-600 hover:bg-purple-700 text-white font-extrabold rounded-xl shadow-sm transition-all uppercase tracking-wider text-xs shadow-md shadow-purple-100 hover:shadow-purple-200">
                        Create Bundle
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Load Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Switch tab mechanism
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(tabId).classList.remove('hidden');

            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-purple-600', 'text-purple-600', 'bg-white');
                b.classList.add('border-transparent', 'text-slate-400');
            });

            const activeBtn = document.getElementById('btn-' + tabId);
            activeBtn.classList.remove('border-transparent', 'text-slate-400');
            activeBtn.classList.add('border-purple-600', 'text-purple-600', 'bg-white');
        }

        // Modal triggers
        function openBundleModal(ruleId, ruleText, support, confidence, lift) {
            document.getElementById('modal_rule_id').value = ruleId;
            document.getElementById('modal_rule_id_array').value = ruleId;
            
            // Clean display representation
            const displayRuleText = ruleText.replace('==>', '➔').replace('→', '➔');
            document.getElementById('modal_rule_text').innerHTML = displayRuleText.replace('➔', ' <span class="text-purple-600 font-black px-3">➔</span> ');
            document.getElementById('modal_support').innerText = support + '%';
            document.getElementById('modal_confidence').innerText = confidence + '%';
            document.getElementById('modal_lift').innerText = lift;

            // Generate clean defaults splitting on whichever arrow exists
            const parts = ruleText.includes('==>') ? ruleText.split('==>') : ruleText.split('→');
            const ante = parts[0] ? parts[0].trim() : 'Product A';
            const cons = parts[1] ? parts[1].trim() : 'Product B';
            document.getElementById('promo_name').value = 'Apriori Deal: ' + ante + ' + ' + cons;
            document.getElementById('description').value = 'Get a special bundle discount on ' + cons + ' when purchased with ' + ante + '. Discovered by WEKA Apriori.';

            document.getElementById('bundleModal').classList.remove('hidden');
        }

        function closeBundleModal() {
            document.getElementById('bundleModal').classList.add('hidden');
        }

        // Rule Details Custom Modal
        let activeDetailsRuleId = null;
        let activeDetailsRuleText = "";
        let activeDetailsSupport = "";
        let activeDetailsConfidence = "";
        let activeDetailsLift = "";

        function openRuleDetailsModal(ruleId, ruleText, ruleCodeText, support, confidence, lift, conviction) {
            activeDetailsRuleId = ruleId;
            activeDetailsRuleText = ruleText;
            activeDetailsSupport = support;
            activeDetailsConfidence = confidence;
            activeDetailsLift = lift;

            document.getElementById('rule_details_code_text').innerHTML = ruleCodeText.replace('→', ' <span class="text-purple-600 font-black px-3">➔</span> ').replace('==>', ' <span class="text-purple-600 font-black px-3">➔</span> ');
            document.getElementById('rule_details_text').innerHTML = '( ' + ruleText.replace('==>', ' <span class="text-slate-400 font-bold px-1.5">➔</span> ').replace('→', ' <span class="text-slate-400 font-bold px-1.5">➔</span> ') + ' )';
            document.getElementById('rule_details_support').innerText = support;
            document.getElementById('rule_details_confidence').innerText = confidence;
            document.getElementById('rule_details_lift').innerText = lift;
            document.getElementById('rule_details_conviction').innerText = conviction;

            // Toggle Create Bundle button depending on whether manager is logged in & rule has antecedent/consequent
            const bundleBtn = document.getElementById('details_modal_create_bundle_btn');
            if (bundleBtn) {
                if (ruleId && ruleText.includes('==>')) {
                    bundleBtn.classList.remove('hidden');
                } else {
                    bundleBtn.classList.add('hidden');
                }
            }

            const modal = document.getElementById('ruleDetailsModal');
            const content = document.getElementById('ruleDetailsModalContent');
            
            modal.classList.remove('hidden');
            // Force repaint to register initial transition state
            void modal.offsetWidth;
            
            modal.classList.add('active');
            content.classList.add('active');
        }

        function closeRuleDetailsModal() {
            const modal = document.getElementById('ruleDetailsModal');
            const content = document.getElementById('ruleDetailsModalContent');
            
            modal.classList.remove('active');
            content.classList.remove('active');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 320);
        }

        function triggerBundleFromDetailsModal() {
            closeRuleDetailsModal();
            setTimeout(() => {
                const numericSupport = parseFloat(activeDetailsSupport);
                const numericConfidence = parseFloat(activeDetailsConfidence);
                openBundleModal(activeDetailsRuleId, activeDetailsRuleText, numericSupport, numericConfidence, activeDetailsLift);
            }, 350);
        }

        function closeRuleDetailsModalOnOutsideClick(event) {
            if (event.target.id === 'ruleDetailsModal') {
                closeRuleDetailsModal();
            }
        }

        // All Association Rules Modal Triggers
        function openAllRulesModal() {
            const modal = document.getElementById('allRulesModal');
            const content = document.getElementById('allRulesModalContent');
            
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            
            modal.classList.add('active');
            content.classList.add('active');
        }

        function closeAllRulesModal() {
            const modal = document.getElementById('allRulesModal');
            const content = document.getElementById('allRulesModalContent');
            
            modal.classList.remove('active');
            content.classList.remove('active');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 320);
        }

        function closeAllRulesModalOnOutsideClick(event) {
            if (event.target.id === 'allRulesModal') {
                closeAllRulesModal();
            }
        }

        // Weka Output Log Expanded Modal
        const rawWekaLog = `{!! addslashes($wekaLog) !!}`;

        function highlightLogText(text) {
            return text
                .replace(/(minimum support|min\. support|support)/gi, '<span class="text-purple-400 font-bold">$1</span>')
                .replace(/(minimum confidence|confidence|conf)/gi, '<span class="text-amber-400 font-bold">$1</span>')
                .replace(/(lift)/gi, '<span class="text-emerald-400 font-bold">$1</span>')
                .replace(/(apriori)/gi, '<span class="text-blue-400 font-bold">$1</span>');
        }

        function openWekaLogModal() {
            document.getElementById('expanded_log_content').innerHTML = highlightLogText(rawWekaLog);

            const modal = document.getElementById('wekaLogModal');
            const content = document.getElementById('wekaLogModalContent');
            
            modal.classList.remove('hidden');
            void modal.offsetWidth;
            
            modal.classList.add('active');
            content.classList.add('active');
        }

        function closeWekaLogModal() {
            const modal = document.getElementById('wekaLogModal');
            const content = document.getElementById('wekaLogModalContent');
            
            modal.classList.remove('active');
            content.classList.remove('active');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 320);
        }

        function closeWekaLogModalOnOutsideClick(event) {
            if (event.target.id === 'wekaLogModal') {
                closeWekaLogModal();
            }
        }

        function copyWekaLog() {
            navigator.clipboard.writeText(rawWekaLog).then(() => {
                alert('Copied WEKA Log to clipboard!');
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }

        function downloadWekaLog() {
            const element = document.createElement("a");
            const file = new Blob([rawWekaLog], {type: 'text/plain'});
            element.href = URL.createObjectURL(file);
            element.download = "weka_output_log.txt";
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }

        // ── Export CSV: Top 3 Association Rules ──
        function exportTableCSV() {
            const rows = [['No', 'Rule (X → Y)', 'Support', 'Confidence', 'Lift', 'Conviction']];
            document.querySelectorAll('#tab-rules tbody tr').forEach(tr => {
                const cells = tr.querySelectorAll('td');
                if (cells.length >= 6) {
                    rows.push([
                        cells[0].innerText.trim(),
                        cells[1].innerText.trim(),
                        cells[2].innerText.trim(),
                        cells[3].innerText.trim(),
                        cells[4].innerText.trim(),
                        cells[5].innerText.trim()
                    ]);
                }
            });
            const csvContent = rows.map(r => r.map(c => '"' + c.replace(/"/g, '""') + '"').join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'weka_top3_association_rules.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        // ── Export PDF: Top 3 Association Rules (Print Window) ──
        function exportTablePDF() {
            const rows = [];
            document.querySelectorAll('#tab-rules tbody tr').forEach(tr => {
                const cells = tr.querySelectorAll('td');
                if (cells.length >= 6) {
                    rows.push({
                        no: cells[0].innerText.trim(),
                        rule: cells[1].innerText.trim(),
                        support: cells[2].innerText.trim(),
                        confidence: cells[3].innerText.trim(),
                        lift: cells[4].innerText.trim(),
                        conviction: cells[5].innerText.trim()
                    });
                }
            });

            const tableRows = rows.map(r => `
                <tr>
                    <td>${r.no}</td>
                    <td>${r.rule}</td>
                    <td>${r.support}</td>
                    <td style="color:#6C5CE7;font-weight:800">${r.confidence}</td>
                    <td>${r.lift}</td>
                    <td>${r.conviction}</td>
                </tr>`).join('');

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`<!DOCTYPE html>
<html>
<head>
    <title>WEKA Top 3 Association Rules</title>
    <style>
        body { font-family: 'Inter', sans-serif; padding: 40px; color: #1e293b; }
        h1 { font-size: 20px; font-weight: 900; color: #6C5CE7; margin-bottom: 4px; }
        p { font-size: 12px; color: #64748b; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #6C5CE7; color: white; padding: 10px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin-top: 24px; font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <h1>TOP 3 WEKA APRIORI ASSOCIATION RULES</h1>
    <p>Apriori algorithm &nbsp;|&nbsp; Do'Zee System &nbsp;|&nbsp; Exported: ${new Date().toLocaleString()}</p>
    <table>
        <thead><tr><th>No</th><th>Rule (X → Y)</th><th>Support</th><th>Confidence</th><th>Lift</th><th>Conviction</th></tr></thead>
        <tbody>${tableRows}</tbody>
    </table>
    <div class="footer">Generated by WEKA Apriori Engine &mdash; Do'Zee Analytics</div>
    <script>window.onload = function() { window.print(); }<\/script>
</body>
</html>`);
            printWindow.document.close();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // ── File Upload Handlers (Drag & Drop + Click to Upload) ──
            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('weka-file-input');
            const browseBtn = document.getElementById('browse-btn');
            const defaultView = document.getElementById('upload-default-view');
            const successView = document.getElementById('upload-success-view');
            const fileNamePreview = document.getElementById('file-name-preview');
            const fileSizePreview = document.getElementById('file-size-preview');
            const errorMsg = document.getElementById('upload-error-msg');
            const errorText = document.getElementById('error-text');
            const removeFileBtn = document.getElementById('remove-file-btn');

            // Open file explorer on drop zone click
            dropZone.addEventListener('click', function(e) {
                // Ignore click if clicking on the remove button
                if (e.target === removeFileBtn || removeFileBtn.contains(e.target)) {
                    return;
                }
                fileInput.click();
            });

            // Prevent browse button click propagation (so we don't trigger click twice)
            browseBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.click();
            });

            // Drag and drop event listeners
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Visual indicators for dragover
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, function() {
                    dropZone.classList.add('upload-dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function() {
                    dropZone.classList.remove('upload-dragover');
                }, false);
            });

            // Handle dropped files
            dropZone.addEventListener('drop', function(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            });

            // Handle selected files from input
            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                if (files.length === 0) return;
                const file = files[0];
                const fileName = file.name;
                const fileExtension = fileName.split('.').pop().toLowerCase();

                // Validate file extension
                if (fileExtension !== 'csv' && fileExtension !== 'arff') {
                    showError("Invalid file format. Please upload CSV or ARFF file only.");
                    resetUploadState();
                    return;
                }

                // Hide error message if validation succeeds
                hideError();

                // Sync the files to the file input if dropped
                if (fileInput.files !== files) {
                    fileInput.files = files;
                }

                // Update file preview text
                fileNamePreview.textContent = fileName;
                fileSizePreview.textContent = formatBytes(file.size);

                // Transition views: fade-in success state
                defaultView.classList.add('hidden');
                successView.classList.remove('hidden');
                void successView.offsetWidth; // Force layout calculation to trigger transition
                successView.classList.remove('opacity-0');
                successView.classList.add('opacity-100', 'fade-in');
            }

            // Remove/reset file selection
            removeFileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                resetUploadState();
            });

            function resetUploadState() {
                fileInput.value = ''; // Clear native input
                
                // Transition views back to default
                successView.classList.remove('opacity-100', 'fade-in');
                successView.classList.add('opacity-0');
                
                setTimeout(() => {
                    successView.classList.add('hidden');
                    defaultView.classList.remove('hidden');
                    // Trigger reflow
                    void defaultView.offsetWidth;
                }, 300);
            }

            function showError(msg) {
                errorText.textContent = msg;
                errorMsg.classList.remove('hidden');
                void errorMsg.offsetWidth;
                errorMsg.classList.add('fade-in');
            }

            function hideError() {
                errorMsg.classList.add('hidden');
            }

            // Helper to format file size in bytes/KB/MB
            function formatBytes(bytes, decimals = 2) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const dm = decimals < 0 ? 0 : decimals;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
            }

            // ── Chart 1: Top Rules by Lift (Bar Chart) ──
            const liftCtx = document.getElementById('liftBarChart').getContext('2d');
            const rulesLiftLabels = [
                @foreach($allRules->take(5) as $index => $r)
                    "Rule #{{ $index + 1 }}",
                @endforeach
            ];
            const rulesLiftValues = [
                @foreach($allRules->take(5) as $r)
                    {{ $r->lift }},
                @endforeach
            ];

            new Chart(liftCtx, {
                type: 'bar',
                data: {
                    labels: rulesLiftLabels,
                    datasets: [{
                        label: 'Lift Ratio',
                        data: rulesLiftValues,
                        backgroundColor: '#8B5CF6',
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 16
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Lift: ${context.parsed.y.toFixed(3)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 9, weight: 'bold' }, color: '#94A3B8' } },
                        y: { border: { dash: [4, 4] }, ticks: { font: { size: 9 }, color: '#94A3B8' } }
                    }
                }
            });

            // ── Chart 2: Support vs Confidence Scatter Plot ──
            const scatterCtx = document.getElementById('scatterPlot').getContext('2d');
            const scatterPoints = [
                @foreach($allRules as $rule)
                @php
                    $cleanText = $rule->rule_text;
                    if (preg_match('/\[conv:(.*?)\]/', $rule->rule_text, $match)) {
                        $cleanText = trim(str_replace($match[0], '', $rule->rule_text));
                    }
                @endphp
                {
                    x: {{ $rule->support * 100 }},
                    y: {{ $rule->confidence * 100 }},
                    ruleText: "{{ addslashes($cleanText) }}"
                },
                @endforeach
            ];

            new Chart(scatterCtx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Rules',
                        data: scatterPoints,
                        backgroundColor: '#C084FC',
                        borderColor: '#A78BFA',
                        borderWidth: 1,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const pt = context.raw;
                                    return [
                                        `Rule: ${pt.ruleText}`,
                                        `Support: ${pt.x.toFixed(2)}%`,
                                        `Confidence: ${pt.y.toFixed(1)}%`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Support (%)', font: { size: 9, weight: 'bold' }, color: '#64748B' },
                            ticks: { font: { size: 9 }, color: '#94A3B8' },
                            grid: { color: '#F1F5F9' }
                        },
                        y: {
                            title: { display: true, text: 'Confidence (%)', font: { size: 9, weight: 'bold' }, color: '#64748B' },
                            ticks: { font: { size: 9 }, color: '#94A3B8' },
                            grid: { color: '#F1F5F9' }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
