<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('vendor')
            ->when($request->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%"))
            ->when($request->approval_status, fn ($query, $status) => $query->where('approval_status', $status))
            ->when($request->vendor_id, fn ($query, $vendorId) => $query->where('vendor_id', $vendorId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'vendors' => Vendor::orderBy('name')->get(),
        ]);
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
