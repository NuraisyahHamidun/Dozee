<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        if (Auth::guard('manager')->check()) {
            $query = Promotion::query();
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('promo_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            $promotions = $query->with(['manager', 'salesmen', 'analysis.antecedentProduct', 'analysis.consequentProduct', 'associationRules'])->latest()->paginate(10)->withQueryString();
        } else {
            // Salesmen sees approved ones or their own pending/rejected ones
            $salesmen = Auth::guard('salesmen')->user();
            $query = Promotion::where(function($q) use ($salesmen) {
                $q->where('status', 'Active')
                  ->orWhere('salesmen_id', $salesmen->salesmen_id);
            });
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('promo_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            $promotions = $query->with(['manager', 'salesmen', 'analysis.antecedentProduct', 'analysis.consequentProduct', 'associationRules'])->latest()->paginate(10)->withQueryString();
        }

        $stats = [
            'active_count' => Promotion::where('status', 'Active')->count(),
            'pending_count' => Promotion::where('status', 'Pending')->count(),
            'total_count' => Promotion::count(),
            'avg_confidence' => \App\Models\AprioriAnalysis::avg('confidence') ?? 0,
        ];
        
        return view('promotions.index', compact('promotions', 'stats'));
    }

    public function create(Request $request)
    {
        $rule_id = $request->get('rule_id');
        $rule = null;
        $antecedentProduct = null;
        $consequentProduct = null;
        
        $default_promo_name = '';
        $default_description = '';
        if ($rule_id) {
            $rule = \App\Models\AprioriAnalysis::find($rule_id);
            if ($rule) {
                $antecedentProduct = \App\Models\Product::find($rule->antecedent);
                $consequentProduct = \App\Models\Product::find($rule->consequent);
                if ($antecedentProduct && $consequentProduct) {
                    $default_promo_name = 'Combo: ' . $antecedentProduct->item_name . ' & ' . $consequentProduct->item_name;
                    $default_description = 'Combo promotion featuring ' . $antecedentProduct->item_name . ' and ' . $consequentProduct->item_name . '. Recommended based on association rules (Support: ' . round($rule->support * 100, 2) . '%, Confidence: ' . round($rule->confidence * 100, 1) . '%, Lift: ' . round($rule->lift, 2) . ').';
                }
            }
        }

        // Get top 10 most frequently bought products
        $topProducts = \App\Models\SaleItem::select('item_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('item_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->limit(10)
            ->get()
            ->filter(fn($item) => $item->product !== null);

        $allRules = \App\Models\AprioriAnalysis::all();
        $products = \App\Models\Product::pluck('item_name', 'item_id');

        return view('promotions.create', compact('rule_id', 'rule', 'topProducts', 'antecedentProduct', 'consequentProduct', 'allRules', 'products', 'default_promo_name', 'default_description'));
    }

    public function store(Request $request)
    {
        file_put_contents(storage_path('logs/store_debug.txt'), json_encode($request->all()) . "\n", FILE_APPEND);
        $request->validate([
            'promo_name' => 'required|string|max:255|unique:promotion,promo_name',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rule_ids' => 'nullable|array',
            'rule_ids.*' => 'exists:association_rules,rule_id',
            'rule_id' => 'nullable|exists:association_rules,rule_id',
            'final_discount' => 'nullable|integer|min:5|max:15',
            'status' => 'nullable|in:Active,Pending,Expired,Rejected',
            'discount_type' => 'required|string|in:Percentage',
            'discount_value' => 'required|numeric|min:0',
            'discount_apply_to' => 'required|string|in:all_selected_bundles',
        ]);

        $manager = Auth::guard('manager')->user();
        $salesmen = Auth::guard('salesmen')->user();

        DB::transaction(function () use ($request, $manager, $salesmen) {
            $primaryRuleId = $request->rule_id ?? ($request->rule_ids[0] ?? null);

            $promotion = Promotion::create([
                'promo_name' => $request->promo_name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'rule_id' => $primaryRuleId,
                'manager_id' => $manager ? $manager->manager_id : null,
                'salesmen_id' => $salesmen ? $salesmen->salesmen_id : null,
                'status' => $request->status ?? ($manager ? 'Active' : 'Pending'),
                'final_discount' => $request->final_discount ?? 10,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'discount_apply_to' => $request->discount_apply_to,
            ]);

            if ($request->has('rule_ids')) {
                $promotion->associationRules()->sync($request->rule_ids);
            }
        });

        $msg = $manager ? 'Promotion campaign launched successfully.' : 'Promotion proposal submitted for approval.';

        return redirect()->route('promotions.index')->with('success', $msg);
    }

    public function ajaxStore(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'promo_name' => 'required|string|max:255|unique:promotion,promo_name',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rule_id' => 'required|exists:association_rules,rule_id',
            'discount_value' => 'required|integer|min:5|max:15',
            'status' => 'required|in:Active,Pending',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $manager = Auth::guard('manager')->user();
        $salesmen = Auth::guard('salesmen')->user();

        try {
            DB::transaction(function () use ($request, $manager, $salesmen) {
                Promotion::create([
                    'promo_name' => $request->promo_name,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'rule_id' => $request->rule_id,
                    'manager_id' => $manager ? $manager->manager_id : null,
                    'salesmen_id' => $salesmen ? $salesmen->salesmen_id : null,
                    'status' => $request->status,
                    'final_discount' => $request->discount_value,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Promotion campaign created successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function edit(Promotion $promotion)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        $promotion->load('analysis');
        return view('promotions.edit', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $request->validate([
            'promo_name' => 'required|string|max:255|unique:promotion,promo_name,' . $promotion->promo_id . ',promo_id',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:Active,Pending,Expired,Rejected',
        ]);

        $promotion->update($request->all());

        return redirect()->route('promotions.index')->with('success', 'Promotion updated successfully.');
    }

    public function approve(Promotion $promotion)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        $promotion->update(['status' => 'Active']);
        return redirect()->route('promotions.index')->with('success', 'Promotion approved and activated.');
    }

    public function reject(Promotion $promotion)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        $promotion->update(['status' => 'Rejected']);
        return redirect()->route('promotions.index')->with('success', 'Promotion proposal rejected.');
    }

    public function apiIndex(Request $request)
    {
        $promotions = Promotion::with(['manager', 'salesmen', 'analysis.antecedentProduct', 'analysis.consequentProduct', 'associationRules'])->latest()->get();
        
        // Filter out promotions that do not have any associated products (empty bundles)
        $promotions = $promotions->filter(function($promo) {
            return ($promo->rule_id && $promo->analysis) || $promo->associationRules->isNotEmpty();
        })->values();

        return response()->json($promotions);
    }

    public function destroy(Promotion $promotion)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Promotion deleted.');
    }
}
