<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Manager;
use App\Models\Salesmen;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\AprioriAnalysis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\AprioriService;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key checks to safely truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear all existing data
        SaleItem::truncate();
        Sale::truncate();
        Promotion::truncate();
        AprioriAnalysis::truncate();
        Product::truncate();
        Category::truncate();
        Salesmen::truncate();
        Manager::truncate();
        DB::table('promotion_association_rule')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Create Manager
        $manager = Manager::create([
            'name' => 'Nur Aisyah Siti',
            'email' => 'nuraisyahsiti793@gmail.com',
            'username' => 'nuraisyah_793',
            'password' => Hash::make('Nurisy@22'),
            'address' => 'No. 12, Jalan Maju, 50480 Kuala Lumpur',
        ]);

        // 2. Create Salesmen
        $salesmenAlya = Salesmen::create([
            'manager_id' => $manager->manager_id,
            'name' => 'Alya',
            'email' => 'alya@dozee.com',
            'username' => 'alya_dozee',
            'password' => Hash::make('Aly@1234'),
            'address' => 'No. 23, Jalan Merdeka, 50000 Kuala Lumpur',
        ]);

        $salesmenDiana = Salesmen::create([
            'manager_id' => $manager->manager_id,
            'name' => 'Diana',
            'email' => 'diana@dozee.com',
            'username' => 'diana_dozee',
            'password' => Hash::make('Dian@1234'),
            'address' => 'No. 45, Jalan Bunga, 51000 Kuala Lumpur',
        ]);

        // 3. Create Categories
        $catDetergent = Category::create(['name' => 'Detergent']);
        $catFabric = Category::create(['name' => 'Fabric Care']);
        $catAccessories = Category::create(['name' => 'Laundry Accessories']);

        // 4. Create Products
        $productsData = [
            // Detergent
            ['item_name' => 'Do\'Zee Ultra White', 'volume' => '10KG', 'price' => 45.00, 'category' => 'Detergent', 'category_id' => $catDetergent->id, 'description' => 'Ultra White detergent designed for effective cleaning.'],
            ['item_name' => 'Do\'Zee Red Sporty', 'volume' => '10KG', 'price' => 45.00, 'category' => 'Detergent', 'category_id' => $catDetergent->id, 'description' => 'Red Sporty detergent for tough sports stains.'],
            ['item_name' => 'Do\'Zee Blue Caring', 'volume' => '10KG', 'price' => 45.00, 'category' => 'Detergent', 'category_id' => $catDetergent->id, 'description' => 'Blue Caring detergent gentle on fabric.'],
            ['item_name' => 'Do\'Zee Pink Soft', 'volume' => '10KG', 'price' => 45.00, 'category' => 'Detergent', 'category_id' => $catDetergent->id, 'description' => 'Pink Soft detergent with built-in softener.'],
            ['item_name' => 'Do\'Zee Apple Fresh', 'volume' => '10KG', 'price' => 45.00, 'category' => 'Detergent', 'category_id' => $catDetergent->id, 'description' => 'Apple Fresh detergent with fresh aroma.'],
            
            // Fabric Care
            ['item_name' => 'Do\'Zee Aroma Fabric Care Pink', 'volume' => '25KG', 'price' => 85.00, 'category' => 'Fabric Care', 'category_id' => $catFabric->id, 'description' => 'Fabric softener that keeps clothes soft.'],
            ['item_name' => 'Do\'Zee Aroma Fabric Care Blue', 'volume' => '25KG', 'price' => 85.00, 'category' => 'Fabric Care', 'category_id' => $catFabric->id, 'description' => 'Fabric softener for long-lasting scent.'],
            
            // Laundry Accessories
            ['item_name' => 'Do\'Zee Laundry Net', 'volume' => 'Standard', 'price' => 15.00, 'category' => 'Laundry Accessories', 'category_id' => $catAccessories->id, 'description' => 'High quality laundry net for delicate clothes.'],
            ['item_name' => 'Do\'Zee Measuring Cup', 'volume' => 'Standard', 'price' => 5.00, 'category' => 'Laundry Accessories', 'category_id' => $catAccessories->id, 'description' => 'Standard measuring cup for precise detergent usage.'],
            ['item_name' => 'Do\'Zee Stain Remover Pen', 'volume' => 'Standard', 'price' => 12.00, 'category' => 'Laundry Accessories', 'category_id' => $catAccessories->id, 'description' => 'Instant stain remover pen for on-the-go.'],
        ];

        $products = [];
        foreach ($productsData as $pData) {
            $pData['stock_qty'] = 500; // Start with high stock
            $products[$pData['item_name'] . '_' . $pData['volume']] = Product::create($pData);
        }

        // Retrieve created product models for pattern creation
        $ultraWhite10 = $products['Do\'Zee Ultra White_10KG'];
        $fabricCarePink = $products['Do\'Zee Aroma Fabric Care Pink_25KG'];
        $laundryNet = $products['Do\'Zee Laundry Net_Standard'];
        $appleFresh = $products['Do\'Zee Apple Fresh_10KG'];
        $stainRemover = $products['Do\'Zee Stain Remover Pen_Standard'];

        // 5. Seed sales transactions to establish Apriori buying patterns and metrics
        // We want multiple multi-item transactions to trigger Apriori rule generation:
        
        // Salesmen: Alya (transactions in last 60 days)
        // Pattern 1: Ultra White 10KG + Aroma Fabric Care Pink 25KG (15 times) - Approved
        for ($i = 0; $i < 15; $i++) {
            $this->createTransaction($salesmenAlya->salesmen_id, [$ultraWhite10, $fabricCarePink], now()->subDays(rand(1, 58)), 'Approved');
        }
        // Pattern 2: Apple Fresh 10KG + Stain Remover Pen (12 times) - Approved
        for ($i = 0; $i < 12; $i++) {
            $this->createTransaction($salesmenAlya->salesmen_id, [$appleFresh, $stainRemover], now()->subDays(rand(1, 58)), 'Approved');
        }

        // Salesmen: Diana
        // Pattern 1: Ultra White 10KG + Aroma Fabric Care Pink 25KG (10 times) - Approved
        for ($i = 0; $i < 10; $i++) {
            $this->createTransaction($salesmenDiana->salesmen_id, [$ultraWhite10, $fabricCarePink], now()->subDays(rand(1, 58)), 'Approved');
        }

        // Noise/Random transactions (single items and mixed items)
        $allProdList = array_values($products);
        foreach ([$salesmenAlya, $salesmenDiana] as $salesmen) {
            for ($i = 0; $i < 20; $i++) {
                $randomProds = collect($allProdList)->random(rand(1, 3))->all();
                $this->createTransaction($salesmen->salesmen_id, $randomProds, now()->subDays(rand(1, 58)), 'Approved');
            }
        }

        // PENDING TRANSACTIONS (For Manager Approval Verification)
        // These are recent transactions that haven't been approved yet, with different events
        $events = ['Weekend Roadshow', 'Mall Activation', 'Online Flash Sale', 'Corporate Outreach'];
        for ($i = 0; $i < 10; $i++) {
            $randomProds = collect($allProdList)->random(rand(1, 3))->all();
            $randomEvent = $events[array_rand($events)];
            $this->createTransaction($salesmenAlya->salesmen_id, $randomProds, now()->subDays(rand(1, 10)), 'Pending', $randomEvent);
        }
        for ($i = 0; $i < 5; $i++) {
            $randomProds = collect($allProdList)->random(rand(1, 3))->all();
            $randomEvent = $events[array_rand($events)];
            $this->createTransaction($salesmenDiana->salesmen_id, $randomProds, now()->subDays(rand(1, 10)), 'Pending', $randomEvent);
        }

        // 6. Run Apriori Analysis to generate the association rules in DB
        $apriori = new AprioriService(0.05, 0.3); // 5% support, 30% confidence
        $aprioriResults = $apriori->run();

        // 7. Seed promotions using the generated rules
        // Let's grab generated rules from the DB
        $rules = AprioriAnalysis::all();

        $rule1 = $rules->first();
        $rule2 = $rules->skip(1)->first();

        // Promotion 1: Single item promo
        $promoSingle = Promotion::create([
            'manager_id' => $manager->manager_id,
            'promo_name' => 'Ultra White Solo Special',
            'description' => '10% discount on Do\'Zee Ultra White 10KG laundry detergent.',
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Active',
        ]);

        // Promotion 2: Bundle Combo based on rule 1 (Ultra White + Fabric Care Pink)
        $promoCombo1 = Promotion::create([
            'manager_id' => $manager->manager_id,
            'rule_id' => $rule1 ? $rule1->rule_id : null,
            'promo_name' => 'Aroma Laundry Combo',
            'description' => 'Ultimate Laundry Combo: Do\'Zee Ultra White 10KG & Do\'Zee Aroma Fabric Care Pink 25KG.',
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Active',
        ]);
        if ($rule1) {
            $promoCombo1->associationRules()->sync([$rule1->rule_id]);
        }

        // Promotion 3: Bundle Combo based on rule 2 (Apple Fresh + Stain Remover Pen)
        $promoCombo2 = Promotion::create([
            'manager_id' => $manager->manager_id,
            'rule_id' => $rule2 ? $rule2->rule_id : null,
            'promo_name' => 'Fresh & Clean Combo',
            'description' => 'Keep your clothes spotless: Do\'Zee Apple Fresh & Do\'Zee Stain Remover Pen Bundle.',
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'Active',
        ]);
        if ($rule2) {
            $promoCombo2->associationRules()->sync([$rule2->rule_id]);
        }

        // Promotion 4: Pending Salesmen proposal
        Promotion::create([
            'salesmen_id' => $salesmenAlya->salesmen_id,
            'promo_name' => 'Alya Care Proposal',
            'description' => 'Proposed bundle deal for Do\'Zee Blue Caring + Do\'Zee Laundry Net.',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(15)->format('Y-m-d'),
            'status' => 'Pending',
        ]);

        // 8. Seed some sales that explicitly use the promotions to populate performance charts
        // Transaction with single item promo
        $this->createPromoTransaction($salesmenAlya->salesmen_id, $ultraWhite10, $promoSingle, 2, now()->subDays(5), 'Approved');
        $this->createPromoTransaction($salesmenDiana->salesmen_id, $ultraWhite10, $promoSingle, 1, now()->subDays(3), 'Approved');
        // Pending promo transaction
        $this->createPromoTransaction($salesmenAlya->salesmen_id, $ultraWhite10, $promoSingle, 1, now(), 'Pending');

        // Transactions with combo promos
        if ($rule1) {
            $this->createBundleTransaction($salesmenAlya->salesmen_id, [$ultraWhite10, $fabricCarePink], $promoCombo1, now()->subDays(4), 'Approved');
            $this->createBundleTransaction($salesmenDiana->salesmen_id, [$ultraWhite10, $fabricCarePink], $promoCombo1, now()->subDays(2), 'Approved');
        }
        if ($rule2) {
            $this->createBundleTransaction($salesmenAlya->salesmen_id, [$appleFresh, $stainRemover], $promoCombo2, now()->subDays(6), 'Approved');
        }

        $this->call(DetergentProductSeeder::class);
    }

    private function createTransaction(int $salesmenId, array $products, $date, $status = 'Pending', $eventName = null): void
    {
        $sale = Sale::create([
            'salesmen_id' => $salesmenId,
            'total_amount' => 0,
            'sale_date' => $date,
            'status' => $status,
            'event_name' => $eventName,
        ]);

        $total = 0;
        foreach ($products as $product) {
            $qty = rand(1, 3);
            
            // Create sale item
            $sale->saleItems()->create([
                'item_id' => $product->item_id,
                'quantity' => $qty,
            ]);

            // Decrement product stock
            $product->decrement('stock_qty', $qty);

            $total += $product->price * $qty;
        }

        $sale->update(['total_amount' => $total]);
    }

    private function createPromoTransaction(int $salesmenId, Product $product, Promotion $promo, int $qty, $date, $status = 'Pending'): void
    {
        $sale = Sale::create([
            'salesmen_id' => $salesmenId,
            'total_amount' => 0,
            'sale_date' => $date,
            'status' => $status,
        ]);

        $sale->saleItems()->create([
            'item_id' => $product->item_id,
            'promo_id' => $promo->promo_id,
            'quantity' => $qty,
        ]);

        $product->decrement('stock_qty', $qty);

        // Apply 10% discount for single promo
        $discountedPrice = $product->price * 0.9; 
        $sale->update(['total_amount' => $discountedPrice * $qty]);
    }

    private function createBundleTransaction(int $salesmenId, array $products, Promotion $promo, $date, $status = 'Pending'): void
    {
        $sale = Sale::create([
            'salesmen_id' => $salesmenId,
            'total_amount' => 0,
            'sale_date' => $date,
            'status' => $status,
        ]);

        $total = 0;
        foreach ($products as $product) {
            $qty = 1;
            
            $sale->saleItems()->create([
                'item_id' => $product->item_id,
                'promo_id' => $promo->promo_id,
                'quantity' => $qty,
            ]);

            $product->decrement('stock_qty', $qty);

            // Combo price: 10% off
            $total += $product->price * 0.9 * $qty;
        }

        $sale->update(['total_amount' => $total]);
    }
}
