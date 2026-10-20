<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    public const TYPE_PEMASUKAN = 'pemasukan';
    public const TYPE_PENGELUARAN = 'pengeluaran';

    protected $fillable = [
        'type',
        'expense_date',
        'category',
        'amount',
        'description',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function scopePengeluaran(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_PENGELUARAN);
    }

    public function scopePemasukan(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_PEMASUKAN);
    }
}