<x-layout title="Add Blotter">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card title="New Blotter Record">
                <form action="{{ route('blotters.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Complainant</label>
                            <input type="text" name="complainant_name" value="{{ old('complainant_name') }}" class="form-control @error('complainant_name') is-invalid @enderror" placeholder="Type complainant name" required>
                            @error('complainant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Respondent</label>
                            <input type="text" name="respondent_name" value="{{ old('respondent_name') }}" class="form-control @error('respondent_name') is-invalid @enderror" placeholder="Type respondent name" required>
                            @error('respondent_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Incident Date</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date') }}" class="form-control @error('incident_date') is-invalid @enderror" required>
                        @error('incident_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror" required>
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Incident Description</label>
                        <textarea name="incident_description" rows="4" class="form-control @error('incident_description') is-invalid @enderror" required>{{ old('incident_description') }}</textarea>
                        @error('incident_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('blotters.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit Record</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layout>
