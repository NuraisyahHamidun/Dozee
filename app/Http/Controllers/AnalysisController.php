<?php

namespace App\Http\Controllers;

use App\Services\AprioriService;
use App\Models\Product;
use App\Models\AprioriAnalysis;
use App\Models\Sale;
use App\Models\Salesman;
use App\Services\WekaService;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('analysis.weka');
    }

    public function wekaIndex(Request $request)
    {
        $minSupport = (float) $request->get('support', 0.02);
        $minConfidence = (float) $request->get('confidence', 0.6);
        $numRules = (int) $request->get('num_rules', 20);
        $eventName = $request->get('event_name');
        $salesmanId = $request->get('salesman_id');

        // Always load top 4 strongest rules from DB (sorted by highest lift, then highest confidence)
        $results = AprioriAnalysis::with([
            'antecedentProduct:item_id,item_name',
            'consequentProduct:item_id,item_name',
        ])->orderByDesc('lift')->orderByDesc('confidence')->take(4)->get();

        // Fetch all rules for charts and best rule calculations
        $allRules = AprioriAnalysis::orderByDesc('lift')->get();
        $bestRule = AprioriAnalysis::orderByDesc('lift')->first();

        // Count Weka-eligible transactions (transactions with >= 2 items)
        $txQuery = Sale::has('saleItems', '>=', 2);
        if (!empty($eventName)) {
            $txQuery->where('event_name', $eventName);
        }
        if (!empty($salesmanId)) {
            $txQuery->where('salesman_id', $salesmanId);
        }
        $wekaTransactionsCount = $txQuery->count();

        // All items for name/code lookup (keyed by item_id)
        $items = Product::pluck('item_name', 'item_id');
        $itemCodes = Product::pluck('item_code', 'item_id');

        // Aggregate stats
        $allRulesCount = AprioriAnalysis::count();
        $topLift = AprioriAnalysis::max('lift') ?? 0;
        $avgConfidence = AprioriAnalysis::avg('confidence') ?? 0;

        $stats = [
            'total_sales' => Sale::count(),
            'total_products' => Product::count(),
            'rules_count' => $allRulesCount,
            'top_lift' => $topLift,
            'avg_confidence' => $avgConfidence,
            'weka_transactions' => $wekaTransactionsCount,
        ];

        $eventNames = Sale::whereNotNull('event_name')->distinct()->pluck('event_name');
        $salesmen = Salesman::pluck('name', 'salesman_id');

        // Fetch Weka raw log
        $logPath = storage_path('app/weka/weka_output.txt');
        $wekaLog = file_exists($logPath) ? file_get_contents($logPath) : 'No execution logs available yet. Please run the Apriori engine.';

        return view('analysis.weka', compact(
            'results',
            'allRules',
            'bestRule',
            'items',
            'itemCodes',
            'minSupport',
            'minConfidence',
            'numRules',
            'stats',
            'eventNames',
            'salesmen',
            'eventName',
            'salesmanId',
            'wekaLog'
        ));
    }

    public function runApriori(Request $request)
    {
        $minSupport = max(0.001, min(1.0, (float) $request->get('support', 0.02)));
        $minConfidence = max(0.001, min(1.0, (float) $request->get('confidence', 0.6)));
        $numRules = max(1, min(1000, (int) $request->get('num_rules', 20)));
        $eventName = $request->get('event_name');
        $salesmanId = $request->get('salesman_id');

        $startTime = microtime(true);
        
        $wekaService = new WekaService();
        $wekaService->run([
            'event_name' => $eventName,
            'salesman_id' => $salesmanId,
        ], $numRules, $minConfidence, $minSupport);

        $executionTime = round(microtime(true) - $startTime, 3);
        session()->flash('weka_execution_time', $executionTime);

        return redirect()->route('analysis.weka', [
            'support' => $minSupport,
            'confidence' => $minConfidence,
            'num_rules' => $numRules,
            'event_name' => $eventName,
            'salesman_id' => $salesmanId,
            'refresh' => 1
        ])->with('success', 'Weka Apriori Analysis completed successfully!');
    }

    public function allRules(Request $request)
    {
        $search     = $request->get('search', '');
        $sortBy     = $request->get('sort', 'lift');
        $sortDir    = in_array($request->get('dir', 'desc'), ['asc', 'desc']) ? $request->get('dir', 'desc') : 'desc';
        $filterSupp = $request->get('support_filter', '');
        $filterConf = $request->get('confidence_filter', '');
        $highImpact = $request->boolean('high_impact');
        $validSort  = ['lift', 'confidence', 'support', 'conviction'];
        if (!in_array($sortBy, $validSort)) $sortBy = 'lift';

        $query = AprioriAnalysis::with([
            'antecedentProduct:item_id,item_name',
            'consequentProduct:item_id,item_name',
        ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('antecedent', 'like', "%{$search}%")
                  ->orWhere('consequent', 'like', "%{$search}%")
                  ->orWhere('rule_text',  'like', "%{$search}%");
            });
        }

        if ($filterSupp === 'high')        $query->where('support', '>=', 0.3);
        elseif ($filterSupp === 'medium')  $query->whereBetween('support', [0.1, 0.3]);
        elseif ($filterSupp === 'low')     $query->where('support', '<', 0.1);

        if ($filterConf === 'high')        $query->where('confidence', '>=', 0.8);
        elseif ($filterConf === 'medium')  $query->whereBetween('confidence', [0.5, 0.8]);
        elseif ($filterConf === 'low')     $query->where('confidence', '<', 0.5);

        if ($highImpact) $query->where('lift', '>=', 3);

        $query->orderBy($sortBy, $sortDir);

        $rules = $query->paginate(10)->withQueryString();

        $stats = [
            'total'          => AprioriAnalysis::count(),
            'high_impact'    => AprioriAnalysis::where('lift', '>=', 3)->count(),
            'avg_confidence' => AprioriAnalysis::avg('confidence') ?? 0,
            'max_lift'       => AprioriAnalysis::max('lift') ?? 0,
        ];

        $items = Product::pluck('item_name', 'item_id');
        $itemCodes = Product::pluck('item_code', 'item_id');

        return view('analysis.all-rules', compact(
            'rules', 'stats', 'items', 'itemCodes',
            'search', 'sortBy', 'sortDir',
            'filterSupp', 'filterConf', 'highImpact'
        ));
    }
}
