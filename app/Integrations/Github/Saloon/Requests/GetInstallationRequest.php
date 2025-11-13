<?php

namespace App\Integrations\Github\Saloon\Requests;

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
}
