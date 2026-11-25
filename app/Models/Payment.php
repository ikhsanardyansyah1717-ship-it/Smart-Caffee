<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'cashier_id',
        'cashier_name',
        'payment_method',
        'amount',
        'status',
        'paid_at',
        'transfer_proof_path',
        'bank_account_snapshot',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'bank_account_snapshot' => 'array',
    ];

    /**
     * Payment milik satu order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(PaymentAttempt::class);
    }
}


