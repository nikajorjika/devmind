<?php

use App\Integrations\VersionControlProviderResolver;
use App\Integrations\Github\GithubProvider;

it('resolves github provider correctly', function () {
    $resolver = new VersionControlProviderResolver();
    
    $provider = $resolver->resolve('github');
    
    expect($provider)->toBeInstanceOf(GithubProvider::class);
    expect($provider->getProviderKey())->toBe('github');
    expect($provider->getName())->toBe('github');
    expect($provider->getDisplayName())->toBe('GitHub');
});

it('throws exception for unknown provider', function () {
    $resolver = new VersionControlProviderResolver();
    
    $resolver->resolve('unknown-provider');
})->throws(\InvalidArgumentException::class, 'Unknown version control provider: unknown-provider');

it('returns available providers', function () {
    $resolver = new VersionControlProviderResolver();
    
    $providers = $resolver->getAvailableProviders();
    
    expect($providers)->toBeArray();
    expect($providers)->toContain('github');
});

it('checks if provider is supported', function () {
    $resolver = new VersionControlProviderResolver();
    
    expect($resolver->supports('github'))->toBeTrue();
    expect($resolver->supports('gitlab'))->toBeFalse();
    expect($resolver->supports('unknown'))->toBeFalse();
});
