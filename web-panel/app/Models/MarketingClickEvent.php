<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingClickEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'placement',
        'cta_key',
        'cta_label',
        'source_host',
        'source_path',
        'landing_path',
        'target_host',
        'target_path',
        'client_token',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'user_agent',
        'ip_hash',
    ];
}
