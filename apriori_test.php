<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Apriori Smoke Test ===" . PHP_EOL;

$svc = new App\Services\AprioriService(0.01, 0.1);
$result = $svc->run();

echo "Total Transactions : " . $result['totalTransactions'] . PHP_EOL;
echo "2-Itemsets found   : " . count($result['frequentItemsets'][2] ?? []) . PHP_EOL;
echo "3-Itemsets found   : " . count($result['frequentItemsets'][3] ?? []) . PHP_EOL;
echo "Rules saved to DB  : " . count($result['associationRules']) . PHP_EOL;

echo PHP_EOL . "=== Sample Rules ===" . PHP_EOL;
$rules = App\Models\AprioriAnalysis::latest('confidence')->take(5)->get();

if ($rules->isEmpty()) {
    echo "No rules in DB yet. Try lowering minSupport or add more sales data." . PHP_EOL;
} else {
    foreach ($rules as $rule) {
        echo sprintf(
            "  [%s] => [%s]  | Support: %.2f%%  | Confidence: %.2f%%  | Lift: %.3f" . PHP_EOL,
            $rule->antecedent,
            $rule->consequent,
            ($rule->support ?? 0) * 100,
            $rule->confidence * 100,
            $rule->lift
        );
    }
}

echo PHP_EOL . "=== DB Column Check ===" . PHP_EOL;
$cols = Illuminate\Support\Facades\Schema::getColumnListing('association_rules');
echo "Columns: " . implode(', ', $cols) . PHP_EOL;

echo PHP_EOL . "✅ Smoke test complete." . PHP_EOL;
