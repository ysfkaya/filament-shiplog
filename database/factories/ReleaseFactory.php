<?php

namespace Ysfkaya\ShipLog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Ysfkaya\ShipLog\Enums\ReleaseStatus;
use Ysfkaya\ShipLog\Models\Release;

/**
 * @extends Factory<Release>
 */
class ReleaseFactory extends Factory
{
    protected $model = Release::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version' => implode('.', $this->faker->unique()->randomElements(range(0, 40), 3)),
            'title' => $this->faker->sentence(3),
            'body' => "### Added\n- " . $this->faker->sentence(),
            'released_at' => $this->faker->dateTimeBetween('-1 year'),
            'status' => ReleaseStatus::Published,
            'environments' => [],
            'yanked' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(['status' => ReleaseStatus::Draft]);
    }

    /**
     * @param  array<int, string>  $environments
     */
    public function onlyIn(array $environments): static
    {
        return $this->state(['environments' => $environments]);
    }

    public function yanked(): static
    {
        return $this->state(['yanked' => true]);
    }
}
