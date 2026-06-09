<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // Check for OWNER guard first
            if ($guard === 'owner' && Auth::guard($guard)->check()) {
                return redirect()->route('owner.dashboard');
            }
            
            // Check for ADMIN guard (if you have one)
            if ($guard === 'admin' && Auth::guard($guard)->check()) {
                return redirect()->route('admin.dashboard');
            }
            
            // Check for default USER/TENANT guard
            if ($guard === 'web' && Auth::guard($guard)->check()) {
                return redirect('/home');
            }
            
            // Default guard (web/users)
            if ($guard === null && Auth::guard($guard)->check()) {
                // If the authenticated user is an owner (through web guard)
                if (Auth::guard($guard)->user() && Auth::guard($guard)->user()->role === 'owner') {
                    return redirect()->route('owner.dashboard');
                }
                return redirect('/home');
            }
        }

        return $next($request);
    }
}