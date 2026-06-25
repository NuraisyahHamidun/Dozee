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
            $promotions = $query->with(['manager', 'salesman', 'analysis.antecedentProduct', 'analysis.consequentProduct', 'associationRules'])->latest()->get();
        } else {
            // Salesman sees approved ones or their own pending/rejected ones
            $salesman = Auth::guard('salesman')->user();
            $query = Promotion::where(function($q) use ($salesman) {
                $q->where('status', 'Active')
                  ->orWhere('salesman_id', $salesman->salesman_id);
            });
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('promo_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            $promotions = $query->with(['manager', 'salesman', 'analysis.antecedentProduct', 'analysis.consequentProduct', 'associationRules'])->latest()->get();
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
        
        if ($rule_id) {
            $rule = \App\Models\AprioriAnalysis::find($rule_id);
            if ($rule) {
                $antecedentProduct = \App\Models\Product::find($rule->antecedent);
                $consequentProduct = \App\Models\Product::find($rule->consequent);
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

        return view('promotions.create', compact('rule_id', 'rule', 'topProducts', 'antecedentProduct', 'consequentProduct', 'allRules', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'promo_name' => 'required|string|max:255|unique:promotion,promo_name',
            'description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'rule_ids' => 'nullable|array',
            'rule_ids.*' => 'exists:association_rules,rule_id',
            'rule_id' => 'nullable|exists:association_rules,rule_id',
        ]);

        $manager = Auth::guard('manager')->user();
        $salesman = Auth::guard('salesman')->user();

        DB::transaction(function () use ($request, $manager, $salesman) {
            $primaryRuleId = $request->rule_id ?? ($request->rule_ids[0] ?? null);

            $promotion = Promotion::create([
                'promo_name' => $request->promo_name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'rule_id' => $primaryRuleId,
                'manager_id' => $manager ? $manager->manager_id : null,
                'salesman_id' => $salesman ? $salesman->salesman_id : null,
                'status' => $manager ? 'Active' : 'Pending',
            ]);

            if ($request->has('rule_ids')) {
                $promotion->associationRules()->sync($request->rule_ids);
            }
        });

        $msg = $manager ? 'Promotion campaign launched successfully.' : 'Promotion proposal submitted for approval.';

        return redirect()->route('promotions.index')->with('success', $msg);
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
            'status' => 'required|in:Active,Expired',
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

    public function destroy(Promotion $promotion)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Promotion deleted.');
    }
}
