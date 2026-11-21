<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeLeaveController extends Controller
{
    public function employeeIndex(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->firstOrFail();
        $leaveRequests = EmployeeLeaveRequest::where('employee_id', $employee->id)
            ->latest()->get();

        return view('staff.leave', compact('employee', 'leaveRequests'));
    }

    public function store(Request $request)
    {
        $employee = Employee::where('user_id', $request->user()->id)->firstOrFail();
        $validated = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1500'],
        ]);

        $overlap = EmployeeLeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['Pending', 'Disetujui'])
            ->whereDate('start_date', '<=', $validated['end_date'])
            ->whereDate('end_date', '>=', $validated['start_date'])
            ->exists();

        if ($overlap) {
            return back()->withErrors(['start_date' => 'Tanggal tersebut bertabrakan dengan pengajuan cuti lain yang masih aktif.'])->withInput();
        }

        EmployeeLeaveRequest::create([
            'employee_id' => $employee->id,
            ...$validated,
            'status' => 'Pending',
        ]);

        return redirect()->route($request->user()->role . '.leave')->with('success', 'Pengajuan cuti berhasil dikirim ke Owner.');
    }

    public function ownerIndex()
    {
        $leaveRequests = EmployeeLeaveRequest::with(['employee.user', 'reviewer'])
            ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
            ->latest()->get();

        return view('owner.leaves', compact('leaveRequests'));
    }

    public function review(Request $request, EmployeeLeaveRequest $leaveRequest)
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::in(['Disetujui', 'Ditolak'])],
            'owner_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($leaveRequest->status !== 'Pending') {
            return back()->with('error', 'Pengajuan ini sudah pernah diproses.');
        }

        DB::transaction(function () use ($request, $leaveRequest, $validated) {
            $leaveRequest->update([
                'status' => $validated['decision'],
                'owner_note' => $validated['owner_note'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

        });

        return back()->with('success', 'Pengajuan cuti ' . strtolower($validated['decision']) . '. Akses login karyawan mengikuti periode cuti yang disetujui.');
    }
}
