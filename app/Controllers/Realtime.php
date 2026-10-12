<?php

namespace App\Controllers;

class Realtime extends BaseController
{
    public function index()
    {
        if (!session('logged_in')) {
            return redirect()->to('/login');
        }

        return view('realtime/index', [
            'mesinLabels' => Produksi::mesinLabels(),
        ]);
    }
}
