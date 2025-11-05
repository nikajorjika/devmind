<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Member\InviteMemberController;
use App\Http\Controllers\Member\ResendInvitationController;
use App\Http\Controllers\Member\RevokeInvitationController;
use App\Http\Controllers\Member\ShowMembersController;
use Illuminate\Support\Facades\Route;


Route::get('dashboard', DashboardController::class)->name('dashboard');


Route::prefix('members')->group(function () {
    Route::get('/', ShowMembersController::class)->name('showMembers');
    Route::post('/invite', InviteMemberController::class)->name('inviteMember');
    Route::post('/invitations/{invitation}/resend', ResendInvitationController::class)->name('resendInvitation');
    Route::delete('/invitations/{invitation}/revoke', RevokeInvitationController::class)->name('revokeInvitation');
});

require __DIR__.'/settings.php';

