<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_database_statistics_and_ordered_activity(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participants = User::factory()->count(3)->create(['role' => User::ROLE_USER]);

        $nearTask = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Deadline Terdekat',
            'status' => Task::STATUS_ACTIVE,
            'deadline' => now()->addDay(),
            'created_at' => now()->subHour(),
        ]);
        $farTask = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Deadline Jauh',
            'status' => Task::STATUS_ACTIVE,
            'deadline' => now()->addDays(5),
            'created_at' => now()->subHours(2),
        ]);
        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Draf',
            'status' => Task::STATUS_DRAFT,
            'deadline' => now()->addDays(2),
            'created_at' => now()->subHours(3),
        ]);
        $closedTask = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Ditutup',
            'status' => Task::STATUS_CLOSED,
            'deadline' => now()->subDay(),
            'created_at' => now()->subHours(4),
        ]);

        Submission::factory()->for($nearTask)->for($participants[0])->create([
            'submitted_at' => now()->subHours(3),
            'status' => Submission::STATUS_SUBMITTED,
        ]);
        $latestSubmission = Submission::factory()->for($nearTask)->for($participants[1])->create([
            'submitted_at' => now()->subHour(),
            'status' => Submission::STATUS_LATE,
        ]);
        Submission::factory()->for($closedTask)->for($participants[2])->create([
            'submitted_at' => now()->subHours(2),
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Tugas Deadline Terdekat')
            ->assertSee('Aktivitas Pengumpulan Terbaru')
            ->assertSee('Progress Pengumpulan')
            ->assertSee('3 Agustus 2026, 07:00')
            ->assertViewHas('statistics', [
                'total_tasks' => 4,
                'active_tasks' => 2,
                'total_participants' => 3,
                'total_submissions' => 3,
                'late_submissions' => 1,
                'near_deadline_tasks' => 1,
            ])
            ->assertViewHas('latestTasks', fn ($tasks): bool => $tasks->first()->is($nearTask))
            ->assertViewHas('latestSubmissions', fn ($submissions): bool => $submissions->first()->is($latestSubmission))
            ->assertViewHas('nearestDeadlineTasks', fn ($tasks): bool => $tasks->first()->is($nearTask))
            ->assertViewHas('taskProgress', function ($progress) use ($nearTask): bool {
                $firstProgress = $progress->first();

                return $firstProgress['task']->is($nearTask)
                    && $firstProgress['submitted_count'] === 2
                    && $firstProgress['percentage'] === 66.7;
            });
    }

    public function test_participant_dashboard_only_uses_the_authenticated_participants_data(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create([
            'name' => 'Juhari Dashboard',
            'role' => User::ROLE_USER,
        ]);
        $otherParticipant = User::factory()->create(['role' => User::ROLE_USER]);

        $submittedTask = $this->activeTask($admin, 'Tugas Sudah Dikumpulkan', now()->addDays(4));
        $lateTask = $this->activeTask($admin, 'Tugas Dikumpulkan Terlambat', now()->subDay());
        $nearPendingTask = $this->activeTask($admin, 'Tugas Mendesak', now()->addHours(12));
        $farPendingTask = $this->activeTask($admin, 'Tugas Berikutnya', now()->addDays(3));
        $closedTask = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Lama Ditutup',
            'status' => Task::STATUS_CLOSED,
            'deadline' => now()->subDays(2),
        ]);

        Submission::factory()->for($submittedTask)->for($participant)->create([
            'submitted_at' => now()->subHours(3),
            'status' => Submission::STATUS_SUBMITTED,
        ]);
        Submission::factory()->for($lateTask)->for($participant)->create([
            'submitted_at' => now()->subHours(2),
            'status' => Submission::STATUS_LATE,
        ]);
        $latestOwnSubmission = Submission::factory()->for($closedTask)->for($participant)->create([
            'submitted_at' => now()->subHour(),
            'status' => Submission::STATUS_SUBMITTED,
        ]);
        Submission::factory()->for($nearPendingTask)->for($otherParticipant)->create([
            'submitted_at' => now(),
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($participant)->get(route('dashboard'));

        $response
            ->assertOk()
            ->assertSee('Halo, Juhari Dashboard')
            ->assertSee('Tugas Mendesak')
            ->assertSee('Tugas Berikutnya')
            ->assertSee('3 Agustus 2026, 07:00')
            ->assertViewHas('statistics', [
                'total_active_tasks' => 4,
                'submitted' => 1,
                'not_submitted' => 2,
                'late' => 1,
                'nearest_deadline' => $nearPendingTask->deadline,
                'completion_percentage' => 50.0,
            ])
            ->assertViewHas('nearestDeadlineTask', fn ($task): bool => $task->is($nearPendingTask))
            ->assertViewHas('urgentTasks', function ($tasks) use ($nearPendingTask, $farPendingTask): bool {
                return $tasks->pluck('id')->all() === [
                    $nearPendingTask->id,
                    $farPendingTask->id,
                ];
            })
            ->assertViewHas(
                'latestSubmissions',
                fn ($submissions): bool => $submissions->first()->is($latestOwnSubmission)
                    && $submissions->every(fn (Submission $submission): bool => $submission->user_id === $participant->id),
            );
    }

    public function test_dashboards_show_empty_states_when_database_has_no_related_data(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Belum ada tugas')
            ->assertSee('Belum ada pengumpulan')
            ->assertSee('Belum ada progress')
            ->assertViewHas('statistics', [
                'total_tasks' => 0,
                'active_tasks' => 0,
                'total_participants' => 1,
                'total_submissions' => 0,
                'late_submissions' => 0,
                'near_deadline_tasks' => 0,
            ]);

        $this->actingAs($participant)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tidak ada tugas mendesak')
            ->assertSee('Belum ada pengumpulan')
            ->assertSee('Tidak ada')
            ->assertViewHas('statistics', [
                'total_active_tasks' => 0,
                'submitted' => 0,
                'not_submitted' => 0,
                'late' => 0,
                'nearest_deadline' => null,
                'completion_percentage' => 0,
            ]);
    }

    private function activeTask(User $admin, string $title, Carbon $deadline): Task
    {
        return Task::factory()->for($admin, 'creator')->create([
            'title' => $title,
            'status' => Task::STATUS_ACTIVE,
            'start_date' => now()->subDay(),
            'deadline' => $deadline,
        ]);
    }
}
