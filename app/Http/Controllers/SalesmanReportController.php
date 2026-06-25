<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class SalesmanReportController extends Controller
{
    public function index(Request $request)
    {
        $salesman = Auth::guard('salesman')->user();
        if (!$salesman) {
            abort(403, 'Unauthorized action.');
        }

        // Get filter inputs
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        $monthInput = $request->get('month'); // Format: Y-m
        $itemIdInput = $request->get('item_id');

        // Determine date range
        if ($monthInput) {
            $carbonMonth = Carbon::createFromFormat('Y-m', $monthInput);
            $startDate = $carbonMonth->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $carbonMonth->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = $startDateInput ?: Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = $endDateInput ?: Carbon::now()->format('Y-m-d');
        }

        // Base query for transaction details (SaleItem)
        $query = SaleItem::with(['sale', 'product'])
            ->whereHas('sale', function ($q) use ($salesman, $startDate, $endDate) {
                $q->where('salesman_id', $salesman->salesman_id)
                  ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });

        // Filter by item
        if ($itemIdInput) {
            $query->where('item_id', $itemIdInput);
        }

        // Fetch matched details
        $saleItems = $query->orderBy(
            DB::raw('(select sale_date from sales_transaction where sales_transaction.transaction_id = transaction_detail.transaction_id)'),
            'desc'
        )->paginate(15)->withQueryString();

        // Calculate statistics based on the same query filters (unpaginated)
        $statsQuery = SaleItem::whereHas('sale', function ($q) use ($salesman, $startDate, $endDate) {
                $q->where('salesman_id', $salesman->salesman_id)
                  ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });

        if ($itemIdInput) {
            $statsQuery->where('item_id', $itemIdInput);
        }

        $allFilteredItems = $statsQuery->get();

        $myTotalSales = $allFilteredItems->sum(function ($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });

        $myTotalTransactions = $allFilteredItems->pluck('transaction_id')->unique()->count();
        $myItemsSold = $allFilteredItems->sum('quantity');

        // Chart Trend Data: Grouped by date (Only own sales in range)
        $trendData = Sale::where('salesman_id', $salesman->salesman_id)
            ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $chartLabels = $trendData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $chartValues = $trendData->pluck('total')->map(fn($v) => (float)$v)->toArray();

        // Get list of products for dropdown filter
        $products = Product::orderBy('item_name', 'asc')->get();

        return view('reports.salesman-report', compact(
            'salesman',
            'startDate',
            'endDate',
            'monthInput',
            'itemIdInput',
            'saleItems',
            'myTotalSales',
            'myTotalTransactions',
            'myItemsSold',
            'chartLabels',
            'chartValues',
            'products'
        ));
    }

    public function export(Request $request)
    {
        $salesman = Auth::guard('salesman')->user();
        if (!$salesman) {
            abort(403, 'Unauthorized action.');
        }

        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        $monthInput = $request->get('month');
        $itemIdInput = $request->get('item_id');
        $format = $request->get('format', 'excel');

        if ($monthInput) {
            $carbonMonth = Carbon::createFromFormat('Y-m', $monthInput);
            $startDate = $carbonMonth->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $carbonMonth->copy()->endOfMonth()->format('Y-m-d');
        } else {
            $startDate = $startDateInput ?: Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = $endDateInput ?: Carbon::now()->format('Y-m-d');
        }

        // Query details
        $query = SaleItem::with(['sale', 'product'])
            ->whereHas('sale', function ($q) use ($salesman, $startDate, $endDate) {
                $q->where('salesman_id', $salesman->salesman_id)
                  ->whereBetween('sale_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });

        if ($itemIdInput) {
            $query->where('item_id', $itemIdInput);
        }

        $saleItems = $query->orderBy(
            DB::raw('(select sale_date from sales_transaction where sales_transaction.transaction_id = transaction_detail.transaction_id)'),
            'desc'
        )->get();

        $myTotalSales = $saleItems->sum(function ($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });
        $myTotalTransactions = $saleItems->pluck('transaction_id')->unique()->count();
        $myItemsSold = $saleItems->sum('quantity');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.salesman-pdf', compact(
                'salesman',
                'startDate',
                'endDate',
                'saleItems',
                'myTotalSales',
                'myTotalTransactions',
                'myItemsSold'
            ));
            return $pdf->download('my_sales_report_' . $startDate . '_to_' . $endDate . '.pdf');
        }

        // Export Excel/CSV Format (streamed response)
        $fileName = 'my_sales_report_' . $startDate . '_to_' . $endDate . '.xls';
        $headers = [
            "Content-type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($saleItems, $startDate, $endDate, $salesman, $myTotalSales, $myItemsSold, $myTotalTransactions) {
            echo '<html><body>';
            echo '<table border="1">';
            echo '<tr><th colspan="7" style="background-color: #8B5CF6; color: white;">SALES REPORT - ' . strtoupper($salesman->name) . ' (' . $startDate . ' to ' . $endDate . ')</th></tr>';
            echo '<tr>
                    <th style="background-color: #F3E8FF;">Transaction ID</th>
                    <th style="background-color: #F3E8FF;">Item Name</th>
                    <th style="background-color: #F3E8FF;">Item Code</th>
                    <th style="background-color: #F3E8FF;">Quantity</th>
                    <th style="background-color: #F3E8FF;">Unit Price (RM)</th>
                    <th style="background-color: #F3E8FF;">Total Price (RM)</th>
                    <th style="background-color: #F3E8FF;">Date</th>
                  </tr>';

            foreach ($saleItems as $item) {
                $unitPrice = $item->product->price ?? 0;
                $totalPrice = $unitPrice * $item->quantity;
                echo '<tr>';
                echo '<td>TXN-' . str_pad($item->transaction_id, 6, '0', STR_PAD_LEFT) . '</td>';
                echo '<td>' . ($item->product->item_name ?? 'N/A') . '</td>';
                echo '<td>' . ($item->product->item_code ?? 'N/A') . '</td>';
                echo '<td align="center">' . $item->quantity . '</td>';
                echo '<td align="right">' . number_format($unitPrice, 2) . '</td>';
                echo '<td align="right">' . number_format($totalPrice, 2) . '</td>';
                echo '<td>' . ($item->sale->sale_date ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            
            echo '<tr>
                    <td colspan="3" align="right"><b>TOTAL SUMMARY</b></td>
                    <td align="center"><b>' . $myItemsSold . '</b></td>
                    <td></td>
                    <td align="right"><b>' . number_format($myTotalSales, 2) . '</b></td>
                    <td><b>Txns: ' . $myTotalTransactions . '</b></td>
                  </tr>';
            echo '</table>';
            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}
