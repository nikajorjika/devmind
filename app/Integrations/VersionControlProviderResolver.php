<?php

namespace App\Integrations;

use App\Integrations\Contracts\VersionControlProvider;
use App\Integrations\Github\GithubProvider;
use Illuminate\Support\Facades\App;

class VersionControlProviderResolver
{
    /**
     * Registry of available providers.
     *
     * @var array<string, string>
     */
    protected array $providers = [
        'github' => GithubProvider::class,
        // Add more providers here as they're implemented
        // 'gitlab' => GitlabProvider::class,
        // 'bitbucket' => BitbucketProvider::class,
    ];

    /**
     * Resolve a provider instance by its key.
     */
    public function resolve(string $providerKey): VersionControlProvider
    {
        if (! isset($this->providers[$providerKey])) {
            throw new \InvalidArgumentException("Unknown version control provider: {$providerKey}");
        }

        $providerClass = $this->providers[$providerKey];

        return App::make($providerClass);
    }

    /**
     * Get all available provider keys.
     *
     * @return array<string>
     */
    public function getAvailableProviders(): array
    {
        return array_keys($this->providers);
    }

    /**
     * Check if a provider is supported.
     */
    public function supports(string $providerKey): bool
    {
        return isset($this->providers[$providerKey]);
    }
}
