<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AprioriAnalysis extends Model
{
    use HasFactory;

    protected $table = 'association_rules';
    protected $primaryKey = 'rule_id';

    protected $fillable = ['rule_text', 'antecedent', 'consequent', 'support', 'confidence', 'lift'];

    /**
     * True when the antecedent encodes two items (e.g. "12+15").
     */
    public function isMultiAntecedent(): bool
    {
        return str_contains($this->antecedent, '+');
    }

    /**
     * Returns the antecedent item IDs as an array.
     */
    public function antecedentIds(): array
    {
        return explode('+', $this->antecedent);
    }

    public function antecedentProduct()
    {
        return $this->belongsTo(Product::class, 'antecedent', 'item_id');
    }

    public function consequentProduct()
    {
        return $this->belongsTo(Product::class, 'consequent', 'item_id');
    }
}
