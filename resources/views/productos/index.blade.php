<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Productos
            </h2>

            @if(auth()->user()->rol === 'admin')
                <a href="{{ route('productos.create') }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md text-sm">
                    + Nuevo Producto
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Panel de Filtros y Paginación --}}
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <form method="GET" action="{{ route('productos.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                    
                    {{-- Buscar por nombre --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" value="{{ request('nombre') }}"
                               placeholder="Buscar producto..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>

                    {{-- Filtrar por categoría --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Categoría</label>
                        <select name="categoria_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">Todas</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" @selected(request('categoria_id') == $categoria->id)>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Precio mínimo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio Mín.</label>
                        <input type="number" step="0.01" name="precio_min" value="{{ request('precio_min') }}"
                               placeholder="0.00"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>

                    {{-- Precio máximo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Precio Máx.</label>
                        <input type="number" step="0.01" name="precio_max" value="{{ request('precio_max') }}"
                               placeholder="9999.99"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>

                    {{-- Filtrar por estado --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <select name="estado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">Todos</option>
                            <option value="Disponible" @selected(request('estado') === 'Disponible')>Disponible</option>
                            <option value="Agotado" @selected(request('estado') === 'Agotado')>Agotado</option>
                        </select>
                    </div>

                    {{-- Registros por página --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mostrar</label>
                        <select name="per_page" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                            @foreach([10, 15, 25, 50] as $cantidad)
                                <option value="{{ $cantidad }}" @selected($perPage == $cantidad)>
                                    {{ $cantidad }} registros
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Conservar ordenamiento actual al filtrar --}}
                    <input type="hidden" name="sort" value="{{ $sort }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">

                    {{-- Botones Filtrar y Limpiar --}}
                    <div class="md:col-span-5 flex gap-2 justify-end">
                        <a href="{{ route('productos.index') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md text-sm">
                            Limpiar filtros
                        </a>
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md text-sm">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabla de Productos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- Contador de registros y página actual --}}
                <div class="mb-4 text-sm text-gray-600 flex justify-between items-center">
                    <span>
                        Total de registros encontrados: <strong>{{ $productos->total() }}</strong>
                    </span>
                    <span>
                        Página <strong>{{ $productos->currentPage() }}</strong> de <strong>{{ $productos->lastPage() }}</strong>
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left text-gray-700">
                                    <x-sortable-header column="nombre" label="Nombre" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Descripción</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Categoría</th>
                                <th class="px-4 py-3 text-left text-gray-700">
                                    <x-sortable-header column="precio" label="Precio" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-left text-gray-700">
                                    <x-sortable-header column="existencias" label="Existencias" :sort="$sort" :direction="$direction" />
                                </th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Estado</th>
                                <th class="px-4 py-3 text-left text-gray-700">
                                    <x-sortable-header column="created_at" label="Fecha Registro" :sort="$sort" :direction="$direction" />
                                </th>
                                @if(auth()->user()->rol === 'admin')
                                    <th class="px-4 py-3 text-center font-semibold text-gray-700">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($productos as $producto)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $producto->id }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $producto->nombre }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $producto->descripcion }}</td>
                                    <td class="px-4 py-3">{{ $producto->categoria->nombre }}</td>
                                    <td class="px-4 py-3">${{ number_format($producto->precio, 2) }}</td>
                                    <td class="px-4 py-3">{{ $producto->existencias }}</td>
                                    <td class="px-4 py-3">
                                        <x-estado-badge :estado="$producto->estado" />
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $producto->created_at->format('d/m/Y H:i') }}</td>
                                    
                                    @if(auth()->user()->rol === 'admin')
                                        <td class="px-4 py-3 text-center flex justify-center gap-2">
                                            <a href="{{ route('productos.edit', $producto) }}"
                                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                                                Editar
                                            </a>
                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->rol === 'admin' ? 9 : 8 }}" class="px-4 py-6 text-center text-gray-500">
                                        No se encontraron productos con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Enlaces de paginación --}}
                <div class="mt-4">
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>