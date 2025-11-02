<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  The permission required to access the route
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Check if user is admin
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Check if user has the required permission
        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'Unauthorized. You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
