<?php

namespace App\Controllers;

use App\Models\MPpicMesinBecum;
use App\Models\MPpicMesinPowerjet1;
use App\Models\MPpicMesinPowerjet2;
use App\Models\MPpicMesinCcm1;
use App\Models\MPpicMesinCcm2;
use App\Models\MPpicMesinIps1;
use App\Models\MPpicMesinIps2;
use App\Models\MPpicMesinIps3;
use App\Models\MPpicMesinIps4;

/**
 * Export Excel untuk LOG MESIN PPIC.
 * Semua data diambil dari database oroppic (bukan orogudang).
 *
 * Route: GET /ppic/export/mesin_{slug}
 * Contoh: /ppic/export/mesin_becum
 */
class PpicExport extends BaseController
{
    // ----------------------------------------------------------------
    // Kolom target+realisasi yang sama di semua mesin baru
    // ----------------------------------------------------------------
    private const COMMON_HEADERS = [
        'Tanggal', 'No. SPK', 'No Mesin', 'Shift', 'Jam',
        'Waktu Awal Target', 'Waktu Akhir Target',
        'Target Shift 1 (pcs)', 'Target Shift 2 (pcs)', 'Target Shift 3 (pcs)',
        'Waktu Awal Realisasi', 'Waktu Akhir Realisasi',
        'Realisasi Shift 1 (pcs)', 'Realisasi Shift 2 (pcs)', 'Realisasi Shift 3 (pcs)',
        'Total Hasil Produksi (pcs)',
        'Berat (gr)', 'Komposisi', 'Total SPK', 'Packing', 'Keterangan',
    ];

    private const COMMON_ROW = [
        'tanggal', 'no_spk', 'nomor_mesin', 'shif', 'jam',
        'jam', 'waktu_akhir_target',
        'target_shift1', 'target_shift2', 'target_shift3',
        'waktu_awal_realisasi', 'waktu_akhir_realisasi',
        'realisasi_shift1', 'realisasi_shift2', 'realisasi_shift3',
        'total_hasil_produksi',
        'berat', 'revisi', 'total_spk', 'packing', 'keterangan',
    ];

    // ----------------------------------------------------------------
    // Entry point — dipanggil via route /ppic/export/mesin_{slug}
    // ----------------------------------------------------------------
    public function mesin(string $slug): \CodeIgniter\HTTP\ResponseInterface
    {
        $validSlugs = [
            'becum', 'powerjet1', 'powerjet2',
            'ccm1', 'ccm2',
            'ips1', 'ips2', 'ips3', 'ips4',
        ];

        if (!in_array($slug, $validSlugs, true)) {
            return $this->response->setStatusCode(404)->setBody('Mesin tidak ditemukan');
        }

        $model = $this->resolveModel($slug);

        // Filter dari query string (sama dengan tampilan index)
        $q         = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $query = $model;
        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->orLike($this->produkField($slug), $q)
                ->groupEnd();
        }
        if (!empty($startDate)) $query = $query->where('tanggal >=', $startDate);
        if (!empty($endDate))   $query = $query->where('tanggal <=', $endDate);

        $data = $query->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll();

        $produkField  = $this->produkField($slug);
        $produkLabel  = $this->produkLabel($slug);
        $machineName  = $this->machineName($slug);
        $extraHeaders = $this->extraHeaders($slug);

        // Header baris Excel: produk dulu, lalu semua kolom umum
        $headers = array_merge(
            [$produkLabel, 'Brand', 'Grade', 'Warna'],
            $extraHeaders,
            self::COMMON_HEADERS
        );

        $rows = [];
        foreach ($data as $d) {
            // Calculate total hasil produksi (sum of all shifts + extra shifts if any)
            $totalHasil = (int) ($d['realisasi_shift1'] ?? 0) +
                         (int) ($d['realisasi_shift2'] ?? 0) +
                         (int) ($d['realisasi_shift3'] ?? 0);

            // Handle extra_realisasi_shifts if it exists (JSON field)
            if (!empty($d['extra_realisasi_shifts'])) {
                try {
                    $extra = json_decode($d['extra_realisasi_shifts'], true);
                    if (is_array($extra)) {
                        foreach ($extra as $val) {
                            $totalHasil += (int) $val;
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore JSON decode errors
                }
            }

            $row = [
                $d[$produkField] ?? '',
                $d['brand']      ?? '',
                $d['grade']      ?? '',
                $d['warna']      ?? '',
            ];

            // Extra per-mesin (misal: stock, nomor_lot untuk becum)
            foreach ($this->extraFields($slug) as $f) {
                $row[] = $d[$f] ?? '';
            }

            // Kolom umum
            $commonRowValues = [];
            foreach (self::COMMON_ROW as $f) {
                if ($f === 'total_hasil_produksi') {
                    $commonRowValues[] = $totalHasil > 0 ? $totalHasil : '-';
                } else {
                    $commonRowValues[] = $d[$f] ?? '';
                }
            }

            $row = array_merge($row, $commonRowValues);
            $rows[] = $row;
        }

        $filename = 'mesin_' . $slug . '_ppic_' . date('Y-m-d-His') . '.xls';
        return $this->buildExcel($filename, $headers, $rows, $machineName);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function resolveModel(string $slug)
    {
        return match ($slug) {
            'becum'     => new MPpicMesinBecum(),
            'powerjet1' => new MPpicMesinPowerjet1(),
            'powerjet2' => new MPpicMesinPowerjet2(),
            'ccm1'      => new MPpicMesinCcm1(),
            'ccm2'      => new MPpicMesinCcm2(),
            'ips1'      => new MPpicMesinIps1(),
            'ips2'      => new MPpicMesinIps2(),
            'ips3'      => new MPpicMesinIps3(),
            default     => new MPpicMesinIps4(),
        };
    }

    /** Nama kolom produk utama setiap mesin */
    private function produkField(string $slug): string
    {
        return match (true) {
            $slug === 'becum'                   => 'produk_galon',
            in_array($slug, ['ips1','ips2','ips3','ips4'], true) => 'produk_preform',
            default                             => 'produk_cap_galon',
        };
    }

    /** Label header kolom produk */
    private function produkLabel(string $slug): string
    {
        return match (true) {
            $slug === 'becum'                   => 'Produk Galon',
            in_array($slug, ['ips1','ips2','ips3','ips4'], true) => 'Produk Preform',
            default                             => 'Produk Cap Galon',
        };
    }

    /** Nama mesin untuk judul sheet */
    private function machineName(string $slug): string
    {
        return match ($slug) {
            'becum'     => 'Becum',
            'powerjet1' => 'Powerjet 1',
            'powerjet2' => 'Powerjet 2',
            'ccm1'      => 'CCM 1',
            'ccm2'      => 'CCM 2',
            'ips1'      => 'IPS 1',
            'ips2'      => 'IPS 2',
            'ips3'      => 'IPS 3',
            default     => 'IPS 4',
        };
    }

    /** Header kolom khusus per mesin (di luar kolom umum) */
    private function extraHeaders(string $slug): array
    {
        // Becum menyimpan stock & nomor_lot dari proses lama
        return [];
    }

    /** Field kolom khusus per mesin */
    private function extraFields(string $slug): array
    {
        return [];
    }

    // ----------------------------------------------------------------
    // Build Excel HTML
    // ----------------------------------------------------------------

    private function buildExcel(string $filename, array $headers, array $rows, string $machineName): \CodeIgniter\HTTP\ResponseInterface
    {
        $title = 'LOG MESIN ' . strtoupper($machineName) . ' – PPIC';
        $exportDate = date('d-m-Y H:i');

        $html  = "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>";
        $html .= "<h2>$title</h2><p>Tanggal Export: $exportDate</p>";
        $html .= "<table border='1' cellpadding='5' cellspacing='0'>";
        $html .= "<thead><tr>";
        foreach ($headers as $h) {
            $html .= "<th style='background:#1a56db;color:#fff;font-weight:bold;white-space:nowrap;'>" . htmlspecialchars($h) . "</th>";
        }
        $html .= "</tr></thead><tbody>";

        foreach ($rows as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                $html .= "<td>" . htmlspecialchars((string) $cell) . "</td>";
            }
            $html .= "</tr>";
        }

        if (empty($rows)) {
            $html .= "<tr><td colspan='" . count($headers) . "' style='text-align:center;'>Tidak ada data</td></tr>";
        }

        $html .= "</tbody></table></body></html>";

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setBody($html);
    }
}
