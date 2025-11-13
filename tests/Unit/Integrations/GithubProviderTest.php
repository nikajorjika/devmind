<?php

use App\Integrations\Github\GithubProvider;
use App\Integrations\Github\Saloon\GithubConnector;
use App\Models\Workspace;

it('constructs authorization URL correctly', function () {
    config(['services.github.app_name' => 'test-app']);

    $connector = new GithubConnector();
    $provider = new GithubProvider($connector);
    $workspace = Workspace::factory()->create();

    $url = $provider->getAuthorizationRedirectUrl($workspace);

    expect($url)->toContain('https://github.com/apps/test-app/installations/new');
    expect($url)->toContain('state=');
});

it('returns correct provider information', function () {
    $connector = new GithubConnector();
    $provider = new GithubProvider($connector);

    expect($provider->getName())->toBe('github');
    expect($provider->getDisplayName())->toBe('GitHub');
    expect($provider->getProviderKey())->toBe('github');
});
