<?php

namespace App\Controllers;

use App\Models\MUser;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function loginDebug()
    {
        $model = new \App\Models\MUser();
        $user = $model->where('role', 'ppic')->first();
        if ($user) {
            session()->set([
                'id_user'   => $user['id'],
                'username'  => $user['username'],
                'role'      => $user['role'],
                'logged_in' => true
            ]);
        }
        return redirect()->to('/ppic');
    }

    public function processLogin()
    {
        $model = new MUser();

        $user = $model
            ->where('username', $this->request->getPost('username'))
            ->first();

        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {

            session()->set([
                'id_user'   => $user['id'],
                'username'  => $user['username'],
                'role'      => $user['role'],
                'logged_in' => true
            ]);

            // redirect berdasarkan role
            switch ($user['role']) {
                case 'manager_ppic':
                    return redirect()->to('/ppic');
                case 'home':
                    return redirect()->to('/home');
                case 'admin':
                    return redirect()->to('/admin');
                case 'ppic':
                    return redirect()->to('/ppic');
                case 'operator_ppic':
                    return redirect()->to('/ppic');
                case 'operator':
                    return redirect()->to('/operator');
                case 'gudang':
                    return redirect()->to('/gudang');
                case 'produksi':
                    return redirect()->to('/produksi');
                case 'leader':
                    return redirect()->to('/produksi');
                case 'leader_plant1':
                    return redirect()->to('/produksi');
                case 'leader_plant2':
                    return redirect()->to('/produksi');
                case 'operator_produksi':
                    return redirect()->to('/produksi');
                case 'operator_produksi1':
                    return redirect()->to('/produksi');
                case 'operator_produksi2':
                    return redirect()->to('/produksi');
                case 'warehouse2':
                    return redirect()->to('/warehouse2');
                case 'manager_warehouse2':
                    return redirect()->to('/warehouse2');
                case 'realtime':
                    return redirect()->to('/realtime');
            }

            return redirect()->back()->with('error', 'Login gagal');
        }

        return redirect()->back()->with('error', 'Username atau password salah');
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
