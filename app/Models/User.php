<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'tenant_id',
        'username',
        'password',
        'email',
        'role'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'password' => 'hashed'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }
    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class, 'user_id', 'user_id');
    }
}
