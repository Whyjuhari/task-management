<?php

namespace Tests\Feature\Admin;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_a_paginated_task_list(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Task::factory()->count(12)->for($admin, 'creator')->create();

        $response = $this->actingAs($admin)->get(route('admin.tasks.index'));

        $response
            ->assertOk()
            ->assertViewHas('tasks', fn ($tasks) => $tasks->total() === 12 && $tasks->perPage() === 10);
    }

    public function test_admin_can_search_tasks_by_title_and_filter_by_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Latihan Laravel Dasar',
            'status' => Task::STATUS_ACTIVE,
        ]);

        Task::factory()->for($admin, 'creator')->create([
            'title' => 'Desain Antarmuka',
            'status' => Task::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.tasks.index', [
            'search' => 'Laravel',
            'status' => Task::STATUS_ACTIVE,
        ]));

        $response
            ->assertOk()
            ->assertSee('Latihan Laravel Dasar')
            ->assertDontSee('Desain Antarmuka');
    }

    public function test_admin_can_create_a_task_and_created_by_uses_the_authenticated_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.tasks.store'), $this->validPayload([
            'title' => 'Tugas Autentikasi Laravel',
            'created_by' => $otherUser->id,
            'start_date' => null,
        ]));

        $task = Task::query()->where('title', 'Tugas Autentikasi Laravel')->firstOrFail();

        $response
            ->assertRedirect(route('admin.tasks.show', $task))
            ->assertSessionHas('success', 'Tugas berhasil dibuat.');

        $this->assertSame($admin->id, $task->created_by);
    }

    public function test_task_form_rejects_invalid_required_dates_types_and_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)
            ->from(route('admin.tasks.create'))
            ->post(route('admin.tasks.store'), [
                'title' => '',
                'description' => '',
                'start_date' => '2026-08-10T10:00',
                'deadline' => '2026-08-09T10:00',
                'submission_type' => 'email',
                'status' => 'pending',
            ]);

        $response
            ->assertRedirect(route('admin.tasks.create'))
            ->assertSessionHasErrors([
                'title',
                'description',
                'deadline',
                'submission_type',
                'status',
            ]);
    }

    public function test_admin_can_read_update_change_status_and_delete_a_task(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($admin, 'creator')->create([
            'title' => 'Tugas Sebelum Diperbarui',
            'status' => Task::STATUS_DRAFT,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.tasks.show', $task))
            ->assertOk()
            ->assertSee('Tugas Sebelum Diperbarui');

        $updateResponse = $this->actingAs($admin)->put(
            route('admin.tasks.update', $task),
            $this->validPayload([
                'title' => 'Tugas Setelah Diperbarui',
                'created_by' => $otherUser->id,
            ]),
        );

        $updateResponse
            ->assertRedirect(route('admin.tasks.show', $task))
            ->assertSessionHas('success', 'Tugas berhasil diperbarui.');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Tugas Setelah Diperbarui',
            'created_by' => $admin->id,
        ]);

        $statusResponse = $this->actingAs($admin)
            ->from(route('admin.tasks.show', $task))
            ->patch(route('admin.tasks.status', $task), ['status' => Task::STATUS_CLOSED]);

        $statusResponse
            ->assertRedirect(route('admin.tasks.show', $task))
            ->assertSessionHas('success', 'Status tugas berhasil diperbarui.');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => Task::STATUS_CLOSED,
        ]);

        $deleteResponse = $this->actingAs($admin)->delete(route('admin.tasks.destroy', $task));

        $deleteResponse
            ->assertRedirect(route('admin.tasks.index'))
            ->assertSessionHas('success', 'Tugas berhasil dihapus.');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_participant_cannot_access_admin_task_management(): void
    {
        $participant = User::factory()->create(['role' => User::ROLE_USER]);

        $this->actingAs($participant)
            ->get(route('admin.tasks.index'))
            ->assertForbidden();
    }

    public function test_deleting_a_task_removes_its_private_submission_files(): void
    {
        Storage::fake('local');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $task = Task::factory()->for($admin, 'creator')->create();
        Storage::disk('local')->put('submissions/hasil-peserta.pdf', 'isi-file');

        Submission::factory()->for($task)->for($participant)->create([
            'file_path' => 'submissions/hasil-peserta.pdf',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.tasks.destroy', $task))
            ->assertRedirect(route('admin.tasks.index'));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertDatabaseMissing('submissions', ['task_id' => $task->id]);
        Storage::disk('local')->assertMissing('submissions/hasil-peserta.pdf');
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Tugas Praktik Laravel',
            'category' => 'Web Development',
            'description' => 'Membuat aplikasi Laravel sederhana.',
            'instructions' => 'Kumpulkan proyek sebelum deadline.',
            'start_date' => '2026-08-10T09:00',
            'deadline' => '2026-08-12T17:00',
            'submission_type' => Task::SUBMISSION_TYPE_FILE_OR_LINK,
            'status' => Task::STATUS_DRAFT,
        ], $overrides);
    }
}
