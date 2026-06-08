<x-layout title="Edit Resident — {{ $resident->full_name }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Edit Resident</h4>
            <small class="text-muted">Update profile, household assignment, and account status.</small>
        </div>
        <a href="{{ route('residents.show', $resident) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Profile
        </a>
    </div>

    <form action="{{ route('residents.update', $resident) }}" method="POST">
        @csrf @method('PUT')

        <div class="row g-4">
            {{-- LEFT: Account + Profile --}}
            <div class="col-md-7">
                <x-card title="Account">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                            <input type="text" name="user_name"
                                   value="{{ old('user_name', $resident->user?->name ?? $resident->full_name) }}"
                                   class="form-control @error('user_name') is-invalid @enderror" required>
                            @error('user_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email"
                                   value="{{ old('email', $resident->email ?? $resident->user?->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">New Password <span class="text-muted fw-normal">(leave blank to keep)</span></label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 characters">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        @if($resident->user)
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Account Status</label>
                            <select name="status" class="form-select">
                                <option value="active"                @selected(($resident->user->status ?? 'active')==='active')>Active</option>
                                <option value="pending_verification"  @selected(($resident->user->status ?? '')==='pending_verification')>Pending Verification</option>
                                <option value="rejected"              @selected(($resident->user->status ?? '')==='rejected')>Rejected</option>
                            </select>
                        </div>
                        @endif
                    </div>
                </x-card>

                <x-card title="Resident Profile">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name"
                                   value="{{ old('first_name', $resident->first_name) }}"
                                   class="form-control @error('first_name') is-invalid @enderror" required>
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Middle Name</label>
                            <input type="text" name="middle_name"
                                   value="{{ old('middle_name', $resident->middle_name) }}"
                                   class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name"
                                   value="{{ old('last_name', $resident->last_name) }}"
                                   class="form-control @error('last_name') is-invalid @enderror" required>
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="birth_date"
                                   value="{{ old('birth_date', $resident->birth_date?->format('Y-m-d')) }}"
                                   class="form-control @error('birth_date') is-invalid @enderror"
                                   max="{{ now()->subDay()->toDateString() }}" required>
                            @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                <option value="male"   @selected(old('gender',$resident->gender)==='male')>Male</option>
                                <option value="female" @selected(old('gender',$resident->gender)==='female')>Female</option>
                                
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Civil Status <span class="text-danger">*</span></label>
                            <select name="civil_status" class="form-select @error('civil_status') is-invalid @enderror" required>
                                @foreach(['single','married','widowed','separated','annulled'] as $cs)
                                    <option value="{{ $cs }}" @selected(old('civil_status',$resident->civil_status)===$cs)>{{ ucfirst($cs) }}</option>
                                @endforeach
                            </select>
                            @error('civil_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number"
                                   value="{{ old('contact_number', $resident->contact_number) }}"
                                   class="form-control @error('contact_number') is-invalid @enderror" required>
                            @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Relationship to Head</label>
                            <input type="text" name="relationship_to_head"
                                   value="{{ old('relationship_to_head', $resident->relationship_to_head) }}"
                                   class="form-control" placeholder="Head / Spouse / Child">
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- RIGHT: Household --}}
            <div class="col-md-5">
                <x-card title="Household Assignment" subtitle="Address auto-syncs when changed (Req 4)">
                    <label class="form-label fw-semibold">Select Household</label>
                    <select name="household_id" class="form-select @error('household_id') is-invalid @enderror">
                        <option value="">— Unassigned —</option>
                        @foreach($households as $hh)
                            <option value="{{ $hh->id }}"
                                    @selected(old('household_id', $resident->household_id)==$hh->id)>
                                #{{ $hh->id }} — {{ $hh->address }}
                                @if($hh->purok) (Purok {{ $hh->purok }}) @endif
                                — {{ $hh->residents()->count() }} member(s)
                            </option>
                        @endforeach
                    </select>
                    @error('household_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Changing the household will update this resident's address field.</div>

                    @if($resident->household)
                    <div class="alert alert-primary mt-3 py-2" style="font-size:13px;">
                        <i class="bi bi-house-fill"></i>
                        Current: <strong>{{ $resident->household->address }}</strong>
                        @if($resident->household->purok) — Purok {{ $resident->household->purok }} @endif
                    </div>
                    @endif
                </x-card>

                <div class="d-flex gap-2 mt-2">
                    <a href="{{ route('residents.show', $resident) }}" class="btn btn-secondary flex-fill">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-layout>
