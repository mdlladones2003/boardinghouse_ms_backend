<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'tenant_id',
        'payment_date',
        'amount',
        'payment_type',
        'status'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'tenant_id');
    }
    public function paymentHistories()
    {
        return $this->hasMany(PaymentHistory::class, 'payment_id', 'payment_id');
    }
}
