<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Realtime Monitor — akses per role:
 * - realtime   : akses khusus hanya halaman /realtime (read-only)
 * - produksi   : full access termasuk realtime
 * - admin      : bisa akses
 * - manager    : bisa akses
 * - manager_ppic, ppic, gudang, warehouse2, dll: tidak bisa akses
 */
class RoleRealtime implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            if ($request->isAJAX()) {
                return response()
                    ->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->to('/login');
        }

        $role = session()->get('role');

        $allowed = ['realtime', 'produksi', 'admin', 'manager'];

        if (!in_array($role, $allowed)) {
            if ($request->isAJAX()) {
                return response()
                    ->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'Akses ditolak']);
            }
            return redirect()->to('/login')->with('error', 'Anda tidak memiliki akses ke halaman Realtime');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
