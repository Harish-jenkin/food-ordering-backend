<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\ImageUploadService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function __construct(private readonly ImageUploadService $images)
    {
    }

    public function index(Request $request)
    {
        $query = Category::query()->withCount('foodItems')->orderBy('sort_order');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
        }

        $categories = $query->paginate($request->integer('per_page', 15));

        return ApiResponse::success([
            'items' => CategoryResource::collection($categories)->resolve(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'total' => $categories->total(),
            ],
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->safe()->except('image');
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'categories');
        }

        $category = Category::query()->create($data);

        return ApiResponse::success(new CategoryResource($category), 'Category created.', 201);
    }

    public function show(Category $category)
    {
        $category->loadCount('foodItems');

        return ApiResponse::success(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->safe()->except('image');

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->replace($category->image, $request->file('image'), 'categories');
        }

        $category->update($data);

        return ApiResponse::success(new CategoryResource($category->fresh()), 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $this->images->delete($category->image);
        $category->delete();

        return ApiResponse::success(null, 'Category deleted.');
    }
}
