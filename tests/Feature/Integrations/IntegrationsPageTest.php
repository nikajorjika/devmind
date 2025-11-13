<?php

use App\Models\User;
use App\Models\Workspace;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
    $this->workspace->makeCurrent();
});

it('renders integrations page for authenticated user', function () {
    actingAs($this->user);

    $response = get(route('integrations.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('integrations/Index')
        ->has('integrations')
        ->has('availableProviders')
    );
});

it('shows github as available provider', function () {
    actingAs($this->user);

    $response = get(route('integrations.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('availableProviders.0.key', 'github')
        ->where('availableProviders.0.name', 'GitHub')
    );
});

it('shows no integrations when none are connected', function () {
    actingAs($this->user);

    $response = get(route('integrations.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('integrations', [])
    );
});

it('shows connected github integration', function () {
    actingAs($this->user);

    $integration = \App\Models\VersionControlIntegration::create([
        'workspace_id' => $this->workspace->id,
        'provider' => 'github',
        'external_id' => '12345',
        'external_name' => 'test-org',
        'installation_id' => '67890',
        'avatar_url' => 'https://example.com/avatar.png',
        'connected_at' => now(),
    ]);

    $response = get(route('integrations.index'));

    $response->assertInertia(fn ($page) => $page
        ->where('integrations.github.external_name', 'test-org')
        ->where('integrations.github.provider', 'github')
    );
});

it('requires authentication', function () {
    $response = get(route('integrations.index'));

    $response->assertRedirect(route('login'));
});
