<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        // withCount, bukan with. Yang dibutuhin cuma angkanya,
        // nggak perlu narik semua baris item cuma buat dihitung.
        $categories = Category::withCount('items')->orderBy('name')->get();

        return response()->json([
            'message' => 'Daftar kategori berhasil diambil.',
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load('items');

        return response()->json([
            'message' => 'Detail kategori berhasil diambil.',
            'data' => new CategoryResource($category),
        ]);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Kategori berhasil dibuat.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json([
            'message' => 'Kategori berhasil diperbarui.',
            'data' => new CategoryResource($category->fresh()),
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        // Foreign key-nya restrictOnDelete, jadi tanpa cek ini yang keluar
        // adalah error database mentah, bukan pesan yang kebaca manusia.
        if ($category->items()->exists()) {
            return response()->json([
                'message' => 'Kategori ini masih dipakai barang, pindahkan barangnya dulu.',
            ], 409);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
