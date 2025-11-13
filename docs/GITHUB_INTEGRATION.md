# GitHub Integration Setup Guide

## Overview

This feature allows DevMind organizations to connect to GitHub organizations via a GitHub App integration. The implementation uses Laravel Saloon for HTTP communication and follows a provider abstraction pattern for easy extensibility to other VCS providers.

## Environment Configuration

Add the following environment variables to your `.env` file:

```env
GITHUB_APP_NAME=your-github-app-name
GITHUB_APP_ID=your-app-id
GITHUB_CLIENT_ID=your-client-id
GITHUB_CLIENT_SECRET=your-client-secret
GITHUB_PRIVATE_KEY=your-private-key
GITHUB_WEBHOOK_SECRET=your-webhook-secret
```

## Database Migration

Run the migration to create the `version_control_integrations` table:

```bash
php artisan migrate
```

## Usage

### For End Users

1. Navigate to the Integrations page within your workspace
2. Click the "Connect GitHub" button on the GitHub card
3. You'll be redirected to GitHub to authorize the DevMind app
4. Select the GitHub organization you want to connect
5. After authorization, you'll be redirected back to DevMind
6. The integration status will show as "Connected" with the organization details

### For Developers

#### Adding a New VCS Provider (e.g., GitLab)

1. Create a new provider class implementing `VersionControlProvider`:

```php
namespace App\Integrations\Gitlab;

use App\Integrations\Contracts\VersionControlProvider;

class GitlabProvider implements VersionControlProvider
{
    // Implement interface methods
}
```

2. Create Saloon connector and requests:

```php
namespace App\Integrations\Gitlab\Saloon;

use Saloon\Http\Connector;

class GitlabConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://gitlab.com/api/v4';
    }
}
```

3. Register the provider in `VersionControlProviderResolver`:

```php
protected array $providers = [
    'github' => GithubProvider::class,
    'gitlab' => GitlabProvider::class, // Add new provider
];
```

4. Create routes and controller for the new provider following the GitHub pattern

## Architecture

### Provider Abstraction

- **Interface**: `App\Integrations\Contracts\VersionControlProvider`
- **Resolver**: `App\Integrations\VersionControlProviderResolver`
- **Implementations**: 
  - `App\Integrations\Github\GithubProvider`
  - Future: GitLab, Bitbucket, etc.

### HTTP Layer

All API calls use Laravel Saloon:

- **Connector**: `App\Integrations\Github\Saloon\GithubConnector`
- **Requests**: 
  - `GetInstallationRequest`
  - `GetOrganizationRequest`

### Data Model

The `VersionControlIntegration` model stores:

- `workspace_id` - Foreign key to workspace
- `provider` - Provider type (github, gitlab, etc.)
- `external_id` - Provider's organization ID
- `external_name` - Organization name/login
- `installation_id` - GitHub App installation ID
- `avatar_url` - Organization avatar
- `meta` - JSON field for provider-specific data
- `connected_at` - Connection timestamp
- `disconnected_at` - Disconnection timestamp (null if active)

### Routes

Tenant-scoped routes:

- `GET /integrations` - Show integrations page
- `POST /integrations/github/redirect` - Start GitHub OAuth flow
- `GET /integrations/github/callback` - Handle GitHub OAuth callback

### Frontend

Vue component: `resources/js/pages/integrations/Index.vue`

Uses shadcn-vue components:
- Card, CardHeader, CardTitle, CardDescription, CardContent
- Button
- Badge
- Avatar

## Testing

Run the test suite:

```bash
php artisan pest

# With coverage
php artisan pest --coverage
```

Test organization:

- **Unit Tests**:
  - `tests/Unit/Integrations/VersionControlProviderResolverTest.php`
  - `tests/Unit/Integrations/GithubProviderTest.php`
  - `tests/Unit/Integrations/GithubConnectorTest.php`
  - `tests/Unit/Models/VersionControlIntegrationTest.php`

- **Feature Tests**:
  - `tests/Feature/Integrations/IntegrationsPageTest.php`
  - `tests/Feature/Integrations/GithubIntegrationTest.php`

The test suite uses Saloon's mock client to avoid real HTTP calls during testing.

## Security

- OAuth state parameter validated via cache
- CSRF protection via Laravel's middleware
- GitHub App authentication (not implemented: JWT token generation)
- Scoped to workspace/tenant via multitenancy

## TODO / Future Improvements

- [ ] Implement disconnect/revoke functionality
- [ ] Add re-authorization flow
- [ ] Generate JWT tokens for GitHub App authentication
- [ ] Add webhook handling for GitHub events
- [ ] Implement GitLab and Bitbucket providers
- [ ] Add permission checks (MANAGE_INTEGRATIONS capability)
- [ ] Add UI for multiple organizations per workspace
