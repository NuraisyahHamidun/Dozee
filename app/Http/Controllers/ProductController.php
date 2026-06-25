<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->select('item_id', 'item_code', 'item_name', 'volume', 'category', 'category_id', 'price', 'stock_qty', 'description');

        if ($request->filled('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->with('categoryRelation')->orderBy('item_id', 'asc')->paginate(10)->withQueryString();
        
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $categories = Category::all();
        $nextItemCode = Product::generateNextItemCode();

        return view('products.create', compact('categories', 'nextItemCode'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $rules = [
            'item_name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('item', 'item_name')->where(function ($query) use ($request) {
                    return $query->where('volume', $request->volume);
                }),
            ],
            'volume' => 'required|string|max:50',
            'category_id' => 'required|exists:category,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
        ];

        if ($request->has('override_item_code')) {
            $rules['item_code'] = 'required|string|unique:item,item_code|regex:/^ITM-[A-Za-z0-9_-]+$/i';
        }

        $request->validate($rules);

        $data = $request->all();
        $data['category'] = Category::find($request->category_id)->name; // Keep legacy field sync

        if (!$request->has('override_item_code')) {
            $data['item_code'] = null; // Let the model generate it on creating event
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $rules = [
            'item_name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('item', 'item_name')
                    ->ignore($product->item_id, 'item_id')
                    ->where(function ($query) use ($request) {
                        return $query->where('volume', $request->volume);
                    }),
            ],
            'volume' => 'required|string|max:50',
            'category_id' => 'required|exists:category,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
        ];

        if ($request->has('override_item_code')) {
            $rules['item_code'] = 'required|string|regex:/^ITM-[A-Za-z0-9_-]+$/i|unique:item,item_code,' . $product->item_id . ',item_id';
        }

        $request->validate($rules);

        $data = $request->all();
        $data['category'] = Category::find($request->category_id)->name; // Keep legacy field sync

        if (!$request->has('override_item_code')) {
            $data['item_code'] = $product->item_code; // Revert to current if override toggle not active
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
