<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ParticipantTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_only_sees_active_and_closed_tasks_ordered_by_nearest_deadline(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);

        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Aktif Deadline Kedua',
            'deadline' => now()->addDays(2),
            'status' => Task::STATUS_ACTIVE,
        ]);
        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Aktif Deadline Pertama',
            'deadline' => now()->addDay(),
            'status' => Task::STATUS_ACTIVE,
        ]);
        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Ditutup',
            'deadline' => now()->addDays(3),
            'status' => Task::STATUS_CLOSED,
        ]);
        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Rahasia Draf',
            'deadline' => now()->addHours(12),
            'status' => Task::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($participant)->get(route('tasks.index'));

        $response
            ->assertOk()
            ->assertSee('Tugas Aktif Deadline Pertama')
            ->assertSee('Tugas Aktif Deadline Kedua')
            ->assertSee('Tugas Ditutup')
            ->assertDontSee('Tugas Rahasia Draf')
            ->assertSeeInOrder([
                'Tugas Aktif Deadline Pertama',
                'Tugas Aktif Deadline Kedua',
                'Tugas Ditutup',
            ]);
    }

    public function test_participant_can_search_by_title_and_filter_by_category(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);

        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Latihan Laravel Dasar',
            'category' => 'Web Development',
            'status' => Task::STATUS_ACTIVE,
        ]);
        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Membuat Wireframe',
            'category' => 'UI/UX',
            'status' => Task::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($participant)->get(route('tasks.index', [
            'search' => 'Laravel',
            'category' => 'Web Development',
        ]));

        $response
            ->assertOk()
            ->assertSee('Latihan Laravel Dasar')
            ->assertDontSee('Membuat Wireframe');
    }

    public function test_personal_status_uses_the_participants_submission_and_deadline(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $otherParticipant = User::factory()->create(['role' => User::ROLE_USER]);

        $notSubmittedTask = Task::factory()->for($admin, 'creator')->create([
            'deadline' => now()->addDays(2),
            'status' => Task::STATUS_ACTIVE,
        ]);
        Submission::factory()->for($notSubmittedTask)->for($otherParticipant)->create();

        $submittedTask = Task::factory()->for($admin, 'creator')->create([
            'deadline' => now()->addDays(2),
            'status' => Task::STATUS_ACTIVE,
        ]);
        Submission::factory()->for($submittedTask)->for($participant)->create([
            'submitted_at' => now(),
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $lateTask = Task::factory()->for($admin, 'creator')->create([
            'deadline' => now()->subDay(),
            'status' => Task::STATUS_CLOSED,
        ]);
        Submission::factory()->for($lateTask)->for($participant)->create([
            'submitted_at' => now(),
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $expiredTask = Task::factory()->for($admin, 'creator')->create([
            'deadline' => now()->subHour(),
            'status' => Task::STATUS_ACTIVE,
        ]);

        $closedTask = Task::factory()->for($admin, 'creator')->create([
            'deadline' => now()->addDay(),
            'status' => Task::STATUS_CLOSED,
        ]);

        $this->assertSame(
            Task::PERSONAL_STATUS_NOT_SUBMITTED,
            $notSubmittedTask->personalStatusFor($participant),
        );
        $this->assertSame(
            Task::PERSONAL_STATUS_SUBMITTED,
            $submittedTask->personalStatusFor($participant),
        );
        $this->assertSame(
            Task::PERSONAL_STATUS_LATE,
            $lateTask->personalStatusFor($participant),
        );
        $this->assertSame(
            Task::PERSONAL_STATUS_DEADLINE_ENDED,
            $expiredTask->personalStatusFor($participant),
        );
        $this->assertSame(
            Task::PERSONAL_STATUS_DEADLINE_ENDED,
            $closedTask->personalStatusFor($participant),
        );
    }

    public function test_task_list_only_eager_loads_the_authenticated_participants_submission(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $otherParticipant = User::factory()->create(['role' => User::ROLE_USER]);
        $task = Task::factory()->for($admin, 'creator')->create([
            'status' => Task::STATUS_ACTIVE,
        ]);

        Submission::factory()->for($task)->for($otherParticipant)->create();

        $response = $this->actingAs($participant)->get(route('tasks.index'));

        $response->assertViewHas('tasks', function ($tasks) use ($task): bool {
            $listedTask = $tasks->getCollection()->firstWhere('id', $task->id);

            return $listedTask !== null
                && $listedTask->relationLoaded('submissions')
                && $listedTask->submissions->isEmpty();
        });
    }

    public function test_participant_can_view_active_task_details_with_indonesian_date_format(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $task = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Detail Peserta',
            'category' => 'Web Development',
            'description' => 'Deskripsi lengkap tugas peserta.',
            'instructions' => 'Ikuti instruksi tugas dengan teliti.',
            'start_date' => Carbon::parse('2026-08-05 09:00:00'),
            'deadline' => Carbon::parse('2026-08-10 17:00:00'),
            'submission_type' => Task::SUBMISSION_TYPE_FILE_OR_LINK,
            'status' => Task::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($participant)->get(route('tasks.show', $task));

        $response
            ->assertOk()
            ->assertSee('Tugas Detail Peserta')
            ->assertSee('Web Development')
            ->assertSee('Deskripsi lengkap tugas peserta.')
            ->assertSee('Ikuti instruksi tugas dengan teliti.')
            ->assertSee('5 Agustus 2026, 09:00')
            ->assertSee('10 Agustus 2026, 17:00')
            ->assertSee('File atau tautan')
            ->assertSee('Belum Dikumpulkan')
            ->assertSee('Kumpulkan Tugas');
    }

    public function test_participant_cannot_view_a_draft_task_detail(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $draftTask = Task::factory()->for($admin, 'creator')->create([
            'status' => Task::STATUS_DRAFT,
        ]);

        $this->actingAs($participant)
            ->get(route('tasks.show', $draftTask))
            ->assertNotFound();
    }
}
