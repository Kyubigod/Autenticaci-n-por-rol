<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Equipos de Cómputo
            </h2>
            <a href="{{ route('equipos.create') }}"
               style="background-color: #2563eb; color: #ffffff;"
               class="inline-flex items-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm">
                + Nuevo Equipo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div style="background-color: #064e3b; border: 1px solid #10b981; color: #a7f3d0;" class="px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #450a0a; border: 1px solid #ef4444; color: #fecaca;" class="px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Código</th>
                            <th class="px-6 py-3 text-left">Nombre</th>
                            <th class="px-6 py-3 text-left">Especificaciones</th>
                            <th class="px-6 py-3 text-left">Ubicación</th>
                            <th class="px-6 py-3 text-left">Estado</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-900 dark:text-gray-200">
                        @forelse($equipos as $equipo)
                            <tr>
                                <td class="px-6 py-4 font-mono font-semibold">{{ $equipo->codigo }}</td>
                                <td class="px-6 py-4 font-medium">{{ $equipo->nombre }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $equipo->especificaciones }}</td>
                                <td class="px-6 py-4">{{ $equipo->ubicacion }}</td>
                                <td class="px-6 py-4">
                                    @if($equipo->estado === 'Disponible')
                                        <span style="background-color: #064e3b; color: #6ee7b7; border: 1px solid #10b981;" class="px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                            Disponible
                                        </span>
                                    @else
                                        <span style="background-color: #78350f; color: #fde68a; border: 1px solid #f59e0b;" class="px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                            Mantenimiento
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('equipos.edit', $equipo) }}"
                                       style="background-color: #4f46e5; color: #ffffff;"
                                       class="inline-flex items-center px-3 py-1 rounded text-xs font-semibold">
                                        Editar
                                    </a>
                                    <form action="{{ route('equipos.destroy', $equipo) }}" method="POST" class="inline"
                                          onsubmit="return confirm('¿Seguro que deseas eliminar este equipo?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="background-color: #dc2626; color: #ffffff;"
                                                class="inline-flex items-center px-3 py-1 rounded text-xs font-semibold">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No hay equipos registrados todavía.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $equipos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>