<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">
                    {{ __('Market Basket Analysis + Association Insight Dashboard') }}
                </h2>
                <p class="text-xs text-slate-505 mt-1 font-medium">
                    {{ __('Customer purchase patterns and product bundling insights using Apriori Algorithm') }}
                </p>
            </div>
            <div class="flex items-center gap-2 text-sm font-medium text-slate-505 bg-white border border-slate-100 px-4 py-2 rounded-2xl shadow-sm">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                    Apriori Engine Active
                </span>
                <span class="text-slate-300">|</span>
                <span>{{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{
        selectedItem: null,
        associationData: {{ json_encode($associationData) }},
        showNetworkModal: false,
        currentPage: 1,
        itemsPerPage: 5,
        get paginatedData() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.associationData.slice(start, start + this.itemsPerPage);
        },
        get totalPages() {
            return Math.ceil(this.associationData.length / this.itemsPerPage);
        },
        init() {
            if (this.associationData.length > 0) {
                this.selectedItem = this.associationData[0];
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">



            {{-- ── TWO COLUMN MAIN LAYOUT ──────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- ── LEFT SECTION: REPORT TABLE ─────────────────────────────────────── --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="premium-card bg-white p-6 md:p-8">
                        
                        {{-- Card Header with Export buttons --}}
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 border-b border-slate-50 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 heading-font">
                                    {{ __('Market Basket Analysis Report') }}
                                </h3>
                                <p class="text-xs text-slate-450 mt-0.5">
                                    {{ __('Customer purchase patterns and product bundling insights') }}
                                </p>
                            </div>
                            
                            {{-- Export actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('reports.apriori.export', ['format'=>'excel']) }}" 
                                   class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl shadow-md shadow-emerald-100 hover:shadow-emerald-200 transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    {{ __('Excel') }}
                                </a>
                                <a href="{{ route('reports.apriori.export', ['format'=>'pdf']) }}" 
                                   class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl shadow-md shadow-rose-100 hover:shadow-rose-200 transition-all flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    {{ __('PDF') }}
                                </a>
                            </div>
                        </div>

                        {{-- Table Container --}}
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="min-w-full divide-y divide-slate-100 text-left">
                                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="px-5 py-4 w-12 text-center">NO.</th>
                                        <th class="px-5 py-4 w-1/3">ITEM CODE</th>
                                        <th class="px-5 py-4">ASSOCIATED ITEMS (PARTNERS)</th>
                                        <th class="px-5 py-4 w-24 text-right">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <template x-for="(item, idx) in paginatedData" :key="item.item_id">
                                        <tr class="hover:bg-purple-50/20 transition-all duration-150"
                                            :class="selectedItem && selectedItem.item_id === item.item_id ? 'bg-purple-50/40' : ''">
                                            <td class="px-5 py-4 text-xs font-black text-slate-400 text-center" x-text="(currentPage - 1) * itemsPerPage + idx + 1"></td>
                                            <td class="px-5 py-4">
                                                <span class="text-xs font-black text-indigo-600 uppercase tracking-wide" x-text="item.item_code"></span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-col gap-1.5">
                                                    <template x-for="(partner, pIdx) in item.partners" :key="partner.item_id">
                                                        <div class="flex items-center gap-1.5 text-xs text-slate-700">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            <span class="font-extrabold text-slate-800" x-text="partner.item_code"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4 text-right">
                                                <button type="button" @click="selectedItem = item"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 hover:bg-purple-600 text-purple-700 hover:text-white text-[10px] font-black uppercase tracking-wider rounded-lg transition-all duration-200">
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="associationData.length === 0">
                                        <tr>
                                            <td colspan="4" class="px-5 py-16 text-center text-slate-400 italic text-sm">
                                                No association results available in the database. Please add more transaction sales.
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- Client-side Pagination Controls --}}
                        <div class="mt-4 flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                            <div class="text-xs font-bold text-slate-505">
                                Showing <span x-text="associationData.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0"></span> to 
                                <span x-text="Math.min(currentPage * itemsPerPage, associationData.length)"></span> of 
                                <span x-text="associationData.length"></span> entries
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" @click="if (currentPage > 1) currentPage--" :disabled="currentPage === 1"
                                        class="px-3 py-1.5 bg-white border border-slate-150 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-lg disabled:opacity-50 transition-all cursor-pointer">
                                    Prev
                                </button>
                                <template x-for="page in totalPages" :key="page">
                                    <button type="button" @click="currentPage = page"
                                            :class="currentPage === page ? 'bg-purple-600 text-white border-purple-600' : 'bg-white border-slate-150 text-slate-700 hover:bg-slate-50'"
                                            class="w-8 h-8 flex items-center justify-center border text-xs font-bold rounded-lg transition-all cursor-pointer"
                                            x-text="page">
                                    </button>
                                </template>
                                <button type="button" @click="if (currentPage < totalPages) currentPage++" :disabled="currentPage === totalPages"
                                        class="px-3 py-1.5 bg-white border border-slate-150 text-slate-700 hover:bg-slate-50 text-xs font-bold rounded-lg disabled:opacity-50 transition-all cursor-pointer">
                                    Next
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ── RIGHT SECTION: ASSOCIATION INSIGHT PANEL ───────────────────────── --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="premium-card bg-white p-6 md:p-8 space-y-6 sticky top-6 shadow-sm border border-slate-100">
                        <div class="border-b border-slate-50 pb-4">
                            <h3 class="text-lg font-bold text-slate-900 heading-font">
                                {{ __('Association Rule Insight Panel') }}
                            </h3>
                            <p class="text-xs text-slate-450 mt-0.5">
                                {{ __('Focused analysis and bundling opportunities for the selected item') }}
                            </p>
                        </div>

                        {{-- Section 1: Selected Item --}}
                        <div class="space-y-2">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Selected Item</span>
                            <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                <div class="min-w-0">
                                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider" x-text="selectedItem ? selectedItem.item_code : 'N/A'"></span>
                                    <h4 class="text-sm font-bold text-slate-800 leading-snug mt-0.5 truncate" x-text="selectedItem ? selectedItem.item_name : 'No item selected'"></h4>
                                </div>
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-[9px] font-bold rounded-lg uppercase tracking-wider">
                                    Focused
                                </span>
                            </div>
                        </div>

                        {{-- Section 3: Association Map (SVG Graph) --}}
                        <div class="space-y-2">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Association Map (Clean Graph)</span>
                            <div class="relative w-full border border-slate-100 rounded-2xl bg-slate-50/50 p-4">
                                <svg class="w-full h-44" viewBox="0 0 350 180">
                                    <defs>
                                        <style>
                                            @keyframes dash {
                                                to { stroke-dashoffset: -20; }
                                            }
                                            .animated-line {
                                                animation: dash 1.5s linear infinite;
                                            }
                                        </style>
                                    </defs>

                                    <!-- Connection lines with dashed pattern using x-show to render in SVG correctly -->
                                    <g x-show="selectedItem && selectedItem.partners[0]">
                                        <line x1="80" y1="90" x2="220" y2="35" stroke="#818CF8" stroke-width="2" stroke-dasharray="4" class="animated-line" />
                                        <rect x="135" y="50" width="35" height="15" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                        <text x="152.5" y="61" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedItem && selectedItem.partners[0] ? selectedItem.partners[0].confidence + '%' : ''"></text>
                                    </g>
                                    <g x-show="selectedItem && selectedItem.partners[1]">
                                        <line x1="80" y1="90" x2="220" y2="90" stroke="#818CF8" stroke-width="2" stroke-dasharray="4" class="animated-line" />
                                        <rect x="135" y="82.5" width="35" height="15" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                        <text x="152.5" y="93.5" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedItem && selectedItem.partners[1] ? selectedItem.partners[1].confidence + '%' : ''"></text>
                                    </g>
                                    <g x-show="selectedItem && selectedItem.partners[2]">
                                        <line x1="80" y1="90" x2="220" y2="145" stroke="#818CF8" stroke-width="2" stroke-dasharray="4" class="animated-line" />
                                        <rect x="135" y="108" width="35" height="15" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                        <text x="152.5" y="119" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedItem && selectedItem.partners[2] ? selectedItem.partners[2].confidence + '%' : ''"></text>
                                    </g>

                                    <!-- Center Node (Selected Item) -->
                                    <circle cx="80" cy="90" r="28" fill="#4F46E5" />
                                    <circle cx="80" cy="90" r="24" fill="none" stroke="#FFFFFF" stroke-width="1.5" />
                                    <text x="80" y="93.5" fill="#FFFFFF" class="text-[9px] font-black" text-anchor="middle" x-text="selectedItem ? selectedItem.item_code : 'Item'"></text>

                                    <!-- Partner Nodes -->
                                    <g x-show="selectedItem && selectedItem.partners[0]">
                                        <circle cx="220" cy="35" r="18" fill="#10B981" />
                                        <text x="220" y="38" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedItem && selectedItem.partners[0] ? selectedItem.partners[0].item_code : ''"></text>
                                        <text x="245" y="38.5" fill="#334155" class="text-[9px] font-extrabold" x-text="selectedItem && selectedItem.partners[0] ? selectedItem.partners[0].item_name : ''"></text>
                                    </g>
                                    <g x-show="selectedItem && selectedItem.partners[1]">
                                        <circle cx="220" cy="90" r="18" fill="#0EA5E9" />
                                        <text x="220" y="93" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedItem && selectedItem.partners[1] ? selectedItem.partners[1].item_code : ''"></text>
                                        <text x="245" y="93.5" fill="#334155" class="text-[9px] font-extrabold" x-text="selectedItem && selectedItem.partners[1] ? selectedItem.partners[1].item_name : ''"></text>
                                    </g>
                                    <g x-show="selectedItem && selectedItem.partners[2]">
                                        <circle cx="220" cy="145" r="18" fill="#F59E0B" />
                                        <text x="220" y="148" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedItem && selectedItem.partners[2] ? selectedItem.partners[2].item_code : ''"></text>
                                        <text x="245" y="148.5" fill="#334155" class="text-[9px] font-extrabold" x-text="selectedItem && selectedItem.partners[2] ? selectedItem.partners[2].item_name : ''"></text>
                                    </g>
                                </svg>
                            </div>
                        </div>

                        {{-- Section 2: Top Associations --}}
                        <div class="space-y-2.5">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Top Associations</span>
                            <div class="flex flex-col gap-2">
                                <template x-for="(partner, pIdx) in (selectedItem ? selectedItem.partners : [])" :key="partner.item_id">
                                    <div class="flex items-center justify-between p-3 border border-slate-100 rounded-xl hover:bg-slate-50 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full" :class="pIdx === 0 ? 'bg-emerald-500' : (pIdx === 1 ? 'bg-sky-500' : 'bg-amber-500')"></span>
                                            <span class="text-xs font-black text-slate-800" x-text="partner.item_code"></span>
                                            <span class="text-xs text-slate-505 font-medium truncate max-w-[200px]" x-text="partner.item_name"></span>
                                        </div>
                                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-lg" x-text="partner.confidence + '% Conf'"></span >
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Section 4: Key Association Metrics --}}
                        <div class="space-y-2">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Key Association Metrics</span>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-center">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Support</p>
                                    <p class="text-sm font-black text-slate-800 mt-1 tabular-nums"
                                       x-text="selectedItem && selectedItem.partners[0] ? selectedItem.partners[0].support + '%' : '0%'"></p>
                                </div>
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-center">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Confidence</p>
                                    <p class="text-sm font-black text-emerald-600 mt-1 tabular-nums"
                                       x-text="selectedItem && selectedItem.partners[0] ? selectedItem.partners[0].confidence + '%' : '0%'"></p>
                                </div>
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-center">
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-wider">Lift</p>
                                    <p class="text-sm font-black text-indigo-600 mt-1 tabular-nums"
                                       x-text="selectedItem && selectedItem.partners[0] ? selectedItem.partners[0].lift : '0.00'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 5: Interpretation --}}
                        <div class="space-y-2 pt-2 border-t border-slate-50">
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Interpretation</span>
                            <p class="text-xs text-slate-505 leading-relaxed font-medium">
                                {{ __('Customers who purchase this item are likely to also purchase the top associated items based on association analysis.') }}
                            </p>
                        </div>

                        {{-- Full Network Modal Toggle Button --}}
                        <button type="button" @click="showNetworkModal = true"
                                class="w-full py-3 border-2 border-dashed border-purple-200 hover:border-purple-600 hover:bg-purple-50 text-purple-700 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-200">
                            {{ __('View Full Network (All Links)') }}
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── FULL PRODUCT ASSOCIATION NETWORK EXPLORER MODAL ────────────────── --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none"
             x-show="showNetworkModal" style="display: none;"
             x-data="{
                selectedNetworkItem: null,
                selectedPartner: null,
                currentView: 'explorer', // 'explorer' or 'form'
                
                // Form Fields
                formPromoName: '',
                formDiscountType: 'Percentage',
                formDiscountValue: 10,
                formStartDate: '',
                formEndDate: '',
                formStatus: 'Active',
                isSaving: false,

                init() {
                    this.$watch('showNetworkModal', value => {
                        if (value) {
                            this.selectedNetworkItem = this.selectedItem;
                            this.selectedPartner = null;
                            this.currentView = 'explorer';
                        }
                    });
                },

                openPromoForm() {
                    if (!this.selectedPartner) return;
                    this.formPromoName = 'Combo Promotion - ' + this.selectedNetworkItem.item_name + ' + ' + this.selectedPartner.item_name;
                    this.formDiscountType = 'Percentage';
                    this.formDiscountValue = 10;
                    this.formStartDate = new Date().toISOString().split('T')[0];
                    this.updateEndDate(this.formStartDate);
                    this.formStatus = 'Active';
                    this.currentView = 'form';
                },

                updateEndDate(startDateVal) {
                    if (!startDateVal) return;
                    const startDate = new Date(startDateVal);
                    const endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 2);
                    const yyyy = endDate.getFullYear();
                    const mm = String(endDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(endDate.getDate()).padStart(2, '0');
                    this.formEndDate = `${yyyy}-${mm}-${dd}`;
                },

                submitPromo() {
                    this.isSaving = true;
                    fetch('{{ route('promotions.ajax-store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            promo_name: this.formPromoName,
                            description: 'Combo promotion featuring ' + this.selectedNetworkItem.item_name + ' and ' + this.selectedPartner.item_name + '. Recommended based on association rules (Support: ' + this.selectedPartner.support + '%, Confidence: ' + this.selectedPartner.confidence + '%, Lift: ' + this.selectedPartner.lift + ').',
                            start_date: this.formStartDate,
                            end_date: this.formEndDate,
                            rule_id: this.selectedPartner.rule_id,
                            discount_value: this.formDiscountValue,
                            status: this.formStatus
                        })
                    })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(res => {
                        this.isSaving = false;
                        if (res.status === 200 && res.body.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: res.body.message,
                                confirmButtonColor: '#4F46E5'
                            }).then(() => {
                                window.location.href = '{{ route('promotions.index') }}';
                            });
                        } else {
                            const errMsgs = res.body.errors ? res.body.errors.join('<br>') : 'Something went wrong.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                html: `<div class='text-left text-xs text-red-600 font-bold'>${errMsgs}</div>`,
                                confirmButtonColor: '#EF4444'
                            });
                        }
                    })
                    .catch(err => {
                        this.isSaving = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to connect to the server.',
                            confirmButtonColor: '#EF4444'
                        });
                    });
                }
             }"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showNetworkModal = false"></div>

            {{-- Modal Box --}}
            <div class="relative w-full max-w-4xl mx-auto my-6 z-50 pointer-events-none px-4">
                <div class="relative flex flex-col w-full bg-white border border-slate-100 rounded-3xl shadow-2xl outline-none focus:outline-none pointer-events-auto max-h-[95vh]">
                    
                    {{-- Modal Header --}}
                    <div class="flex items-start justify-between p-6 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-wide">
                                {{ __('Full Product Association Network') }}
                            </h3>
                            <p class="text-xs text-slate-505 font-medium mt-0.5">
                                {{ __('Visualize product relationships and frequently purchased combinations.') }}
                            </p>
                        </div>
                        <button class="p-1 ml-auto bg-transparent border-0 text-slate-400 hover:text-slate-700 float-right text-3xl leading-none font-semibold outline-none focus:outline-none"
                                @click="showNetworkModal = false">
                            <span class="bg-transparent text-slate-400 h-6 w-6 text-2xl block outline-none focus:outline-none">×</span>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="relative p-6 flex-auto overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                            
                            {{-- Interactive Network Graph Column --}}
                            <div class="md:col-span-7 flex flex-col">
                                
                                {{-- SVG network graph --}}
                                <div class="w-full border border-slate-100 rounded-3xl bg-slate-50/50 p-4 flex-auto flex items-center justify-center">
                                    <svg class="w-full h-96" viewBox="0 0 420 360">
                                        <defs>
                                            <radialGradient id="purpleGlow" cx="50%" cy="50%" r="50%">
                                                <stop offset="0%" stop-color="#A5B4FC" />
                                                <stop offset="70%" stop-color="#6366F1" />
                                                <stop offset="100%" stop-color="#4F46E5" />
                                            </radialGradient>
                                            <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                                                <feGaussianBlur stdDeviation="4" result="blur" />
                                                <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                            </filter>
                                            <style>
                                                @keyframes flow {
                                                    to { stroke-dashoffset: -20; }
                                                }
                                                .flow-line {
                                                    stroke-dasharray: 5;
                                                    animation: flow 1.2s linear infinite;
                                                }
                                            </style>
                                        </defs>

                                        <!-- Connection Line 1 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[0]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[0]; }">
                                            <line x1="210" y1="180" x2="210" y2="60" stroke="#818CF8" stroke-width="2.5" class="flow-line"
                                                  :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[0].item_id ? '#4F46E5' : '#818CF8'"
                                                  :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[0].item_id ? 3.5 : 2" />
                                            <rect x="192.5" y="110" width="35" height="16" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                            <text x="210" y="121" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[0] ? selectedNetworkItem.partners[0].confidence + '%' : ''"></text>
                                        </g>

                                        <!-- Connection Line 2 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[1]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[1]; }">
                                            <line x1="210" y1="180" x2="330" y2="130" stroke="#818CF8" stroke-width="2.5" class="flow-line"
                                                  :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[1].item_id ? '#4F46E5' : '#818CF8'"
                                                  :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[1].item_id ? 3.5 : 2" />
                                            <rect x="252.5" y="147" width="35" height="16" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                            <text x="270" y="158" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[1] ? selectedNetworkItem.partners[1].confidence + '%' : ''"></text>
                                        </g>

                                        <!-- Connection Line 3 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[2]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[2]; }">
                                            <line x1="210" y1="180" x2="290" y2="270" stroke="#818CF8" stroke-width="2.5" class="flow-line"
                                                  :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[2].item_id ? '#4F46E5' : '#818CF8'"
                                                  :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[2].item_id ? 3.5 : 2" />
                                            <rect x="232.5" y="217" width="35" height="16" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                            <text x="250" y="228" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[2] ? selectedNetworkItem.partners[2].confidence + '%' : ''"></text>
                                        </g>

                                        <!-- Connection Line 4 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[3]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[3]; }">
                                            <line x1="210" y1="180" x2="130" y2="270" stroke="#818CF8" stroke-width="2.5" class="flow-line"
                                                  :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[3].item_id ? '#4F46E5' : '#818CF8'"
                                                  :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[3].item_id ? 3.5 : 2" />
                                            <rect x="152.5" y="217" width="35" height="16" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                            <text x="170" y="228" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[3] ? selectedNetworkItem.partners[3].confidence + '%' : ''"></text>
                                        </g>

                                        <!-- Connection Line 5 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[4]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[4]; }">
                                            <line x1="210" y1="180" x2="90" y2="130" stroke="#818CF8" stroke-width="2.5" class="flow-line"
                                                  :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[4].item_id ? '#4F46E5' : '#818CF8'"
                                                  :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[4].item_id ? 3.5 : 2" />
                                            <rect x="132.5" y="147" width="35" height="16" rx="4" fill="#FFFFFF" stroke="#E2E8F0" />
                                            <text x="150" y="158" fill="#4F46E5" class="text-[8px] font-black" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[4] ? selectedNetworkItem.partners[4].confidence + '%' : ''"></text>
                                        </g>

                                        <!-- Center Node (Selected Product) -->
                                        <g class="cursor-pointer" @click="if (currentView === 'explorer') { selectedPartner = null; }">
                                            <circle cx="210" cy="180" r="32" fill="url(#purpleGlow)" filter="url(#glow)" />
                                            <circle cx="210" cy="180" r="28" fill="none" stroke="#FFFFFF" stroke-width="1.5" />
                                            <text x="210" y="183.5" fill="#FFFFFF" class="text-[9px] font-black" text-anchor="middle" x-text="selectedNetworkItem ? selectedNetworkItem.item_code : ''"></text>
                                        </g>

                                        <!-- Partner Node 1 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[0]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[0]; }">
                                            <circle cx="210" cy="60" r="20" fill="#10B981"
                                                    :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[0].item_id ? '#047857' : '#A7F3D0'"
                                                    :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[0].item_id ? 3.5 : 1.5" />
                                            <text x="210" y="63" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[0] ? selectedNetworkItem.partners[0].item_code : ''"></text>
                                            <text x="210" y="32" fill="#334155" class="text-[9px] font-extrabold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[0] ? selectedNetworkItem.partners[0].item_name : ''"></text>
                                        </g>

                                        <!-- Partner Node 2 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[1]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[1]; }">
                                            <circle cx="330" cy="130" r="20" fill="#0EA5E9"
                                                    :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[1].item_id ? '#0369A1' : '#BAE6FD'"
                                                    :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[1].item_id ? 3.5 : 1.5" />
                                            <text x="330" y="133" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[1] ? selectedNetworkItem.partners[1].item_code : ''"></text>
                                            <text x="330" y="102" fill="#334155" class="text-[9px] font-extrabold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[1] ? selectedNetworkItem.partners[1].item_name : ''"></text>
                                        </g>

                                        <!-- Partner Node 3 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[2]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[2]; }">
                                            <circle cx="290" cy="270" r="20" fill="#F59E0B"
                                                    :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[2].item_id ? '#B45309' : '#FDE68A'"
                                                    :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[2].item_id ? 3.5 : 1.5" />
                                            <text x="290" y="273" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[2] ? selectedNetworkItem.partners[2].item_code : ''"></text>
                                            <text x="290" y="299" fill="#334155" class="text-[9px] font-extrabold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[2] ? selectedNetworkItem.partners[2].item_name : ''"></text>
                                        </g>

                                        <!-- Partner Node 4 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[3]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[3]; }">
                                            <circle cx="130" cy="270" r="20" fill="#EC4899"
                                                    :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[3].item_id ? '#BE185D' : '#FBCFE8'"
                                                    :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[3].item_id ? 3.5 : 1.5" />
                                            <text x="130" y="273" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[3] ? selectedNetworkItem.partners[3].item_code : ''"></text>
                                            <text x="130" y="299" fill="#334155" class="text-[9px] font-extrabold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[3] ? selectedNetworkItem.partners[3].item_name : ''"></text>
                                        </g>

                                        <!-- Partner Node 5 -->
                                        <g x-show="selectedNetworkItem && selectedNetworkItem.partners[4]" class="cursor-pointer"
                                           @click="if (currentView === 'explorer') { selectedPartner = selectedNetworkItem.partners[4]; }">
                                            <circle cx="90" cy="130" r="20" fill="#8B5CF6"
                                                    :stroke="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[4].item_id ? '#6D28D9' : '#DDD6FE'"
                                                    :stroke-width="selectedPartner && selectedPartner.item_id === selectedNetworkItem.partners[4].item_id ? 3.5 : 1.5" />
                                            <text x="90" y="133" fill="#FFFFFF" class="text-[8px] font-bold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[4] ? selectedNetworkItem.partners[4].item_code : ''"></text>
                                            <text x="90" y="102" fill="#334155" class="text-[9px] font-extrabold" text-anchor="middle" x-text="selectedNetworkItem && selectedNetworkItem.partners[4] ? selectedNetworkItem.partners[4].item_name : ''"></text>
                                        </g>
                                    </svg>
                                </div>

                                {{-- Legend --}}
                                <div class="mt-4 flex flex-wrap items-center justify-center gap-6 p-3 bg-white border border-slate-50 rounded-2xl text-[10px] font-bold text-slate-550">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3.5 h-3.5 rounded-full bg-purple-600 border-2 border-purple-300"></span>
                                        <span>Selected Product</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-500"></span>
                                        <span>Partner Product</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 border-b-2 border-dashed border-indigo-400"></div>
                                        <span>Association (Dashed Line)</span>
                                    </div>
                                </div>

                            </div>

                            {{-- Selected Product Detail Panel Column --}}
                            <div class="md:col-span-5 flex flex-col justify-between">
                                
                                {{-- Explorer View --}}
                                <div x-show="currentView === 'explorer'" class="flex-auto flex flex-col justify-between">
                                    
                                    {{-- Default State: No partner clicked --}}
                                    <div x-show="selectedPartner === null" class="flex flex-col items-center justify-center text-center p-8 bg-slate-50 border border-slate-100 rounded-3xl min-h-[350px]">
                                        <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                                        <p class="text-sm font-bold text-slate-750 leading-snug">Click on any partner product to view detailed association metrics.</p>
                                        <p class="text-[11px] text-slate-400 mt-1 max-w-[220px] leading-relaxed">Explore cross-selling probabilities, lift index, and purchase support.</p>
                                    </div>

                                    {{-- Partner Details State: Partner clicked --}}
                                    <div x-show="selectedPartner !== null" class="space-y-6">
                                        {{-- Selected Partner Title Block --}}
                                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Selected Partner Product</p>
                                            <span class="text-xs font-black text-emerald-600 uppercase tracking-wider block mt-1"
                                                  x-text="selectedPartner ? selectedPartner.item_code : ''"></span>
                                            <h4 class="text-sm font-bold text-slate-800 leading-snug mt-0.5"
                                                x-text="selectedPartner ? selectedPartner.item_name : ''"></h4>
                                        </div>

                                        {{-- Association Details --}}
                                        <div class="space-y-4">
                                            <p class="text-[8px] font-black uppercase tracking-widest text-slate-400">Association Details</p>

                                            <!-- Confidence Card -->
                                            <div class="p-4 border border-slate-100 rounded-2xl bg-white shadow-sm hover:shadow transition-shadow">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-505">Confidence</span>
                                                    <span class="text-sm font-black text-emerald-600 tabular-nums" x-text="selectedPartner ? selectedPartner.confidence + '%' : ''"></span>
                                                </div>
                                                <p class="text-xs text-slate-505 mt-2 font-medium leading-relaxed">
                                                    Probability that <span class="text-slate-700 font-bold" x-text="selectedPartner ? selectedPartner.item_name : ''"></span> is purchased when <span class="text-slate-700 font-bold" x-text="selectedNetworkItem ? selectedNetworkItem.item_name : ''"></span> is purchased.
                                                </p>
                                            </div>

                                            <!-- Lift Card -->
                                            <div class="p-4 border border-slate-100 rounded-2xl bg-white shadow-sm hover:shadow transition-shadow">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-505">Lift</span>
                                                    <span class="text-sm font-black text-indigo-600 tabular-nums" x-text="selectedPartner ? selectedPartner.lift + 'x' : ''"></span>
                                                </div>
                                                <p class="text-xs text-slate-505 mt-2 font-medium leading-relaxed">
                                                    How much more likely <span class="text-slate-700 font-bold" x-text="selectedPartner ? selectedPartner.item_name : ''"></span> is purchased with <span class="text-slate-700 font-bold" x-text="selectedNetworkItem ? selectedNetworkItem.item_name : ''"></span>.
                                                </p>
                                            </div>

                                            <!-- Support Card -->
                                            <div class="p-4 border border-slate-100 rounded-2xl bg-white shadow-sm hover:shadow transition-shadow">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-550">Support</span>
                                                    <span class="text-sm font-black text-slate-700 tabular-nums" x-text="selectedPartner ? selectedPartner.support + '%' : ''"></span>
                                                </div>
                                                <p class="text-xs text-slate-505 mt-2 font-medium leading-relaxed">
                                                    Frequency of <span class="text-slate-700 font-bold" x-text="selectedNetworkItem ? selectedNetworkItem.item_name : ''"></span> + <span class="text-slate-700 font-bold" x-text="selectedPartner ? selectedPartner.item_name : ''"></span> purchased together.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Bottom Buttons --}}
                                    <div class="pt-4 mt-6 border-t border-slate-100 space-y-2.5">
                                        <button class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200 shadow-lg shadow-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer animate-none"
                                                type="button"
                                                :disabled="selectedPartner === null"
                                                @click="openPromoForm()">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ __('Create Combo Promotion') }}
                                        </button>
                                        <button class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-widest rounded-xl transition-all duration-200 cursor-pointer animate-none"
                                                type="button" @click="showNetworkModal = false">
                                            {{ __('Close Explorer') }}
                                        </button>
                                    </div>
                                </div>

                                {{-- Form View --}}
                                <div x-show="currentView === 'form'" class="flex-auto flex flex-col justify-between" style="display: none;">
                                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                                        <div class="p-4 bg-purple-50 border border-purple-100 rounded-2xl">
                                            <p class="text-[8px] font-black uppercase tracking-widest text-purple-505">Combo Builder Mode</p>
                                            <h4 class="text-sm font-bold text-slate-800 leading-snug mt-1">Configure Promotion Strategy</h4>
                                        </div>

                                        <div class="space-y-3 text-xs">
                                            <!-- Promotion Name -->
                                            <div>
                                                <label class="block font-black text-[9px] uppercase tracking-wider text-slate-400 mb-1">Promotion Name</label>
                                                <input type="text" x-model="formPromoName" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700 shadow-sm" required />
                                            </div>

                                            <!-- Readonly Fields Grid -->
                                            <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                                <div class="col-span-2">
                                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider block">Main Product</span>
                                                    <span class="font-bold text-slate-700" x-text="selectedNetworkItem ? selectedNetworkItem.item_name : ''"></span>
                                                </div>
                                                <div class="col-span-2">
                                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider block">Partner Product</span>
                                                    <span class="font-bold text-emerald-600" x-text="selectedPartner ? selectedPartner.item_name : ''"></span>
                                                </div>
                                                <div>
                                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider block">Confidence</span>
                                                    <span class="font-extrabold text-slate-700" x-text="selectedPartner ? selectedPartner.confidence + '%' : ''"></span>
                                                </div>
                                                <div>
                                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-wider block">Support / Lift</span>
                                                    <span class="font-extrabold text-slate-700" x-text="selectedPartner ? selectedPartner.support + '% / ' + selectedPartner.lift + 'x' : ''"></span>
                                                </div>
                                            </div>

                                            <!-- Editable Input Fields -->
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block font-black text-[9px] uppercase tracking-wider text-slate-400 mb-1">Discount Type</label>
                                                    <input type="text" x-model="formDiscountType" class="w-full px-4 py-2.5 bg-slate-100 border border-slate-100 rounded-xl font-bold text-slate-505 cursor-not-allowed" readonly />
                                                </div>
                                                <div>
                                                    <label class="block font-black text-[9px] uppercase tracking-wider text-slate-400 mb-1">Discount Value (5-15%)</label>
                                                    <input type="number" min="5" max="15" x-model="formDiscountValue" class="w-full px-4 py-2.5 bg-white border border-slate-150 rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700" required />
                                                </div>
                                                <div>
                                                    <label class="block font-black text-[9px] uppercase tracking-wider text-slate-400 mb-1">Start Date</label>
                                                    <input type="date" x-model="formStartDate" @change="updateEndDate(formStartDate)" class="w-full px-4 py-2.5 bg-white border border-slate-150 rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700" required />
                                                </div>
                                                <div>
                                                    <label class="block font-black text-[9px] uppercase tracking-wider text-slate-400 mb-1">End Date (Auto 2 Days)</label>
                                                    <input type="date" x-model="formEndDate" class="w-full px-4 py-2.5 bg-slate-100 border border-slate-100 rounded-xl font-bold text-slate-550 cursor-not-allowed" readonly />
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="block font-black text-[9px] uppercase tracking-wider text-slate-400 mb-1">Promotion Status</label>
                                                    <select x-model="formStatus" class="w-full px-4 py-2.5 bg-white border border-slate-150 rounded-xl focus:ring-2 focus:ring-indigo-500 font-bold text-slate-700 cursor-pointer">
                                                        <option value="Active">Active</option>
                                                        <option value="Pending">Draft</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Save / Cancel / Back Buttons --}}
                                    <div class="pt-4 border-t border-slate-100 space-y-2 mt-4">
                                        <button class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 cursor-pointer animate-none"
                                                type="button"
                                                :disabled="isSaving"
                                                @click="submitPromo()">
                                            <template x-if="isSaving">
                                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            </template>
                                            <span x-text="isSaving ? 'Saving...' : 'Save Promotion'"></span>
                                        </button>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button class="py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all cursor-pointer text-center animate-none"
                                                    type="button"
                                                    @click="currentView = 'explorer'">
                                                Back
                                            </button>
                                            <button class="py-2.5 bg-slate-150 hover:bg-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-wider rounded-xl transition-all cursor-pointer text-center animate-none"
                                                    type="button"
                                                    @click="showNetworkModal = false">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
