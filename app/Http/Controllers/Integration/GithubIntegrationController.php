<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Http\Integrations\VersionControlProviderResolver;
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
        $workspace = Workspace::current();
        $provider = $this->providerResolver->resolve('github');
        try {
            $provider->handleCallback($request, $workspace);
            session()->flash('flash', [
                'status' => 'success',
                'title' => 'GitHub integration successful',
                'description' => 'The GitHub integration has been successfully configured.',
            ]);
        } catch (\Exception $e) {
            session()->flash('flash', [
                'status' => 'error',
                'title' => 'GitHub integration failed: '.$e->getMessage(),
                'description' => 'This installation request was not correctly configured, try again or contact support.',
            ]);
        }


        return to_route('integrations.index');

    }
}
