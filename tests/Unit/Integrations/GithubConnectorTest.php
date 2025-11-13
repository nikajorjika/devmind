<?php

use App\Integrations\Github\Saloon\GithubConnector;

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

it('can set bearer token', function () {
    $connector = new GithubConnector();
    
    $connector->withToken('test-token');
    
    $headers = $connector->headers()->all();
    expect($headers)->toHaveKey('Authorization');
    expect($headers['Authorization'])->toBe('Bearer test-token');
});
