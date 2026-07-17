<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Manager;
use App\Models\Salesmen;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\AprioriService;

class DetergentProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Find or create the "Detergent" category
        $category = Category::firstOrCreate(['name' => 'Detergent']);

        // 2. Ensure test manager exists with requested password
        $manager = Manager::updateOrCreate(
            ['email' => 'nuraisyahsiti793@gmail.com'],
            [
                'name' => 'Nur Aisyah Siti',
                'username' => 'nuraisyahsiti',
                'password' => Hash::make('Nurisy@22'),
                'address' => 'No. 12, Jalan Maju, 50480 Kuala Lumpur',
                'phone_number' => '018-2349809',
            ]
        );

        // 3. Ensure test salesmen exists with requested password
        $salesmen = Salesmen::updateOrCreate(
            ['email' => 'ariff@dozee.com'],
            [
                'manager_id' => $manager->manager_id,
                'name' => 'Nur Ariff',
                'username' => 'nurariff',
                'password' => Hash::make('Ariff@1234'),
                'address' => '22 Jalan Suria',
                'phone_number' => '011-2312456',
                'staff_code' => Salesmen::generateUniqueStaffCode(),
            ]
        );

        // 4. Products list (30 unique detergent products)
        $detergentProducts = [
            ['name' => 'Dozee Ultra Liquid 1L', 'volume' => '1L'],
            ['name' => 'Dozee Power Powder 2kg', 'volume' => '2kg'],
            ['name' => 'FreshClean Liquid Wash', 'volume' => '1L'],
            ['name' => 'BrightWash Powder 1kg', 'volume' => '1kg'],
            ['name' => 'AntiStain Pro Liquid', 'volume' => '1L'],
            ['name' => 'EcoClean Powder 2kg', 'volume' => '2kg'],
            ['name' => 'SuperFoam Liquid 500ml', 'volume' => '500ml'],
            ['name' => 'AromaFresh Detergent Liquid', 'volume' => '1L'],
            ['name' => 'HeavyDuty Powder Clean', 'volume' => '2kg'],
            ['name' => 'SoftTouch Liquid Wash', 'volume' => '1L'],
            ['name' => 'UltraBright Powder', 'volume' => '1.5kg'],
            ['name' => 'PowerClean Liquid 1L', 'volume' => '1L'],
            ['name' => 'StainFree Powder Detergent', 'volume' => '2kg'],
            ['name' => 'NatureWash Eco Liquid', 'volume' => '1L'],
            ['name' => 'MaxClean Powder 2kg', 'volume' => '2kg'],
            ['name' => 'UltraCare Liquid Detergent', 'volume' => '1.5L'],
            ['name' => 'FreshScent Powder Wash', 'volume' => '1.5kg'],
            ['name' => 'DeepClean Liquid Pro', 'volume' => '1.5L'],
            ['name' => 'EasyWash Powder 1kg', 'volume' => '1kg'],
            ['name' => 'BrightMax Liquid 500ml', 'volume' => '500ml'],
            ['name' => 'GreenClean Eco Powder', 'volume' => '2kg'],
            ['name' => 'ProWash Liquid Detergent', 'volume' => '1L'],
            ['name' => 'StainBuster Powder 2kg', 'volume' => '2kg'],
            ['name' => 'SoftClean Liquid Wash', 'volume' => '1L'],
            ['name' => 'PowerFoam Powder Clean', 'volume' => '1.5kg'],
            ['name' => 'FreshGlow Liquid Detergent', 'volume' => '1L'],
            ['name' => 'UltraFresh Powder Wash', 'volume' => '1.5kg'],
            ['name' => 'CleanMax Liquid 1L', 'volume' => '1L'],
            ['name' => 'SuperBright Powder Pro', 'volume' => '2kg'],
            ['name' => 'Dozee Premium Liquid 1L', 'volume' => '1L'],
        ];

        $productModels = [];

        foreach ($detergentProducts as $prodInfo) {
            // Price: random (RM5 - RM50)
            $price = round(5.00 + (rand(0, 4500) / 100), 2);
            // Stock: random (10 - 100)
            $stock = rand(10, 100);

            $product = Product::updateOrCreate(
                ['item_name' => $prodInfo['name']],
                [
                    'volume' => $prodInfo['volume'],
                    'price' => $price,
                    'stock_qty' => $stock,
                    'category' => 'Detergent',
                    'category_id' => $category->id,
                    'description' => 'Premium cleaning detergent product: ' . $prodInfo['name'] . ', designed for effective laundry care.',
                ]
            );

            $productModels[] = $product;
        }

        // 5. Seed some sales transactions (approved) spread over the last 6 months
        // This generates reports, analytics updates, and monthly charts.
        for ($i = 0; $i < 15; $i++) {
            $date = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 27));
            $sale = Sale::create([
                'salesmen_id' => $salesmen->salesmen_id,
                'total_amount' => 0,
                'sale_date' => $date,
                'status' => 'Approved',
                'date_create' => $date,
                'date_verify' => $date->copy()->addMinutes(rand(10, 60)),
            ]);

            // Pick 2-4 random products from the new detergent items
            $pickCount = rand(2, 4);
            $randomProducts = collect($productModels)->random($pickCount);
            
            $totalAmount = 0;
            foreach ($randomProducts as $product) {
                $qty = rand(1, 3);
                $sale->saleItems()->create([
                    'item_id' => $product->item_id,
                    'quantity' => $qty,
                ]);

                // Decrement stock
                $product->decrement('stock_qty', $qty);

                $totalAmount += $product->price * $qty;
            }

            $sale->update(['total_amount' => round($totalAmount, 2)]);
        }

        // 6. Run Apriori service to regenerate the buying patterns
        $apriori = new AprioriService(0.01, 0.1);
        $apriori->run();
    }
}
