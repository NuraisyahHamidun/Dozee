<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class ProductDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks to truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        SaleItem::truncate();
        Sale::truncate();
        Product::truncate();
        Category::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            'Laundry Detergent' => [
                ['Ultra White', '10KG', 45, 'Ultra White detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Ultra White', '4KG', 18, 'Ultra White detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Ultra White', '1.8KG', 10, 'Ultra White detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Ultra White', '1.5KG', 8, 'Ultra White detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Ultra White', '25KG', 95, 'Ultra White detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Red Sporty', '10KG', 45, 'Red Sporty detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Red Sporty', '4KG', 18, 'Red Sporty detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Red Sporty', '1.8KG', 10, 'Red Sporty detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Red Sporty', '1.5KG', 8, 'Red Sporty detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Red Sporty', '25KG', 95, 'Red Sporty detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Blue Caring', '10KG', 45, 'Blue Caring detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Blue Caring', '4KG', 18, 'Blue Caring detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Blue Caring', '1.8KG', 10, 'Blue Caring detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Blue Caring', '1.5KG', 8, 'Blue Caring detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Blue Caring', '25KG', 95, 'Blue Caring detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Pink Soft', '10KG', 45, 'Pink Soft detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Pink Soft', '4KG', 18, 'Pink Soft detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Pink Soft', '1.8KG', 10, 'Pink Soft detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Pink Soft', '1.5KG', 8, 'Pink Soft detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Pink Soft', '25KG', 95, 'Pink Soft detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Apple Fresh', '10KG', 45, 'Apple Fresh detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Apple Fresh', '4KG', 18, 'Apple Fresh detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Apple Fresh', '1.8KG', 10, 'Apple Fresh detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Apple Fresh', '1.5KG', 8, 'Apple Fresh detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Apple Fresh', '25KG', 95, 'Apple Fresh detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Lemon Clean', '10KG', 45, 'Lemon Clean detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Lemon Clean', '4KG', 18, 'Lemon Clean detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Lemon Clean', '1.8KG', 10, 'Lemon Clean detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Lemon Clean', '1.5KG', 8, 'Lemon Clean detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Lemon Clean', '25KG', 95, 'Lemon Clean detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Aroma Rose', '10KG', 45, 'Aroma Rose detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Aroma Rose', '4KG', 18, 'Aroma Rose detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Aroma Rose', '1.8KG', 10, 'Aroma Rose detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Aroma Rose', '1.5KG', 8, 'Aroma Rose detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Aroma Rose', '25KG', 95, 'Aroma Rose detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Sweet Citrus', '10KG', 45, 'Sweet Citrus detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Sweet Citrus', '4KG', 18, 'Sweet Citrus detergent designed for effective cleaning, removing stains, and leading a pleasant fragrance on clothes.'],
                ['Sweet Citrus', '1.8KG', 10, 'Sweet Citrus detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Sweet Citrus', '1.5KG', 8, 'Sweet Citrus detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
                ['Sweet Citrus', '25KG', 95, 'Sweet Citrus detergent designed for effective cleaning, removing stains, and leaving a pleasant fragrance on clothes.'],
            ],
            'Fabric Care' => [
                ['Aroma Fabric Care Pink', '25KG', 85, 'Fabric softener that keeps clothes soft, fresh, and fragrant.'],
                ['Aroma Fabric Care Blue', '25KG', 85, 'Fabric softener that reduces wrinkles and provides long-lasting fragrance.'],
                ['Aroma Fabric Care White', '25KG', 85, 'Fabric softener suitable for all fabrics with gentle scent.'],
            ],
            'Cleaning' => [
                ['Hand Wash', 'Standard', 6, 'Liquid hand wash that removes dirt and germs while being gentle on skin.'],
                ['Dish Wash', 'Standard', 5, 'Dishwashing liquid that cuts grease and leaves dishes clean and shiny.'],
                ['Floor Cleaner', 'Standard', 12, 'Floor cleaning solution that removes stains and leaves a fresh scent.'],
            ],
            'Industrial' => [
                ['Car Shampoo', 'Standard', 15, 'Car cleaning shampoo that removes dirt without damaging paint.'],
                ['Engine Chemical', 'Standard', 20, 'Chemical solution for cleaning engine parts and removing grease.'],
                ['Industrial XP2', '25KG', 120, 'Heavy-duty industrial cleaner for large-scale cleaning operations.'],
                ['Bleach', 'Standard', 7, 'Bleach solution for whitening, disinfecting, and stain removal.'],
            ],
        ];

        foreach ($data as $categoryName => $items) {
            $category = Category::create(['name' => $categoryName]);
            
            foreach ($items as $item) {
                Product::create([
                    'item_name' => $item[0],
                    'volume' => $item[1],
                    'price' => $item[2],
                    'description' => $item[3],
                    'category' => $categoryName, // Add this for legacy support and filtering
                    'category_id' => $category->id,
                    'stock_qty' => 100, // Default stock
                ]);
            }
        }
    }
}
