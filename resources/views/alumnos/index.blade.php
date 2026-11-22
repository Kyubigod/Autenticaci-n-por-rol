<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ auth()->user()->rol === 'profesor' ? 'Gestión de Alumnos' : 'Mi Historial Académico' }}
            </h2>

            @if(auth()->user()->rol === 'profesor')
                <a href="{{ route('alumnos.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                    + Registrar Alumno
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div style="background-color: #064e3b; border: 1px solid #10b981; color: #a7f3d0;" class="px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900/50 text-gray-700 dark:text-gray-300 uppercase text-xs">
                            <tr>
                                @php
                                    $esProfesor = auth()->user()->rol === 'profesor';
                                    $siguienteDir = $direccion === 'asc' ? 'desc' : 'asc';
                                @endphp

                                <th class="px-6 py-3 text-left font-semibold">
                                    @if($esProfesor)
                                        <a href="{{ route('alumnos.index', ['orden' => 'nombre', 'direccion' => $orden === 'nombre' ? $siguienteDir : 'asc']) }}" class="hover:underline flex items-center gap-1">
                                            Nombre {{ $orden === 'nombre' ? ($direccion === 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    @else
                                        Nombre
                                    @endif
                                </th>

                                <th class="px-6 py-3 text-left font-semibold">
                                    @if($esProfesor)
                                        <a href="{{ route('alumnos.index', ['orden' => 'faltas', 'direccion' => $orden === 'faltas' ? $siguienteDir : 'asc']) }}" class="hover:underline flex items-center gap-1">
                                            Faltas {{ $orden === 'faltas' ? ($direccion === 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    @else
                                        Faltas
                                    @endif
                                </th>

                                <th class="px-6 py-3 text-left font-semibold">
                                    @if($esProfesor)
                                        <a href="{{ route('alumnos.index', ['orden' => 'calificacion', 'direccion' => $orden === 'calificacion' ? $siguienteDir : 'desc']) }}" class="hover:underline flex items-center gap-1">
                                            Calificación {{ $orden === 'calificacion' ? ($direccion === 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    @else
                                        Calificación
                                    @endif
                                </th>

                                <th class="px-6 py-3 text-left font-semibold">Equivalencia</th>

                                @if($esProfesor)
                                    <th class="px-6 py-3 text-right font-semibold">Acciones</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                            @forelse($alumnos as $alumno)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                    <td class="px-6 py-4 font-medium">{{ $alumno->nombre }}</td>
                                    <td class="px-6 py-4">{{ $alumno->faltas }}</td>
                                    <td class="px-6 py-4">{{ number_format($alumno->calificacion, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $colores = [
                                                'AU' => 'background-color: #064e3b; color: #a7f3d0; border: 1px solid #10b981;',
                                                'DE' => 'background-color: #1e3a8a; color: #bfdbfe; border: 1px solid #3b82f6;',
                                                'SA' => 'background-color: #78350f; color: #fde68a; border: 1px solid #f59e0b;',
                                                'NA' => 'background-color: #450a0a; color: #fecaca; border: 1px solid #ef4444;',
                                            ];
                                        @endphp
                                        <span style="{{ $colores[$alumno->equivalencia] ?? '' }}" class="px-2.5 py-0.5 rounded-full text-xs font-bold">
                                            {{ $alumno->equivalencia }}
                                        </span>
                                    </td>

                                    @if($esProfesor)
                                        <td class="px-6 py-4 text-right space-x-2">
                                            <a href="{{ route('alumnos.edit', $alumno->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-500 transition">
                                                Editar
                                            </a>

                                            <form action="{{ route('alumnos.destroy', $alumno->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este registro académico?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded text-xs font-semibold hover:bg-red-500 transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $esProfesor ? 5 : 4 }}" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ $esProfesor ? 'Aún no hay alumnos registrados.' : 'Tu profesor aún no ha cargado tus calificaciones.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>