<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    protected $primaryKey = 'payment_history_id';

    protected $fillable = [
        'payment_id',
        'user_id',
        'action',
        'action_type',
        'action_date'
    ];

    protected $casts = [
        'action_date' => 'datetime'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
