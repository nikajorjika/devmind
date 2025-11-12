<?php

namespace App\Actions\Fortify;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\RegisterResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectAfterRegister implements RegisterResponse
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
        $url = session()->pull('url.intended') ?? '/dashboard';

        return Inertia::location($url);
    }
}
