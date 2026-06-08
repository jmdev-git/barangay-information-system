<x-layout title="My Dashboard">
    {{-- Welcome header --}}
    <div class="card mb-4" style="background:linear-gradient(135deg,#667eea,#764ba2);border-radius:.85rem;border:none;">
        <div class="card-body d-flex align-items-center gap-3 py-4 px-4">
            <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,.2);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-person-fill" style="font-size:1.8rem;color:#fff;"></i>
            </div>
            <div>
                <h5 class="text-white fw-bold mb-0">Welcome, {{ auth()->user()->name }}!</h5>
                <small style="color:rgba(255,255,255,.75);">
                    Account Status:
                    <strong style="color:#fff;">{{ str_replace('_',' ',ucwords(auth()->user()->status ?? 'active')) }}</strong>
                </small>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="row g-3 mb-4">
        @php
            $resident     = auth()->user()->resident;
            $totalC       = $resident ? $resident->clearances()->count() : 0;
            $approvedC    = $resident ? $resident->clearances()->where('status','approved')->count() : 0;
            $totalB       = $resident ? $resident->complainantBlotters()->count() : 0;
            $activeB      = $resident ? $resident->complainantBlotters()->whereIn('status',['pending_review','open'])->count() : 0;
        @endphp

        <div class="col-md-3 col-6">
            <div class="card text-center h-100" style="border-top:3px solid #667eea;">
                <div class="card-body py-3 px-2">
                    <div class="fw-bold" style="font-size:1.8rem;color:#667eea;">{{ $totalC }}</div>
                    <small class="text-muted" style="font-size:12px;">Clearances<br>Submitted</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-center h-100" style="border-top:3px solid #48bb78;">
                <div class="card-body py-3 px-2">
                    <div class="fw-bold" style="font-size:1.8rem;color:#48bb78;">{{ $approvedC }}</div>
                    <small class="text-muted" style="font-size:12px;">Clearances<br>Approved</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-center h-100" style="border-top:3px solid #764ba2;">
                <div class="card-body py-3 px-2">
                    <div class="fw-bold" style="font-size:1.8rem;color:#764ba2;">{{ $totalB }}</div>
                    <small class="text-muted" style="font-size:12px;">Blotters<br>Filed</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card text-center h-100" style="border-top:3px solid #ed8936;">
                <div class="card-body py-3 px-2">
                    <div class="fw-bold" style="font-size:1.8rem;color:#ed8936;">{{ $activeB }}</div>
                    <small class="text-muted" style="font-size:12px;">Blotters<br>Under Review</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Clearance quick action --}}
        <div class="col-md-6">
            <x-card title="Barangay Clearance" subtitle="Request and track your clearance certificates">
                <p class="text-muted mb-3" style="font-size:14px;">
                    Submit a clearance request online. Once approved, you can download the certificate directly.
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ route('clearances.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Request Clearance
                    </a>
                    <a href="{{ route('clearances.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list-ul"></i> View All
                    </a>
                </div>
            </x-card>
        </div>

        {{-- Blotter quick action --}}
        <div class="col-md-6">
            <x-card title="Blotter Report" subtitle="File an incident report online">
                <p class="text-muted mb-3" style="font-size:14px;">
                    File a blotter report without visiting the barangay hall. A barangay admin will review and process it.
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ route('resident.blotters.create') }}" class="btn btn-danger">
                        <i class="bi bi-plus-lg"></i> File a Blotter
                    </a>
                    <a href="{{ route('resident.blotters.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list-ul"></i> View All
                    </a>
                </div>
            </x-card>
        </div>

        {{-- Announcements --}}
        <div class="col-md-12">
            <x-card title="Announcements" subtitle="Latest barangay news and updates">
                <a href="{{ route('announcements.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-megaphone"></i> View Announcements
                </a>
            </x-card>
        </div>
    </div>
</x-layout>
