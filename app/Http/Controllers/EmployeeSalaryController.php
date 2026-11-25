<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalaryPayment;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EmployeeSalaryController extends Controller
{
    public function index(): View
    {
        $employees = Employee::with(['user', 'salary'])->orderBy('name')->get();
        $salaryPayments = EmployeeSalaryPayment::with('employee')->orderByDesc('paid_on')->orderByDesc('id')->limit(100)->get();
        $totalMonthlyPayroll = (float) $employees->sum(fn (Employee $employee) => (float) ($employee->salary?->amount ?? 0));
        $employeesWithSalary = $employees->filter(fn (Employee $employee) => (float) ($employee->salary?->amount ?? 0) > 0)->count();
        $averageSalary = $employeesWithSalary > 0 ? $totalMonthlyPayroll / $employeesWithSalary : 0;

        return view('owner.salaries', compact('employees', 'salaryPayments', 'totalMonthlyPayroll', 'employeesWithSalary', 'averageSalary'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $salary = $employee->salary()->first();
        $validated['effective_date'] = $salary?->effective_date?->toDateString() ?? today()->toDateString();
        $employee->salary()->updateOrCreate([], $validated);

        return redirect()->route('owner.salaries.index')->with('success', 'Data gaji ' . $employee->name . ' berhasil diperbarui.');
    }

    public function recordPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'period' => ['required', 'date_format:Y-m'],
            'paid_on' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $employee = Employee::with('salary')->findOrFail($validated['employee_id']);
        $salary = $employee->salary;
        if (!$salary || (float) $salary->amount <= 0) {
            return back()->withErrors(['employee_id' => 'Atur nominal gaji karyawan terlebih dahulu sebelum mencatat pembayaran.'])->withInput();
        }
        if (EmployeeSalaryPayment::where('employee_id', $employee->id)->where('period', $validated['period'])->exists()) {
            return back()->withErrors(['period' => 'Pembayaran gaji karyawan ini untuk periode tersebut sudah tercatat.'])->withInput();
        }

        DB::transaction(function () use ($employee, $salary, $validated) {
            $expense = Expense::create([
                'type' => Expense::TYPE_PENGELUARAN,
                'expense_date' => $validated['paid_on'],
                'category' => 'Gaji Karyawan',
                'amount' => $salary->amount,
                'description' => 'Gaji ' . $employee->name . ' · periode ' . $validated['period'] . ($validated['notes'] ? ' · ' . $validated['notes'] : ''),
            ]);

            EmployeeSalaryPayment::create([
                'employee_id' => $employee->id,
                'employee_salary_id' => $salary->id,
                'expense_id' => $expense->id,
                'period' => $validated['period'],
                'amount' => $salary->amount,
                'paid_on' => $validated['paid_on'],
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('owner.salaries.index')->with('success', 'Pembayaran gaji ' . $employee->name . ' tercatat dan otomatis masuk ke Catatan Keuangan.');
    }
}
