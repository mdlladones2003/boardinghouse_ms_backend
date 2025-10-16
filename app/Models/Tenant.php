<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $primaryKey = 'tenant_id';

    protected $fillable = [
        'room_id',
        'address_id',
        'first_name',
        'last_name',
        'phone',
        'move_in',
        'status'
    ];

    protected $casts = [
        'move_in' => 'date'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }
    public function bedAssignments()
    {
        return $this->hasMany(BedAssignment::class, 'tenant_id', 'tenant_id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tenant_id', 'tenant_id');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'tenant_id', 'tenant_id');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'tenant_id', 'tenant_id');
    }
}
