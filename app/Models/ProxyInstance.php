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

    protected static function booted()
    {
        static::updated(function (ProxyInstance $proxy) {
            if ($proxy->status === 'Hoạt động' && $proxy->ip) {
                $cacheKey = "proxy_welcome_email_sent_{$proxy->id}";
                
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($proxy->user->email)->queue(
                            new \App\Mail\ProxyCreated($proxy)
                        );
                        \Illuminate\Support\Facades\Cache::forever($cacheKey, true);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to send Proxy welcome email', [
                            'proxy_id' => $proxy->id, 
                            'msg' => $e->getMessage()
                        ]);
                    }
                }
            }
        });
    }
}
