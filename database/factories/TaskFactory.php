<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $startDate = now()->subDay();

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'instructions' => fake()->optional()->paragraph(),
            'category' => fake()->randomElement(['Web Development', 'UI/UX', 'Pemrograman Dasar']),
            'start_date' => $startDate,
            'deadline' => $startDate->copy()->addDays(7),
            'submission_type' => fake()->randomElement(Task::SUBMISSION_TYPES),
            'status' => fake()->randomElement(Task::STATUSES),
            'created_by' => User::factory(),
        ];
    }
}
