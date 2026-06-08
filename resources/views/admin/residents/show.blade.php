<x-layout title="Resident Profile — {{ $resident->full_name }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Resident Profile</h4>
            <small class="text-muted">Full profile view — resident info, household, documents, clearances, blotters.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('residents.edit', $resident) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('residents.destroy', $resident) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Delete {{ addslashes($resident->full_name) }} and all associated records?')">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </form>
            <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Personal Info --}}
        <div class="col-md-5">
            <x-card>
                <div class="d-flex align-items-center gap-3 mb-4">
                    {{-- Photo or avatar --}}
                    @if($resident->photo_path)
                        <img src="{{ asset('storage/' . $resident->photo_path) }}"
                             style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:3px solid #e2e8f0;"
                             alt="Photo">
                    @else
                        <div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-person-fill" style="color:#fff;font-size:2rem;"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-0">{{ $resident->full_name }}</h5>
                        @if($resident->user)
                            <span class="badge {{ $resident->user->isActive() ? 'bg-success' : ($resident->user->isPendingVerification() ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ str_replace('_',' ', ucfirst($resident->user->status)) }}
                            </span>
                        @else
                            <span class="badge bg-secondary">No Account</span>
                        @endif
                    </div>
                </div>

                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted fw-semibold" style="width:42%">Email</td>
                        <td>{{ $resident->email ?? $resident->user?->email ?? 'N/A' }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Contact</td>
                        <td>{{ $resident->contact_number }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Date of Birth</td>
                        <td>{{ $resident->birth_date?->format('M d, Y') }} (Age {{ $resident->age }})</td></tr>
                    <tr><td class="text-muted fw-semibold">Gender</td>
                        <td>{{ ucfirst($resident->gender) }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Civil Status</td>
                        <td>{{ ucfirst($resident->civil_status ?? 'N/A') }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Source</td>
                        <td>{{ ucfirst(str_replace('_',' ', $resident->source ?? 'N/A')) }}</td></tr>
                    @if($resident->verifier)
                    <tr><td class="text-muted fw-semibold">Verified By</td>
                        <td>{{ $resident->verifier->name }}</td></tr>
                    @endif
                </table>
            </x-card>

            {{-- Household (Req 4 AC3) --}}
            <x-card title="Household">
                @if($resident->household)
                    <table class="table table-sm table-borderless mb-3">
                        <tr><td class="text-muted fw-semibold" style="width:42%">Address</td>
                            <td>{{ $resident->household->address }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Purok</td>
                            <td>{{ $resident->household->purok ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Barangay</td>
                            <td>{{ $resident->household->barangay }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Head</td>
                            <td>{{ $resident->household->household_head_name ?? 'N/A' }}</td></tr>
                        <tr><td class="text-muted fw-semibold">Members</td>
                            <td>{{ $resident->household->residents->count() }}</td></tr>
                    </table>
                    <a href="{{ route('households.show', $resident->household) }}"
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-house"></i> View Household
                    </a>
                @else
                    <div class="text-muted py-2">
                        <i class="bi bi-house-x"></i> Unassigned
                    </div>
                @endif
            </x-card>

            {{-- ID Documents (Req 4 AC3) --}}
            @if($resident->documents->isNotEmpty())
            <x-card title="Government ID Documents">
                @foreach($resident->documents as $doc)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi {{ str_contains($doc->mime_type,'pdf') ? 'bi-file-pdf-fill text-danger' : 'bi-image-fill text-primary' }}"
                           style="font-size:1.1rem;flex-shrink:0;"></i>
                        <div class="flex-grow-1">
                            <div style="font-size:13px;font-weight:600;">{{ $doc->document_type_label }}</div>
                            <small class="text-muted">{{ $doc->file_size_formatted }}</small>
                        </div>
                        <a href="{{ route('admin.documents.show', $doc) }}" target="_blank"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                @endforeach
            </x-card>
            @endif
        </div>

        {{-- Right: Clearances + Blotters --}}
        <div class="col-md-7">
            {{-- Clearances --}}
            <x-card title="Clearance Requests" subtitle="{{ $resident->clearances->count() }} total">
                @if($resident->clearances->isEmpty())
                    <div class="text-muted py-2" style="font-size:13px;">No clearance requests.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Purpose</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                @foreach($resident->clearances->take(5) as $c)
                                <tr>
                                    <td style="font-size:13px;">{{ \Str::limit($c->purpose, 40) }}</td>
                                    <td>
                                        <span class="badge {{ $c->status==='approved' ? 'bg-success' : ($c->status==='pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>
                                    <td style="font-size:12px;">{{ $c->requested_at?->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>

            {{-- Blotters as Complainant --}}
            <x-card title="Blotter Records (as Complainant)" subtitle="{{ $resident->complainantBlotters->count() }} filed">
                @if($resident->complainantBlotters->isEmpty())
                    <div class="text-muted py-2" style="font-size:13px;">No blotter records filed.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Case No.</th><th>Respondent</th><th>Status</th><th>Date</th></tr>
                            </thead>
                            <tbody>
                                @foreach($resident->complainantBlotters->take(5) as $b)
                                @php $bc = ['pending_review'=>'bg-warning text-dark','open'=>'bg-primary','closed'=>'bg-secondary','resolved'=>'bg-success','rejected'=>'bg-danger']; @endphp
                                <tr>
                                    <td style="font-size:12px;" class="fw-semibold text-muted">{{ $b->case_number }}</td>
                                    <td style="font-size:13px;">{{ \Str::limit($b->respondent_name, 25) }}</td>
                                    <td><span class="badge {{ $bc[$b->status] ?? 'bg-secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
                                    <td style="font-size:12px;">{{ $b->incident_date?->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>

            {{-- Blotters as Respondent --}}
            @if($resident->respondentBlotters->isNotEmpty())
            <x-card title="Blotter Records (as Respondent)" subtitle="{{ $resident->respondentBlotters->count() }} records">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Case No.</th><th>Complainant</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            @foreach($resident->respondentBlotters->take(5) as $b)
                            @php $bc = ['pending_review'=>'bg-warning text-dark','open'=>'bg-primary','closed'=>'bg-secondary','resolved'=>'bg-success','rejected'=>'bg-danger']; @endphp
                            <tr>
                                <td style="font-size:12px;" class="fw-semibold text-muted">{{ $b->case_number }}</td>
                                <td style="font-size:13px;">{{ \Str::limit($b->complainant_name, 25) }}</td>
                                <td><span class="badge {{ $bc[$b->status] ?? 'bg-secondary' }}">{{ ucfirst(str_replace('_',' ',$b->status)) }}</span></td>
                                <td style="font-size:12px;">{{ $b->incident_date?->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
            @endif
        </div>
    </div>
</x-layout>
