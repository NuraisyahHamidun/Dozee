<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales_transaction';
    protected $primaryKey = 'transaction_id';
    
    protected $fillable = [
        'salesman_id', 
        'event_name', 
        'total_amount', 
        'sale_date', 
        'status', 
        'ante_create', 
        'date_modifier', 
        'date_verify',
        'approved_by'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'ante_create' => 'datetime',
        'date_modifier' => 'datetime',
        'date_verify' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($sale) {
            if (empty($sale->status)) {
                $sale->status = 'Pending';
            }
            if (empty($sale->ante_create)) {
                $sale->ante_create = now();
            }
        });
    }

    public function salesman()
    {
        return $this->belongsTo(Salesman::class, 'salesman_id', 'salesman_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'transaction_id', 'transaction_id');
    }
}
