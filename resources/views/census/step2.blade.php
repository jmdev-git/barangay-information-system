<x-layout title="Census Step 2 — Collect Resident Information">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Census Intake</h4>
            <small class="text-muted">Step 2 of 5 — Collect Resident Information</small>
        </div>
        <form action="{{ route('census.reset') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm"
                    onclick="return confirm('Reset?')">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </form>
    </div>

    @include('census.partials.stepper', ['current' => 2])

    {{-- Household context banner --}}
    @if($household)
    <div class="alert alert-primary d-flex align-items-center gap-3 mb-4" style="border-radius:.75rem;">
        <i class="bi bi-house-check-fill" style="font-size:1.5rem;flex-shrink:0;"></i>
        <div>
            <strong>Household Selected:</strong>
            {{ $household->address }}, Purok {{ $household->purok ?? 'N/A' }}, {{ $household->barangay }}
            <br><small class="text-muted">Head: {{ $household->household_head_name ?? 'To be assigned' }}</small>
        </div>
        <a href="{{ route('census.step1') }}" class="btn btn-sm btn-outline-primary ms-auto">Change</a>
    </div>
    @endif

    <x-card title="Step 2: Collect Resident Information" subtitle="Input resident details — name, birthdate, sex, civil status, relationship.">
        <form method="POST" action="{{ route('census.step2.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $data['resident_info']['first_name'] ?? '') }}"
                           class="form-control @error('first_name') is-invalid @enderror"
                           placeholder="Juan" required>
                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Middle Name</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $data['resident_info']['middle_name'] ?? '') }}"
                           class="form-control" placeholder="Santos">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $data['resident_info']['last_name'] ?? '') }}"
                           class="form-control @error('last_name') is-invalid @enderror"
                           placeholder="Dela Cruz" required>
                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                    <input type="date" name="birth_date"
                           value="{{ old('birth_date', $data['resident_info']['birth_date'] ?? '') }}"
                           class="form-control @error('birth_date') is-invalid @enderror"
                           max="{{ now()->subDay()->toDateString() }}" required>
                    @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Sex <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">Select</option>
                        <option value="male"   @selected(old('gender', $data['resident_info']['gender'] ?? '') === 'male')>Male</option>
                        <option value="female" @selected(old('gender', $data['resident_info']['gender'] ?? '') === 'female')>Female</option>
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Civil Status <span class="text-danger">*</span></label>
                    <select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required>
                        <option value="">Select</option>
                        @foreach(['single','married','widowed','separated','annulled'] as $cs)
                            <option value="{{ $cs }}" @selected(old('civil_status', $data['resident_info']['civil_status'] ?? '') === $cs)>
                                {{ ucfirst($cs) }}
                            </option>
                        @endforeach
                    </select>
                    @error('civil_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Relationship to Head</label>
                    <input type="text" name="relationship_to_head"
                           value="{{ old('relationship_to_head', $data['resident_info']['relationship_to_head'] ?? '') }}"
                           class="form-control" placeholder="e.g. Head, Spouse, Child">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                    <input type="text" name="contact_number"
                           value="{{ old('contact_number', $data['resident_info']['contact_number'] ?? '') }}"
                           class="form-control @error('contact_number') is-invalid @enderror"
                           placeholder="09XXXXXXXXX" required minlength="7" maxlength="15">
                    @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('census.step1') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    Proceed to Step 3 — Upload ID <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </form>
    </x-card>
</x-layout>
