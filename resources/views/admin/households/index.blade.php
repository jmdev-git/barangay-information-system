<x-layout title="Households">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Household Management</h4>
            <small class="text-muted">Manage household numbers, addresses, zones, heads, and members.</small>
        </div>

        <a href="{{ route('households.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Household
        </a>
    </div>

    <x-card>
        <form method="GET" action="{{ route('households.index') }}" class="row g-2 mb-3">
            <div class="col-md-9">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control"
                       placeholder="Search household number, address, zone, or household head...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Search</button>
                <a href="{{ route('households.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <x-table.head>
                    <th>Address</th>
                    <th>Household Number</th>
                    <th>Zone</th>
                    <th>Head</th>
                    <th>Residents</th>
                    <th>Actions</th>
                </x-table.head>

                <tbody>
                    @forelse($households as $household)
                        <x-table.row>
                            <td>{{ $household->address ?? 'N/A' }}</td>
                            <td>Household #{{ $household->id }}</td>
                            <td>{{ $household->purok ?? 'N/A' }}</td>
                            <td>{{ $household->household_head_name ?? ($household->head ? $household->head->first_name . ' ' . $household->head->last_name : 'Unassigned') }}</td>
                            <td><span class="badge bg-primary">{{ $household->residents_count ?? $household->residents->count() }}</span></td>
                            <td>
                                <a href="{{ route('households.show', $household) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('households.edit', $household) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('households.destroy', $household) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this household?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </x-table.row>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No households found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="d-flex justify-content-center">
        {{ $households->links() }}
    </div>
</x-layout>
