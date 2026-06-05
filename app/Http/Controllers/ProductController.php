<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('vendor');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $vendors = Vendor::orderBy('name')->get();

        return view('products.index', compact('products', 'vendors'));
    }

    public function create()
    {
        return view('products.create', [
            'vendors' => Vendor::where('status', 'active')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Product::create($this->normalizedProductData($request));

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
        $product->update($this->normalizedProductData($request, $product->id));

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function approve(Product $product)
    {
        $product->update([
            'approval_status' => 'approved',
            'is_active' => true,
        ]);

        return back()->with('success', 'Product approved.');
    }

    public function reject(Product $product)
    {
        $product->update([
            'approval_status' => 'rejected',
            'is_active' => false,
        ]);

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

    protected function normalizedProductData(Request $request, ?int $ignoreId = null): array
    {
        $data = $this->validated($request, $ignoreId);
        $data['slug'] = Str::slug($data['name'] . '-' . $data['sku']);
        $data['stock_quantity'] = $data['stock'];
        $data['is_active'] = $data['approval_status'] === 'approved';

        return $data;
    }
}
