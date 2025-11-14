<?php

namespace App\Http\Integrations\Github;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;

class GithubConnector extends Connector
{
    use AcceptsJson;

    /**
     * The Base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.github.com';
    }

    /**
     * Default headers for every request.
     */
    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'DevMind-App',
        ];
    }
}
