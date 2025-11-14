<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Integrations\VersionControlProviderResolver;
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
            ->map(fn($integration) => $integration->toArray());

        // Get available providers
        $availableProviders = collect($this->providerResolver->getAvailableProviders())
            ->map(function ($providerKey) {
                $provider = $this->providerResolver->resolve($providerKey);

                return [
                    'key' => $provider->getProviderKey(),
                    'name' => $provider->getDisplayName(),
                ];
            })
            ->values();

        return Inertia::render('integrations/Index', [
            'integrations' => $integrations,
            'availableProviders' => $availableProviders,
        ]);
    }
}
