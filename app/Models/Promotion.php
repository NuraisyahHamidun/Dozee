<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promotion';

    protected $primaryKey = 'promo_id';
    
    protected $fillable = [
        'manager_id', 
        'salesmen_id',
        'rule_id', 
        'promo_name', 
        'description', 
        'start_date', 
        'end_date', 
        'status',
        'final_discount',
        'discount_type',
        'discount_value',
        'discount_apply_to'
    ];
    public function getStatusAttribute($value)
    {
        if ($value === 'Active' && $this->end_date && \Carbon\Carbon::parse($this->end_date)->endOfDay()->isPast()) {
            return 'Expired';
        }
        
        return $value;
    }
 
    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id', 'manager_id');
    }

    public function salesmen()
    {
        return $this->belongsTo(Salesmen::class, 'salesmen_id', 'salesmen_id');
    }

    public function analysis()
    {
        return $this->belongsTo(AprioriAnalysis::class, 'rule_id', 'rule_id');
    }

    public function associationRules()
    {
        return $this->belongsToMany(AprioriAnalysis::class, 'promotion_association_rule', 'promotion_id', 'rule_id', 'promo_id', 'rule_id');
    }

    public function getProductsAttribute()
    {
        $productIds = collect();
        if ($this->rule_id && $this->analysis) {
            $productIds = $productIds->merge($this->analysis->antecedentIds());
            $productIds->push($this->analysis->consequent);
        }
        foreach ($this->associationRules as $rule) {
            $productIds = $productIds->merge($rule->antecedentIds());
            $productIds->push($rule->consequent);
        }
        return \App\Models\Product::whereIn('item_id', $productIds->unique()->filter())->get();
    }

    public static function syncFromAprioriRules()
    {
        $rules = \App\Models\AprioriAnalysis::where('confidence', '>=', 0.8)
            ->where('lift', '>', 1)
            ->get();

        foreach ($rules as $rule) {
            $antecedents = $rule->antecedentIds();
            $consequent = $rule->consequent;

            $anteProducts = \App\Models\Product::whereIn('item_id', $antecedents)->get();
            $consProduct = \App\Models\Product::find($consequent);

            if (!$consProduct || $anteProducts->isEmpty()) {
                continue;
            }

            $anteCodes = $anteProducts->pluck('item_code')->sort()->implode('+');
            $consCode = $consProduct->item_code;

            $promoName = "AI Bundle: " . $anteCodes . " → " . $consCode;

            // Check if this promotion already exists
            $exists = self::where('promo_name', $promoName)
                ->whereIn('status', ['Active', 'Pending'])
                ->exists();

            if (!$exists) {
                $description = "AI-generated bundle promotion based on Apriori association rule: " . $rule->rule_text . " (Confidence: " . ($rule->confidence * 100) . "%, Lift: " . $rule->lift . ").";
                
                $promotion = self::create([
                    'rule_id' => $rule->rule_id,
                    'promo_name' => $promoName,
                    'description' => $description,
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addDays(30)->toDateString(),
                    'status' => 'Active',
                ]);

                \Illuminate\Support\Facades\DB::table('promotion_association_rule')->insert([
                    'promotion_id' => $promotion->promo_id,
                    'rule_id' => $rule->rule_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $existingPromo = self::where('promo_name', $promoName)
                    ->whereIn('status', ['Active', 'Pending'])
                    ->first();
                if ($existingPromo) {
                    $existingPromo->update(['rule_id' => $rule->rule_id]);
                    
                    \Illuminate\Support\Facades\DB::table('promotion_association_rule')
                        ->where('promotion_id', $existingPromo->promo_id)
                        ->delete();
                    \Illuminate\Support\Facades\DB::table('promotion_association_rule')->insert([
                        'promotion_id' => $existingPromo->promo_id,
                        'rule_id' => $rule->rule_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}

