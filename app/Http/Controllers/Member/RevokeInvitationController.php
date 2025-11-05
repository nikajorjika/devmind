<?php

namespace App\Http\Controllers\Member;

use App\Enums\Invitation\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;

class RevokeInvitationController extends Controller
{
    public function __invoke(Request $request, Invitation $invitation)
    {
        // Ensure the invitation belongs to the current workspace
        if ($invitation->workspace_id !== $request->session()->get('current_workspace_id')) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure the invitation is still pending
        if (!$invitation->isPending()) {
            return redirect()->back()->withErrors([
                'invitation' => 'This invitation cannot be revoked because it is no longer pending.',
            ]);
        }

        // Revoke the invitation
        $invitation->update([
            'revoked_at' => now(),
            'status' => InvitationStatus::REVOKED->value,
        ]);

        return redirect()->back()->with('flash', [
            'status' => 'success',
            'title' => 'Invitation revoked',
            'description' => 'The invitation for '.$invitation->email.' has been revoked.',
        ]);
    }
}
