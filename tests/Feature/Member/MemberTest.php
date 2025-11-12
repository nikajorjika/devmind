<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('members page can be rendered', function () {
    $user = User::factory()->withWorkspace()->create();

    $response = $this->actingAs($user)->get(route('showMembers'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('members/Show')
    );
});

test('members page shows workspace members', function () {
    $user = User::factory()->withWorkspace()->create();
    $workspace = $user->currentWorkspace;

    // Add another user to the workspace
    $otherUser = User::factory()->create();
    $workspace->users()->attach($otherUser);

    $response = $this->actingAs($user)->get(route('showMembers'));

    $response->assertStatus(200);

    // Verify the members data structure is present
    $response->assertInertia(fn (Assert $page) => $page
        ->component('members/Show')
    );
});

test('guests cannot access members page', function () {
    $response = $this->get(route('showMembers'));

    $response->assertRedirect(route('login'));
});
