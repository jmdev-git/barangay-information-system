<x-layout title="Access Denied">
    <div class="text-center py-5">
        <h1 class="display-4 fw-bold text-danger">403</h1>

        <h4 class="mb-3">Access Denied</h4>

        <p class="lead text-muted">
            You do not have permission to view this page.
        </p>

        <p class="text-muted">
            This page is only available for admin accounts.
        </p>

        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-primary">
            Return to Home
        </a>
    </div>
</x-layout>