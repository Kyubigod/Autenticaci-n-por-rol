<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->rol === 'alumno') {
            $alumnos = Alumno::where('user_id', $user->id)->get();
            return view('alumnos.index', [
                'alumnos' => $alumnos,
                'orden' => 'nombre',
                'direccion' => 'asc',
            ]);
        }

        $orden = $request->query('orden', 'nombre');
        $direccion = $request->query('direccion', 'asc');

        if (!in_array($orden, ['nombre', 'calificacion', 'faltas'])) {
            $orden = 'nombre';
        }

        if (!in_array($direccion, ['asc', 'desc'])) {
            $direccion = 'asc';
        }

        $alumnos = Alumno::orderBy($orden, $direccion)->get();

        return view('alumnos.index', compact('alumnos', 'orden', 'direccion'));
    }

    public function create()
{
    return view('alumnos.create');
}

   

public function store(Request $request)
{
    $validated = $request->validate([
        'nombre'   => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    DB::transaction(function () use ($validated) {
        $user = User::create([
            'name'     => $validated['nombre'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol'      => 'alumno',
        ]);

        Alumno::create([
            'user_id'      => $user->id,
            'nombre'       => $validated['nombre'],
            'faltas'       => 0,
            'calificacion' => 0,
        ]);
    });

    return redirect()->route('alumnos.index')
        ->with('success', 'Alumno registrado correctamente (inicia con 0 faltas y calificación 0).');
}

public function edit(Alumno $alumno)
{
    return view('alumnos.edit', compact('alumno'));
}

public function update(Request $request, Alumno $alumno)
{
    $validated = $request->validate([
        'nombre'       => 'required|string|max:255',
        'faltas'       => 'required|integer|min:0|max:30',
        'calificacion' => 'required|numeric|min:0|max:100',
    ]);

    if ($validated['faltas'] >= 4) {
        $validated['calificacion'] = 0;
    }

    DB::transaction(function () use ($alumno, $validated) {
        $alumno->update([
            'nombre'       => $validated['nombre'],
            'faltas'       => $validated['faltas'],
            'calificacion' => $validated['calificacion'],
        ]);

        if ($alumno->user) {
            $alumno->user->update([
                'name' => $validated['nombre'],
            ]);
        }
    });

    return redirect()->route('alumnos.index')
        ->with('success', 'Registro académico actualizado correctamente.');
}

    public function destroy(Alumno $alumno)
{
    DB::transaction(function () use ($alumno) {
        $user = $alumno->user;
        $alumno->delete();

        if ($user) {
            $user->forceDelete(); 
        }
    });

    return redirect()->route('alumnos.index')
        ->with('success', 'Alumno eliminado correctamente.');
}
}