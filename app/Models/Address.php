<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $primaryKey = 'address_id';

    protected $fillable = [
        'street_address',
        'city',
        'state',
        'postal_code',
        'country'
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'address_id', 'address_id');
    }
}
