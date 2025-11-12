<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('middleware redirects user without workspace to workspace create page', function () {
    $user = User::factory()->create(); // User without workspace

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('workspace.create'));
});

test('middleware allows user with workspace to proceed', function () {
    $user = User::factory()->withWorkspace()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
});

test('middleware returns json response when expecting json', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withHeaders(['Accept' => 'application/json'])
        ->get(route('dashboard'));

    $response->assertStatus(409);
    $response->assertJson([
        'message' => 'Workspace required.',
        'redirect' => route('workspace.create'),
    ]);
});

test('middleware handles inertia post requests', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withHeaders(['X-Inertia' => 'true'])
        ->patch(route('profile.update'), [
            'name' => 'Test',
            'email' => $user->email,
        ]);

    $response->assertStatus(409);
});
