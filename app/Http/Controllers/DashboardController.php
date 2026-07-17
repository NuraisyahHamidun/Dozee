<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Salesmen;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $salesmen = Auth::guard('salesmen')->user();
        
        $query = Sale::query();
        if ($salesmen) {
            $query->where('salesmen_id', $salesmen->salesmen_id);
        }

        $salesCount = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('total_amount');
        $productCount = Product::count();
        $activePromotions = Promotion::where('status', 'Active')->count();

        // Data for chart: Sales in the last 7 days
        $salesData = (clone $query)->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
            ->reverse();

        $chartLabels = $salesData->pluck('date')->toArray();
        $chartValues = $salesData->pluck('total')->toArray();

        // Data for chart: Monthly Sales
        $monthlySalesData = (clone $query)->select(DB::raw('DATE_FORMAT(sale_date, "%Y-%m") as month'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get()
            ->reverse();

        $monthlyChartLabels = $monthlySalesData->pluck('month')->map(function($m) {
            return \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y');
        })->toArray();
        $monthlyChartValues = $monthlySalesData->pluck('total')->toArray();

        // Top 5 Products
        $topProductsQuery = DB::table('transaction_detail')
            ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
            ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
            ->select('item.item_name', DB::raw('SUM(transaction_detail.quantity) as total_qty'))
            ->groupBy('item.item_id', 'item.item_name')
            ->orderBy('total_qty', 'desc')
            ->take(5);

        if ($salesmen) {
            $topProductsQuery->where('sales_transaction.salesmen_id', $salesmen->salesmen_id);
        }

        $topProducts = $topProductsQuery->get();

        $salesmenPerformance = [];
        if (Auth::guard('manager')->check()) {
            $salesmenPerformance = Sale::join('salesmen', 'sales_transaction.salesmen_id', '=', 'salesmen.salesmen_id')
                ->select('salesmen.name', DB::raw('SUM(total_amount) as total'))
                ->groupBy('salesmen.salesmen_id', 'salesmen.name')
                ->orderBy('total', 'desc')
                ->take(3)
                ->get();
        }

        // Staff Specific variables
        $itemsSold = 0;
        $activeDeals = 0;
        $performanceScore = 0;
        $bestSellingProduct = null;
        $salesThisWeek = 0;
        $salesLastWeek = 0;
        $salesGrowth = 0;

        if ($salesmen) {
            $itemsSold = (int) DB::table('transaction_detail')
                ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
                ->where('sales_transaction.salesmen_id', $salesmen->salesmen_id)
                ->sum('transaction_detail.quantity');

            $activeDeals = Promotion::where('status', 'Active')
                ->where(function($q) use ($salesmen) {
                    $q->whereNull('salesmen_id')
                      ->orWhere('salesmen_id', $salesmen->salesmen_id);
                })->count();

            // Personal Performance Score: e.g., target is RM1500 monthly sales
            $performanceScore = $totalRevenue > 0 ? min(100, round(($totalRevenue / 1500) * 100)) : 0;

            $bestSellingProduct = DB::table('transaction_detail')
                ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
                ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
                ->select('item.item_name', DB::raw('SUM(transaction_detail.quantity) as total_qty'))
                ->where('sales_transaction.salesmen_id', $salesmen->salesmen_id)
                ->groupBy('item.item_id', 'item.item_name')
                ->orderBy('total_qty', 'desc')
                ->first();

            $salesThisWeek = Sale::where('salesmen_id', $salesmen->salesmen_id)
                ->whereBetween('sale_date', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()])
                ->sum('total_amount');

            $salesLastWeek = Sale::where('salesmen_id', $salesmen->salesmen_id)
                ->whereBetween('sale_date', [\Carbon\Carbon::now()->subWeek()->startOfWeek(), \Carbon\Carbon::now()->subWeek()->endOfWeek()])
                ->sum('total_amount');

            if ($salesLastWeek > 0) {
                $salesGrowth = round((($salesThisWeek - $salesLastWeek) / $salesLastWeek) * 100, 1);
            } elseif ($salesThisWeek > 0) {
                $salesGrowth = 100;
            }
        }

        return view('dashboard', compact(
            'salesCount', 
            'totalRevenue', 
            'productCount', 
            'activePromotions',
            'chartLabels',
            'chartValues',
            'monthlyChartLabels',
            'monthlyChartValues',
            'topProducts',
            'salesmenPerformance',
            'itemsSold',
            'activeDeals',
            'performanceScore',
            'bestSellingProduct',
            'salesThisWeek',
            'salesLastWeek',
            'salesGrowth'
        ));
    }

    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = Sale::with(['salesmen', 'saleItems.product'])
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (Auth::guard('salesmen')->check()) {
            $query->where('salesmen_id', Auth::guard('salesmen')->user()->salesmen_id);
        }

        // Calculate totals from a clone of the query before pagination
        $statsQuery = clone $query;
        $totalRevenue = $statsQuery->sum('total_amount');
        $totalSales = $statsQuery->count();
        $avgSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        $sales = $query->orderBy('sale_date', 'desc')->paginate(10)->withQueryString();
        
        return view('reports.index', compact('sales', 'totalRevenue', 'totalSales', 'avgSale', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $query = Sale::with(['salesmen'])
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if (Auth::guard('salesmen')->check()) {
            $query->where('salesmen_id', Auth::guard('salesmen')->user()->salesmen_id);
        }

        $sales = $query->get();

        $fileName = 'sales_report_' . $startDate . '_to_' . $endDate . '.xls';

        $headers = array(
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($sales, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Output HTML table that Excel can read
            echo '<html><body>';
            echo '<table border="1">';
            echo '<tr><th colspan="4" style="background-color: #4F46E5; color: white;">SALES REPORT (' . $startDate . ' to ' . $endDate . ')</th></tr>';
            echo '<tr>
                    <th style="background-color: #F1F5F9;">Sale ID</th>
                    <th style="background-color: #F1F5F9;">Date</th>
                    <th style="background-color: #F1F5F9;">Salesmen</th>
                    <th style="background-color: #F1F5F9;">Amount (RM)</th>
                  </tr>';

            $total = 0;
            foreach ($sales as $sale) {
                $total += $sale->total_amount;
                echo '<tr>';
                echo '<td>TXN-' . str_pad($sale->transaction_id, 6, '0', STR_PAD_LEFT) . '</td>';
                echo '<td>' . $sale->sale_date . '</td>';
                echo '<td>' . ($sale->salesmen->name ?? 'N/A') . '</td>';
                echo '<td align="right">' . number_format($sale->total_amount, 2) . '</td>';
                echo '</tr>';
            }
            
            echo '<tr><td colspan="3" align="right"><b>TOTAL REVENUE</b></td><td align="right"><b>' . number_format($total, 2) . '</b></td></tr>';
            echo '</table>';
            echo '</body></html>';

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function getChartData(Request $request)
    {
        $salesmen = Auth::guard('salesmen')->user();
        
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        
        $baseQuery = Sale::query()->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($salesmen) {
            $baseQuery->where('salesmen_id', $salesmen->salesmen_id);
        }

        // 1. Daily Sales
        $dailyData = (clone $baseQuery)->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // 2. Monthly Sales
        $monthlyData = (clone $baseQuery)->select(DB::raw('DATE_FORMAT(sale_date, "%Y-%m") as month'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()->map(function($item) {
                $item->month = \Carbon\Carbon::createFromFormat('Y-m', $item->month)->format('M Y');
                return $item;
            });

        // 3. Promotion Performance
        $promoQuery = DB::table('transaction_detail')
            ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
            ->join('promotion', 'transaction_detail.promo_id', '=', 'promotion.promo_id')
            ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
            ->select('promotion.promo_name', DB::raw('SUM(transaction_detail.quantity * item.price) as total_revenue'))
            ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
        if ($salesmen) {
            $promoQuery->where('sales_transaction.salesmen_id', $salesmen->salesmen_id);
        }
        $promoData = $promoQuery->groupBy('promotion.promo_id', 'promotion.promo_name')
            ->orderBy('total_revenue', 'desc')
            ->take(10)
            ->get();

        // 4. Single vs Combo Sales
        $comboQuery = DB::table('transaction_detail')
            ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
            ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
            ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
        if ($salesmen) {
            $comboQuery->where('sales_transaction.salesmen_id', $salesmen->salesmen_id);
        }

        $comboData = (clone $comboQuery)->whereNotNull('transaction_detail.promo_id')->sum(DB::raw('transaction_detail.quantity * item.price'));
        $singleData = (clone $comboQuery)->whereNull('transaction_detail.promo_id')->sum(DB::raw('transaction_detail.quantity * item.price'));

        // 5. Top Selling Items
        $topItemsQuery = DB::table('transaction_detail')
            ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
            ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
            ->select('item.item_name', DB::raw('SUM(transaction_detail.quantity) as total_qty'), DB::raw('SUM(transaction_detail.quantity * item.price) as total_revenue'))
            ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
        if ($salesmen) {
            $topItemsQuery->where('sales_transaction.salesmen_id', $salesmen->salesmen_id);
        }
        
        $sortBy = $request->get('sort_by', 'quantity');
        $orderColumn = $sortBy === 'revenue' ? 'total_revenue' : 'total_qty';

        $topItemsData = $topItemsQuery->groupBy('item.item_id', 'item.item_name')
            ->orderBy($orderColumn, 'desc')
            ->take(10)
            ->get();

        // 6. Apriori Item Combinations
        $aprioriData = \App\Models\AprioriAnalysis::orderBy('support', 'desc')
            ->take(10)
            ->get()
            ->map(function($rule) {
                $antecedentNames = '';
                if ($rule->isMultiAntecedent()) {
                    $ids = $rule->antecedentIds();
                    $names = \App\Models\Product::whereIn('item_id', $ids)->pluck('item_name')->toArray();
                    $antecedentNames = implode(' + ', $names);
                } else {
                    $p = \App\Models\Product::find($rule->antecedent);
                    $antecedentNames = $p ? $p->item_name : $rule->antecedent;
                }
                
                $p2 = \App\Models\Product::find($rule->consequent);
                $consequentName = $p2 ? $p2->item_name : $rule->consequent;
                
                return [
                    'rule_id' => $rule->rule_id,
                    'label' => $antecedentNames . ' => ' . $consequentName,
                    'support' => $rule->support,
                    'confidence' => $rule->confidence,
                    'lift' => $rule->lift,
                ];
            });

        // 7. Category Distribution (Pie/Doughnut Chart)
        $categoryQuery = DB::table('transaction_detail')
            ->join('sales_transaction', 'transaction_detail.transaction_id', '=', 'sales_transaction.transaction_id')
            ->join('item', 'transaction_detail.item_id', '=', 'item.item_id')
            ->select('item.category', DB::raw('SUM(transaction_detail.quantity) as total_qty'))
            ->whereBetween('sales_transaction.sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
        if ($salesmen) {
            $categoryQuery->where('sales_transaction.salesmen_id', $salesmen->salesmen_id);
        }
        $categoryData = $categoryQuery->groupBy('item.category')->get();

        return response()->json([
            'daily' => [
                'labels' => $dailyData->pluck('date'),
                'values' => $dailyData->pluck('total')
            ],
            'monthly' => [
                'labels' => $monthlyData->pluck('month'),
                'values' => $monthlyData->pluck('total')
            ],
            'promo' => [
                'labels' => $promoData->pluck('promo_name'),
                'values' => $promoData->pluck('total_revenue')
            ],
            'combo' => [
                'labels' => ['Combo/Promo', 'Single Item'],
                'values' => [(float)$comboData, (float)$singleData]
            ],
            'topItems' => [
                'labels' => $topItemsData->pluck('item_name'),
                'quantities' => $topItemsData->pluck('total_qty'),
                'revenues' => $topItemsData->pluck('total_revenue')
            ],
            'apriori' => [
                'labels' => $aprioriData->pluck('label'),
                'support' => $aprioriData->pluck('support'),
                'confidence' => $aprioriData->pluck('confidence'),
                'lift' => $aprioriData->pluck('lift'),
                'rule_ids' => $aprioriData->pluck('rule_id'),
            ],
            'categoryDistribution' => [
                'labels' => $categoryData->pluck('category'),
                'values' => $categoryData->pluck('total_qty')->map(fn($v) => (int)$v)
            ]
        ]);
    }
}
