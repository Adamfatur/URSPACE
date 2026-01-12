<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     * Redirects to profile edit if required fields are missing.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isProfileComplete()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Mohon lengkapi profil Anda (NIM, Program Studi, dan Angkatan) sebelum melanjutkan.');
        }

        return $next($request);
    }
}
