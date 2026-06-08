<x-layout title="Edit Blotter">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card title="Update Blotter Record">
                <form action="{{ route('blotters.update', $blotter) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Complainant</label>
                            <input type="text" name="complainant_name" value="{{ old('complainant_name', $blotter->complainant_name ?? trim(($blotter->complainant?->first_name ?? '') . ' ' . ($blotter->complainant?->last_name ?? ''))) }}" class="form-control @error('complainant_name') is-invalid @enderror" placeholder="Type complainant name" required>
                            @error('complainant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Respondent</label>
                            <input type="text" name="respondent_name" value="{{ old('respondent_name', $blotter->respondent_name ?? trim(($blotter->respondent?->first_name ?? '') . ' ' . ($blotter->respondent?->last_name ?? ''))) }}" class="form-control @error('respondent_name') is-invalid @enderror" placeholder="Type respondent name" required>
                            @error('respondent_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Incident Date</label>
                        <input type="date" name="incident_date" value="{{ old('incident_date', $blotter->incident_date?->format('Y-m-d')) }}" class="form-control @error('incident_date') is-invalid @enderror" required>
                        @error('incident_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" value="{{ old('location', $blotter->location) }}" class="form-control @error('location') is-invalid @enderror" required>
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="open" {{ old('status', $blotter->status) == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="closed" {{ old('status', $blotter->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="resolved" {{ old('status', $blotter->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Incident Description</label>
                        <textarea name="incident_description" rows="4" class="form-control @error('incident_description') is-invalid @enderror" required>{{ old('incident_description', $blotter->incident_description) }}</textarea>
                        @error('incident_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('blotters.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layout>
