<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Juego del Gato (Tic-Tac-Toe)
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Sesión activa: <strong class="text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</strong> ({{ auth()->user()->rol }})
                </p>
            </div>
            <form action="{{ route('gato.store') }}" method="POST">
                @csrf
                <button type="submit"
                        style="background-color: #2563eb; color: #ffffff;"
                        class="px-4 py-2 rounded-md text-xs font-semibold uppercase tracking-widest shadow-sm hover:opacity-90 transition">
                    + Crear Nueva Partida (Serás X)
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- JUGADORES CONECTADOS Y PARTIDAS DISPONIBLES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                        Jugadores Disponibles en Línea
                    </h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-700 dark:text-gray-300">
                        @forelse($jugadoresDisponibles as $jugador)
                            <li class="py-2 flex items-center justify-between">
                                <span>{{ $jugador->name }}</span>
                                <span style="background-color: #064e3b; color: #6ee7b7;" class="px-2 py-0.5 rounded-full text-xs">En línea</span>
                            </li>
                        @empty
                            <li class="py-4 text-gray-500">No hay otros jugadores conectados en este momento.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                        Partidas Disponibles para Unirse
                    </h3>
                    <div class="space-y-3">
                        @forelse($partidasDisponibles as $p)
                            <div class="flex items-center justify-between p-3 rounded border border-gray-200 dark:border-gray-700">
                                <div>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">Partida #{{ $p->id }}</p>
                                    <p class="text-xs text-gray-500">Creada por: {{ $p->jugadorX?->name ?? 'Jugador' }} (X)</p>
                                </div>
                                <form action="{{ route('gato.unirse', $p) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            style="background-color: #16a34a; color: #ffffff;"
                                            class="px-3 py-1.5 rounded text-xs font-semibold uppercase hover:opacity-90 transition">
                                        Unirse como O
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-4">No hay partidas esperando rival. ¡Crea una!</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- MIS PARTIDAS ACTIVAS / EN ESPERA --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                    Mis Partidas Activas / En Curso
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Jugador X</th>
                                <th class="px-4 py-2 text-left">Jugador O</th>
                                <th class="px-4 py-2 text-left">Estado</th>
                                <th class="px-4 py-2 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                            @forelse($misPartidasActivas as $p)
                                <tr>
                                    <td class="px-4 py-3 font-mono">#{{ $p->id }}</td>
                                    <td class="px-4 py-3">{{ $p->jugadorX?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $p->jugadorO?->name ?? 'Esperando rival...' }}</td>
                                    <td class="px-4 py-3 uppercase text-xs font-semibold">{{ $p->estado }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('gato.show', $p) }}"
                                           style="background-color: #4f46e5; color: #ffffff;"
                                           class="px-3 py-1 rounded text-xs font-semibold">
                                            Entrar al Tablero
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">No tienes partidas en curso.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- HISTORIAL DEL JUGADOR --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">
                    Mi Historial de Partidas
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Jugador X</th>
                                <th class="px-4 py-2 text-left">Jugador O</th>
                                <th class="px-4 py-2 text-left">Resultado</th>
                                <th class="px-4 py-2 text-right">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                            @forelse($historial as $p)
                                <tr>
                                    <td class="px-4 py-3 font-mono">#{{ $p->id }}</td>
                                    <td class="px-4 py-3">{{ $p->jugadorX?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $p->jugadorO?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        @if($p->estado === 'empate')
                                            <span class="text-yellow-400 font-semibold">Empate</span>
                                        @elseif($p->estado === 'cancelada')
                                            <span class="text-gray-400 font-semibold">Cancelada</span>
                                        @elseif($p->ganador_id === auth()->id())
                                            <span class="text-green-400 font-semibold">Victoria</span>
                                        @else
                                            <span class="text-red-400 font-semibold">Derrota (Ganó {{ $p->ganador?->name ?? 'Rival' }})</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('gato.show', $p) }}" class="text-indigo-400 hover:underline text-xs">Ver tablero</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Aún no tienes partidas finalizadas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>