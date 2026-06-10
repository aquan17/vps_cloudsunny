<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'amount',
        'note',
        'status',
        'provider',
        'provider_order_code',
        'transaction_ref',
        'raw_payload',
        'paid_at',
        'approved_amount',
        'admin_note',
        'approved_by',
        'processed_at',
    ];

    protected $casts = [
        'amount'          => 'integer',
        'approved_amount' => 'integer',
        'processed_at'    => 'datetime',
        'raw_payload'     => 'array',
        'paid_at'         => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getVietQrUrlAttribute(): string
    {
        return 'https://img.vietqr.io/image/' . config('deposit.bank_id') . '-' . config('deposit.account_no') . '-compact2.png?' . http_build_query([
            'amount' => $this->amount,
            'addInfo' => $this->code,
            'accountName' => config('deposit.account_name'),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function isApproved(): bool
    {
        return $this->status === 'approved' || $this->status === 'paid';
    }
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function statusLabel(): string
    {
        if ($this->status === 'paid') return 'Đã thanh toán';
        return [
            'pending'  => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
        ][$this->status] ?? $this->status;
    }

    public function statusClass(): string
    {
        if ($this->status === 'paid') return 'badge-success';
        return [
            'pending'  => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
        ][$this->status] ?? 'badge-muted';
    }
}
