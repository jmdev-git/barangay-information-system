<x-layout title="Barangay Census Intake">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Barangay Census & Resident Validation</h4>
            <small class="text-muted">Digitalized step-by-step census process — From Manual to BMIS</small>
        </div>
    </div>

    {{-- Validation Logic Banner --}}
    <div class="card mb-4" style="background:linear-gradient(135deg,#1e3a8a,#667eea);border-radius:1rem;border:none;">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 text-white">
                <div class="text-center">
                    <i class="bi bi-person-badge" style="font-size:2rem;"></i>
                    <div style="font-size:12px;font-weight:700;">Resident Info</div>
                </div>
                <i class="bi bi-plus-lg" style="font-size:1.5rem;opacity:.7;"></i>
                <div class="text-center">
                    <i class="bi bi-card-text" style="font-size:2rem;"></i>
                    <div style="font-size:12px;font-weight:700;">Valid Gov't ID</div>
                </div>
                <i class="bi bi-plus-lg" style="font-size:1.5rem;opacity:.7;"></i>
                <div class="text-center">
                    <i class="bi bi-camera" style="font-size:2rem;"></i>
                    <div style="font-size:12px;font-weight:700;">Resident Photo</div>
                </div>
                <i class="bi bi-plus-lg" style="font-size:1.5rem;opacity:.7;"></i>
                <div class="text-center">
                    <i class="bi bi-house-check" style="font-size:2rem;"></i>
                    <div style="font-size:12px;font-weight:700;">Household Verification</div>
                </div>
                <i class="bi bi-arrow-right" style="font-size:1.5rem;opacity:.7;"></i>
                <div class="text-center">
                    <div style="background:rgba(255,255,255,0.15);border-radius:50%;width:50px;height:50px;
                                display:inline-flex;align-items:center;justify-content:center;">
                        <i class="bi bi-check-circle-fill" style="font-size:1.8rem;color:#48bb78;"></i>
                    </div>
                    <div style="font-size:12px;font-weight:700;">Verified Resident</div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="{{ route('census.step1') }}" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-play-circle"></i> Start Census Intake
        </a>
    </div>
</x-layout>
