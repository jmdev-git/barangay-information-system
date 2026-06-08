<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\Clearance;
use App\Models\Resident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Reports — generate and export (Req 9 AC1)
 * Supports JSON responses with filters. Export formats (PDF/XLSX) are handled by the web layer.
 */
class ReportController extends Controller
{
    /**
     * GET /api/reports/residents
     * Filters: date_from, date_to, purok
     */
    public function residents(Request $request): JsonResponse
    {
        $query = Resident::with(['household', 'user'])
            ->when($request->date_from, fn ($q, $v) =>
                $q->where('created_at', '>=', $v)
            )
            ->when($request->date_to, fn ($q, $v) =>
                $q->where('created_at', '<=', $v . ' 23:59:59')
            )
            ->when($request->purok, fn ($q, $v) =>
                $q->whereHas('household', fn ($hq) => $hq->where('purok', 'like', "%{$v}%"))
            );

        $residents = $query->get();

        $filtersApplied = array_filter([
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
            'purok'     => $request->purok,
        ]);

        return response()->json([
            'report_type'      => 'Resident Census Report',
            'generated_at'     => now()->toIso8601String(),
            'total_records'    => $residents->count(),
            'filters_applied'  => $filtersApplied ?: 'No filters applied',
            'data'             => $residents->map(fn ($r) => [
                'id'             => $r->id,
                'full_name'      => $r->full_name,
                'age'            => $r->age,
                'gender'         => $r->gender,
                'civil_status'   => $r->civil_status,
                'contact_number' => $r->contact_number,
                'address'        => $r->address,
                'purok'          => $r->household?->purok,
                'barangay'       => $r->household?->barangay,
                'account_status' => $r->user?->status,
            ]),
        ]);
    }

    /**
     * GET /api/reports/blotters
     * Filters: date_from, date_to, household_id, status
     */
    public function blotters(Request $request): JsonResponse
    {
        $blotters = Blotter::with(['complainant.household', 'respondent'])
            ->when($request->date_from, fn ($q, $v) =>
                $q->where('incident_date', '>=', $v)
            )
            ->when($request->date_to, fn ($q, $v) =>
                $q->where('incident_date', '<=', $v)
            )
            ->when($request->household_id, fn ($q, $v) =>
                $q->whereHas('complainant', fn ($rq) =>
                    $rq->where('household_id', $v)
                )
            )
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest('incident_date')
            ->get();

        $filtersApplied = array_filter([
            'date_from'    => $request->date_from,
            'date_to'      => $request->date_to,
            'household_id' => $request->household_id,
            'status'       => $request->status,
        ]);

        return response()->json([
            'report_type'     => 'Blotter Incident Summary Report',
            'generated_at'    => now()->toIso8601String(),
            'total_records'   => $blotters->count(),
            'summary'         => [
                'pending_review' => $blotters->where('status', 'pending_review')->count(),
                'open'           => $blotters->where('status', 'open')->count(),
                'closed'         => $blotters->where('status', 'closed')->count(),
                'resolved'       => $blotters->where('status', 'resolved')->count(),
                'rejected'       => $blotters->where('status', 'rejected')->count(),
            ],
            'filters_applied' => $filtersApplied ?: 'No filters applied',
            'data'            => $blotters->map(fn ($b) => [
                'case_number'      => $b->case_number,
                'complainant_name' => $b->complainant_name,
                'respondent_name'  => $b->respondent_name,
                'incident_date'    => $b->incident_date?->toDateString(),
                'location'         => $b->location,
                'status'           => $b->status,
            ]),
        ]);
    }

    /**
     * GET /api/reports/clearances
     * Filters: date_from, date_to, household_id, status
     */
    public function clearances(Request $request): JsonResponse
    {
        $clearances = Clearance::with(['resident.household'])
            ->when($request->date_from, fn ($q, $v) =>
                $q->where('requested_at', '>=', $v)
            )
            ->when($request->date_to, fn ($q, $v) =>
                $q->where('requested_at', '<=', $v . ' 23:59:59')
            )
            ->when($request->household_id, fn ($q, $v) =>
                $q->whereHas('resident', fn ($rq) =>
                    $rq->where('household_id', $v)
                )
            )
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest('requested_at')
            ->get();

        $filtersApplied = array_filter([
            'date_from'    => $request->date_from,
            'date_to'      => $request->date_to,
            'household_id' => $request->household_id,
            'status'       => $request->status,
        ]);

        return response()->json([
            'report_type'     => 'Clearance Issuance Report',
            'generated_at'    => now()->toIso8601String(),
            'total_records'   => $clearances->count(),
            'summary'         => [
                'pending'  => $clearances->where('status', 'pending')->count(),
                'approved' => $clearances->where('status', 'approved')->count(),
                'rejected' => $clearances->where('status', 'rejected')->count(),
            ],
            'filters_applied' => $filtersApplied ?: 'No filters applied',
            'data'            => $clearances->map(fn ($c) => [
                'id'          => $c->id,
                'resident'    => $c->resident?->full_name,
                'household'   => $c->resident?->household?->address,
                'purpose'     => $c->purpose,
                'status'      => $c->status,
                'requested_at'=> $c->requested_at?->toDateString(),
                'issued_at'   => $c->issued_at?->toDateString(),
            ]),
        ]);
    }

    /**
     * GET /api/reports/export
     * Same filters as above, returns JSON array for the requested type.
     */
    public function export(Request $request): JsonResponse
    {
        $type = $request->query('type', 'residents');

        $response = match ($type) {
            'blotters'   => $this->blotters($request),
            'clearances' => $this->clearances($request),
            default      => $this->residents($request),
        };

        return $response;
    }
}
