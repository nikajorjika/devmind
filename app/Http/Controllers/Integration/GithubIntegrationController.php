<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Integrations\VersionControlProviderResolver;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class GithubIntegrationController extends Controller
{
    public function __construct(
        protected VersionControlProviderResolver $providerResolver
    ) {
    }

    /**
     * Redirect to GitHub for authorization.
     */
    public function redirect(): Response
    {
        $workspace = Workspace::current();
        $provider = $this->providerResolver->resolve('github');

        $authUrl = $provider->getAuthorizationRedirectUrl($workspace);

        return Inertia::location($authUrl);
    }

    /**
     * Handle the GitHub callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        try {
            $workspace = Workspace::current();
            $provider = $this->providerResolver->resolve('github');

            $integration = $provider->handleCallback($request, $workspace);

            return redirect()
                ->route('integrations.index')
                ->with('toast', [
                    'type' => 'success',
                    'message' => "Successfully connected to {$integration->external_name}!",
                ]);
        } catch (\Exception $e) {
            return redirect()
                ->route('integrations.index')
                ->with('toast', [
                    'type' => 'error',
                    'message' => 'Failed to connect GitHub: '.$e->getMessage(),
                ]);
        }
    }
}
