<?php

namespace App\Tenant\Tasks;

use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class UpdateUserCurrentTenant implements SwitchTenantTask
{

    public function makeCurrent(IsTenant $tenant): void
    {
        // TODO: Implement makeCurrent() method.
    }

    public function forgetCurrent(): void
    {
        // TODO: Implement forgetCurrent() method.
    }
}
