<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // Check if there is an active session for the IP address
        if (Cache::has('session_' . $ip)) {
            $prompt = 'Previous sessions in progress and does not allow new sessions.';
            return response()->json(['message' => $prompt], 403);
        }

        // If no active session, create a session and proceed
        Cache::put('session_' . $ip, true, now()->addMinutes(60));

        return $next($request);
        
    }
}
