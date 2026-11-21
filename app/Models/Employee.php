<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'shift',
        'status',
        'phone',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(EmployeeLeaveRequest::class);
    }

    public function salary(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EmployeeSalary::class);
    }
}
