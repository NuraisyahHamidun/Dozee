<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Salesmen extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $table = 'salesmen';

    protected $primaryKey = 'salesmen_id';

    protected $fillable = [
        'manager_id',
        'name',
        'username',
        'email',
        'password',
        'address',
        'staff_code',
        'phone_number',
        'profile_picture',
    ];

    /**
     * Generate a unique staff code in alphanumeric format, e.g., STF-A1B2C3.
     */
    public static function generateUniqueStaffCode(): string
    {
        do {
            $code = 'STF-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (self::where('staff_code', $code)->exists());

        return $code;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getSalesmanIdAttribute()
    {
        return $this->attributes['salesmen_id'] ?? $this->salesmen_id;
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'salesmen_id');
    }
}
