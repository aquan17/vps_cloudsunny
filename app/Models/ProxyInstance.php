<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cloudsunny_account_id',
        'provider_proxy_id',
        'provider_order_id',
        'product_id',
        'type_proxy',
        'ip',
        'port',
        'username',
        'password',
        'sock5_port',
        'sock5_username',
        'sock5_password',
        'status',
        'billing_cycle',
        'cost_monthly_usd',
        'paid_amount',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'cost_monthly_usd' => 'decimal:4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cloudSunnyAccount()
    {
        return $this->belongsTo(CloudSunnyAccount::class, 'cloudsunny_account_id');
    }
}
