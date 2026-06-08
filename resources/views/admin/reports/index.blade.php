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
                    <form method="GET" action="{{ route('reports.residents') }}" class="mb-3">
                        <div class="mb-2">
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                   placeholder="Date From">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                   placeholder="Date To">
                        </div>
                        <div class="mb-3">
                            <input type="text" name="purok" class="form-control form-control-sm"
                                   placeholder="Filter by Purok (e.g. Purok 1)">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="bi bi-eye"></i> View Report
                        </button>
                    </form>
                    <div class="d-flex gap-1">
                        <a href="{{ route('reports.export', ['type'=>'residents','format'=>'pdf']) }}"
                           class="btn btn-outline-danger btn-sm flex-fill">PDF</a>
                        <a href="{{ route('reports.export', ['type'=>'residents','format'=>'xlsx']) }}"
                           class="btn btn-outline-success btn-sm flex-fill">XLSX</a>
                        <a href="{{ route('reports.export', ['type'=>'residents','format'=>'csv']) }}"
                           class="btn btn-outline-primary btn-sm flex-fill">CSV</a>
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
                    <form method="GET" action="{{ route('reports.blotters') }}" class="mb-3">
                        <div class="mb-2">
                            <input type="date" name="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <select name="household_id" class="form-select form-select-sm">
                                <option value="">All Households</option>
                                @foreach($households as $hh)
                                    <option value="{{ $hh->id }}">{{ $hh->address }}{{ $hh->purok ? ' (Purok '.$hh->purok.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="status" class="form-select form-select-sm">
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
                        <a href="{{ route('reports.export', ['type'=>'blotters','format'=>'pdf']) }}"
                           class="btn btn-outline-danger btn-sm flex-fill">PDF</a>
                        <a href="{{ route('reports.export', ['type'=>'blotters','format'=>'xlsx']) }}"
                           class="btn btn-outline-success btn-sm flex-fill">XLSX</a>
                        <a href="{{ route('reports.export', ['type'=>'blotters','format'=>'csv']) }}"
                           class="btn btn-outline-primary btn-sm flex-fill">CSV</a>
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
                    <form method="GET" action="{{ route('reports.clearances') }}" class="mb-3">
                        <div class="mb-2">
                            <input type="date" name="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="date_to" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <select name="household_id" class="form-select form-select-sm">
                                <option value="">All Households</option>
                                @foreach($households as $hh)
                                    <option value="{{ $hh->id }}">{{ $hh->address }}{{ $hh->purok ? ' (Purok '.$hh->purok.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="status" class="form-select form-select-sm">
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
                        <a href="{{ route('reports.export', ['type'=>'clearances','format'=>'pdf']) }}"
                           class="btn btn-outline-danger btn-sm flex-fill">PDF</a>
                        <a href="{{ route('reports.export', ['type'=>'clearances','format'=>'xlsx']) }}"
                           class="btn btn-outline-success btn-sm flex-fill">XLSX</a>
                        <a href="{{ route('reports.export', ['type'=>'clearances','format'=>'csv']) }}"
                           class="btn btn-outline-primary btn-sm flex-fill">CSV</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
