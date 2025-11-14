<?php

namespace App\Http\Integrations\Contracts;

use App\Models\VersionControlIntegration;
use App\Models\Workspace;
use Illuminate\Http\Request;

interface VersionControlProvider
{
    /**
     * Get the internal provider name (e.g., 'github', 'gitlab').
     */
    public function getName(): string;

    /**
     * Get the human-readable display name (e.g., 'GitHub', 'GitLab').
     */
    public function getDisplayName(): string;

    /**
     * Get the provider key for identification (e.g., 'github').
     */
    public function getProviderKey(): string;

    /**
     * Generate the authorization redirect URL for the given workspace.
     */
    public function getAuthorizationRedirectUrl(Workspace $workspace): string;

    /**
     * Handle the OAuth callback and create/update the integration.
     */
    public function handleCallback(Request $request, Workspace $workspace): VersionControlIntegration;
}
