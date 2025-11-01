<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return; // Ensure categories exist before seeding products
        }

        $productsData = [
            [
                'name' => 'Lenin Pants',
                'description' => 'Great quality and can wear as casual or for work. Does not shrink when wearing a long time.',
                'sku' => 'MEN-LENIN-BLC-01',
                'color_code' => '#000000',
                'price' => 15.99,
                'sale_price' => null,
                'stock' => 120, // ✅ new field
                'sale_start_date' => null,
                'sale_end_date' => null,
                'subcategory_id' => 1,
                'user_id' => 1,
                'product_image' => [
                    'https://cottoncottageindia.com/cdn/shop/files/BlackHandDyedViscoseLinenMenDrawstringPantMDSPT04243_1.jpg?v=1728127916&width=375',
                    'https://image.hm.com/assets/hm/50/56/5056249f81d5d23c1de5e93f3603c1e1b368bbc7.jpg?imwidth=2160'
                ]
            ],
            [
                'name' => 'Classic Crewneck T-Shirt',
                'description' => 'A high-quality, 100% cotton crewneck t-shirt. Perfect for everyday wear.',
                'sku' => 'MEN-TSHIRT-WHT-01',
                'color_code' => '#FFFFFF',
                'price' => 24.99,
                'sale_price' => null,
                'stock' => 300, // ✅
                'sale_start_date' => null,
                'sale_end_date' => null,
                'subcategory_id' => 2,
                'user_id' => 1,
                'product_image' => [
                    'https://www.boody.co.uk/cdn/shop/files/B10966_WHITE_Women_sClassicCrewNeckT-Shirt_White-front-full-crop-01_small_5b617ea3-a21a-4137-8a55-9c0a9170250d.jpg?v=1745799913',
                    'https://www.jcrew.com/s7-img-facade/BY359_WT0002_m?hei=850&crop=0,0,680,0'
                ]
            ],
            [
                'name' => 'Floral Print Midi Skirt',
                'description' => 'A light and airy midi skirt with a beautiful floral print, perfect for summer.',
                'sku' => 'WMN-SKIRT-FLR-01',
                'color_code' => '#FFC0CB',
                'price' => 45.00,
                'sale_price' => 39.99,
                'stock' => 80, // ✅
                'sale_start_date' => Carbon::now()->subDays(7),
                'sale_end_date' => Carbon::now()->addMonth(),
                'subcategory_id' => 9,
                'user_id' => 1,
                'product_image' => [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT1qms9J4K8k3PE3vUx6GLlyaHF_97PvesLMg&s',
                    'https://www.lulus.com/images/product/xlarge/9997741_2072556.jpg?w=375'
                ]
            ],
            [
                'name' => 'Minimalist Leather Watch',
                'description' => 'A sleek, minimalist watch with a genuine leather strap and stainless steel case.',
                'sku' => 'ACC-WATCH-LTHR-01',
                'color_code' => '#D2B48C',
                'price' => 129.99,
                'sale_price' => null,
                'stock' => 50, // ✅
                'sale_start_date' => null,
                'sale_end_date' => null,
                'subcategory_id' => 10,
                'user_id' => 1,
                'product_image' => [
                    'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQWbGdITCpn4S05ssZrcFdez4zb7sVKuGK3rw&s'
                ]
            ],
            [
                'name' => 'Creed Aventus',
                'description' => 'A popular and mass-appealing fragrance with notes of pineapple, bergamot, and musk.',
                'sku' => 'FRG-CREED-AVT-01',
                'color_code' => '#FFFFFF',
                'price' => 349.99,
                'sale_price' => null,
                'stock' => 25, // ✅
                'sale_start_date' => null,
                'sale_end_date' => null,
                'subcategory_id' => 16,
                'user_id' => 1,
                'product_image' => [
                    'https://creedboutique.com/cdn/shop/files/aventus-100ml-bottle_3413e5f4-3eee-40b3-8451-2546a370ec5b.jpg?v=1734710265&width=1500'
                ]
            ],
        ];

        foreach ($productsData as $productData) {
            $imageUrls = $productData['product_image'];
            unset($productData['product_image']);

            $product = Product::create($productData);

            foreach ($imageUrls as $imageUrl) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                ]);
            }
        }
    }
}
