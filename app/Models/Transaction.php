<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vps_instance_id',
        'proxy_instance_id',
        'type',
        'amount',
        'provider_cost',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
        'provider_cost' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vps()
    {
        return $this->belongsTo(VpsInstance::class, 'vps_instance_id');
    }

    public function proxy()
    {
        return $this->belongsTo(ProxyInstance::class, 'proxy_instance_id');
    }
}

