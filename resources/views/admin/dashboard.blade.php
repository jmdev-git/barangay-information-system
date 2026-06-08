<x-layout title="Admin Dashboard">
    @php
        $totalRequests = max(($pendingClearances ?? 0) + ($approvedClearances ?? 0) + ($rejectedClearances ?? 0), 1);
        $pendingPercent  = round((($pendingClearances  ?? 0) / $totalRequests) * 100);
        $approvedPercent = round((($approvedClearances ?? 0) / $totalRequests) * 100);
        $rejectedPercent = round((($rejectedClearances ?? 0) / $totalRequests) * 100);
    @endphp

    {{-- Pending approvals alert --}}
    @if(($pendingApprovals ?? 0) > 0)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4" style="border-radius:.75rem;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size:1.4rem;flex-shrink:0;"></i>
            <div class="flex-grow-1">
                <strong>{{ $pendingApprovals }} account(s) are pending verification.</strong>
                Review uploaded government IDs and approve or reject them.
            </div>
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-warning btn-sm fw-bold">
                Review Now <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @endif

    {{-- Stats row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card text-center h-100" style="border-top:4px solid #667eea;">
                <div class="card-body py-3">
                    <i class="bi bi-people-fill" style="font-size:1.8rem;color:#667eea;"></i>
                    <h3 class="fw-bold mb-0 mt-1">{{ $activeResidents ?? 0 }}</h3>
                    <small class="text-muted">Active Residents</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card text-center h-100" style="border-top:4px solid #4299e1;">
                <div class="card-body py-3">
                    <i class="bi bi-houses-fill" style="font-size:1.8rem;color:#4299e1;"></i>
                    <h3 class="fw-bold mb-0 mt-1">{{ $householdCount ?? 0 }}</h3>
                    <small class="text-muted">Households</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card text-center h-100" style="border-top:4px solid #f56565;">
                <div class="card-body py-3">
                    <i class="bi bi-journal-text" style="font-size:1.8rem;color:#f56565;"></i>
                    <h3 class="fw-bold mb-0 mt-1">{{ $openBlotters ?? 0 }}</h3>
                    <small class="text-muted">Open Blotters</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card text-center h-100" style="border-top:4px solid #ed8936;">
                <div class="card-body py-3">
                    <i class="bi bi-hourglass-split" style="font-size:1.8rem;color:#ed8936;"></i>
                    <h3 class="fw-bold mb-0 mt-1">{{ $pendingClearances ?? 0 }}</h3>
                    <small class="text-muted">Pending Clearances</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card text-center h-100" style="border-top:4px solid #48bb78;">
                <div class="card-body py-3">
                    <i class="bi bi-check-circle-fill" style="font-size:1.8rem;color:#48bb78;"></i>
                    <h3 class="fw-bold mb-0 mt-1">{{ $approvedClearances ?? 0 }}</h3>
                    <small class="text-muted">Approved Clearances</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-4 col-6">
            <div class="card text-center h-100" style="border-top:4px solid #f56565;">
                <div class="card-body py-3">
                    <i class="bi bi-x-circle-fill" style="font-size:1.8rem;color:#f56565;"></i>
                    <h3 class="fw-bold mb-0 mt-1">{{ $rejectedClearances ?? 0 }}</h3>
                    <small class="text-muted">Rejected Clearances</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Clearance analytics --}}
        <div class="col-md-6">
            <x-card title="Clearance Request Analytics" subtitle="Status distribution">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:13px;">Pending</span>
                        <strong style="font-size:13px;">{{ $pendingPercent }}%</strong>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-warning" style="width:{{ $pendingPercent }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:13px;">Approved</span>
                        <strong style="font-size:13px;">{{ $approvedPercent }}%</strong>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-success" style="width:{{ $approvedPercent }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:13px;">Rejected</span>
                        <strong style="font-size:13px;">{{ $rejectedPercent }}%</strong>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-danger" style="width:{{ $rejectedPercent }}%"></div>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Quick actions --}}
        <div class="col-md-6">
            <x-card title="Quick Actions">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-warning w-100 text-start">
                            <i class="bi bi-person-check"></i> Review Accounts
                            @if(($pendingApprovals ?? 0) > 0)
                                <span class="badge bg-dark ms-1">{{ $pendingApprovals }}</span>
                            @endif
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('census.index') }}" class="btn btn-primary w-100 text-start">
                            <i class="bi bi-clipboard2-data"></i> Census Intake
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('residents.create') }}" class="btn btn-outline-primary w-100 text-start">
                            <i class="bi bi-person-plus"></i> Add Resident
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('households.create') }}" class="btn btn-outline-info w-100 text-start">
                            <i class="bi bi-house-add"></i> Add Household
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('blotters.create') }}" class="btn btn-outline-danger w-100 text-start">
                            <i class="bi bi-journal-plus"></i> New Blotter
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-success w-100 text-start">
                            <i class="bi bi-download"></i> Export Reports
                        </a>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-layout>
