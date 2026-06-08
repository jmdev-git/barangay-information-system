<x-layout title="Blotter — {{ $blotter->case_number }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Blotter Record</h4>
            <small class="text-muted">Case No. {{ $blotter->case_number }}</small>
        </div>
        <div class="d-flex gap-2">
            @if($blotter->status !== 'pending_review')
                <a href="{{ route('blotters.edit', $blotter) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
            <a href="{{ route('blotters.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <x-card title="Incident Details">
                @php
                    $badges = ['pending_review'=>'bg-warning text-dark','open'=>'bg-primary','closed'=>'bg-secondary','resolved'=>'bg-success','rejected'=>'bg-danger'];
                    $labels = ['pending_review'=>'Pending Review','open'=>'Open','closed'=>'Closed','resolved'=>'Resolved','rejected'=>'Rejected'];
                @endphp
                <div class="mb-3">
                    <span class="badge {{ $badges[$blotter->status] ?? 'bg-secondary' }} fs-6 px-3 py-2">
                        {{ $labels[$blotter->status] ?? ucfirst($blotter->status) }}
                    </span>
                </div>

                <table class="table table-borderless table-sm">
                    <tr><td class="text-muted fw-semibold" style="width:35%">Case Number</td>
                        <td class="fw-bold">{{ $blotter->case_number }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Complainant</td>
                        <td>
                            {{ $blotter->complainant_name }}
                            @if($blotter->complainant)
                                <a href="{{ route('residents.show', $blotter->complainant) }}"
                                   class="badge bg-light text-dark border ms-1" style="font-size:11px;">View Profile</a>
                            @endif
                        </td></tr>
                    <tr><td class="text-muted fw-semibold">Respondent</td>
                        <td>
                            {{ $blotter->respondent_name }}
                            @if($blotter->respondent)
                                <a href="{{ route('residents.show', $blotter->respondent) }}"
                                   class="badge bg-light text-dark border ms-1" style="font-size:11px;">View Profile</a>
                            @endif
                        </td></tr>
                    <tr><td class="text-muted fw-semibold">Incident Date</td>
                        <td>{{ $blotter->incident_date?->format('M d, Y') }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Location</td>
                        <td>{{ $blotter->location }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Description</td>
                        <td style="white-space:pre-wrap;">{{ $blotter->incident_description }}</td></tr>
                    @if($blotter->rejection_reason)
                    <tr><td class="text-muted fw-semibold text-danger">Rejection Reason</td>
                        <td class="text-danger">{{ $blotter->rejection_reason }}</td></tr>
                    @endif
                    <tr><td class="text-muted fw-semibold">Filed</td>
                        <td>{{ $blotter->created_at->format('M d, Y h:i A') }}</td></tr>
                </table>
            </x-card>
        </div>

        {{-- Approve / Reject panel for pending_review --}}
        @if($blotter->status === 'pending_review')
        <div class="col-md-5">
            <x-card title="Review This Blotter">
                <p class="text-muted" style="font-size:14px;">
                    This blotter was filed online by a resident. Approve to open it as an official case or reject with a reason.
                </p>
                <form action="{{ route('blotters.approve', $blotter) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 fw-bold"
                            onclick="return confirm('Approve this blotter report?')">
                        <i class="bi bi-check-circle"></i> Approve — Set to Open
                    </button>
                </form>
                <hr>
                <form action="{{ route('blotters.reject', $blotter) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror"
                                  rows="4" placeholder="Provide reason for rejection..."
                                  required minlength="1" maxlength="500">{{ old('rejection_reason') }}</textarea>
                        <div class="form-text">The resident will receive this by email.</div>
                        @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-danger w-100 fw-bold">
                        <i class="bi bi-x-circle"></i> Reject Report
                    </button>
                </form>
            </x-card>
        </div>
        @endif
    </div>
</x-layout>
