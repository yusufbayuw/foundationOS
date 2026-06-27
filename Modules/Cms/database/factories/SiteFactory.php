<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cms\Models\Site;

/**
 * @extends Factory<Site>
 */
class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        return [
            'code' => strtolower(fake()->unique()->bothify('site-###')),
            'name' => fake()->words(2, true),
            'domain' => fake()->domainName(),
            'default_locale' => 'id',
            'is_active' => true,
        ];
    }
}
