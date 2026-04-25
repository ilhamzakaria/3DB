<?php

namespace App\Controllers;

use App\Models\MPpic;
use App\Models\MRevisi;

class Ppic extends BaseController
{
    protected $MPpic;
    protected $MRevisi;
    protected ?bool $hasRevisiTable = null;

    public function __construct()
    {
        $this->MPpic = new MPpic();
        $this->MRevisi = new MRevisi();
        helper('form');
        helper('tanggal');
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!empty($startDate) && !empty($endDate) && $endDate < $startDate) {
            // swap agar selalu start <= end
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $builder = $this->MPpic
            ->select('ppic.*, SUM(prod.hasil_produksi) as hasil_produksi')
            ->join(
                'prod',
                'TRIM(prod.no_spk) = TRIM(ppic.no_spk)
    AND prod.tanggal = ppic.tanggal',
                'left'
            );

        if ($q !== '') {
            $builder->groupStart()
                ->like('ppic.no_spk', $q)
                ->orLike('ppic.nama_mesin', $q)
                ->orLike('ppic.nama_produk', $q)
                ->orLike('ppic.grade', $q)
                ->orLike('ppic.warna', $q)
                ->orLike('ppic.nomor_mesin', $q)
                ->orLike('ppic.operator', $q)
                ->orLike('ppic.shif', $q)
                ->groupEnd();
        }

        if (!empty($startDate)) {
            $builder->where('ppic.tanggal >=', $startDate);
        }

        if (!empty($endDate)) {
            $builder->where('ppic.tanggal <=', $endDate);
        }

        $data['ppic'] = $builder
            ->groupBy('ppic.id')
            ->orderBy('ppic.tanggal', 'DESC')
            ->orderBy('ppic.shif', 'DESC')
            ->orderBy('ppic.jam', 'DESC')
            ->orderBy('ppic.id', 'ASC')
            ->findAll();

        $data['ppic'] = $this->attachRevisiData($data['ppic']);

        return view('ppic', [
            'title' => 'Laporan PPIC Harian',
            'produksi' => $data['ppic'],
            'spk_master' => $this->getSpkMasterData(),
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
    public function tambahData()
    {
        // Validasi sederhana (opsional tapi bagus)
        if (!$this->request->is('post')) {
            return redirect()->to('/ppic');
        }

        $jamArray = $this->request->getPost('jam');
        if (!is_array($jamArray)) {
            $jamArray = $jamArray !== null ? [$jamArray] : [];
        }

        $revisiInputs = [
            1 => trim((string) $this->request->getPost('revisi_1')),
            2 => trim((string) $this->request->getPost('revisi_2')),
            3 => trim((string) $this->request->getPost('revisi_3')),
        ];

        // fallback untuk kompatibilitas field revisi lama
        $legacyRevisi = trim((string) $this->request->getPost('revisi'));
        if ($legacyRevisi !== '' && implode('', $revisiInputs) === '') {
            $revisiInputs[1] = $legacyRevisi;
        }

        $latestRevisi = '';
        foreach ($revisiInputs as $nilaiRevisi) {
            if ($nilaiRevisi !== '') {
                $latestRevisi = $nilaiRevisi;
            }
        }
        $hasManualRevisi = implode('', $revisiInputs) !== '';
        $noSpk = trim((string) $this->request->getPost('no_spk'));
        $inheritedRevisi = [
            'latest' => '',
            'entries' => [],
        ];

        if (!$hasManualRevisi && $noSpk !== '') {
            $inheritedRevisi = $this->getLatestRevisiSnapshotByNoSpk($noSpk);
            if ($latestRevisi === '' && $inheritedRevisi['latest'] !== '') {
                $latestRevisi = $inheritedRevisi['latest'];
            }
        }

        $jenis = $this->request->getPost('jenis');
        if (!is_array($jenis)) {
            $jenis = [];
        }

        $jumlah = $this->request->getPost('jumlah');
        if (!is_array($jumlah)) {
            $jumlah = [];
        }

        $data = [
            // store selected jam intervals as comma-separated string
            'jam'         => implode(', ', $jamArray),
            'no_spk'      => $noSpk,
            'nama_mesin'  => $this->request->getPost('nama_mesin'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'grade'       => $this->request->getPost('grade'),
            'warna'       => $this->request->getPost('warna'),
            'nomor_mesin' => $this->request->getPost('nomor_mesin'),
            // compute shift automatically from jam
            'shif'        => $this->determineShift($jamArray),
            'operator'    => $this->request->getPost('operator'),
            'targett'     => $this->request->getPost('targett'),
            'revisi'      => $latestRevisi,
            'tanggal'     => $this->request->getPost('tanggal'),
        ];

        // Mapping reject dinamis ke kolom database
        if (!empty($jenis)) {
            for ($i = 0; $i < count($jenis); $i++) {
                if (!empty($jenis[$i])) {
                    $data[$jenis[$i]] = $jumlah[$i] ?? 0;
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

        $data['total_reject'] = $totalReject;

        // Ambil jam produksi dinamis (reuse normalized jamArray)
        $hasil = $this->request->getPost('hasil_produksi');

        if (!empty($jamArray)) {
            foreach ($jamArray as $i => $jamval) {
                if (!empty($jamval)) {
                    // Simpan kolom jam (misal: 06-07)
                    $data[$jamval] = $jamval;

                    // Ubah 06-07 jadi 06_07 untuk kolom hasil
                    $kolomHasil = 'hasil_produksi_' . str_replace('-', '_', $jamval);
                    $data[$kolomHasil] = $hasil[$i] ?? 0;
                }
            }
        }

        $this->MPpic->save($data);
        $idPpic = (int) $this->MPpic->getInsertID();
        if ($idPpic > 0) {
            if ($hasManualRevisi) {
                $this->saveInitialRevisi($idPpic, $revisiInputs);
            } elseif (!empty($inheritedRevisi['entries'])) {
                $this->copyRevisiToNewData($idPpic, $inheritedRevisi['entries']);
            }
        }

        return redirect()->to('/ppic')->with('success', 'Data berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->MPpic->delete($id);
        return redirect()->to('/ppic')->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $data = $this->MPpic->find($id);
        return view('edit', [
            'title' => 'Ubah Data Produksi',
            'data' => $data
        ]);
    }

    public function update($id = null)
    {
        $ppicModel  = new MPpic();
        $notifModel = new \App\Models\MNotifikasi();

        $id = $id ?? $this->request->getPost('id');
        $id = is_numeric($id) ? (int) $id : 0;
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID data tidak valid');
        }

        $old = $id ? $ppicModel->find($id) : null;

        $jam = $this->request->getPost('jam');
        if (is_array($jam)) {
            $jam = implode(', ', $jam);
        }

        $data = [
            'jam'         => $jam,
            'no_spk'      => $this->request->getPost('no_spk'),
            'nama_mesin'  => $this->request->getPost('nama_mesin'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'grade'       => $this->request->getPost('grade'),
            'warna'       => $this->request->getPost('warna'),
            'nomor_mesin' => $this->request->getPost('nomor_mesin'),
            'shif'        => $this->request->getPost('shif'),
            'operator'    => $this->request->getPost('operator'),
            'targett'     => $this->request->getPost('targett'),
            'revisi'      => $this->request->getPost('revisi'),
            'tanggal'     => $this->request->getPost('tanggal'),
        ];

        $ppicModel->update($id, $data);

        // kirim notifikasi jika revisi berubah (atau pertama kali diisi)
        $newRevisi = trim((string) ($data['revisi'] ?? ''));
        $oldRevisi = is_array($old) ? trim((string) ($old['revisi'] ?? '')) : '';
        if ($newRevisi !== '' && $newRevisi !== $oldRevisi) {
            $this->saveNextRevisi($id, $newRevisi);

            $from = $oldRevisi !== '' ? $oldRevisi : (is_array($old) ? (string) ($old['targett'] ?? $data['targett']) : (string) $data['targett']);
            $pesan = "Revisi target SPK {$data['no_spk']} dari {$from} menjadi {$newRevisi}";
            $now = date('Y-m-d H:i:s');

            $notifModel->insertBatch([
                [
                    'role_tujuan' => 'produksi',
                    'pesan'       => $pesan,
                    'status'      => 'unread',
                    'created_at'  => $now,
                ],
                [
                    'role_tujuan' => 'gudang',
                    'pesan'       => $pesan,
                    'status'      => 'unread',
                    'created_at'  => $now,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    private function saveInitialRevisi(int $idPpic, array $revisiInputs): void
    {
        if (!$this->isRevisiTableReady()) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $payload = [];

        foreach ($revisiInputs as $revisiKe => $nilai) {
            $nilai = trim((string) $nilai);
            if ($nilai === '') {
                continue;
            }

            $payload[] = [
                'id_produksi'    => $idPpic,
                'revisi_ke'      => (int) $revisiKe,
                'nilai_revisi'   => $nilai,
                'keterangan'     => null,
                'tanggal_revisi' => $now,
            ];
        }

        if (!empty($payload)) {
            $this->MRevisi->insertBatch($payload);
        }
    }

    private function saveNextRevisi(int $idPpic, string $newRevisi): void
    {
        if (!$this->isRevisiTableReady()) {
            return;
        }

        $newRevisi = trim($newRevisi);
        if ($newRevisi === '') {
            return;
        }

        $existing = $this->MRevisi
            ->where('id_produksi', $idPpic)
            ->orderBy('revisi_ke', 'ASC')
            ->findAll();

        $existingByKe = [];
        $maxKe = 0;

        foreach ($existing as $item) {
            $ke = (int) ($item['revisi_ke'] ?? 0);
            if ($ke < 1 || $ke > 3) {
                continue;
            }
            $existingByKe[$ke] = $item;
            if ($ke > $maxKe) {
                $maxKe = $ke;
            }
        }

        $targetKe = $maxKe > 0 ? min($maxKe + 1, 3) : 1;
        $now = date('Y-m-d H:i:s');

        if (isset($existingByKe[$targetKe])) {
            $this->MRevisi->update((int) $existingByKe[$targetKe]['id_revisi'], [
                'nilai_revisi'   => $newRevisi,
                'tanggal_revisi' => $now,
            ]);
            return;
        }

        $this->MRevisi->insert([
            'id_produksi'    => $idPpic,
            'revisi_ke'      => $targetKe,
            'nilai_revisi'   => $newRevisi,
            'keterangan'     => null,
            'tanggal_revisi' => $now,
        ]);
    }

    private function getLatestRevisiSnapshotByNoSpk(string $noSpk): array
    {
        $noSpk = trim($noSpk);
        if ($noSpk === '') {
            return [
                'latest' => '',
                'entries' => [],
            ];
        }

        $source = $this->MPpic
            ->where('no_spk', $noSpk)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();

        if (!is_array($source)) {
            return [
                'latest' => '',
                'entries' => [],
            ];
        }

        $entries = [];
        $latest = trim((string) ($source['revisi'] ?? ''));

        if ($this->isRevisiTableReady()) {
            $entries = $this->MRevisi
                ->where('id_produksi', (int) ($source['id'] ?? 0))
                ->orderBy('revisi_ke', 'ASC')
                ->orderBy('tanggal_revisi', 'ASC')
                ->findAll();

            $entries = array_values(array_filter($entries, static function ($item) {
                $ke = (int) ($item['revisi_ke'] ?? 0);
                $nilai = trim((string) ($item['nilai_revisi'] ?? ''));

                return $ke >= 1 && $ke <= 3 && $nilai !== '';
            }));

            if (!empty($entries)) {
                $lastEntry = end($entries);
                $latest = trim((string) ($lastEntry['nilai_revisi'] ?? $latest));
            }
        }

        if (empty($entries) && $latest !== '') {
            $entries[] = [
                'revisi_ke' => 1,
                'nilai_revisi' => $latest,
                'keterangan' => null,
                'tanggal_revisi' => date('Y-m-d H:i:s'),
            ];
        }

        return [
            'latest' => $latest,
            'entries' => $entries,
        ];
    }

    private function copyRevisiToNewData(int $idPpic, array $entries): void
    {
        if (!$this->isRevisiTableReady() || $idPpic <= 0 || empty($entries)) {
            return;
        }

        $payload = [];

        foreach ($entries as $entry) {
            $revisiKe = (int) ($entry['revisi_ke'] ?? 0);
            $nilai = trim((string) ($entry['nilai_revisi'] ?? ''));

            if ($revisiKe < 1 || $revisiKe > 3 || $nilai === '') {
                continue;
            }

            $payload[] = [
                'id_produksi' => $idPpic,
                'revisi_ke' => $revisiKe,
                'nilai_revisi' => $nilai,
                'keterangan' => $entry['keterangan'] ?? null,
                'tanggal_revisi' => $entry['tanggal_revisi'] ?? date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($payload)) {
            $this->MRevisi->insertBatch($payload);
        }
    }

    private function attachRevisiData(array $rows): array
    {
        if (empty($rows) || !$this->isRevisiTableReady()) {
            return $rows;
        }

        $ids = array_values(array_unique(array_filter(array_map(static function ($row) {
            return $row['id'] ?? null;
        }, $rows))));

        if (empty($ids)) {
            return $rows;
        }

        $revisiRows = $this->MRevisi
            ->whereIn('id_produksi', $ids)
            ->orderBy('id_produksi', 'ASC')
            ->orderBy('revisi_ke', 'ASC')
            ->orderBy('tanggal_revisi', 'ASC')
            ->findAll();

        $revisiMap = [];
        foreach ($revisiRows as $revisi) {
            $idPpic = (int) ($revisi['id_produksi'] ?? 0);
            $revisiKe = (int) ($revisi['revisi_ke'] ?? 0);
            $nilai = trim((string) ($revisi['nilai_revisi'] ?? ''));

            if ($idPpic <= 0 || $revisiKe < 1 || $revisiKe > 3 || $nilai === '') {
                continue;
            }

            $revisiMap[$idPpic][$revisiKe] = $revisi;
        }

        foreach ($rows as &$row) {
            $idPpic = (int) ($row['id'] ?? 0);
            $riwayat = $revisiMap[$idPpic] ?? [];

            if (empty($riwayat)) {
                $row['revisi_display'] = trim((string) ($row['revisi'] ?? ''));
                continue;
            }

            ksort($riwayat);
            $parts = [];
            $lastNilai = '';

            foreach ($riwayat as $ke => $detail) {
                $nilai = trim((string) ($detail['nilai_revisi'] ?? ''));
                if ($nilai === '') {
                    continue;
                }

                $lastNilai = $nilai;
                $tanggalRevisi = $detail['tanggal_revisi'] ?? '';
                $timeLabel = '';
                if (!empty($tanggalRevisi) && strtotime($tanggalRevisi) !== false) {
                    $timeLabel = ' (' . date('H:i', strtotime($tanggalRevisi)) . ')';
                }

                // Sertakan jam revisi secara eksplisit di tampilan (menu PPIC)
                $parts[] = 'R' . $ke . ': ' . $nilai . $timeLabel;
            }

            if ($lastNilai !== '') {
                $row['revisi'] = $lastNilai;
            }

            // nilai untuk kalkulasi jumlah (target/revisi dikurangi hasil produksi)
            $nilaiRevisi = trim((string) ($row['revisi'] ?? $row['targett'] ?? ''));
            $row['nilai'] = is_numeric($nilaiRevisi) ? (int) $nilaiRevisi : (int) preg_replace('/[^\d-]/', '', $nilaiRevisi);

            $row['revisi_display'] = implode(' | ', $parts);
        }
        unset($row);

        return $rows;
    }

    private function isRevisiTableReady(): bool
    {
        if ($this->hasRevisiTable !== null) {
            return $this->hasRevisiTable;
        }

        try {
            $this->hasRevisiTable = db_connect()->tableExists('revisi_produksi');
        } catch (\Throwable $e) {
            $this->hasRevisiTable = false;
        }

        return $this->hasRevisiTable;
    }

    private function determineShift($jamArray)
    {
        if (empty($jamArray)) {
            return null;
        }

        if (!is_array($jamArray)) {
            $jamArray = [$jamArray];
        }

        $minStart = null;
        foreach ($jamArray as $interval) {
            if (trim($interval) === '') {
                continue;
            }
            $parts = explode('-', $interval);
            $start = (int) $parts[0];
            if ($minStart === null || $start < $minStart) {
                $minStart = $start;
            }
        }

        if ($minStart === null) {
            return null;
        }

        if ($minStart >= 7 && $minStart < 14) {
            return '1';
        }
        if ($minStart >= 14 && $minStart < 22) {
            return '2';
        }

        return '3';
    }

    private function getSpkMasterData(): array
    {
        $rows = $this->MPpic
            ->select('no_spk, nama_mesin, nama_produk, grade, warna, nomor_mesin, operator, targett, tanggal, id')
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        if (empty($rows)) {
            return [];
        }

        $master = [];

        foreach ($rows as $row) {
            $noSpk = trim((string) ($row['no_spk'] ?? ''));
            if ($noSpk === '' || isset($master[$noSpk])) {
                continue;
            }

            $master[$noSpk] = [
                'no_spk' => $noSpk,
                'nama_mesin' => trim((string) ($row['nama_mesin'] ?? '')),
                'nama_produk' => trim((string) ($row['nama_produk'] ?? '')),
                'grade' => trim((string) ($row['grade'] ?? '')),
                'warna' => trim((string) ($row['warna'] ?? '')),
                'nomor_mesin' => trim((string) ($row['nomor_mesin'] ?? '')),
                'operator' => trim((string) ($row['operator'] ?? '')),
                'targett' => trim((string) ($row['targett'] ?? '')),
            ];
        }

        ksort($master, SORT_NATURAL | SORT_FLAG_CASE);

        return array_values($master);
    }

    public function exportExcel()
    {
        $data = $this->MPpic->findAll();

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
                <th>Grade</th>
                <th>Warna</th>
                <th>Nomor Mesin</th>
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
                    <td>" . $d['grade'] . "</td>
                    <td>" . $d['warna'] . "</td>
                    <td>" . $d['nomor_mesin'] . "</td>
                    <td>" . $d['batch_number'] . "</td>
                    <td>" . $d['shift'] . "</td>
                    <td>" . $d['nomor_mesin'] . "</td>
                    <td>" . $d['packing_isi'] . "</td>
                    <td>" . $d['cycle_time'] . "</td>
                    <td>" . $d['targett'] . "</td>
                    <td>" . $d['operator'] . "</td>
                </tr>";
        }

        echo "</table>";
        exit;
    }
}
