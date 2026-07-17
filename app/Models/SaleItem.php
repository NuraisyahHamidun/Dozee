<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $table = 'transaction_detail';
    protected $primaryKey = 'detail_id';

    protected $fillable = ['transaction_id', 'item_id', 'promo_id', 'bundle_group_id', 'quantity'];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'transaction_id', 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id', 'item_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promo_id', 'promo_id');
    }
}
