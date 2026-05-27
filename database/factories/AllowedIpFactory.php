<?php

namespace Database\Factories;

use App\Models\AllowedIp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AllowedIp>
 */
class AllowedIpFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => $this->faker->unique()->ipv4(),
            'label' => $this->faker->company(),
            'notes' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): self
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
