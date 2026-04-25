<?php

namespace App\Controllers;

use App\Models\MProd;

class Produksi extends BaseController
{
    protected $MProd;

    public function __construct()
    {
        $this->MProd = new MProd();
        helper('form');
    }

    public function index()
    {
        $id_user = session()->get('id_user');
        $role = session()->get('role');
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!$id_user) {
            return redirect()->to('/login');
        }

        if (!empty($startDate) && !empty($endDate) && $endDate < $startDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $query = $this->MProd;

        // Jika bukan ppic, filter berdasarkan id_user
        if ($role !== 'ppic' && $role !== 'produksi' && $role !== 'admin' && $role !== 'gudang') {
            $query = $query->where('id_user', $id_user);
        }

        if ($q !== '') {
            $query = $query->groupStart()
                ->like('jam', $q)
                ->orLike('hasil_produksi', $q)
                ->orLike('no_spk', $q)
                ->orLike('nama_mesin', $q)
                ->orLike('nama_produk', $q)
                ->orLike('batch_number', $q)
                ->orLike('shif', $q)
                ->orLike('grup', $q)
                ->orLike('nomor_mesin', $q)
                ->orLike('packing', $q)
                ->orLike('isi', $q)
                ->orLike('cycle_time', $q)
                ->orLike('target', $q)
                ->orLike('operator', $q)
                ->groupEnd();
        }

        if (!empty($startDate)) {
            $query = $query->where('tanggal >=', $startDate);
        }

        if (!empty($endDate)) {
            $query = $query->where('tanggal <=', $endDate);
        }

        $produksi = $query
            ->orderBy('tanggal', 'DESC')
            ->orderBy('shif', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('produksi', [
            'title' => 'produksi',
            'produksi' => $produksi,
            'spk_master' => $this->getSpkMasterData(),
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/produksi');
        }

        $id_user = session()->get('id_user');
        if (!$id_user) {
            return redirect()->to('/login');
        }

        $jenis  = $this->request->getPost('jenis_reject');
        $jumlah = $this->request->getPost('jumlah_reject');

        $data = array_merge([
            'id_user' => $id_user,
        ], $this->collectProduksiPayload());

        // Mapping reject dinamis ke kolom database
        if (!empty($jenis)) {
            // pastikan kita punya array, karena getPost bisa mengembalikan string ketika hanya satu nilai
            $jenisArr = is_array($jenis) ? $jenis : [$jenis];
            $jumlahArr = is_array($jumlah) ? $jumlah : [$jumlah];

            foreach ($jenisArr as $idx => $jns) {
                if (!empty($jns)) {
                    $data[$jns] = $jumlahArr[$idx] ?? 0;
                }
            }
        }

        // Hitung total reject otomatis
        $totalReject = 0;
        if (!empty($jumlah)) {
            foreach ($jumlah as $j) {
                $totalReject += (int)$j;
            }
        }

        $data['total_rijek'] = $totalReject;

        // Ambil jam produksi dinamis
        $jam   = $this->request->getPost('jam');
        $hasil = $this->request->getPost('hasil_produksi');

        if (!empty($jam)) {
            // pastikan array agar count() tidak dilemparkan string
            $jamArr   = is_array($jam) ? $jam : [$jam];
            $hasilArr = is_array($hasil) ? $hasil : [$hasil];

            foreach ($jamArr as $idx => $j) {
                if (!empty($j)) {
                    // Simpan kolom jam (misal: 06-07)
                    $data[$j] = $j;

                    // Ubah 06-07 jadi 06_07 untuk kolom hasil
                    $kolomHasil = 'hasil_produksi_' . str_replace('-', '_', $j);
                    $data[$kolomHasil] = $hasilArr[$idx] ?? 0;
                }
            }
        }

        $this->MProd->save($data);

        return redirect()->to('/produksi')->with('success', 'Data berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->MProd->delete($id);
        return redirect()->to('/produksi')->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $data = $this->MProd->find($id);
        return view('edit', [
            'title' => 'Ubah Data Produksi',
            'data' => $data
        ]);
    }

    public function update($id)
    {
        $data = $this->collectProduksiPayload();

        $this->MProd->update($id, $data);
        return redirect()->to('/produksi')->with('success', 'Data berhasil diubah');
    }

    public function exportExcel()
    {
        $data = $this->MProd->findAll();

        // Bersihkan output buffer (penting!)
        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=data_produksi.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<tr>
                <th>No SPK</th>
                <th>Tanggal</th>
                <th>Nama Mesin</th>
                <th>Nama Produk</th>
                <th>Batch Number</th>
                <th>Shift</th>
                <th>Nomor Mesin</th>
                <th>Packing Isi</th>
                <th>Cycle Time</th>
                <th>Target</th>
                <th>Operator</th>
            </tr>";

        foreach ($data as $d) {
            echo "<tr>
                    <td>" . $d['no_spk'] . "</td>
                    <td>" . $d['tanggal'] . "</td>
                    <td>" . $d['nama_mesin'] . "</td>
                    <td>" . $d['nama_produk'] . "</td>
                    <td>" . $d['batch_number'] . "</td>
                    <td>" . $d['shif'] . "</td>
                    <td>" . $d['nomor_mesin'] . "</td>
                    <td>" . $d['packing_isi'] . "</td>
                    <td>" . $d['cycle_time'] . "</td>
                    <td>" . $d['target'] . "</td>
                    <td>" . $d['operator'] . "</td>
                </tr>";
        }

        echo "</table>";
        exit;
    }

    private function getSpkMasterData(): array
    {
        $master = [];

        try {
            $ppicRows = db_connect()
                ->table('ppic')
                ->select('no_spk, nama_mesin, nama_produk, shif, operator, targett, tanggal, id')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Throwable $e) {
            $ppicRows = [];
        }

        $prodRows = $this->MProd
            ->select('no_spk, nama_mesin, nama_produk, batch_number, shif, grup, nomor_mesin, packing, isi, cycle_time, target, operator, tanggal, id')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $latestProdBySpk = [];
        foreach ($prodRows as $row) {
            $noSpk = trim((string) ($row['no_spk'] ?? ''));
            if ($noSpk === '' || isset($latestProdBySpk[$noSpk])) {
                continue;
            }

            $latestProdBySpk[$noSpk] = [
                'nama_mesin' => trim((string) ($row['nama_mesin'] ?? '')),
                'nama_produk' => trim((string) ($row['nama_produk'] ?? '')),
                'batch_number' => trim((string) ($row['batch_number'] ?? '')),
                'shif' => trim((string) ($row['shif'] ?? '')),
                'grup' => trim((string) ($row['grup'] ?? '')),
                'nomor_mesin' => trim((string) ($row['nomor_mesin'] ?? '')),
                'packing' => trim((string) ($row['packing'] ?? '')),
                'isi' => trim((string) ($row['isi'] ?? '')),
                'cycle_time' => trim((string) ($row['cycle_time'] ?? '')),
                'target' => trim((string) ($row['target'] ?? '')),
                'operator' => trim((string) ($row['operator'] ?? '')),
                'tanggal' => trim((string) ($row['tanggal'] ?? '')),
            ];
        }

        foreach ($ppicRows as $row) {
            $noSpk = trim((string) ($row['no_spk'] ?? ''));
            if ($noSpk === '' || isset($master[$noSpk])) {
                continue;
            }

            $master[$noSpk] = array_merge([
                'no_spk' => $noSpk,
                'nama_mesin' => trim((string) ($row['nama_mesin'] ?? '')),
                'nama_produk' => trim((string) ($row['nama_produk'] ?? '')),
                'shif' => trim((string) ($row['shif'] ?? '')),
                'batch_number' => '',
                'grup' => '',
                'nomor_mesin' => '',
                'packing' => '',
                'isi' => '',
                'cycle_time' => '',
                'target' => trim((string) ($row['targett'] ?? '')),
                'tanggal' => trim((string) ($row['tanggal'] ?? '')),
                'operator' => trim((string) ($row['operator'] ?? '')),
            ], $latestProdBySpk[$noSpk] ?? []);
        }

        foreach ($latestProdBySpk as $noSpk => $values) {
            if (isset($master[$noSpk])) {
                continue;
            }

            $master[$noSpk] = array_merge([
                'no_spk' => $noSpk,
                'nama_mesin' => '',
                'nama_produk' => '',
                'batch_number' => '',
                'shif' => '',
                'grup' => '',
                'nomor_mesin' => '',
                'packing' => '',
                'isi' => '',
                'cycle_time' => '',
                'target' => '',
                'tanggal' => '',
                'operator' => '',
            ], $values);
        }

        ksort($master, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($master);
    }

    private function collectProduksiPayload(): array
    {
        return [
            'nama_mesin' => $this->request->getPost('nama_mesin'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'batch_number' => $this->request->getPost('batch_number'),
            'shif' => $this->request->getPost('shif'),
            'grup' => $this->request->getPost('grup'),
            'nomor_mesin' => $this->request->getPost('nomor_mesin'),
            'packing' => $this->request->getPost('packing'),
            'isi' => $this->request->getPost('isi'),
            'cycle_time' => $this->request->getPost('cycle_time'),
            'target' => $this->request->getPost('target'),
            'no_spk' => $this->request->getPost('no_spk'),
            'operator' => $this->request->getPost('operator'),
            'jam' => $this->request->getPost('jam'),
            'hasil_produksi' => $this->request->getPost('hasil_produksi'),
            'tanggal' => $this->request->getPost('tanggal'),
        ];
    }
}
