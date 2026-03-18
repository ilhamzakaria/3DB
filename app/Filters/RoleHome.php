<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleHome implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!in_array(session()->get('role'), ['ppic', 'admin', 'home', 'manager', 'produksi', 'gudang'])) {
            return redirect()->to('/operator');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
