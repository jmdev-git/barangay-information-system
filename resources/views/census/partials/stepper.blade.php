@props(['current' => 1])
@php
$steps = [
    1 => 'Household',
    2 => 'Resident Info',
    3 => 'Upload ID',
    4 => 'Validate',
    5 => 'Save Profile',
];
@endphp
<div class="d-flex align-items-center mb-4" style="overflow-x:auto;">
    @foreach($steps as $num => $label)
        @php
            $isActive   = $num === $current;
            $isComplete = $num < $current;
            $color      = $isComplete ? '#48bb78' : ($isActive ? '#667eea' : '#cbd5e1');
            $textColor  = ($isActive || $isComplete) ? '#fff' : '#94a3b8';
        @endphp
        <div class="d-flex align-items-center {{ !$loop->last ? 'flex-grow-1' : '' }}">
            <div class="d-flex flex-column align-items-center" style="min-width:80px;">
                <div style="width:38px;height:38px;border-radius:50%;background:{{ $color }};
                            display:flex;align-items:center;justify-content:center;
                            font-weight:800;font-size:14px;color:{{ $textColor }};
                            transition:.3s;">
                    @if($isComplete)
                        <i class="bi bi-check-lg" style="color:#fff;font-size:14px;"></i>
                    @else
                        {{ $num }}
                    @endif
                </div>
                <small class="mt-1 text-center fw-{{ $isActive ? 'bold' : 'normal' }}"
                       style="font-size:11px;color:{{ $isActive ? '#667eea' : '#94a3b8' }};line-height:1.2;">
                    {{ $label }}
                </small>
            </div>
            @if(!$loop->last)
                <div style="flex:1;height:2px;background:{{ $isComplete ? '#48bb78' : '#e2e8f0' }};
                            margin:0 4px;margin-bottom:20px;transition:.3s;"></div>
            @endif
        </div>
    @endforeach
</div>
