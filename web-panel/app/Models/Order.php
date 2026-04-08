<?php

namespace App\Models;

use App\Enums\Order\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
      'order_status' => OrderStatus::class,
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'payment_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }

    public function refund() {
        return $this->hasOne(Refund::class);
    }

}
