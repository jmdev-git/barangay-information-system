<x-layout title="Residents">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Residents Management</h4>
            <small class="text-muted">All registered residents — from self-registration and census intake.</small>
        </div>

        <a href="{{ route('census.index') }}" class="btn btn-primary">
            <i class="bi bi-clipboard2-data"></i> Census Intake
        </a>
    </div>

    <x-card>
        <form method="GET" action="{{ route('residents.index') }}" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control"
                       placeholder="Search name, email, contact, household, or zone...">
            </div>
            <div class="col-md-3">
                <select name="gender" class="form-select">
                    <option value="">All genders</option>
                    <option value="male" @selected(($gender ?? '') === 'male')>Male</option>
                    <option value="female" @selected(($gender ?? '') === 'female')>Female</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('residents.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <x-table.head>
                    <th>Name</th>
                    <th>Birth Date</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Purok / Zone</th>
                    <th>Household</th>
                    <th>Actions</th>
                </x-table.head>

                <tbody>
                    @forelse($residents as $resident)
                        <x-table.row>
                            <td><strong>{{ $resident->first_name ?? '' }} {{ $resident->last_name ?? '' }}</strong></td>
                            <td>{{ $resident->birth_date ? $resident->birth_date->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $resident->gender ? ucfirst($resident->gender) : 'N/A' }}</td>
                            <td>{{ $resident->email ?? $resident->user?->email ?? 'N/A' }}</td>
                            <td>{{ $resident->contact_number ?? 'N/A' }}</td>
                            <td>
                                @if($resident->household?->purok)
                                    {{ $resident->household->purok }}
                                @elseif($resident->address)
                                    <span class="text-muted" style="font-size:12px;" title="{{ $resident->address }}">
                                        {{ Str::limit($resident->address, 30) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $resident->household ? 'Household #' . $resident->household->id : 'Unassigned' }}</td>
                            <td>
                                <a href="{{ route('residents.show', $resident) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('residents.edit', $resident) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('residents.destroy', $resident) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this resident?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </x-table.row>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No residents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="d-flex justify-content-center">
        {{ $residents->links() }}
    </div>
</x-layout>
