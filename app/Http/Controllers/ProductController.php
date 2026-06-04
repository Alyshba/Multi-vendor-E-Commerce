<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
{

    $query = Product::query();

    
    if ($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    
    if ($request->has('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }
    if ($request->has('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    
    $perPage = $request->get('per_page', 10);
    $products = $query->paginate($perPage);

    
    return response()->json($products);
}

    public function create()
    {
        return view('products.create', [
            'vendors' => Vendor::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load('vendor');

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'vendors' => Vendor::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product->id));

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function approve(Product $product)
    {
        $product->update(['approval_status' => 'approved']);

        return back()->with('success', 'Product approved.');
    }

    public function reject(Product $product)
    {
        $product->update(['approval_status' => 'rejected']);

        return back()->with('success', 'Product rejected.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['required', 'string', 'max:80', 'unique:products,sku,'.($ignoreId ?? 'NULL')],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'approval_status' => ['required', 'in:pending,approved,rejected'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
