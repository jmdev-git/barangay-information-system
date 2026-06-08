<x-layout title="Dashboard">
    <div class="mb-4">
        <h4>Dashboard</h4>
        <p class="text-muted">Welcome to the Barangay Information System.</p>
    </div>

    @if(strtolower(auth()->user()->role ?? '') === 'admin')
        <div class="row">
            <div class="col-md-4 mb-4">
                <x-card title="Residents">
                    <p class="text-muted">Manage resident records.</p>

                    <a href="{{ route('residents.index') }}" class="btn btn-primary">
                        <i class="bi bi-people"></i> Go to Residents
                    </a>
                </x-card>
            </div>

            <div class="col-md-4 mb-4">
                <x-card title="Households">
                    <p class="text-muted">Manage household records.</p>

                    <a href="{{ route('households.index') }}" class="btn btn-primary">
                        <i class="bi bi-houses"></i> Go to Households
                    </a>
                </x-card>
            </div>

            <div class="col-md-4 mb-4">
                <x-card title="Blotters">
                    <p class="text-muted">Manage blotter records.</p>

                    <a href="{{ route('blotters.index') }}" class="btn btn-primary">
                        <i class="bi bi-journal-text"></i> Go to Blotters
                    </a>
                </x-card>
            </div>

            <div class="col-md-4 mb-4">
                <x-card title="Clearance Requests">
                    <p class="text-muted">Review and approve clearance requests.</p>

                    <a href="{{ route('clearances.admin') }}" class="btn btn-success">
                        <i class="bi bi-file-check"></i> Go to Clearances
                    </a>
                </x-card>
            </div>

            <div class="col-md-4 mb-4">
                <x-card title="Reports">
                    <p class="text-muted">View system reports.</p>

                    <a href="{{ route('reports.index') }}" class="btn btn-danger">
                        <i class="bi bi-bar-chart"></i> Go to Reports
                    </a>
                </x-card>
            </div>

            <div class="col-md-4 mb-4">
                <x-card title="Announcements">
                    <p class="text-muted">View barangay announcements.</p>

                    <a href="{{ route('announcements.index') }}" class="btn btn-warning">
                        <i class="bi bi-megaphone"></i> Go to Announcements
                    </a>
                </x-card>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-6 mb-4">
                <x-card title="My Clearances">
                    <p class="text-muted">Request and view your clearance requests.</p>

                    <a href="{{ route('clearances.index') }}" class="btn btn-primary">
                        <i class="bi bi-file-text"></i> Go to My Clearances
                    </a>
                </x-card>
            </div>

            <div class="col-md-6 mb-4">
                <x-card title="Announcements">
                    <p class="text-muted">View barangay announcements.</p>

                    <a href="{{ route('announcements.index') }}" class="btn btn-warning">
                        <i class="bi bi-megaphone"></i> Go to Announcements
                    </a>
                </x-card>
            </div>
        </div>
    @endif
</x-layout>