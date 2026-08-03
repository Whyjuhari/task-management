<?php

namespace Tests\Feature;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_submit_a_file_to_private_storage(): void
    {
        Storage::fake('local');
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_FILE,
            'deadline' => now()->addDay(),
        ]);

        $response = $this->actingAs($participant)->post(route('submissions.store', $task), [
            'file' => UploadedFile::fake()->create('hasil-tugas.pdf', 500, 'application/pdf'),
            'note' => 'Tugas sudah diperiksa kembali.',
        ]);

        $submission = Submission::query()->sole();

        $response
            ->assertRedirect(route('submissions.show', $submission))
            ->assertSessionHas('success', 'Tugas berhasil dikumpulkan.');

        $this->assertSame($task->id, $submission->task_id);
        $this->assertSame($participant->id, $submission->user_id);
        $this->assertSame('hasil-tugas.pdf', $submission->original_file_name);
        $this->assertSame(Submission::STATUS_SUBMITTED, $submission->status);
        $this->assertStringStartsWith('submissions/', $submission->file_path);
        $this->assertNotSame('submissions/hasil-tugas.pdf', $submission->file_path);
        Storage::disk('local')->assertExists($submission->file_path);
    }

    public function test_link_task_requires_a_valid_link_and_can_be_submitted(): void
    {
        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_LINK,
        ]);

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [])
            ->assertSessionHasErrors('submission_link');

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [
                'submission_link' => 'https://github.com/example/taskflow-result',
                'note' => 'Repository dapat diakses oleh instruktur.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('submissions', [
            'task_id' => $task->id,
            'user_id' => $participant->id,
            'submission_link' => 'https://github.com/example/taskflow-result',
            'file_path' => null,
        ]);
    }

    public function test_file_or_link_requires_at_least_one_submission_source(): void
    {
        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_FILE_OR_LINK,
        ]);

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), ['note' => 'Belum menyertakan hasil.'])
            ->assertSessionHasErrors('submission_link');

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [
                'submission_link' => 'https://example.com/hasil-tugas',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('submissions', 1);
    }

    public function test_file_validation_rejects_unsupported_type_and_files_over_five_megabytes(): void
    {
        Storage::fake('local');

        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_FILE,
        ]);

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [
                'file' => UploadedFile::fake()->create('program.exe', 100, 'application/octet-stream'),
            ])
            ->assertSessionHasErrors('file');

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [
                'file' => UploadedFile::fake()->create('terlalu-besar.pdf', 5121, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_submission_after_deadline_is_marked_as_late_while_task_is_active(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_LINK,
            'deadline' => now()->subMinute(),
            'status' => Task::STATUS_ACTIVE,
        ]);

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [
                'submission_link' => 'https://example.com/terlambat',
            ])
            ->assertRedirect();

        $submission = Submission::query()->sole();

        $this->assertSame(Submission::STATUS_LATE, $submission->status);
        $this->assertTrue($submission->submitted_at->isAfter($task->deadline));
    }

    public function test_updating_a_file_keeps_one_record_and_deletes_the_old_file(): void
    {
        Storage::fake('local');
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_FILE,
            'deadline' => now()->addDay(),
        ]);
        Storage::disk('local')->put('submissions/file-lama.pdf', 'old-content');

        $submission = Submission::factory()->for($task)->for($participant)->create([
            'file_path' => 'submissions/file-lama.pdf',
            'original_file_name' => 'file-lama.pdf',
            'submission_link' => null,
            'submitted_at' => now()->subHour(),
        ]);

        $this->actingAs($participant)
            ->put(route('submissions.update', $submission), [
                'file' => UploadedFile::fake()->create('file-baru.zip', 250, 'application/zip'),
                'note' => 'Versi terbaru.',
            ])
            ->assertRedirect(route('submissions.show', $submission));

        $submission->refresh();

        $this->assertDatabaseCount('submissions', 1);
        $this->assertSame('file-baru.zip', $submission->original_file_name);
        $this->assertSame('Versi terbaru.', $submission->note);
        $this->assertNotSame('submissions/file-lama.pdf', $submission->file_path);
        Storage::disk('local')->assertMissing('submissions/file-lama.pdf');
        Storage::disk('local')->assertExists($submission->file_path);
    }

    public function test_repeated_store_updates_the_existing_submission(): void
    {
        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_LINK,
        ]);

        $this->actingAs($participant)->post(route('submissions.store', $task), [
            'submission_link' => 'https://example.com/versi-satu',
        ]);

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [
                'submission_link' => 'https://example.com/versi-dua',
                'note' => 'Dikirim ulang.',
            ])
            ->assertSessionHas('success', 'Pengumpulan berhasil diperbarui.');

        $this->assertDatabaseCount('submissions', 1);
        $this->assertDatabaseHas('submissions', [
            'submission_link' => 'https://example.com/versi-dua',
            'note' => 'Dikirim ulang.',
        ]);
    }

    public function test_participant_cannot_access_or_update_another_participants_submission(): void
    {
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        [$owner, $task] = $this->participantAndTask();
        $submission = Submission::factory()->for($task)->for($owner)->create();

        $this->actingAs($participant)
            ->get(route('submissions.show', $submission))
            ->assertForbidden();

        $this->actingAs($participant)
            ->get(route('submissions.edit', $submission))
            ->assertForbidden();

        $this->actingAs($participant)
            ->put(route('submissions.update', $submission), [
                'submission_link' => 'https://example.com/diambil-alih',
            ])
            ->assertForbidden();
    }

    public function test_closed_task_cannot_receive_or_update_a_submission(): void
    {
        [$participant, $task] = $this->participantAndTask([
            'submission_type' => Task::SUBMISSION_TYPE_LINK,
            'status' => Task::STATUS_CLOSED,
        ]);

        $this->actingAs($participant)
            ->get(route('submissions.create', $task))
            ->assertForbidden();

        $this->actingAs($participant)
            ->post(route('submissions.store', $task), [
                'submission_link' => 'https://example.com/ditolak',
            ])
            ->assertForbidden();

        $submission = Submission::factory()->for($task)->for($participant)->create();

        $this->actingAs($participant)
            ->put(route('submissions.update', $submission), [
                'submission_link' => 'https://example.com/tetap-ditolak',
            ])
            ->assertForbidden();
    }

    public function test_owner_can_view_submission_details(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        [$participant, $task] = $this->participantAndTask([
            'title' => 'Tugas Detail Pengumpulan',
            'deadline' => Carbon::parse('2026-08-04 17:00:00'),
        ]);
        $submission = Submission::factory()->for($task)->for($participant)->create([
            'original_file_name' => 'hasil-akhir.zip',
            'submission_link' => 'https://example.com/hasil-akhir',
            'note' => 'Catatan pengumpulan peserta.',
            'submitted_at' => Carbon::parse('2026-08-03 07:30:00'),
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $this->actingAs($participant)
            ->get(route('submissions.show', $submission))
            ->assertOk()
            ->assertSee('Tugas Detail Pengumpulan')
            ->assertSee('hasil-akhir.zip')
            ->assertSee('https://example.com/hasil-akhir')
            ->assertSee('Catatan pengumpulan peserta.')
            ->assertSee('3 Agustus 2026, 07:30')
            ->assertSee('Sudah Dikumpulkan');
    }

    /**
     * @param  array<string, mixed>  $taskAttributes
     * @return array{User, Task}
     */
    private function participantAndTask(array $taskAttributes = []): array
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        $task = Task::factory()->for($admin, 'creator')->create(array_merge([
            'start_date' => now()->subDay(),
            'deadline' => now()->addDay(),
            'submission_type' => Task::SUBMISSION_TYPE_FILE_OR_LINK,
            'status' => Task::STATUS_ACTIVE,
        ], $taskAttributes));

        return [$participant, $task];
    }
}
