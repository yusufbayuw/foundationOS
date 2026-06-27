<?php

namespace Modules\Alumni\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Alumni\Models\CompanyPartner;

/**
 * @extends Factory<CompanyPartner>
 */
class CompanyPartnerFactory extends Factory
{
    protected $model = CompanyPartner::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('CP-###')),
            'name' => fake()->company(),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
