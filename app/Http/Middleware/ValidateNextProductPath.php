<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ValidateNextProductPath
{
    /**
     * Whitelist path yang diizinkan untuk next product redirect
     *
     * Hanya allow:
     * 1. Internal product page: /product?productId=X
     * 2. Stock API: http://localhost:8001/api/product/stock/check?...
     */
    private array $allowedPatterns = [
        '/^\/product\?productId=\d+$/',  // Internal: /product?productId=123
        '/^http:\/\/localhost:8001\/api\/product\/stock\/check\?.+$/',  // Stock API
        '/^http:\/\/127\.0\.0\.1:8001\/api\/product\/stock\/check\?.+$/',  // Stock API (127.0.0.1)
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->query('path');

        // Validate path exists
        if (!$path) {
            Log::warning('Next Product: Missing path parameter', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'error' => 'Missing path parameter',
                'message' => 'Path parameter is required for next product navigation'
            ], 403);
        }

        // Validate path format
        if (!$this->isValidPath($path)) {
            Log::warning('Next Product: Invalid path blocked', [
                'path' => $path,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'error' => 'Invalid redirect path',
                'message' => 'Only internal product pages and Stock API endpoints are allowed',
                'blocked_path' => $path,
                'allowed_patterns' => [
                    '/product?productId=X',
                    'http://localhost:8001/api/product/stock/check?...'
                ]
            ], 403);
        }

        // Additional check: Parse URL untuk validate domain
        $parsedUrl = parse_url($path);

        // Jika ada host (external URL)
        if (isset($parsedUrl['host'])) {
            $host = $parsedUrl['host'];
            $port = $parsedUrl['port'] ?? '';

            // Hanya allow localhost:8001 atau 127.0.0.1:8001
            $allowedHosts = ['localhost', '127.0.0.1', '::1'];
            $allowedPort = '8001';

            if (!in_array($host, $allowedHosts) || $port != $allowedPort) {
                Log::warning('Next Product: Unauthorized host/port', [
                    'path' => $path,
                    'host' => $host,
                    'port' => $port,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'error' => 'Unauthorized redirect host',
                    'message' => 'Only localhost:8001 is allowed for external redirects',
                    'blocked_host' => $host . ($port ? ":$port" : ''),
                    'allowed_host' => 'localhost:8001'
                ], 403);
            }
        }

        Log::info('Next Product: Valid path allowed', [
            'path' => $path,
            'ip' => $request->ip()
        ]);

        return $next($request);
    }

    /**
     * Validate path terhadap allowed patterns
     */
    private function isValidPath(string $path): bool
    {
        foreach ($this->allowedPatterns as $pattern) {
            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }
}
