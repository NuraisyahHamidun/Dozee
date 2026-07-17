<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Manager;
use App\Models\Salesmen;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\AprioriAnalysis;
use App\Services\AprioriService;
use Maatwebsite\Excel\Facades\Excel;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fetch existing users (Restore them with original demo credentials if missing)
        $manager = Manager::where('email', 'nuraisyahsiti793@gmail.com')->first();
        if (!$manager) {
            $manager = Manager::create([
                'name' => 'Nur Aisyah Siti',
                'username' => 'nuraisyahsiti',
                'email' => 'nuraisyahsiti793@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('Nurisy@22'),
                'address' => 'No. 12, Jalan Maju, 50480 Kuala Lumpur',
                'phone_number' => '018-2349809',
            ]);
            $this->command->info("Restored Manager: {$manager->name} ({$manager->email})");
        }

        $salesman = Salesmen::where('email', 'ariff@dozee.com')->first();
        if (!$salesman) {
            $salesman = Salesmen::create([
                'manager_id' => $manager->manager_id,
                'name' => 'Nur Ariff',
                'username' => 'nurariff',
                'email' => 'ariff@dozee.com',
                'password' => \Illuminate\Support\Facades\Hash::make('Ariff@1234'),
                'address' => '22 Jalan Suria',
                'phone_number' => '011-2312456',
                'staff_code' => Salesmen::generateUniqueStaffCode(),
            ]);
            $this->command->info("Restored Salesman: {$salesman->name} ({$salesman->email})");
        }

        $this->command->info("Using Manager: {$manager->name} ({$manager->email})");
        $this->command->info("Using Salesman: {$salesman->name} ({$salesman->email})");

        // 2. Truncate business tables only
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SaleItem::truncate();
        Sale::truncate();
        Promotion::truncate();
        AprioriAnalysis::truncate();
        Product::truncate();
        Category::truncate();
        DB::table('promotion_association_rule')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("Cleaned up existing business tables.");

        // 3. Load products from Item.xlsx
        $excelPath = storage_path('app/Item.xlsx');
        if (!file_exists($excelPath)) {
            $this->command->error("Item.xlsx not found at $excelPath.");
            return;
        }

        $rows = Excel::toArray([], $excelPath)[0];
        $categoriesMap = [];
        $products = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue; // skip header
            }

            $itemName = $row[2] ?? null;
            $weight = $row[3] ?? '';
            $categoryName = $row[4] ?? 'General';
            $description = $row[5] ?? '';
            $price = $row[6] ?? 0.00;

            if (empty($itemName)) {
                continue;
            }

            if (!isset($categoriesMap[$categoryName])) {
                $category = Category::firstOrCreate(['name' => $categoryName]);
                $categoriesMap[$categoryName] = $category->id;
            }

            $categoryId = $categoriesMap[$categoryName];

            $product = Product::create([
                'item_name' => $itemName,
                'volume' => $weight,
                'category' => $categoryName,
                'category_id' => $categoryId,
                'description' => $description,
                'price' => (float)$price,
                'stock_qty' => rand(100, 500),
            ]);
            $products[] = $product;
        }

        $totalProducts = count($products);
        $this->command->info("Imported $totalProducts products from Excel.");

        if ($totalProducts < 4) {
            $this->command->error("Not enough products in Excel to build realistic co-occurrence patterns.");
            return;
        }

        // Define two pairs for high co-occurrence
        $pair1 = [$products[0], $products[1]];
        $pair2 = [$products[2], $products[3]];

        $salesCount = 100;
        $singleCount = 40; // 40% single item
        $multiCount = 60;  // 60% multi item (35 of pair1, 25 of pair2)

        $events = ['Roadshow Midvalley', 'Morning Shift', 'Evening Session', 'AEON Mall Booth', 'Merdeka Promo Week'];
        $paymentMethods = ['QR', 'Cash'];

        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2026, 6, 30);
        $secondsDiff = $endDate->diffInSeconds($startDate);

        $salesmenList = Salesmen::all();

        // 4. Generate Transactions
        for ($i = 1; $i <= $salesCount; $i++) {
            // Select random salesman
            $currSalesman = $salesmenList->random();

            // Select random date
            $saleDate = $startDate->copy()->addSeconds(rand(0, $secondsDiff));

            // Select payment method
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            // Select event name
            $eventName = $events[array_rand($events)] . " " . $saleDate->format('Y');

            // Select items for this basket
            $basket = [];
            $type = 'single';

            if ($i <= $singleCount) {
                // Single item transaction
                $basket[] = $products[array_rand($products)];
                $type = 'single';
            } else if ($i <= $singleCount + 35) {
                // Pair 1 transaction (co-occurrence)
                $basket = $pair1;
                // Add an occasional random item to make it mixed/natural
                if (rand(1, 10) <= 3) {
                    $basket[] = $products[array_rand($products)];
                }
                $type = 'bundle';
            } else {
                // Pair 2 transaction (co-occurrence)
                $basket = $pair2;
                // Add an occasional random item
                if (rand(1, 10) <= 3) {
                    $basket[] = $products[array_rand($products)];
                }
                $type = 'bundle';
            }

            // Create transaction record
            $sale = Sale::create([
                'salesmen_id' => $currSalesman->salesmen_id,
                'event_name' => $eventName,
                'total_amount' => 0,
                'type' => $type,
                'payment_method' => $paymentMethod,
                'status' => 'Approved',
                'sale_date' => $saleDate,
                'date_create' => $saleDate,
                'date_modifier' => $saleDate->copy()->addMinutes(rand(5, 60)),
                'date_verify' => $saleDate->copy()->addMinutes(rand(61, 120)),
                'approved_by' => $manager->manager_id,
            ]);

            $totalAmount = 0;
            foreach ($basket as $prod) {
                $qty = rand(1, 3);
                $sale->saleItems()->create([
                    'item_id' => $prod->item_id,
                    'quantity' => $qty,
                ]);

                // Decrement stock
                $prod->decrement('stock_qty', $qty);
                $totalAmount += $prod->price * $qty;
            }

            $sale->update(['total_amount' => $totalAmount]);
        }

        $this->command->info("Successfully generated $salesCount Sales Transactions.");

        // 5. Run Apriori Algorithm (which automatically calls syncFromAprioriRules)
        $this->command->info("Running Apriori Engine to discover patterns and generate promotions...");
        $apriori = new AprioriService(0.05, 0.6); // 5% support, 60% confidence
        $results = $apriori->run();

        $rulesCount = count($results['associationRules']);
        $this->command->info("Apriori completed: $rulesCount rules generated.");

        $promotionsCount = Promotion::count();
        $this->command->info("Active Bundle Promotions generated: $promotionsCount.");
    }
}
