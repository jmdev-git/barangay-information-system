<x-layout title="File a Blotter Report">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">File a Blotter Report</h4>
            <small class="text-muted">Submit an incident report online. A barangay admin will review your submission.</small>
        </div>
        <a href="{{ route('resident.blotters.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card title="Incident Report Form">
                <form method="POST" action="{{ route('resident.blotters.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">
                                Respondent Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="respondent_name"
                                   value="{{ old('respondent_name') }}"
                                   class="form-control @error('respondent_name') is-invalid @enderror"
                                   placeholder="Full name of the person being reported"
                                   required maxlength="255">
                            @error('respondent_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                Incident Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="incident_date"
                                   value="{{ old('incident_date') }}"
                                   class="form-control @error('incident_date') is-invalid @enderror"
                                   max="{{ now()->toDateString() }}" required>
                            @error('incident_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Cannot be a future date.</div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold">
                            Incident Location <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="location" value="{{ old('location') }}"
                               class="form-control @error('location') is-invalid @enderror"
                               placeholder="e.g. Purok 2, near the community hall" required maxlength="500">
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Incident Description <span class="text-danger">*</span>
                        </label>
                        <textarea name="incident_description" rows="6"
                                  class="form-control @error('incident_description') is-invalid @enderror"
                                  placeholder="Describe what happened in detail..."
                                  required maxlength="2000">{{ old('incident_description') }}</textarea>
                        <div class="form-text">Maximum 2,000 characters.</div>
                        @error('incident_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-info d-flex gap-2 align-items-start" style="border-radius:.75rem;">
                        <i class="bi bi-info-circle-fill mt-1" style="flex-shrink:0;"></i>
                        <div style="font-size:13px;">
                            Your report will be submitted as <strong>Pending Review</strong>.
                            The barangay admin will review it and notify you once it has been processed.
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('resident.blotters.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="bi bi-send"></i> Submit Blotter Report
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layout>
