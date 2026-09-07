<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $employees = $query->orderBy('name')->get();

        $totalEmployees   = Employee::count();
        $activeEmployees  = Employee::where('status', 'Aktif')->count();
        $shiftToday       = Employee::whereIn('shift', ['Pagi', 'Siang'])->where('status', 'Aktif')->count();
        $onLeave          = Employee::where('status', 'Cuti')->count();

        return view('owner.employees', compact(
            'employees',
            'totalEmployees',
            'activeEmployees',
            'shiftToday',
            'onLeave'
        ));
    }

    public function create()
    {
        return view('owner.employees_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'position' => 'required|in:Admin,Kasir,Kitchen',
            'shift'    => 'required|in:Pagi,Siang',
            'status'   => 'required|in:Aktif,Cuti',
            'phone'    => 'nullable|string|max:20',
        ]);

        Employee::create($validated);

        return redirect()
            ->route('owner.employees.index')
            ->with('success', 'Karyawan baru berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        return view('owner.employees_edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'position' => 'required|in:Admin,Kasir,Kitchen',
            'shift'    => 'required|in:Pagi,Siang',
            'status'   => 'required|in:Aktif,Cuti',
            'phone'    => 'nullable|string|max:20',
        ]);

        $employee->update($validated);

        return redirect()
            ->route('owner.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('owner.employees.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }
}