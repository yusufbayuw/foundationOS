<?php

namespace Modules\Cms\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cms\Models\HomepageSection;

/**
 * @extends Factory<HomepageSection>
 */
class HomepageSectionFactory extends Factory
{
    protected $model = HomepageSection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(HomepageSection::standardTypes()),
            'title' => fake()->words(2, true),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'settings' => ['limit' => 5],
        ];
    }
}
