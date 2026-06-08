@props(['type' => 'info', 'dismissible' => true])

<div class="alert alert-{{ $type }}{{ $dismissible ? ' alert-dismissible fade show' : '' }}" role="alert" {{ $attributes }}>
    {{ $slot }}
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    @endif
</div>
