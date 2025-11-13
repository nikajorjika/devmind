<?php

namespace Database\Factories;

use App\Models\VersionControlIntegration;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VersionControlIntegration>
 */
class VersionControlIntegrationFactory extends Factory
{
    protected $model = VersionControlIntegration::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'provider' => 'github',
            'external_id' => $this->faker->numerify('#####'),
            'external_name' => $this->faker->company,
            'installation_id' => $this->faker->numerify('#####'),
            'avatar_url' => $this->faker->imageUrl(),
            'meta' => [],
            'connected_at' => now(),
            'disconnected_at' => null,
        ];
    }

    public function github(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'github',
        ]);
    }

    public function gitlab(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'gitlab',
        ]);
    }

    public function disconnected(): static
    {
        return $this->state(fn (array $attributes) => [
            'disconnected_at' => now(),
        ]);
    }
}
