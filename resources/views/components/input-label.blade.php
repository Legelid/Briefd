@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm']) }} style="color: #6b6b8a;">
    {{ $value ?? $slot }}
</label>
