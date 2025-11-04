<?php

describe('Helpers functions', function () {
    beforeEach(function () {
        Config::set('app.url', 'https://laravel.test');
    });

    it('can generate workspace URLs', function () {
        $url = workspace_url('acme');
        expect($url)->toBe('https://acme.laravel.test');

        $urlWithPort = workspace_url('beta');
        expect($urlWithPort)->toBe('https://beta.laravel.test');
    });
});


describe('subdomain_from_url', function () {

    it('extracts a subdomain against non-normalized url', function () {
        expect(subdomain_from_url('acme.laravel.test'))
            ->toBe(['acme']);
    });

    it('extracts a single subdomain against default app.url', function () {
        expect(subdomain_from_url('https://acme.laravel.test'))
            ->toBe(['acme']);
    });

    it('strips leading www when present', function () {
        expect(subdomain_from_url('https://www.beta.laravel.test'))
            ->toBe(['beta']);
    });

    it('returns empty string when no subdomain (default base)', function () {
        expect(subdomain_from_url('https://laravel.test'))
            ->toBe('');
    });

    it('supports custom base domain (simple TLD)', function () {
        expect(subdomain_from_url('https://app.example.com', 'example.com'))
            ->toBe(['app']);
    });

    it('supports multiple subdomains (left-to-right order)', function () {
        // Expected order based on your earlier example: test.api.laravel.test -> ['test','api']
        expect(subdomain_from_url('https://test.api.laravel.test'))
            ->toBe(['test', 'api']);
    });

    it('handles multi-label public suffix style bases when provided explicitly', function () {
        // When base is explicitly provided as example.co.uk
        expect(subdomain_from_url('https://app.eu.example.co.uk', 'example.co.uk'))
            ->toBe(['app', 'eu']);
    });

    it('is case-insensitive for host and www stripping', function () {
        expect(subdomain_from_url('HTTPS://WWW.ACME.LARAVEL.TEST'))
            ->toBe(['acme']);
    });

    it('ignores ports, paths, and query strings', function () {
        expect(subdomain_from_url('https://alpha.laravel.test:8443/foo/bar?x=1#y'))
            ->toBe(['alpha']);
    });

    it('handles trailing dot on FQDNs', function () {
        expect(subdomain_from_url('https://alpha.laravel.test.'))
            ->toBe(['alpha']);
    });

    it('returns empty string for IP addresses', function () {
        expect(subdomain_from_url('http://127.0.0.1'))
            ->toBe('');
        expect(subdomain_from_url('http://[::1]'))
            ->toBe('');
    });

    it('returns empty string for localhost', function () {
        expect(subdomain_from_url('http://localhost'))
            ->toBe('');
        expect(subdomain_from_url('http://localhost:3000'))
            ->toBe('');
    });

    it('handles credentials in URL', function () {
        expect(subdomain_from_url('https://user:pass@alpha.laravel.test/path'))
            ->toBe(['alpha']);
    });

    it('respects a custom base even if different from app.url', function () {
        // app.url is laravel.test, but we pass example.com explicitly
        expect(subdomain_from_url('https://staging.api.example.com', 'example.com'))
            ->toBe(['staging', 'api']);
    });

    it('treats leading www as ignorable only when it is the first label', function () {
        // If "www" appears NOT as the first label, we keep it
        expect(subdomain_from_url('https://edge.www.laravel.test'))
            ->toBe(['edge', 'www']);
    });

    it('returns empty string when url host equals provided custom base', function () {
        expect(subdomain_from_url('https://example.com', 'example.com'))
            ->toBe('')
            ->and(subdomain_from_url('https://example.co.uk', 'example.co.uk'))
            ->toBe('');
    });

    it('falls back to default base when custom is null and still works', function () {
        expect(subdomain_from_url('https://billing.eu.laravel.test', null))
            ->toBe(['billing', 'eu']);
    });

    it('handles unknown/invalid urls gracefully (no host => empty string)', function () {
        // Depending on your implementation, you might return '' or throw.
        // This asserts the tolerant behavior.
        expect(subdomain_from_url('not-a-url'))
            ->toBe('');
    });

    it('handles unicode/punycode hosts transparently', function () {
        // If implementation normalizes via idn_to_ascii, this should still return the left label(s)
        // Here we just assert we still get the subdomain label back in its ascii/punycode form.
        expect(subdomain_from_url('https://bücher.laravel.test'))
            ->toBe(['bücher']); // Accept either preserved unicode or punycode based on your helper.
    })->skip(fn() => !function_exists('idn_to_ascii'), 'Skip if IDN not supported in environment');

    //
    // Data-driven sweep for a bunch of typical cases
    //
    dataset('urls', [
        // [url, baseDomain, expected]
        ['acme.laravel.test', null, ['acme']],
        ['https://acme.laravel.test', null, ['acme']],
        ['https://www.alpha.laravel.test', null, ['alpha']],
        ['https://laravel.test', null, ''],
        ['https://api.example.com', 'example.com', ['api']],
        ['https://a.b.c.example.com', 'example.com', ['a', 'b', 'c']],
        ['https://a.b.c.example.co.uk', 'example.co.uk', ['a', 'b', 'c']],
        ['http://localhost:8080', null, ''],
        ['http://127.0.0.1:3000', null, ''],
        ['https://edge.www.laravel.test', null, ['edge', 'www']],
        ['https://WWW.BETA.LARAVEL.TEST', null, ['beta']],
        ['https://app.example.com:443/x?y=1', 'example.com', ['app']],
        ['https://example.com', 'example.com', ''],
    ]);

    it('passes the sweep of typical cases', function (string $url, ?string $base, $expected) {
        expect(subdomain_from_url($url, $base))->toBe($expected);
    })->with('urls');
});
