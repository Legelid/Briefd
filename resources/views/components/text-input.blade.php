@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg text-white text-sm border transition-colors focus:outline-none focus:ring-1 focus:ring-accent']) }}
       style="background-color: #0a0a0f; border-color: #1e1e2e;">
