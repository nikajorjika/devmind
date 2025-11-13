<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

if (! function_exists('workspace_url')) {
    /**
     * Build a full URL with the given subdomain based on app.url.
     *
     * @example workspace_url('acme') => https://acme.laravel.test
     */
    function workspace_url(string $subdomain): string
    {
        $base = Config::get('app.url');
        $parts = parse_url($base);

        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? 'localhost';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '';

        if (Str::startsWith($host, $subdomain.'.')) {
            return "{$scheme}://{$host}{$port}{$path}";
        }

        return "{$scheme}://{$subdomain}.{$host}{$port}{$path}";
    }
}

if (! function_exists('subdomain_from_url')) {
    /**
     * Extract subdomains from a URL.
     *
     *
     * @return array|string Returns array of subdomains (left-to-right) or '' when none
     */
    function subdomain_from_url(string $url, ?string $baseDomain = null): array|string
    {
        $url = strtolower(trim($url));

        if (! str_starts_with($url, 'https://') && ! str_starts_with($url, 'http://')) {
            $url = 'https://'.$url;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return '';
        }

        $host = strtolower(rtrim($host, '.'));

        if (
            $host === 'localhost' ||
            filter_var($host, FILTER_VALIDATE_IP) ||
            str_starts_with($host, '[') // IPv6 literal
        ) {
            return '';
        }

        $baseDomain ??= parse_url(Config::get('app.url'), PHP_URL_HOST);
        $baseDomain = strtolower(rtrim((string) $baseDomain, '.'));

        $hostParts = explode('.', $host);
        $baseParts = explode('.', $baseDomain);

        $basePartsCount = count($baseParts);

        if (! str_ends_with($host, $baseDomain)) {
            $basePartsCount = min(2, count($hostParts));
        }

        $subParts = array_slice($hostParts, 0, count($hostParts) - $basePartsCount);

        if (isset($subParts[0]) && $subParts[0] === 'www') {
            array_shift($subParts);
        }

        if (empty($subParts)) {
            return '';
        }

        return array_values($subParts);
    }
}
