<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoodItemResource;
use App\Models\FoodItem;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodItem::query()->with('category')->orderBy('sort_order')->orderBy('name');

        if ($request->boolean('available_only', true)) {
            $query->available();
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ids')) {
            $ids = collect(explode(',', (string) $request->input('ids')))
                ->map(fn ($id) => (int) trim($id))
                ->filter();
            $query->whereIn('id', $ids);
        }

        $sort = $request->string('sort');
        if ($sort === 'price_asc') {
            $query->reorder()->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $query->reorder()->orderByDesc('price');
        }

        if ($request->boolean('popular')) {
            $query->where('is_popular', true);
        }
        if ($request->boolean('bestseller')) {
            $query->where('is_bestseller', true);
        }
        if ($request->boolean('special')) {
            $query->where('is_special', true);
        }

        if ($request->boolean('paginated')) {
            $foods = $query->paginate($request->integer('per_page', 12))->withQueryString();

            return ApiResponse::success([
                'items' => FoodItemResource::collection($foods)->resolve(),
                'meta' => [
                    'current_page' => $foods->currentPage(),
                    'last_page' => $foods->lastPage(),
                    'per_page' => $foods->perPage(),
                    'total' => $foods->total(),
                ],
            ]);
        }

        return ApiResponse::success(FoodItemResource::collection($query->get())->resolve());
    }

    public function show(string $slug)
    {
        $food = FoodItem::query()->with('category')->where('slug', $slug)->firstOrFail();

        return ApiResponse::success(new FoodItemResource($food));
    }
}
