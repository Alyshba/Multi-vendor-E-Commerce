<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // ─── GET /api/categories ──────────────────────────────────────────────────

    public function index()
    {
        $categories = Category::withCount('products')->get();

        return response()->json([
            'status'     => 'success',
            'categories' => $categories,
        ]);
    }

    // ─── GET /api/categories/{id} ─────────────────────────────────────────────

    public function show($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status'   => 'success',
            'category' => $category,
        ]);
    }

    // ─── POST /api/categories ─────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $category = Category::create(['name' => $request->name]);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Category created successfully',
            'category' => $category,
        ], 201);
    }
}
