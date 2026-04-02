<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 rounded-lg font-semibold text-sm text-white transition-opacity focus:outline-none']) }}
        style="background-color: #ff6b2b;">
    {{ $slot }}
</button>
