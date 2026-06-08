<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResidentResource;
use App\Models\Resident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * API Residents — list, show, create, update, delete (Req 9 AC1)
 */
class ResidentController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        $residents = Resident::with(['user', 'household'])
            ->when($request->query('purok'), fn ($q, $v) =>
                $q->whereHas('household', fn ($hq) => $hq->where('purok', 'like', "%{$v}%"))
            )
            ->when($request->query('status'), fn ($q, $v) =>
                $q->whereHas('user', fn ($uq) => $uq->where('status', $v))
            )
            ->paginate(min($perPage, 100));

        return ResidentResource::collection($residents);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'household_id'   => 'nullable|exists:households,id',
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'birth_date'     => 'required|date|before:today',
            'gender'         => 'required|in:male,female',
            'civil_status'   => 'nullable|in:single,married,widowed,separated,annulled',
            'contact_number' => 'required|string|min:7|max:15',
            'address'        => 'required|string|max:500',
        ]);

        $resident = Resident::create($validated);

        return (new ResidentResource($resident->load(['user', 'household'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Resident $resident): ResidentResource
    {
        return new ResidentResource($resident->load(['user', 'household', 'clearances']));
    }

    public function update(Request $request, Resident $resident): ResidentResource
    {
        $validated = $request->validate([
            'household_id'   => 'nullable|exists:households,id',
            'first_name'     => 'sometimes|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'sometimes|string|max:255',
            'birth_date'     => 'sometimes|date|before:today',
            'gender'         => 'sometimes|in:male,female',
            'civil_status'   => 'nullable|in:single,married,widowed,separated,annulled',
            'contact_number' => 'sometimes|string|min:7|max:15',
            'address'        => 'sometimes|string|max:500',
        ]);

        $resident->update($validated);

        return new ResidentResource($resident->load(['user', 'household']));
    }

    public function destroy(Resident $resident): JsonResponse
    {
        $resident->delete();
        return response()->json(['message' => 'Resident deleted successfully.'], 200);
    }
}
