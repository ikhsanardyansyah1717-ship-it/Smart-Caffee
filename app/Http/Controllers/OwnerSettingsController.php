<?php

namespace App\Http\Controllers;

use App\Services\SiteSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OwnerSettingsController extends Controller
{
    public function edit(SiteSettingsService $settings): View
    {
        return view('owner.settings', [
            'bank' => $settings->bankDetails(),
            'bankAccounts' => $settings->bankAccounts(),
            'privacyPolicy' => $settings->privacyPolicy(),
        ]);
    }

    public function update(Request $request, SiteSettingsService $settings)
    {
        $validated = $request->validate([
            'bank_accounts' => ['required', 'array', 'min:1', 'max:10'],
            'bank_accounts.*.id' => ['nullable', 'string', 'max:50'],
            'bank_accounts.*.bank_name' => ['required', 'string', 'max:80'],
            'bank_accounts.*.bank_account_number' => ['required', 'string', 'max:40', 'regex:/^[0-9]+$/'],
            'bank_accounts.*.bank_account_holder' => ['required', 'string', 'max:120'],
        ]);

        $usedIds = [];
        $accounts = collect($validated['bank_accounts'])->map(function ($account) use (&$usedIds) {
            $id = $account['id'] ?? null;
            if (!filled($id) || in_array($id, $usedIds, true)) {
                $id = (string) Str::uuid();
            }
            $usedIds[] = $id;

            return [
                'id' => $id,
                'bank_name' => trim($account['bank_name']),
                'bank_account_number' => trim($account['bank_account_number']),
                'bank_account_holder' => trim($account['bank_account_holder']),
            ];
        })->values()->all();
        $firstAccount = $accounts[0];
        $settings->update([
            'bank_accounts' => $accounts,
            // Simpan juga rekening pertama untuk kompatibilitas dengan integrasi lama.
            'bank_name' => $firstAccount['bank_name'],
            'bank_account_number' => $firstAccount['bank_account_number'],
            'bank_account_holder' => $firstAccount['bank_account_holder'],
        ]);

        return redirect()->route('owner.settings')->with('success', 'Daftar rekening transfer berhasil diperbarui.');
    }

    public function updatePrivacyPolicy(Request $request, SiteSettingsService $settings)
    {
        $validated = $request->validate([
            'privacy_policy' => ['required', 'string', 'max:20000'],
        ]);

        $settings->update($validated);

        return redirect()->route('owner.settings')->with('success', 'Privacy Policy berhasil diperbarui.');
    }
}
