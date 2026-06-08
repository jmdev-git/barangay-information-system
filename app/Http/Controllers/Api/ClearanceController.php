<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClearanceResource;
use App\Models\Clearance;
use App\Notifications\ClearanceStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClearanceController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        $clearances = Clearance::with('resident')
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->latest('requested_at')
            ->paginate(min($perPage, 100));

        return ClearanceResource::collection($clearances);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'resident_id' => 'required|exists:residents,id',
            'purpose'     => 'required|string|min:10|max:500',
        ]);

        // Duplicate pending guard (Req 5 AC6)
        $hasPending = Clearance::where('resident_id', $validated['resident_id'])
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['resident_id' => ['A pending clearance request already exists for this resident.']],
            ], 422);
        }

        $clearance = Clearance::create([
            'resident_id'  => $validated['resident_id'],
            'purpose'      => $validated['purpose'],
            'status'       => 'pending',
            'requested_at' => now(),
        ]);

        return (new ClearanceResource($clearance->load('resident')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Clearance $clearance): ClearanceResource
    {
        return new ClearanceResource($clearance->load('resident'));
    }

    public function update(Request $request, Clearance $clearance): ClearanceResource
    {
        $validated = $request->validate([
            'purpose' => 'sometimes|string|min:10|max:500',
        ]);

        $clearance->update($validated);

        return new ClearanceResource($clearance->load('resident'));
    }

    public function destroy(Clearance $clearance): JsonResponse
    {
        $clearance->delete();
        return response()->json(['message' => 'Clearance deleted successfully.'], 200);
    }

    public function approve(Clearance $clearance): ClearanceResource
    {
        $clearance->update([
            'status'      => 'approved',
            'issued_at'   => now(),
            'approved_by' => auth()->id(),
        ]);

        $clearance->resident?->user?->notify(
            new ClearanceStatusNotification($clearance, 'approved')
        );

        return new ClearanceResource($clearance->load('resident'));
    }

    public function reject(Request $request, Clearance $clearance): ClearanceResource|JsonResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:1|max:500',
        ]);

        $clearance->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
        ]);

        $clearance->resident?->user?->notify(
            new ClearanceStatusNotification($clearance, 'rejected', $request->rejection_reason)
        );

        return new ClearanceResource($clearance->load('resident'));
    }
}
