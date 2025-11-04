<?php

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
