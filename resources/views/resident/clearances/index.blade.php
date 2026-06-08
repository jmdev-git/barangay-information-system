<x-layout title="My Clearances">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>My Clearance Requests</h4>
            <p class="text-muted mb-0">
                View your barangay clearance requests and their status.
            </p>
        </div>

        <a href="{{ route('clearances.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Request Clearance
        </a>
    </div>

    <x-card>
        <table class="table table-hover align-middle">
            <x-table.head>
                <th>Date Requested</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Issued Date</th>
                <th>Action</th>
            </x-table.head>

            <tbody>
                @forelse($clearances as $clearance)
                    <x-table.row>
                        <td>
                            {{ $clearance->requested_at
                                ? \Carbon\Carbon::parse($clearance->requested_at)->format('M d, Y h:i A')
                                : 'N/A' }}
                        </td>

                        <td>
                            {{ \Illuminate\Support\Str::limit($clearance->purpose ?? 'N/A', 60) }}
                        </td>

                        <td>
                            @if($clearance->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($clearance->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($clearance->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ $clearance->status ?? 'N/A' }}
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $clearance->issued_at
                                ? \Carbon\Carbon::parse($clearance->issued_at)->format('M d, Y h:i A')
                                : 'Not yet issued' }}
                        </td>

                        <td>
                            <a href="{{ route('clearances.show', $clearance) }}"
                               class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </x-table.row>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            You have not requested any clearances yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-card>

    @if($clearances->hasPages())
        <div class="d-flex justify-content-center">
            {{ $clearances->links() }}
        </div>
    @endif
</x-layout>