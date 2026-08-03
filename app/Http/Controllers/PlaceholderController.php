<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PlaceholderController extends Controller
{
    public function adminTasks(): View
    {
        return $this->show(
            'Kelola Tugas',
            'Halaman untuk membuat dan mengelola tugas pelatihan akan tersedia pada tahap berikutnya.',
        );
    }

    public function adminSubmissions(): View
    {
        return $this->show(
            'Monitoring Pengumpulan',
            'Halaman untuk memantau pengumpulan peserta akan tersedia pada tahap berikutnya.',
        );
    }

    public function adminParticipants(): View
    {
        return $this->show(
            'Data Peserta',
            'Halaman untuk melihat data peserta pelatihan akan tersedia pada tahap berikutnya.',
        );
    }

    public function submissions(): View
    {
        return $this->show(
            'Pengumpulan Saya',
            'Halaman riwayat pengumpulan tugas akan tersedia pada tahap berikutnya.',
        );
    }

    private function show(string $pageTitle, string $description): View
    {
        return view('placeholder', compact('pageTitle', 'description'));
    }
}
