<x-layout title="Resident Census Report">
    @php $type = 'residents'; @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Resident Census Report</h4>
            <small class="text-muted">{{ $filters }}</small>
        </div>
        <div class="d-flex gap-1">
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'pdf'], request()->only(['date_from','date_to','purok','gender','search']))) }}"
               class="btn btn-outline-danger btn-sm"><i class="bi bi-file-pdf"></i> PDF</a>
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'xlsx'], request()->only(['date_from','date_to','purok','gender','search']))) }}"
               class="btn btn-outline-success btn-sm"><i class="bi bi-file-excel"></i> XLSX</a>
            <a href="{{ route('reports.export', array_merge(['type'=>$type,'format'=>'csv'], request()->only(['date_from','date_to','purok','gender','search']))) }}"
               class="btn btn-outline-primary btn-sm"><i class="bi bi-filetype-csv"></i> CSV</a>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <x-card>
        <form method="GET" action="{{ route('reports.residents') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Date From</label>
                <input type="date" name="date_from" id="dateFrom" value="{{ $dateFrom }}" class="form-control form-control-sm"
                       onchange="validateDateRange()">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Date To</label>
                <input type="date" name="date_to" id="dateTo" value="{{ $dateTo }}" class="form-control form-control-sm"
                       onchange="validateDateRange()">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Purok</label>
                <input type="text" name="purok" value="{{ $purok }}" class="form-control form-control-sm"
                       placeholder="e.g. Purok 1">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Gender</label>
                <select name="gender" class="form-select form-select-sm">
                    <option value="">All Genders</option>
                    <option value="male"   @selected(($gender??'')==='male')>Male</option>
                    <option value="female" @selected(($gender??'')==='female')>Female</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold mb-1" style="font-size:12px;">Search</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm"
                       placeholder="Name or contact...">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" id="filterBtn" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('reports.residents') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
        {{-- Date range warning --}}
        <div id="dateWarning" class="alert alert-warning py-2 mt-2 mb-0" style="display:none;font-size:13px;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <strong>Invalid date range:</strong> "Date To" must be equal to or after "Date From".
        </div>
    </x-card>

    {{-- Active filter banner — shows date range prominently (Req 12) --}}
    @if($dateFrom || $dateTo || $purok || ($gender ?? '') || ($search ?? ''))
    <div class="alert d-flex align-items-center gap-2 mb-3 py-2"
         style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:.6rem;">
        <i class="bi bi-funnel-fill text-primary"></i>
        <div style="font-size:13px;">
            <strong>Active Filters:</strong>
            @if($dateFrom || $dateTo)
                <span class="badge bg-primary ms-1">
                    📅 {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : 'Start' }}
                    → {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : 'Now' }}
                </span>
            @endif
            @if($purok)
                <span class="badge bg-secondary ms-1">📍 Purok: {{ $purok }}</span>
            @endif
            @if($gender ?? '')
                <span class="badge bg-info ms-1">⚥ {{ ucfirst($gender) }}</span>
            @endif
            @if($search ?? '')
                <span class="badge bg-warning text-dark ms-1">🔍 "{{ $search }}"</span>
            @endif
            <span class="text-muted ms-2" style="font-size:12px;">— {{ $residents->count() }} record(s) shown</span>
        </div>
    </div>
    @endif

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

<script>
function validateDateRange() {
    const from    = document.getElementById('dateFrom').value;
    const to      = document.getElementById('dateTo').value;
    const warning = document.getElementById('dateWarning');
    const btn     = document.getElementById('filterBtn');

    if (from && to && from > to) {
        warning.style.display = 'flex';
        btn.disabled = true;
        // Auto-fix: set date_to to match date_from
        document.getElementById('dateTo').style.borderColor = '#ef4444';
    } else {
        warning.style.display = 'none';
        btn.disabled = false;
        document.getElementById('dateTo').style.borderColor = '';
    }
}

// Run on page load to catch server-returned invalid ranges
document.addEventListener('DOMContentLoaded', validateDateRange);
</script>
