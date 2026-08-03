<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_id' => Task::factory(),
            'user_id' => User::factory(),
            'file_path' => null,
            'original_file_name' => null,
            'submission_link' => fake()->url(),
            'note' => fake()->optional()->sentence(),
            'submitted_at' => now(),
            'status' => Submission::STATUS_SUBMITTED,
        ];
    }
}
