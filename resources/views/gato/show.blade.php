<x-app-layout>
    @if($partida->estado === 'esperando' || ($partida->estado === 'en_curso' && $partida->turno_actual_id != auth()->id()))
        <meta http-equiv="refresh" content="3">
    @endif

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Partida #{{ $partida->id }} — Tú eres {{ $partida->jugador_x_id == auth()->id() ? 'X' : 'O' }}
            </h2>
            <a href="{{ route('gato.index') }}" class="text-sm text-indigo-400 hover:underline">
                &larr; Volver al Lobby
            </a>
        </div>
    </x-slot>

    @php
        $casillas = str_split($partida->tablero);
        $esMiTurno = ($partida->estado === 'en_curso' && $partida->turno_actual_id == auth()->id());
    @endphp

    <div class="py-8 max-w-md mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex justify-between text-sm text-gray-800 dark:text-gray-200">
            <div>
                <span class="text-xs text-gray-400 block">Jugador X</span>
                <strong>{{ $partida->jugadorX?->name ?? 'Jugador X' }}</strong>
            </div>
            <div class="text-right">
                <span class="text-xs text-gray-400 block">Jugador O</span>
                <strong>{{ $partida->jugadorO?->name ?? 'Esperando rival...' }}</strong>
            </div>
        </div>

        <div class="p-4 rounded-lg text-center font-semibold bg-gray-800 text-white border border-gray-700">
            @if($partida->estado === 'esperando')
                Esperando a que se una el segundo jugador...
            @elseif($partida->estado === 'en_curso')
                {{ $esMiTurno ? '¡ES TU TURNO! Toca una casilla.' : 'Turno del oponente...' }}
            @elseif($partida->estado === 'finalizada')
                Ganador: {{ $partida->ganador?->name ?? 'Jugador' }}
            @elseif($partida->estado === 'empate')
                ¡Empate!
            @elseif($partida->estado === 'cancelada')
                Partida cancelada.
            @endif
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 90px); gap: 10px; justify-content: center;">
            @foreach($casillas as $i => $valor)
                @if($valor === '-' && $esMiTurno)
                    <form action="{{ route('gato.jugar', [$partida, $i]) }}" method="POST">
                        @csrf
                        <button type="submit"
                                style="width: 90px; height: 90px; background-color: #111827; border: 2px solid #4f46e5; color: #ffffff;"
                                class="rounded-lg text-3xl font-bold">
                        </button>
                    </form>
                @else
                    <div style="width: 90px; height: 90px; background-color: #1f2937; border: 2px solid #374151; color: {{ $valor === 'X' ? '#60a5fa' : '#fb7185' }};"
                         class="rounded-lg text-3xl font-bold flex items-center justify-center">
                        {{ $valor === '-' ? '' : $valor }}
                    </div>
                @endif
            @endforeach
        </div>

        @if(in_array($partida->estado, ['esperando', 'en_curso']))
            <div class="text-center">
                <form action="{{ route('gato.abandonar', $partida) }}" method="POST">
                    @csrf
                    <button type="submit"
                            style="background-color: #dc2626; color: #ffffff;"
                            class="px-4 py-2 rounded-md text-xs font-semibold uppercase">
                        Abandonar Partida
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>