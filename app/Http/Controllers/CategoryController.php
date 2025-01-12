<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategory(int $id): Category
    {
        $category = Category::where('id', $id)->first();
        if (!$category) {
            throw new HttpResponseException(response([
                "status" => false,
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ], 400));
        }
        return $category;
    }
    public function create(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        $category = new Category($data);
        $category->save();

        return response()->json([
            "status" => true,
            "data" => new CategoryResource($category)
        ], 201);
    }

    public function detail(int $id): JsonResponse
    {
        $category = $this->getCategory($id);
        return response()->json([
            "status" => true,
            "data" => new CategoryResource($category)
        ]);
    }

    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $category = $this->getCategory($id);

        $category->fill($data);
        $category->save();

        return response()->json([
            "status" => true,
            "data" => new CategoryResource($category)
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $category = $this->getCategory($id);
        $category->delete();

        return response()->json([
            "status" => true,
            "data" => true
        ]);
    }

    public function list(): JsonResponse
    {
        $category = Category::all();
        return response()->json([
            "status" => true,
            "data" => CategoryResource::collection($category)
        ]);
    }
}
