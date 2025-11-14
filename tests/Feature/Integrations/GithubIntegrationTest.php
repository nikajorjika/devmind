<?php

use App\Http\Integrations\Github\Services\GithubAppJwtService;
use App\Http\Integrations\Github\Services\GithubInstallationTokenService;
use App\Models\User;
use App\Models\VersionControlIntegration;
use Illuminate\Support\Facades\Cache;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

beforeEach(function () {
    MockClient::destroyGlobal();

    $this->user = User::factory()->withWorkspace()->create();
    $this->workspace = $this->user->currentWorkspace;
    $this->workspace->makeCurrent();

    config(['services.github.app_name' => 'test-app']);

    $this->mock(GithubAppJwtService::class, function ($mock) {
        $mock->shouldReceive('generate')
            ->andReturn('fake-jwt-token');
    });
    $this->mock(GithubInstallationTokenService::class, function ($mock) {
        $mock->shouldReceive('getValidToken')
            ->andReturn('fake-access-token');
    });
});

it('redirects to github for authorization', function () {
    actingAs($this->user);

    $response = post(route('integrations.github.redirect'));

    $response->assertRedirect();
    $location = $response->headers->get('Location');
    expect($location)->toContain('https://github.com/apps/test-app/installations/new');
    expect($location)->toContain('state=');
});

it('stores state in cache when redirecting', function () {
    actingAs($this->user);

    post(route('integrations.github.redirect'));

    $cacheKey = "github_oauth_state_{$this->workspace->id}";
    expect(Cache::has($cacheKey))->toBeTrue();
});

it('handles callback with valid parameters', function () {
    actingAs($this->user);

    // Set up state
    $state = hash_hmac('sha256', $this->workspace->id.time(), config('app.key'));
    Cache::put("github_oauth_state_{$this->workspace->id}", $state, now()->addMinutes(10));

    // Mock GitHub API responses
    MockClient::global([
        MockResponse::make([
            'id' => 12345,
            'account' => [
                'login' => 'test-org',
                'type' => 'Organization',
                'id' => 67890,
            ],
        ], 200),
        MockResponse::make([
            'id' => 67890,
            'login' => 'test-org',
            'type' => 'Organization',
            'avatar_url' => 'https://example.com/avatar.png',
            'html_url' => 'https://github.com/test-org',
            'type' => 'Organization',
        ], 200),
    ]);

    $response = get(route('integrations.github.callback', [
        'installation_id' => 12345,
        'setup_action' => 'install',
        'state' => $state,
    ]));

    $response->assertRedirect(route('integrations.index'));
    $response->assertSessionHas('flash.status', 'success');

    // Verify integration was created
    $integration = VersionControlIntegration::where('workspace_id', $this->workspace->id)
        ->where('provider', 'github')
        ->first();

    expect($integration)->not->toBeNull();
    expect($integration->external_name)->toBe('test-org');
    expect($integration->installation_id)->toBe('12345');
});

it('handles callback with invalid state', function () {
    actingAs($this->user);

    $response = get(route('integrations.github.callback', [
        'installation_id' => 12345,
        'setup_action' => 'install',
        'state' => 'invalid-state',
    ]));

    $response->assertRedirect(route('integrations.index'));
    $response->assertSessionHas('flash.status', 'error');

    // Verify no integration was created
    $integration = VersionControlIntegration::where('workspace_id', $this->workspace->id)
        ->where('provider', 'github')
        ->first();

    expect($integration)->toBeNull();
});

it('handles callback with missing installation_id', function () {
    actingAs($this->user);

    $state = 'test-state';
    Cache::put("github_oauth_state_{$this->workspace->id}", $state, now()->addMinutes(10));

    $response = get(route('integrations.github.callback', [
        'state' => $state,
    ]));

    $response->assertRedirect(route('integrations.index'));
    $response->assertSessionHas('flash.status', 'error');
});

it('updates existing integration on reconnect', function () {
    actingAs($this->user);

    // Create existing integration
    $existingIntegration = VersionControlIntegration::create([
        'workspace_id' => $this->workspace->id,
        'provider' => 'github',
        'external_id' => 'old-id',
        'external_name' => 'old-org',
        'installation_id' => 'old-installation',
        'connected_at' => now()->subDays(7),
    ]);

    // Set up state
    $state = hash_hmac('sha256', $this->workspace->id.time(), config('app.key'));
    Cache::put("github_oauth_state_{$this->workspace->id}", $state, now()->addMinutes(10));

    // Mock GitHub API responses
    MockClient::global([
        MockResponse::make([
            'id' => 12345,
            'account' => [
                'login' => 'new-org',
                'type' => 'Organization',
                'id' => 67890,
            ],
        ], 200),
        MockResponse::make([
            'id' => 67890,
            'login' => 'new-org',
            'type' => 'Organization',
            'avatar_url' => 'https://example.com/new-avatar.png',
            'html_url' => 'https://github.com/new-org',
            'type' => 'Organization',
        ], 200),
    ]);

    $response = get(route('integrations.github.callback', [
        'installation_id' => 12345,
        'setup_action' => 'install',
        'state' => $state,
    ]));

    $response->assertRedirect(route('integrations.index'));

    // Verify integration was updated, not duplicated
    expect(VersionControlIntegration::where('workspace_id', $this->workspace->id)->count())->toBe(1);

    $integration = VersionControlIntegration::where('workspace_id', $this->workspace->id)->first();
    expect($integration->external_name)->toBe('new-org');
    expect($integration->installation_id)->toBe('12345');
});

it('requires authentication for redirect', function () {
    $response = post(route('integrations.github.redirect'));

    $response->assertRedirect(route('login'));
});

it('requires authentication for callback', function () {
    $response = get(route('integrations.github.callback'));

    $response->assertRedirect(route('login'));
});
