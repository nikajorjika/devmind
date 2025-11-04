<?php

namespace App\Actions\Fortify;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Laravel\Fortify\Contracts\LoginResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectAfterLogin implements LoginResponse
{
    /**
     * Redirect User after login
     *
     * @param $request
     *
     * @return Redirector|Response|RedirectResponse
     */
    public function toResponse($request): Redirector|Response|RedirectResponse
    {
        return redirect('/dashboard');
    }
}
