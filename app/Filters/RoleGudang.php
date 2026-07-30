<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleGudang implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Only gudang, admin, and manager can write to warehouse2
        if (!in_array(session()->get('role'), ['gudang', 'admin', 'manager'])) {
            return $this->forbiddenResponse($request);
        }
    }

    protected function forbiddenResponse(RequestInterface $request)
    {
        // If AJAX / API request, return JSON error
        if (
            $request->hasHeader('X-Requested-With') ||
            str_contains($request->getHeaderLine('Accept'), 'application/json') ||
            str_contains($request->getHeaderLine('Content-Type'), 'application/json')
        ) {
            $response = service('response');
            return $response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk melakukan aksi ini.'
                ]);
        }

        // For normal requests, redirect back with error
        return redirect()->back()->with('error', 'Akses ditolak. Hanya gudang, admin, dan manager yang dapat mengubah data ini.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
