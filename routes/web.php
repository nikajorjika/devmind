<?php

use App\Http\Controllers\Member\ApproveInvitationController;
use App\Http\Controllers\Member\JoinInvitationController;
use App\Http\Controllers\Member\RejectInvitationController;
use App\Http\Controllers\Member\ShowAcceptInvitationController;
use App\Http\Controllers\Workspace\CreateWorkspaceController;
use App\Http\Controllers\Workspace\StoreWorkspaceController;
use App\Http\Controllers\Workspace\SwitchWorkspaceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/workspace/create', CreateWorkspaceController::class)->name('workspace.create');
    Route::post('/workspace/create', StoreWorkspaceController::class)->name('workspace.store');
    Route::post('/workspace/switch', SwitchWorkspaceController::class)->name('workspace.switch');
});

Route::group([], function () {
    Route::get('/invitation/join/{token}', JoinInvitationController::class)->name('invitation.join');
    Route::get('/invitation/accept/{token}',
        ShowAcceptInvitationController::class)->middleware('auth')->name('invitation.accept');

    Route::post('/invitation/approve/{token}', ApproveInvitationController::class)
        ->name('invitation.approve');

    Route::delete('/invitation/reject/{token}', RejectInvitationController::class)
        ->name('invitation.reject');
});
