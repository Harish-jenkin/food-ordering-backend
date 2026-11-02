<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFoodItemRequest;
use App\Http\Requests\UpdateFoodItemRequest;
use App\Http\Resources\FoodItemResource;
use App\Models\FoodItem;
use App\Services\ImageUploadService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FoodAdminController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function index(Request $request)
    {
        $query = FoodItem::query()->with('category')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        if ($this->hasFilter($request, 'available')) {
            $query->where('is_available', $request->boolean('available'));
        }
        if ($this->hasFilter($request, 'is_veg')) {
            $query->where('is_veg', $request->boolean('is_veg'));
        }

        $foods = $query->paginate($request->integer('per_page', 12));

        return ApiResponse::success([
            'items' => FoodItemResource::collection($foods)->resolve(),
            'meta' => [
                'current_page' => $foods->currentPage(),
                'last_page' => $foods->lastPage(),
                'total' => $foods->total(),
            ],
        ]);
    }

    public function store(StoreFoodItemRequest $request)
    {
        $data = $this->payload($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'foods');
        }

        $food = FoodItem::query()->create($data);

        return ApiResponse::success(new FoodItemResource($food->load('category')), 'Food item created.', 201);
    }

    public function show(FoodItem $food)
    {
        return ApiResponse::success(new FoodItemResource($food->load('category')));
    }

    public function update(UpdateFoodItemRequest $request, FoodItem $food)
    {
        $data = $this->payload($request, false);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->replace($food->image, $request->file('image'), 'foods');
        }

        $food->update($data);

        return ApiResponse::success(new FoodItemResource($food->fresh('category')), 'Food item updated.');
    }

    public function destroy(FoodItem $food)
    {
        $this->images->delete($food->image);
        $food->delete();

        return ApiResponse::success(null, 'Food item deleted.');
    }

    private function payload(Request $request, bool $creating = true): array
    {
        $data = $request->safe()->except('image');

        foreach (['is_veg', 'is_available', 'is_popular', 'is_bestseller', 'is_special'] as $flag) {
            if ($creating || $request->has($flag)) {
                $data[$flag] = $request->boolean($flag, $flag === 'is_veg' || $flag === 'is_available');
            }
        }

        return $data;
    }

    private function hasFilter(Request $request, string $key): bool
    {
        return $request->exists($key) && $request->input($key) !== '' && $request->input($key) !== null;
    }
}
