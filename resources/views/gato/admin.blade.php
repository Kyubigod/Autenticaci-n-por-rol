<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Administración Global — Partidas de Gato
            </h2>
            <a href="{{ route('users.index') }}"
               style="background-color: #2563eb; color: #ffffff;"
               class="px-4 py-2 rounded-md text-xs font-semibold uppercase">
                Gestionar Usuarios
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

            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">Jugador X</th>
                            <th class="px-6 py-3 text-left">Jugador O</th>
                            <th class="px-6 py-3 text-left">Estado</th>
                            <th class="px-6 py-3 text-left">Ganador</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                        @forelse($partidas as $p)
                            <tr>
                                <td class="px-6 py-4 font-mono">#{{ $p->id }}</td>
                                <td class="px-6 py-4">{{ $p->jugadorX->name }}</td>
                                <td class="px-6 py-4">{{ $p->jugadorO?->name ?? 'Sin rival' }}</td>
                                <td class="px-6 py-4 uppercase text-xs font-semibold">{{ $p->estado }}</td>
                                <td class="px-6 py-4">{{ $p->ganador?->name ?? ($p->estado === 'empate' ? 'Empate' : '-') }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('gato.show', $p) }}"
                                       style="background-color: #4f46e5; color: #ffffff;"
                                       class="px-3 py-1 rounded text-xs font-semibold">
                                        Ver
                                    </a>
                                    @if(in_array($p->estado, ['esperando', 'en_curso']))
                                        <form action="{{ route('gato.cancelarAdmin', $p) }}" method="POST" class="inline"
                                              onsubmit="return confirm('¿Cancelar esta partida en curso?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    style="background-color: #dc2626; color: #ffffff;"
                                                    class="px-3 py-1 rounded text-xs font-semibold">
                                                Cancelar
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-gray-500">No hay partidas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $partidas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>