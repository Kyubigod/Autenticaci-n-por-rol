<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->rol == 'admin') {
            $users = User::withTrashed()->get();
        }
        if (auth()->user()->rol == 'empleado') {
            $users = User::all();
        }
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'rol'      => 'required|string|in:admin,empleado,alumno,profesor,estudiante,jugador',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'rol'      => auth()->user()->rol === 'admin' ? $request->rol : 'empleado',
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (in_array(auth()->user()->rol, ['admin', 'empleado'])) {
            $user = User::findOrFail($id);
            return view('users.show', compact('user'));
        } else {
            abort(403, 'No tienes permiso para acceder a esta página');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (auth()->user()->rol == 'admin' || auth()->user()->rol == 'empleado') {
            $user = User::findOrFail($id);
            return view('users.edit', compact('user'));
        } else {
            abort(403, 'No tienes permiso para ingresar aquí');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        if (auth()->user()->rol !== 'admin' && auth()->id() != $user->id) {
            abort(403, 'No tienes permiso para editar este usuario.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'rol'      => 'nullable|string|in:admin,empleado,alumno,profesor,estudiante,jugador',
        ]);

        $datos = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if (auth()->user()->rol === 'admin' && $user->id != auth()->id() && $request->filled('rol')) {
            $datos['rol'] = $request->rol;
        }

        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $user->update($datos);

        return redirect()->route('users.index')->with('success', 'Actualizado Correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        if ($user->id == auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No se puede eliminar al administrador con este id, porque lo estás usando actualmente');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Soft Deleteado Correctamente');
    }

    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'Recuperado Correctamente');
    }

    public function forceDelete(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        if ($user->id == auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No se puede eliminar al administrador con este id, porque lo estás usando actualmente');
        }
        $user->forceDelete();
        return redirect()->route('users.index')->with('success', 'Eliminado Correctamente');
    }
}