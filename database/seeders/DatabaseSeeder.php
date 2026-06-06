<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate tables to ensure a clean slate and remove all other users
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        User::truncate();
        Vendor::truncate();
        Product::truncate();
        Category::truncate();
        Order::truncate();
        OrderItem::truncate();
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 1. Create Default Users (Sharing the same email Taha@gmail.com and password 123Taha)
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'Tahaadmin@gmail.com',
            'password' => Hash::make('123Taha'),
            'role' => 'admin'
        ]);

        $vendorUser = User::create([
            'name' => 'Vendor User',
            'email' => 'TahaVendor@gmail.com',
            'password' => Hash::make('123Taha'),
            'role' => 'vendor'
        ]);

        $customerUser = User::create([
            'name' => 'Customer User',
            'email' => 'TahaCustomer@gmail.com',
            'password' => Hash::make('123Taha'),
            'role' => 'customer',
            'phone' => '03001234567',
            'address' => 'Customer Address'
        ]);

        // 2. Create Default Categories
        $categories = collect([
            ['name' => 'Bakery', 'slug' => 'bakery', 'description' => 'Freshly baked goods and pastries'],
            ['name' => 'Dairy', 'slug' => 'dairy', 'description' => 'Milk, cheese, butter and eggs'],
            ['name' => 'Fast Food', 'slug' => 'fast-food', 'description' => 'Burgers, pizzas and quick bites'],
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Smoothies, shakes, juices and sodas'],
            ['name' => 'Snacks', 'slug' => 'snacks', 'description' => 'Chips, biscuits and cookies'],
        ])->map(fn ($data) => Category::create($data));

        // 3. Create Default Vendor
        $vendor = Vendor::create([
            'name' => 'Gourmet Bistro',
            'owner_name' => 'Ali Khan',
            'email' => 'TahaVendor@gmail.com',
            'user_id' => $vendorUser->id,
            'phone' => '03001234567',
            'address' => 'Main Food Street, Lahore',
            'status' => 'active'
        ]);

        // 4. Create Default Products (all associated with the single vendor)
        $products = collect([
            [
                'vendor_id' => $vendor->id, 
                'name' => 'Double Beef Burger', 
                'slug' => 'double-beef-burger',
                'sku' => 'GB-BURGER-01', 
                'category' => 'Fast Food', 
                'category_id' => $categories[2]->id,
                'price' => 650, 
                'stock' => 50, 
                'stock_quantity' => 50,
                'approval_status' => 'approved', 
                'is_active' => true,
                'description' => 'Juicy double beef patty burger with cheese and special sauce.'
            ],
            [
                'vendor_id' => $vendor->id, 
                'name' => 'Pepperoni Pizza', 
                'slug' => 'pepperoni-pizza',
                'sku' => 'GB-PIZZA-02', 
                'category' => 'Fast Food', 
                'category_id' => $categories[2]->id,
                'price' => 1250, 
                'stock' => 15, 
                'stock_quantity' => 15,
                'approval_status' => 'approved', 
                'is_active' => true,
                'description' => 'Classic pepperoni pizza with mozzarella cheese and tomato sauce.'
            ],
            [
                'vendor_id' => $vendor->id, 
                'name' => 'Sourdough Bread', 
                'slug' => 'sourdough-bread',
                'sku' => 'DB-BREAD-01', 
                'category' => 'Bakery', 
                'category_id' => $categories[0]->id,
                'price' => 350, 
                'stock' => 30, 
                'stock_quantity' => 30,
                'approval_status' => 'approved', 
                'is_active' => true,
                'description' => 'Freshly baked artisanal sourdough bread.'
            ],
            [
                'vendor_id' => $vendor->id, 
                'name' => 'Cheddar Cheese Block', 
                'slug' => 'cheddar-cheese-block',
                'sku' => 'FD-CHEESE-01', 
                'category' => 'Dairy', 
                'category_id' => $categories[1]->id,
                'price' => 950, 
                'stock' => 20, 
                'stock_quantity' => 20,
                'approval_status' => 'approved', 
                'is_active' => true,
                'description' => 'Premium aged white cheddar cheese block.'
            ],
        ])->map(fn ($data) => Product::create($data));

        // 5. Create Default Order
        $order = Order::create([
            'order_number' => 'ORD-1001',
            'customer_name' => 'Hamza Malik', 
            'customer_email' => 'hamza@example.com', 
            'user_id' => $customerUser->id,
            'subtotal' => 1000,
            'shipping' => 50,
            'total' => 1050,
            'total_amount' => 1050,
            'status' => 'paid',
            'payment_status' => 'paid',
            'shipping_address' => 'Test Customer Address',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $products[0]->id,
            'product_name' => 'Double Beef Burger',
            'price' => 650,
            'total' => 650,
            'quantity' => 1, 
            'unit_price' => 650, 
            'line_total' => 650
        ]);
        
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $products[2]->id,
            'product_name' => 'Sourdough Bread',
            'price' => 350,
            'total' => 350,
            'quantity' => 1, 
            'unit_price' => 350, 
            'line_total' => 350
        ]);
    }
}
