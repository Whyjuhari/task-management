<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ParticipantSubmissionIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_participant_submission_history(): void
    {
        $this->get(route('submissions.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_open_participant_submission_history(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('submissions.index'))
            ->assertForbidden();
    }

    public function test_participant_only_sees_their_own_submissions(): void
    {
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $otherParticipant = User::factory()->create(['role' => User::ROLE_USER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $newestTask = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Milik Peserta Terbaru',
            'status' => Task::STATUS_ACTIVE,
        ]);
        $olderTask = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Milik Peserta Lama',
            'status' => Task::STATUS_CLOSED,
        ]);
        $otherTask = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Milik Peserta Lain',
        ]);

        Submission::factory()->for($newestTask)->for($participant)->create([
            'submitted_at' => Carbon::parse('2026-08-03 10:00:00'),
            'status' => Submission::STATUS_SUBMITTED,
        ]);
        Submission::factory()->for($olderTask)->for($participant)->create([
            'submitted_at' => Carbon::parse('2026-08-02 10:00:00'),
            'status' => Submission::STATUS_LATE,
        ]);
        Submission::factory()->for($otherTask)->for($otherParticipant)->create();

        $this->actingAs($participant)
            ->get(route('submissions.index'))
            ->assertOk()
            ->assertSee('Pengumpulan Saya')
            ->assertSeeInOrder(['Tugas Milik Peserta Terbaru', 'Tugas Milik Peserta Lama'])
            ->assertDontSee('Tugas Milik Peserta Lain')
            ->assertSee('Tepat Waktu')
            ->assertSee('Terlambat')
            ->assertViewHas('statistics', [
                'total' => 2,
                'submitted' => 1,
                'late' => 1,
            ]);
    }

    public function test_submission_history_is_paginated(): void
    {
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $tasks = Task::factory()
            ->count(12)
            ->for($admin, 'creator')
            ->create();

        foreach ($tasks as $task) {
            Submission::factory()->for($task)->for($participant)->create();
        }

        $this->actingAs($participant)
            ->get(route('submissions.index'))
            ->assertOk()
            ->assertSee('Menampilkan')
            ->assertSee('Berikutnya')
            ->assertViewHas('submissions', function ($submissions): bool {
                return $submissions->total() === 12 && $submissions->count() === 10;
            });
    }
}
