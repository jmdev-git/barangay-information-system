<x-layout title="Add Resident">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Add Resident</h4>
            <small class="text-muted">Create a new resident profile and user account. Select or create a household inline.</small>
        </div>
        <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <form action="{{ route('residents.store') }}" method="POST" id="residentForm">
        @csrf

        <div class="row g-4">
            {{-- LEFT: Account + Resident Info --}}
            <div class="col-md-7">
                {{-- Account --}}
                <x-card title="Account Credentials">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                            <input type="text" name="user_name" value="{{ old('user_name') }}"
                                   class="form-control @error('user_name') is-invalid @enderror"
                                   placeholder="e.g. Juan Dela Cruz" required>
                            @error('user_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="email@example.com" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </x-card>

                {{-- Resident Profile --}}
                <x-card title="Resident Profile">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="form-control @error('first_name') is-invalid @enderror" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                   class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="form-control @error('last_name') is-invalid @enderror" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                   class="form-control @error('birth_date') is-invalid @enderror"
                                   max="{{ now()->subDay()->toDateString() }}" required>
                            @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="">Select</option>
                                <option value="male"   @selected(old('gender')==='male')>Male</option>
                                <option value="female" @selected(old('gender')==='female')>Female</option>
                                
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Civil Status <span class="text-danger">*</span></label>
                            <select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required>
                                <option value="">Select</option>
                                @foreach(['single','married','widowed','separated','annulled'] as $cs)
                                    <option value="{{ $cs }}" @selected(old('civil_status')===$cs)>{{ ucfirst($cs) }}</option>
                                @endforeach
                            </select>
                            @error('civil_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                                   class="form-control @error('contact_number') is-invalid @enderror"
                                   placeholder="09XXXXXXXXX" required>
                            @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Relationship to Head</label>
                            <input type="text" name="relationship_to_head" value="{{ old('relationship_to_head') }}"
                                   class="form-control" placeholder="Head / Spouse / Child / etc.">
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- RIGHT: Household --}}
            <div class="col-md-5">
                <x-card title="Household Assignment" subtitle="Required — select existing or create new (Req 4)">
                    {{-- Toggle --}}
                    <div class="btn-group w-100 mb-3" role="group">
                        <input type="radio" class="btn-check" name="household_mode" id="mode_existing"
                               value="existing" {{ old('household_mode','existing')==='existing' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="mode_existing">
                            <i class="bi bi-search"></i> Select Existing
                        </label>
                        <input type="radio" class="btn-check" name="household_mode" id="mode_new"
                               value="new" {{ old('household_mode')==='new' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success" for="mode_new">
                            <i class="bi bi-house-add"></i> Create New
                        </label>
                    </div>

                    {{-- Existing --}}
                    <div id="panel_existing" style="display:none;">
                        <label class="form-label fw-semibold">Select Household <span class="text-danger">*</span></label>
                        <select name="household_id" id="household_id"
                                class="form-select @error('household_id') is-invalid @enderror">
                            <option value="">— Choose a household —</option>
                            @foreach($households as $hh)
                                <option value="{{ $hh->id }}"
                                        @selected(old('household_id')==$hh->id)
                                        data-address="{{ $hh->address }}"
                                        data-purok="{{ $hh->purok }}">
                                    #{{ $hh->id }} — {{ $hh->address }}
                                    @if($hh->purok) (Purok {{ $hh->purok }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('household_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="hh_preview" class="mt-2 p-2 bg-light rounded" style="display:none; font-size:13px;"></div>
                    </div>

                    {{-- New inline --}}
                    <div id="panel_new" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Street / House Address <span class="text-danger">*</span></label>
                            <input type="text" name="new_address" value="{{ old('new_address') }}"
                                   class="form-control @error('new_address') is-invalid @enderror"
                                   placeholder="e.g. 12 Rizal St., Purok 1">
                            @error('new_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Purok / Zone</label>
                                <input type="text" name="new_purok" value="{{ old('new_purok') }}"
                                       class="form-control" placeholder="Purok 3">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Barangay <span class="text-danger">*</span></label>
                                <input type="text" name="new_barangay" value="{{ old('new_barangay','Centro') }}"
                                       class="form-control @error('new_barangay') is-invalid @enderror">
                                @error('new_barangay')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </x-card>

                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('residents.index') }}" class="btn btn-secondary flex-fill">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-person-check"></i> Save Resident
                    </button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function toggleHouseholdPanel() {
            const mode = document.querySelector('input[name="household_mode"]:checked')?.value;
            document.getElementById('panel_existing').style.display = mode === 'existing' ? 'block' : 'none';
            document.getElementById('panel_new').style.display      = mode === 'new'      ? 'block' : 'none';
        }
        document.querySelectorAll('input[name="household_mode"]').forEach(r => r.addEventListener('change', toggleHouseholdPanel));
        toggleHouseholdPanel();

        // Household preview
        document.getElementById('household_id')?.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const preview = document.getElementById('hh_preview');
            if (this.value) {
                preview.style.display = 'block';
                preview.innerHTML = `<i class="bi bi-house-fill text-primary"></i> <strong>${opt.dataset.address}</strong>`
                    + (opt.dataset.purok ? ` — Purok ${opt.dataset.purok}` : '');
            } else {
                preview.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-layout>
