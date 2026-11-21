<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalaryPayment extends Model
{
    protected $fillable = ['employee_id', 'employee_salary_id', 'expense_id', 'period', 'amount', 'paid_on', 'notes'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'paid_on' => 'date'];
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function salary(): BelongsTo { return $this->belongsTo(EmployeeSalary::class, 'employee_salary_id'); }
    public function expense(): BelongsTo { return $this->belongsTo(Expense::class); }
}
