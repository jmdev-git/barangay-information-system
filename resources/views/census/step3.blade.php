<x-layout title="Census Step 3 — Capture/Upload Valid ID">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Census Intake</h4>
            <small class="text-muted">Step 3 of 5 — Capture / Upload Valid ID & Resident Photo</small>
        </div>
        <form action="{{ route('census.reset') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm"
                    onclick="return confirm('Reset?')">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </form>
    </div>

    @include('census.partials.stepper', ['current' => 3])

    <x-card title="Step 3: Capture / Upload Valid Government ID"
            subtitle="Attach valid government ID (PhilSys, Driver's License, UMID, Passport) — Max 5MB per file.">

        <form method="POST" action="{{ route('census.step3.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- ID Type Selection --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">ID Type <span class="text-danger">*</span></label>
                <div class="row g-2">
                    @foreach($documentTypes as $value => $label)
                        @php
                        $icons = ['philsys'=>'bi-person-badge','drivers_license'=>'bi-car-front','umid'=>'bi-credit-card','passport'=>'bi-book','other'=>'bi-card-text'];
                        @endphp
                        <div class="col-md-2 col-4">
                            <label class="d-block text-center border rounded-3 p-3 id-type-option"
                                   style="cursor:pointer;transition:.2s;border-color:#e2e8f0 !important;">
                                <input type="radio" name="id_type" value="{{ $value }}"
                                       class="d-none id-radio" {{ old('id_type') === $value ? 'checked' : '' }} required>
                                <i class="bi {{ $icons[$value] ?? 'bi-card-text' }}"
                                   style="font-size:1.8rem;color:#667eea;display:block;margin-bottom:4px;"></i>
                                <div style="font-size:11px;font-weight:600;color:#334155;line-height:1.2;">{{ $label }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('id_type')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
            </div>

            {{-- ID File Upload --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Upload ID File(s) <span class="text-danger">*</span>
                    <small class="text-muted fw-normal">— 1 to 3 files, JPEG / PNG / PDF, max 5 MB each</small>
                </label>
                <div class="upload-dropzone border-2 border-dashed rounded-3 p-4 text-center"
                     style="border:2px dashed #cbd5e1;background:#f8fafc;cursor:pointer;transition:.2s;"
                     onclick="document.getElementById('id_files_census').click()"
                     ondragover="event.preventDefault();this.style.borderColor='#667eea'"
                     ondragleave="this.style.borderColor='#cbd5e1'">
                    <i class="bi bi-cloud-arrow-up" style="font-size:2.5rem;color:#667eea;display:block;margin-bottom:8px;"></i>
                    <div class="fw-semibold text-muted">Click to browse or drag & drop files here</div>
                    <small class="text-muted">Accepted: JPEG · PNG · PDF — Max 5 MB each</small>
                </div>
                <input type="file" id="id_files_census" name="id_files[]"
                       accept=".jpg,.jpeg,.png,.pdf" multiple style="display:none"
                       onchange="previewCensusFiles(this, 'id_preview')">
                <div id="id_preview" class="mt-2"></div>
                @error('id_files')  <div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
                @error('id_files.*')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
            </div>

            {{-- Resident Photo Upload --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Resident Photo <span class="text-muted fw-normal">(Optional — JPEG/PNG, max 5 MB)</span>
                </label>
                <div class="d-flex align-items-center gap-3">
                    <div id="photo_preview_wrap" style="width:90px;height:90px;border-radius:50%;
                                border:3px solid #e2e8f0;overflow:hidden;background:#f1f5f9;
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-circle" style="font-size:2.5rem;color:#cbd5e1;"></i>
                    </div>
                    <div>
                        <label class="btn btn-outline-secondary btn-sm"
                               for="resident_photo_input" style="cursor:pointer;">
                            <i class="bi bi-camera"></i> Upload Photo
                        </label>
                        <input type="file" id="resident_photo_input" name="resident_photo"
                               accept=".jpg,.jpeg,.png" style="display:none"
                               onchange="previewPhoto(this)">
                        <div class="text-muted mt-1" style="font-size:12px;">JPEG · PNG — Max 5 MB</div>
                        @error('resident_photo')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-2">
                <a href="{{ route('census.step2') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    Proceed to Step 4 — Validate <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </form>
    </x-card>

    @push('scripts')
    <script>
        // ID type card selection highlight
        document.querySelectorAll('.id-type-option').forEach(label => {
            label.addEventListener('click', () => {
                document.querySelectorAll('.id-type-option').forEach(l => {
                    l.style.borderColor = '#e2e8f0';
                    l.style.background  = '#fff';
                });
                label.style.borderColor = '#667eea';
                label.style.background  = '#f0f4ff';
            });
        });

        function previewCensusFiles(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';
            Array.from(input.files).forEach(file => {
                const isPdf   = file.type === 'application/pdf';
                const icon    = isPdf ? 'bi-file-pdf-fill' : 'bi-image-fill';
                const color   = isPdf ? '#ef4444' : '#667eea';
                const size    = file.size > 1048576
                    ? (file.size / 1048576).toFixed(1) + ' MB'
                    : Math.round(file.size / 1024) + ' KB';
                preview.innerHTML += `
                    <div class="d-flex align-items-center gap-2 bg-light rounded-3 px-3 py-2 mb-1">
                        <i class="bi ${icon}" style="color:${color};font-size:16px;flex-shrink:0;"></i>
                        <span style="font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${file.name}</span>
                        <span class="ms-auto text-muted" style="font-size:11px;white-space:nowrap;">${size}</span>
                    </div>`;
            });
        }

        function previewPhoto(input) {
            const wrap = document.getElementById('photo_preview_wrap');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    wrap.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-layout>
