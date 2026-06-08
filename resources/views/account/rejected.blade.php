<x-layout title="Account Not Approved">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center" style="border-radius:1rem; overflow:hidden;">
                <div style="background:linear-gradient(135deg,#ef4444,#b91c1c); padding:2.5rem 2rem 1.5rem;">
                    <div style="width:80px;height:80px;background:rgba(255,255,255,0.15);border-radius:50%;
                                display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <i class="bi bi-x-circle-fill" style="font-size:2.5rem;color:#fff;"></i>
                    </div>
                    <h3 style="color:#fff;font-weight:800;margin:0;">Account Not Approved</h3>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-3">
                        Hello, <strong>{{ auth()->user()->name }}</strong>.
                    </p>
                    <div class="alert alert-danger d-flex align-items-start gap-3 text-start" style="border-radius:.75rem;">
                        <i class="bi bi-exclamation-triangle-fill mt-1" style="font-size:1.2rem;color:#ef4444;flex-shrink:0;"></i>
                        <div>
                            <strong>Your account registration was not approved.</strong><br>
                            @if($reason)
                                <span class="text-muted" style="font-size:14px;">Reason: {{ $reason }}</span>
                            @else
                                <span class="text-muted" style="font-size:14px;">Please contact the barangay hall for more information.</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-4" style="font-size:14px;">
                        If you believe this is an error, please visit the barangay hall or contact us directly.
                        You may register again with corrected documents.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat"></i> Re-register
                        </a>
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
    </div>
</x-layout>
