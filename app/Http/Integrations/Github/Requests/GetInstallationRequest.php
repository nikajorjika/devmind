<?php

namespace App\Http\Integrations\Github\Requests;

use App\Http\Integrations\Github\Auth\AppInstallationAccessTokenAuthenticator;
use App\Http\Integrations\Github\Auth\JWTAuth;
use App\Http\Integrations\Github\Services\GithubAppJwtService;
use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetInstallationRequest extends Request
{
    /**
     * The HTTP method of the request.
     */
    protected Method $method = Method::GET;

    /**
     * Create a new request instance.
     */
    public function __construct(protected string $installationId)
    {
    }

    /**
     * The endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return "/app/installations/{$this->installationId}";
    }

    public function defaultAuth(): ?Authenticator
    {
        return new JWTAuth(app(GithubAppJwtService::class)->generate());
    }
}
