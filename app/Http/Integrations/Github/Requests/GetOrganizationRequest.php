<?php

namespace App\Http\Integrations\Github\Requests;

use App\Http\Integrations\Github\Auth\TokenAuth;
use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetOrganizationRequest extends Request
{
    /**
     * The HTTP method of the request.
     */
    protected Method $method = Method::GET;

    /**
     * Create a new request instance.
     */
    public function __construct(
        protected string $installationId,
        protected string $organizationLogin,
    ) {
    }

    /**
     * The endpoint for the request.
     */
    public function resolveEndpoint(): string
    {
        return "/orgs/{$this->organizationLogin}";
    }

    public function defaultAuth(): ?Authenticator
    {
        return new TokenAuth($this->installationId);
    }
}
