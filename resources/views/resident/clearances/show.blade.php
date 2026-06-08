<x-layout title="Clearance Details">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Clearance Request Details</h4>
            <p class="text-muted mb-0">View the status and details of your clearance request.</p>
        </div>
        <a href="{{ route('clearances.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <x-card title="Request Information">
                <div class="mb-4">
                    <small class="text-muted d-block fw-bold text-uppercase mb-2">Status</small>
                    @if($clearance->status === 'pending')
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                            <i class="bi bi-hourglass-split"></i> Pending
                        </span>
                    @elseif($clearance->status === 'approved')
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="bi bi-check-circle-fill"></i> Approved
                        </span>
                    @elseif($clearance->status === 'rejected')
                        <span class="badge bg-danger px-3 py-2 fs-6">
                            <i class="bi bi-x-circle-fill"></i> Rejected
                        </span>
                    @endif
                </div>

                <div class="mb-4">
                    <strong class="d-block text-secondary mb-2">Purpose:</strong>
                    <p class="bg-light p-3 rounded border mb-0">{{ $clearance->purpose }}</p>
                </div>

                @if($clearance->rejection_reason)
                <div class="mb-4">
                    <div class="alert alert-danger" style="border-radius:.75rem;">
                        <strong><i class="bi bi-exclamation-triangle-fill"></i> Rejection Reason:</strong>
                        <p class="mb-0 mt-1">{{ $clearance->rejection_reason }}</p>
                    </div>
                </div>
                @endif

                <div class="row text-center bg-light m-0 p-3 rounded border mb-4">
                    <div class="col-sm-6 border-end">
                        <strong class="text-muted d-block">Date Requested:</strong>
                        {{ $clearance->requested_at?->format('M d, Y — h:i A') ?? 'N/A' }}
                    </div>
                    <div class="col-sm-6">
                        <strong class="text-muted d-block">Date Approved:</strong>
                        {{ $clearance->issued_at?->format('M d, Y — h:i A') ?? 'Not yet issued' }}
                    </div>
                </div>

                {{-- Download button (Req 5 AC4) --}}
                @if($clearance->status === 'approved')
                    <div class="d-grid">
                        <a href="{{ route('clearances.download', $clearance) }}"
                           class="btn btn-success btn-lg fw-bold">
                            <i class="bi bi-download"></i> Download Clearance Certificate (PDF)
                        </a>
                    </div>
                @elseif($clearance->status === 'pending')
                    <div class="alert alert-info" style="border-radius:.75rem;">
                        <i class="bi bi-info-circle"></i>
                        Your clearance is pending review. You will receive an email notification once it has been processed.
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layout>
