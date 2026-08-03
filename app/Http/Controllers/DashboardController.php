<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(): View
    {
        return view('dashboard', [
            'pageTitle' => 'Dasbor Admin',
            'description' => 'Ringkasan area kerja instruktur untuk mengelola kegiatan pelatihan.',
        ]);
    }

    public function participant(): View
    {
        return view('dashboard', [
            'pageTitle' => 'Dasbor Peserta',
            'description' => 'Area kerja peserta untuk mengikuti tugas dan pengumpulan pelatihan.',
        ]);
    }
}
