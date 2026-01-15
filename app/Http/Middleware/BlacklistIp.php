<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlacklistIp
{
    /**
     * Whitelist IP yang SELALU diizinkan (bypass blacklist)
     * Khusus untuk Stock API di localhost:8001
     */
    private array $whitelist = [
        'localhost:8001',
        '127.0.0.1:8001',
        '[::1]:8001',  // IPv6 localhost
    ];

    /**
     * Blacklist IP local/private yang tidak boleh diakses
     *
     * Middleware ini akan memblokir request yang mencoba akses IP:
     * - 127.0.0.1 (localhost)
     * - 10.x.x.x (private network)
     * - 172.16.x.x - 172.31.x.x (private network)
     * - 192.168.x.x (private network)
     * - localhost
     * - ::1 (IPv6 localhost)
     *
     * KECUALI yang ada di whitelist (localhost:8001)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil stockApi dari request
        $stockApi = $request->input('stockApi');

        if ($stockApi) {
            // Parse URL untuk ambil host dan port
            $parsedUrl = parse_url($stockApi);
            $host = $parsedUrl['host'] ?? '';
            $port = $parsedUrl['port'] ?? '';

            // Cek whitelist DULU sebelum blacklist
            if ($this->isWhitelisted($host, $port)) {
                // IP di whitelist, izinkan langsung (skip blacklist check)
                return $next($request);
            }

            // Cek apakah host adalah IP/hostname yang di-blacklist
            if ($this->isBlacklistedHost($host)) {
                return response()->json([
                    'error' => 'Access to local/private IP addresses is blocked',
                    'blocked_host' => $host
                ], 403);
            }

            // Jika hostname (bukan IP), resolve ke IP dan cek
            if (!filter_var($host, FILTER_VALIDATE_IP)) {
                $resolvedIp = gethostbyname($host);

                if ($this->isPrivateOrLocalIp($resolvedIp)) {
                    return response()->json([
                        'error' => 'Access to local/private IP addresses is blocked',
                        'hostname' => $host,
                        'resolved_ip' => $resolvedIp
                    ], 403);
                }
            }
        }

        return $next($request);
    }

    /**
     * Cek apakah host:port ada di whitelist
     */
    private function isWhitelisted(string $host, string $port): bool
    {
        // Format host:port
        $hostWithPort = $port ? "$host:$port" : $host;

        // Normalize untuk case-insensitive
        $hostWithPort = strtolower($hostWithPort);
        $host = strtolower($host);

        foreach ($this->whitelist as $whitelistedEntry) {
            $whitelistedEntry = strtolower($whitelistedEntry);

            // Exact match dengan port
            if ($hostWithPort === $whitelistedEntry) {
                return true;
            }

            // Match host:port format
            if ($port && "$host:$port" === $whitelistedEntry) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah host ada di blacklist
     */
    private function isBlacklistedHost(string $host): bool
    {
        // Blacklist hostname
        $blacklistedHosts = [
            'localhost',
            '127.0.0.1',
            '0.0.0.0',
            '::1',
            '::ffff:127.0.0.1'
        ];

        // Cek exact match
        if (in_array(strtolower($host), $blacklistedHosts)) {
            return true;
        }

        // Jika IP, cek apakah private/local
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->isPrivateOrLocalIp($host);
        }

        return false;
    }

    /**
     * Cek apakah IP adalah private atau local
     */
    private function isPrivateOrLocalIp(string $ip): bool
    {
        // Validasi IP
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            return false;
        }

        // Cek jika bukan public IP (artinya private/reserved)
        if (!filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        )) {
            return true;
        }

        // Additional checks untuk IP ranges yang berbahaya
        $ipLong = ip2long($ip);

        if ($ipLong === false) {
            return false; // IPv6 atau invalid
        }

        // Blacklist IP ranges (IPv4)
        $blacklistRanges = [
            ['10.0.0.0', '10.255.255.255'],         // Private network
            ['172.16.0.0', '172.31.255.255'],       // Private network
            ['192.168.0.0', '192.168.255.255'],     // Private network
            ['127.0.0.0', '127.255.255.255'],       // Loopback
            ['169.254.0.0', '169.254.255.255'],     // Link-local
            ['0.0.0.0', '0.255.255.255'],           // Current network
        ];

        foreach ($blacklistRanges as [$start, $end]) {
            $startLong = ip2long($start);
            $endLong = ip2long($end);

            if ($ipLong >= $startLong && $ipLong <= $endLong) {
                return true;
            }
        }

        return false;
    }
}
