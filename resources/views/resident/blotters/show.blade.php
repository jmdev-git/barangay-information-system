<x-layout title="Blotter Report — {{ $blotter->case_number }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Blotter Report</h4>
            <small class="text-muted">Case No. {{ $blotter->case_number }}</small>
        </div>
        <a href="{{ route('resident.blotters.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <x-card>
                @php
                    $statusConfig = [
                        'pending_review' => ['label'=>'Pending Review','class'=>'bg-warning text-dark','icon'=>'bi-hourglass-split'],
                        'open'           => ['label'=>'Open',          'class'=>'bg-primary',          'icon'=>'bi-folder2-open'],
                        'closed'         => ['label'=>'Closed',        'class'=>'bg-secondary',        'icon'=>'bi-folder-check'],
                        'resolved'       => ['label'=>'Resolved',      'class'=>'bg-success',          'icon'=>'bi-check-circle-fill'],
                        'rejected'       => ['label'=>'Rejected',      'class'=>'bg-danger',           'icon'=>'bi-x-circle-fill'],
                    ];
                    $sc = $statusConfig[$blotter->status] ?? ['label'=>ucfirst($blotter->status),'class'=>'bg-secondary','icon'=>'bi-question-circle'];
                @endphp

                <div class="text-center mb-4 pt-2">
                    <span class="badge {{ $sc['class'] }} fs-6 px-3 py-2">
                        <i class="bi {{ $sc['icon'] }}"></i> {{ $sc['label'] }}
                    </span>
                    <div class="text-muted mt-1" style="font-size:13px;">
                        Case No. <strong>{{ $blotter->case_number }}</strong> ·
                        Filed {{ $blotter->created_at->format('M d, Y') }}
                    </div>
                </div>

                <table class="table table-borderless table-sm">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width:35%">Complainant</td>
                            <td>{{ $blotter->complainant_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Respondent</td>
                            <td>{{ $blotter->respondent_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Incident Date</td>
                            <td>{{ $blotter->incident_date?->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Location</td>
                            <td>{{ $blotter->location }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Description</td>
                            <td style="white-space:pre-wrap;">{{ $blotter->incident_description }}</td>
                        </tr>
                        @if($blotter->rejection_reason)
                        <tr>
                            <td class="text-muted fw-semibold text-danger">Rejection Reason</td>
                            <td class="text-danger">{{ $blotter->rejection_reason }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

                @if($blotter->status === 'pending_review')
                    <div class="alert alert-warning mt-3" style="border-radius:.75rem;">
                        <i class="bi bi-info-circle"></i>
                        Your report is currently being reviewed by the barangay admin. You will be notified by email once it has been processed.
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layout>
