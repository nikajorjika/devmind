<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShowAcceptInvitationController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        $invitation = Invitation::token($token)->withInviter()->withWorkspace()->firstOrFail();
        $invitationResponse = InvitationResource::make($invitation);

        return Inertia::render('members/AcceptInvitation', [
            'invitation' => $invitationResponse->toArray($request),
            'token' => $token,
        ]);
    }
}
