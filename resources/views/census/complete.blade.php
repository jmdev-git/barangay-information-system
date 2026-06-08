<x-layout title="Census Complete — Profile Saved">
    <div class="row justify-content-center">
        <div class="col-md-7">
            {{-- Success header --}}
            <div class="card mb-4 text-center" style="border-radius:1rem;overflow:hidden;border:none;">
                <div style="background:linear-gradient(135deg,#48bb78,#2f855a);padding:2.5rem 2rem 1.5rem;">
                    <div style="width:80px;height:80px;background:rgba(255,255,255,0.2);border-radius:50%;
                                display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <i class="bi bi-check-circle-fill" style="font-size:2.5rem;color:#fff;"></i>
                    </div>
                    <h3 style="color:#fff;font-weight:800;margin:0;">Resident Profile Saved!</h3>
                    <p style="color:rgba(255,255,255,.85);margin:6px 0 0;font-size:14px;">
                        Step 7 complete — All data saved securely to the BMIS database.
                    </p>
                </div>
                <div class="card-body p-4">
                    <table class="table table-sm table-borderless text-start mb-3">
                        <tbody>
                            <tr>
                                <td class="text-muted fw-semibold" style="width:40%">Full Name</td>
                                <td class="fw-bold">{{ $resident->full_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Date of Birth</td>
                                <td>{{ $resident->birth_date?->format('M d, Y') }} (Age {{ $resident->age }})</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Gender</td>
                                <td>{{ ucfirst($resident->gender) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Civil Status</td>
                                <td>{{ ucfirst($resident->civil_status) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Household</td>
                                <td>{{ $resident->household?->address ?? 'N/A' }}, Purok {{ $resident->household?->purok ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">ID Documents</td>
                                <td>{{ $resident->documents->count() }} file(s) attached</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Verified By</td>
                                <td>{{ auth()->user()->name }} <span class="badge bg-success">Enumerator</span></td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Step 8 hint: Generate Reports --}}
                    <div class="alert alert-info d-flex align-items-start gap-2 text-start" style="border-radius:.75rem;">
                        <i class="bi bi-bar-chart-fill mt-1" style="flex-shrink:0;color:#4299e1;"></i>
                        <div>
                            <strong>Step 8: Generate Reports</strong><br>
                            <small>Real-time reports for residents, households, seniors, PWDs, voters, and more are available in the Reports module.</small>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                        <a href="{{ route('residents.show', $resident) }}" class="btn btn-primary">
                            <i class="bi bi-person"></i> View Profile
                        </a>
                        <a href="{{ route('census.step1') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Add Another Resident
                        </a>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-bar-chart"></i> Generate Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
