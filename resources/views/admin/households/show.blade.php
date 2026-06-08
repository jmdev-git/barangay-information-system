<x-layout title="Household — {{ $household->address }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Household #{{ $household->id }}</h4>
            <small class="text-muted">{{ $household->address }}, Purok {{ $household->purok ?? 'N/A' }}, {{ $household->barangay }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('households.edit', $household) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form action="{{ route('households.destroy', $household) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Delete this household? Residents will be unassigned.')">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
            <a href="{{ route('households.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <x-card title="Household Info">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted fw-semibold" style="width:45%">Address</td>
                        <td>{{ $household->address }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Purok</td>
                        <td>{{ $household->purok ?? 'N/A' }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Barangay</td>
                        <td>{{ $household->barangay }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Head</td>
                        <td>{{ $household->household_head_name ?? 'N/A' }}</td></tr>
                    <tr><td class="text-muted fw-semibold">Members</td>
                        <td><span class="badge bg-primary">{{ $household->residents->count() }}</span></td></tr>
                </table>
            </x-card>
        </div>

        {{-- Resident list (Req 4 AC4) — full_name, gender, computed age, account status --}}
        <div class="col-md-8">
            <x-card title="Household Members" subtitle="{{ $household->residents->count() }} resident(s) assigned">
                @if($household->residents->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-people" style="font-size:2rem;opacity:.3;display:block;margin-bottom:8px;"></i>
                        No residents assigned to this household.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <x-table.head>
                                <th>Full Name</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>Civil Status</th>
                                <th>Relationship</th>
                                <th>Account Status</th>
                                <th></th>
                            </x-table.head>
                            <tbody>
                                @foreach($household->residents as $member)
                                <x-table.row>
                                    <td class="fw-semibold">{{ $member->full_name }}</td>
                                    <td>{{ ucfirst($member->gender) }}</td>
                                    <td>{{ $member->age ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($member->civil_status ?? 'N/A') }}</td>
                                    <td>{{ $member->relationship_to_head ?? '—' }}</td>
                                    <td>
                                        @if($member->user)
                                            @php $st = $member->user->status ?? 'active'; @endphp
                                            <span class="badge {{ $st==='active' ? 'bg-success' : ($st==='pending_verification' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                {{ str_replace('_',' ', ucfirst($st)) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Account</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('residents.show', $member) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </x-table.row>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layout>
