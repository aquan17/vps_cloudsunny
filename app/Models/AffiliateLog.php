<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'buyer_id',
        'transaction_type',
        'amount',
        'commission',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
