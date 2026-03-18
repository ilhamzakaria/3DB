<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MPpic;

class Export extends BaseController
{
    protected $produkModel;

    public function __construct()
    {
        $this->produkModel = new MPpic();
    }

    // =========================
    // DASHBOARD EXPORT
    // =========================
    public function dashboard()
    {
        $data = $this->produkModel->findAll();

        return $this->exportExcel(
            'data_dashboard.xls',
            ['No SPK', 'Nama Mesin', 'Nama Produk', 'operator', 'target', 'revisi'],
            $data,
            function ($d) {
                return [
                    $d['no_spk'] ?? '',
                    $d['nama_mesin'] ?? '',
                    $d['nama_produk'] ?? '',
                    $d['operator'] ?? '',
                    $d['targett'] ?? '',
                    $d['revisi'] ?? '',

                ];
            }
        );
    }

    // =========================
    // HOME EXPORT (contoh)
    // =========================
    public function home()
    {
        $data = $this->produkModel->findAll(); // ganti model kalau beda tabel

        return $this->exportExcel(
            'data_home.xls',
            ['ID', 'Judul', 'Deskripsi'],
            $data,
            function ($d) {
                return [
                    $d['nama_mesin'] ?? '',
                    $d['nomor_mesin'] ?? '',
                    $d['tanggal'] ?? '',
                    $d['nama_produk'] ?? '',
                    $d['packing'] ?? '',
                    $d['isi'] ?? '',
                    $d['no_spk'] ?? '',
                    $d['batch_number'] ?? '',
                    $d['shift'] ?? '',
                    $d['cycle_time'] ?? '',
                    $d['target'] ?? '',
                    $d['operator'] ?? '',
                ];
            }
        );
    }


    public function kontak()
    {
        $data = $this->produkModel->findAll(); // ganti model kalau beda tabel

        return $this->exportExcel(
            'data_kontak.xls',
            ['Nama', 'Email', 'Pesan'],
            $data,
            function ($d) {
                return [
                    $d['nama_mesin'] ?? '',
                    $d['nomor_mesin'] ?? '',
                    $d['tanggal'] ?? '',
                    $d['nama_produk'] ?? '',
                    $d['packing'] ?? '',
                    $d['isi'] ?? '',
                    $d['no_spk'] ?? '',
                    $d['batch_number'] ?? '',
                    $d['shift'] ?? '',
                    $d['cycle_time'] ?? '',
                    $d['target'] ?? '',
                    $d['operator'] ?? '',
                ];
            }
        );
    }

    // =========================
    // CORE EXPORT FUNCTION
    // =========================
    private function exportExcel(string $filename, array $headers, array $data, callable $mapper)
    {
        $html = "<table border='1' cellpadding='5' cellspacing='0'>";
        $html .= "<thead><tr>";

        foreach ($headers as $header) {
            $html .= "<th style='background:#f2f2f2;font-weight:bold;'>$header</th>";
        }

        $html .= "</tr></thead><tbody>";

        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($mapper($row) as $value) {
                $html .= "<td>" . htmlspecialchars((string)$value) . "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</tbody></table>";

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel')
            ->setHeader('Content-Disposition', "attachment; filename=\"$filename\"")
            ->setBody($html);
    }
}
