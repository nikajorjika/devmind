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
});

Route::prefix('invitation')->group(function () {
    Route::post('/create', InviteMemberController::class)->name('invitationCreate');
    Route::post('/{invitation}/resend', ResendInvitationController::class)
        ->name('invitationResend')
        ->middleware('throttle:resend-invitation');
    Route::delete('/{invitation}/revoke', RevokeInvitationController::class)
        ->name('invitationRevoke');
});

require __DIR__.'/settings.php';

