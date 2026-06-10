<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudSunnyAccount extends Model
{
    protected $fillable = [
        'label',
        'api_username',
        'api_app',
        'api_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'credit_vnd',
        'total_vnd',
        'is_active',
        'is_full',
        'priority',
        'last_synced_at',
        'sync_error',
    ];

    protected $casts = [
        'api_secret' => 'encrypted',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_full' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    protected $hidden = [
        'api_secret',
        'access_token',
        'refresh_token',
    ];

    public function instances()
    {
        return $this->hasMany(VpsInstance::class, 'cloudsunny_account_id');
    }

    public function activeInstances()
    {
        return $this->instances()->whereNotIn('status', ['Lỗi', 'Đã xóa', 'Hết hạn']);
    }
}
