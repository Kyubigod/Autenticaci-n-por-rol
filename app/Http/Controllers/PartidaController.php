<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartidaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->rol === 'admin') {
            $partidas = Partida::with(['jugadorX', 'jugadorO', 'ganador'])
                ->orderByDesc('id')
                ->paginate(10);

            return view('gato.admin', compact('partidas'));
        }

        $activosIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(10)->timestamp)
            ->pluck('user_id')
            ->unique();

        $jugadoresDisponibles = User::where('rol', 'jugador')
            ->whereIn('id', $activosIds)
            ->where('id', '!=', $user->id)
            ->get();

        $partidasDisponibles = Partida::with('jugadorX')
            ->where('estado', 'esperando')
            ->where('jugador_x_id', '!=', $user->id)
            ->orderByDesc('id')
            ->get();

        $misPartidasActivas = Partida::with(['jugadorX', 'jugadorO'])
            ->whereIn('estado', ['esperando', 'en_curso'])
            ->where(function ($q) use ($user) {
                $q->where('jugador_x_id', $user->id)
                  ->orWhere('jugador_o_id', $user->id);
            })
            ->orderByDesc('id')
            ->get();

        $historial = Partida::with(['jugadorX', 'jugadorO', 'ganador'])
            ->whereIn('estado', ['finalizada', 'empate', 'cancelada'])
            ->where(function ($q) use ($user) {
                $q->where('jugador_x_id', $user->id)
                  ->orWhere('jugador_o_id', $user->id);
            })
            ->orderByDesc('id')
            ->get();

        return view('gato.index', compact(
            'jugadoresDisponibles',
            'partidasDisponibles',
            'misPartidasActivas',
            'historial'
        ));
    }

    public function store()
    {
        $user = auth()->user();

        $partida = Partida::create([
            'jugador_x_id'    => $user->id,
            'turno_actual_id' => $user->id,
            'tablero'         => '---------',
            'estado'          => 'esperando',
        ]);

        return redirect()->route('gato.show', $partida);
    }

    public function unirse(Partida $partida)
    {
        $user = auth()->user();

        DB::transaction(function () use ($partida, $user) {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            if ($p->estado !== 'esperando' || $p->jugador_x_id == $user->id || $p->jugador_o_id !== null) {
                abort(403, 'Esta partida ya no está disponible.');
            }

            $p->update([
                'jugador_o_id' => $user->id,
                'estado'       => 'en_curso',
            ]);
        });

        return redirect()->route('gato.show', $partida);
    }

    public function show(Partida $partida)
    {
        $user = auth()->user();

        if ($user->rol !== 'admin' && $partida->jugador_x_id != $user->id && $partida->jugador_o_id != $user->id) {
            abort(403, 'No formas parte de esta partida.');
        }

        $partida->load(['jugadorX', 'jugadorO', 'ganador']);

        return view('gato.show', compact('partida'));
    }

    public function moverSimple(Request $request, Partida $partida, int $casilla)
    {
        $user = auth()->user();

        DB::transaction(function () use ($partida, $user, $casilla) {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            if ($p->estado !== 'en_curso' || $p->turno_actual_id != $user->id) {
                return;
            }

            if ($casilla < 0 || $casilla > 8) {
                return;
            }

            $tablero = str_split($p->tablero);
            if ($tablero[$casilla] !== '-') {
                return;
            }

            $simbolo = ($p->jugador_x_id == $user->id) ? 'X' : 'O';
            $tablero[$casilla] = $simbolo;
            $nuevoTablero = implode('', $tablero);

            if ($this->verificarGanador($tablero, $simbolo)) {
                $p->update([
                    'tablero'         => $nuevoTablero,
                    'estado'          => 'finalizada',
                    'ganador_id'      => $user->id,
                    'turno_actual_id' => null,
                ]);
            } elseif (!in_array('-', $tablero)) {
                $p->update([
                    'tablero'         => $nuevoTablero,
                    'estado'          => 'empate',
                    'turno_actual_id' => null,
                ]);
            } else {
                $siguienteTurno = ($p->jugador_x_id == $user->id) ? $p->jugador_o_id : $p->jugador_x_id;
                $p->update([
                    'tablero'         => $nuevoTablero,
                    'turno_actual_id' => $siguienteTurno,
                ]);
            }
        });

        return redirect()->route('gato.show', $partida);
    }

    public function estado(Partida $partida)
    {
        $user = auth()->user();

        if ($user->rol !== 'admin' && $partida->jugador_x_id != $user->id && $partida->jugador_o_id != $user->id) {
            abort(403);
        }

        $partida->load(['jugadorX', 'jugadorO', 'ganador']);

        return response()->json([
            'id'              => $partida->id,
            'tablero'         => str_split($partida->tablero),
            'estado'          => $partida->estado,
            'turno_actual_id' => $partida->turno_actual_id,
            'jugador_x'       => $partida->jugadorX?->name,
            'jugador_o'       => $partida->jugadorO?->name ?? 'Esperando rival...',
            'ganador_id'      => $partida->ganador_id,
            'ganador_nombre'  => $partida->ganador?->name,
        ]);
    }

    public function mover(Request $request, Partida $partida)
    {
        $request->validate([
            'casilla' => 'required|integer|min:0|max:8',
        ]);

        $user = auth()->user();
        $casilla = (int) $request->casilla;

        return DB::transaction(function () use ($partida, $user, $casilla) {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            if ($p->estado !== 'en_curso') {
                return response()->json(['error' => 'La partida no está en curso.'], 422);
            }

            if ($p->turno_actual_id != $user->id) {
                return response()->json(['error' => 'No es tu turno.'], 403);
            }

            $tablero = str_split($p->tablero);

            if ($tablero[$casilla] !== '-') {
                return response()->json(['error' => 'Esa casilla ya está ocupada.'], 422);
            }

            $simbolo = ($p->jugador_x_id == $user->id) ? 'X' : 'O';
            $tablero[$casilla] = $simbolo;
            $nuevoTablero = implode('', $tablero);

            if ($this->verificarGanador($tablero, $simbolo)) {
                $p->update([
                    'tablero'         => $nuevoTablero,
                    'estado'          => 'finalizada',
                    'ganador_id'      => $user->id,
                    'turno_actual_id' => null,
                ]);
            } elseif (!in_array('-', $tablero)) {
                $p->update([
                    'tablero'         => $nuevoTablero,
                    'estado'          => 'empate',
                    'turno_actual_id' => null,
                ]);
            } else {
                $siguienteTurno = ($p->jugador_x_id == $user->id) ? $p->jugador_o_id : $p->jugador_x_id;
                $p->update([
                    'tablero'         => $nuevoTablero,
                    'turno_actual_id' => $siguienteTurno,
                ]);
            }

            return response()->json(['ok' => true]);
        });
    }

    public function abandonar(Partida $partida)
    {
        $user = auth()->user();

        DB::transaction(function () use ($partida, $user) {
            $p = Partida::where('id', $partida->id)->lockForUpdate()->firstOrFail();

            if ($p->jugador_x_id != $user->id && $p->jugador_o_id != $user->id) {
                abort(403);
            }

            if ($p->estado === 'esperando') {
                $p->update(['estado' => 'cancelada', 'turno_actual_id' => null]);
            } elseif ($p->estado === 'en_curso') {
                $rivalId = ($p->jugador_x_id == $user->id) ? $p->jugador_o_id : $p->jugador_x_id;
                $p->update([
                    'estado'          => 'finalizada',
                    'ganador_id'      => $rivalId,
                    'turno_actual_id' => null,
                ]);
            }
        });

        return redirect()->route('gato.index')->with('success', 'Has abandonado la partida.');
    }

    public function cancelarAdmin(Partida $partida)
    {
        if (in_array($partida->estado, ['esperando', 'en_curso'])) {
            $partida->update([
                'estado'          => 'cancelada',
                'turno_actual_id' => null,
            ]);
        }

        return redirect()->route('gato.index')->with('success', 'Partida cancelada por el administrador.');
    }

    private function verificarGanador(array $t, string $s): bool
    {
        $lineas = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8],
            [0, 3, 6], [1, 4, 7], [2, 5, 8],
            [0, 4, 8], [2, 4, 6],
        ];

        foreach ($lineas as [$a, $b, $c]) {
            if ($t[$a] === $s && $t[$b] === $s && $t[$c] === $s) {
                return true;
            }
        }

        return false;
    }
}