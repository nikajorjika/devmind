<?php

use App\Models\User;
use App\Models\Workspace;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('workspace create page can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('workspace.create'));

    $response->assertStatus(200);
    $response->assertInertia(fn (Assert $page) => $page
        ->component('workspace/Create')
    );
});

test('user can create a workspace', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
        'subdomain' => 'test-workspace',
    ]);

    $response->assertRedirect(route('dashboard'));

    expect($user->workspaces()->count())->toBe(1)
        ->and($user->workspaces()->first()->name)->toBe('Test Workspace')
        ->and($user->workspaces()->first()->subdomain)->toBe('test-workspace');
});

test('workspace creation requires name', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'subdomain' => 'test-workspace',
    ]);

    $response->assertSessionHasErrors('name');
});

test('workspace creation requires subdomain', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertSessionHasErrors('subdomain');
});

test('workspace subdomain must be unique', function () {
    $user = User::factory()->create();
    Workspace::factory()->create(['subdomain' => 'existing']);

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
        'subdomain' => 'existing',
    ]);

    $response->assertSessionHasErrors('subdomain');
});

test('user can switch between workspaces', function () {
    $user = User::factory()->create();
    $workspace1 = Workspace::factory()->create();
    $workspace2 = Workspace::factory()->create();

    $user->workspaces()->attach([$workspace1->id, $workspace2->id]);
    $user->forceFill(['current_workspace_id' => $workspace1->id])->save();

    $response = $this->actingAs($user)->post(route('workspace.switch'), [
        'workspace_id' => $workspace2->id,
    ]);

    $response->assertRedirect(route('dashboard'));

    expect($user->fresh()->current_workspace_id)->toBe($workspace2->id);
});

test('user cannot switch to workspace they do not belong to', function () {
    $user = User::factory()->withWorkspace()->create();
    $otherWorkspace = Workspace::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.switch'), [
        'workspace_id' => $otherWorkspace->id,
    ]);

    $response->assertForbidden();
});

test('workspace switch requires workspace_id', function () {
    $user = User::factory()->withWorkspace()->create();

    $response = $this->actingAs($user)->post(route('workspace.switch'), []);

    $response->assertForbidden(); // Authorization fails without valid workspace_id
});

test('user can switch to second workspace after creating first', function () {
    $user = User::factory()->create();
    $workspace1 = Workspace::factory()->create();
    $workspace2 = Workspace::factory()->create();

    $user->workspaces()->attach([$workspace1->id, $workspace2->id]);
    $user->forceFill(['current_workspace_id' => $workspace1->id])->save();

    $response = $this->actingAs($user)->post(route('workspace.switch'), [
        'workspace_id' => $workspace2->id,
    ]);

    $response->assertRedirect(route('dashboard'));

    expect($user->fresh()->current_workspace_id)->toBe($workspace2->id);
});
