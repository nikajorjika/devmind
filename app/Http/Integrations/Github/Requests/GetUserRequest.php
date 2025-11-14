<?php

namespace App\Http\Integrations\Github\Requests;

use App\Http\Integrations\Github\Auth\TokenAuth;
use Saloon\Contracts\Authenticator;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetUserRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $installationId,
        protected string $username,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/users/{$this->username}";
    }

    public function defaultAuth(): ?Authenticator
    {
        return new TokenAuth($this->installationId);
    }
}
