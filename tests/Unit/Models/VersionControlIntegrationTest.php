<?php

use App\Models\VersionControlIntegration;
use App\Models\Workspace;

it('creates version control integration', function () {
    $workspace = Workspace::factory()->create();
    
    $integration = VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'github',
        'external_id' => '12345',
        'external_name' => 'test-org',
        'installation_id' => '67890',
        'connected_at' => now(),
    ]);
    
    expect($integration)->not->toBeNull();
    expect($integration->provider)->toBe('github');
    expect($integration->external_name)->toBe('test-org');
});

it('belongs to workspace', function () {
    $workspace = Workspace::factory()->create();
    
    $integration = VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'github',
        'external_id' => '12345',
        'external_name' => 'test-org',
        'installation_id' => '67890',
        'connected_at' => now(),
    ]);
    
    expect($integration->workspace->id)->toBe($workspace->id);
});

it('checks if integration is active', function () {
    $workspace = Workspace::factory()->create();
    
    $activeIntegration = VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'github',
        'external_id' => '12345',
        'external_name' => 'test-org',
        'installation_id' => '67890',
        'connected_at' => now(),
        'disconnected_at' => null,
    ]);
    
    $disconnectedIntegration = VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'gitlab',
        'external_id' => '54321',
        'external_name' => 'test-org-2',
        'installation_id' => '09876',
        'connected_at' => now()->subDays(7),
        'disconnected_at' => now(),
    ]);
    
    expect($activeIntegration->isActive())->toBeTrue();
    expect($disconnectedIntegration->isActive())->toBeFalse();
});

it('scopes to active integrations', function () {
    $workspace = Workspace::factory()->create();
    
    VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'github',
        'external_id' => '12345',
        'external_name' => 'active-org',
        'installation_id' => '67890',
        'connected_at' => now(),
        'disconnected_at' => null,
    ]);
    
    VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'gitlab',
        'external_id' => '54321',
        'external_name' => 'disconnected-org',
        'installation_id' => '09876',
        'connected_at' => now()->subDays(7),
        'disconnected_at' => now(),
    ]);
    
    $activeIntegrations = VersionControlIntegration::active()->get();
    
    expect($activeIntegrations)->toHaveCount(1);
    expect($activeIntegrations->first()->external_name)->toBe('active-org');
});

it('scopes to specific provider', function () {
    $workspace = Workspace::factory()->create();
    
    VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'github',
        'external_id' => '12345',
        'external_name' => 'github-org',
        'installation_id' => '67890',
        'connected_at' => now(),
    ]);
    
    VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'gitlab',
        'external_id' => '54321',
        'external_name' => 'gitlab-org',
        'installation_id' => '09876',
        'connected_at' => now(),
    ]);
    
    $githubIntegrations = VersionControlIntegration::forProvider('github')->get();
    
    expect($githubIntegrations)->toHaveCount(1);
    expect($githubIntegrations->first()->provider)->toBe('github');
});

it('casts meta to array', function () {
    $workspace = Workspace::factory()->create();
    
    $integration = VersionControlIntegration::create([
        'workspace_id' => $workspace->id,
        'provider' => 'github',
        'external_id' => '12345',
        'external_name' => 'test-org',
        'installation_id' => '67890',
        'connected_at' => now(),
        'meta' => ['key' => 'value', 'nested' => ['data' => 'test']],
    ]);
    
    expect($integration->meta)->toBeArray();
    expect($integration->meta['key'])->toBe('value');
    expect($integration->meta['nested']['data'])->toBe('test');
});
