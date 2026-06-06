<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        $products = [
            ['name' => 'iPhone 14', 'price' => 250000],
            ['name' => 'Samsung Galaxy S23', 'price' => 220000],
            ['name' => 'HP Laptop i5', 'price' => 180000],
            ['name' => 'Dell Monitor 24 inch', 'price' => 45000],
            ['name' => 'Wireless Mouse', 'price' => 2500],
            ['name' => 'Gaming Keyboard', 'price' => 8000],
            ['name' => 'AirPods Pro', 'price' => 60000],
            ['name' => 'LED TV 43 inch', 'price' => 95000],
            ['name' => 'Bluetooth Speaker', 'price' => 7000],
            ['name' => 'Power Bank 20000mAh', 'price' => 5000],
            ['name' => 'Backpack Bag', 'price' => 3000],
            ['name' => 'Office Chair', 'price' => 15000],
            ['name' => 'Table Lamp', 'price' => 2000],
            ['name' => 'Shoes Nike', 'price' => 12000],
            ['name' => 'Men T Shirt', 'price' => 1500],
            ['name' => 'Women Handbag', 'price' => 8000],
            ['name' => 'Perfume Brand', 'price' => 6000],
            ['name' => 'Watch Casio', 'price' => 9000],
            ['name' => 'Smart Watch', 'price' => 25000],
            ['name' => 'Gaming Chair', 'price' => 35000],
            ['name' => 'iPad Air', 'price' => 180000],
            ['name' => 'Canon Camera', 'price' => 120000],
            ['name' => 'Tripod Stand', 'price' => 4000],
            ['name' => 'Mic Wireless', 'price' => 10000],
            ['name' => 'Router TP Link', 'price' => 6000],
            ['name' => 'SSD 512GB', 'price' => 12000],
            ['name' => 'HDD 1TB', 'price' => 10000],
            ['name' => 'RAM 8GB', 'price' => 7000],
            ['name' => 'Graphic Card GTX', 'price' => 80000],
            ['name' => 'Laptop Stand', 'price' => 3000],
            ['name' => 'USB Cable', 'price' => 500],
            ['name' => 'Extension Board', 'price' => 1500],
            ['name' => 'Electric Kettle', 'price' => 3500],
            ['name' => 'Rice Cooker', 'price' => 7000],
            ['name' => 'Microwave Oven', 'price' => 25000],
            ['name' => 'Blender Machine', 'price' => 6000],
            ['name' => 'Air Fryer', 'price' => 20000],
            ['name' => 'Water Bottle', 'price' => 800],
            ['name' => 'Notebook Pack', 'price' => 1200],
            ['name' => 'Pen Set', 'price' => 500],
            ['name' => 'Calculator', 'price' => 1500],
            ['name' => 'Study Table', 'price' => 10000],
            ['name' => 'Bookshelf', 'price' => 12000],
            ['name' => 'Curtains Set', 'price' => 4000],
            ['name' => 'Bed Sheet Set', 'price' => 3000],
            ['name' => 'Pillow Set', 'price' => 2000],
            ['name' => 'Mattress', 'price' => 25000],
            ['name' => 'Sofa Set', 'price' => 90000],
            ['name' => 'Dining Table', 'price' => 60000],
            ['name' => 'Wall Clock', 'price' => 2500],
            ['name' => 'Decoration Lights', 'price' => 1500],
        ];

        foreach ($products as $index => $item) {
            Product::create([
                'name' => $item['name'],
                'description' => 'Auto generated product for testing',
                'price' => $item['price'],
                'stock' => rand(5, 100),
                'category_id' => $categories->random()->id,
                'vendor_id' => 1,
                'status' => 'Approved',
                'image' => null,
            ]);
        }
    }
}