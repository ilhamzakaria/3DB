<?php

namespace App\Controllers;

use App\Models\MChat;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pesan extends BaseController
{
    protected $MChat;

    private const CHAT_ROLES = ['ppic', 'produksi', 'gudang', 'admin', 'manager'];
    private const MAX_ATTACHMENT_SIZE = 5242880; // 5 MB
    private const ALLOWED_ATTACHMENT_EXT = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'txt',
        'zip',
        'rar',
    ];

    public function __construct()
    {
        $this->MChat = new MChat();
    }

    private function hasChatAccess(): bool
    {
        if (!session()->get('logged_in')) {
            return false;
        }

        $role = session()->get('role');
        return in_array($role, self::CHAT_ROLES, true);
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!$this->hasChatAccess()) {
            return redirect()->back()->with('error', 'Akses chat hanya untuk PPIC, Produksi, Gudang, Admin, dan Manager.');
        }

        // Tandai semua pesan terbaca saat buka halaman chat
        $last = $this->MChat->orderBy('id', 'DESC')->first();
        if ($last && !empty($last['id'])) {
            session()->set('last_chat_read_id', (int) $last['id']);
        }

        return view('pesan', [
            'title' => 'Chat - Komunikasi Departemen',
        ]);
    }

    /**
     * AJAX: Ambil pesan chat (untuk polling)
     */
    public function getMessages()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        if (!$this->hasChatAccess()) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $lastId = (int) $this->request->getGet('last_id');

        $query = $this->MChat
            ->orderBy('id', 'ASC');

        if ($lastId > 0) {
            $query = $query->where('id >', $lastId);
        }

        $messages = $query->findAll();

        return $this->response->setJSON([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * AJAX: Kirim pesan
     */
    public function sendMessage()
    {
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        if (!$this->hasChatAccess()) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $id_user = session()->get('id_user');
        $username = session()->get('username');
        $role = session()->get('role');

        if (!$id_user || !$username) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $message = trim((string) $this->request->getPost('message'));

        $file = $this->request->getFile('attachment');
        $hasFile = $file && $file->getError() !== UPLOAD_ERR_NO_FILE;

        if (!$message && !$hasFile) {
            return $this->response->setJSON(['error' => 'Pesan atau file harus diisi']);
        }

        $attachmentData = [];
        if ($hasFile) {
            if (!$file->isValid() || $file->hasMoved()) {
                return $this->response->setJSON(['error' => 'File gagal diupload']);
            }

            if ($file->getSize() > self::MAX_ATTACHMENT_SIZE) {
                return $this->response->setJSON(['error' => 'Ukuran file maksimal 5 MB']);
            }

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, self::ALLOWED_ATTACHMENT_EXT, true)) {
                return $this->response->setJSON(['error' => 'Format file tidak diizinkan']);
            }

            $uploadDir = WRITEPATH . 'uploads/chat';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $clientName = $file->getClientName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            $newName = $file->getRandomName();
            try {
                $file->move($uploadDir, $newName);
            } catch (\Throwable $e) {
                return $this->response->setJSON(['error' => 'File gagal diupload']);
            }

            if (!$file->hasMoved()) {
                return $this->response->setJSON(['error' => 'File gagal diupload']);
            }

            $attachmentData = [
                'attachment_path' => 'chat/' . $newName,
                'attachment_name' => basename($clientName),
                'attachment_mime' => $mimeType,
                'attachment_size' => $size,
            ];
        }

        $data = array_merge([
            'id_user'  => $id_user,
            'username' => $username,
            'role'     => $role,
            'message'  => $message,
        ], $attachmentData);

        $id = $this->MChat->insert($data);

        if ($id) {
            $row = $this->MChat->find($id);
            return $this->response->setJSON([
                'success' => true,
                'message' => $row,
            ]);
        }

        return $this->response->setJSON(['error' => 'Gagal mengirim pesan']);
    }

    /**
     * AJAX: Jumlah pesan belum dibaca (untuk badge & notif)
     */
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        if (!$this->hasChatAccess()) {
            return $this->response->setJSON(['success' => true, 'count' => 0]);
        }

        $id_user = session()->get('id_user');
        if (!$id_user) {
            return $this->response->setJSON(['success' => true, 'count' => 0]);
        }

        $lastRead = (int) session()->get('last_chat_read_id');
        $count = $this->MChat
            ->where('id >', $lastRead)
            ->where('id_user !=', $id_user) // pesan dari orang lain saja
            ->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'count'   => $count,
        ]);
    }

    /**
     * Download attachment
     */
    public function download($id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!$this->hasChatAccess()) {
            return redirect()->back()->with('error', 'Akses chat hanya untuk PPIC, Produksi, Gudang, Admin, dan Manager.');
        }

        $row = $this->MChat->find((int) $id);
        if (!$row || empty($row['attachment_path'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $path = WRITEPATH . 'uploads/' . $row['attachment_path'];
        if (!is_file($path)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $download = $this->response->download($path, null);
        if (!empty($row['attachment_name'])) {
            $download->setFileName($row['attachment_name']);
        }

        return $download;
    }
}

