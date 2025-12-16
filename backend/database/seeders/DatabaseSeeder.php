<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create categories
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and gadgets'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'description' => 'Home improvement and garden supplies'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports equipment and accessories'],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Books and educational materials'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create sample products
        $products = [
            ['name' => 'iPhone 15 Pro', 'description' => 'Latest Apple smartphone with A17 Pro chip', 'price' => 999.99, 'category_id' => 1],
            ['name' => 'MacBook Air M3', 'description' => 'Lightweight laptop with M3 chip', 'price' => 1199.99, 'category_id' => 1],
            ['name' => 'Sony WH-1000XM5', 'description' => 'Premium noise-canceling headphones', 'price' => 349.99, 'category_id' => 1],
            ['name' => 'Samsung 65" OLED TV', 'description' => '4K OLED Smart TV', 'price' => 1499.99, 'category_id' => 1],
            ['name' => 'Nike Air Max 90', 'description' => 'Classic sneakers with Air cushioning', 'price' => 129.99, 'category_id' => 2],
            ['name' => 'Levi\'s 501 Jeans', 'description' => 'Original fit jeans', 'price' => 79.99, 'category_id' => 2],
            ['name' => 'North Face Jacket', 'description' => 'Waterproof outdoor jacket', 'price' => 199.99, 'category_id' => 2],
            ['name' => 'Dyson V15 Vacuum', 'description' => 'Cordless vacuum cleaner', 'price' => 649.99, 'category_id' => 3],
            ['name' => 'Weber Gas Grill', 'description' => '3-burner propane grill', 'price' => 549.99, 'category_id' => 3],
            ['name' => 'Yoga Mat Pro', 'description' => 'Non-slip premium yoga mat', 'price' => 59.99, 'category_id' => 4],
            ['name' => 'Dumbbell Set', 'description' => 'Adjustable dumbbells 5-50 lbs', 'price' => 299.99, 'category_id' => 4],
            ['name' => 'Clean Code', 'description' => 'A Handbook of Agile Software Craftsmanship', 'price' => 39.99, 'category_id' => 5],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}

