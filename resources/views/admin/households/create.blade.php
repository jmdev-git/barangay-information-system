<x-layout title="Add Household">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <x-card title="New Household">
                <form action="{{ route('households.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Head of Household</label>
                        <input type="text" name="household_head_name" value="{{ old('household_head_name') }}" class="form-control @error('household_head_name') is-invalid @enderror" placeholder="Type household head name">
                        @error('household_head_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Barangay <span class="text-danger">*</span></label>
                            <input type="text" name="barangay" value="{{ old('barangay','Centro') }}"
                                   class="form-control @error('barangay') is-invalid @enderror" required>
                            @error('barangay')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purok / Zone</label>
                            <input type="text" name="purok" value="{{ old('purok') }}"
                                   class="form-control @error('purok') is-invalid @enderror" placeholder="e.g. Purok 3">
                            @error('purok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Members</label>
                            <input type="text" class="form-control" value="0" readonly>
                            <small class="text-muted">Auto-updated when residents are assigned.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('households.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Household</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layout>
