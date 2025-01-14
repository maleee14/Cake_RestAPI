<?php

namespace Tests\Feature;

use App\Models\Cake;
use App\Models\Category;
use Database\Seeders\CakeSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
                    // 'image' => menyesuaikan nama path,
                    'category' => [
                        'id' => 1,
                        'name' => 'Cheese Cake'
                    ]
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

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class, CakeSeeder::class]);
        $cake = Cake::query()->limit(1)->first();

        $this->get('/api/categories/' . $cake->category_id . '/cakes/' . $cake->id, [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Blueberry Cheese Cake',
                    'description' => 'Creamy dan Segar di mulut',
                    'price' => 15000,
                    'stock' => 100,
                    'image' => $cake->image,
                    'category' => [
                        'id' => 1,
                        'name' => 'Cheese Cake'
                    ]
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class, CakeSeeder::class]);
        $cake = Cake::query()->limit(1)->first();

        $this->get('/api/categories/' . $cake->category_id . '/cakes/' . ($cake->id + 1), [
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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class, CakeSeeder::class]);
        $cake = Cake::query()->limit(1)->first();
        $this->delete(uri: '/api/categories/' . $cake->category_id . '/cakes/' . $cake->id, headers: [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class, CakeSeeder::class]);
        $cake = Cake::query()->limit(1)->first();

        $this->delete(uri: '/api/categories/' . $cake->category_id . '/cakes/' . ($cake->id + 1), headers: [
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
