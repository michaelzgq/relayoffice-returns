<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'integer'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'counter_id');
    }
}
