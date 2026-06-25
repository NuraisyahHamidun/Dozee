<!DOCTYPE html>
<html>
<head>
    <title>Market Basket Analysis Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #4F46E5; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #F1F5F9; color: #333; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Do'zee - Market Basket Analysis Report</h2>
        <p>Customer purchase patterns and product bundling insights</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Rule ID</th>
                <th>Antecedent</th>
                <th>Consequent</th>
                <th class="text-right">Support</th>
                <th class="text-right">Confidence</th>
                <th class="text-right">Lift</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aprioriRules as $rule)
            @php
                $antecedentNames = '';
                if ($rule->isMultiAntecedent()) {
                    $ids = $rule->antecedentIds();
                    $names = \App\Models\Product::whereIn('item_id', $ids)->get()->map(function($p) {
                        return $p->item_code ? $p->item_code . ' (' . $p->item_name . ')' : $p->item_name;
                    })->toArray();
                    $antecedentNames = implode(' + ', $names);
                } else {
                    $p = \App\Models\Product::find($rule->antecedent);
                    $antecedentNames = $p ? ($p->item_code ? $p->item_code . ' (' . $p->item_name . ')' : $p->item_name) : $rule->antecedent;
                }
                $p2 = \App\Models\Product::find($rule->consequent);
                $consequentName = $p2 ? ($p2->item_code ? $p2->item_code . ' (' . $p2->item_name . ')' : $p2->item_name) : $rule->consequent;
            @endphp
            <tr>
                <td>{{ $rule->rule_id }}</td>
                <td>{{ $antecedentNames }}</td>
                <td>{{ $consequentName }}</td>
                <td class="text-right">{{ number_format($rule->support * 100, 2) }}%</td>
                <td class="text-right">{{ number_format($rule->confidence * 100, 1) }}%</td>
                <td class="text-right">{{ number_format($rule->lift, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
