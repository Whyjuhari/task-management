<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(Request $request): View
    {
        return view('dashboard', [
            'user' => $request->user(),
            'pageTitle' => 'Dasbor Admin',
            'roleLabel' => 'Admin / Instruktur',
        ]);
    }

    public function participant(Request $request): View
    {
        return view('dashboard', [
            'user' => $request->user(),
            'pageTitle' => 'Dasbor Peserta',
            'roleLabel' => 'Peserta Pelatihan',
        ]);
    }
}
