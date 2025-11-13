<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinInvitationController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $invitation = Invitation::token($token)->firstOrFail();

        if (! $invitation || ! $invitation->isPending() || $invitation->isExpired()) {
            return redirect()->route('login')->with('flash', [
                'status' => 'warning',
                'title' => 'Invitation invalid',
                'description' => 'This invitation is invalid, expired, or revoked.',
            ]);
        }

        $request->session()->put('invitation.token', $token);

        $request->session()->put('url.intended', route('invitation.accept', ['token' => $token]));

        return Auth::check()
            ? redirect()->route('invitation.accept', ['token' => $token])
            : redirect()->route('login');
    }
}
