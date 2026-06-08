<x-layout title="Review Account — {{ $user->name }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Account Review</h4>
            <small class="text-muted">Verify identity documents and approve or reject this account.</small>
        </div>
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Pending
        </a>
    </div>

    <div class="row g-4">
        {{-- Registrant Info --}}
        <div class="col-md-5">
            <x-card title="Registrant Information">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);
                                display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-fill" style="color:#fff;font-size:28px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                        <span class="badge bg-warning text-dark">Pending Verification</span>
                    </div>
                </div>

                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width:40%">Email</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Registered</td>
                            <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @if($user->resident)
                        <tr><td colspan="2"><hr class="my-2"></td></tr>
                        <tr>
                            <td class="text-muted fw-semibold">Full Name</td>
                            <td>{{ $user->resident->full_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Date of Birth</td>
                            <td>{{ $user->resident->birth_date?->format('M d, Y') }} (Age: {{ $user->resident->age }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Gender</td>
                            <td>{{ ucfirst($user->resident->gender) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Contact</td>
                            <td>{{ $user->resident->contact_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Address</td>
                            <td>{{ $user->resident->address }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </x-card>

            {{-- Approve/Reject Actions --}}
            <x-card title="Decision">
                <form action="{{ route('admin.accounts.approve', $user) }}" method="POST" class="mb-3">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 fw-bold"
                            onclick="return confirm('Approve this account? The resident will be notified by email.')">
                        <i class="bi bi-check-circle"></i> Approve Account
                    </button>
                </form>

                <hr>

                <form action="{{ route('admin.accounts.reject', $user) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror"
                                  rows="4" placeholder="Provide a clear reason for rejection..."
                                  required minlength="1" maxlength="500">{{ old('rejection_reason') }}</textarea>
                        <div class="form-text">1–500 characters. The resident will receive this by email.</div>
                        @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-danger w-100 fw-bold">
                        <i class="bi bi-x-circle"></i> Reject Account
                    </button>
                </form>
            </x-card>
        </div>

        {{-- ID Documents --}}
        <div class="col-md-7">
            <x-card title="Uploaded Government IDs" subtitle="{{ $documents->count() }} file(s) submitted">
                @if($documents->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-file-earmark-x" style="font-size:2.5rem;opacity:0.3;display:block;margin-bottom:8px;"></i>
                        No documents were uploaded with this registration.
                    </div>
                @else
                    <div class="row g-3">
                        @foreach($documents as $doc)
                            <div class="col-md-6">
                                <div class="border rounded-3 overflow-hidden">
                                    {{-- Preview header --}}
                                    <div class="d-flex align-items-center gap-2 px-3 py-2"
                                         style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                                        <i class="bi {{ str_contains($doc->mime_type, 'pdf') ? 'bi-file-pdf-fill text-danger' : 'bi-image-fill text-primary' }}"
                                           style="font-size:1.1rem;"></i>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="fw-semibold text-truncate" style="font-size:13px;"
                                                 title="{{ $doc->original_name }}">{{ $doc->original_name }}</div>
                                            <small class="text-muted">{{ $doc->document_type_label }} · {{ $doc->file_size_formatted }}</small>
                                        </div>
                                    </div>

                                    {{-- Image preview (if image) --}}
                                    @if(str_contains($doc->mime_type, 'image'))
                                        <div style="height:180px; overflow:hidden; background:#f1f5f9;">
                                            <img src="{{ route('admin.documents.show', $doc) }}"
                                                 alt="ID Preview"
                                                 style="width:100%; height:100%; object-fit:cover;"
                                                 onerror="this.style.display='none'">
                                        </div>
                                    @else
                                        <div style="height:100px; background:#fff3f3; display:flex; align-items:center; justify-content:center;">
                                            <i class="bi bi-file-pdf-fill" style="font-size:3rem; color:#ef4444;"></i>
                                        </div>
                                    @endif

                                    <div class="px-3 py-2">
                                        <a href="{{ route('admin.documents.show', $doc) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary w-100">
                                            <i class="bi bi-eye"></i> View Full File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layout>
