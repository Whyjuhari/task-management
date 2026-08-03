<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\TaskFlowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_private_local_disk_has_no_framework_serving_route(): void
    {
        $this->assertFalse(Route::has('storage.local'));
        $this->assertFalse(Route::has('storage.local.upload'));
    }

    public function test_user_password_is_hashed_by_the_model(): void
    {
        $user = User::factory()->create(['password' => 'kata-sandi-rahasia']);

        $this->assertNotSame('kata-sandi-rahasia', $user->password);
        $this->assertTrue(Hash::check('kata-sandi-rahasia', $user->password));
    }

    public function test_system_managed_fields_are_not_mass_assignable(): void
    {
        $user = new User;
        $user->fill(['role' => User::ROLE_ADMIN]);

        $task = new Task;
        $task->fill(['created_by' => 99]);

        $submission = new Submission;
        $submission->fill([
            'task_id' => 10,
            'user_id' => 20,
            'file_path' => '../rahasia.txt',
            'submitted_at' => now(),
            'status' => Submission::STATUS_LATE,
        ]);

        $this->assertNull($user->role);
        $this->assertNull($task->created_by);
        $this->assertNull($submission->task_id);
        $this->assertNull($submission->user_id);
        $this->assertNull($submission->file_path);
        $this->assertNull($submission->submitted_at);
        $this->assertNull($submission->status);
    }

    public function test_demo_seeder_assigns_guarded_system_fields_explicitly(): void
    {
        $this->seed(TaskFlowSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@taskflow.test',
            'role' => User::ROLE_ADMIN,
        ]);
        $this->assertDatabaseCount('tasks', 3);
        $this->assertDatabaseCount('submissions', 4);
    }

    public function test_login_is_temporarily_limited_after_repeated_failures(): void
    {
        RateLimiter::clear('peserta@example.test|127.0.0.1');
        User::factory()->create([
            'email' => 'peserta@example.test',
            'password' => 'password-benar',
        ]);

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login.store'), [
                'email' => 'peserta@example.test',
                'password' => 'password-salah',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => 'peserta@example.test',
            'password' => 'password-benar',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
