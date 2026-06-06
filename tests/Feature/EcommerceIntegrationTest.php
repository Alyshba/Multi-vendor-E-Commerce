<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EcommerceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Authentication & Middleware Guard Tests
     */
    public function test_authentication_and_middleware_guards(): void
    {
        // Setup Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'Tahaadmin@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'admin',
        ]);

        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'TahaCustomer@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'customer',
        ]);

        $vendorUser = User::create([
            'name' => 'Vendor User',
            'email' => 'TahaVendor@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'vendor',
        ]);

        // Setup Vendor record corresponding to vendor user
        Vendor::create([
            'name' => 'Test Vendor Shop',
            'owner_name' => 'Vendor Owner',
            'email' => 'Taha@gmail.com',
            'phone' => '1234567890',
            'address' => 'Vendor Address',
            'status' => 'active',
            'user_id' => $vendorUser->id,
        ]);

        // Admin Access: Hit the admin dashboard route, assert 200 OK
        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(200);

        // Customer Access: Hit the admin dashboard route, assert 403 Forbidden
        $this->actingAs($customer)
            ->get('/dashboard')
            ->assertStatus(403);

        // Vendor Access: Hit the vendor dashboard route, assert 200 OK
        $this->actingAs($vendorUser)
            ->get('/vendor/products')
            ->assertStatus(200);
    }

    /**
     * 2. E2E Inventory and Approval Flow Test
     */
    public function test_e2e_inventory_and_approval_flow(): void
    {
        // Setup Vendor and Category
        $vendorUser = User::create([
            'name' => 'Vendor User 2',
            'email' => 'Taha@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'vendor',
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Shop 2',
            'owner_name' => 'Vendor Owner 2',
            'email' => 'Taha@gmail.com',
            'phone' => '1234567890',
            'address' => 'Vendor Address 2',
            'status' => 'active',
            'user_id' => $vendorUser->id,
        ]);

        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic items',
        ]);

        // Simulate Vendor authenticated session
        $this->actingAs($vendorUser);

        // Send a POST request to the product creation route to add a test product
        $productData = [
            'name' => 'Test Product E2E',
            'description' => 'Test Description E2E',
            'price' => 299.99,
            'stock' => 10,
            'category_id' => $category->id,
        ];

        $response = $this->post(route('vendor.products.store'), $productData);

        // Assert redirect back to products index after creation
        $response->assertRedirect(route('vendor.products.index'));

        // Assert that the product is created in the database with approval_status set to 'pending'
        $product = Product::where('name', 'Test Product E2E')->first();
        $this->assertNotNull($product);
        $this->assertEquals('pending', $product->approval_status);
        $this->assertDatabaseHas('products', [
            'name' => 'Test Product E2E',
            'approval_status' => 'pending',
            'vendor_id' => $vendor->id,
        ]);

        // Switch to Admin session
        $admin = User::create([
            'name' => 'Admin User 2',
            'email' => 'Taha@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'admin',
        ]);

        // Send a request to the approval route for that specific product
        $approveResponse = $this->actingAs($admin)->patch(route('products.approve', $product));
        $approveResponse->assertStatus(302); // Redirect back

        // Assert that approval_status updates to 'approved'
        $this->assertEquals('approved', $product->fresh()->approval_status);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'approval_status' => 'approved',
        ]);
    }

    /**
     * 3. Customer Purchase & Key Relationship Test
     */
    public function test_customer_purchase_lifecycle_and_assertions(): void
    {
        // Setup Vendor, Category, and approved Product
        $vendorUser = User::create([
            'name' => 'Vendor User 3',
            'email' => 'Taha@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'vendor',
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Shop 3',
            'owner_name' => 'Vendor Owner 3',
            'email' => 'Taha@gmail.com',
            'phone' => '1234567890',
            'address' => 'Vendor Address 3',
            'status' => 'active',
            'user_id' => $vendorUser->id,
        ]);

        $category = Category::create([
            'name' => 'Apparel',
            'slug' => 'apparel',
            'description' => 'Clothing items',
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'name' => 'QA Brand T-Shirt',
            'sku' => 'TSHIRT-001',
            'slug' => 'qa-brand-t-shirt',
            'category' => 'Apparel',
            'category_id' => $category->id,
            'price' => 19.99,
            'stock' => 15,
            'stock_quantity' => 15,
            'approval_status' => 'approved',
            'is_active' => true,
            'description' => 'A test t-shirt',
        ]);

        // Setup Customer User
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'Taha@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'customer',
        ]);

        // Simulate Customer authenticated session
        $this->actingAs($customer);

        // Hit the product marketplace page, ensuring the approved product is visible
        $marketplaceResponse = $this->get(route('customer.products'));
        $marketplaceResponse->assertStatus(200);
        $marketplaceResponse->assertSee('QA Brand T-Shirt');

        // Send a POST request to add this product to the cart
        $cartAddResponse = $this->post(route('customer.cart.add'), [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
        $cartAddResponse->assertStatus(302); // Redirects back

        // Crucial: Assert that the product_id correctly maps to the product's id primary key
        $cartItem = Cart::where('user_id', $customer->id)->first();
        $this->assertNotNull($cartItem);
        $this->assertEquals($product->id, $cartItem->product_id);
        $this->assertDatabaseHas('carts', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        // Send a POST request to the checkout route to place the order
        $orderPlaceResponse = $this->post(route('customer.order.place'), [
            'shipping_address' => '789 Testing Lane, Automation City',
            'payment_method' => 'cod',
            'notes' => 'Deliver during business hours.',
        ]);
        $orderPlaceResponse->assertRedirect(route('customer.orders'));

        // Assert that an entry is created in the orders and order_items tables
        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $customer->id,
            'shipping_address' => '789 Testing Lane, Automation City',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        // Stock Check: Assert that the product's operational stock column has decremented by the purchased quantity
        $productFresh = $product->fresh();
        $this->assertEquals(12, $productFresh->stock);
        $this->assertEquals(12, $productFresh->stock_quantity);
    }

    /**
     * 4. Data Integrity & API Endpoints Assertions
     */
    public function test_api_endpoints_integrity(): void
    {
        // Setup Vendor, Category, Product, and Customer
        $customer = User::create([
            'name' => 'API Customer',
            'email' => 'Taha@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'customer',
        ]);

        $vendorUser = User::create([
            'name' => 'Vendor User 4',
            'email' => 'Taha@gmail.com',
            'password' => bcrypt('123Taha'),
            'role' => 'vendor',
        ]);

        $vendor = Vendor::create([
            'name' => 'Vendor Shop 4',
            'owner_name' => 'Vendor Owner 4',
            'email' => 'Taha@gmail.com',
            'phone' => '1234567890',
            'address' => 'Vendor Address 4',
            'status' => 'active',
            'user_id' => $vendorUser->id,
        ]);

        $category = Category::create([
            'name' => 'Home',
            'slug' => 'home',
            'description' => 'Home items',
        ]);

        Product::create([
            'vendor_id' => $vendor->id,
            'name' => 'API Test Product',
            'sku' => 'API-001',
            'slug' => 'api-test-product',
            'category' => 'Home',
            'category_id' => $category->id,
            'price' => 49.99,
            'stock' => 20,
            'stock_quantity' => 20,
            'approval_status' => 'approved',
            'is_active' => true,
            'description' => 'A test product for API',
        ]);

        // Generate JWT token for the authenticated customer using the api guard
        $token = auth('api')->login($customer);
        $this->assertNotEmpty($token);

        // Assert that the public /api/products endpoint successfully returns 200 OK with valid JSON structure
        $productsResponse = $this->getJson('/api/products');
        $productsResponse->assertStatus(200);
        $productsResponse->assertJsonStructure([
            'data' => [
                '*' => [
                    'id', 'name', 'sku', 'price', 'stock', 'approval_status'
                ]
            ]
        ]);

        // Assert that the authenticated /api/v1/cart endpoint successfully returns 200 OK with valid JSON structure
        $cartResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/cart');

        $cartResponse->assertStatus(200);
        $cartResponse->assertJsonStructure([
            'items', 'total', 'count'
        ]);

        // Assert that the authenticated /api/v1/orders endpoint successfully returns 200 OK with valid JSON structure
        $ordersResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/orders');

        $ordersResponse->assertStatus(200);
        $ordersResponse->assertJsonStructure([
            'data', 'current_page', 'first_page_url', 'last_page'
        ]);
    }
}
