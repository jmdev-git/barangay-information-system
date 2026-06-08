@props(['name', 'label' => '', 'checked' => false])

<div class="form-check mb-3">
    <input 
        type="checkbox" 
        class="form-check-input" 
        id="{{ $name }}" 
        name="{{ $name }}"
        value="1"
        {{ old($name, $checked) ? 'checked' : '' }}
        {{ $attributes }}
    >
    @if($label)
        <label class="form-check-label" for="{{ $name }}">
            {{ $label }}
        </label>
    @endif
</div>
