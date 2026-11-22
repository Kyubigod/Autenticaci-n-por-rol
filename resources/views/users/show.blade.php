<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detalle del Usuario
            </h2>
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6 space-y-4 text-sm">
                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="font-medium text-gray-500 dark:text-gray-400">ID</span>
                    <span class="font-mono text-gray-900 dark:text-gray-100">{{ $user->id }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="font-medium text-gray-500 dark:text-gray-400">Nombre</span>
                    <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="font-medium text-gray-500 dark:text-gray-400">Correo electrónico</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="font-medium text-gray-500 dark:text-gray-400">Rol</span>
                    <span class="text-gray-900 dark:text-gray-100 capitalize">{{ $user->rol }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="font-medium text-gray-500 dark:text-gray-400">Estado</span>
                    @if($user->trashed())
                        <span class="text-red-400 font-medium">Deshabilitado ({{ $user->deleted_at->format('d/m/Y H:i') }})</span>
                    @else
                        <span class="text-emerald-400 font-medium">Activo</span>
                    @endif
                </div>
                <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="font-medium text-gray-500 dark:text-gray-400">Fecha de registro</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $user->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-500 dark:text-gray-400">Última actualización</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $user->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>