<?php

namespace App\Http\Integrations\Github\Requests;

use App\Http\Integrations\Github\Auth\JWTAuth;
use App\Http\Integrations\Github\Services\GithubAppJwtService;
use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class CreateInstallationAccessTokenRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        protected string $installationId
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/app/installations/{$this->installationId}/access_tokens";
    }

    public function defaultAuth(): ?Authenticator
    {
        return new JWTAuth(app(GithubAppJwtService::class)->generate());
    }
}
