<x-layout title="Account Pending Verification">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center" style="border-radius:1rem; overflow:hidden;">
                <div style="background:linear-gradient(135deg,#667eea,#764ba2); padding:2.5rem 2rem 1.5rem;">
                    <div style="width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;
                                display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <i class="bi bi-hourglass-split" style="font-size:2.5rem;color:#fff;"></i>
                    </div>
                    <h3 style="color:#fff;font-weight:800;margin:0;">Account Pending Verification</h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-3">
                        Hello, <strong>{{ auth()->user()->name }}</strong>. Your registration has been received.
                    </p>
                    <div class="alert alert-warning d-flex align-items-start gap-3 text-start" style="border-radius:.75rem;">
                        <i class="bi bi-info-circle-fill mt-1" style="font-size:1.2rem;color:#f59e0b;flex-shrink:0;"></i>
                        <div>
                            <strong>Your account is currently under review.</strong><br>
                            A barangay admin will verify your uploaded government ID and notify you by email
                            once your account has been reviewed. This typically takes 1–2 business days.
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-4" style="font-size:14px;">
                        You will receive an email at <strong>{{ auth()->user()->email }}</strong> when your account is approved or if additional information is needed.
                    </p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-box-arrow-right"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
