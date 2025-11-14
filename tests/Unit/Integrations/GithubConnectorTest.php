<?php

use App\Http\Integrations\Github\GithubConnector;

it('has correct base URL', function () {
    $connector = new GithubConnector();

    expect($connector->resolveBaseUrl())->toBe('https://api.github.com');
});

it('includes default headers', function () {
    $connector = new GithubConnector();

    $headers = $connector->headers()->all();

    expect($headers)->toHaveKey('Accept');
    expect($headers['Accept'])->toContain('application/vnd.github.v3+json');
    expect($headers)->toHaveKey('User-Agent');
});
