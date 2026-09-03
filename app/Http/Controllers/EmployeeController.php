<?php

namespace App\Http/Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        // Fitur Pencarian Realtime/Filter
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('position', 'like', '%' . $request->search . '%');
        }

        $employees = $query->latest()->get();

        // Hitung statistik untuk cards
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'Aktif')->count();
        $shiftToday = Employee::whereIn('shift', ['Pagi', 'Siang'])->where('status', 'Aktif')->count();
        $onLeave = Employee::where('status', 'Cuti')->count();

        return view('employees', compact(
            'employees',
            'totalEmployees',
            'activeEmployees',
            'shiftToday',
            'onLeave'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string',
            'shift' => 'required|string',
            'status' => 'required|in:Aktif,Cuti,Nonaktif',
            'phone' => 'required|string|max:20',
        ]);

        Employee::create($request->all());

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string',
            'shift' => 'required|string',
            'status' => 'required|in:Aktif,Cuti,Nonaktif',
            'phone' => 'required|string|max:20',
        ]);

        $employee->update($request->all());

        return redirect()->back()->with('success', 'Data karyawan berhasil diperbarui!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->back()->with('success', 'Karyawan berhasil dihapus!');
    }
}