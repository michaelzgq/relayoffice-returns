<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = [
        'id', 'unit_type', 'status', 'created_at', 'updated_at', 'company_id'
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_type', 'id');
    }
}
