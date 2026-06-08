<x-layout title="Pending Account Approvals">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Pending Account Approvals</h4>
            <small class="text-muted">Review uploaded government IDs and approve or reject resident registrations.</small>
        </div>
        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
            <i class="bi bi-hourglass-split"></i> {{ $pendingUsers->total() }} Pending
        </span>
    </div>

    <x-card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <x-table.head>
                    <th>Resident Name</th>
                    <th>Email</th>
                    <th>Submitted</th>
                    <th>ID Documents</th>
                    <th class="text-center">Actions</th>
                </x-table.head>
                <tbody>
                    @forelse($pendingUsers as $user)
                        @php $docs = $user->resident?->documents ?? collect(); @endphp
                        <x-table.row>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);
                                                display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-person-fill" style="color:#fff;font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        @if($user->resident)
                                            <small class="text-muted">
                                                {{ $user->resident->first_name }} {{ $user->resident->last_name }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <div>{{ $user->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($docs->isEmpty())
                                    <span class="badge bg-secondary">No files</span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($docs as $doc)
                                            <a href="{{ route('admin.documents.show', $doc) }}"
                                               target="_blank"
                                               class="badge bg-primary text-decoration-none"
                                               title="{{ $doc->original_name }}">
                                                <i class="bi {{ $doc->mime_type === 'application/pdf' ? 'bi-file-pdf' : 'bi-image' }}"></i>
                                                {{ $doc->document_type_label }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.accounts.show', $user) }}"
                                   class="btn btn-sm btn-info me-1">
                                    <i class="bi bi-eye"></i> Review
                                </a>
                                <form action="{{ route('admin.accounts.approve', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success me-1"
                                            onclick="return confirm('Approve account for {{ addslashes($user->name) }}?')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal{{ $user->id }}">
                                    <i class="bi bi-x-lg"></i> Reject
                                </button>
                            </td>
                        </x-table.row>

                        {{-- Reject Modal --}}
                        <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius:1rem;overflow:hidden;">
                                    <div class="modal-header" style="background:linear-gradient(135deg,#ef4444,#b91c1c);border:none;">
                                        <h5 class="modal-title text-white fw-bold">
                                            <i class="bi bi-x-circle"></i> Reject Account
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.accounts.reject', $user) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4">
                                            <p class="text-muted mb-3">
                                                You are about to reject the account for
                                                <strong>{{ $user->name }}</strong>. They will be notified by email.
                                            </p>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Rejection Reason <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="rejection_reason" class="form-control" rows="4"
                                                          placeholder="e.g. Uploaded ID is not clearly visible. Please re-submit with a clearer image."
                                                          required minlength="1" maxlength="500"></textarea>
                                                <div class="form-text">1–500 characters required.</div>
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
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-check-circle" style="font-size:2rem;color:#48bb78;display:block;margin-bottom:8px;"></i>
                                No pending accounts — you're all caught up!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="d-flex justify-content-center mt-3">
        {{ $pendingUsers->links() }}
    </div>
</x-layout>
