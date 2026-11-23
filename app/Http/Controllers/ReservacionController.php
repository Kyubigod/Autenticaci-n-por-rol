<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservacionController extends Controller
{
    public function index()
    {
        if(auth()->user()->rol == admin) {
            Reservacion::with(['user','equipo'])->latest()->get();
            return redirect()->route('reservaciones.index')-with('succes','Reservaciones cargadas');
        } elseif (auth()->user()->rol == estudiante) {
            Reservacion::where('user_id', 'auth_id')->with('equipo')->latest()->get();
            Equipo::where('estado', 'Disponible')->get();
        }
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'equipo_id'   => 'required|exists:equipos,id',
        'fecha'       => 'required|date|after_or_equal:today',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin'    => 'required|date_format:H:i|after:hora_inicio',
        'motivo'      => 'nullable|string|max:255',
    ], [
        'fecha.after_or_equal' => 'La fecha de reservación no puede ser anterior a hoy.',
        'hora_fin.after'       => 'La hora de fin debe ser posterior a la hora de inicio.',
    ]);

    DB::transaction(function () use ($validated) {

        $equipo = Equipo::where('id', $validated['equipo_id'])
            ->lockForUpdate()
            ->firstOrFail();

        if ($equipo->estado !== 'Disponible') {
            throw ValidationException::withMessages([
                'equipo_id' => 'El equipo seleccionado ya no se encuentra disponible (en mantenimiento).',
            ]);
        }

        $existeTraslape = Reservacion::where('equipo_id', $equipo->id)
            ->where('fecha', $validated['fecha'])
            ->where('estado', 'Activa')
            ->where(function ($query) use ($validated) {
                $query->where('hora_inicio', '<', $validated['hora_fin'])
                      ->where('hora_fin', '>', $validated['hora_inicio']);
            })
            ->exists();

        if ($existeTraslape) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'El equipo ya está reservado en ese horario. Por favor elige otro horario o equipo.',
            ]);
        }

        Reservacion::create([
            'user_id'     => auth()->id(),
            'equipo_id'   => $equipo->id,
            'fecha'       => $validated['fecha'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin'    => $validated['hora_fin'],
            'motivo'      => $validated['motivo'] ?? null,
            'estado'      => 'Activa',
        ]);
    });

    return redirect()->route('reservaciones.index')
        ->with('success', '¡Equipo reservado exitosamente!');
}
}
