<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    protected $fillable = ['employee_id', 'amount', 'effective_date', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'effective_date' => 'date'];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
