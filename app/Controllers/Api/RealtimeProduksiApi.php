<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Controllers\Produksi;

/**
 * API endpoint for Realtime Production Monitor
 * GET /api/realtime/produksi-live
 */
class RealtimeProduksiApi extends BaseController
{
    public function live()
    {
        try {
            $db    = $this->dbProduksi();
            $today = date('Y-m-d');

            $mesinLabels = Produksi::mesinLabels();
            $result      = [];

            foreach ($mesinLabels as $slug => $label) {
                // nama_mesin stored exactly as the label (e.g. "Becum", "Powerjet 1")
                $row = $db->table('produksi_headers')
                    ->where('tanggal', $today)
                    ->where('deleted_at', null)
                    ->like('nama_mesin', $label, 'none')
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getFirstRow('array');

                if ($row) {
                    // Get hourly production data from produksi_jam table
                    // Group by rentang_jam to get unique hours, take MAX hasil_produksi if multiple entries
                    $jamData = $db->table('produksi_jam')
                        ->select('rentang_jam, MAX(hasil_produksi) as hasil_produksi')
                        ->where('produksi_header_id', $row['id'])
                        ->groupBy('rentang_jam')
                        ->orderBy('rentang_jam', 'ASC')
                        ->get()
                        ->getResultArray();

                    $jams = [];
                    foreach ($jamData as $j) {
                        $jams[] = [
                            'rentang_jam' => $j['rentang_jam'] ?? '-',
                            'hasil'       => (int)($j['hasil_produksi'] ?? 0)
                        ];
                    }

                    // Check for active downtime (waktu_selesai IS NULL)
                    $hasActiveDowntime = (bool)$db->table('produksi_downtimes')
                        ->where('produksi_header_id', $row['id'])
                        ->where('waktu_selesai', null)
                        ->where('deleted_at', null)
                        ->countAllResults() > 0;

                    // Get latest downtime alasan for display
                    $latestDowntime = null;
                    if ($hasActiveDowntime) {
                        $latestDowntime = $db->table('produksi_downtimes')
                            ->where('produksi_header_id', $row['id'])
                            ->where('waktu_selesai', null)
                            ->where('deleted_at', null)
                            ->select('alasan_downtime, problem, action_plant, waktu_mulai')
                            ->orderBy('id', 'DESC')
                            ->limit(1)
                            ->get()
                            ->getFirstRow('array');
                    }

                    // Determine status: problem (if downtime active) or running
                    $status = $hasActiveDowntime ? 'problem' : 'running';

                    $result[] = [
                        'slug'               => $slug,
                        'label'              => $label,
                        'status'             => $status,
                        'nomor_spk'          => $row['no_spk']           ?? '-',
                        'nama_produk'        => $row['nama_produk']       ?? '-',
                        'brand'              => $row['brand']             ?? '-',
                        'grade'              => $row['grade']             ?? '-',
                        'warna'              => $row['warna']             ?? '-',
                        'operator'           => $row['operator']          ?? '-',
                        'grup'               => $row['grup']               ?? '-',
                        'shift'              => $row['shift']             ?? '-',
                        'grand_total_bagus'  => (int)($row['grand_total_bagus']  ?? 0),
                        'grand_total_reject' => (int)($row['grand_total_reject'] ?? 0),
                        'sisa_po'            => (int)($row['sisa_po']    ?? 0),
                        'total_spk'          => (int)($row['total_spk']  ?? 0),
                        'target'             => (int)($row['target']     ?? 0),
                        'selesai_spk'        => (int)($row['selesai_spk'] ?? 0),
                        'tanggal'            => $row['tanggal']           ?? $today,
                        'jams'               => $jams,
                        'downtime_active'    => $hasActiveDowntime,
                        'alasan_downtime'    => $latestDowntime['alasan_downtime'] ?? null,
                        'problem'            => $latestDowntime['problem'] ?? null,
                        'action_plant'       => $latestDowntime['action_plant'] ?? null,
                        'waktu_mulai'        => $latestDowntime['waktu_mulai'] ?? null,
                    ];
                } else {
                    $result[] = [
                        'slug'               => $slug,
                        'label'              => $label,
                        'status'             => 'idle',
                        'nomor_spk'          => '-',
                        'nama_produk'        => '-',
                        'brand'              => '-',
                        'grade'              => '-',
                        'warna'              => '-',
                        'operator'           => '-',
                        'grup'               => '-',
                        'shift'              => '-',
                        'grand_total_bagus'  => 0,
                        'grand_total_reject' => 0,
                        'sisa_po'            => 0,
                        'total_spk'          => 0,
                        'target'             => 0,
                        'selesai_spk'        => 0,
                        'tanggal'            => $today,
                        'jams'               => [],
                        'downtime_active'    => false,
                        'alasan_downtime'    => null,
                        'problem'            => null,
                        'action_plant'       => null,
                        'waktu_mulai'        => null,
                    ];
                }
            }

            return $this->response
                ->setContentType('application/json')
                ->setJSON(['success' => true, 'data' => $result, 'updated_at' => date('H:i:s')]);
        } catch (\Throwable $e) {
            log_message('error', '[RealtimeProduksiApi] ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setJSON(['success' => false, 'message' => 'Gagal mengambil data']);
        }
    }
}
