<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorCodeController extends Controller
{
    public function verify()
    {
        return view('auth.verify');
    }
    
    public function verifyPost(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        $user = auth()->user();

        if ((string) $user->two_factor_code !== (string) $request->code) {
            return back()->withErrors(['code' => 'El código es incorrecto']);
        }

        if ($user->two_factor_expires_at < now()) {
            return back()->withErrors(['code' => 'El código ha expirado']);
        }

        $user->clearTwoFactorCode();

        return redirect()->route('dashboard')->with('message', 'Has iniciado sesión correctamente');
    }
}