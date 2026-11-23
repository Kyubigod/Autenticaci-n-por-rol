<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Prácticas Laravel
        </h2>
    </x-slot>

    @php
        $rol = auth()->user()->rol;

        $practicas = [
            [
    'numero' => 'Práctica 1.1',
    'titulo' => 'Autenticación por rol',
    'descripcion' => 'Login con código de verificación en dos pasos (2FA) enviado por correo para el administrador.',
    'ruta' => '#',
    'acceso' => false,
    'texto_bloqueado' => 'Completada (Sesión iniciada)',
],
            [
                'numero' => 'Práctica 1.2',
                'titulo' => 'CRUD de usuarios',
                'descripcion' => 'Gestión de usuarios con permisos basados en roles, middlewares y SoftDeletes.',
                'ruta' => route('users.index'),
                'acceso' => in_array($rol, ['admin', 'empleado']),
                'texto_bloqueado' => 'Sin acceso',
            ],
            [
                'numero' => 'Práctica 1.3',
                'titulo' => 'Sistema académico',
                'descripcion' => 'Módulo de alumnos, control de faltas y registro de calificaciones.',
                'ruta' => route('alumnos.index'),
                'acceso' => in_array($rol, ['profesor', 'alumno']),
                'texto_bloqueado' => 'Sin acceso',
            ],
            [
                'numero' => 'Práctica 1.4',
                'titulo' => 'Gestión de productos',
                'descripcion' => 'Catálogo con paginación, filtros dinámicos y ordenamiento.',
                'ruta' => route('productos.index'),
                'acceso' => in_array($rol, ['admin', 'empleado']),
                'texto_bloqueado' => 'Sin acceso',
            ],
            [
                'numero' => 'Práctica 1.5',
                'titulo' => 'Reservación de equipos',
                'descripcion' => 'Control de sesiones independientes y disponibilidad de equipos.',
                'ruta' => '#',
                'acceso' => false,
                'texto_bloqueado' => 'Sin acceso',
            ],
            [
                'numero' => 'Práctica 1.6',
                'titulo' => 'Juego del gato',
                'descripcion' => 'Partidas multijugador en tiempo real.',
                'ruta' => '#',
                'acceso' => false,
                'texto_bloqueado' => 'Sin acceso',
            ],
        ];
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Encabezado separado dentro del contenido --}}
            <div style="margin-bottom: 2rem;">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Bienvenido, {{ auth()->user()->name }}
                </h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Rol: <span class="font-semibold text-indigo-400">{{ auth()->user()->rol }}</span>
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($practicas as $practica)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ $practica['numero'] }}
                            </h3>
                            <p class="text-sm font-medium text-indigo-500 dark:text-indigo-400 mt-1">
                                {{ $practica['titulo'] }}
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-3">
                                {{ $practica['descripcion'] }}
                            </p>
                        </div>

                        <div class="mt-6">
                            @if($practica['acceso'])
                                <a href="{{ $practica['ruta'] }}"
                                   style="background-color: #2563eb; color: #ffffff;"
                                   class="block w-full text-center px-4 py-2 rounded-md font-semibold text-sm hover:opacity-90 transition">
                                    Entrar
                                </a>
                            @else
                                <button type="button" disabled
                                        style="background-color: #1f2937; border: 1px solid #374151; color: #6b7280;"
                                        class="block w-full text-center px-4 py-2 rounded-md font-semibold text-sm cursor-not-allowed">
                                    {{ $practica['texto_bloqueado'] }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>