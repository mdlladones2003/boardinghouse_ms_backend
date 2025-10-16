<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedAssignment extends Model
{
    protected $primaryKey = 'bed_assignment_id';

    protected $fillable = [
        'room_id',
        'tenant_id',
        'bed_number',
        'assigned_on',
        'status'
    ];

    protected $casts = [
        'assigned_on' => 'date'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }
}
