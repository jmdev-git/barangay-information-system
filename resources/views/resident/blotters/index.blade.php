<x-layout title="My Blotter Reports">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">My Blotter Reports</h4>
            <small class="text-muted">Incident reports you have filed with the barangay.</small>
        </div>
        <a href="{{ route('resident.blotters.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> File a Blotter
        </a>
    </div>

    <x-card>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <x-table.head>
                    <th>Case No.</th>
                    <th>Respondent</th>
                    <th>Incident Date</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Filed</th>
                    <th>Action</th>
                </x-table.head>
                <tbody>
                    @forelse($blotters as $blotter)
                        <x-table.row>
                            <td>
                                <span class="fw-bold text-muted">{{ $blotter->case_number ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $blotter->respondent_name }}</td>
                            <td>{{ $blotter->incident_date?->format('M d, Y') }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($blotter->location, 40) }}</td>
                            <td>
                                @php
                                    $badges = [
                                        'pending_review' => 'bg-warning text-dark',
                                        'open'           => 'bg-primary',
                                        'closed'         => 'bg-secondary',
                                        'resolved'       => 'bg-success',
                                        'rejected'       => 'bg-danger',
                                    ];
                                    $labels = [
                                        'pending_review' => 'Pending Review',
                                        'open'           => 'Open',
                                        'closed'         => 'Closed',
                                        'resolved'       => 'Resolved',
                                        'rejected'       => 'Rejected',
                                    ];
                                @endphp
                                <span class="badge {{ $badges[$blotter->status] ?? 'bg-secondary' }}">
                                    {{ $labels[$blotter->status] ?? ucfirst($blotter->status) }}
                                </span>
                            </td>
                            <td>{{ $blotter->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('resident.blotters.show', $blotter) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </x-table.row>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-journal-x" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.3;"></i>
                                You have not filed any blotter reports yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    @if($blotters->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $blotters->links() }}
        </div>
    @endif
</x-layout>
