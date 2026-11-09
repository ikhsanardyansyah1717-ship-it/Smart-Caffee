<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'branch_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'guest_token',
        'table_number',
        'subtotal',
        'tax',
        'total',
        'status',
        'payment_status',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'kitchen_confirmed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'kitchen_started_at' => 'datetime',
        'kitchen_completed_at' => 'datetime',
        'kitchen_confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Order milik satu user/customer
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Order memiliki banyak item
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Order memiliki satu payment
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
