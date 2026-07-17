<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $salesmen = Auth::guard('salesmen')->user();
        $query = Sale::query()->with(['salesmen', 'saleItems.product']);

        if ($salesmen) {
            $query->where('salesmen_id', $salesmen->salesmen_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('transaction_id', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('salesmen', function($sq) use ($searchTerm) {
                      $sq->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $sales = $query->orderBy('transaction_id', 'desc')->paginate(15)->withQueryString();
        
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::orderBy('item_id', 'asc')->get();
        $promotions = \App\Models\Promotion::with(['analysis.antecedentProduct', 'analysis.consequentProduct', 'associationRules'])
            ->where('status', 'Active')
            ->get()
            ->filter(function($promo) {
                return ($promo->rule_id && $promo->analysis) || $promo->associationRules->isNotEmpty();
            })
            ->values();
        $existingEvents = Sale::distinct()->pluck('event_name')->filter()->values();
        return view('sales.create', compact('products', 'promotions', 'existingEvents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_date' => 'nullable|date',
            'event_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:item,item_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.promo_id' => 'nullable|exists:promotion,promo_id',
        ]);

        if ($request->filled('sale_date')) {
            $saleDate = \Carbon\Carbon::parse($request->sale_date);
            if ($saleDate->isBefore(\Carbon\Carbon::today())) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['sale_date' => 'Invalid date. You cannot select a past transaction date.']);
            }
            $timeValue = $saleDate->hour * 60 + $saleDate->minute;
            if ($timeValue < (8 * 60) || $timeValue > (20 * 60)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['sale_date' => 'Transaction time must be between 8:00 AM and 8:00 PM only.']);
            }
        }

        // Check for duplicate (product_id + promo_id) pairs
        $pairs = [];
        foreach ($request->items as $item) {
            $key = ($item['product_id'] ?? '') . '|' . ($item['promo_id'] ?? 'null');
            if (in_array($key, $pairs)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['items' => 'The same product cannot appear more than once under the same strategy link.']);
            }
            $pairs[] = $key;
        }

        $salesmen = Auth::guard('salesmen')->user();

        if (!$salesmen) {
            return redirect()->back()->withErrors(['error' => 'Only salesmen can record sales.']);
        }

        try {
            DB::beginTransaction();

            $hasSingle = false;
            $hasBundle = false;
            $bundleGroupMap = [];

            foreach ($request->items as $item) {
                if (!empty($item['promo_id'])) {
                    $pid = $item['promo_id'];
                    if (!isset($bundleGroupMap[$pid])) {
                        $bundleGroupMap[$pid] = 'BG-' . $pid . '-' . strtoupper(bin2hex(random_bytes(3)));
                    }
                    $hasBundle = true;
                } else {
                    $hasSingle = true;
                }
            }

            $type = 'single';
            if ($hasSingle && $hasBundle) {
                $type = 'mixed';
            } elseif ($hasBundle) {
                $type = 'bundle';
            }

            $sale = Sale::create([
                'salesmen_id' => $salesmen->salesmen_id,
                'event_name' => $request->event_name,
                'total_amount' => 0,
                'type' => $type,
                'sale_date' => $request->sale_date ?? now(),
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock_qty < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->item_name}. Available: {$product->stock_qty}");
                }

                $discountPercent = 0;
                if (!empty($item['promo_id'])) {
                    $promo = Promotion::find($item['promo_id']);
                    if ($promo) {
                        $discountPercent = $promo->final_discount ?? 10;
                    }
                }
                $subtotal = $product->price * $item['quantity'] * (1 - ($discountPercent / 100));
                
                $sale->saleItems()->create([
                    'item_id' => $product->item_id,
                    'quantity' => $item['quantity'],
                    'promo_id' => $item['promo_id'] ?? null,
                    'bundle_group_id' => !empty($item['promo_id']) ? $bundleGroupMap[$item['promo_id']] : null,
                ]);

                $product->decrement('stock_qty', $item['quantity']);

                $totalAmount += $subtotal;
            }

            $sale->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error recording sale: ' . $e->getMessage());
        }
    }

    public function show(Sale $sale)
    {
        $salesmen = Auth::guard('salesmen')->user();
        if ($salesmen && $sale->salesmen_id !== $salesmen->salesmen_id) {
            abort(403, 'Unauthorized action.');
        }

        $sale->load(['saleItems.product', 'salesmen']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $salesmen = Auth::guard('salesmen')->user();
        if ($salesmen) {
            if ($sale->salesmen_id !== $salesmen->salesmen_id) {
                abort(403, 'Unauthorized action.');
            }
            if ($sale->status !== 'Approved') {
                return redirect()->route('sales.index')->with('error', 'You cannot edit this sale until it is approved by a Manager.');
            }
        }

        $sale->load('saleItems.product', 'saleItems.promotion');
        $products = Product::all();
        $promotions = Promotion::with(['analysis.antecedentProduct', 'analysis.consequentProduct', 'associationRules'])
            ->where('status', 'Active')
            ->get()
            ->filter(function($promo) {
                return ($promo->rule_id && $promo->analysis) || $promo->associationRules->isNotEmpty();
            })
            ->values();
        $existingEvents = Sale::distinct()->pluck('event_name')->filter()->values();
        
        return view('sales.edit', compact('sale', 'products', 'promotions', 'existingEvents'));
    }

    public function update(Request $request, Sale $sale)
    {
        $salesmen = Auth::guard('salesmen')->user();
        if ($salesmen) {
            if ($sale->salesmen_id !== $salesmen->salesmen_id) {
                abort(403, 'Unauthorized action.');
            }
            if ($sale->status !== 'Approved') {
                return redirect()->route('sales.index')->with('error', 'You cannot edit this sale until it is approved by a Manager.');
            }
        }

        $request->validate([
            'sale_date' => 'nullable|date',
            'event_name' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.detail_id' => 'nullable|exists:transaction_detail,detail_id',
            'items.*.product_id' => 'required|exists:item,item_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.promo_id' => 'nullable|exists:promotion,promo_id',
        ]);

        if ($request->filled('sale_date')) {
            $saleDate = \Carbon\Carbon::parse($request->sale_date);
            if ($saleDate->isBefore(\Carbon\Carbon::today())) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['sale_date' => 'Invalid date. You cannot select a past transaction date.']);
            }
            $timeValue = $saleDate->hour * 60 + $saleDate->minute;
            if ($timeValue < (8 * 60) || $timeValue > (20 * 60)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['sale_date' => 'Transaction time must be between 8:00 AM and 8:00 PM only.']);
            }
        }

        // Check for duplicate (product_id + promo_id) pairs
        $pairs = [];
        foreach ($request->items as $item) {
            $key = ($item['product_id'] ?? '') . '|' . ($item['promo_id'] ?? 'null');
            if (in_array($key, $pairs)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['items' => 'The same product cannot appear more than once under the same strategy link.']);
            }
            $pairs[] = $key;
        }

        try {
            DB::beginTransaction();

            $hasSingle = false;
            $hasBundle = false;
            $bundleGroupMap = [];

            foreach ($request->items as $item) {
                if (!empty($item['promo_id'])) {
                    $pid = $item['promo_id'];
                    if (!isset($bundleGroupMap[$pid])) {
                        $bundleGroupMap[$pid] = 'BG-' . $pid . '-' . strtoupper(bin2hex(random_bytes(3)));
                    }
                    $hasBundle = true;
                } else {
                    $hasSingle = true;
                }
            }

            $type = 'single';
            if ($hasSingle && $hasBundle) {
                $type = 'mixed';
            } elseif ($hasBundle) {
                $type = 'bundle';
            }

            $sale->update([
                'event_name' => $request->event_name,
                'sale_date' => $request->sale_date ?? $sale->sale_date,
                'type' => $type,
                'date_modifier' => now(),
            ]);

            $totalAmount = 0;
            $keptItemIds = [];

            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                
                $discountPercent = 0;
                if (!empty($itemData['promo_id'])) {
                    $promo = Promotion::find($itemData['promo_id']);
                    if ($promo) {
                        $discountPercent = $promo->final_discount ?? 10;
                    }
                }
                $subtotal = $product->price * $itemData['quantity'] * (1 - ($discountPercent / 100));
                
                $itemBundleGroupId = !empty($itemData['promo_id']) ? $bundleGroupMap[$itemData['promo_id']] : null;

                if (isset($itemData['detail_id']) && $itemData['detail_id']) {
                    $saleItem = $sale->saleItems()->where('detail_id', $itemData['detail_id'])->first();
                    if ($saleItem) {
                        if ($saleItem->item_id == $product->item_id) {
                            $difference = $itemData['quantity'] - $saleItem->quantity;
                            if ($difference > 0 && $product->stock_qty < $difference) {
                                throw new \Exception("Insufficient stock for {$product->item_name}.");
                            }
                            if ($difference != 0) {
                                $product->decrement('stock_qty', $difference);
                            }
                        } else {
                            $oldProduct = Product::find($saleItem->item_id);
                            if ($oldProduct) {
                                $oldProduct->increment('stock_qty', $saleItem->quantity);
                            }
                            if ($product->stock_qty < $itemData['quantity']) {
                                throw new \Exception("Insufficient stock for {$product->item_name}.");
                            }
                            $product->decrement('stock_qty', $itemData['quantity']);
                        }

                        $saleItem->update([
                            'item_id' => $product->item_id,
                            'quantity' => $itemData['quantity'],
                            'promo_id' => $itemData['promo_id'] ?? null,
                            'bundle_group_id' => $itemBundleGroupId,
                        ]);
                        $keptItemIds[] = $saleItem->detail_id;
                    }
                } else {
                    if ($product->stock_qty < $itemData['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->item_name}.");
                    }
                    $product->decrement('stock_qty', $itemData['quantity']);

                    $newSaleItem = $sale->saleItems()->create([
                        'item_id' => $product->item_id,
                        'quantity' => $itemData['quantity'],
                        'promo_id' => $itemData['promo_id'] ?? null,
                        'bundle_group_id' => $itemBundleGroupId,
                    ]);
                    $keptItemIds[] = $newSaleItem->detail_id;
                }

                $totalAmount += $subtotal;
            }

            // Remove items that were deleted from the form and return stock
            $deletedItems = $sale->saleItems()->whereNotIn('detail_id', $keptItemIds)->get();
            foreach ($deletedItems as $delItem) {
                $delProduct = Product::find($delItem->item_id);
                if ($delProduct) {
                    $delProduct->increment('stock_qty', $delItem->quantity);
                }
                $delItem->delete();
            }

            // Recalculate total
            $sale->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating sale: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Sale $sale)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        
        try {
            DB::beginTransaction();

            if ($sale->status !== 'Rejected') {
                foreach ($sale->saleItems as $item) {
                    $product = Product::find($item->item_id);
                    if ($product) {
                        $product->increment('stock_qty', $item->quantity);
                    }
                }
            }
            
            $sale->delete();

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale record deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error deleting sale: ' . $e->getMessage());
        }
    }

    public function approve(Sale $sale)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403, 'Unauthorized action.');
        }

        $sale->update([
            'status' => 'Approved',
            'date_verify' => now(),
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale transaction approved successfully.');
    }

    public function reject(Sale $sale)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            // Set to rejected and update verification timestamp
            $sale->update([
                'status' => 'Rejected',
                'date_verify' => now(),
            ]);

            // Return items back to stock
            foreach ($sale->saleItems as $item) {
                $product = Product::find($item->item_id);
                if ($product) {
                    $product->increment('stock_qty', $item->quantity);
                }
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Sale transaction rejected and items returned to stock.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error rejecting sale: ' . $e->getMessage());
        }
    }

    /**
     * Returns all products belonging to a promotion bundle as JSON.
     * Called via AJAX from the sale create/edit form when a Strategy Link is selected.
     */
    public function getBundleItems(Promotion $promotion)
    {
        $products = $promotion->products;

        if ($products->isEmpty()) {
            return response()->json(['items' => [], 'message' => 'This promotion has no associated products.']);
        }

        $items = $products->map(function ($product) {
            return [
                'item_id'   => $product->item_id,
                'item_name' => $product->item_name,
                'volume'    => $product->volume,
                'price'     => $product->price,
                'stock_qty' => $product->stock_qty,
            ];
        });

        return response()->json(['items' => $items]);
    }
}
