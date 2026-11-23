<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ auth()->user()->rol === 'admin' ? 'Administración Global de Reservaciones' : 'Reservación de Equipos de Cómputo' }}
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Sesión activa: <strong class="text-gray-800 dark:text-gray-200">{{ auth()->user()->name }}</strong> ({{ auth()->user()->rol }})
            </span>
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

            @if($errors->any())
                <div style="background-color: #450a0a; border: 1px solid #ef4444; color: #fecaca;" class="px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORMULARIO DE RESERVACIÓN SOLO PARA ESTUDIANTES --}}
            @if(auth()->user()->rol === 'estudiante')
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                        Reservar un Equipo Disponible
                    </h3>

                    <form action="{{ route('reservaciones.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Equipo Disponible</label>
                            <select name="equipo_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">-- Selecciona un equipo --</option>
                                @foreach($equiposDisponibles as $equipo)
                                    <option value="{{ $equipo->id }}" {{ old('equipo_id') == $equipo->id ? 'selected' : '' }}>
                                        {{ $equipo->codigo }} - {{ $equipo->nombre }} ({{ $equipo->ubicacion }} | {{ $equipo->especificaciones }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                            <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo (Opcional)</label>
                            <input type="text" name="motivo" value="{{ old('motivo') }}" placeholder="Ej. Práctica de desarrollo web"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora de Inicio</label>
                            <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora de Fin</label>
                            <input type="time" name="hora_fin" value="{{ old('hora_fin') }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        </div>

                        <div class="md:col-span-2 flex justify-end pt-2">
                            <button type="submit"
                                    style="background-color: #2563eb; color: #ffffff;"
                                    class="px-5 py-2 rounded-md text-xs font-semibold uppercase tracking-widest shadow-sm">
                                Confirmar Reservación
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- TABLA DE RESERVACIONES (PRIVADA PARA ESTUDIANTE / GLOBAL PARA ADMIN) --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-x-auto">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        {{ auth()->user()->rol === 'admin' ? 'Todas las Reservaciones Registradas' : 'Mis Reservaciones' }}
                    </h3>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            @if(auth()->user()->rol === 'admin')
                                <th class="px-6 py-3 text-left">Estudiante</th>
                            @endif
                            <th class="px-6 py-3 text-left">Equipo</th>
                            <th class="px-6 py-3 text-left">Fecha</th>
                            <th class="px-6 py-3 text-left">Horario</th>
                            <th class="px-6 py-3 text-left">Motivo</th>
                            <th class="px-6 py-3 text-left">Estado</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-900 dark:text-gray-200">
                        @forelse($reservaciones as $reservacion)
                            <tr>
                                <td class="px-6 py-4 font-mono">#{{ $reservacion->id }}</td>
                                @if(auth()->user()->rol === 'admin')
                                    <td class="px-6 py-4 font-medium">{{ $reservacion->user->name ?? 'Usuario eliminado' }}</td>
                                @endif
                                <td class="px-6 py-4">
                                    <span class="font-semibold">{{ $reservacion->equipo->codigo }}</span> - {{ $reservacion->equipo->nombre }}
                                </td>
                                <td class="px-6 py-4">{{ $reservacion->fecha }}</td>
                                <td class="px-6 py-4 font-mono">
                                    {{ substr($reservacion->hora_inicio, 0, 5) }} - {{ substr($reservacion->hora_fin, 0, 5) }}
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $reservacion->motivo ?? 'Sin motivo' }}</td>
                                <td class="px-6 py-4">
                                    @if($reservacion->estado === 'Activa')
                                        <span style="background-color: #064e3b; color: #6ee7b7; border: 1px solid #10b981;" class="px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                            Activa
                                        </span>
                                    @else
                                        <span style="background-color: #450a0a; color: #fca5a5; border: 1px solid #ef4444;" class="px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                            Cancelada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($reservacion->estado === 'Activa')
                                        <form action="{{ route('reservaciones.cancelar', $reservacion) }}" method="POST"
                                              onsubmit="return confirm('¿Deseas cancelar esta reservación?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    style="background-color: #dc2626; color: #ffffff;"
                                                    class="inline-flex items-center px-3 py-1 rounded text-xs font-semibold">
                                                Cancelar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Sin acciones</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->rol === 'admin' ? 8 : 7 }}" class="px-6 py-8 text-center text-gray-500">
                                    No hay reservaciones registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(auth()->user()->rol === 'admin')
                <div>
                    {{ $reservaciones->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>