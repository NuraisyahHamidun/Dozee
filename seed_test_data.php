<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// ============================================================
// STEP 0: Clean up previous test data from previous sessions
// ============================================================
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('association_rules')->truncate();
DB::table('transaction_details')->truncate();
DB::table('sales_transactions')->truncate();
DB::table('promotions')->truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

// ============================================================
// STEP 1: Ensure manager exists
// ============================================================
DB::table('managers')->updateOrInsert(
    ['email' => 'nuraisyahsiti793@gmail.com'],
    [
        'name'       => 'Nur Aisyah',
        'username'   => 'nuraisyah',
        'password'   => Hash::make('Nurisy@22'),
        'address'    => 'Kuala Lumpur',
        'created_at' => now(),
        'updated_at' => now(),
    ]
);
$manager = DB::table('managers')->where('email', 'nuraisyahsiti793@gmail.com')->first();
$managerId = $manager->manager_id;
echo "Manager ID: $managerId\n";

// ============================================================
// STEP 2: Create Salesmen
// ============================================================
$salesmen = [
    ['name' => 'Ahmad Faris', 'username' => 'ahmadfaris', 'email' => 'faris@dozee.com'],
    ['name' => 'Siti Nurul',  'username' => 'sitinurul',  'email' => 'nurul@dozee.com'],
];
$salesmanIds = [];
foreach ($salesmen as $s) {
    $existing = DB::table('salesmen')->where('email', $s['email'])->first();
    if (!$existing) {
        $id = DB::table('salesmen')->insertGetId([
            'manager_id'     => $managerId,
            'name'           => $s['name'],
            'username'       => $s['username'],
            'email'          => $s['email'],
            'password'       => Hash::make('password'),
            'address'        => 'Kuala Lumpur',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
        $salesmanIds[] = $id;
    } else {
        $salesmanIds[] = $existing->salesman_id;
    }
}
echo "Salesmen: " . implode(', ', $salesmanIds) . "\n";

// ============================================================
// STEP 3: Ensure categories & products exist
// ============================================================
$categories = ['Liquid Laundry Detergent', 'Powder Detergent', 'Fabric Softener', 'Bleach', 'Stain Remover'];
$catIds = [];
foreach ($categories as $cat) {
    $existing = DB::table('categories')->where('name', $cat)->first();
    if (!$existing) {
        $id = DB::table('categories')->insertGetId([
            'name'       => $cat,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        $id = $existing->id;
    }
    $catIds[$cat] = $id;
}

$products = [
    ['item_name' => 'Dynamo Liquid 1L',    'volume' => '1L',   'price' => 12.50, 'stock_qty' => 100, 'category' => 'Liquid Laundry Detergent'],
    ['item_name' => 'Dynamo Powder 1kg',   'volume' => '1kg',  'price' => 10.00, 'stock_qty' => 80,  'category' => 'Powder Detergent'],
    ['item_name' => 'Comfort Blue 1L',     'volume' => '1L',   'price' => 9.90,  'stock_qty' => 60,  'category' => 'Fabric Softener'],
    ['item_name' => 'Downy Fresh 900ml',   'volume' => '900ml','price' => 11.00, 'stock_qty' => 70,  'category' => 'Fabric Softener'],
    ['item_name' => 'Clorox Bleach 1L',    'volume' => '1L',   'price' => 7.50,  'stock_qty' => 50,  'category' => 'Bleach'],
    ['item_name' => 'Vanish Stain 500g',   'volume' => '500g', 'price' => 15.00, 'stock_qty' => 40,  'category' => 'Stain Remover'],
];
$productIds = [];
foreach ($products as $p) {
    $existing = DB::table('items')->where('item_name', $p['item_name'])->first();
    if (!$existing) {
        $id = DB::table('items')->insertGetId([
            'item_name'  => $p['item_name'],
            'volume'     => $p['volume'],
            'price'      => $p['price'],
            'stock_qty'  => $p['stock_qty'],
            'category'   => $p['category'],
            'category_id'=> $catIds[$p['category']] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        $id = $existing->item_id;
    }
    $productIds[$p['item_name']] = $id;
}
echo "Products: " . implode(', ', $productIds) . "\n";

// ============================================================
// STEP 4: Create 25 diverse sale transactions with overlapping items
// ============================================================
// Pattern 1 (very common): Dynamo Liquid + Comfort Blue  — 15 times
// Pattern 2 (common):      Dynamo Liquid + Downy Fresh   — 10 times
// Pattern 3 (moderate):    Dynamo Powder + Vanish Stain  — 8 times
// Pattern 4 (rare):        Clorox + Vanish               — 4 times

$dynamoLiquid = $productIds['Dynamo Liquid 1L'];
$dynamoPowder = $productIds['Dynamo Powder 1kg'];
$comfortBlue  = $productIds['Comfort Blue 1L'];
$downyFresh   = $productIds['Downy Fresh 900ml'];
$clorox       = $productIds['Clorox Bleach 1L'];
$vanish       = $productIds['Vanish Stain 500g'];

$transactions = [
    // 15x Dynamo Liquid + Comfort Blue
    ...array_fill(0, 15, [$dynamoLiquid, $comfortBlue]),
    // 10x Dynamo Liquid + Downy Fresh
    ...array_fill(0, 10, [$dynamoLiquid, $downyFresh]),
    // 8x Dynamo Powder + Vanish Stain
    ...array_fill(0, 8, [$dynamoPowder, $vanish]),
    // 4x Clorox + Vanish
    ...array_fill(0, 4, [$clorox, $vanish]),
    // 3x single-item transactions
    [[$dynamoLiquid]],
    [[$comfortBlue]],
    [[$vanish]],
];

$totalTx = 0;
foreach ($transactions as $itemList) {
    // Round-robin salesmen
    $salesmanId = $salesmanIds[$totalTx % count($salesmanIds)];
    
    // Distribute transactions across the last 6 months to display distinct bars in the monthly chart
    $saleDate = now()->subMonths(rand(0, 5))->subDays(rand(0, 27));
    $txId = DB::table('sales_transactions')->insertGetId([
        'salesman_id' => $salesmanId,
        'total_amount'=> 0,
        'sale_date'   => $saleDate,
        'created_at'  => $saleDate,
        'updated_at'  => $saleDate,
    ]);

    $total = 0;
    $items = is_array($itemList[0]) ? $itemList[0] : $itemList;
    foreach ($items as $itemId) {
        $product = DB::table('items')->where('item_id', $itemId)->first();
        $qty = rand(1, 3);
        DB::table('transaction_details')->insert([
            'transaction_id' => $txId,
            'item_id'        => $itemId,
            'quantity'       => $qty,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
        $total += $product->price * $qty;
    }

    DB::table('sales_transactions')->where('transaction_id', $txId)->update(['total_amount' => $total]);
    $totalTx++;
}
echo "Inserted $totalTx transactions.\n";

// ============================================================
// STEP 5: Create sample Promotions
// ============================================================
// Manager-created (Active)
DB::table('promotions')->insert([
    'manager_id'  => $managerId,
    'salesman_id' => null,
    'rule_id'     => null,
    'promo_name'  => 'Bundle & Save — Detergent + Softener',
    'description' => 'Buy any detergent and get 15% off fabric softener. Based on purchasing pattern analysis.',
    'start_date'  => now()->format('Y-m-d'),
    'end_date'    => now()->addMonths(1)->format('Y-m-d'),
    'status'      => 'Active',
    'created_at'  => now(),
    'updated_at'  => now(),
]);

// Salesman-proposed (Pending)
DB::table('promotions')->insert([
    'manager_id'  => null,
    'salesman_id' => $salesmanIds[0],
    'rule_id'     => null,
    'promo_name'  => 'Holiday Raya Special',
    'description' => 'Offer 10% discount on all fabric softeners for the festive season.',
    'start_date'  => now()->format('Y-m-d'),
    'end_date'    => now()->addMonths(2)->format('Y-m-d'),
    'status'      => 'Pending',
    'created_at'  => now(),
    'updated_at'  => now(),
]);

// Run Apriori algorithm to populate association rules (buying patterns)
$apriori = new \App\Services\AprioriService(0.1, 0.5);
$apriori->run();
echo "Buying patterns generated.\n";

echo "Promotions inserted.\n";
echo "\n=== ALL DONE ===\n";
echo "Total transactions: $totalTx\n";
echo "Manager login:  nuraisyahsiti793@gmail.com / Nurisy@22\n";
echo "Salesman login: faris@dozee.com / password\n";
echo "Salesman login: nurul@dozee.com / password\n";
