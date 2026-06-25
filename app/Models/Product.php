<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'item';
    protected $primaryKey = 'item_id';

    protected $fillable = ['item_code', 'item_name', 'volume', 'category', 'category_id', 'description', 'price', 'stock_qty'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->item_code)) {
                $product->item_code = self::generateNextItemCode();
            }
        });
    }

    public static function generateNextItemCode(): string
    {
        $maxNumber = self::where('item_code', 'like', 'ITM-%')
            ->select('item_code')
            ->get()
            ->map(function ($item) {
                return (int) substr($item->item_code, 4);
            })
            ->max();

        $nextNum = $maxNumber ? $maxNumber + 1 : 1;

        return sprintf('ITM-%04d', $nextNum);
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'item_id', 'item_id');
    }
}
