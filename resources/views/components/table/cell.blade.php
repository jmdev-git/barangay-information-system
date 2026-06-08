@props(['align' => 'left'])

<td class="text-{{ $align }}" {{ $attributes }}>
    {{ $slot }}
</td>
