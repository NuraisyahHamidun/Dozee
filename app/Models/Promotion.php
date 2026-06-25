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
        'salesman_id',
        'rule_id', 
        'promo_name', 
        'description', 
        'start_date', 
        'end_date', 
        'status'
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

    public function salesman()
    {
        return $this->belongsTo(Salesman::class, 'salesman_id', 'salesman_id');
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
}

