<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'vendor@example.com'],
            ['name' => 'Vendor User', 'password' => Hash::make('password'), 'role' => 'vendor']
        );

        User::updateOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Customer User', 'password' => Hash::make('password'), 'role' => 'customer']
        );

        $vendors = collect([
            ['name' => 'Tech Bazaar', 'owner_name' => 'Ali Khan', 'email' => 'tech@example.com', 'phone' => '03001234567', 'address' => 'Main Market, Lahore', 'status' => 'active'],
            ['name' => 'Style Hub', 'owner_name' => 'Sara Ahmed', 'email' => 'style@example.com', 'phone' => '03007654321', 'address' => 'Blue Area, Islamabad', 'status' => 'active'],
            ['name' => 'HomeMart', 'owner_name' => 'Usman Raza', 'email' => 'home@example.com', 'phone' => '03112223334', 'address' => 'Clifton, Karachi', 'status' => 'pending'],
        ])->map(fn ($data) => Vendor::updateOrCreate(['email' => $data['email']], $data));

        $products = collect([
            ['vendor_id' => $vendors[0]->id, 'name' => 'Wireless Mouse', 'sku' => 'TB-MOUSE-01', 'category' => 'Electronics', 'price' => 1850, 'stock' => 44, 'approval_status' => 'approved', 'description' => 'Ergonomic wireless mouse with USB receiver.'],
            ['vendor_id' => $vendors[0]->id, 'name' => 'Mechanical Keyboard', 'sku' => 'TB-KEY-02', 'category' => 'Electronics', 'price' => 7200, 'stock' => 12, 'approval_status' => 'pending', 'description' => 'RGB mechanical keyboard.'],
            ['vendor_id' => $vendors[1]->id, 'name' => 'Denim Jacket', 'sku' => 'SH-JKT-01', 'category' => 'Fashion', 'price' => 5200, 'stock' => 20, 'approval_status' => 'approved', 'description' => 'Unisex denim jacket.'],
            ['vendor_id' => $vendors[2]->id, 'name' => 'Ceramic Dinner Set', 'sku' => 'HM-DIN-01', 'category' => 'Home', 'price' => 8900, 'stock' => 8, 'approval_status' => 'rejected', 'description' => 'Twenty-four piece ceramic dinner set.'],
        ])->map(fn ($data) => Product::updateOrCreate(['sku' => $data['sku']], $data));

        $order = Order::updateOrCreate(
            ['order_number' => 'ORD-1001'],
            ['customer_name' => 'Hamza Malik', 'customer_email' => 'hamza@example.com', 'status' => 'paid', 'total_amount' => 7050]
        );

        OrderItem::updateOrCreate(
            ['order_id' => $order->id, 'product_id' => $products[0]->id],
            ['quantity' => 1, 'unit_price' => 1850, 'line_total' => 1850]
        );
        OrderItem::updateOrCreate(
            ['order_id' => $order->id, 'product_id' => $products[2]->id],
            ['quantity' => 1, 'unit_price' => 5200, 'line_total' => 5200]
        );
    }
}
