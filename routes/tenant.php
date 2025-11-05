<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Member\InviteMemberController;
use App\Http\Controllers\Member\ShowMembersController;
use Illuminate\Support\Facades\Route;


Route::get('dashboard', DashboardController::class)->name('dashboard');


Route::prefix('members')->group(function () {
    Route::get('/', ShowMembersController::class)->name('showMembers');
    Route::post('/invite', InviteMemberController::class)->name('inviteMember');
});

require __DIR__.'/settings.php';

