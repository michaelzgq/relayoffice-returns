<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    use HasFactory;

    protected $appends = ['image_fullpath'];
    public function getImageFullPathAttribute(): string
    {
        $image = $this->image ?? null;
        $path = asset('assets/admin/img/160x160/img1.jpg');
        if (!is_null($image) && Storage::disk('public')->exists('customer/' . $image)) {
            $path = asset('storage/customer/' . $image);
        }
        return $path;
    }
    public function orders()
    {
        return $this->hasMany(Order::class,'user_id');
    }
    public function getFullAddressAttribute(): string
    {
        $address = $this->address ?? '';
        $city = $this->city ?? '';
        $state = $this->state ?? '';
        $zip = $this->zip_code ?? '';
        $fullAddress = '';
        if (!empty($address)) {
            $fullAddress .= $address;
        }
        if (!empty($city)) {
            $fullAddress .= ', ' . $city;
        }
        if (!empty($state)) {
            $fullAddress .= ', ' . $state;
        }
        if (!empty($zip)) {
            $fullAddress .= ', ' . $zip;
        }
        return $fullAddress;
    }
}
