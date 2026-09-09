<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * Produksi write routes — akses per role:
 * admin          : tambah saja (tidak boleh update/delete)
 * leader         : edit saja  (tidak boleh tambah/delete)
 * leader_plant1  : edit saja, hanya mesin plant 1
 * leader_plant2  : edit saja, hanya mesin plant 2
 * operator       : tambah saja
 * operator_produksi  : tambah saja
 * operator_produksi1 : tambah saja, hanya mesin plant 1
 * operator_produksi2 : tambah saja, hanya mesin plant 2
 * produksi       : full access (tambah, edit, hapus)
 * manager        : full access
 */
class RoleManagerProduksi implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');

        if (!in_array($role, ['admin', 'manager', 'leader', 'leader_plant1', 'leader_plant2', 'operator', 'operator_produksi', 'operator_produksi1', 'operator_produksi2', 'produksi'])) {
            return redirect()->to('/home')->with('error', 'Akses terbatas');
        }

        $method = strtolower($request->getMethod());
        $uri    = $request->getUri()->getPath();

        // admin: hanya boleh tambah (simpan), tidak boleh update/delete
        if ($role === 'admin') {
            if ($method === 'post' && preg_match('/(\/update\/|\/delete\/|\/restore\/|\/deletePermanent\/)/i', $uri)) {
                return redirect()->back()->with('error', 'Admin hanya dapat menambah data, tidak dapat mengubah atau menghapus.');
            }
        }

        // leader, leader_plant1, leader_plant2: hanya boleh update, tidak boleh tambah/delete
        if (in_array($role, ['leader', 'leader_plant1', 'leader_plant2'])) {
            if ($method === 'post' && !preg_match('/\/update\//i', $uri)) {
                return redirect()->back()->with('error', 'Leader hanya dapat mengedit data, tidak dapat menambah atau menghapus.');
            }
        }

        // operator: hanya boleh tambah (simpan/tambahData), tidak boleh update/delete
        if ($role === 'operator') {
            if ($method === 'post') {
                if (!preg_match('/\/(simpan|tambahData)$/i', $uri)) {
                    return redirect()->back()->with('error', 'Operator hanya dapat menambah data, tidak dapat mengubah atau menghapus.');
                }
            } elseif (in_array($method, ['put', 'delete'])) {
                return redirect()->back()->with('error', 'Operator hanya dapat menambah data, tidak dapat mengubah atau menghapus.');
            }
        }

        // operator_produksi: hanya boleh tambah (simpan/tambahData), tidak boleh update/delete
        if ($role === 'operator_produksi') {
            if ($method === 'post') {
                if (!preg_match('/\/(simpan|tambahData)$/i', $uri)) {
                    return redirect()->back()->with('error', 'Operator produksi hanya dapat menambah data, tidak dapat mengubah atau menghapus.');
                }
            } elseif (in_array($method, ['put', 'delete'])) {
                return redirect()->back()->with('error', 'Operator produksi hanya dapat menambah data, tidak dapat mengubah atau menghapus.');
            }
        }

        // operator_produksi1/2: hanya boleh tambah (simpan/tambahData), tidak boleh update/delete
        if (in_array($role, ['operator_produksi1', 'operator_produksi2'])) {
            if ($method === 'post') {
                if (!preg_match('/\/(simpan|tambahData)$/i', $uri)) {
                    return redirect()->back()->with('error', 'Operator produksi hanya dapat menambah data, tidak dapat mengubah atau menghapus.');
                }
            } elseif (in_array($method, ['put', 'delete'])) {
                return redirect()->back()->with('error', 'Operator produksi hanya dapat menambah data, tidak dapat mengubah atau menghapus.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
