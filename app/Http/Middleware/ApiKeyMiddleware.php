<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');
        
        // Check if API key is provided
        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API key is missing'
            ], 401);
        }
        
        // Validate API key against the configured key
        // We'll store this in the .env file
        $validApiKey = config('services.invoice_app.api_key');
        
        if ($apiKey !== $validApiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API key'
            ], 403);
        }
        
        return $next($request);
    }
}
