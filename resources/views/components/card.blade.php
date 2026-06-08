@props(['title' => '', 'subtitle' => ''])

<div class="card" {{ $attributes }}>
    @if($title)
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">{{ $title }}</h5>
            @if($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </div>
    @endif
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
