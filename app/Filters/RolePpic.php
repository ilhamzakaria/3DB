<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RolePpic implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!in_array(session()->get('role'), ['home', 'admin', 'manager', 'ppic', 'operator', 'gudang', 'produksi'])) {
            return redirect()->to('/operator');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
