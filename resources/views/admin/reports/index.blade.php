<x-layout title="Reports">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Reports & Data Export</h4>
            <small class="text-muted">Generate and export census, blotter, and clearance reports with filters.</small>
        </div>
    </div>

    {{-- Import Section --}}
    <div class="card mb-4" style="border-top:4px solid #667eea;">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#667eea,#764ba2);
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-file-earmark-arrow-up" style="color:#fff;font-size:1.4rem;"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Import Residents from Excel</h5>
                    <small class="text-muted">
                        Upload an .xlsx file. Required columns:
                        <code>first_name, last_name, birth_date, gender, civil_status, contact_number, address</code>
                        — Optional: <code>middle_name, purok, barangay</code>
                    </small>
                </div>
            </div>
            <form method="POST" action="{{ route('reports.import') }}" enctype="multipart/form-data"
                  class="d-flex align-items-center gap-3 flex-wrap">
                @csrf
                <input type="file" name="import_file" accept=".xlsx,.xls"
                       class="form-control form-control-sm @error('import_file') is-invalid @enderror"
                       style="max-width:320px;" required>
                @error('import_file')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-upload me-1"></i> Import
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Resident Census Report ── --}}
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
                            <small class="text-muted">Filter by date range, purok & gender</small>
                        </div>
                    </div>

                    {{-- View Report form --}}
                    <form method="GET" action="{{ route('reports.residents') }}" class="mb-3" id="resForm">
                        <input type="hidden" name="format" id="r_format" value="">
                        <div class="mb-2">
                            <input type="date" name="date_from" id="r_df" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" id="r_dt" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="text" name="purok" id="r_pu" class="form-control form-control-sm"
                                   placeholder="Filter by Purok (e.g. Zone 1)">
                        </div>
                        <div class="mb-3">
                            <select name="gender" id="r_ge" class="form-select form-select-sm">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <button type="submit" onclick="document.getElementById('r_format').value=''" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="bi bi-eye"></i> View Report
                        </button>
                    </form>

                    {{-- Export buttons — submit same form but to export route --}}
                    <div class="d-flex gap-1">
                        <button type="button" onclick="submitExport('resForm','residents','pdf')"
                                class="btn btn-outline-danger btn-sm flex-fill">
                            <i class="bi bi-file-pdf"></i> PDF
                        </button>
                        <button type="button" onclick="submitExport('resForm','residents','xlsx')"
                                class="btn btn-outline-success btn-sm flex-fill">
                            <i class="bi bi-file-excel"></i> XLSX
                        </button>
                        <button type="button" onclick="submitExport('resForm','residents','csv')"
                                class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-filetype-csv"></i> CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Blotter Incidents Report ── --}}
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
                            <small class="text-muted">Filter by date range & status</small>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('reports.blotters') }}" class="mb-3" id="bltForm">
                        <div class="mb-2">
                            <input type="date" name="date_from" id="b_df" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" id="b_dt" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <select name="household_id" id="b_hh" class="form-select form-select-sm">
                                <option value="">All Households</option>
                                @foreach($households as $hh)
                                    <option value="{{ $hh->id }}">
                                        {{ $hh->address }}{{ $hh->purok ? ' ('.$hh->purok.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="status" id="b_st" class="form-select form-select-sm">
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
                        <button onclick="submitExport('bltForm','blotters','pdf')" class="btn btn-outline-danger btn-sm flex-fill">
                            <i class="bi bi-file-pdf"></i> PDF
                        </button>
                        <button onclick="submitExport('bltForm','blotters','xlsx')" class="btn btn-outline-success btn-sm flex-fill">
                            <i class="bi bi-file-excel"></i> XLSX
                        </button>
                        <button onclick="submitExport('bltForm','blotters','csv')" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-filetype-csv"></i> CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Clearance Issuances Report ── --}}
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
                            <small class="text-muted">Filter by date range & status</small>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('reports.clearances') }}" class="mb-3" id="clrForm">
                        <div class="mb-2">
                            <input type="date" name="date_from" id="c_df" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" id="c_dt" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <select name="household_id" id="c_hh" class="form-select form-select-sm">
                                <option value="">All Households</option>
                                @foreach($households as $hh)
                                    <option value="{{ $hh->id }}">
                                        {{ $hh->address }}{{ $hh->purok ? ' ('.$hh->purok.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="status" id="c_st" class="form-select form-select-sm">
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
                        <button onclick="submitExport('clrForm','clearances','pdf')" class="btn btn-outline-danger btn-sm flex-fill">
                            <i class="bi bi-file-pdf"></i> PDF
                        </button>
                        <button onclick="submitExport('clrForm','clearances','xlsx')" class="btn btn-outline-success btn-sm flex-fill">
                            <i class="bi bi-file-excel"></i> XLSX
                        </button>
                        <button onclick="submitExport('clrForm','clearances','csv')" class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-filetype-csv"></i> CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layout>

<script>
function submitExport(formId, type, format) {
    const form   = document.getElementById(formId);
    const inputs = form.querySelectorAll('input, select');
    const params = new URLSearchParams({ type, format });

    inputs.forEach(el => {
        if (el.name && el.name !== 'format' && el.value) {
            params.set(el.name, el.value);
        }
    });

    window.location.href = '{{ route("reports.export") }}?' + params.toString();
}
</script>
