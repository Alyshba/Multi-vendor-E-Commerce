<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Vendor ─────────────────────────────────────────────
        $vendor = User::create([
            'name'     => 'Vendor 1',
            'email'    => 'vendor@test.com',
            'password' => Hash::make('123456'),
            'role'     => 'vendor',
        ]);

        // ─── Categories ─────────────────────────────────────────
        $categoryMap = [];

        $categoryMap['Mobiles & Tablets'] = Category::create(['name' => 'Mobiles & Tablets'])->id;
        $categoryMap['Electronics']        = Category::create(['name' => 'Electronics & Gadgets'])->id;
        $categoryMap['Men Fashion']       = Category::create(['name' => 'Fashion Men'])->id;
        $categoryMap['Women Fashion']     = Category::create(['name' => 'Fashion Women'])->id;
        $categoryMap['Footwear']          = Category::create(['name' => 'Footwear'])->id;
        $categoryMap['Home Appliances']   = Category::create(['name' => 'Home Appliances'])->id;
        $categoryMap['Books']             = Category::create(['name' => 'Books & Stationery'])->id;
        $categoryMap['Sports']            = Category::create(['name' => 'Sports & Fitness'])->id;

        // ─── Products (Proper Category Mapping) ─────────────────

        $products = [

            // Mobiles
            ['name' => 'Infinix Hot 40', 'price' => 38999, 'cat' => 'Mobiles & Tablets'],
            ['name' => 'Tecno Spark 20', 'price' => 31999, 'cat' => 'Mobiles & Tablets'],
            ['name' => 'Samsung Galaxy A15', 'price' => 45999, 'cat' => 'Mobiles & Tablets'],
            ['name' => 'Redmi Note 13', 'price' => 52999, 'cat' => 'Mobiles & Tablets'],
            ['name' => 'Realme C53', 'price' => 28999, 'cat' => 'Mobiles & Tablets'],

            // Electronics
            ['name' => 'Ronin Earbuds Pro', 'price' => 4999, 'cat' => 'Electronics'],
            ['name' => 'Audionic Bluetooth Speaker', 'price' => 7999, 'cat' => 'Electronics'],
            ['name' => 'Anker Power Bank 20000mAh', 'price' => 8999, 'cat' => 'Electronics'],
            ['name' => 'HP Wireless Mouse', 'price' => 2499, 'cat' => 'Electronics'],
            ['name' => 'Logitech Keyboard K120', 'price' => 3499, 'cat' => 'Electronics'],

            // Men Fashion
            ['name' => 'Khaadi Men Kurta', 'price' => 3999, 'cat' => 'Men Fashion'],
            ['name' => 'Bonanza Cotton Shirt', 'price' => 2499, 'cat' => 'Men Fashion'],
            ['name' => 'Outfitters Jeans Men', 'price' => 5999, 'cat' => 'Men Fashion'],
            ['name' => 'Junaid Jamshed Shalwar Kameez', 'price' => 6999, 'cat' => 'Men Fashion'],
            ['name' => 'Edenrobe Polo Shirt', 'price' => 2999, 'cat' => 'Men Fashion'],

            // Women Fashion
            ['name' => 'Khaadi Lawn Suit', 'price' => 7499, 'cat' => 'Women Fashion'],
            ['name' => 'Gul Ahmed Unstitched Dress', 'price' => 8999, 'cat' => 'Women Fashion'],
            ['name' => 'Sapphire Pret Dress', 'price' => 10999, 'cat' => 'Women Fashion'],
            ['name' => 'Alkaram Summer Collection', 'price' => 6599, 'cat' => 'Women Fashion'],
            ['name' => 'Maria B Formal Dress', 'price' => 14999, 'cat' => 'Women Fashion'],

            // Footwear
            ['name' => 'Service Formal Shoes', 'price' => 4999, 'cat' => 'Footwear'],
            ['name' => 'Bata Sneakers', 'price' => 5999, 'cat' => 'Footwear'],
            ['name' => 'Hush Puppies Leather Shoes', 'price' => 9999, 'cat' => 'Footwear'],
            ['name' => 'Ndure Sports Shoes', 'price' => 3999, 'cat' => 'Footwear'],
            ['name' => 'Borjan Sandals', 'price' => 2999, 'cat' => 'Footwear'],

            // Home Appliances
            ['name' => 'Dawlance Microwave Oven', 'price' => 28999, 'cat' => 'Home Appliances'],
            ['name' => 'Haier Refrigerator 12 CFT', 'price' => 55999, 'cat' => 'Home Appliances'],
            ['name' => 'Anex Electric Kettle', 'price' => 3999, 'cat' => 'Home Appliances'],
            ['name' => 'Westpoint Iron', 'price' => 2499, 'cat' => 'Home Appliances'],
            ['name' => 'Gree AC 1 Ton', 'price' => 89999, 'cat' => 'Home Appliances'],

            // Books
            ['name' => 'Matric Physics Book', 'price' => 899, 'cat' => 'Books'],
            ['name' => 'Java Programming Guide', 'price' => 1299, 'cat' => 'Books'],
            ['name' => 'English Grammar Book', 'price' => 699, 'cat' => 'Books'],
            ['name' => 'Islamiat Textbook', 'price' => 499, 'cat' => 'Books'],
            ['name' => 'CSS Exam Preparation Book', 'price' => 1999, 'cat' => 'Books'],

            // Sports
            ['name' => 'CA Cricket Bat Tape Ball', 'price' => 2499, 'cat' => 'Sports'],
            ['name' => 'Football Size 5', 'price' => 3999, 'cat' => 'Sports'],
            ['name' => 'Gym Dumbbells Set 20kg', 'price' => 7999, 'cat' => 'Sports'],
            ['name' => 'Yoga Mat Premium', 'price' => 1999, 'cat' => 'Sports'],
            ['name' => 'Skipping Rope Pro', 'price' => 999, 'cat' => 'Sports'],
        ];

        foreach ($products as $p) {
            Product::create([
                'name'        => $p['name'],
                'description' => 'High quality Pakistani market product',
                'price'       => $p['price'],
                'stock'       => rand(10, 100),
                'category_id' => $categoryMap[$p['cat']],
                'vendor_id'   => $vendor->id,
                'status'      => rand(0, 1) ? 'Approved' : 'Pending',
            ]);
        }

        // ─── Orders ─────────────────────────────────────────────
        Order::create([
            'customer_name'  => 'Ali Khan',
            'customer_email' => 'ali@example.com',
            'product_id'     => 1,
            'vendor_id'      => $vendor->id,
            'quantity'       => 2,
            'total_price'    => 77998,
            'status'         => 'Delivered',
        ]);

        Order::create([
            'customer_name'  => 'Sara Ahmed',
            'customer_email' => 'sara@example.com',
            'product_id'     => 2,
            'vendor_id'      => $vendor->id,
            'quantity'       => 1,
            'total_price'    => 31999,
            'status'         => 'Processing',
        ]);
    }
}