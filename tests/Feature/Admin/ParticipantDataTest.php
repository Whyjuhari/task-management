<?php

namespace Tests\Feature\Admin;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_participant_task_and_submission_totals(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create([
            'name' => 'Ayu Statistik',
            'email' => 'ayu.statistik@example.test',
            'role' => User::ROLE_USER,
        ]);
        $participantWithoutSubmission = User::factory()->create([
            'name' => 'Budi Belum',
            'email' => 'budi.belum@example.test',
            'role' => User::ROLE_USER,
        ]);

        $activeTask = Task::factory()->for($admin, 'creator')->create([
            'status' => Task::STATUS_ACTIVE,
        ]);
        $closedTask = Task::factory()->for($admin, 'creator')->create([
            'status' => Task::STATUS_CLOSED,
        ]);
        $draftTask = Task::factory()->for($admin, 'creator')->create([
            'status' => Task::STATUS_DRAFT,
        ]);

        Submission::factory()->for($activeTask)->for($participant)->create([
            'status' => Submission::STATUS_SUBMITTED,
        ]);
        Submission::factory()->for($closedTask)->for($participant)->create([
            'status' => Submission::STATUS_LATE,
        ]);
        Submission::factory()->for($draftTask)->for($participant)->create([
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.participants.index'));

        $response
            ->assertOk()
            ->assertSee('Ayu Statistik')
            ->assertSee('ayu.statistik@example.test')
            ->assertSee('Budi Belum')
            ->assertViewHas('totalTasks', 2)
            ->assertViewHas('participants', function ($participants) use ($participant, $participantWithoutSubmission): bool {
                $ayu = $participants->firstWhere('id', $participant->id);
                $budi = $participants->firstWhere('id', $participantWithoutSubmission->id);

                return $ayu->submitted_count === 1
                    && $ayu->late_count === 1
                    && $budi->submitted_count === 0
                    && $budi->late_count === 0;
            });
    }

    public function test_participant_data_page_has_empty_state_and_admin_protection(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.participants.index'))
            ->assertOk()
            ->assertSee('Belum ada peserta');

        $participant = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($participant)
            ->get(route('admin.participants.index'))
            ->assertForbidden();
    }
}
