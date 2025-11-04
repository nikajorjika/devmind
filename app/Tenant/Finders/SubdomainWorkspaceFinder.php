<?php

namespace App\Tenant\Finders;

use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\TenantFinder\TenantFinder;

class SubdomainWorkspaceFinder extends TenantFinder
{
    public function findForRequest($request): ?Tenant
    {
        $host = $request->getHost();
        $subdomains = subdomain_from_url($host);

        if (empty($subdomains)) {
            return null;
        }

        return app(IsTenant::class)::whereSubdomain($subdomains[0])->first();
    }
}
