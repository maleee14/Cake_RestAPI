<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/categories', [
            'name' => 'Cheese Cake',
            'description' => 'Mantap'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Cheese Cake',
                    'description' => 'Mantap'
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/categories', [
            'name' => '',
            'description' => 'Mantap'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testCreateInvalid()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/categories', [
            'name' => 'Cheese Cake',
            'description' => 'Mantap'
        ], [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'invalid token'
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/categories', [
            'name' => 'Cheese Cake',
            'description' => 'Mantap'
        ], [])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testDetailSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->get('/api/categories/' . ($category->id + 1), [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => 2,
                    'name' => 'Pudding',
                    'description' => 'Enak'
                ]
            ]);
    }

    public function testDetailNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->get('/api/categories/' . ($category->id + 2), [
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->put('/api/categories/' . $category->id, [
            'name' => 'Cheese Cake',
            'description' => 'Creamy'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Cheese Cake',
                    'description' => 'Creamy'
                ]
            ]);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->put('/api/categories/' . $category->id, [
            'name' => '',
            'description' => 'Creamy'
        ], [
            'Authorization' => 'token123'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'name' => [
                        'The name field is required.'
                    ]
                ]
            ]);
    }

    public function testUpdateNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->put('/api/categories/' . ($category->id + 2), [
            'name' => 'Cheese Cake',
            'description' => 'Creamy'
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

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->delete(uri: '/api/categories/' . $category->id, headers: [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson(
                [
                    'data' => true
                ]
            );
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->delete(uri: '/api/categories/' . ($category->id + 2), headers: [
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

    public function testDeleteUnauthorized()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $category = Category::query()->limit(1)->first();
        $this->delete(uri: '/api/categories/' . ($category->id + 1), headers: [])
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'unauthorized'
                    ]
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, CategorySeeder::class]);
        $this->get('/api/categories/list', [
            'Authorization' => 'token123'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'Cheese Cake',
                        'description' => 'Mantap'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Pudding',
                        'description' => 'Enak'
                    ]
                ]
            ]);
    }
}
