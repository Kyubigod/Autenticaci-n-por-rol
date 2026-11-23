@props(['column', 'label', 'sort', 'direction'])

@php
    $isCurrent = $sort === $column;
    $nextDirection = ($isCurrent && $direction === 'asc') ? 'desc' : 'asc';
    $url = route('productos.index', array_merge(request()->query(), [
        'sort' => $column,
        'direction' => $nextDirection,
    ]));
@endphp

<a href="{{ $url }}" class="inline-flex items-center gap-1 font-semibold hover:text-indigo-600">
    <span>{{ $label }}</span>
    @if ($isCurrent)
        <span class="text-xs text-indigo-600">{{ $direction === 'asc' ? '▲' : '▼' }}</span>
    @else
        <span class="text-xs text-gray-400">⇅</span>
    @endif
</a>