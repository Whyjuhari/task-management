<?php

namespace Tests\Feature\Admin;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_every_participant_and_the_correct_summary(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        [$admin, $task] = $this->adminAndTask();
        $submittedParticipant = User::factory()->create([
            'name' => 'Ayu Tepat Waktu',
            'email' => 'ayu@example.test',
            'role' => User::ROLE_USER,
        ]);
        $lateParticipant = User::factory()->create([
            'name' => 'Budi Terlambat',
            'email' => 'budi@example.test',
            'role' => User::ROLE_USER,
        ]);
        User::factory()->create([
            'name' => 'Citra Belum Mengumpulkan',
            'email' => 'citra@example.test',
            'role' => User::ROLE_USER,
        ]);

        Submission::factory()->for($task)->for($submittedParticipant)->create([
            'submitted_at' => now()->subHour(),
            'status' => Submission::STATUS_SUBMITTED,
        ]);
        Submission::factory()->for($task)->for($lateParticipant)->create([
            'submitted_at' => now()->addHour(),
            'status' => Submission::STATUS_LATE,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.submissions.index', [
            'task_id' => $task->id,
        ]));

        $response
            ->assertOk()
            ->assertSee('Ayu Tepat Waktu')
            ->assertSee('Budi Terlambat')
            ->assertSee('Citra Belum Mengumpulkan')
            ->assertSee('Sudah Mengumpulkan')
            ->assertSee('Belum Mengumpulkan')
            ->assertSee('Terlambat')
            ->assertViewHas('summary', [
                'total' => 3,
                'submitted' => 1,
                'not_submitted' => 1,
                'late' => 1,
                'percentage' => 66.7,
            ]);
    }

    public function test_admin_can_search_participants_by_name_or_email(): void
    {
        [$admin, $task] = $this->adminAndTask();
        User::factory()->create([
            'name' => 'Dewi Larasati',
            'email' => 'dewi@example.test',
            'role' => User::ROLE_USER,
        ]);
        User::factory()->create([
            'name' => 'Eko Prasetyo',
            'email' => 'eko@example.test',
            'role' => User::ROLE_USER,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.index', [
                'task_id' => $task->id,
                'search' => 'dewi@',
            ]))
            ->assertOk()
            ->assertSee('Dewi Larasati')
            ->assertDontSee('Eko Prasetyo');
    }

    public function test_admin_can_filter_each_monitoring_status(): void
    {
        [$admin, $task] = $this->adminAndTask();
        $submittedParticipant = User::factory()->create([
            'name' => 'Peserta Tepat Waktu',
            'role' => User::ROLE_USER,
        ]);
        $lateParticipant = User::factory()->create([
            'name' => 'Peserta Terlambat',
            'role' => User::ROLE_USER,
        ]);
        User::factory()->create([
            'name' => 'Peserta Belum Mengumpulkan',
            'role' => User::ROLE_USER,
        ]);

        Submission::factory()->for($task)->for($submittedParticipant)->create([
            'status' => Submission::STATUS_SUBMITTED,
        ]);
        Submission::factory()->for($task)->for($lateParticipant)->create([
            'status' => Submission::STATUS_LATE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.index', [
                'task_id' => $task->id,
                'status' => Submission::STATUS_SUBMITTED,
            ]))
            ->assertSee('Peserta Tepat Waktu')
            ->assertDontSee('Peserta Terlambat')
            ->assertDontSee('Peserta Belum Mengumpulkan');

        $this->actingAs($admin)
            ->get(route('admin.submissions.index', [
                'task_id' => $task->id,
                'status' => Submission::STATUS_LATE,
            ]))
            ->assertDontSee('Peserta Tepat Waktu')
            ->assertSee('Peserta Terlambat')
            ->assertDontSee('Peserta Belum Mengumpulkan');

        $this->actingAs($admin)
            ->get(route('admin.submissions.index', [
                'task_id' => $task->id,
                'status' => 'not_submitted',
            ]))
            ->assertDontSee('Peserta Tepat Waktu')
            ->assertDontSee('Peserta Terlambat')
            ->assertSee('Peserta Belum Mengumpulkan');
    }

    public function test_admin_can_view_submission_details(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        [$admin, $task] = $this->adminAndTask([
            'title' => 'Tugas Monitoring Detail',
            'deadline' => Carbon::parse('2026-08-04 17:00:00'),
        ]);
        $participant = User::factory()->create([
            'name' => 'Fajar Nugraha',
            'email' => 'fajar@example.test',
            'role' => User::ROLE_USER,
        ]);
        $submission = Submission::factory()->for($task)->for($participant)->create([
            'original_file_name' => 'hasil-fajar.zip',
            'submission_link' => 'https://example.com/hasil-fajar',
            'note' => 'Catatan untuk instruktur.',
            'submitted_at' => Carbon::parse('2026-08-03 07:30:00'),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Tugas Monitoring Detail')
            ->assertSee('Fajar Nugraha')
            ->assertSee('fajar@example.test')
            ->assertSee('hasil-fajar.zip')
            ->assertSee('https://example.com/hasil-fajar')
            ->assertSee('Catatan untuk instruktur.')
            ->assertSee('3 Agustus 2026, 07:30');
    }

    public function test_only_admin_can_download_an_existing_private_file(): void
    {
        Storage::fake('local');

        [$admin, $task] = $this->adminAndTask();
        $participant = User::factory()->create(['role' => User::ROLE_USER]);
        Storage::disk('local')->put('submissions/hasil-peserta.pdf', 'isi-file');
        $submission = Submission::factory()->for($task)->for($participant)->create([
            'file_path' => 'submissions/hasil-peserta.pdf',
            'original_file_name' => 'Laporan Peserta.pdf',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.download', $submission))
            ->assertOk()
            ->assertDownload('Laporan Peserta.pdf');

        $this->actingAs($participant)
            ->get(route('admin.submissions.download', $submission))
            ->assertForbidden();

        $this->app['auth']->guard()->logout();

        $this->get(route('admin.submissions.download', $submission))
            ->assertRedirect(route('login'));
    }

    public function test_download_returns_not_found_for_missing_or_unapproved_paths(): void
    {
        Storage::fake('local');

        [$admin, $task] = $this->adminAndTask();
        $participant = User::factory()->create(['role' => User::ROLE_USER]);

        $missingFileSubmission = Submission::factory()->for($task)->for($participant)->create([
            'file_path' => 'submissions/tidak-ada.pdf',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.submissions.download', $missingFileSubmission))
            ->assertNotFound();

        $unapprovedPathSubmission = Submission::factory()->for($task)
            ->for(User::factory()->create(['role' => User::ROLE_USER]))
            ->create(['file_path' => '../rahasia.txt']);

        $this->actingAs($admin)
            ->get(route('admin.submissions.download', $unapprovedPathSubmission))
            ->assertNotFound();
    }

    public function test_monitoring_has_an_empty_state_before_a_task_is_selected(): void
    {
        [$admin] = $this->adminAndTask();

        $this->actingAs($admin)
            ->get(route('admin.submissions.index'))
            ->assertOk()
            ->assertSee('Pilih tugas untuk dipantau')
            ->assertDontSee('Total peserta');
    }

    public function test_admin_can_export_monitoring_csv_with_special_characters(): void
    {
        $this->travelTo(Carbon::parse('2026-08-03 08:00:00'));

        [$admin, $task] = $this->adminAndTask([
            'title' => 'Analisis, "Data" & Presentasi',
        ]);
        $submittedParticipant = User::factory()->create([
            'name' => 'Budi, "Santoso"',
            'email' => 'budi@example.test',
            'role' => User::ROLE_USER,
        ]);
        User::factory()->create([
            'name' => 'Ayu Belum',
            'email' => 'ayu@example.test',
            'role' => User::ROLE_USER,
        ]);
        Submission::factory()->for($task)->for($submittedParticipant)->create([
            'original_file_name' => 'laporan, final.pdf',
            'submission_link' => 'https://example.com/hasil?bagian=1&status=final',
            'note' => "Baris pertama, dengan koma.\nBaris kedua \"dengan kutip\".",
            'submitted_at' => Carbon::parse('2026-08-03 07:30:00'),
            'status' => Submission::STATUS_SUBMITTED,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.submissions.export', [
            'task_id' => $task->id,
        ]));

        $response
            ->assertOk()
            ->assertDownload('monitoring-analisis-data-presentasi-2026-08-03.csv');

        $rows = $this->csvRows($response->streamedContent());

        $this->assertSame([
            'Nama Peserta',
            'Email',
            'Judul Tugas',
            'Status',
            'Waktu Pengumpulan',
            'Nama File',
            'Tautan',
            'Catatan',
        ], $rows[0]);
        $this->assertCount(3, $rows);
        $rowsByParticipant = collect(array_slice($rows, 1))->keyBy(fn (array $row): string => $row[0]);

        $this->assertSame([
            'Budi, "Santoso"',
            'budi@example.test',
            'Analisis, "Data" & Presentasi',
            'Sudah Mengumpulkan',
            '03 Agustus 2026, 07:30',
            'laporan, final.pdf',
            'https://example.com/hasil?bagian=1&status=final',
            "Baris pertama, dengan koma.\nBaris kedua \"dengan kutip\".",
        ], $rowsByParticipant->get('Budi, "Santoso"'));
        $this->assertSame([
            'Ayu Belum',
            'ayu@example.test',
            'Analisis, "Data" & Presentasi',
            'Belum Mengumpulkan',
            '',
            '',
            '',
            '',
        ], $rowsByParticipant->get('Ayu Belum'));
    }

    public function test_csv_export_uses_monitoring_filters_and_is_restricted_to_admin(): void
    {
        [$admin, $task] = $this->adminAndTask();
        $submittedParticipant = User::factory()->create([
            'name' => 'Peserta Sudah',
            'role' => User::ROLE_USER,
        ]);
        $notSubmittedParticipant = User::factory()->create([
            'name' => 'Peserta Belum',
            'role' => User::ROLE_USER,
        ]);
        Submission::factory()->for($task)->for($submittedParticipant)->create();

        $response = $this->actingAs($admin)->get(route('admin.submissions.export', [
            'task_id' => $task->id,
            'search' => 'Peserta',
            'status' => 'not_submitted',
        ]));

        $rows = $this->csvRows($response->streamedContent());

        $this->assertCount(2, $rows);
        $this->assertSame($notSubmittedParticipant->name, $rows[1][0]);

        $this->actingAs($submittedParticipant)
            ->get(route('admin.submissions.export', ['task_id' => $task->id]))
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $taskAttributes
     * @return array{User, Task}
     */
    private function adminAndTask(array $taskAttributes = []): array
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $task = Task::factory()->for($admin, 'creator')->create(array_merge([
            'start_date' => now()->subDay(),
            'deadline' => now()->addDay(),
            'status' => Task::STATUS_ACTIVE,
        ], $taskAttributes));

        return [$admin, $task];
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    private function csvRows(string $content): array
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, preg_replace('/^\xEF\xBB\xBF/', '', $content));
        rewind($stream);

        $rows = [];

        while (($row = fgetcsv($stream, null, ',', '"', '')) !== false) {
            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }
}
