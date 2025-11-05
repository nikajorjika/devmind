<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'inviter_id' => User::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'role_name' => 'member',
            'token' => Str::ulid(),
            'expires_at' => now()->addDays(7),
            'status' => 'pending',
        ];
    }
}
