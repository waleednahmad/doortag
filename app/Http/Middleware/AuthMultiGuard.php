<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMultiGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated with either web or customer guard
        if (Auth::guard('web')->check() || Auth::guard('customer')->check()) {
            return $next($request);
        }

        // If not authenticated with either guard, redirect to login
        return redirect()->route('login');
    }
}
