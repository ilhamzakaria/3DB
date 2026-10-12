<?php

namespace App\Controllers;

use App\Models\MSettingMesin;

class SettingMesin extends BaseController
{
    protected $model;
    protected $db;

    public function __construct()
    {
        $this->model = new MSettingMesin();
        $this->db    = \Config\Database::connect();
    }

    /**
     * Display list of setting mesin
     */
    public function index()
    {
        try {
            $data['title'] = 'Setting Mesin';
            $data['mesin'] = $this->model->orderBy('id', 'DESC')->paginate(10);
            $data['pager'] = $this->model->pager;

            return view('setting_mesin/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in SettingMesin::index - ' . $e->getMessage());
            // Return empty data instead of redirect to avoid redirect loop
            $data['title'] = 'Setting Mesin';
            $data['mesin'] = [];
            $data['pager'] = null;
            $data['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
            return view('setting_mesin/index', $data);
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        try {
            $data['title'] = 'Tambah Setting Mesin';
            return view('setting_mesin/create', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in SettingMesin::create - ' . $e->getMessage());
            return redirect()->to('settings-mesin')->with('error', 'Terjadi kesalahan');
        }
    }

    /**
     * Store new setting mesin to database
     */
    public function store()
    {
        try {
            // Remove id_user dari validation rules karena akan diambil dari session
            $rules = [
                'product' => 'required',
                'machine' => 'required',
                'type'    => 'required',
                'no_mc'   => 'required',
                'tanggal' => 'required|valid_date[Y-m-d]',
                'shift'   => 'required|integer',
            ];
            
            $messages = [
                'product' => [
                    'required' => 'Produk harus diisi',
                ],
                'machine' => [
                    'required' => 'Mesin harus diisi',
                ],
                'type' => [
                    'required' => 'Tipe harus diisi',
                ],
                'no_mc' => [
                    'required' => 'Nomor Mesin harus diisi',
                ],
                'tanggal' => [
                    'required'    => 'Tanggal harus diisi',
                    'valid_date' => 'Format tanggal tidak valid (gunakan YYYY-MM-DD)',
                ],
                'shift' => [
                    'required' => 'Shift harus diisi',
                    'integer'  => 'Shift harus berupa angka',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'id_user' => session('user_id') ?? session('id') ?? 'unknown',
                'product' => $this->request->getPost('product'),
                'machine' => $this->request->getPost('machine'),
                'type'    => $this->request->getPost('type'),
                'no_mc'   => $this->request->getPost('no_mc'),
                'tanggal' => $this->request->getPost('tanggal'),
                'shift'   => $this->request->getPost('shift'),
            ];

            log_message('debug', 'SettingMesin::store - Data to insert: ' . json_encode($data));

            $insertId = $this->model->insert($data);
            
            if ($insertId) {
                log_message('debug', 'SettingMesin::store - Insert success, ID: ' . $insertId);
                return redirect()->to('settings-mesin')->with('success', 'Data Setting Mesin berhasil ditambahkan');
            } else {
                log_message('error', 'SettingMesin::store - Insert failed. Errors: ' . json_encode($this->model->errors()));
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . json_encode($this->model->errors()));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in SettingMesin::store - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $item = $this->model->find($id);

            if (!$item) {
                return redirect()->to('settings-mesin')->with('error', 'Data tidak ditemukan');
            }

            $data['title'] = 'Edit Setting Mesin';
            $data['item']  = $item;

            return view('setting_mesin/edit', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in SettingMesin::edit - ' . $e->getMessage());
            return redirect()->to('settings-mesin')->with('error', 'Terjadi kesalahan');
        }
    }

    /**
     * Update setting mesin in database
     */
    public function update($id)
    {
        try {
            $item = $this->model->find($id);

            if (!$item) {
                return redirect()->to('settings-mesin')->with('error', 'Data tidak ditemukan');
            }

            // Remove id_user dari validation rules
            $rules = [
                'product' => 'required',
                'machine' => 'required',
                'type'    => 'required',
                'no_mc'   => 'required',
                'tanggal' => 'required|valid_date[Y-m-d]',
                'shift'   => 'required|integer',
            ];
            
            $messages = [
                'product' => [
                    'required' => 'Produk harus diisi',
                ],
                'machine' => [
                    'required' => 'Mesin harus diisi',
                ],
                'type' => [
                    'required' => 'Tipe harus diisi',
                ],
                'no_mc' => [
                    'required' => 'Nomor Mesin harus diisi',
                ],
                'tanggal' => [
                    'required'    => 'Tanggal harus diisi',
                    'valid_date' => 'Format tanggal tidak valid (gunakan YYYY-MM-DD)',
                ],
                'shift' => [
                    'required' => 'Shift harus diisi',
                    'integer'  => 'Shift harus berupa angka',
                ],
            ];

            if (!$this->validate($rules, $messages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = [
                'id_user' => session('user_id') ?? session('id'),
                'product' => $this->request->getPost('product'),
                'machine' => $this->request->getPost('machine'),
                'type'    => $this->request->getPost('type'),
                'no_mc'   => $this->request->getPost('no_mc'),
                'tanggal' => $this->request->getPost('tanggal'),
                'shift'   => $this->request->getPost('shift'),
            ];

            if ($this->model->update($id, $data)) {
                return redirect()->to('settings-mesin')->with('success', 'Data Setting Mesin berhasil diubah');
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengubah data');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in SettingMesin::update - ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete setting mesin from database
     */
    public function delete($id)
    {
        try {
            $item = $this->model->find($id);

            if (!$item) {
                return redirect()->to('settings-mesin')->with('error', 'Data tidak ditemukan');
            }

            if ($this->model->delete($id)) {
                return redirect()->to('settings-mesin')->with('success', 'Data Setting Mesin berhasil dihapus');
            } else {
                return redirect()->to('settings-mesin')->with('error', 'Gagal menghapus data');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in SettingMesin::delete - ' . $e->getMessage());
            return redirect()->to('settings-mesin')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
