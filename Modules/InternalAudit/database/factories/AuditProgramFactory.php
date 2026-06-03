<?php

namespace Modules\InternalAudit\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\InternalAudit\Models\AuditProgram;

/**
 * @extends Factory<AuditProgram>
 */
class AuditProgramFactory extends Factory
{
    protected $model = AuditProgram::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('AUD-###')),
            'name' => fake()->words(2, true),
            'status' => 'active',
            'description' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
