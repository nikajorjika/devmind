<?php

namespace App\Http\Integrations\Github\Auth;

use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class JWTAuth implements Authenticator
{
    public function __construct(
        protected string $jwt,
    ) {
    }

    /**
     * Apply the authentication to the request.
     */
    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', "Bearer {$this->jwt}");
    }
}
