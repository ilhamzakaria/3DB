<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Warehouse2 routes - akses lintas role
 * Full access  : admin, manager, operator, manager_warehouse2, operator_produksi
 * Read-only    : ppic, manager_ppic, operator_ppic, produksi, warehouse2
 */
class RoleReadOnlyWarehouse2 implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');

        $allowed = ['admin', 'manager', 'operator', 'ppic', 'manager_ppic', 'operator_ppic', 'produksi', 'warehouse2', 'operator_produksi', 'manager_warehouse2'];

        if (!in_array($role, $allowed)) {
            return redirect()->to('/home')->with('error', 'Akses terbatas');
        }

        // ppic, manager_ppic, operator_ppic, produksi, warehouse2 hanya boleh GET (view)
        $readOnlyRoles = ['ppic', 'manager_ppic', 'operator_ppic', 'produksi', 'warehouse2'];
        if (in_array($role, $readOnlyRoles)) {
            $method = $request->getMethod();
            if (in_array($method, ['post', 'put', 'delete'])) {
                return redirect()->back()->with('error', 'Anda hanya dapat melihat data warehouse2, tidak dapat mengubah atau menghapus');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
