@props(['estado'])

@php
    $esDisponible = $estado === 'Disponible';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $esDisponible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
    {{ $estado }}
</span>