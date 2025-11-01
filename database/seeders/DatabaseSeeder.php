<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // In database/seeders/DatabaseSeeder.php

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'two_factor_confirmed_at' => now(),
        ]);

        $categoriesWithSubcategories = [
            'Men' => [
                'Shirt', 
                'T-Shirt', 
                'Pants', 
                'Jeans'
            ],
            'Women' => [
                'Shirt', 
                'T-Shirt', 
                'Pants', 
                'Jeans', 
                'Skirt'
            ],
            'Accessories' => [
                'Watch', 
                'Glasses', 
                'Ring'
            ],
            // --- ADDED SUBCATEGORIES HERE ---
            'Fragments' => [
                'Parfum',
                'Eau de Toilette',
                'Cologne',
                'Eau de Parfum'
            ]
        ];

        foreach($categoriesWithSubcategories as $categoryName => $subCategoryNames){
            $category = Category::create([
                'name' => $categoryName
            ]);

            if(!empty($subCategoryNames)){
                foreach($subCategoryNames as $subCategoryName){
                    Subcategory::create([
                        'name' => $subCategoryName,
                        'category_id' => $category->id,
                    ]);
                }
            }
        }

        $this->call(ProductSeeder::class);
    }
}
