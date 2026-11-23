<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::latest()->paginate(10);
        return view('equipos.index', compact('equipos'));
    }

    public function create()
    {
        return view('equipos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo'           => 'required|string|max:50|unique:equipos,codigo',
            'nombre'           => 'required|string|max:255',
            'especificaciones' => 'required|string',
            'ubicacion'        => 'required|string|max:255',
            'estado'           => 'required|in:Disponible,Mantenimiento',
        ]);

        Equipo::create($validated);

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo de cómputo registrado correctamente.');
    }

    public function edit(Equipo $equipo)
    {
        return view('equipos.edit', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $validated = $request->validate([
            'codigo'           => [
                'required',
                'string',
                'max:50',
                Rule::unique('equipos', 'codigo')->ignore($equipo->id),
            ],
            'nombre'           => 'required|string|max:255',
            'especificaciones' => 'required|string',
            'ubicacion'        => 'required|string|max:255',
            'estado'           => 'required|in:Disponible,Mantenimiento',
        ]);

        $equipo->update($validated);

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipo $equipo)
    {
        // Evitar eliminar el equipo si tiene reservaciones activas
        if ($equipo->reservaciones()->where('estado', 'Activa')->exists()) {
            return redirect()->route('equipos.index')
                ->with('error', 'No se puede eliminar el equipo porque tiene reservaciones activas.');
        }

        $equipo->delete();

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }
}