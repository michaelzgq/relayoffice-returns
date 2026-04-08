<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'refund_reason',
        'refund_amount',
        'admin_payment_method',
        'customer_payout_method',
        'other_payment_details',
    ];

    protected $casts = [
        'other_payment_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
