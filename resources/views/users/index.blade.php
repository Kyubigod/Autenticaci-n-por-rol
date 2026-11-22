<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Usuarios
            </h2>

          <a href="{{ route('users.create') }}"
   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white transition">
    + Nuevo Usuario
</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-emerald-900/60 border border-emerald-500 text-emerald-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-900/60 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif
                
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/60 border-b border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="py-3.5 px-4">ID</th>
                                <th class="py-3.5 px-4">Nombre</th>
                                <th class="py-3.5 px-4">Correo</th>
                                <th class="py-3.5 px-4">Rol</th>
                                <th class="py-3.5 px-4">Estado</th>
                                <th class="py-3.5 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm text-gray-800 dark:text-gray-200">
                            @foreach($users as $user)
                                <tr class="{{ $user->trashed() ? 'bg-red-950/30 text-gray-400' : 'hover:bg-gray-50 dark:hover:bg-gray-700/40' }} transition">
                                    <td class="py-3.5 px-4 font-mono text-xs text-gray-400">{{ $user->id }}</td>
                                    <td class="py-3.5 px-4 font-medium">{{ $user->name }}</td>
                                    <td class="py-3.5 px-4 text-gray-300">{{ $user->email }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $user->rol === 'admin' ? 'bg-indigo-900/70 text-indigo-300 border border-indigo-700' : 'bg-gray-700 text-gray-300' }}">
                                            {{ $user->rol }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($user->trashed())
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900/70 text-red-300 border border-red-700">
                                                Deshabilitado
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-900/70 text-emerald-300 border border-emerald-700">
                                                Activo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right space-x-3 whitespace-nowrap">
                                        <a href="{{ route('users.show', $user->id) }}" class="text-indigo-400 hover:text-indigo-300 font-medium">
                                            Ver
                                        </a>

                                        @if(!$user->trashed())
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-amber-400 hover:text-amber-300 font-medium">
                                                Editar
                                            </a>

                                            @if(auth()->user()->rol === 'admin' && auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 font-medium" onclick="return confirm('¿Deshabilitar este usuario?')">
                                                        Deshabilitar
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            @if(auth()->user()->rol === 'admin')
                                                <form action="{{ route('users.restore', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-emerald-400 hover:text-emerald-300 font-medium">
                                                        Restaurar
                                                    </button>
                                                </form>

                                                <form action="{{ route('users.forceDelete', $user->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-400 font-semibold" onclick="return confirm('¿Eliminar permanentemente? Esta acción no se puede deshacer.')">
                                                        Eliminar definitivo
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>