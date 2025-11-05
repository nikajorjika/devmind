<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\InviteMemberRequest;

class InviteMemberController extends Controller
{
    public function __invoke(InviteMemberRequest $request)
    {
        dd('Invite Member Page', $request->validated());
    }
}
