<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $categories = Category::withCount('products')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        return view('categories.create');
    }

    public function store(Request $request)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|unique:category,name'
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|unique:category,name,' . $category->id
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated products.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
