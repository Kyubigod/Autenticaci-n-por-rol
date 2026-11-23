<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservacionController extends Controller
{
    /**
     * Muestra el panel de reservaciones adaptado al rol del usuario en sesión.
     */
    public function index()
    {
        // Obtenemos el usuario autenticado de la sesión actual (independiente por navegador/equipo)
        $user = auth()->user();

        if ($user->rol === 'admin') {
            // El administrador consulta TODAS las reservaciones del sistema
            // Usamos with(['user', 'equipo']) para cargar en una sola consulta quién reservó y qué equipo
            $reservaciones = Reservacion::with(['user', 'equipo'])
                ->latest()
                ->paginate(10);

            return view('reservaciones.index', compact('reservaciones'));
        }

        // Si es estudiante:
        // 1. Consultamos ÚNICAMENTE las reservaciones vinculadas a su user_id de sesión (privacidad)
        $reservaciones = Reservacion::with('equipo')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // 2. Consultamos únicamente los equipos cuyo estado físico sea 'Disponible' para que pueda reservarlos
        $equiposDisponibles = Equipo::where('estado', 'Disponible')
            ->orderBy('codigo')
            ->get();

        return view('reservaciones.index', compact('reservaciones', 'equiposDisponibles'));
    }

    /**
     * Registra una nueva reservación evitando colisiones simultáneas (concurrencia).
     */
    public function store(Request $request)
    {
        // Validación básica de formato y coherencia de fechas/horas
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

        // Iniciamos una transacción atómica en SQL Server:
        // Si algo falla o hay conflicto en medio del proceso, se revierte todo automáticamente
        DB::transaction(function () use ($validated) {

            // CANDADO DE CONCURRENCIA (Bloqueo pesimista):
            // lockForUpdate() bloquea la fila de este equipo en SQL Server hasta que termine la transacción.
            // Si dos estudiantes dan clic en "Reservar" al mismo milisegundo, el segundo espera aquí
            // hasta que el primero termine de guardar, evitando reservas duplicadas.
            $equipo = Equipo::where('id', $validated['equipo_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Verificamos que un administrador no lo haya puesto en 'Mantenimiento' justo antes
            if ($equipo->estado !== 'Disponible') {
                throw ValidationException::withMessages([
                    'equipo_id' => 'El equipo seleccionado ya no se encuentra disponible (en mantenimiento).',
                ]);
            }

            // Verificamos traslape de horarios para ese mismo equipo y fecha en reservaciones activas:
            // Dos rangos de tiempo [A_inicio, A_fin] y [B_inicio, B_fin] se cruzan si:
            // A_inicio < B_fin  Y  A_fin > B_inicio
            $existeTraslape = Reservacion::where('equipo_id', $equipo->id)
                ->where('fecha', $validated['fecha'])
                ->where('estado', 'Activa')
                ->where(function ($query) use ($validated) {
                    $query->where('hora_inicio', '<', $validated['hora_fin'])
                          ->where('hora_fin', '>', $validated['hora_inicio']);
                })
                ->exists();

            // Si ya existe otra reservación activa en ese horario, detenemos la transacción y avisamos
            if ($existeTraslape) {
                throw ValidationException::withMessages([
                    'hora_inicio' => 'El equipo ya está reservado en ese horario. Por favor elige otro horario o equipo.',
                ]);
            }

            // Guardamos la reservación vinculándola estrictamente al ID de la sesión activa (auth()->id())
            // Nunca tomamos el user_id del formulario para impedir suplantación de identidad
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

    /**
     * Cancela una reservación respetando la privacidad de sesión y condiciones de estado.
     */
    public function cancelar(Reservacion $reservacion)
    {
        $user = auth()->user();

        // SEGURIDAD CONTRA MANIPULACIÓN DE URL:
        // Si el usuario es estudiante y el user_id de la reservación NO coincide con su sesión actual,
        // bloqueamos inmediatamente con error 403 (evita cancelar reservas de otros cambiando el ID en la URL)
        if ($user->rol === 'estudiante' && $reservacion->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder o cancelar una reservación de otro usuario.');
        }

        // Condición establecida: solo se pueden cancelar reservaciones que sigan en estado 'Activa'
        if ($reservacion->estado !== 'Activa') {
            return redirect()->route('reservaciones.index')
                ->with('error', 'Esta reservación ya fue cancelada previamente.');
        }

        // Cambiamos el estado a 'Cancelada' (esto libera automáticamente el horario para otros estudiantes)
        $reservacion->update([
            'estado' => 'Cancelada',
        ]);

        return redirect()->route('reservaciones.index')
            ->with('success', 'La reservación ha sido cancelada correctamente.');
    }
}