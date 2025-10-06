<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MetaTraderApiRateLimit
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
        // Only apply rate limiting to MetaTrader API endpoints
        if (!$this->isMetaTraderApiEndpoint($request)) {
            return $next($request);
        }

        $clientIp = $request->ip();
        $userId = $this->extractUserId($request);
        $identifier = $userId ? "user_{$userId}" : "ip_{$clientIp}";
        
        $rateLimitKey = "metatrader_rate_limit_{$identifier}";
        $globalRateLimitKey = "metatrader_global_rate_limit";
        
        // Check global rate limit (max 50 requests per minute across all users)
        $globalRequests = Cache::get($globalRateLimitKey, 0);
        if ($globalRequests >= 50) {
            Log::warning('Global MetaTrader API rate limit exceeded', [
                'client_ip' => $clientIp,
                'user_id' => $userId,
                'global_requests' => $globalRequests
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Service temporarily unavailable due to high traffic. Please try again in a few minutes.',
                'error_code' => 'GLOBAL_RATE_LIMITED'
            ], 429);
        }
        
        // Check per-user/IP rate limit (max 100 requests per minute per user/IP)
        $userRequests = Cache::get($rateLimitKey, 0);
        if ($userRequests >= 100) {
            Log::warning('Per-user MetaTrader API rate limit exceeded', [
                'client_ip' => $clientIp,
                'user_id' => $userId,
                'user_requests' => $userRequests
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please wait a minute before trying again.',
                'error_code' => 'USER_RATE_LIMITED'
            ], 429);
        }
        
        // Increment counters
        Cache::put($globalRateLimitKey, $globalRequests + 1, 60); // 1 minute
        Cache::put($rateLimitKey, $userRequests + 1, 60); // 1 minute
        
        // Add rate limit headers to response
        $response = $next($request);
        
        $response->headers->set('X-RateLimit-Limit', '10');
        $response->headers->set('X-RateLimit-Remaining', max(0, 10 - ($userRequests + 1)));
        $response->headers->set('X-RateLimit-Reset', time() + 60);
        
        return $response;
    }
    
    /**
     * Check if the request is for a MetaTrader API endpoint
     */
    private function isMetaTraderApiEndpoint(Request $request): bool
    {
        $path = $request->path();
        return str_starts_with($path, 'api/new-accounts') || 
               str_starts_with($path, 'api/new/accounts');
    }
    
    /**
     * Extract user ID from request
     */
    private function extractUserId(Request $request): ?string
    {
        // Extract from URL parameters
        $path = $request->path();
        if (preg_match('/\/get\/(\d+)/', $path, $matches)) {
            return $matches[1];
        }
        
        // Extract from request body
        $userId = $request->input('user_id');
        if ($userId) {
            return $userId;
        }
        
        // Extract from query parameters
        $userId = $request->query('user_id');
        if ($userId) {
            return $userId;
        }
        
        return null;
    }
} 