<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Calificación y Faltas
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if($errors->any())
                <div style="background-color: #450a0a; border: 1px solid #ef4444; color: #fecaca;" class="px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('alumnos.update', $alumno->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Usuario Vinculado</label>
                        <input type="text" value="{{ $alumno->user->email ?? 'Sin usuario' }}" disabled
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-400 shadow-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Completo del Alumno</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Faltas</label>
                            <input type="number" name="faltas" value="{{ old('faltas', $alumno->faltas) }}" min="0" step="1" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Calificación (0 - 100)</label>
                            <input type="number" name="calificacion" value="{{ old('calificacion', $alumno->calificacion) }}" min="0" max="100" step="0.01" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end items-center space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('alumnos.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
                            Actualizar Registro
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>