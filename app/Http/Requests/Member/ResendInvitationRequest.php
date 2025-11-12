<?php

namespace App\Http\Requests\Member;

use App\Enums\Workspace\Capabilities;
use Illuminate\Foundation\Http\FormRequest;

class ResendInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function canPerformAction($invitation): bool
    {
        return $this->user()->can(Capabilities::MEMBER_INVITE) && $invitation->belongsToCurrentWorkspace($this);
    }

}
