<?php

namespace App\Http\Integrations\Github\Auth;

use App\Http\Integrations\Github\Services\GithubInstallationTokenService;
use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class TokenAuth implements Authenticator
{
    public function __construct(
        protected string $installationId,
        protected ?GithubInstallationTokenService $tokenService = null,
    ) {
        // allow DI override in tests, but default to container
        $this->tokenService ??= app(GithubInstallationTokenService::class);
    }

    public function set(PendingRequest $pendingRequest): void
    {
        // 1. Get a valid (cached or fresh) token
        $token = $this->tokenService->getValidToken($this->installationId);

        // 2. Attach it to the request
        $pendingRequest->headers()->add('Authorization', "token {$token}");
    }
}
