<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Item::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        if ($request->filled('q')) {
            $kata = $request->query('q');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$kata}%")->orWhere('sku', 'like', "%{$kata}%"));
        }

        if ($request->boolean('low_stock')) {
            $query->where('stock', '<', 10);
        }

        $items = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return response()->json([
            'message' => 'Daftar barang berhasil diambil.',
            'data' => ItemResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    public function show(Item $item): JsonResponse
    {
        $item->load('category');

        return response()->json([
            'message' => 'Detail barang berhasil diambil.',
            'data' => new ItemResource($item),
        ]);
    }

    public function store(ItemRequest $request): JsonResponse
    {
        $item = Item::create($request->validated());

        return response()->json([
            'message' => 'Barang berhasil ditambahkan.',
            'data' => new ItemResource($item->load('category')),
        ], 201);
    }

    public function update(ItemRequest $request, Item $item): JsonResponse
    {
        $item->update($request->validated());

        return response()->json([
            'message' => 'Barang berhasil diperbarui.',
            'data' => new ItemResource($item->fresh()->load('category')),
        ]);
    }

    public function destroy(Item $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus.',
        ]);
    }
}
