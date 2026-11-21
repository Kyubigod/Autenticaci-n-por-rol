<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      if (auth()->check() && auth()->user()->rol === 'admin' && auth()->user()->two_factor_code) {
    return redirect()->to(URL::temporarySignedRoute('verify', now()->addMinutes(5), ['user' => auth()->user()->id]));
}

return $next($request);
    }
}
