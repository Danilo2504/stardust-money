@props([
    'variant' => 'primary',
    'type'    => 'button',
])

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'inline-flex items-center px-4 py-2 rounded font-medium transition',
        'bg-blue-600 text-white hover:bg-blue-700'   => $variant === 'primary',
        'bg-gray-200 text-gray-800 hover:bg-gray-300' => $variant === 'secondary',
        'bg-red-600 text-white hover:bg-red-700'      => $variant === 'danger',
    ]) }}>
    {{ $slot }}
</button>
