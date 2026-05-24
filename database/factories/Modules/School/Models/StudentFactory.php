<?php

namespace Database\Factories\Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Student;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('NIS-######'),
            'nisn' => fake()->unique()->numerify('NISN-######'),
            'status' => 'active',
            'entry_date' => now()->toDateString(),
        ];
    }
}
