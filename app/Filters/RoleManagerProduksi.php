<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Produksi write routes - hanya role dengan full access yang boleh POST/PUT/DELETE
 * Full access  : admin, manager, operator, produksi
 */
class RoleManagerProduksi implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');

        if (!in_array($role, ['admin', 'manager', 'operator', 'produksi'])) {
            return redirect()->to('/home')->with('error', 'Akses terbatas');
        }

        // operator: hanya bisa POST ke simpan/tambahData
        if ($role === 'operator') {
            $method = $request->getMethod();
            $uri = $request->getUri()->getPath();

            if ($method === 'post') {
                if (!preg_match('/\/(simpan|tambahData)$/i', $uri)) {
                    return redirect()->back()->with('error', 'Operator hanya dapat menambah data, tidak dapat mengubah atau menghapus');
                }
            } elseif (in_array($method, ['put', 'delete'])) {
                return redirect()->back()->with('error', 'Operator hanya dapat menambah data, tidak dapat mengubah atau menghapus');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
