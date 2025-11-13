<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Integrations\VersionControlProviderResolver;
use App\Models\VersionControlIntegration;
use App\Models\Workspace;
use Inertia\Inertia;
use Inertia\Response;

class IntegrationsController extends Controller
{
    public function __construct(
        protected VersionControlProviderResolver $providerResolver
    ) {
    }

    /**
     * Display the integrations page.
     */
    public function index(): Response
    {
        $workspace = Workspace::current();

        // Get all active integrations for the workspace
        $integrations = VersionControlIntegration::where('workspace_id', $workspace->id)
            ->active()
            ->get()
            ->keyBy('provider')
            ->toArray();

        // Get available providers
        $availableProviders = collect($this->providerResolver->getAvailableProviders())
            ->map(function ($providerKey) {
                $provider = $this->providerResolver->resolve($providerKey);

                return [
                    'key' => $provider->getProviderKey(),
                    'name' => $provider->getDisplayName(),
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('integrations/Index', [
            'integrations' => $integrations,
            'availableProviders' => $availableProviders,
        ]);
    }
}
