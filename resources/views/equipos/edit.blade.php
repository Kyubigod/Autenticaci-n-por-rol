<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Equipo: {{ $equipo->codigo }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('equipos.update', $equipo) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código del Equipo</label>
                        <input type="text" name="codigo" value="{{ old('codigo', $equipo->codigo) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @error('codigo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre / Modelo</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $equipo->nombre) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Especificaciones Técnicas</label>
                        <textarea name="especificaciones" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">{{ old('especificaciones', $equipo->especificaciones) }}</textarea>
                        @error('especificaciones') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ubicación</label>
                        <input type="text" name="ubicacion" value="{{ old('ubicacion', $equipo->ubicacion) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                        @error('ubicacion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                        <select name="estado" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                            <option value="Disponible" {{ old('estado', $equipo->estado) === 'Disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="Mantenimiento" {{ old('estado', $equipo->estado) === 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        </select>
                        @error('estado') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('equipos.index') }}"
                           style="background-color: #4b5563; color: #ffffff;"
                           class="px-4 py-2 rounded-md text-xs font-semibold uppercase">
                            Cancelar
                        </a>
                        <button type="submit"
                                style="background-color: #2563eb; color: #ffffff;"
                                class="px-4 py-2 rounded-md text-xs font-semibold uppercase">
                            Actualizar Equipo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>