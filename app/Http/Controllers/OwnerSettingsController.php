<?php

namespace App\Http\Controllers;

use App\Services\SiteSettingsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerSettingsController extends Controller
{
    public function edit(SiteSettingsService $settings): View
    {
        return view('owner.settings', [
            'bank' => $settings->bankDetails(),
            'privacyPolicy' => $settings->privacyPolicy(),
        ]);
    }

    public function update(Request $request, SiteSettingsService $settings)
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:80'],
            'bank_account_number' => ['required', 'string', 'max:40'],
            'bank_account_holder' => ['required', 'string', 'max:120'],
        ]);

        $settings->update($validated);

        return redirect()->route('owner.settings')->with('success', 'Informasi rekening transfer berhasil diperbarui.');
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
