<?php

namespace App\Http\Controllers;

use App\Http\Requests\CakeRequest;
use App\Http\Resources\CakeResource;
use App\Models\Cake;
use App\Models\Category;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CakeController extends Controller
{
    private function getCategory(int $idCategory): Category
    {
        $category = Category::where('id', $idCategory)->first();
        if (!$category) {
            throw new HttpResponseException(response([
                "status" => false,
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ], 404));
        }
        return $category;
    }

    public function create(CakeRequest $request, int $idCategory): JsonResponse
    {
        $data = $request->validated();
        $category = $this->getCategory($idCategory);

        $cake = new Cake($data);
        $cake->category_id = $category->id;

        $file = $request->file('image');
        $path = $file->hashName();
        Storage::disk('cake')->put($path, file_get_contents($file));

        $cake->image = $path;
        $cake->save();

        return response()->json([
            "status" => true,
            "data" => new CakeResource($cake)
        ], 201);
    }
}
