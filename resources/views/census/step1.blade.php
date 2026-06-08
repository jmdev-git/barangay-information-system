<x-layout title="Census Step 1 — Search or Create Household">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Census Intake</h4>
            <small class="text-muted">Step 1 of 5 — Search or Create Household</small>
        </div>
        <form action="{{ route('census.reset') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm"
                    onclick="return confirm('Reset the census form?')">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </form>
    </div>

    @include('census.partials.stepper', ['current' => 1])

    {{-- Search existing households --}}
    <x-card title="Step 1: Search or Create Household" subtitle="Search for an existing household or create a new one below.">

        <form method="GET" action="{{ route('census.step1') }}" class="row g-2 mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ $search }}" class="form-control"
                           placeholder="Search by address, purok, or household head name...">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Search</button>
                @if($search)
                    <a href="{{ route('census.step1') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>

        @if($search && $households->isNotEmpty())
            <h6 class="mb-3 text-muted fw-semibold">Search Results <span class="badge bg-primary">{{ $households->total() }}</span></h6>
            <form method="POST" action="{{ route('census.step1.store') }}">
                @csrf
                <input type="hidden" name="action" value="select">
                <div class="row g-3 mb-3">
                    @foreach($households as $hh)
                        <div class="col-md-4">
                            <label class="card h-100 p-3 cursor-pointer"
                                   style="border:2px solid #e2e8f0;border-radius:.75rem;cursor:pointer;transition:.2s;"
                                   onmouseover="this.style.borderColor='#667eea'"
                                   onmouseout="this.style.borderColor=this.querySelector('input').checked ? '#667eea' : '#e2e8f0'">
                                <div class="d-flex gap-2">
                                    <input type="radio" name="household_id" value="{{ $hh->id }}" required>
                                    <div>
                                        <div class="fw-bold">{{ $hh->address }}</div>
                                        <small class="text-muted">
                                            Purok {{ $hh->purok ?? 'N/A' }} ·
                                            {{ $hh->barangay }} ·
                                            Head: {{ $hh->household_head_name ?? 'N/A' }}
                                        </small>
                                        <div class="mt-1">
                                            <span class="badge bg-light text-dark border">
                                                {{ $hh->residents->count() }} member(s)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('household_id')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Use Selected Household — Proceed to Step 2
                </button>
            </form>
        @elseif($search && $households->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No households found for "<strong>{{ $search }}</strong>". Create a new one below.
            </div>
        @endif

        <hr class="my-4">

        {{-- Create new household inline --}}
        <h6 class="mb-3 fw-bold text-muted"><i class="bi bi-house-add"></i> Create New Household</h6>
        <form method="POST" action="{{ route('census.step1.store') }}">
            @csrf
            <input type="hidden" name="action" value="create">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Street/House Address <span class="text-danger">*</span></label>
                    <input type="text" name="new_address" value="{{ old('new_address') }}"
                           class="form-control @error('new_address') is-invalid @enderror"
                           placeholder="e.g. 12 Rizal St., Purok 1" required>
                    @error('new_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Purok / Zone</label>
                    <input type="text" name="new_purok" value="{{ old('new_purok') }}"
                           class="form-control" placeholder="e.g. Purok 3">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Barangay <span class="text-danger">*</span></label>
                    <input type="text" name="new_barangay" value="{{ old('new_barangay', 'Centro') }}"
                           class="form-control @error('new_barangay') is-invalid @enderror" required>
                    @error('new_barangay')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Household Head Name</label>
                    <input type="text" name="new_household_head_name" value="{{ old('new_household_head_name') }}"
                           class="form-control" placeholder="(Set after resident profile is created)">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-house-add"></i> Create Household & Proceed to Step 2
                </button>
            </div>
        </form>
    </x-card>
</x-layout>
