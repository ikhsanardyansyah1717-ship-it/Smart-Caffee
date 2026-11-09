<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        $branches = Branch::orderBy('name')->get();

        return view('owner.branches', compact('branches'));
    }

    public function select(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
        ]);

        $branch = Branch::where('is_active', true)->findOrFail($data['branch_id']);
        session(['branch_id' => $branch->id]);

        return redirect()->back()->with('success', 'Lokasi ' . $branch->name . ' berhasil dipilih.');
    }

    public function nearest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $branches = Branch::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($branches->isEmpty()) {
            return response()->json([
                'message' => 'Koordinat cabang belum tersedia.',
            ], 422);
        }

        $lat = (float) $data['latitude'];
        $lng = (float) $data['longitude'];

        $nearest = $branches->sortBy(function (Branch $branch) use ($lat, $lng) {
            $branchLat = (float) $branch->latitude;
            $branchLng = (float) $branch->longitude;

            // Jarak Haversine dalam kilometer.
            $dLat = deg2rad($branchLat - $lat);
            $dLng = deg2rad($branchLng - $lng);
            $a = sin($dLat / 2) ** 2
                + cos(deg2rad($lat)) * cos(deg2rad($branchLat))
                * sin($dLng / 2) ** 2;

            return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
        })->first();

        session(['branch_id' => $nearest->id]);

        return response()->json([
            'message' => 'Cabang terdekat berhasil dipilih.',
            'branch' => [
                'id' => $nearest->id,
                'name' => $nearest->name,
                'address' => $nearest->address,
            ],
        ]);
    }
}
