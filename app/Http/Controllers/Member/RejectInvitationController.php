<?php

namespace App\Http\Controllers\Member;

use App\Enums\Invitation\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;

class RejectInvitationController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        /** @var Invitation $invitation */
        $invitation = Invitation::query()
            ->where('token', $token)
            ->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect()
                ->route('invitation.accept', $token)
                ->with('flash', [
                    'status' => 'warning',
                    'title' => 'Invitation invalid',
                    'description' => 'This invitation is invalid, expired, or revoked.',
                ]);
        }

        if (strtolower($invitation->email) !== strtolower($request->user()->email)) {
            return redirect()
                ->route('invitation.accept', $token)
                ->with('flash', [
                    'status' => 'warning',
                    'title' => 'Email mismatch',
                    'description' => 'You are logged in as a different user than the invited email.',
                ]);
        }

        if ($invitation->status === InvitationStatus::DECLINED) {
            return $this->redirectAfterReject();
        }

        $invitation->forceFill([
            'status' => InvitationStatus::DECLINED->value,
            'accepted_at' => null,
            'accepted_by' => null,
        ])->save();

        return $this->redirectAfterReject();
    }

    protected function redirectAfterReject()
    {
        // Send them somewhere neutral; or back to a "Join" page with info
        return redirect()->route('dashboard')->with('flash', [
            'status' => 'info',
            'title' => 'Invitation declined',
            'description' => 'You have declined the invitation.',
        ]);
    }
}
