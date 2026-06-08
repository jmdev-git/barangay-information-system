<x-layout title="Blotter Management">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Blotter Management</h4>
            <small class="text-muted">All incident records — including resident-submitted blotters pending review.</small>
        </div>
        <a href="{{ route('blotters.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Blotter
        </a>
    </div>

    {{-- Pending review alert --}}
    @php $pendingCount = \App\Models\Blotter::where('status','pending_review')->count(); @endphp
    @if($pendingCount > 0)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4" style="border-radius:.75rem;">
            <i class="bi bi-exclamation-triangle-fill" style="font-size:1.3rem;flex-shrink:0;"></i>
            <div class="flex-grow-1">
                <strong>{{ $pendingCount }} blotter report(s)</strong> submitted by residents are awaiting your review.
            </div>
            <a href="{{ route('blotters.index', ['status'=>'pending_review']) }}" class="btn btn-warning btn-sm fw-bold">
                Review Now <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @endif

    <x-card>
        <form method="GET" action="{{ route('blotters.index') }}" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control"
                       placeholder="Search case number, complainant, respondent, location...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending_review" @selected(($status??'')==='pending_review')>⏳ Pending Review</option>
                    <option value="open"           @selected(($status??'')==='open')>Open</option>
                    <option value="closed"         @selected(($status??'')==='closed')>Closed</option>
                    <option value="resolved"       @selected(($status??'')==='resolved')>Resolved</option>
                    <option value="rejected"       @selected(($status??'')==='rejected')>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('blotters.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <x-table.head>
                    <th>Case No.</th>
                    <th>Complainant</th>
                    <th>Respondent</th>
                    <th>Incident Date</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </x-table.head>
                <tbody>
                    @forelse($blotters as $blotter)
                    @php
                        $badges = [
                            'pending_review' => 'bg-warning text-dark',
                            'open'     => 'bg-primary',
                            'closed'   => 'bg-secondary',
                            'resolved' => 'bg-success',
                            'rejected' => 'bg-danger',
                        ];
                        $labels = [
                            'pending_review' => 'Pending Review',
                            'open'     => 'Open',
                            'closed'   => 'Closed',
                            'resolved' => 'Resolved',
                            'rejected' => 'Rejected',
                        ];
                    @endphp
                    <x-table.row>
                        <td class="fw-semibold text-muted" style="font-size:12px;">{{ $blotter->case_number ?? '—' }}</td>
                        <td>{{ $blotter->complainant_name }}</td>
                        <td>{{ $blotter->respondent_name }}</td>
                        <td>{{ $blotter->incident_date?->format('M d, Y') }}</td>
                        <td>{{ \Str::limit($blotter->location, 35) }}</td>
                        <td>
                            <span class="badge {{ $badges[$blotter->status] ?? 'bg-secondary' }}">
                                {{ $labels[$blotter->status] ?? ucfirst($blotter->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('blotters.show', $blotter) }}" class="btn btn-sm btn-info me-1">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($blotter->status === 'pending_review')
                                {{-- Quick approve --}}
                                <form action="{{ route('blotters.approve', $blotter) }}" method="POST" class="d-inline me-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Approve this blotter report?')">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                {{-- Reject with modal --}}
                                <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectBlotter{{ $blotter->id }}">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            @else
                                <a href="{{ route('blotters.edit', $blotter) }}" class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('blotters.destroy', $blotter) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this blotter record?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </x-table.row>

                    {{-- Reject Blotter Modal --}}
                    <div class="modal fade" id="rejectBlotter{{ $blotter->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius:1rem;overflow:hidden;">
                                <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#b91c1c);border:none;">
                                    <h5 class="modal-title text-white fw-bold">
                                        <i class="bi bi-x-circle"></i> Reject Blotter Report
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('blotters.reject', $blotter) }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <p class="text-muted mb-3">
                                            Rejecting blotter <strong>{{ $blotter->case_number }}</strong> filed by
                                            <strong>{{ $blotter->complainant_name }}</strong>.
                                            They will be notified by email.
                                        </p>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" class="form-control" rows="4"
                                                      placeholder="Provide a reason for rejection..." required minlength="1" maxlength="500"></textarea>
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
                        <tr><td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-journal-x" style="font-size:2rem;opacity:.3;display:block;margin-bottom:8px;"></i>
                            No blotter records found.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
    <div class="d-flex justify-content-center mt-3">{{ $blotters->links() }}</div>
</x-layout>
