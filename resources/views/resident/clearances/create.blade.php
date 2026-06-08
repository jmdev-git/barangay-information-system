<x-layout title="Request Clearance">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Request Barangay Clearance</h4>
            <p class="text-muted mb-0">Fill out the purpose of your clearance request.</p>
        </div>
        <a href="{{ route('clearances.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if($hasPending)
        <div class="alert alert-warning d-flex gap-3 align-items-start" style="border-radius:.75rem;">
            <i class="bi bi-exclamation-triangle-fill mt-1" style="flex-shrink:0;"></i>
            <div>
                <strong>You already have a pending clearance request.</strong><br>
                You cannot submit a new request until your existing one has been reviewed.
                <a href="{{ route('clearances.index') }}" class="fw-bold">View your requests</a>.
            </div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-md-8">
                <x-card title="Clearance Request Form">
                    <form action="{{ route('clearances.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="purpose" class="form-label fw-bold">
                                Purpose of Request <span class="text-danger">*</span>
                            </label>
                            <textarea name="purpose" id="purpose" rows="5"
                                      class="form-control @error('purpose') is-invalid @enderror"
                                      placeholder="e.g. Job application requirement, school requirement, postal ID application, business permit..."
                                      required minlength="10" maxlength="500">{{ old('purpose') }}</textarea>
                            <div class="form-text">10–500 characters required.</div>
                            @error('purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="alert alert-info d-flex gap-2 align-items-start" style="border-radius:.75rem;">
                            <i class="bi bi-info-circle-fill mt-1" style="flex-shrink:0;"></i>
                            <div style="font-size:13px;">
                                Once your clearance is <strong>approved</strong>, you will be able to download the official
                                barangay clearance certificate as a PDF directly from this system.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('clearances.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary fw-bold">
                                <i class="bi bi-send"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    @endif
</x-layout>
