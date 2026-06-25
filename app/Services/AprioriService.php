<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\AprioriAnalysis;
use Illuminate\Support\Facades\DB;

class AprioriService
{
    protected float $minSupport;
    protected float $minConfidence;

    /**
     * @param float $minSupport    Minimum support threshold (0 < x <= 1)
     * @param float $minConfidence Minimum confidence threshold (0 < x <= 1)
     */
    public function __construct(float $minSupport = 0.1, float $minConfidence = 0.5)
    {
        // Validate parameters
        $this->minSupport    = max(0.001, min(1.0, $minSupport));
        $this->minConfidence = max(0.001, min(1.0, $minConfidence));
    }

    public function run(array $filters = []): array
    {
        // ── 1. Build transactions using chunking to avoid memory exhaustion ──
        $query = Sale::with('saleItems:detail_id,transaction_id,item_id');

        if (!empty($filters['event_name'])) {
            $query->where('event_name', $filters['event_name']);
        }

        if (!empty($filters['salesman_id'])) {
            $query->where('salesman_id', $filters['salesman_id']);
        }

        $transactions      = [];
        $totalTransactions = 0;

        $query->chunk(500, function ($sales) use (&$transactions, &$totalTransactions) {
            foreach ($sales as $sale) {
                $items = $sale->saleItems->pluck('item_id')->unique()->values()->toArray();
                if (count($items) >= 2) {          // skip single-item transactions
                    $transactions[]     = $items;
                    $totalTransactions++;
                }
            }
        });

        if ($totalTransactions === 0) {
            return [
                'frequentItemsets'  => [],
                'associationRules'  => [],
                'totalTransactions' => 0,
            ];
        }

        $minSupportCount = $this->minSupport * $totalTransactions;

        // ── 2. Frequent 1-itemsets ──────────────────────────────────────────
        $itemCounts = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction as $itemId) {
                $itemCounts[$itemId] = ($itemCounts[$itemId] ?? 0) + 1;
            }
        }

        $frequent1 = array_filter($itemCounts, fn($c) => $c >= $minSupportCount);
        $f1Keys    = array_keys($frequent1);

        // ── 3. Frequent 2-itemsets ──────────────────────────────────────────
        $frequent2   = [];
        $f1KeysCount = count($f1Keys);

        for ($i = 0; $i < $f1KeysCount; $i++) {
            for ($j = $i + 1; $j < $f1KeysCount; $j++) {
                $a = $f1Keys[$i];
                $b = $f1Keys[$j];

                $count = 0;
                foreach ($transactions as $t) {
                    if (in_array($a, $t, true) && in_array($b, $t, true)) {
                        $count++;
                    }
                }

                if ($count >= $minSupportCount) {
                    $key            = $a . ',' . $b;
                    $frequent2[$key] = $count;
                }
            }
        }

        // ── 4. Frequent 3-itemsets (bonus: larger bundles) ──────────────────
        $frequent3  = [];
        $f2Keys     = array_keys($frequent2);
        $f2KeyCount = count($f2Keys);

        for ($i = 0; $i < $f2KeyCount; $i++) {
            $pairA     = explode(',', $f2Keys[$i]);
            $existingItems = array_unique($pairA);

            // Extend with each frequent 1-item not already in the pair
            foreach ($f1Keys as $extra) {
                if (in_array($extra, $existingItems, true)) {
                    continue;
                }

                $trio = $existingItems;
                $trio[] = $extra;
                sort($trio);
                $key = implode(',', $trio);

                if (isset($frequent3[$key])) {
                    continue; // already counted
                }

                $count = 0;
                foreach ($transactions as $t) {
                    $allPresent = true;
                    foreach ($trio as $item) {
                        if (!in_array($item, $t, true)) {
                            $allPresent = false;
                            break;
                        }
                    }
                    if ($allPresent) {
                        $count++;
                    }
                }

                if ($count >= $minSupportCount) {
                    $frequent3[$key] = $count;
                }
            }
        }

        // ── 5. Generate Association Rules & persist ─────────────────────────
        // Clear old rules atomically
        AprioriAnalysis::query()->delete();

        $productsById = Product::pluck('item_name', 'item_id')->toArray();
        $rules = [];

        // Rules from 2-itemsets: A → B and B → A
        foreach ($frequent2 as $pairKey => $count) {
            [$a, $b] = explode(',', $pairKey);
            $support  = $count / $totalTransactions;

            // A → B
            $confAB = $count / $itemCounts[$a];
            if ($confAB >= $this->minConfidence) {
                $anteName = $productsById[$a] ?? 'Item #' . $a;
                $consName = $productsById[$b] ?? 'Item #' . $b;
                $supportB = $itemCounts[$b] / $totalTransactions;
                $denominator = 1 - $confAB;
                $conviction = $denominator > 0 ? (1 - $supportB) / $denominator : 1.0;
                $ruleText = $anteName . ' ==> ' . $consName . ' [conv:' . round($conviction, 2) . ']';

                $rules[] = AprioriAnalysis::create([
                    'rule_text'  => $ruleText,
                    'antecedent' => (string) $a,
                    'consequent' => (string) $b,
                    'support'    => round($support, 4),
                    'confidence' => round($confAB, 4),
                    'lift'       => round($confAB / ($supportB), 4),
                ]);
            }

            // B → A
            $confBA = $count / $itemCounts[$b];
            if ($confBA >= $this->minConfidence) {
                $anteName = $productsById[$b] ?? 'Item #' . $b;
                $consName = $productsById[$a] ?? 'Item #' . $a;
                $supportA = $itemCounts[$a] / $totalTransactions;
                $denominator = 1 - $confBA;
                $conviction = $denominator > 0 ? (1 - $supportA) / $denominator : 1.0;
                $ruleText = $anteName . ' ==> ' . $consName . ' [conv:' . round($conviction, 2) . ']';

                $rules[] = AprioriAnalysis::create([
                    'rule_text'  => $ruleText,
                    'antecedent' => (string) $b,
                    'consequent' => (string) $a,
                    'support'    => round($support, 4),
                    'confidence' => round($confBA, 4),
                    'lift'       => round($confBA / ($supportA), 4),
                ]);
            }
        }

        // Rules from 3-itemsets: {A,B} → C  (all 3 rotations)
        foreach ($frequent3 as $trioKey => $count) {
            $trio    = explode(',', $trioKey);
            $support = $count / $totalTransactions;

            for ($k = 0; $k < 3; $k++) {
                $consequent  = $trio[$k];
                $antecedents = array_values(array_diff($trio, [$consequent]));
                sort($antecedents);

                // Support of antecedent pair
                $antKey     = implode(',', $antecedents);
                $antSupport = ($frequent2[$antKey] ?? null);
                if ($antSupport === null) {
                    $antKey     = implode(',', array_reverse($antecedents));
                    $antSupport = ($frequent2[$antKey] ?? 0);
                }

                if ($antSupport === 0) {
                    continue;
                }

                $confidence = $count / $antSupport;
                if ($confidence >= $this->minConfidence) {
                    $anteNames = array_map(fn($id) => $productsById[$id] ?? 'Item #' . $id, $antecedents);
                    $consName = $productsById[$consequent] ?? 'Item #' . $consequent;
                    $supportCons = $itemCounts[$consequent] / $totalTransactions;
                    $denominator = 1 - $confidence;
                    $conviction = $denominator > 0 ? (1 - $supportCons) / $denominator : 1.0;
                    $ruleText = implode(' + ', $anteNames) . ' ==> ' . $consName . ' [conv:' . round($conviction, 2) . ']';

                    // Encode antecedent as "A+B" so UI can split on '+'
                    $rules[] = AprioriAnalysis::create([
                        'rule_text'  => $ruleText,
                        'antecedent' => implode('+', $antecedents),
                        'consequent' => (string) $consequent,
                        'support'    => round($support, 4),
                        'confidence' => round($confidence, 4),
                        'lift'       => round($confidence / ($supportCons), 4),
                    ]);
                }
            }
        }

        return [
            'frequentItemsets'  => [
                1 => $frequent1,
                2 => $frequent2,
                3 => $frequent3,
            ],
            'associationRules'  => $rules,
            'totalTransactions' => $totalTransactions,
        ];
    }
}
