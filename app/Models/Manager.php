<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Manager extends Authenticatable
{
    use Notifiable;

    protected $table = 'manager';

    protected $primaryKey = 'manager_id';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'address',
        'staff_code',
        'phone_number',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function salesmen()
    {
        return $this->hasMany(Salesmen::class, 'manager_id');
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'manager_id');
    }
}
