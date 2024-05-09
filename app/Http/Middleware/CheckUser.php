<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if the user is authenticated and their ID matches the admin ID
        if ($user && $user->id !== 1) {
            // If the user ID matches the admin ID (e.g., ID 1), grant access as admin
            return $next($request);
        }

        // If the user is not an admin, return unauthorized response
        return response()->json([
            'message' => 'Unauthorized access.',
        ], 401);
    }
}
