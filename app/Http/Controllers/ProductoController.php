<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $columnasPermitidas = ['nombre', 'precio', 'existencias', 'created_at'];
        $sort = in_array($request->get('sort'), $columnasPermitidas) ? $request->get('sort') : 'created_at';
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        $porPaginaPermitidos = [10, 15, 25, 50];
        $perPage = in_array((int) $request->get('per_page'), $porPaginaPermitidos) ? (int) $request->get('per_page') : 10;

        $productos = Producto::with('categoria')
            ->when($request->filled('nombre'), function ($query) use ($request) {
                $query->where('nombre', 'like', '%' . $request->nombre . '%');
            })
            ->when($request->filled('categoria_id'), function ($query) use ($request) {
                $query->where('categoria_id', $request->categoria_id);
            })
            ->when($request->filled('precio_min'), function ($query) use ($request) {
                $query->where('precio', '>=', $request->precio_min);
            })
            ->when($request->filled('precio_max'), function ($query) use ($request) {
                $query->where('precio', '<=', $request->precio_max);
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                $query->where('estado', $request->estado);
            })
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias', 'sort', 'direction', 'perPage'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    public function store(StoreProductoRequest $request)
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}