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
