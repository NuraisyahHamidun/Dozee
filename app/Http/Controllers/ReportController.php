<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Promotion;
use App\Models\AprioriAnalysis;
use App\Models\Salesmen;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\SalesExport;
use App\Exports\PromotionsExport;
use App\Exports\AprioriExport;
use App\Exports\SalesmenFinanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // 1. Sales Data
        $salesQuery = Sale::with(['salesmen', 'saleItems.product'])
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $totalSalesRevenue = (clone $salesQuery)->sum('total_amount');
        $totalSalesCount = (clone $salesQuery)->count();
        $sales = $salesQuery->orderBy('sale_date', 'desc')->paginate(15)->withQueryString();

        // 2. Promotions Data
        $promoStatus = $request->get('promo_status', 'All');
        $promotionsQuery = Promotion::query();
        if ($promoStatus !== 'All') {
            $promotionsQuery->where('status', $promoStatus);
        }
        $promotionsQuery->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
        $promotions = $promotionsQuery->paginate(10, ['*'], 'promo_page')->appends([
            'tab' => 'promotions',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'promo_status' => $promoStatus,
        ]);

        // Promotion Revenue calculation
        $promoRevenue = [];
        foreach ($promotions as $promo) {
            $rev = DB::table('transaction_detail')
                ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
                ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
                ->where('transaction_detail.promo_id', $promo->promo_id)
                ->sum(DB::raw('transaction_detail.quantity * item.price'));
            $salesCount = DB::table('transaction_detail')
                ->where('promo_id', $promo->promo_id)
                ->sum('quantity');
            $promoRevenue[$promo->promo_id] = [
                'revenue' => $rev,
                'sales_count' => $salesCount
            ];
        }

        // 3. Apriori Data
        // AprioriAnalysis doesn't have a date by default in many implementations, 
        // but we'll fetch the rules and possibly filter if there's a timestamp.
        $aprioriRules = AprioriAnalysis::orderBy('support', 'desc')->paginate(10, ['*'], 'apriori_page')->appends(['tab' => 'apriori']);

        // 4. Salesmen Data
        $salesmen = Salesmen::orderBy('name', 'asc')->get();

        // 5. Pending sale counts per salesmen (for the Approve badge)
        $pendingCounts = Sale::where('status', 'Pending')
            ->selectRaw('salesmen_id, COUNT(*) as cnt')
            ->groupBy('salesmen_id')
            ->pluck('cnt', 'salesmen_id');

        return view('reports.index', compact(
            'startDate', 'endDate', 'promoStatus',
            'sales', 'totalSalesRevenue', 'totalSalesCount',
            'promotions', 'promoRevenue',
            'aprioriRules', 'salesmen', 'pendingCounts'
        ));
    }

    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            $sales = Sale::with(['salesmen', 'saleItems.product'])
                ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('sale_date', 'desc')->get();
            $totalSalesRevenue = $sales->sum('total_amount');
            $pdf = Pdf::loadView('reports.pdf.sales', compact('sales', 'startDate', 'endDate', 'totalSalesRevenue'));
            return $pdf->download('sales_report_'.$startDate.'_to_'.$endDate.'.pdf');
        }

        return Excel::download(new SalesExport($startDate, $endDate), 'sales_report_'.$startDate.'_to_'.$endDate.'.xlsx');
    }

    public function exportPromotions(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $promoStatus = $request->get('promo_status', 'All');
        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            $promotionsQuery = Promotion::query();
            if ($promoStatus !== 'All') {
                $promotionsQuery->where('status', $promoStatus);
            }
            $promotionsQuery->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });
            $promotions = $promotionsQuery->get();

            $promoRevenue = [];
            foreach ($promotions as $promo) {
                $rev = DB::table('transaction_detail')
                    ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
                    ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
                    ->where('transaction_detail.promo_id', $promo->promo_id)
                    ->sum(DB::raw('transaction_detail.quantity * item.price'));
                $salesCount = DB::table('transaction_detail')
                    ->where('promo_id', $promo->promo_id)
                    ->sum('quantity');
                $promoRevenue[$promo->promo_id] = [
                    'revenue' => $rev,
                    'sales_count' => $salesCount
                ];
            }
            $pdf = Pdf::loadView('reports.pdf.promotions', compact('promotions', 'promoRevenue', 'startDate', 'endDate', 'promoStatus'));
            return $pdf->download('promotions_report_'.$startDate.'_to_'.$endDate.'.pdf');
        }

        return Excel::download(new PromotionsExport($startDate, $endDate, $promoStatus), 'promotions_report_'.$startDate.'_to_'.$endDate.'.xlsx');
    }

    public function exportApriori(Request $request)
    {
        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            $aprioriRules = AprioriAnalysis::orderBy('support', 'desc')->get();
            $pdf = Pdf::loadView('reports.pdf.apriori', compact('aprioriRules'));
            return $pdf->download('market_basket_analysis_report.pdf');
        }

        return Excel::download(new AprioriExport(), 'market_basket_analysis_report.xlsx');
    }

    public function salesmenFinanceReport(Request $request, $salesmen_id)
    {
        $user = Auth::guard('manager')->user() ?? Auth::guard('salesmen')->user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::guard('salesmen')->check() && Auth::guard('salesmen')->user()->salesmen_id != $salesmen_id) {
            abort(403, 'Unauthorized action.');
        }

        $salesmen = Salesmen::findOrFail($salesmen_id);

        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $promoId = $request->get('promo_id', 'All');
        $eventName = $request->get('event_name', '');
        $isManager = Auth::guard('manager')->check();

        // Optimized stats calculation using database queries with joins
        $statsQuery = DB::table('transaction_detail')
            ->join('sales_transaction', 'sales_transaction.transaction_id', '=', 'transaction_detail.transaction_id')
            ->join('item', 'item.item_id', '=', 'transaction_detail.item_id')
            ->where('sales_transaction.salesmen_id', $salesmen_id)
            ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($eventName) {
            $statsQuery->where('sales_transaction.event_name', 'like', '%' . $eventName . '%');
        }

        if ($promoId && $promoId !== 'All') {
            $statsQuery->where('transaction_detail.promo_id', $promoId);
        }

        $stats = $statsQuery->select(
            DB::raw('SUM(transaction_detail.quantity) as total_quantity'),
            DB::raw('SUM(item.price * transaction_detail.quantity) as total_price'),
            DB::raw('COUNT(DISTINCT transaction_detail.transaction_id) as total_transactions')
        )->first();

        $totalQuantity = (int) ($stats->total_quantity ?? 0);
        $totalPrice = (float) ($stats->total_price ?? 0);

        // Fetch transaction IDs for total amount query
        $transactionIdsQuery = DB::table('transaction_detail')
            ->join('sales_transaction', 'sales_transaction.transaction_id', '=', 'transaction_detail.transaction_id')
            ->where('sales_transaction.salesmen_id', $salesmen_id)
            ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($eventName) {
            $transactionIdsQuery->where('sales_transaction.event_name', 'like', '%' . $eventName . '%');
        }

        if ($promoId && $promoId !== 'All') {
            $transactionIdsQuery->where('transaction_detail.promo_id', $promoId);
        }

        $transactionIds = $transactionIdsQuery->distinct()->pluck('transaction_id')->toArray();
        $totalSaleAmount = Sale::whereIn('transaction_id', $transactionIds)->sum('total_amount');

        // Fetch unique promo IDs directly from DB
        $promoIds = DB::table('transaction_detail')
            ->join('sales_transaction', 'sales_transaction.transaction_id', '=', 'transaction_detail.transaction_id')
            ->where('sales_transaction.salesmen_id', $salesmen_id)
            ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->distinct()
            ->pluck('promo_id')
            ->filter()
            ->toArray();

        $promotions = Promotion::where('status', 'Active')
            ->orWhereIn('promo_id', $promoIds)
            ->get();

        $saleItems = collect();

        // Load all sales (transactions) for bulk approval / list table
        $salesQuery = Sale::with(['saleItems.product'])
            ->where('salesmen_id', $salesmen_id)
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($eventName) {
            $salesQuery->where('event_name', 'like', '%' . $eventName . '%');
        }

        $sales = $salesQuery->orderBy('sale_date', 'desc')->paginate(5)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('reports.partials.sales-table', compact('sales', 'isManager'))->render(),
                'pagination' => $sales->hasPages() ? $sales->links()->render() : '',
                'totalQuantity' => $totalQuantity,
                'totalPrice' => number_format($totalPrice, 2),
                'totalSaleAmount' => number_format($totalSaleAmount, 2),
            ]);
        }

        return view('reports.salesmen-finance', compact(
            'salesmen', 'startDate', 'endDate', 'promoId', 'eventName',
            'saleItems', 'totalQuantity', 'totalPrice', 'totalSaleAmount', 'promotions', 'sales', 'isManager'
        ));
    }

    /**
     * Bulk-approve selected sales for a given salesmen.
     */
    public function approveSelectedSales(Request $request, $salesmen_id)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403, 'Only managers can approve sales.');
        }

        $salesmen = Salesmen::findOrFail($salesmen_id);

        $request->validate([
            'sale_ids' => 'required|array',
            'sale_ids.*' => 'exists:sales_transaction,transaction_id'
        ]);

        $count = 0;
        DB::transaction(function () use ($request, $salesmen_id, &$count) {
            $count = Sale::whereIn('transaction_id', $request->sale_ids)
                ->where('salesmen_id', $salesmen_id)
                ->where('status', 'Pending')
                ->update([
                    'status' => 'Approved',
                    'date_verify' => now(),
                    'approved_by' => Auth::guard('manager')->id(),
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => $count . ' pending sale(s) approved successfully.',
            'approved_count' => $count,
        ]);
    }

    public function exportSalesmenReport(Request $request, $salesmen_id)
    {
        $user = Auth::guard('manager')->user() ?? Auth::guard('salesmen')->user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::guard('salesmen')->check() && Auth::guard('salesmen')->user()->salesmen_id != $salesmen_id) {
            abort(403, 'Unauthorized action.');
        }

        $salesmen = Salesmen::findOrFail($salesmen_id);

        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $promoId = $request->get('promo_id', 'All');
        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            $query = SaleItem::with(['sale.salesmen', 'product', 'promotion'])
                ->select('transaction_detail.*')
                ->join('sales_transaction', 'sales_transaction.transaction_id', '=', 'transaction_detail.transaction_id')
                ->where('sales_transaction.salesmen_id', $salesmen_id)
                ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            if ($promoId && $promoId !== 'All') {
                $query->where('transaction_detail.promo_id', $promoId);
            }

            $saleItems = $query->orderBy('sales_transaction.sale_date', 'desc')->get();

            $totalQuantity = $saleItems->sum('quantity');
            $totalPrice = $saleItems->sum(function($item) {
                return ($item->product->price ?? 0) * $item->quantity;
            });

            $transactionIds = $saleItems->pluck('transaction_id')->unique();
            $totalSaleAmount = Sale::whereIn('transaction_id', $transactionIds)->sum('total_amount');

            $pdf = Pdf::loadView('reports.pdf.salesmen-finance', compact(
                'salesmen', 'saleItems', 'startDate', 'endDate', 'promoId',
                'totalQuantity', 'totalPrice', 'totalSaleAmount'
            ));
            return $pdf->download('salesmen_report_'.$salesmen->username.'_'.$startDate.'_to_'.$endDate.'.pdf');
        }

        return Excel::download(
            new SalesmenFinanceExport($salesmen_id, $startDate, $endDate, $promoId),
            'salesmen_report_'.$salesmen->username.'_'.$startDate.'_to_'.$endDate.'.xlsx'
        );
    }

    /**
     * Bulk-approve all Pending sales for a given salesmen.
     * Manager-only action (enforced by route middleware + explicit check here).
     */
    public function approvePendingSales(Request $request, $salesmen_id)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403, 'Only managers can approve sales.');
        }

        $salesmen = Salesmen::findOrFail($salesmen_id);

        $pendingSales = Sale::where('salesmen_id', $salesmen_id)
            ->where('status', 'Pending')
            ->get();

        if ($pendingSales->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending sales found for ' . $salesmen->name . '.',
            ]);
        }

        $count = 0;
        DB::transaction(function () use ($salesmen_id, &$count) {
            $count = Sale::where('salesmen_id', $salesmen_id)
                ->where('status', 'Pending')
                ->update([
                    'status'      => 'Approved',
                    'date_verify' => now(),
                    'approved_by' => Auth::guard('manager')->id(),
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => $count . ' pending sale(s) for ' . $salesmen->name . ' approved successfully.',
            'approved_count' => $count,
        ]);
    }
}
