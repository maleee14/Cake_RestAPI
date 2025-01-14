<?php

namespace Database\Seeders;

use App\Models\Cake;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class CakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::query()->limit(1)->first();
        $image = UploadedFile::fake()->image('blueberry.jpg', 600, 400);

        Cake::create([
            'category_id' => $category->id,
            'name' => 'Blueberry Cheese Cake',
            'description' => 'Creamy dan Segar di mulut',
            'price' => 15000,
            'stock' => 100,
            'image' => $image
        ]);
    }
}
