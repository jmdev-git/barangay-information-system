<x-layout title="Census Step 4 — Validate Information">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Census Intake</h4>
            <small class="text-muted">Step 4 of 5 — Validate Information</small>
        </div>
    </div>

    @include('census.partials.stepper', ['current' => 4])

    {{-- Duplicate warning banner --}}
    @if($duplicates->isNotEmpty())
        <div class="alert alert-warning d-flex align-items-start gap-3 mb-4" style="border-radius:.75rem;">
            <i class="bi bi-exclamation-triangle-fill mt-1" style="font-size:1.3rem;flex-shrink:0;"></i>
            <div class="flex-grow-1">
                <strong>Potential Duplicate Detected</strong>
                <p class="mb-2 mt-1" style="font-size:14px;">
                    A resident with the same name and date of birth already exists in the system.
                    Please verify before proceeding.
                </p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered bg-white mb-2" style="font-size:13px;">
                        <thead class="table-light">
                            <tr><th>Name</th><th>Birth Date</th><th>Household</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($duplicates as $dup)
                            <tr>
                                <td>{{ $dup->full_name }}</td>
                                <td>{{ $dup->birth_date?->format('M d, Y') }}</td>
                                <td>{{ $dup->household?->address ?? 'Unassigned' }}</td>
                                <td>
                                    @if($dup->user?->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $dup->user?->status ?? 'Census' }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <form method="POST" action="{{ route('census.step4.confirm') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm fw-bold">
                        <i class="bi bi-check2-circle"></i> I Confirm — This Is a Different Person, Proceed Anyway
                    </button>
                </form>
                <a href="{{ route('census.step2') }}" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-pencil"></i> Edit Information
                </a>
            </div>
        </div>
    @endif

    {{-- Summary for confirmation --}}
    <div class="row g-4">
        <div class="col-md-6">
            <x-card title="Resident Information Summary">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr><td class="text-muted fw-semibold" style="width:45%">Full Name</td>
                            <td>{{ $info['first_name'] }} {{ $info['middle_name'] ?? '' }} {{ $info['last_name'] }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Date of Birth</td>
                            <td>{{ \Carbon\Carbon::parse($info['birth_date'])->format('M d, Y') }}
                                (Age {{ \Carbon\Carbon::parse($info['birth_date'])->age }})</td></tr>
                        <tr><td class="text-muted fw-semibold">Sex</td><td>{{ ucfirst($info['gender']) }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Civil Status</td><td>{{ ucfirst($info['civil_status']) }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Contact</td><td>{{ $info['contact_number'] }}</td></tr>
                        @if(!empty($info['relationship_to_head']))
                        <tr><td class="text-muted fw-semibold">Relationship</td><td>{{ $info['relationship_to_head'] }}</td></tr>
                        @endif
                    </tbody>
                </table>
            </x-card>
        </div>
        <div class="col-md-6">
            <x-card title="Household">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr><td class="text-muted fw-semibold" style="width:40%">Address</td>
                            <td>{{ $household->address }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Purok</td><td>{{ $household->purok ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Barangay</td><td>{{ $household->barangay }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Head</td><td>{{ $household->household_head_name ?? 'N/A' }}</td></tr>
                    </tbody>
                </table>
            </x-card>
            <x-card title="Documents Uploaded">
                @foreach($data['id_files'] as $file)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi {{ str_contains($file['mime_type'], 'pdf') ? 'bi-file-pdf-fill text-danger' : 'bi-image-fill text-primary' }}"
                           style="font-size:1.1rem;flex-shrink:0;"></i>
                        <div>
                            <div style="font-size:13px;font-weight:600;">{{ $file['original_name'] }}</div>
                            <small class="text-muted">{{ $data['id_type'] }} · {{ number_format($file['file_size'] / 1024, 1) }} KB</small>
                        </div>
                    </div>
                @endforeach
                @if(!empty($data['photo_path']))
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <i class="bi bi-camera-fill text-success" style="font-size:1.1rem;flex-shrink:0;"></i>
                        <span style="font-size:13px;">Resident photo uploaded</span>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    @if($duplicates->isEmpty())
        {{-- No duplicates — proceed directly --}}
        <div class="alert alert-success d-flex align-items-center gap-3" style="border-radius:.75rem;">
            <i class="bi bi-check-circle-fill" style="font-size:1.3rem;flex-shrink:0;color:#48bb78;"></i>
            <strong>No duplicates found. Information validated successfully.</strong>
        </div>
        <div class="d-flex justify-content-between">
            <a href="{{ route('census.step3') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <form method="POST" action="{{ route('census.step5.save') }}">
                @csrf
                <button type="submit" class="btn btn-success px-5 fw-bold">
                    <i class="bi bi-database-check"></i> Create Profile & Save to BMIS
                </button>
            </form>
        </div>
    @else
        <div class="d-flex mt-2">
            <a href="{{ route('census.step3') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    @endif
</x-layout>
