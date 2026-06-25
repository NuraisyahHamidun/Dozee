<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight flex items-center gap-3">
                <button onclick="window.history.back()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-xl transition-all shadow-sm border border-slate-200 dark:border-slate-700/60 mr-1" title="Go Back">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Back</span>
                </button>
                <span class="p-2 bg-indigo-600 rounded-2xl text-white shadow-lg shadow-indigo-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                {{ __('Salesman Finance Report') }}
            </h2>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <span>Salesman:</span>
                <span class="font-bold text-slate-800 dark:text-white">{{ $salesman->name }} ({{ $salesman->username }})</span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="premium-card bg-white dark:bg-slate-800 p-6 md:p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                
                <!-- Filters & Export Header -->
                <div class="flex flex-col lg:flex-row justify-between items-stretch lg:items-center mb-8 gap-4 pb-6 border-b border-slate-100 dark:border-slate-700">
                    
                    <!-- Filter Form -->
                    <form id="filterForm" action="{{ route('reports.salesman', ['id' => $salesman->salesman_id]) }}" method="GET" class="flex flex-wrap items-center gap-3">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 mb-1 font-semibold">Start Date</span>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 mb-1 font-semibold">End Date</span>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 mb-1 font-semibold">Event Name</span>
                            <input type="text" name="event_name" value="{{ $eventName ?? '' }}" placeholder="Search event..." class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 max-w-[150px]">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-400 mb-1 font-semibold">Promotion Applied</span>
                            <select name="promo_id" class="text-sm rounded-xl border-slate-200 dark:border-slate-600 dark:bg-slate-700 dark:text-white focus:ring-indigo-500 min-w-[150px]">
                                <option value="All" {{ $promoId === 'All' ? 'selected' : '' }}>All Promotions</option>
                                @foreach($promotions as $promo)
                                    <option value="{{ $promo->promo_id }}" {{ $promoId == $promo->promo_id ? 'selected' : '' }}>
                                        {{ $promo->promo_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col justify-end pt-5">
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-colors shadow-md shadow-indigo-200 dark:shadow-none">
                                Filter
                            </button>
                        </div>
                    </form>

                    <!-- Export Buttons -->
                    <div class="flex items-end gap-3 self-end lg:self-center">
                        <a href="{{ route('reports.salesman.export', ['id' => $salesman->salesman_id, 'format' => 'excel', 'start_date' => $startDate, 'end_date' => $endDate, 'promo_id' => $promoId]) }}" 
                           class="px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-100 dark:shadow-none transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 
                            <span>Excel</span>
                        </a>
                        <a href="{{ route('reports.salesman.export', ['id' => $salesman->salesman_id, 'format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate, 'promo_id' => $promoId]) }}" 
                           class="px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-rose-100 dark:shadow-none transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> 
                            <span>PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="p-5 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Quantity Sold</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white" id="valTotalQuantity">{{ $totalQuantity }}</p>
                    </div>
                    <div class="p-5 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Price (Itemized Sum)</p>
                        <p class="text-2xl font-black text-emerald-600" id="valTotalPrice">RM {{ number_format($totalPrice, 2) }}</p>
                    </div>
                    <div class="p-5 bg-indigo-50/50 dark:bg-indigo-950/20 rounded-2xl border border-indigo-100/50 dark:border-indigo-950">
                        <p class="text-[10px] font-bold text-indigo-500 dark:text-indigo-400 uppercase tracking-widest mb-1">Total Sales Revenue</p>
                        <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400" id="valTotalSaleAmount">RM {{ number_format($totalSaleAmount, 2) }}</p>
                    </div>
                </div>

                <!-- Report Table -->
                @if($isManager)
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnApproveSelected" disabled onclick="approveSelectedSales()"
                                class="px-5 py-2.5 bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-600 text-xs font-bold rounded-xl transition-all cursor-not-allowed flex items-center gap-1.5 border border-slate-200/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Approve Selected (<span id="selectedCount">0</span>)</span>
                            </button>
                            <span id="filterLoadingSpinner" class="hidden">
                                <svg class="animate-spin w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Only pending sales can be selected for approval.</span>
                    </div>
                @endif

                    <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-slate-700">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-700" id="salesTable">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    @if($isManager)
                                        <th class="px-5 py-4 text-left w-12">
                                            <input type="checkbox" id="selectAll" title="Select all pending"
                                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                                        </th>
                                    @endif
                                    <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Sale ID</th>
                                    <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Event Name</th>
                                    <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                                    <th class="px-5 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Items</th>
                                    <th class="px-5 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total (RM)</th>
                                    <th class="px-5 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Approval Status</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody" class="bg-white dark:bg-slate-800 divide-y divide-slate-100 dark:divide-slate-700">
                                @if($sales->isEmpty())
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-slate-500 dark:text-slate-400 font-medium">
                                            No sale records found matching the filters.
                                        </td>
                                    </tr>
                                @else
                                    @include('reports.partials.sales-table')
                                @endif
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>

                    @if($isManager)
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const btnApproveSelected = document.getElementById('btnApproveSelected');
                const selectedCountSpan = document.getElementById('selectedCount');
                const filterForm = document.getElementById('filterForm');

                function getCheckboxes() {
                    return document.querySelectorAll('.sale-checkbox');
                }

                function updateButtonState() {
                    const checkedCheckboxes = document.querySelectorAll('.sale-checkbox:checked');
                    const count = checkedCheckboxes.length;
                    
                    selectedCountSpan.textContent = count;
                    
                    if (count > 0) {
                        btnApproveSelected.disabled = false;
                        btnApproveSelected.className = "px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all flex items-center gap-1.5 shadow-md shadow-emerald-100 dark:shadow-none border border-transparent cursor-pointer";
                    } else {
                        btnApproveSelected.disabled = true;
                        btnApproveSelected.className = "px-5 py-2.5 bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-600 text-xs font-bold rounded-xl transition-all cursor-not-allowed flex items-center gap-1.5 border border-slate-200/30";
                    }
                }

                function attachCheckboxListeners() {
                    const checkboxes = getCheckboxes();
                    checkboxes.forEach(cb => {
                        cb.addEventListener('change', function() {
                            if (!this.checked) {
                                if (selectAllCheckbox) selectAllCheckbox.checked = false;
                            } else {
                                const totalEnabled = document.querySelectorAll('.sale-checkbox').length;
                                const totalChecked = document.querySelectorAll('.sale-checkbox:checked').length;
                                if (totalEnabled === totalChecked && selectAllCheckbox) {
                                    selectAllCheckbox.checked = true;
                                }
                            }
                            updateButtonState();
                        });
                    });
                    
                    if (selectAllCheckbox) {
                        // Reset selectAll when list changes
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.onclick = function() {
                            getCheckboxes().forEach(cb => {
                                if (!cb.disabled) {
                                    cb.checked = selectAllCheckbox.checked;
                                }
                            });
                            updateButtonState();
                        };
                    }
                }

                // Initial attach
                attachCheckboxListeners();

                if (filterForm) {
                    // Submit on button click
                    filterForm.addEventListener('submit', runFilter);
                    // Also trigger on Enter key in event_name input
                    const eventNameInput = filterForm.querySelector('[name="event_name"]');
                    if (eventNameInput) {
                        eventNameInput.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter') { e.preventDefault(); runFilter.call(filterForm, e); }
                        });
                    }
                }

                function runFilter(e) {
                    e.preventDefault();
                    const url = new URL(filterForm.action);
                    const formData = new FormData(filterForm);
                    url.search = new URLSearchParams(formData).toString();

                    const tbody = document.getElementById('salesTableBody');
                    const spinner = document.getElementById('filterLoadingSpinner');
                    if (tbody) tbody.style.opacity = '0.4';
                    if (spinner) spinner.classList.remove('hidden');

                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (tbody) {
                            tbody.innerHTML = data.html;
                            tbody.style.opacity = '1';
                        }
                        if (spinner) spinner.classList.add('hidden');

                        document.getElementById('valTotalQuantity').textContent = data.totalQuantity;
                        document.getElementById('valTotalPrice').textContent = 'RM ' + data.totalPrice;
                        document.getElementById('valTotalSaleAmount').textContent = 'RM ' + data.totalSaleAmount;

                        // Sync export link params
                        document.querySelectorAll('a[href*="export"]').forEach(link => {
                            const u = new URL(link.href);
                            u.searchParams.set('start_date', formData.get('start_date'));
                            u.searchParams.set('end_date', formData.get('end_date'));
                            u.searchParams.set('event_name', formData.get('event_name'));
                            u.searchParams.set('promo_id', formData.get('promo_id'));
                            link.href = u.toString();
                        });

                        attachCheckboxListeners();
                        updateButtonState();
                    })
                    .catch(err => {
                        console.error('Filter fetch error:', err);
                        if (tbody) tbody.style.opacity = '1';
                        if (spinner) spinner.classList.add('hidden');
                    });
                }
            });

            function approveSelectedSales() {
                const checkedCheckboxes = document.querySelectorAll('.sale-checkbox:checked');
                const saleIds = Array.from(checkedCheckboxes).map(cb => cb.value);

                if (saleIds.length === 0) return;

                Swal.fire({
                    title: 'Approve Selected Sales?',
                    text: `Are you sure you want to approve the ${saleIds.length} selected sale(s)?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981', // emerald-600
                    cancelButtonColor: '#ef4444', // red-500
                    confirmButtonText: 'Yes, approve selected',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            html: 'Please wait while we approve the selected sales.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch("{{ route('reports.salesman.approve-selected', ['id' => $salesman->salesman_id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                sale_ids: saleIds
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: data.message,
                                    confirmButtonColor: '#4f46e5'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message || 'Something went wrong.',
                                    confirmButtonColor: '#4f46e5'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An unexpected error occurred.',
                                confirmButtonColor: '#4f46e5'
                            });
                        });
                    }
                });
            }
        </script>
    @endif
</x-app-layout>
