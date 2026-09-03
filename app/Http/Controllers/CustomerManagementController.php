<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerManagementController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::query()
            ->where('role', 'customer')
            ->withCount('orders')
            ->when($request->get('q'), function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->get();

        $totalCustomers = User::where('role', 'customer')->count();
        $newThisMonth   = User::where('role', 'customer')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('owner.customers', compact('customers', 'totalCustomers', 'newThisMonth'));
    }

    public function create(): View
    {
        return view('owner.customers-create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCustomer($request);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role']     = 'customer';

        User::create($validated);

        return redirect()
            ->route('owner.customers.index')
            ->with('success', 'Pelanggan "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function edit(User $customer): View
    {
        return view('owner.customers-edit', compact('customer'));
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        $validated = $this->validateCustomer($request, $customer->id);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        return redirect()
            ->route('owner.customers.index')
            ->with('success', 'Data pelanggan "' . $customer->name . '" berhasil diperbarui.');
    }

    public function destroy(User $customer): RedirectResponse
    {
        if ($customer->role !== 'customer') {
            abort(403, 'Akun ini bukan pelanggan.');
        }

        $name = $customer->name;
        $customer->delete();

        return redirect()
            ->route('owner.customers.index')
            ->with('success', 'Pelanggan "' . $name . '" berhasil dihapus.');
    }

    private function validateCustomer(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($ignoreId),
            ],
            'password' => $ignoreId
                ? ['nullable', 'confirmed', Password::min(8)]
                : ['required', 'confirmed', Password::min(8)],
        ]);
    }
}