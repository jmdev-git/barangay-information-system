<x-layout title="Page Not Found">
    <div class="text-center py-5">
        <h1 class="display-4 fw-bold text-warning">404</h1>

        <h4 class="mb-3">Page Not Found</h4>

        <p class="lead text-muted">
            The page you are looking for cannot be found.
        </p>

        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="btn btn-primary">
            Return to Home
        </a>
    </div>
</x-layout>