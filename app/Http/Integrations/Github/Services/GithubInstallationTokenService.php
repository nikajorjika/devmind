<?php

namespace App\Http\Integrations\Github\Services;

use App\Http\Integrations\Github\GithubConnector;
use App\Http\Integrations\Github\Requests\CreateInstallationAccessTokenRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class GithubInstallationTokenService
{
    public function __construct(
        protected GithubConnector $connector,
    ) {
    }

    public function getValidToken(string $installationId): string
    {
        $cacheKey = "github_installation_token_{$installationId}";

        $cached = Cache::get($cacheKey);

        if ($cached && Carbon::parse($cached['expires_at'])->isFuture()) {
            return $cached['token'];
        }

        // Call GitHub to create a new installation access token
        $response = $this->connector->send(
            new CreateInstallationAccessTokenRequest($installationId)
        );

        if (!$response->successful()) {
            throw new \RuntimeException('Failed to create installation access token from GitHub');
        }

        $token = $response->json('token');
        $expiresAt = $response->json('expires_at');

        Cache::put($cacheKey, [
            'token' => $token,
            'expires_at' => $expiresAt,
        ], Carbon::parse($expiresAt));

        return $token;
    }
}
