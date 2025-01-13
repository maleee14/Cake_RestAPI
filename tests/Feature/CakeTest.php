<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CakeTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $image = UploadedFile::fake()->image('blueberry.jpg', 600, 400);

        $this->post('/api/categories/' . $category->id . '/cakes', [
            'name' => 'Blueberry Cheese Cake',
            'description' => 'Creamy dan Segar di mulut',
            'price' => 15000,
            'stock' => 100,
            'image' => $image
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Blueberry Cheese Cake',
                    'description' => 'Creamy dan Segar di mulut',
                    'price' => 15000,
                    'stock' => 100,
                    'image' => $image->hashName()
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();

        $this->post('/api/categories/' . $category->id . '/cakes', [
            'name' => '',
            'description' => 'Creamy dan Segar di mulut',
            'price' => 15000,
            'stock' => 100,
            'image' => 'test'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'image' => [
                        'The image field must be an image.'
                    ]
                ]
            ]);
    }

    public function testCategoryNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $image = UploadedFile::fake()->image('blueberry.jpg', 600, 400);

        $this->post('/api/categories/' . ($category->id + 2) . '/cakes', [
            'name' => 'Blueberry Cheese Cake',
            'description' => 'Creamy dan Segar di mulut',
            'price' => 15000,
            'stock' => 100,
            'image' => $image
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }
}
