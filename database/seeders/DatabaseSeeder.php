<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Store;
use App\Models\ProductStock;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Create stores
        $london = Store::create([
            'name' => 'London Store',
            'location' => 'London'
        ]);

        $paris = Store::create([
            'name' => 'Paris Store',
            'location' => 'Paris'
        ]);

        $milan = Store::create([
            'name' => 'Milan Store',
            'location' => 'Milan'
        ]);

        // Create products
        $product1 = Product::create([
            'name' => 'Laptop Dell XPS 15',
            'description' => 'High-performance laptop with Intel Core i7',
            'price' => 1299.99,
            'image' => 'laptop.jpg'
        ]);

        $product2 = Product::create([
            'name' => 'iPhone 15 Pro',
            'description' => 'Latest iPhone with A17 Pro chip',
            'price' => 999.99,
            'image' => 'iphone.jpg'
        ]);

        $product3 = Product::create([
            'name' => 'Samsung 4K TV 55"',
            'description' => 'Smart TV with Crystal UHD display',
            'price' => 799.99,
            'image' => 'tv.jpg'
        ]);

        // Create stock for each product in each store
        $stores = [$london, $paris, $milan];
        $products = [$product1, $product2, $product3];

        foreach ($products as $product) {
            foreach ($stores as $store) {
                ProductStock::create([
                    'product_id' => $product->id,
                    'store_id' => $store->id,
                    'quantity' => rand(0, 100)
                ]);
            }
        }
    }
}
