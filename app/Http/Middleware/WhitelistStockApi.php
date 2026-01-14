<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class WhitelistStockApi
{
    /**
     * Whitelist: Hanya Stock API yang diizinkan untuk Check Stock
     *
     * HANYA localhost:8001 yang boleh diakses
     * Semua domain/IP lain akan di-BLOCK
     */
    private array $allowedHosts = [
        'localhost:8001',
        '127.0.0.1:8001',
        '[::1]:8001',  // IPv6 localhost
    ];

    /**
     * Validate stockApi parameter untuk Check Stock
     *
     * Aturan:
     * - HANYA localhost:8001 yang diizinkan
     * - Semua host lain (external domain, public IP, private IP) → BLOCK
     */
    public function handle(Request $request, Closure $next): Response
    {
        $stockApi = $request->input('stockApi');

        if (!$stockApi) {
            return response()->json([
                'error' => 'Missing stockApi parameter',
                'message' => 'stockApi parameter is required for stock check'
            ], 400);
        }

        // Parse URL untuk ambil host dan port
        $parsedUrl = parse_url($stockApi);

        if (!isset($parsedUrl['host'])) {
            Log::warning('Stock API Whitelist: Invalid URL format', [
                'stockApi' => $stockApi,
                'client_ip' => $request->ip()
            ]);

            return response()->json([
                'error' => 'Invalid stockApi URL format',
                'message' => 'URL must include host'
            ], 400);
        }

        $host = $parsedUrl['host'];
        $port = $parsedUrl['port'] ?? '';
        $scheme = $parsedUrl['scheme'] ?? 'http';

        // Cek apakah host:port ada di whitelist
        if ($this->isAllowedHost($host, $port)) {
            Log::info('Stock API Whitelist: Access granted', [
                'host' => $port ? "$host:$port" : $host,
                'stockApi' => $stockApi
            ]);

            return $next($request);
        }

        // Block semua host yang tidak di whitelist
        Log::warning('Stock API Whitelist: Access denied', [
            'host' => $host,
            'port' => $port,
            'stockApi' => $stockApi,
            'client_ip' => $request->ip(),
            'allowed_hosts' => $this->allowedHosts
        ]);

        return response()->json([
            'error' => 'Access denied',
            // 'message' => 'Only Stock API (localhost:8001) is allowed for stock check',
            // 'requested_host' => $port ? "$host:$port" : $host,
            // 'allowed_hosts' => $this->allowedHosts
        ], 403);
    }

    /**
     * Cek apakah host:port ada di whitelist
     */
    private function isAllowedHost(string $host, string $port): bool
    {
        // Format host:port
        $hostWithPort = $port ? "$host:$port" : $host;

        // Normalize untuk case-insensitive matching
        $host = strtolower($host);
        $hostWithPort = strtolower($hostWithPort);

        foreach ($this->allowedHosts as $allowedEntry) {
            $allowedEntry = strtolower($allowedEntry);

            // Exact match dengan port
            if ($hostWithPort === $allowedEntry) {
                return true;
            }

            // Match host:port format
            if ($port && "$host:$port" === $allowedEntry) {
                return true;
            }

            // Match tanpa port (jika allowed entry tidak specify port)
            if (!str_contains($allowedEntry, ':') && $host === $allowedEntry) {
                return true;
            }
        }

        return false;
    }
}
