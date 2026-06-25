<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Promotion;
use App\Models\AprioriAnalysis;
use App\Models\Salesman;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\SalesExport;
use App\Exports\PromotionsExport;
use App\Exports\AprioriExport;
use App\Exports\SalesmanFinanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // 1. Sales Data
        $salesQuery = Sale::with(['salesman', 'saleItems.product'])
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
        $promotions = $promotionsQuery->get();

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
        $aprioriRules = AprioriAnalysis::orderBy('support', 'desc')->take(20)->get();

        // 4. Salesmen Data
        $salesmen = Salesman::orderBy('name', 'asc')->get();

        // 5. Pending sale counts per salesman (for the Approve badge)
        $pendingCounts = Sale::where('status', 'Pending')
            ->selectRaw('salesman_id, COUNT(*) as cnt')
            ->groupBy('salesman_id')
            ->pluck('cnt', 'salesman_id');

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
            $sales = Sale::with(['salesman', 'saleItems.product'])
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

    public function salesmanFinanceReport(Request $request, $salesman_id)
    {
        $user = Auth::guard('manager')->user() ?? Auth::guard('salesman')->user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::guard('salesman')->check() && Auth::guard('salesman')->user()->salesman_id != $salesman_id) {
            abort(403, 'Unauthorized action.');
        }

        $salesman = Salesman::findOrFail($salesman_id);

        $startDate = $request->get('start_date', now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $promoId = $request->get('promo_id', 'All');
        $eventName = $request->get('event_name', '');
        $isManager = Auth::guard('manager')->check();

        $query = SaleItem::with(['sale.salesman', 'product', 'promotion'])
            ->whereHas('sale', function($q) use ($salesman_id, $startDate, $endDate, $eventName) {
                $q->where('salesman_id', $salesman_id)
                  ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                if ($eventName) {
                    $q->where('event_name', 'like', '%' . $eventName . '%');
                }
            });

        if ($promoId && $promoId !== 'All') {
            $query->where('promo_id', $promoId);
        }

        $saleItems = $query->orderBy(
            DB::raw('(select sale_date from sales_transaction where sales_transaction.transaction_id = transaction_detail.transaction_id)'),
            'desc'
        )->get();

        $totalQuantity = $saleItems->sum('quantity');
        $totalPrice = $saleItems->sum(function($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });

        $transactionIds = $saleItems->pluck('transaction_id')->unique();
        $totalSaleAmount = Sale::whereIn('transaction_id', $transactionIds)->sum('total_amount');

        // Fetch promotions for filter dropdown
        $promotions = Promotion::where('status', 'Active')
            ->orWhereIn('promo_id', $saleItems->pluck('promo_id')->filter()->unique())
            ->get();

        // Load all sales (transactions) for bulk approval / list table
        $salesQuery = Sale::with(['saleItems.product'])
            ->where('salesman_id', $salesman_id)
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($eventName) {
            $salesQuery->where('event_name', 'like', '%' . $eventName . '%');
        }

        $sales = $salesQuery->orderBy('sale_date', 'desc')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('reports.partials.sales-table', compact('sales', 'isManager'))->render(),
                'totalQuantity' => $totalQuantity,
                'totalPrice' => number_format($totalPrice, 2),
                'totalSaleAmount' => number_format($totalSaleAmount, 2),
            ]);
        }

        return view('reports.salesman-finance', compact(
            'salesman', 'startDate', 'endDate', 'promoId', 'eventName',
            'saleItems', 'totalQuantity', 'totalPrice', 'totalSaleAmount', 'promotions', 'sales', 'isManager'
        ));
    }

    /**
     * Bulk-approve selected sales for a given salesman.
     */
    public function approveSelectedSales(Request $request, $salesman_id)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403, 'Only managers can approve sales.');
        }

        $salesman = Salesman::findOrFail($salesman_id);

        $request->validate([
            'sale_ids' => 'required|array',
            'sale_ids.*' => 'exists:sales_transaction,transaction_id'
        ]);

        $count = 0;
        DB::transaction(function () use ($request, $salesman_id, &$count) {
            $count = Sale::whereIn('transaction_id', $request->sale_ids)
                ->where('salesman_id', $salesman_id)
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

    public function exportSalesmanReport(Request $request, $salesman_id)
    {
        $user = Auth::guard('manager')->user() ?? Auth::guard('salesman')->user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::guard('salesman')->check() && Auth::guard('salesman')->user()->salesman_id != $salesman_id) {
            abort(403, 'Unauthorized action.');
        }

        $salesman = Salesman::findOrFail($salesman_id);

        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $promoId = $request->get('promo_id', 'All');
        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            $query = SaleItem::with(['sale.salesman', 'product', 'promotion'])
                ->whereHas('sale', function($q) use ($salesman_id, $startDate, $endDate) {
                    $q->where('salesman_id', $salesman_id)
                      ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                });

            if ($promoId && $promoId !== 'All') {
                $query->where('promo_id', $promoId);
            }

            $saleItems = $query->orderBy(
                DB::raw('(select sale_date from sales_transaction where sales_transaction.transaction_id = transaction_detail.transaction_id)'),
                'desc'
            )->get();

            $totalQuantity = $saleItems->sum('quantity');
            $totalPrice = $saleItems->sum(function($item) {
                return ($item->product->price ?? 0) * $item->quantity;
            });

            $transactionIds = $saleItems->pluck('transaction_id')->unique();
            $totalSaleAmount = Sale::whereIn('transaction_id', $transactionIds)->sum('total_amount');

            $pdf = Pdf::loadView('reports.pdf.salesman-finance', compact(
                'salesman', 'saleItems', 'startDate', 'endDate', 'promoId',
                'totalQuantity', 'totalPrice', 'totalSaleAmount'
            ));
            return $pdf->download('salesman_report_'.$salesman->username.'_'.$startDate.'_to_'.$endDate.'.pdf');
        }

        return Excel::download(
            new SalesmanFinanceExport($salesman_id, $startDate, $endDate, $promoId),
            'salesman_report_'.$salesman->username.'_'.$startDate.'_to_'.$endDate.'.xlsx'
        );
    }

    /**
     * Bulk-approve all Pending sales for a given salesman.
     * Manager-only action (enforced by route middleware + explicit check here).
     */
    public function approvePendingSales(Request $request, $salesman_id)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403, 'Only managers can approve sales.');
        }

        $salesman = Salesman::findOrFail($salesman_id);

        $pendingSales = Sale::where('salesman_id', $salesman_id)
            ->where('status', 'Pending')
            ->get();

        if ($pendingSales->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending sales found for ' . $salesman->name . '.',
            ]);
        }

        $count = 0;
        DB::transaction(function () use ($salesman_id, &$count) {
            $count = Sale::where('salesman_id', $salesman_id)
                ->where('status', 'Pending')
                ->update([
                    'status'      => 'Approved',
                    'date_verify' => now(),
                    'approved_by' => Auth::guard('manager')->id(),
                ]);
        });

        return response()->json([
            'success' => true,
            'message' => $count . ' pending sale(s) for ' . $salesman->name . ' approved successfully.',
            'approved_count' => $count,
        ]);
    }
}
