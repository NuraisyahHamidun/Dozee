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
        'salesmen_id', 
        'event_name', 
        'total_amount', 
        'type',
        'payment_method',
        'sale_date', 
        'status', 
        'date_create', 
        'date_modifier', 
        'date_verify',
        'approved_by'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'date_create' => 'datetime',
        'date_modifier' => 'datetime',
        'date_verify' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($sale) {
            if (empty($sale->status)) {
                $sale->status = 'Pending';
            }
            if (empty($sale->date_create)) {
                $sale->date_create = now();
            }
        });
    }

    public function salesmen()
    {
        return $this->belongsTo(Salesmen::class, 'salesmen_id', 'salesmen_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'transaction_id', 'transaction_id');
    }
}
