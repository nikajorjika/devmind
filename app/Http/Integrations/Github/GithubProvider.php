<?php

namespace App\Http\Integrations\Github;

use App\Http\Integrations\Contracts\VersionControlProvider;
use App\Http\Integrations\Github\Requests\GetInstallationRequest;
use App\Http\Integrations\Github\Requests\GetOrganizationRequest;
use App\Http\Integrations\Github\Requests\GetUserRequest;
use App\Models\VersionControlIntegration;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GithubProvider implements VersionControlProvider
{
    public function __construct(
        protected GithubConnector $connector
    ) {
    }

    public function getName(): string
    {
        return 'github';
    }

    public function getDisplayName(): string
    {
        return 'GitHub';
    }

    public function getProviderKey(): string
    {
        return 'github';
    }

    public function getAuthorizationRedirectUrl(Workspace $workspace): string
    {
        $appName = config('services.github.app_name');
        $state = $this->generateState($workspace);

        Cache::put("github_oauth_state_{$workspace->id}", $state, now()->addMinutes(10));

        return "https://github.com/apps/{$appName}/installations/new?state={$state}";
    }

    /**
     * @throws \Exception
     */
    public function handleCallback(Request $request, Workspace $workspace): VersionControlIntegration
    {

        // Validate state
        $state = $request->query('state');
        $cachedState = Cache::pull("github_oauth_state_{$workspace->id}");

        if ($state !== $cachedState) {
            throw new \Exception('Invalid state parameter');
        }

        // Get installation details from callback
        $installationId = $request->query('installation_id');
        $setupAction = $request->query('setup_action');

        if (!$installationId) {
            throw new \Exception('Missing installation_id parameter');
        }

        $installationData = $this->fetchInstallationDetails($installationId);

        $account = $installationData['account'] ?? null;
        $login = $account['login'] ?? null;
        $type = $account['type'] ?? null;

        if ($type !== 'Organization') {
            throw new \Exception('GitHub integration must be installed on an organization account');
        }

        $organizationData = $this->fetchOrganizationDetails($installationId, $login);

        // Create or update the integration
        return VersionControlIntegration::updateOrCreate(
            [
                'workspace_id' => $workspace->id,
                'provider' => $this->getProviderKey(),
            ],
            [
                'external_id' => (string) $organizationData['id'],
                'external_name' => $organizationData['login'],
                'installation_id' => (string) $installationId,
                'avatar_url' => $organizationData['avatar_url'] ?? null,
                'meta' => [
                    'organization_url' => $organizationData['html_url'] ?? null,
                    'organization_type' => $organizationData['type'] ?? null,
                    'setup_action' => $setupAction,
                ],
                'connected_at' => now(),
                'disconnected_at' => null,
            ]
        );
    }

    /**
     * Generate a secure state token for OAuth.
     */
    protected function generateState(Workspace $workspace): string
    {
        return hash_hmac('sha256', $workspace->id.time(), config('app.key'));
    }

    /**
     * Fetch installation details from GitHub API.
     */
    protected function fetchInstallationDetails(string $installationId): array
    {
        $response = $this->connector
            ->send(new GetInstallationRequest($installationId));

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch installation details from GitHub');
        }

        return $response->json();
    }

    /**
     * Fetch organization details from GitHub API.
     */
    protected function fetchOrganizationDetails(string $installationId, string $organizationLogin): array
    {
        $response = $this->connector
            ->send(new GetOrganizationRequest($installationId, $organizationLogin));

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch organization details from GitHub');
        }

        return $response->json();
    }
}
