<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Salesman;
use App\Models\SaleItem;
use App\Services\AprioriService;
use Carbon\Carbon;

class FiftySalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesmanAriff = Salesman::where('email', 'ariff@dozee.com')->first();
        $otherSalesmen = Salesman::where('email', '!=', 'ariff@dozee.com')->get();
        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        // We will generate 50 sales
        for ($i = 0; $i < 50; $i++) {
            // Determine salesman: 80% chance it is Ariff, otherwise a random other salesman
            if ($salesmanAriff && ($otherSalesmen->isEmpty() || rand(1, 10) <= 8)) {
                $salesmanId = $salesmanAriff->salesman_id;
            } else {
                $salesmanId = $otherSalesmen->random()->salesman_id;
            }

            // Select 1 to 3 random products to form a basket
            $numItems = rand(1, 3);
            // If the collection is smaller than the requested random amount, adjust
            $numItems = min($numItems, $products->count());
            $selectedProducts = $products->random($numItems);
            if ($selectedProducts instanceof Product) {
                $selectedProducts = collect([$selectedProducts]);
            }

            // Create the sale with random date in last 30 days
            $saleDate = Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $sale = Sale::create([
                'salesman_id' => $salesmanId,
                'total_amount' => 0,
                'sale_date' => $saleDate,
                'status' => 'Approved', // Approved to make sure it shows up on dashboard
                'event_name' => 'Testing Sale ' . ($i + 1),
            ]);

            $total = 0;
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $sale->saleItems()->create([
                    'item_id' => $product->item_id,
                    'quantity' => $quantity,
                ]);

                $total += $product->price * $quantity;
            }

            $sale->update(['total_amount' => $total]);
        }

        // Run Apriori Analysis to generate/update the buying patterns
        $service = new AprioriService(0.01, 0.05); // Lower thresholds to ensure patterns show up with 50 records
        $service->run();
    }
}
