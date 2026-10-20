<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'customization',
        'quantity',
        'cost_price',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'customization' => 'array',
    ];

    public function getCustomizationSummaryAttribute(): string
    {
        $options = $this->customization ?? [];
        $labels = [
            'temperature' => ['ice' => 'Dingin', 'hot' => 'Panas'],
            'size' => ['regular' => 'Regular', 'large' => 'Large (+Rp5.000)', 'jumbo' => 'Jumbo (+Rp8.000)'],
            'beans' => ['quattro' => 'Quattro Blend', 'arabica' => 'Arabica Blend (+Rp3.000)'],
            'milk' => ['milk' => 'Milk', 'oat' => 'Oat milk (+Rp5.000)'],
            'sugar' => ['normal' => 'Normal sugar', 'less' => 'Less sugar', 'none' => 'No sugar'],
        ];
        $summary = [];

        foreach ($labels as $key => $choices) {
            if (isset($options[$key], $choices[$options[$key]])) {
                $summary[] = $choices[$options[$key]];
            }
        }

        $toppingLabels = [
            'whipped_cream' => 'Whipped cream (+Rp5.000)',
            'caramel_drizzle' => 'Caramel drizzle (+Rp4.000)',
            'espresso_shot' => 'Extra espresso shot (+Rp8.000)',
        ];
        foreach (($options['toppings'] ?? []) as $topping) {
            if (isset($toppingLabels[$topping])) {
                $summary[] = $toppingLabels[$topping];
            }
        }

        if (!empty($options['note'])) {
            $summary[] = 'Catatan: ' . trim((string) $options['note']);
        }

        return implode(' · ', array_filter($summary));
    }

    /**
     * Item milik satu order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Item berasal dari satu product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function review(): HasOne
    {
        return $this->hasOne(ProductReview::class);
    }
}
