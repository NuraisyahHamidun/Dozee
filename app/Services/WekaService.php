<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\AprioriAnalysis;
use Illuminate\Support\Facades\Log;

class WekaService
{
    /**
     * Run WEKA Apriori algorithm and return the parsed association rules.
     *
     * @param array $filters
     * @param int $numRules
     * @param float $minConfidence
     * @param float $minSupport
     * @return array
     */
    public function run(array $filters = [], int $numRules = 20, float $minConfidence = 0.6, float $minSupport = 0.02): array
    {
        // 1. Fetch sales transactions, details, and products
        $query = Sale::with('saleItems:detail_id,transaction_id,item_id');

        if (!empty($filters['event_name'])) {
            $query->where('event_name', $filters['event_name']);
        }

        if (!empty($filters['salesman_id'])) {
            $query->where('salesman_id', $filters['salesman_id']);
        }

        $transactions = [];
        $totalTransactions = 0;

        // Fetch using chunk to prevent memory exhaustion
        $query->chunk(500, function ($sales) use (&$transactions, &$totalTransactions) {
            foreach ($sales as $sale) {
                $items = $sale->saleItems->pluck('item_id')->unique()->values()->toArray();
                if (count($items) >= 2) { // Skip single-item transactions
                    $transactions[] = $items;
                    $totalTransactions++;
                }
            }
        });

        if ($totalTransactions === 0) {
            return [
                'rules' => [],
                'totalTransactions' => 0,
            ];
        }

        $products = Product::orderBy('item_id')->get();
        $productsById = $products->pluck('item_name', 'item_id')->toArray();

        // 2. Convert data into ARFF format
        $header = "@relation transactions\n\n";
        foreach ($products as $product) {
            $header .= "@attribute " . ($product->item_code ?: 'item_id_' . $product->item_id) . " {t}\n";
        }
        $header .= "\n@data\n";

        $lines = [];
        foreach ($transactions as $txItems) {
            $row = [];
            foreach ($products as $product) {
                $row[] = in_array($product->item_id, $txItems) ? 't' : '?';
            }
            $lines[] = implode(',', $row);
        }
        $arffContent = $header . implode("\n", $lines);

        // 3. Save file to storage/app/weka/transactions.arff
        $wekaDir = storage_path('app/weka');
        if (!is_dir($wekaDir)) {
            mkdir($wekaDir, 0755, true);
        }
        $arffPath = $wekaDir . '/transactions.arff';
        file_put_contents($arffPath, $arffContent);

        // 4. Execute WEKA Apriori using Java command
        $wekaJar = base_path('weka.jar');
        
        // Escaping parameters to avoid shell injections
        $escapedJar = escapeshellarg($wekaJar);
        $escapedArff = escapeshellarg($arffPath);
        
        $command = sprintf(
            'java --add-opens java.base/java.lang=ALL-UNNAMED -cp %s weka.associations.Apriori -t %s -N %d -C %f -M %f',
            $escapedJar,
            $escapedArff,
            $numRules,
            $minConfidence,
            $minSupport
        );

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        // Save raw output log to a text file for the UI Log tab
        $wekaLogPath = storage_path('app/weka/weka_output.txt');
        file_put_contents($wekaLogPath, implode("\n", $output));

        if ($returnVar !== 0) {
            Log::error("Weka Apriori command failed with exit code: $returnVar. Command: $command");
        }

        // 5. Parse rules from output
        $rules = [];
        $ruleRegex = '/^\s*(\d+)\.\s+(.*?)\s+(\d+)\s+==>\s+(.*?)\s+(\d+)\s+<conf:\((.*?)\)>\s+lift:\((.*?)\)/';

        foreach ($output as $line) {
            if (preg_match($ruleRegex, $line, $matches)) {
                $antecedentStr = $matches[2];
                $consequentStr = $matches[4];
                $ruleCount = (int) $matches[5];
                $confidence = (float) $matches[6];
                $lift = (float) $matches[7];

                // Parse conviction (e.g. conv:(2.87))
                $conv = 1.0;
                if (preg_match('/conv:\((.*?)\)/', $line, $convMatch)) {
                    $conv = (float) $convMatch[1];
                }

                // Extract item codes / item IDs
                $productsByAttribute = [];
                foreach ($products as $product) {
                    $attrName = $product->item_code ?: 'item_id_' . $product->item_id;
                    $productsByAttribute[$attrName] = $product->item_id;
                }

                preg_match_all('/([A-Za-z0-9_-]+)=t/', $antecedentStr, $anteMatches);
                $anteAttrs = $anteMatches[1] ?? [];
                $anteIds = array_filter(array_map(fn($attr) => $productsByAttribute[$attr] ?? null, $anteAttrs));

                preg_match('/([A-Za-z0-9_-]+)=t/', $consequentStr, $consMatch);
                $consAttr = $consMatch[1] ?? null;
                $consId = $productsByAttribute[$consAttr] ?? null;

                if (!empty($anteIds) && $consId) {
                    $anteNames = array_map(fn($id) => $productsById[$id] ?? 'Item #' . $id, $anteIds);
                    $consName = $productsById[$consId] ?? 'Item #' . $consId;

                    $ruleText = implode(' + ', $anteNames) . ' ==> ' . $consName . ' [conv:' . $conv . ']';
                    $support = $ruleCount / $totalTransactions;

                    $rules[] = [
                        'antecedent' => implode('+', $anteIds),
                        'consequent' => $consId,
                        'rule_text' => $ruleText,
                        'support' => round($support, 4),
                        'confidence' => round($confidence, 4),
                        'lift' => round($lift, 4),
                    ];
                }
            }
        }

        // 6. Save rules to association_rules table
        // Delete previous rules
        AprioriAnalysis::query()->delete();

        $savedRules = [];
        foreach ($rules as $rule) {
            $savedRules[] = AprioriAnalysis::create([
                'rule_text' => $rule['rule_text'],
                'antecedent' => $rule['antecedent'],
                'consequent' => $rule['consequent'],
                'support' => $rule['support'],
                'confidence' => $rule['confidence'],
                'lift' => $rule['lift'],
            ]);
        }

        return [
            'rules' => $savedRules,
            'totalTransactions' => $totalTransactions,
        ];
    }
}
