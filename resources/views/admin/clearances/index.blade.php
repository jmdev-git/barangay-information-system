<x-layout title="Clearance Requests">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Clearance Requests</h4>
            <small class="text-muted">Approve or reject resident clearance requests. Rejection requires a reason.</small>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-bar-chart"></i> Reports
        </a>
    </div>

    <x-card>
        <form method="GET" action="{{ route('clearances.admin') }}" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control"
                       placeholder="Search resident name or purpose...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending"  @selected(($status??'')==='pending')>Pending</option>
                    <option value="approved" @selected(($status??'')==='approved')>Approved</option>
                    <option value="rejected" @selected(($status??'')==='rejected')>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('clearances.admin') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <x-table.head>
                    <th>Resident</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Requested</th>
                    <th>Actions</th>
                </x-table.head>
                <tbody>
                    @forelse($clearances as $clearance)
                    <x-table.row>
                        <td>
                            <div class="fw-semibold">
                                {{ $clearance->resident?->full_name ?? 'N/A' }}
                            </div>
                            <small class="text-muted">{{ $clearance->resident?->contact_number }}</small>
                        </td>
                        <td>{{ \Str::limit($clearance->purpose, 60) }}</td>
                        <td>
                            <span class="badge {{ $clearance->status==='approved' ? 'bg-success' : ($clearance->status==='pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ ucfirst($clearance->status) }}
                            </span>
                        </td>
                        <td>{{ $clearance->requested_at?->format('M d, Y') }}</td>
                        <td>
                            @if($clearance->status === 'pending')
                                <form action="{{ route('clearances.approve', $clearance) }}" method="POST" class="d-inline me-1">
                                    @csrf @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success fw-bold"
                                            onclick="return confirm('Approve this clearance?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger fw-bold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectClearance{{ $clearance->id }}">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            @elseif($clearance->status === 'approved')
                                <a href="{{ route('clearances.download', $clearance) }}"
                                   class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-download"></i> Certificate
                                </a>
                            @else
                                <span class="text-muted" style="font-size:13px;">{{ $clearance->rejection_reason ? \Str::limit($clearance->rejection_reason, 30) : 'Rejected' }}</span>
                            @endif
                        </td>
                    </x-table.row>

                    {{-- Reject Modal --}}
                    <div class="modal fade" id="rejectClearance{{ $clearance->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:1rem;overflow:hidden;">
                                <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#b91c1c);border:none;">
                                    <h5 class="modal-title text-white fw-bold">
                                        <i class="bi bi-x-circle"></i> Reject Clearance Request
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('clearances.reject', $clearance) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body p-4">
                                        <p class="text-muted mb-3">
                                            Rejecting clearance for
                                            <strong>{{ $clearance->resident?->full_name }}</strong>.
                                            They will receive the reason by email.
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" class="form-control" rows="4"
                                                      placeholder="e.g. Incomplete requirements submitted..."
                                                      required minlength="1" maxlength="500"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger fw-bold">
                                            <i class="bi bi-send"></i> Send Rejection
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No clearance requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
    <div class="d-flex justify-content-center mt-3">{{ $clearances->links() }}</div>
</x-layout>
