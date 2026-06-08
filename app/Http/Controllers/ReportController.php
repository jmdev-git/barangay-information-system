<?php

namespace App\Http\Controllers;

use App\Exports\GenericReportExport;
use App\Models\Blotter;
use App\Models\Clearance;
use App\Models\Household;
use App\Models\Resident;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Web Report Controller (Req 12)
 * - Filters: date_from, date_to, purok (census), household_id (blotter/clearance)
 * - Report header: type, generation timestamp, total count, filters applied
 * - Zero-record exports: include header row + "no records" message row
 * - Formats: PDF, XLSX, CSV, JSON
 */
class ReportController extends Controller
{
    public function index(): View
    {
        $households = Household::orderBy('address')->get(['id', 'address', 'purok']);
        return view('admin.reports.index', compact('households'));
    }

    // -------------------------------------------------------------------------
    // Resident Census Report (Req 12 AC1, AC2)
    // -------------------------------------------------------------------------
    public function residents(Request $request): View
    {
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');
        $purok    = $request->query('purok');

        $residents = Resident::with(['household', 'user'])
            ->when($dateFrom, fn ($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo,   fn ($q) => $q->where('created_at', '<=', $dateTo . ' 23:59:59'))
            ->when($purok,    fn ($q) => $q->whereHas('household', fn ($hq) =>
                $hq->where('purok', 'like', "%{$purok}%")
            ))
            ->latest()
            ->get();

        $filters = $this->buildFilters([
            'Date From'  => $dateFrom,
            'Date To'    => $dateTo,
            'Purok'      => $purok,
        ]);

        return view('admin.reports.residents', compact('residents', 'filters', 'dateFrom', 'dateTo', 'purok'));
    }

    // -------------------------------------------------------------------------
    // Blotter Report (Req 12 AC1, AC2)
    // -------------------------------------------------------------------------
    public function blotters(Request $request): View
    {
        $dateFrom    = $request->query('date_from');
        $dateTo      = $request->query('date_to');
        $householdId = $request->query('household_id');
        $status      = $request->query('status');

        $blotters = Blotter::with(['complainant.household', 'respondent'])
            ->when($dateFrom,    fn ($q) => $q->where('incident_date', '>=', $dateFrom))
            ->when($dateTo,      fn ($q) => $q->where('incident_date', '<=', $dateTo))
            ->when($householdId, fn ($q) => $q->whereHas('complainant', fn ($rq) =>
                $rq->where('household_id', $householdId)
            ))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('incident_date')
            ->get();

        $stats = [
            'total'          => $blotters->count(),
            'pending_review' => $blotters->where('status', 'pending_review')->count(),
            'open'           => $blotters->where('status', 'open')->count(),
            'closed'         => $blotters->where('status', 'closed')->count(),
            'resolved'       => $blotters->where('status', 'resolved')->count(),
            'rejected'       => $blotters->where('status', 'rejected')->count(),
        ];

        $households = Household::orderBy('address')->get(['id', 'address', 'purok']);
        $filters    = $this->buildFilters([
            'Date From'  => $dateFrom,
            'Date To'    => $dateTo,
            'Household'  => $householdId ? Household::find($householdId)?->address : null,
            'Status'     => $status,
        ]);

        return view('admin.reports.blotters', compact(
            'blotters', 'stats', 'filters', 'households',
            'dateFrom', 'dateTo', 'householdId', 'status'
        ));
    }

    // -------------------------------------------------------------------------
    // Clearance Report (Req 12 AC1, AC2)
    // -------------------------------------------------------------------------
    public function clearances(Request $request): View
    {
        $dateFrom    = $request->query('date_from');
        $dateTo      = $request->query('date_to');
        $householdId = $request->query('household_id');
        $status      = $request->query('status');

        $clearances = Clearance::with(['resident.household'])
            ->when($dateFrom,    fn ($q) => $q->where('requested_at', '>=', $dateFrom))
            ->when($dateTo,      fn ($q) => $q->where('requested_at', '<=', $dateTo . ' 23:59:59'))
            ->when($householdId, fn ($q) => $q->whereHas('resident', fn ($rq) =>
                $rq->where('household_id', $householdId)
            ))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('requested_at')
            ->get();

        $stats = [
            'total'    => $clearances->count(),
            'pending'  => $clearances->where('status', 'pending')->count(),
            'approved' => $clearances->where('status', 'approved')->count(),
            'rejected' => $clearances->where('status', 'rejected')->count(),
        ];

        $households = Household::orderBy('address')->get(['id', 'address', 'purok']);
        $filters    = $this->buildFilters([
            'Date From' => $dateFrom,
            'Date To'   => $dateTo,
            'Household' => $householdId ? Household::find($householdId)?->address : null,
            'Status'    => $status,
        ]);

        return view('admin.reports.clearances', compact(
            'clearances', 'stats', 'filters', 'households',
            'dateFrom', 'dateTo', 'householdId', 'status'
        ));
    }

    // -------------------------------------------------------------------------
    // Export — PDF, XLSX, CSV, JSON (Req 12 AC3, AC4)
    // -------------------------------------------------------------------------
    public function export(Request $request)
    {
        $format   = strtolower($request->query('format', 'pdf'));
        $type     = strtolower($request->query('type', 'residents'));
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');
        $purok    = $request->query('purok');
        $hhId     = $request->query('household_id');
        $status   = $request->query('status');

        [$rows, $headings, $keys, $title] = $this->reportPayload($type, $dateFrom, $dateTo, $purok, $hhId, $status);

        // Req 12 AC3 — zero-record export still produces a file
        if ($rows->isEmpty()) {
            $rows = collect([array_fill_keys($keys, 'No records matched the filter criteria.')]);
        }

        $filters     = $this->buildFilters(array_filter([
            'Date From' => $dateFrom, 'Date To' => $dateTo,
            'Purok'     => $purok,    'Status'  => $status,
            'Household' => $hhId ? Household::find($hhId)?->address : null,
        ]));
        $generatedAt = now();
        $totalCount  = $rows->count();
        $filename    = Str::slug($title) . '-' . $generatedAt->format('Y-m-d-His');

        return match ($format) {
            'json' => response()->json([
                'report_type'    => $title,
                'generated_at'   => $generatedAt->toIso8601String(),
                'total_records'  => $totalCount,
                'filters_applied'=> $filters ?: 'No filters applied',
                'data'           => $rows,
            ])->header('Content-Disposition', "attachment; filename={$filename}.json"),

            'csv'  => $this->downloadCsv($rows, $headings, $keys, "{$filename}.csv"),

            'xlsx' => Excel::download(
                new GenericReportExport($rows, $headings, $keys),
                "{$filename}.xlsx"
            ),

            'pdf'  => Pdf::loadView('admin.reports.pdf.generic', [
                'title'       => $title,
                'rows'        => $rows,
                'headings'    => $headings,
                'keys'        => $keys,
                'generatedAt' => $generatedAt,
                'totalCount'  => $totalCount,
                'filters'     => $filters,
            ])->setPaper('a4', 'landscape')->download("{$filename}.pdf"),

            default => back()->with('error', 'Invalid format. Use: pdf, xlsx, csv, json.'),
        };
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Build human-readable filter summary (Req 12 AC4) */
    private function buildFilters(array $raw): string
    {
        $parts = array_filter($raw);
        if (empty($parts)) {
            return 'No filters applied';
        }
        return implode(' | ', array_map(
            fn ($label, $value) => "{$label}: {$value}",
            array_keys($parts),
            $parts
        ));
    }

    private function reportPayload(
        string $type,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $purok,
        ?string $hhId,
        ?string $status
    ): array {
        return match ($type) {
            'blotters' => [
                $this->getBlotterData($dateFrom, $dateTo, $hhId, $status),
                ['Case #', 'Complainant', 'Respondent', 'Incident Date', 'Location', 'Status'],
                ['case_number', 'complainant', 'respondent', 'incident_date', 'location', 'status'],
                'Blotter Incident Summary Report',
            ],
            'clearances' => [
                $this->getClearanceData($dateFrom, $dateTo, $hhId, $status),
                ['ID', 'Resident', 'Household', 'Purpose', 'Status', 'Requested At', 'Issued At'],
                ['id', 'resident', 'household', 'purpose', 'status', 'requested_at', 'issued_at'],
                'Clearance Issuance Report',
            ],
            default => [
                $this->getResidentData($dateFrom, $dateTo, $purok),
                ['ID', 'Full Name', 'Age', 'Gender', 'Civil Status', 'Contact', 'Address', 'Purok', 'Barangay', 'Account Status'],
                ['id', 'full_name', 'age', 'gender', 'civil_status', 'contact', 'address', 'purok', 'barangay', 'account_status'],
                'Resident Census Report',
            ],
        };
    }

    private function getResidentData(?string $from, ?string $to, ?string $purok): Collection
    {
        return Resident::with(['household', 'user'])
            ->when($from,  fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to,    fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
            ->when($purok, fn ($q) => $q->whereHas('household', fn ($hq) =>
                $hq->where('purok', 'like', "%{$purok}%")
            ))
            ->latest()
            ->get()
            ->map(fn ($r) => [
                'id'             => $r->id,
                'full_name'      => $r->full_name,
                'age'            => $r->age ?? 'N/A',
                'gender'         => ucfirst($r->gender ?? 'N/A'),
                'civil_status'   => ucfirst($r->civil_status ?? 'N/A'),
                'contact'        => $r->contact_number ?? 'N/A',
                'address'        => $r->address ?? 'N/A',
                'purok'          => $r->household?->purok ?? 'N/A',
                'barangay'       => $r->household?->barangay ?? 'N/A',
                'account_status' => str_replace('_', ' ', ucfirst($r->user?->status ?? 'N/A')),
            ]);
    }

    private function getBlotterData(?string $from, ?string $to, ?string $hhId, ?string $status): Collection
    {
        return Blotter::with(['complainant', 'respondent'])
            ->when($from,   fn ($q) => $q->where('incident_date', '>=', $from))
            ->when($to,     fn ($q) => $q->where('incident_date', '<=', $to))
            ->when($hhId,   fn ($q) => $q->whereHas('complainant', fn ($rq) =>
                $rq->where('household_id', $hhId)
            ))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('incident_date')
            ->get()
            ->map(fn ($b) => [
                'case_number'   => $b->case_number ?? 'N/A',
                'complainant'   => $b->complainant_name ?? 'N/A',
                'respondent'    => $b->respondent_name  ?? 'N/A',
                'incident_date' => $b->incident_date?->format('Y-m-d') ?? 'N/A',
                'location'      => $b->location ?? 'N/A',
                'status'        => ucfirst(str_replace('_', ' ', $b->status ?? 'N/A')),
            ]);
    }

    private function getClearanceData(?string $from, ?string $to, ?string $hhId, ?string $status): Collection
    {
        return Clearance::with(['resident.household'])
            ->when($from,   fn ($q) => $q->where('requested_at', '>=', $from))
            ->when($to,     fn ($q) => $q->where('requested_at', '<=', $to . ' 23:59:59'))
            ->when($hhId,   fn ($q) => $q->whereHas('resident', fn ($rq) =>
                $rq->where('household_id', $hhId)
            ))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('requested_at')
            ->get()
            ->map(fn ($c) => [
                'id'           => $c->id,
                'resident'     => $c->resident?->full_name ?? 'N/A',
                'household'    => $c->resident?->household?->address ?? 'N/A',
                'purpose'      => $c->purpose ?? 'N/A',
                'status'       => ucfirst($c->status ?? 'N/A'),
                'requested_at' => $c->requested_at?->format('Y-m-d H:i') ?? 'N/A',
                'issued_at'    => $c->issued_at?->format('Y-m-d H:i')    ?? 'N/A',
            ]);
    }

    private function downloadCsv(Collection $rows, array $headings, array $keys, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($rows, $headings, $keys) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headings);
            foreach ($rows as $row) {
                fputcsv($handle, collect($keys)->map(fn ($k) => data_get($row, $k, ''))->toArray());
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
