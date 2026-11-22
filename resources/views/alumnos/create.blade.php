<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Registrar Nuevo Alumno
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="bg-red-900/50 border border-red-700 text-red-200 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('alumnos.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre completo del alumno
                        </label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Correo electrónico (acceso al sistema)
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Contraseña inicial (mínimo 8 caracteres)
                        </label>
                        <input type="password" name="password" id="password" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        * El alumno se registrará inicialmente con <strong>0 faltas</strong> y calificación <strong>0 (NA)</strong>. Podrás capturar sus calificaciones y asistencias desde la opción Editar.
                    </p>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('alumnos.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                            Registrar Alumno
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>