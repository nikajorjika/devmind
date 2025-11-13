<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Integration\GithubIntegrationController;
use App\Http\Controllers\Integration\IntegrationsController;
use App\Http\Controllers\Member\InviteMemberController;
use App\Http\Controllers\Member\ResendInvitationController;
use App\Http\Controllers\Member\RevokeInvitationController;
use App\Http\Controllers\Member\ShowMembersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::prefix('projects')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('projects.index');
});
Route::prefix('developers')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('developers.index');
});

Route::prefix('integrations')->group(function () {
    Route::get('/', [IntegrationsController::class, 'index'])->name('integrations.index');

    Route::prefix('github')->group(function () {
        Route::post('/redirect', [GithubIntegrationController::class, 'redirect'])->name('integrations.github.redirect');
        Route::get('/callback', [GithubIntegrationController::class, 'callback'])->name('integrations.github.callback');
    });
});

require __DIR__.'/settings.php';
