<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Produksi routes - akses lintas role
 * Full access  : admin, manager, operator, operator_produksi1, operator_produksi2, produksi
 * Read-only    : ppic, manager_ppic, operator_ppic, warehouse2, manager_warehouse2
 */
class RoleReadOnlyProduksi implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');

        $allowed = ['admin', 'manager', 'leader', 'leader_plant1', 'leader_plant2', 'operator', 'operator_produksi', 'operator_produksi1', 'operator_produksi2', 'ppic', 'manager_ppic', 'operator_ppic', 'produksi', 'warehouse2', 'manager_warehouse2'];

        if (!in_array($role, $allowed)) {
            return redirect()->to('/home')->with('error', 'Akses terbatas');
        }

        // ppic, manager_ppic, operator_ppic, warehouse2, manager_warehouse2 hanya boleh GET (view)
        $readOnlyRoles = ['ppic', 'manager_ppic', 'operator_ppic', 'warehouse2', 'manager_warehouse2'];
        if (in_array($role, $readOnlyRoles)) {
            $method = $request->getMethod();
            if (in_array($method, ['post', 'put', 'delete'])) {
                return redirect()->back()->with('error', 'Anda hanya dapat melihat data produksi, tidak dapat mengubah atau menghapus');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
