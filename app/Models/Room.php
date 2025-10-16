<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $primaryKey = 'room_id';

    protected $fillable = [
        'room_number',
        'capacity',
        'status',
        'rent_amount'
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2'
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'room_id', 'room_id');
    }
    public function bedAssignments()
    {
        return $this->hasMany(BedAssignment::class, 'room_id', 'room_id');
    }
}
