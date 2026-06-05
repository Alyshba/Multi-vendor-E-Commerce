<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create categories table if not exists
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Create carts table if not exists
        if (!Schema::hasTable('carts')) {
            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

        // 3. Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }
        });

        // 4. Add columns to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable(); // unique check added later or left nullable
            }
            if (!Schema::hasColumn('products', 'compare_price')) {
                $table->decimal('compare_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0);
            }
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('products', 'images')) {
                $table->json('images')->nullable();
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
        });

        // 5. Add columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'shipping')) {
                $table->decimal('shipping', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'total')) {
                $table->decimal('total', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid');
            }
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable();
            }
        });

        // 6. Add columns to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable();
            }
            if (!Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('order_items', 'total')) {
                $table->decimal('total', 10, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'price', 'total']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'subtotal', 'shipping', 'total', 'payment_method', 'payment_status', 'shipping_address', 'notes']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['slug', 'compare_price', 'stock_quantity', 'category_id', 'image', 'images', 'is_active', 'is_featured']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'address']);
        });

        Schema::dropIfExists('carts');
        Schema::dropIfExists('categories');
    }
};
