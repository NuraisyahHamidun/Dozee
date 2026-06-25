<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Models\Manager;
use App\Models\Salesman;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\AprioriAnalysis;
use App\Services\AprioriService;

/**
 * DoZeeTestDataSeeder
 * ─────────────────────────────────────────────────────────────────────────────
 * Generates a complete, self-consistent test dataset for the Do'Zee Laundry
 * system that covers every Step 1–6 function:
 *
 *   Step 1  – Products & Categories
 *   Step 2  – Promotions (single-item, bundle, pending salesman proposal)
 *   Step 3  – Sales transactions (approved / pending / rejected)
 *   Step 4  – Approval workflow   (pending transactions for manager to act on)
 *   Step 5  – Apriori rules       (pre-computed + re-runnable via AprioriService)
 *   Step 6  – Dashboard & Reports (rich daily/monthly spread across 6 months)
 *
 * Run with:
 *   php artisan db:seed --class=DoZeeTestDataSeeder
 */
class DoZeeTestDataSeeder extends Seeder
{
    // ── tuneable constants ────────────────────────────────────────────────────
    private const APPROVED_PATTERN_REPS  = 18; // how many times each strong pair repeats
    private const RANDOM_NOISE_PER_USER  = 25; // random single/multi-item sales per salesman
    private const PENDING_COUNT          = 8;  // pending-approval sales to seed
    private const REJECTED_COUNT         = 4;  // rejected sales to seed
    private const HISTORY_DAYS          = 180; // look-back window for approved sales

    // ── helpers ───────────────────────────────────────────────────────────────

    private function randDate(int $daysBack): Carbon
    {
        return Carbon::now()->subDays(rand(1, $daysBack));
    }

    /**
     * Build one approved sale transaction.
     * Stock is decremented and status set to 'Approved'.
     */
    private function makeSale(
        int      $salesmanId,
        array    $products,        // array of Product models
        Carbon   $date,
        string   $status = 'Approved',
        ?int     $promoId = null,
        ?string  $eventName = null
    ): Sale {
        $sale = Sale::create([
            'salesman_id' => $salesmanId,
            'event_name'  => $eventName,
            'total_amount' => 0,
            'sale_date'   => $date,
            'status'      => $status,
            'ante_create' => $date,
            'date_modifier' => $status !== 'Pending' ? $date->copy()->addMinutes(rand(5, 60)) : null,
            'date_verify'  => $status === 'Approved' ? $date->copy()->addMinutes(rand(61, 120)) : null,
        ]);

        $total = 0;
        foreach ($products as [$product, $qty]) {
            $sale->saleItems()->create([
                'item_id'  => $product->item_id,
                'promo_id' => $promoId,
                'quantity' => $qty,
            ]);

            // Only decrement stock for approved sales (pending/rejected don't consume stock)
            if ($status === 'Approved') {
                $product->decrement('stock_qty', $qty);
            }

            $total += $product->price * $qty;
        }

        $sale->update(['total_amount' => round($total, 2)]);
        return $sale;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MAIN RUN
    // ─────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Wipe in dependency order
        DB::table('promotion_association_rule')->truncate();
        SaleItem::truncate();
        Sale::truncate();
        Promotion::truncate();
        AprioriAnalysis::truncate();
        Product::truncate();
        Category::truncate();
        Salesman::truncate();
        Manager::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $manager = Manager::create([
            'name'       => 'Nur Aisyah Siti',
            'email'      => 'nuraisyahsiti793@gmail.com',
            'username'   => 'nuraisyah_793',
            'password'   => Hash::make('Nurisy@22'),
            'address'    => 'No. 12, Jalan Maju, 50480 Kuala Lumpur',
        ]);

        $alya = Salesman::create([
            'manager_id' => $manager->manager_id,
            'name'       => 'Alya',
            'email'      => 'alya@dozee.com',
            'username'   => 'alya_dozee',
            'password'   => Hash::make('Aly@1234'),
            'address'    => 'No. 23, Jalan Merdeka, 50000 Kuala Lumpur',
        ]);

        $diana = Salesman::create([
            'manager_id' => $manager->manager_id,
            'name'       => 'Diana',
            'email'      => 'diana@dozee.com',
            'username'   => 'diana_dozee',
            'password'   => Hash::make('Dian@1234'),
            'address'    => 'No. 45, Jalan Bunga, 51000 Kuala Lumpur',
        ]);

        $salesmen = [$alya, $diana];

        // ── 2. CATEGORIES ────────────────────────────────────────────────────
        $catDetergent  = Category::create(['name' => 'Laundry Detergent']);
        $catFabric     = Category::create(['name' => 'Fabric Care']);
        $catAccessory  = Category::create(['name' => 'Laundry Accessories']);
        $catHousehold  = Category::create(['name' => 'Household Cleaning']);

        // ── 3. PRODUCTS (Do'Zee Detergent range) ─────────────────────────────
        $productDefs = [
            // ── Laundry Detergent ──────────────────────────────────────────
            [
                'item_name'   => 'Ultra White',
                'volume'      => '10KG',
                'price'       => 45.00,
                'stock_qty'   => 600,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Ultra White 10KG – deep-clean formula that brightens whites and removes tough stains.',
            ],
            [
                'item_name'   => 'Ultra White',
                'volume'      => '4KG',
                'price'       => 18.00,
                'stock_qty'   => 500,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Ultra White 4KG – compact size ideal for households.',
            ],
            [
                'item_name'   => 'Red Sporty',
                'volume'      => '10KG',
                'price'       => 45.00,
                'stock_qty'   => 500,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Red Sporty 10KG – enzyme-powered formula for sports and active wear.',
            ],
            [
                'item_name'   => 'Blue Caring',
                'volume'      => '10KG',
                'price'       => 45.00,
                'stock_qty'   => 500,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Blue Caring 10KG – gentle formula safe for coloured and delicate fabrics.',
            ],
            [
                'item_name'   => 'Pink Soft',
                'volume'      => '10KG',
                'price'       => 45.00,
                'stock_qty'   => 500,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Pink Soft 10KG – built-in fabric conditioner for extra softness.',
            ],
            [
                'item_name'   => 'Apple Fresh',
                'volume'      => '10KG',
                'price'       => 45.00,
                'stock_qty'   => 500,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Apple Fresh 10KG – refreshing green apple fragrance that lasts all day.',
            ],
            [
                'item_name'   => 'Lemon Clean',
                'volume'      => '10KG',
                'price'       => 45.00,
                'stock_qty'   => 400,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Lemon Clean 10KG – citrus-infused detergent with anti-bacterial action.',
            ],
            [
                'item_name'   => 'Aroma Rose',
                'volume'      => '10KG',
                'price'       => 45.00,
                'stock_qty'   => 400,
                'category_id' => $catDetergent->id,
                'category'    => 'Laundry Detergent',
                'description' => 'Aroma Rose 10KG – floral rose scent for a luxurious laundry experience.',
            ],
            // ── Fabric Care ────────────────────────────────────────────────
            [
                'item_name'   => 'Aroma Fabric Care Pink',
                'volume'      => '25KG',
                'price'       => 85.00,
                'stock_qty'   => 350,
                'category_id' => $catFabric->id,
                'category'    => 'Fabric Care',
                'description' => 'Aroma Fabric Care Pink 25KG – softener that keeps clothes irresistibly soft.',
            ],
            [
                'item_name'   => 'Aroma Fabric Care Blue',
                'volume'      => '25KG',
                'price'       => 85.00,
                'stock_qty'   => 300,
                'category_id' => $catFabric->id,
                'category'    => 'Fabric Care',
                'description' => 'Aroma Fabric Care Blue 25KG – long-lasting ocean-breeze fragrance softener.',
            ],
            // ── Laundry Accessories ────────────────────────────────────────
            [
                'item_name'   => 'Fabric Stain Remover',
                'volume'      => 'Standard',
                'price'       => 12.00,
                'stock_qty'   => 400,
                'category_id' => $catAccessory->id,
                'category'    => 'Laundry Accessories',
                'description' => 'Fabric Stain Remover – spray-on pre-treatment for stubborn stains.',
            ],
            [
                'item_name'   => 'Laundry Net Bag',
                'volume'      => 'Standard',
                'price'       => 8.00,
                'stock_qty'   => 300,
                'category_id' => $catAccessory->id,
                'category'    => 'Laundry Accessories',
                'description' => 'Laundry Net Bag – protects delicate garments during machine wash.',
            ],
            // ── Household Cleaning ─────────────────────────────────────────
            [
                'item_name'   => 'Dish Wash',
                'volume'      => 'Standard',
                'price'       => 5.00,
                'stock_qty'   => 500,
                'category_id' => $catHousehold->id,
                'category'    => 'Household Cleaning',
                'description' => 'Dish Wash – cuts through grease, leaves dishes sparkling clean.',
            ],
            [
                'item_name'   => 'Floor Cleaner',
                'volume'      => 'Standard',
                'price'       => 12.00,
                'stock_qty'   => 450,
                'category_id' => $catHousehold->id,
                'category'    => 'Household Cleaning',
                'description' => 'Floor Cleaner – pine-fresh fragrance floor cleaning solution.',
            ],
            [
                'item_name'   => 'Bleach',
                'volume'      => 'Standard',
                'price'       => 7.00,
                'stock_qty'   => 400,
                'category_id' => $catHousehold->id,
                'category'    => 'Household Cleaning',
                'description' => 'Bleach – whitens, disinfects, and removes mould stains.',
            ],
        ];

        $products = [];
        foreach ($productDefs as $def) {
            $p = Product::create($def);
            $products[$def['item_name'] . '_' . $def['volume']] = $p;
        }

        // Handy aliases
        $ultraWhite10  = $products['Ultra White_10KG'];
        $ultraWhite4   = $products['Ultra White_4KG'];
        $redSporty     = $products['Red Sporty_10KG'];
        $blueCaring    = $products['Blue Caring_10KG'];
        $pinkSoft      = $products['Pink Soft_10KG'];
        $appleFresh    = $products['Apple Fresh_10KG'];
        $lemonClean    = $products['Lemon Clean_10KG'];
        $aromaRose     = $products['Aroma Rose_10KG'];
        $fabricPink    = $products['Aroma Fabric Care Pink_25KG'];
        $fabricBlue    = $products['Aroma Fabric Care Blue_25KG'];
        $stainRemover  = $products['Fabric Stain Remover_Standard'];
        $netBag        = $products['Laundry Net Bag_Standard'];
        $dishWash      = $products['Dish Wash_Standard'];
        $floorCleaner  = $products['Floor Cleaner_Standard'];
        $bleach        = $products['Bleach_Standard'];

        $allProducts = array_values($products);

        // ── 4. APPROVED SALES (historical – populates all dashboard charts) ──
        //
        // We seed three strong Apriori patterns + noise so the AprioriService
        // can always find rules above min-support/confidence.
        //
        // Pattern A: Ultra White 10KG + Aroma Fabric Care Pink 25KG
        // Pattern B: Dish Wash + Floor Cleaner
        // Pattern C: Blue Caring 10KG + Fabric Stain Remover (3-item with Bleach)

        // -- Pattern A (Alya) --
        for ($i = 0; $i < self::APPROVED_PATTERN_REPS; $i++) {
            $qty1 = rand(1, 3);
            $qty2 = rand(1, 2);
            $this->makeSale($alya->salesman_id, [
                [$ultraWhite10, $qty1],
                [$fabricPink,   $qty2],
            ], $this->randDate(self::HISTORY_DAYS));
        }

        // -- Pattern A (Diana) --
        for ($i = 0; $i < (int)(self::APPROVED_PATTERN_REPS * 0.7); $i++) {
            $this->makeSale($diana->salesman_id, [
                [$ultraWhite10, rand(1, 3)],
                [$fabricPink,   rand(1, 2)],
            ], $this->randDate(self::HISTORY_DAYS));
        }

        // -- Pattern B (both) --
        for ($i = 0; $i < self::APPROVED_PATTERN_REPS; $i++) {
            $salesman = $salesmen[array_rand($salesmen)];
            $this->makeSale($salesman->salesman_id, [
                [$dishWash,    rand(1, 4)],
                [$floorCleaner, rand(1, 2)],
            ], $this->randDate(self::HISTORY_DAYS));
        }

        // -- Pattern C: Blue Caring + Stain Remover + Bleach (3-item bundle) --
        for ($i = 0; $i < (int)(self::APPROVED_PATTERN_REPS * 0.8); $i++) {
            $salesman = $salesmen[array_rand($salesmen)];
            $this->makeSale($salesman->salesman_id, [
                [$blueCaring,   rand(1, 2)],
                [$stainRemover, rand(1, 3)],
                [$bleach,       rand(1, 2)],
            ], $this->randDate(self::HISTORY_DAYS));
        }

        // -- Extra single/mixed noise --
        foreach ($salesmen as $salesman) {
            for ($i = 0; $i < self::RANDOM_NOISE_PER_USER; $i++) {
                $pick  = collect($allProducts)->random(rand(1, 3))->all();
                $items = array_map(fn($p) => [$p, rand(1, 3)], $pick);
                $this->makeSale($salesman->salesman_id, $items, $this->randDate(self::HISTORY_DAYS));
            }
        }

        // -- Event-named sales (tests event_name filtering) --
        $events = ['Raya Promotion', 'Year-End Sale', 'Mid-Year Clearance'];
        foreach ($events as $eventName) {
            foreach ($salesmen as $salesman) {
                for ($i = 0; $i < 5; $i++) {
                    $this->makeSale($salesman->salesman_id, [
                        [$ultraWhite10, rand(1, 2)],
                        [$pinkSoft,     rand(1, 2)],
                    ], $this->randDate(60), 'Approved', null, $eventName);
                }
            }
        }

        // ── 5. RUN APRIORI to generate association rules ──────────────────────
        $apriori = new AprioriService(0.05, 0.3); // 5% support, 30% confidence
        $apriori->run();

        // Load generated rules
        $rules    = AprioriAnalysis::all();
        $rule1    = $rules->shift();   // strongest rule (Ultra White → Fabric Care Pink)
        $rule2    = $rules->shift();   // second rule
        $rule3    = $rules->shift();   // third rule (may be null)

        // ── 6. PROMOTIONS ─────────────────────────────────────────────────────

        // Promo 1 – Single-item: 10% off Ultra White 10KG
        $promoUltraWhite = Promotion::create([
            'manager_id'  => $manager->manager_id,
            'promo_name'  => 'Ultra White Solo Special',
            'description' => 'RM5.00 off per pack of Ultra White 10KG. Perfect for bulk buyers.',
            'start_date'  => Carbon::now()->subDays(15)->format('Y-m-d'),
            'end_date'    => Carbon::now()->addDays(30)->format('Y-m-d'),
            'status'      => 'Active',
        ]);

        // Promo 2 – Single-item: Discount on Fabric Care Blue
        $promoFabricBlue = Promotion::create([
            'manager_id'  => $manager->manager_id,
            'promo_name'  => 'Fabric Softener Flash Deal',
            'description' => 'RM8.00 discount on Aroma Fabric Care Blue 25KG. Limited time.',
            'start_date'  => Carbon::now()->subDays(5)->format('Y-m-d'),
            'end_date'    => Carbon::now()->addDays(10)->format('Y-m-d'),
            'status'      => 'Active',
        ]);

        // Promo 3 – Bundle: Aroma Laundry Combo (rule 1 → Ultra White + Fabric Care Pink)
        $promoAromaCombo = Promotion::create([
            'manager_id'  => $manager->manager_id,
            'rule_id'     => $rule1?->rule_id,
            'promo_name'  => 'Aroma Laundry Combo',
            'description' => 'Bundle deal: Ultra White 10KG + Aroma Fabric Care Pink 25KG at 10% off total.',
            'start_date'  => Carbon::now()->subDays(10)->format('Y-m-d'),
            'end_date'    => Carbon::now()->addDays(30)->format('Y-m-d'),
            'status'      => 'Active',
        ]);
        if ($rule1) {
            $promoAromaCombo->associationRules()->sync([$rule1->rule_id]);
        }

        // Promo 4 – Bundle: Clean & Fresh House Pack (Dish Wash + Floor Cleaner)
        $promoHousePack = Promotion::create([
            'manager_id'  => $manager->manager_id,
            'rule_id'     => $rule2?->rule_id,
            'promo_name'  => 'Clean & Fresh House Pack',
            'description' => 'Household combo: Dish Wash + Floor Cleaner — buy together and save RM3.',
            'start_date'  => Carbon::now()->subDays(10)->format('Y-m-d'),
            'end_date'    => Carbon::now()->addDays(25)->format('Y-m-d'),
            'status'      => 'Active',
        ]);
        if ($rule2) {
            $promoHousePack->associationRules()->sync([$rule2->rule_id]);
        }

        // Promo 5 – Bundle: 3-product Deep Clean Kit (rule 3)
        $promoDeepClean = Promotion::create([
            'manager_id'  => $manager->manager_id,
            'rule_id'     => $rule3?->rule_id,
            'promo_name'  => 'Deep Clean Kit Bundle',
            'description' => 'Triple combo: Blue Caring 10KG + Stain Remover + Bleach — best value!',
            'start_date'  => Carbon::now()->subDays(7)->format('Y-m-d'),
            'end_date'    => Carbon::now()->addDays(14)->format('Y-m-d'),
            'status'      => 'Active',
        ]);
        if ($rule3) {
            $promoDeepClean->associationRules()->sync([$rule3->rule_id]);
        }

        // Promo 6 – Expired promotion (for report coverage)
        Promotion::create([
            'manager_id'  => $manager->manager_id,
            'promo_name'  => 'Hari Raya Special',
            'description' => 'Seasonal Raya promotion on all Laundry Detergent products.',
            'start_date'  => Carbon::now()->subDays(60)->format('Y-m-d'),
            'end_date'    => Carbon::now()->subDays(5)->format('Y-m-d'),
            'status'      => 'Expired',
        ]);

        // Promo 7 – Pending salesman proposal (tests workflow Step 4)
        Promotion::create([
            'salesman_id' => $alya->salesman_id,
            'promo_name'  => 'Alya – Sporty Starter Pack',
            'description' => 'Proposed: Red Sporty 10KG + Laundry Net Bag bundle deal.',
            'start_date'  => Carbon::now()->format('Y-m-d'),
            'end_date'    => Carbon::now()->addDays(20)->format('Y-m-d'),
            'status'      => 'Pending',
        ]);

        // Promo 8 – Pending salesman proposal (second for manager queue)
        Promotion::create([
            'salesman_id' => $diana->salesman_id,
            'promo_name'  => 'Diana – Fabric Care Duo',
            'description' => 'Proposed: Aroma Fabric Care Pink + Blue 25KG combo for laundry businesses.',
            'start_date'  => Carbon::now()->format('Y-m-d'),
            'end_date'    => Carbon::now()->addDays(15)->format('Y-m-d'),
            'status'      => 'Pending',
        ]);

        // Promo 9 – Rejected salesman proposal
        Promotion::create([
            'salesman_id' => $alya->salesman_id,
            'promo_name'  => 'Alya – Bleach Mega Sale',
            'description' => 'Proposed 50% off Bleach (rejected: margin too thin).',
            'start_date'  => Carbon::now()->subDays(30)->format('Y-m-d'),
            'end_date'    => Carbon::now()->subDays(10)->format('Y-m-d'),
            'status'      => 'Rejected',
        ]);

        // ── 7. PROMO-LINKED APPROVED SALES ────────────────────────────────────
        // These populate Promotion Performance chart and single-vs-combo donut.

        // Single-item promo sales (Ultra White)
        for ($i = 0; $i < 8; $i++) {
            $salesman = $salesmen[array_rand($salesmen)];
            $this->makeSale($salesman->salesman_id, [
                [$ultraWhite10, rand(1, 2)],
            ], $this->randDate(30), 'Approved', $promoUltraWhite->promo_id);
        }

        // Single-item promo sales (Fabric Blue)
        for ($i = 0; $i < 5; $i++) {
            $this->makeSale($alya->salesman_id, [
                [$fabricBlue, 1],
            ], $this->randDate(10), 'Approved', $promoFabricBlue->promo_id);
        }

        // Bundle promo: Aroma Laundry Combo
        for ($i = 0; $i < 10; $i++) {
            $salesman = $salesmen[array_rand($salesmen)];
            $this->makeSale($salesman->salesman_id, [
                [$ultraWhite10, rand(1, 2)],
                [$fabricPink,   1],
            ], $this->randDate(30), 'Approved', $promoAromaCombo->promo_id);
        }

        // Bundle promo: House Pack
        for ($i = 0; $i < 8; $i++) {
            $salesman = $salesmen[array_rand($salesmen)];
            $this->makeSale($salesman->salesman_id, [
                [$dishWash,     rand(1, 3)],
                [$floorCleaner, rand(1, 2)],
            ], $this->randDate(25), 'Approved', $promoHousePack->promo_id);
        }

        // Bundle promo: Deep Clean Kit
        for ($i = 0; $i < 6; $i++) {
            $salesman = $salesmen[array_rand($salesmen)];
            $this->makeSale($salesman->salesman_id, [
                [$blueCaring,   rand(1, 2)],
                [$stainRemover, rand(1, 2)],
                [$bleach,       1],
            ], $this->randDate(14), 'Approved', $promoDeepClean->promo_id);
        }

        // ── 8. PENDING TRANSACTIONS (for approval workflow testing) ───────────
        // These transactions are created by Alya and require Manager approval.
        $pendingProducts = [
            [[$ultraWhite10, 2]],
            [[$ultraWhite10, 1], [$fabricPink, 1]],
            [[$redSporty, 3]],
            [[$dishWash, 2], [$floorCleaner, 1]],
            [[$pinkSoft, 1], [$netBag, 2]],
            [[$lemonClean, 2]],
            [[$aromaRose, 1], [$stainRemover, 2]],
            [[$blueCaring, 1], [$bleach, 1]],
        ];

        foreach (array_slice($pendingProducts, 0, self::PENDING_COUNT) as $items) {
            Sale::create([
                'salesman_id'  => $alya->salesman_id,
                'event_name'   => null,
                'total_amount' => array_sum(array_map(fn($i) => $i[0]->price * $i[1], $items)),
                'sale_date'    => Carbon::now()->subDays(rand(1, 3)),
                'status'       => 'Pending',
                'ante_create'  => Carbon::now()->subDays(rand(1, 3)),
                'date_modifier' => null,
                'date_verify'  => null,
            ])->each(function ($sale) use ($items) {
                foreach ($items as [$product, $qty]) {
                    $sale->saleItems()->create([
                        'item_id'  => $product->item_id,
                        'promo_id' => null,
                        'quantity' => $qty,
                    ]);
                }
            });
        }

        // A few pending transactions from Diana as well
        $dianaPending = [
            [[$appleFresh, 2], [$fabricBlue, 1]],
            [[$ultraWhite4, 3]],
        ];
        foreach ($dianaPending as $items) {
            Sale::create([
                'salesman_id'  => $diana->salesman_id,
                'event_name'   => null,
                'total_amount' => array_sum(array_map(fn($i) => $i[0]->price * $i[1], $items)),
                'sale_date'    => Carbon::now()->subDays(rand(1, 2)),
                'status'       => 'Pending',
                'ante_create'  => Carbon::now()->subDays(rand(1, 2)),
                'date_modifier' => null,
                'date_verify'  => null,
            ])->each(function ($sale) use ($items) {
                foreach ($items as [$product, $qty]) {
                    $sale->saleItems()->create([
                        'item_id'  => $product->item_id,
                        'promo_id' => null,
                        'quantity' => $qty,
                    ]);
                }
            });
        }

        // ── 9. REJECTED TRANSACTIONS (audit trail) ───────────────────────────
        $rejectedProducts = [
            [[$bleach, 10]],   // unreasonably large quantity – rejected
            [[$dishWash, 5], [$floorCleaner, 5]],
            [[$ultraWhite10, 4]],
            [[$fabricPink, 2], [$fabricBlue, 2]],
        ];

        foreach (array_slice($rejectedProducts, 0, self::REJECTED_COUNT) as $items) {
            $date = Carbon::now()->subDays(rand(7, 30));
            Sale::create([
                'salesman_id'   => $alya->salesman_id,
                'event_name'    => null,
                'total_amount'  => array_sum(array_map(fn($i) => $i[0]->price * $i[1], $items)),
                'sale_date'     => $date,
                'status'        => 'Rejected',
                'ante_create'   => $date,
                'date_modifier' => $date->copy()->addHours(rand(1, 5)),
                'date_verify'   => null,
            ])->each(function ($sale) use ($items) {
                foreach ($items as [$product, $qty]) {
                    $sale->saleItems()->create([
                        'item_id'  => $product->item_id,
                        'promo_id' => null,
                        'quantity' => $qty,
                    ]);
                }
            });
        }

        // ── 10. OUTPUT SUMMARY ────────────────────────────────────────────────
        $totalSales    = Sale::count();
        $pendingCount  = Sale::where('status', 'Pending')->count();
        $approvedCount = Sale::where('status', 'Approved')->count();
        $rejectedCount = Sale::where('status', 'Rejected')->count();
        $rulesCount    = AprioriAnalysis::count();

        $this->command->info('');
        $this->command->info('✅  Do\'Zee Test Data Seeded Successfully');
        $this->command->info('──────────────────────────────────────────');
        $this->command->info("  Manager  : nuraisyahsiti793@gmail.com  / Nurisy@22");
        $this->command->info("  Salesman : alya@dozee.com              / Aly@1234");
        $this->command->info("  Salesman : diana@dozee.com             / Dian@1234");
        $this->command->info('──────────────────────────────────────────');
        $this->command->info("  Products     : " . Product::count());
        $this->command->info("  Categories   : " . Category::count());
        $this->command->info("  Promotions   : " . Promotion::count());
        $this->command->info("  Sales Total  : {$totalSales}");
        $this->command->info("    ↳ Approved : {$approvedCount}");
        $this->command->info("    ↳ Pending  : {$pendingCount}  ← manager can approve/reject");
        $this->command->info("    ↳ Rejected : {$rejectedCount}");
        $this->command->info("  Apriori Rules: {$rulesCount}");
        $this->command->info('──────────────────────────────────────────');
        $this->command->info('  All Step 1–6 functions are ready to test.');
        $this->command->info('');
    }
}
