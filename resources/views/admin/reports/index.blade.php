<x-layout title="Reports">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Reports & Data Export</h4>
            <small class="text-muted">Generate and export census, blotter, and clearance reports with filters.</small>
        </div>
    </div>

    <div class="row g-4">
        {{-- Resident Census Report --}}
        <div class="col-md-4">
            <div class="card h-100" style="border-top:4px solid #667eea;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#667eea,#764ba2);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-people-fill" style="color:#fff;font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Resident Census</h5>
                            <small class="text-muted">Filter by date range & purok</small>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('reports.residents') }}" class="mb-3" id="residentFilterForm">
                        <div class="mb-2">
                            <input type="date" name="date_from" id="res_date_from" class="form-control form-control-sm"
                                   placeholder="Date From">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" id="res_date_to" class="form-control form-control-sm"
                                   placeholder="Date To">
                        </div>
                        <div class="mb-2">
                            <input type="text" name="purok" id="res_purok" class="form-control form-control-sm"
                                   placeholder="Filter by Purok (e.g. Purok 1)">
                        </div>
                        <div class="mb-2">
                            <select name="gender" id="res_gender" class="form-select form-select-sm">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="search" id="res_search" class="form-control form-control-sm"
                                   placeholder="Search name, contact...">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="bi bi-eye"></i> View Report
                        </button>
                    </form>
                    <div class="d-flex gap-1">
                        <button type="button" onclick="exportReport('residents','pdf')"
                                class="btn btn-outline-danger btn-sm flex-fill">PDF</button>
                        <button type="button" onclick="exportReport('residents','xlsx')"
                                class="btn btn-outline-success btn-sm flex-fill">XLSX</button>
                        <button type="button" onclick="exportReport('residents','csv')"
                                class="btn btn-outline-primary btn-sm flex-fill">CSV</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Blotter Report --}}
        <div class="col-md-4">
            <div class="card h-100" style="border-top:4px solid #f56565;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#f56565,#c53030);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-journal-text" style="color:#fff;font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Blotter Incidents</h5>
                            <small class="text-muted">Filter by date range & household</small>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('reports.blotters') }}" class="mb-3" id="blotterFilterForm">
                        <div class="mb-2">
                            <input type="date" name="date_from" id="blt_date_from" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" id="blt_date_to" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <select name="household_id" id="blt_household" class="form-select form-select-sm">
                                <option value="">All Households</option>
                                @foreach($households as $hh)
                                    <option value="{{ $hh->id }}">{{ $hh->address }}{{ $hh->purok ? ' (Purok '.$hh->purok.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="status" id="blt_status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="pending_review">Pending Review</option>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                                <option value="resolved">Resolved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm w-100 mb-2">
                            <i class="bi bi-eye"></i> View Report
                        </button>
                    </form>
                    <div class="d-flex gap-1">
                        <button type="button" onclick="exportReport('blotters','pdf')"
                                class="btn btn-outline-danger btn-sm flex-fill">PDF</button>
                        <button type="button" onclick="exportReport('blotters','xlsx')"
                                class="btn btn-outline-success btn-sm flex-fill">XLSX</button>
                        <button type="button" onclick="exportReport('blotters','csv')"
                                class="btn btn-outline-primary btn-sm flex-fill">CSV</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clearance Report --}}
        <div class="col-md-4">
            <div class="card h-100" style="border-top:4px solid #48bb78;">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#48bb78,#276749);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-file-check-fill" style="color:#fff;font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Clearance Issuances</h5>
                            <small class="text-muted">Filter by date range & household</small>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('reports.clearances') }}" class="mb-3" id="clearanceFilterForm">
                        <div class="mb-2">
                            <input type="date" name="date_from" id="clr_date_from" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" id="clr_date_to" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <select name="household_id" id="clr_household" class="form-select form-select-sm">
                                <option value="">All Households</option>
                                @foreach($households as $hh)
                                    <option value="{{ $hh->id }}">{{ $hh->address }}{{ $hh->purok ? ' (Purok '.$hh->purok.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="status" id="clr_status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100 mb-2">
                            <i class="bi bi-eye"></i> View Report
                        </button>
                    </form>
                    <div class="d-flex gap-1">
                        <button type="button" onclick="exportReport('clearances','pdf')"
                                class="btn btn-outline-danger btn-sm flex-fill">PDF</button>
                        <button type="button" onclick="exportReport('clearances','xlsx')"
                                class="btn btn-outline-success btn-sm flex-fill">XLSX</button>
                        <button type="button" onclick="exportReport('clearances','csv')"
                                class="btn btn-outline-primary btn-sm flex-fill">CSV</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

@push('scripts')
<script>
function exportReport(type, format) {
    const base = "{{ route('reports.export') }}";
    const params = new URLSearchParams({ type, format });

    if (type === 'residents') {
        const df = document.getElementById('res_date_from')?.value;
        const dt = document.getElementById('res_date_to')?.value;
        const pu = document.getElementById('res_purok')?.value;
        const ge = document.getElementById('res_gender')?.value;
        const se = document.getElementById('res_search')?.value;
        if (df) params.set('date_from', df);
        if (dt) params.set('date_to', dt);
        if (pu) params.set('purok', pu);
        if (ge) params.set('gender', ge);
        if (se) params.set('search', se);
    } else if (type === 'blotters') {
        const df = document.getElementById('blt_date_from')?.value;
        const dt = document.getElementById('blt_date_to')?.value;
        const hh = document.getElementById('blt_household')?.value;
        const st = document.getElementById('blt_status')?.value;
        if (df) params.set('date_from', df);
        if (dt) params.set('date_to', dt);
        if (hh) params.set('household_id', hh);
        if (st) params.set('status', st);
    } else if (type === 'clearances') {
        const df = document.getElementById('clr_date_from')?.value;
        const dt = document.getElementById('clr_date_to')?.value;
        const hh = document.getElementById('clr_household')?.value;
        const st = document.getElementById('clr_status')?.value;
        if (df) params.set('date_from', df);
        if (dt) params.set('date_to', dt);
        if (hh) params.set('household_id', hh);
        if (st) params.set('status', st);
    }

    window.location.href = base + '?' + params.toString();
}
</script>
@endpush
