<?php

namespace App\Controllers;

use App\Models\MNotifikasi;


class Notifikasi extends BaseController
{
    protected $notifModel;

    public function __construct()
    {
        $this->notifModel = new MNotifikasi();
    }

    // tampilkan semua notifikasi
    public function index()
    {

        $role = session()->get('role');

        $data['notifikasi'] = $this->notifModel
            ->where('role_tujuan', $role)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data['title'] = 'Notifikasi';
        return view('notifikasi/index', $data);
    }

    // hitung notif unread (untuk badge)
    public function count()
    {
        $role = session()->get('role');

        $count = $this->notifModel
            ->where('role_tujuan', $role)
            ->where('status', 'unread')
            ->countAllResults();

        return $this->response->setJSON(['total' => $count]);
    }

    // tandai notif sudah dibaca
    public function read($id)
    {
        $this->notifModel->update($id, [
            'status' => 'read'
        ]);

        return redirect()->back();
    }
}
