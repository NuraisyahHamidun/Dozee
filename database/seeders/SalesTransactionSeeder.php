<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Salesman;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use App\Services\AprioriService;

class SalesTransactionSeeder extends Seeder
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
        \App\Models\AprioriAnalysis::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $salesman = Salesman::first();
        if (!$salesman) {
            return;
        }

        // Find specific products to create patterns
        $ultraWhite = Product::where('item_name', 'Ultra White')->where('volume', '10KG')->first();
        $fabricPink = Product::where('item_name', 'Aroma Fabric Care Pink')->first();
        
        $dishWash = Product::where('item_name', 'Dish Wash')->first();
        $floorCleaner = Product::where('item_name', 'Floor Cleaner')->first();
        
        $carShampoo = Product::where('item_name', 'Car Shampoo')->first();
        $engineChem = Product::where('item_name', 'Engine Chemical')->first();
        
        $bleach = Product::where('item_name', 'Bleach')->first();

        // Pattern 1: Ultra White + Aroma Fabric Care Pink (Common pair)
        // 12 transactions
        for ($i = 0; $i < 12; $i++) {
            $this->createSale($salesman->salesman_id, [$ultraWhite, $fabricPink]);
        }

        // Pattern 2: Dish Wash + Floor Cleaner (Common pair)
        // 10 transactions
        for ($i = 0; $i < 10; $i++) {
            $this->createSale($salesman->salesman_id, [$dishWash, $floorCleaner]);
        }

        // Pattern 3: Car Shampoo + Engine Chemical
        // 8 transactions
        for ($i = 0; $i < 8; $i++) {
            $this->createSale($salesman->salesman_id, [$carShampoo, $engineChem]);
        }

        // Pattern 4: Floor Cleaner + Bleach
        // 6 transactions
        for ($i = 0; $i < 6; $i++) {
            $this->createSale($salesman->salesman_id, [$floorCleaner, $bleach]);
        }

        // Random single items to add noise
        $allProducts = Product::all();
        for ($i = 0; $i < 10; $i++) {
            $this->createSale($salesman->salesman_id, [$allProducts->random()]);
        }

        // Run Apriori Analysis to generate the buying patterns
        $service = new AprioriService(0.1, 0.4); // 10% support, 40% confidence
        $service->run();
    }

    private function createSale($salesmanId, $products)
    {
        $sale = Sale::create([
            'salesman_id' => $salesmanId,
            'total_amount' => 0,
            'sale_date' => now()->subDays(rand(1, 60)),
        ]);

        $total = 0;
        foreach ($products as $product) {
            if (!$product) continue;
            
            $quantity = rand(1, 2);
            $sale->saleItems()->create([
                'item_id' => $product->item_id,
                'quantity' => $quantity,
            ]);
            
            $total += $product->price * $quantity;
        }

        $sale->update(['total_amount' => $total]);
    }
}
