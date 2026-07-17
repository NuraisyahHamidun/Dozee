<?php

namespace App\Http\Controllers;

use App\Services\AprioriService;
use App\Models\Product;
use App\Models\AprioriAnalysis;
use App\Models\Sale;
use App\Models\Salesmen;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $minSupport = (float) $request->get('support', 0.05);
        $minConfidence = (float) $request->get('confidence', 0.3);
        $eventName = $request->get('event_name');
        $salesmanId = $request->get('salesman_id');

        // Run analysis only if the database is empty of rules, or if recalculation is explicitly requested.
        // This avoids 504 Timeout on free cloud hosting instances.
        $hasRules = AprioriAnalysis::exists();
        if (Sale::exists() && !app()->runningUnitTests() && (!$hasRules || $request->has('recalculate'))) {
            $aprioriService = new AprioriService($minSupport, $minConfidence);
            $aprioriService->run([
                'event_name' => $eventName,
                'salesmen_id' => $salesmanId,
            ]);
        }

        $rules = AprioriAnalysis::all();
        $products = Product::all();

        // Compile grouped association data for Left Table and Right Panel selection
        $associationData = [];
        foreach ($products as $product) {
            $productRules = $rules->filter(function($rule) use ($product) {
                if (!$rule->isMultiAntecedent()) {
                    return $rule->antecedent == $product->item_id;
                }
                return in_array($product->item_id, $rule->antecedentIds());
            })->sortByDesc('confidence');

            if ($productRules->isEmpty()) {
                continue;
            }

            $partners = [];
            foreach ($productRules as $rule) {
                $consequentProduct = $products->firstWhere('item_id', $rule->consequent);
                if ($consequentProduct) {
                    $partners[] = [
                        'item_id' => $consequentProduct->item_id,
                        'item_code' => $consequentProduct->item_code ?? 'ITM-' . str_pad($consequentProduct->item_id, 4, '0', STR_PAD_LEFT),
                        'item_name' => $consequentProduct->item_name,
                        'support' => round($rule->support * 100, 2),
                        'confidence' => round($rule->confidence * 100, 1),
                        'lift' => round($rule->lift, 2),
                        'rule_id' => $rule->rule_id,
                    ];
                }
            }

            $associationData[] = [
                'item_id' => $product->item_id,
                'item_code' => $product->item_code ?? 'ITM-' . str_pad($product->item_id, 4, '0', STR_PAD_LEFT),
                'item_name' => $product->item_name,
                'partners' => array_slice($partners, 0, 5),
            ];
        }

        // Stats
        $stats = [
            'total_sales' => Sale::count(),
            'total_products' => Product::count(),
            'rules_count' => AprioriAnalysis::count(),
            'top_lift' => AprioriAnalysis::max('lift') ?? 0,
            'avg_confidence' => AprioriAnalysis::avg('confidence') ?? 0,
        ];

        // All raw rules for full network modal
        $allRules = AprioriAnalysis::with(['antecedentProduct', 'consequentProduct'])->get();

        return view('analysis.index', compact(
            'associationData',
            'allRules',
            'stats',
            'minSupport',
            'minConfidence'
        ));
    }
}
