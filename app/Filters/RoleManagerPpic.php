<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * PPIC - Manager role (full access)
 * Only manager and admin can access PPIC features
 */
class RoleManagerPpic implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        if (!in_array($role, ['admin', 'manager_ppic'])) {
            return redirect()->to('/home')->with('error', 'Akses PPIC hanya untuk Manager');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
