<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VpsInstance extends Model
{
    protected $fillable = [
        'user_id',
        'cloudsunny_account_id',
        'provider_vps_id',
        'provider_order_id',
        'provider_product_id',
        'provider_os_id',
        'billing_cycle',
        'label',
        'root_password',
        'region',
        'plan_id',
        'status',
        'public_ip',
        'login_username',
        'provider_payload',
        'cpu',
        'ram',
        'disk',
        'cost_monthly_usd',
        'hourly_price_usd',
        'provider_cost',
        'paid_amount',
        'expires_at',
    ];

    protected $casts = [
        'root_password' => 'encrypted',
        'cost_monthly_usd' => 'decimal:4',
        'hourly_price_usd' => 'decimal:6',
        'provider_payload' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cloudSunnyAccount()
    {
        return $this->belongsTo(CloudSunnyAccount::class, 'cloudsunny_account_id');
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['Lỗi', 'Đã xóa', 'Lỗi API', 'Hết hạn'], true);
    }

    public function statusBadgeClass(): string
    {
        if (in_array($this->status, ['Sẵn sàng', 'Đang chạy', 'running'], true)) {
            return 'badge-success';
        }
        if (strpos($this->status, 'Lỗi') !== false) {
            return 'badge-danger';
        }
        return 'badge-warning';
    }
}
