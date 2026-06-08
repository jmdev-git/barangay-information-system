<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HouseholdResource;
use App\Models\Household;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HouseholdController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        $households = Household::with(['head', 'residents'])
            ->when($request->query('purok'), fn ($q, $v) =>
                $q->where('purok', 'like', "%{$v}%")
            )
            ->paginate(min($perPage, 100));

        return HouseholdResource::collection($households);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address'             => 'required|string|max:500',
            'barangay'            => 'required|string|max:100',
            'purok'               => 'nullable|string|max:100',
            'household_head_name' => 'nullable|string|max:255',
        ]);

        $household = Household::create($validated);

        return (new HouseholdResource($household))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Household $household): HouseholdResource
    {
        return new HouseholdResource($household->load(['head', 'residents']));
    }

    public function update(Request $request, Household $household): HouseholdResource
    {
        $validated = $request->validate([
            'address'             => 'sometimes|string|max:500',
            'barangay'            => 'sometimes|string|max:100',
            'purok'               => 'nullable|string|max:100',
            'household_head_name' => 'nullable|string|max:255',
        ]);

        $household->update($validated);

        return new HouseholdResource($household->load(['head', 'residents']));
    }

    public function destroy(Household $household): JsonResponse
    {
        $household->delete();
        return response()->json(['message' => 'Household deleted successfully.'], 200);
    }
}
