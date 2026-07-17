<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesmenItemController extends Controller
{
    /**
     * Display a read-only paginated list of items for staff.
     * Staff can search by item name or category. No CRUD allowed.
     */
    public function index(Request $request)
    {
        if (!Auth::guard('salesmen')->check()) {
            abort(403, 'Access restricted to salesmen only.');
        }

        $query = Product::query()->with('categoryRelation')->select('item_id', 'item_code', 'item_name', 'volume', 'category', 'category_id', 'price', 'stock_qty', 'description');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products   = $query->orderBy('item_name', 'asc')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('salesmen.items', compact('products', 'categories'));
    }
}
