<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorApiController extends Controller
{
    public function index()
    {
        return Vendor::latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $vendor = Vendor::create($this->validated($request));

        return response()->json($vendor, 201);
    }

    public function show(Vendor $vendor)
    {
        return $vendor->load('products');
    }

    public function update(Request $request, Vendor $vendor)
    {
        $vendor->update($this->validated($request, $vendor->id));

        return response()->json($vendor);
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return response()->json(['message' => 'Vendor deleted']);
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'owner_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:vendors,email,'.($ignoreId ?? 'NULL')],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'status' => ['required', 'in:pending,active,suspended'],
        ]);
    }
}
