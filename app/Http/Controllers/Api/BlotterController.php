<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlotterResource;
use App\Models\Blotter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * API Blotters — admin-only (Req 9 AC1, AC3)
 */
class BlotterController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        $blotters = Blotter::with(['complainant', 'respondent'])
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(min($perPage, 100));

        return BlotterResource::collection($blotters);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'complainant_name'     => 'required|string|min:1|max:255',
            'respondent_name'      => 'required|string|min:1|max:255',
            'incident_date'        => 'required|date|before_or_equal:today',
            'incident_description' => 'required|string|min:1|max:1000',
            'location'             => 'required|string|min:1|max:255',
        ]);

        if (strtolower($validated['complainant_name']) === strtolower($validated['respondent_name'])) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['respondent_name' => ['Complainant and respondent cannot be the same person.']],
            ], 422);
        }

        $blotter = Blotter::create(array_merge($validated, ['status' => 'open']));

        return (new BlotterResource($blotter->load(['complainant', 'respondent'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Blotter $blotter): BlotterResource
    {
        return new BlotterResource($blotter->load(['complainant', 'respondent']));
    }

    public function update(Request $request, Blotter $blotter): BlotterResource|JsonResponse
    {
        $validated = $request->validate([
            'complainant_name'     => 'sometimes|string|min:1|max:255',
            'respondent_name'      => 'sometimes|string|min:1|max:255',
            'incident_date'        => 'sometimes|date|before_or_equal:today',
            'incident_description' => 'sometimes|string|min:1|max:1000',
            'location'             => 'sometimes|string|min:1|max:255',
            // Req 7 AC3 — only valid statuses accepted
            'status'               => 'sometimes|in:pending_review,open,closed,resolved',
        ]);

        // Complainant ≠ respondent (Req 7 AC5)
        $cName = $validated['complainant_name'] ?? $blotter->complainant_name;
        $rName = $validated['respondent_name']  ?? $blotter->respondent_name;
        if (strtolower($cName) === strtolower($rName)) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => ['respondent_name' => ['Complainant and respondent cannot be the same person.']],
            ], 422);
        }

        $blotter->update($validated);

        return new BlotterResource($blotter->load(['complainant', 'respondent']));
    }

    public function destroy(Blotter $blotter): JsonResponse
    {
        $blotter->delete();
        return response()->json(['message' => 'Blotter deleted successfully.'], 200);
    }
}
