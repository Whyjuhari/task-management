<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskFlowSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@taskflow.test'],
            [
                'name' => 'Admin Instruktur',
                'password' => $password,
                'role' => User::ROLE_ADMIN,
            ],
        );

        $juhari = User::query()->updateOrCreate(
            ['email' => 'peserta@taskflow.test'],
            [
                'name' => 'Juhari',
                'password' => $password,
                'role' => User::ROLE_USER,
            ],
        );

        $ayu = User::query()->updateOrCreate(
            ['email' => 'ayu@taskflow.test'],
            [
                'name' => 'Ayu Lestari',
                'password' => $password,
                'role' => User::ROLE_USER,
            ],
        );

        $budi = User::query()->updateOrCreate(
            ['email' => 'budi@taskflow.test'],
            [
                'name' => 'Budi Santoso',
                'password' => $password,
                'role' => User::ROLE_USER,
            ],
        );

        $citra = User::query()->updateOrCreate(
            ['email' => 'citra@taskflow.test'],
            [
                'name' => 'Citra Wulandari',
                'password' => $password,
                'role' => User::ROLE_USER,
            ],
        );

        $longDeadlineTask = Task::query()->updateOrCreate(
            [
                'title' => 'Membuat Landing Page Responsif',
                'created_by' => $admin->id,
            ],
            [
                'description' => 'Membuat landing page pelatihan yang responsif pada perangkat desktop dan mobile.',
                'instructions' => 'Kumpulkan source code dalam bentuk file ZIP atau tautan repository.',
                'category' => 'Web Development',
                'start_date' => now()->subDay(),
                'deadline' => now()->addDays(7),
                'submission_type' => Task::SUBMISSION_TYPE_FILE_OR_LINK,
                'status' => Task::STATUS_ACTIVE,
            ],
        );

        $nearDeadlineTask = Task::query()->updateOrCreate(
            [
                'title' => 'Membuat Halaman Formulir',
                'created_by' => $admin->id,
            ],
            [
                'description' => 'Membuat formulir HTML dengan validasi dasar dan tampilan yang mudah digunakan.',
                'instructions' => 'Kumpulkan tautan repository sebelum batas waktu berakhir.',
                'category' => 'Web Development',
                'start_date' => now()->subDays(2),
                'deadline' => now()->addHours(18),
                'submission_type' => Task::SUBMISSION_TYPE_LINK,
                'status' => Task::STATUS_ACTIVE,
            ],
        );

        $expiredTask = Task::query()->updateOrCreate(
            [
                'title' => 'Membuat Struktur HTML Semantik',
                'created_by' => $admin->id,
            ],
            [
                'description' => 'Menyusun halaman menggunakan elemen HTML semantik yang sesuai.',
                'instructions' => 'Kumpulkan file HTML dalam bentuk ZIP.',
                'category' => 'Pemrograman Dasar',
                'start_date' => now()->subDays(10),
                'deadline' => now()->subDays(2),
                'submission_type' => Task::SUBMISSION_TYPE_FILE,
                'status' => Task::STATUS_CLOSED,
            ],
        );

        Submission::query()->updateOrCreate(
            ['task_id' => $longDeadlineTask->id, 'user_id' => $juhari->id],
            [
                'file_path' => 'submissions/landing-page-juhari.zip',
                'original_file_name' => 'landing-page-juhari.zip',
                'submission_link' => null,
                'note' => 'Landing page sudah diuji pada tampilan desktop dan mobile.',
                'submitted_at' => now()->subHours(2),
                'status' => Submission::STATUS_SUBMITTED,
            ],
        );

        Submission::query()->updateOrCreate(
            ['task_id' => $nearDeadlineTask->id, 'user_id' => $ayu->id],
            [
                'file_path' => null,
                'original_file_name' => null,
                'submission_link' => 'https://github.com/example/formulir-ayu',
                'note' => 'Formulir sudah dilengkapi validasi dasar.',
                'submitted_at' => now()->subHour(),
                'status' => Submission::STATUS_SUBMITTED,
            ],
        );

        Submission::query()->updateOrCreate(
            ['task_id' => $expiredTask->id, 'user_id' => $budi->id],
            [
                'file_path' => 'submissions/html-semantik-budi.zip',
                'original_file_name' => 'html-semantik-budi.zip',
                'submission_link' => null,
                'note' => 'Pengumpulan dilakukan setelah batas waktu.',
                'submitted_at' => now()->subDay(),
                'status' => Submission::STATUS_LATE,
            ],
        );

        Submission::query()->updateOrCreate(
            ['task_id' => $expiredTask->id, 'user_id' => $citra->id],
            [
                'file_path' => 'submissions/html-semantik-citra.zip',
                'original_file_name' => 'html-semantik-citra.zip',
                'submission_link' => null,
                'note' => 'Struktur HTML sudah diperiksa kembali.',
                'submitted_at' => now()->subDays(3),
                'status' => Submission::STATUS_SUBMITTED,
            ],
        );
    }
}
