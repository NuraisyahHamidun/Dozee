<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Salesmen;
use App\Models\Manager;
use App\Models\Category;
use App\Services\AprioriService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FortyDetergentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get test manager and salesmen
        $manager = Manager::where('email', 'nuraisyahsiti793@gmail.com')->first();
        $salesmen = Salesmen::where('email', 'ariff@dozee.com')->first();

        // Ensure category "Detergent" exists
        $category = Category::firstOrCreate(['name' => 'Detergent']);

        // 2. Clear pre-existing test items with same codes or names to avoid duplication crashes
        for ($i = 1; $i <= 40; $i++) {
            $code = sprintf('ITM-%04d', $i);
            $name = 'Dozee Detergent ' . $i;

            // Delete existing items matching code or name
            Product::where('item_code', $code)->orWhere('item_name', $name)->delete();
        }

        // 3. Create 40 items
        $createdProducts = [];
        for ($i = 1; $i <= 40; $i++) {
            $code = sprintf('ITM-%04d', $i);
            $price = round(5.00 + (rand(0, 5500) / 100), 2); // RM5 - RM60
            $stock = rand(10, 100);

            $product = Product::create([
                'item_code' => $code,
                'item_name' => 'Dozee Detergent ' . $i,
                'volume' => '1L',
                'category' => 'Detergent',
                'category_id' => $category->id,
                'price' => $price,
                'stock_qty' => $stock,
                'description' => 'Premium cleaning detergent product ' . $i,
            ]);

            $createdProducts[] = $product;
        }

        // 4. Generate sales records linked to these 40 items
        // We will generate 60 transactions
        for ($i = 1; $i <= 60; $i++) {
            // Pick a random product from the newly created 40 products
            $product = $createdProducts[array_rand($createdProducts)];
            
            // Random quantity: 1 - 5
            $qty = rand(1, 5);
            $totalAmount = $product->price * $qty;

            // Mix statuses and roles:
            // 50% chance the transaction is Approved (has approved_by = manager_id)
            // 50% chance the transaction is Pending (has approved_by = null)
            $isApproved = (rand(1, 2) === 1);

            $saleDate = Carbon::now()->subDays(rand(1, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $sale = Sale::create([
                'salesmen_id' => $salesmen ? $salesmen->salesmen_id : 1,
                'total_amount' => $totalAmount,
                'sale_date' => $saleDate,
                'status' => $isApproved ? 'Approved' : 'Pending',
                'approved_by' => ($isApproved && $manager) ? $manager->manager_id : null,
                'date_verify' => $isApproved ? $saleDate->copy()->addMinutes(rand(10, 120)) : null,
                'event_name' => 'Seeded Test Sale ' . $i,
            ]);

            $sale->saleItems()->create([
                'item_id' => $product->item_id,
                'quantity' => $qty,
            ]);
        }

        // 5. Run Apriori Analysis to regenerate buying patterns
        $service = new AprioriService(0.01, 0.05);
        $service->run();
    }
}
