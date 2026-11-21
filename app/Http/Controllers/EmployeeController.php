<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLeaveRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $employees = $query->orderBy('name')->get();
        $totalEmployees = $employees->count();
        $activeLeaveIds = EmployeeLeaveRequest::where('status', 'Disetujui')
            ->whereDate('start_date', '<=', today())->whereDate('end_date', '>=', today())
            ->pluck('employee_id')->unique();
        $activeEmployees = $employees->where('status', 'Aktif')->reject(fn (Employee $employee) => $activeLeaveIds->contains($employee->id))->count();
        $shiftToday = $employees->whereIn('shift', ['Pagi', 'Siang', 'Malam'])->where('status', 'Aktif')->reject(fn (Employee $employee) => $activeLeaveIds->contains($employee->id))->count();
        $onLeave = $employees->where('status', 'Cuti')->count() + $employees->where('status', 'Aktif')->filter(fn (Employee $employee) => $activeLeaveIds->contains($employee->id))->count();

        $kitchenUserIds = $employees->where('position', 'Kitchen')->pluck('user_id')->filter()->values();
        $completionAverages = Order::whereIn('kitchen_completed_by', $kitchenUserIds)
            ->whereNotNull('kitchen_started_at')->whereNotNull('kitchen_completed_at')
            ->get(['kitchen_completed_by', 'kitchen_started_at', 'kitchen_completed_at'])
            ->groupBy('kitchen_completed_by')
            ->map(fn ($orders) => round($orders->avg(fn ($order) => max(0, $order->kitchen_started_at->diffInSeconds($order->kitchen_completed_at) / 60)), 1));

        $employees->each(function (Employee $employee) use ($completionAverages, $activeLeaveIds) {
            $employee->setAttribute('average_completion_minutes', $completionAverages->get($employee->user_id));
            $employee->setAttribute('display_status', $activeLeaveIds->contains($employee->id) ? 'Cuti' : $employee->status);
        });

        return view('owner.employees', compact('employees', 'totalEmployees', 'activeEmployees', 'shiftToday', 'onLeave'));
    }

    public function create()
    {
        return view('owner.employees_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'position' => ['required', Rule::in(['Kasir', 'Kitchen'])],
            'shift' => ['required', Rule::in(['Pagi', 'Siang', 'Malam'])],
            'status' => ['required', Rule::in(['Aktif', 'Cuti', 'Nonaktif'])],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
        ]);

        $temporaryPassword = $this->defaultPasswordFor($validated['position']);
        $username = $this->availableUsername($validated['name']);

        DB::transaction(function () use ($validated, $temporaryPassword, $username) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $username,
                'email' => strtolower($validated['email']),
                'password' => $temporaryPassword,
                'role' => $this->roleFor($validated['position'], $validated['status']),
                'phone' => $validated['phone'],
            ]);

            Employee::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'position' => $validated['position'],
                'shift' => $validated['shift'],
                'status' => $validated['status'],
                'phone' => $validated['phone'],
            ]);
        });

        return redirect()->route('owner.employees.index')->with('success', 'Karyawan dan akun login berhasil dibuat.')
            ->with('created_account', [
                'name' => $validated['name'],
                'username' => $username,
                'email' => strtolower($validated['email']),
                'password' => $temporaryPassword,
            ]);
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');
        return view('owner.employees_edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $employee->load('user');
        $userId = $employee->user?->id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'position' => ['required', Rule::in(['Kasir', 'Kitchen'])],
            'shift' => ['required', Rule::in(['Pagi', 'Siang', 'Malam'])],
            'status' => ['required', Rule::in(['Aktif', 'Cuti', 'Nonaktif'])],
            'phone' => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($userId)],
        ]);

        $createdCredentials = null;
        DB::transaction(function () use ($employee, $validated, &$createdCredentials) {
            $user = $employee->user;
            if (!$user) {
                $username = $this->availableUsername($validated['name']);
                $password = $this->defaultPasswordFor($validated['position']);
                $user = User::create([
                    'name' => $validated['name'],
                    'username' => $username,
                    'password' => $password,
                    'email' => strtolower($validated['email']),
                    'phone' => $validated['phone'],
                    'role' => $this->roleFor($validated['position'], $validated['status']),
                ]);
                $createdCredentials = [
                    'name' => $validated['name'],
                    'username' => $username,
                    'email' => strtolower($validated['email']),
                    'password' => $password,
                ];
                $employee->user_id = $user->id;
            }

            $user->update([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'phone' => $validated['phone'],
                'role' => $this->roleFor($validated['position'], $validated['status']),
            ]);

            $employee->update([
                'name' => $validated['name'],
                'position' => $validated['position'],
                'shift' => $validated['shift'],
                'status' => $validated['status'],
                'phone' => $validated['phone'],
                'user_id' => $user->id,
            ]);
        });

        $redirect = redirect()->route('owner.employees.index')->with('success', 'Data karyawan dan hak akses berhasil diperbarui.');
        return $createdCredentials ? $redirect->with('created_account', $createdCredentials) : $redirect;
    }

    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            if ($employee->user) {
                // Nonaktifkan login tanpa menghapus jejak transaksi yang memakai akun ini.
                $employee->user->update(['role' => 'nonaktif']);
            }
            $employee->delete();
        });

        return redirect()->route('owner.employees.index')->with('success', 'Data karyawan dihapus dan akses login dinonaktifkan.');
    }

    public function resetPassword(Employee $employee)
    {
        $employee->load('user');
        abort_unless($employee->user, 404, 'Akun login karyawan belum tersedia.');

        $password = $this->defaultPasswordFor($employee->position);
        $employee->user->update(['password' => $password]);

        return redirect()->route('owner.employees.index')
            ->with('success', 'Kata sandi akun berhasil diatur ulang ke kata sandi awal.')
            ->with('created_account', [
                'name' => $employee->name,
                'username' => $employee->user->username,
                'email' => $employee->user->email,
                'password' => $password,
            ]);
    }

    private function roleFor(string $position, string $status): string
    {
        if ($status === 'Nonaktif') {
            return 'nonaktif';
        }

        return $position === 'Kasir' ? 'kasir' : 'kitchen';
    }

    private function defaultPasswordFor(string $position): string
    {
        $role = $position === 'Kasir' ? 'kasir' : 'kitchen';

        return (string) config("app.employee_default_passwords.{$role}");
    }

    private function availableUsername(string $name): string
    {
        $base = Str::lower(Str::slug(Str::ascii($name), '.')) ?: 'karyawan';
        $username = $base;
        $suffix = 2;

        while (User::where('username', $username)->exists()) {
            $username = $base . '.' . $suffix++;
        }

        return $username;
    }
}
