<x-layout title="My Profile">

    <div class="row g-4" style="max-width:860px;">

        {{-- Profile Picture Card --}}
        @if(auth()->user()->isResident())
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:.85rem;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-1"><i class="bi bi-camera me-2"></i>Profile Picture</h6>
                    <p class="text-muted mb-3" style="font-size:13px;">Upload a photo to personalize your account.</p>

                    @if(session('status') === 'photo-updated')
                        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                            <i class="bi bi-check-circle me-1"></i> Profile picture updated successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        {{-- Current photo --}}
                        @php $photoUrl = auth()->user()->resident?->photo_path; @endphp
                        <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;
                                    border:3px solid #e2e8f0;flex-shrink:0;background:#f1f5f9;
                                    display:flex;align-items:center;justify-content:center;">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="Profile Photo"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="bi bi-person-fill" style="font-size:3rem;color:#94a3b8;"></i>
                            @endif
                        </div>

                        {{-- Upload form --}}
                        <form method="POST" action="{{ route('profile.photo') }}"
                              enctype="multipart/form-data" class="flex-grow-1">
                            @csrf
                            <div class="mb-2">
                                <input type="file" name="photo" id="photoInput"
                                       accept=".jpg,.jpeg,.png"
                                       class="form-control @error('photo') is-invalid @enderror"
                                       style="max-width:320px;">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted mt-1" style="font-size:12px;">
                                    JPG or PNG, max 5 MB
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-upload me-1"></i> Upload Photo
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Account Info Card --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:.85rem;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-1"><i class="bi bi-person me-2"></i>Account Information</h6>
                    <p class="text-muted mb-3" style="font-size:13px;">Update your display name and email address.</p>

                    @if(session('status') === 'profile-updated')
                        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                            <i class="bi bi-check-circle me-1"></i> Profile updated successfully.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">Display Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   style="max-width:400px;" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:13px;">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   style="max-width:400px;" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layout>
