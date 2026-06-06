<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::marketplaceVisible()->with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->sort ?? 'newest';
        switch ($sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name': $query->orderBy('name', 'asc'); break;
            default: $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount([
            'products' => fn ($q) => $q->marketplaceVisible(),
        ])->get();

        return view('customer.products.index', compact('products', 'categories', 'sort'));
    }

    public function show($slug): View
    {
        $product = Product::marketplaceVisible()->where('slug', $slug)->with('category')->firstOrFail();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->marketplaceVisible()
            ->take(4)
            ->get();

        return view('customer.products.show', compact('product', 'relatedProducts'));
    }
}
