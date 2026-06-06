<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected function getVendorId()
    {
        $user = auth()->user();
        if (!$user) {
            return 0;
        }
        $vendor = Vendor::where('user_id', $user->id)->first() ?? Vendor::where('email', $user->email)->first();
        return $vendor ? $vendor->id : 0;
    }

    public function index(Request $request)
    {
        $vendorId = $this->getVendorId();
        $query = Product::with('category')->where('vendor_id', $vendorId);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('approval_status', strtolower($request->status));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        $stats = [
            'total'    => Product::where('vendor_id', $vendorId)->count(),
            'approved' => Product::where('vendor_id', $vendorId)->where('approval_status', 'approved')->count(),
            'pending'  => Product::where('vendor_id', $vendorId)->where('approval_status', 'pending')->count(),
            'orders'   => OrderItem::whereHas('product', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),
        ];

        return view('vendor-panel.products.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('vendor-panel.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Generate SKU dynamically
        $sku = 'VN-' . strtoupper(uniqid());

        Product::create([
            'name'            => $request->name,
            'sku'             => $sku,
            'category'        => Category::find($request->category_id)->name ?? 'General',
            'slug'            => Str::slug($request->name . '-' . $sku),
            'description'     => $request->description,
            'price'           => $request->price,
            'stock'           => $request->stock,
            'stock_quantity'  => $request->stock,
            'category_id'     => $request->category_id,
            'vendor_id'       => $this->getVendorId(),
            'approval_status' => 'pending',
            'is_active'       => false,
            'image'           => $imagePath,
        ]);

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        if ($product->vendor_id !== $this->getVendorId()) {
            abort(403);
        }
        return view('vendor-panel.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if ($product->vendor_id !== $this->getVendorId()) {
            abort(403);
        }
        $categories = Category::all();
        return view('vendor-panel.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->vendor_id !== $this->getVendorId()) {
            abort(403);
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name'           => $request->name,
            'category'       => Category::find($request->category_id)->name ?? 'General',
            'slug'           => Str::slug($request->name . '-' . $product->sku),
            'description'    => $request->description,
            'price'          => $request->price,
            'stock'          => $request->stock,
            'stock_quantity' => $request->stock,
            'category_id'    => $request->category_id,
        ]);

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function confirmDelete($id)
    {
        $product = Product::findOrFail($id);
        if ($product->vendor_id !== $this->getVendorId()) {
            abort(403);
        }
        return view('vendor-panel.products.confirm_delete', compact('product'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->vendor_id !== $this->getVendorId()) {
            abort(403);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function pdfReport()
    {
        $vendorId = $this->getVendorId();
        $products = Product::with('category')->where('vendor_id', $vendorId)->get();

        $orderItems = OrderItem::with(['order', 'product'])
            ->whereHas('product', function ($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })->latest()->get();

        $orders = $orderItems->map(function ($item) {
            return (object)[
                'id'             => $item->id,
                'customer_name'  => $item->order->customer_name ?? 'N/A',
                'customer_email' => $item->order->customer_email ?? 'N/A',
                'product_id'     => $item->product_id,
                'vendor_id'      => $item->product->vendor_id ?? 0,
                'quantity'       => $item->quantity,
                'total_price'    => $item->line_total,
                'status'         => ucfirst($item->order->status ?? 'pending'),
                'created_at'     => $item->created_at,
                'updated_at'     => $item->updated_at,
                'product'        => $item->product,
            ];
        });

        $stats = [
            'total_products' => $products->count(),
            'approved'       => $products->where('approval_status', 'approved')->count(),
            'pending'        => $products->where('approval_status', 'pending')->count(),
            'total_orders'   => $orders->count(),
            'total_revenue'  => $orders->sum('total_price'),
        ];

        $pdf = Pdf::loadView('vendor-panel.products.pdf_report', compact('products', 'orders', 'stats'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('vendor-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
