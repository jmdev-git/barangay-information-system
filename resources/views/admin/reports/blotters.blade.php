<x-layout title="Blotter Incident Report">
    @php $type = 'blotters'; @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Blotter Incident Summary Report</h4>
            <small class="text-muted">{{ $filters }}</small>
        </div>
        <div class="d-flex gap-1">
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'pdf'], request()->only(['date_from','date_to','household_id','status']))) }}"
               class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'xlsx'], request()->only(['date_from','date_to','household_id','status']))) }}"
               class="btn btn-outline-success btn-sm"><i class="bi bi-file-excel"></i> XLSX</a>
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'csv'], request()->only(['date_from','date_to','household_id','status']))) }}"
               class="btn btn-outline-primary btn-sm"><i class="bi bi-filetype-csv"></i> CSV</a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <x-card>
        <form method="GET" action="{{ route('reports.blotters') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Household</label>
                <select name="household_id" class="form-select form-select-sm">
                    <option value="">All Households</option>
                    @foreach($households as $hh)
                        <option value="{{ $hh->id }}" @selected($householdId == $hh->id)>
                            {{ $hh->address }}{{ $hh->purok ? ' (Purok '.$hh->purok.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach(['pending_review','open','closed','resolved','rejected'] as $s)
                        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('reports.blotters') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </x-card>

    {{-- Stats + header (Req 12 AC4) --}}
    <div class="card mb-3" style="background:#fff5f5;border:1px solid #fecaca;">
        <div class="card-body py-3">
            <div class="row text-center">
                <div class="col"><div style="font-size:11px;color:#ef4444;font-weight:700;text-transform:uppercase;">Total</div><div class="fw-bold fs-5">{{ $stats['total'] }}</div></div>
                <div class="col"><div style="font-size:11px;color:#f59e0b;font-weight:700;text-transform:uppercase;">Pending</div><div class="fw-bold">{{ $stats['pending_review'] }}</div></div>
                <div class="col"><div style="font-size:11px;color:#667eea;font-weight:700;text-transform:uppercase;">Open</div><div class="fw-bold">{{ $stats['open'] }}</div></div>
                <div class="col"><div style="font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;">Closed</div><div class="fw-bold">{{ $stats['closed'] }}</div></div>
                <div class="col"><div style="font-size:11px;color:#48bb78;font-weight:700;text-transform:uppercase;">Resolved</div><div class="fw-bold">{{ $stats['resolved'] }}</div></div>
                <div class="col"><div style="font-size:11px;color:#ef4444;font-weight:700;text-transform:uppercase;">Rejected</div><div class="fw-bold">{{ $stats['rejected'] }}</div></div>
                <div class="col"><div style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Generated</div><div style="font-size:11px;" class="fw-semibold">{{ now()->format('M d, Y h:i A') }}</div></div>
                <div class="col-md-4"><div style="font-size:11px;color:#64748b;font-weight:700;text-transform:uppercase;">Filters</div><div style="font-size:11px;">{{ $filters }}</div></div>
            </div>
        </div>
    </div>

    <x-card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <x-table.head>
                    <th>Case #</th><th>Complainant</th><th>Respondent</th>
                    <th>Incident Date</th><th>Location</th><th>Status</th>
                </x-table.head>
                <tbody>
                    @forelse($blotters as $b)
                    @php
                        $bc = ['pending_review'=>'bg-warning text-dark','open'=>'bg-primary','closed'=>'bg-secondary','resolved'=>'bg-success','rejected'=>'bg-danger'];
                        $bl = ['pending_review'=>'Pending Review','open'=>'Open','closed'=>'Closed','resolved'=>'Resolved','rejected'=>'Rejected'];
                    @endphp
                    <x-table.row>
                        <td class="fw-semibold text-muted" style="font-size:12px;">{{ $b->case_number }}</td>
                        <td>{{ $b->complainant_name }}</td>
                        <td>{{ $b->respondent_name }}</td>
                        <td>{{ $b->incident_date?->format('M d, Y') }}</td>
                        <td>{{ \Str::limit($b->location, 40) }}</td>
                        <td><span class="badge {{ $bc[$b->status] ?? 'bg-secondary' }}">{{ $bl[$b->status] ?? ucfirst($b->status) }}</span></td>
                    </x-table.row>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No records matched the filter criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layout>
