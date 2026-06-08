<x-layout title="Resident Census Report">
    @php $type = 'residents'; @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Resident Census Report</h4>
            <small class="text-muted">{{ $filters }}</small>
        </div>
        <div class="d-flex gap-1">
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'pdf'], request()->only(['date_from','date_to','purok']))) }}"
               class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'xlsx'], request()->only(['date_from','date_to','purok']))) }}"
               class="btn btn-outline-success btn-sm"><i class="bi bi-file-excel"></i> XLSX</a>
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'csv'], request()->only(['date_from','date_to','purok']))) }}"
               class="btn btn-outline-primary btn-sm"><i class="bi bi-filetype-csv"></i> CSV</a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <x-card>
        <form method="GET" action="{{ route('reports.residents') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Purok</label>
                <input type="text" name="purok" value="{{ $purok }}" class="form-control form-control-sm"
                       placeholder="e.g. Purok 1">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('reports.residents') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </x-card>

    {{-- Report header (Req 12 AC4) --}}
    <div class="card mb-3" style="background:#f0f6ff;border:1px solid #c7d7f5;">
        <div class="card-body py-3">
            <div class="row text-center">
                <div class="col-md-3">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#667eea;">Report Type</div>
                    <div class="fw-bold">Resident Census Report</div>
                </div>
                <div class="col-md-3">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#667eea;">Generated At</div>
                    <div class="fw-bold">{{ now()->format('M d, Y h:i A') }}</div>
                </div>
                <div class="col-md-3">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#667eea;">Total Records</div>
                    <div class="fw-bold fs-5">{{ $residents->count() }}</div>
                </div>
                <div class="col-md-3">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#667eea;">Filters Applied</div>
                    <div style="font-size:12px;">{{ $filters }}</div>
                </div>
            </div>
        </div>
    </div>

    <x-card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <x-table.head>
                    <th>#</th><th>Full Name</th><th>Age</th><th>Gender</th><th>Civil Status</th>
                    <th>Contact</th><th>Purok</th><th>Barangay</th><th>Account Status</th>
                </x-table.head>
                <tbody>
                    @forelse($residents as $resident)
                    <x-table.row>
                        <td class="text-muted" style="font-size:12px;">{{ $resident->id }}</td>
                        <td class="fw-semibold">{{ $resident->full_name }}</td>
                        <td>{{ $resident->age ?? 'N/A' }}</td>
                        <td>{{ ucfirst($resident->gender) }}</td>
                        <td>{{ ucfirst($resident->civil_status ?? 'N/A') }}</td>
                        <td>{{ $resident->contact_number }}</td>
                        <td>{{ $resident->household?->purok ?? 'N/A' }}</td>
                        <td>{{ $resident->household?->barangay ?? 'N/A' }}</td>
                        <td>
                            @php $st = $resident->user?->status ?? 'N/A'; @endphp
                            <span class="badge {{ $st==='active' ? 'bg-success' : ($st==='pending_verification' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ str_replace('_',' ', ucfirst($st)) }}
                            </span>
                        </td>
                    </x-table.row>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No records matched the filter criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layout>
