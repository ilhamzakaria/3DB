<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MBahanBaku;
use App\Models\MPackaging;
use App\Models\MMasterbatch;
use App\Models\MGalonReject;
use App\Models\MGilinganGalon;
use App\Models\MStikerGudang;
use App\Models\MRejectProduksi;
use App\Models\MMesinBecum;
use App\Models\MMesinPowerjet1;
use App\Models\MMesinPowerjet2;
use App\Models\MMesinCcm1;
use App\Models\MMesinCcm2;
use App\Models\MMesinIps1;
use App\Models\MMesinIps2;
use App\Models\MMesinIps3;
use App\Models\MMesinIps4;
use App\Models\MPpic;

class Export extends BaseController
{
    protected $produkModel;
    protected $bahanBakuModel;
    protected $packagingModel;
    protected $masterbatchModel;
    protected $galonRejectModel;

    public function __construct()
    {
        $this->produkModel = new MPpic();
        $this->bahanBakuModel = new MBahanBaku();
        $this->packagingModel = new MPackaging();
        $this->masterbatchModel = new MMasterbatch();
        $this->galonRejectModel = new MGalonReject();
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
        $data = $this->produkModel->findAll();

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
        $data = $this->produkModel->findAll();

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

    public function bahanBaku()
    {
        $data = $this->bahanBakuModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->exportExcel(
            'data_bahan_baku.xls',
            ['Nama Bahan', 'Status', 'Kode', 'Jumlah', 'Nomor Lot', 'No SPK', 'Dibuat', 'Diupdate'],
            $data,
            function ($d) {
                return [
                    $d['nama_bahan'] ?? '',
                    $d['status'] ?? '',
                    $d['kode'] ?? '',
                    $d['jumlah'] ?? '',
                    $d['nomor_lot'] ?? '',
                    $d['no_spk'] ?? '',
                    $d['created_at'] ?? '',
                    $d['updated_at'] ?? '',
                ];
            }
        );
    }

    public function packaging()
    {
        $data = $this->packagingModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->exportExcel(
            'data_packaging.xls',
            ['Produk', 'Item Packaging', 'Jenis', 'Status', 'Kode', 'Jumlah', 'Nomor Lot', 'No SPK', 'Shift', 'Dibuat', 'Diupdate'],
            $data,
            function ($d) {
                return [
                    $d['produk'] ?? '',
                    $d['nama_packaging'] ?? '',
                    $d['jenis_packaging'] ?? '',
                    $d['status'] ?? '',
                    $d['kode'] ?? '',
                    $d['jumlah'] ?? '',
                    $d['nomor_lot'] ?? '',
                    $d['no_spk'] ?? '',
                    $d['shif'] ?? '',
                    $d['created_at'] ?? '',
                    $d['updated_at'] ?? '',
                ];
            }
        );
    }

    public function masterbatch()
    {
        $data = $this->masterbatchModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->exportExcel(
            'data_masterbatch.xls',
            ['Produk', 'Item Masterbatch', 'Status', 'Kode', 'Jumlah', 'Nomor Lot', 'No SPK', 'Shift', 'Dibuat', 'Diupdate'],
            $data,
            function ($d) {
                return [
                    $d['produk'] ?? '',
                    $d['nama_masterbatch'] ?? '',
                    $d['status'] ?? '',
                    $d['kode'] ?? '',
                    $d['jumlah'] ?? '',
                    $d['nomor_lot'] ?? '',
                    $d['no_spk'] ?? '',
                    $d['shif'] ?? '',
                    $d['created_at'] ?? '',
                    $d['updated_at'] ?? '',
                ];
            }
        );
    }

    public function galonReject()
    {
        $data = $this->galonRejectModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->exportExcel(
            'data_galon_reject.xls',
            ['Produk', 'Item Galon Reject', 'Sumber', 'Status', 'Kode', 'Jumlah', 'Nomor Lot', 'No SPK', 'Shift', 'Dibuat', 'Diupdate'],
            $data,
            function ($d) {
                return [
                    $d['produk'] ?? '',
                    $d['nama_galon_reject'] ?? '',
                    $d['sumber_reject'] ?? '',
                    $d['status'] ?? '',
                    $d['kode'] ?? '',
                    $d['jumlah'] ?? '',
                    $d['nomor_lot'] ?? '',
                    $d['no_spk'] ?? '',
                    $d['shif'] ?? '',
                    $d['created_at'] ?? '',
                    $d['updated_at'] ?? '',
                ];
            }
        );
    }

    public function gilinganGalon()
    {
        $model = new MGilinganGalon();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_gilingan_galon.xls',
            ['Tanggal', 'Produk', 'Item', 'Sumber', 'Status', 'Kode', 'Jumlah', 'Lot', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk'], $d['nama_gilingan'], $d['sumber_gilingan'], $d['status'], $d['kode'], $d['jumlah'], $d['nomor_lot'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function stiker()
    {
        $model = new MStikerGudang();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_stiker.xls',
            ['Tanggal', 'Produk', 'Item', 'Status', 'Kode', 'Jumlah', 'Lot', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk'], $d['nama_stiker'], $d['status'], $d['kode'], $d['jumlah'], $d['nomor_lot'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function rejectProduksi()
    {
        $model = new MRejectProduksi();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_reject_produksi.xls',
            ['Tanggal', 'Produk', 'Item', 'Jenis', 'Status', 'Kode', 'Jumlah', 'Lot', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk'], $d['nama_limbah'], $d['jenis_limbah'], $d['status'], $d['kode'], $d['jumlah'], $d['nomor_lot'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinBecum()
    {
        $model = new MMesinBecum();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_becum.xls',
            ['Tanggal', 'Produk Galon', 'Brand', 'Grade', 'Berat', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_galon'], $d['brand'], $d['grade'], $d['berat'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinPowerjet1()
    {
        $model = new MMesinPowerjet1();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_powerjet1.xls',
            ['Tanggal', 'Produk Cap Galon', 'Brand', 'Warna', 'Packaging', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_cap_galon'], $d['brand'], $d['warna'], $d['packaging'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinPowerjet2()
    {
        $model = new MMesinPowerjet2();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_powerjet2.xls',
            ['Tanggal', 'Produk Cap Galon', 'Brand', 'Warna', 'Packaging', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_cap_galon'], $d['brand'], $d['warna'], $d['packaging'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinCcm1()
    {
        $model = new MMesinCcm1();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_ccm1.xls',
            ['Tanggal', 'Produk Cap Galon', 'Brand', 'Warna', 'Packaging', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_cap_galon'], $d['brand'], $d['warna'], $d['packaging'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinCcm2()
    {
        $model = new MMesinCcm2();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_ccm2.xls',
            ['Tanggal', 'Produk Cap Galon', 'Brand', 'Warna', 'Packaging', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_cap_galon'], $d['brand'], $d['warna'], $d['packaging'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinIps1()
    {
        $model = new MMesinIps1();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_ips1.xls',
            ['Tanggal', 'Produk Preform', 'Brand', 'Warna', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_preform'], $d['brand'], $d['warna'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinIps2()
    {
        $model = new MMesinIps2();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_ips2.xls',
            ['Tanggal', 'Produk Preform', 'Brand', 'Warna', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_preform'], $d['brand'], $d['warna'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinIps3()
    {
        $model = new MMesinIps3();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_ips3.xls',
            ['Tanggal', 'Produk Preform', 'Brand', 'Warna', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_preform'], $d['brand'], $d['warna'], $d['stock'], $d['no_spk'], $d['shif']];
            }
        );
    }

    public function mesinIps4()
    {
        $model = new MMesinIps4();
        $data = $model->orderBy('tanggal', 'DESC')->findAll();
        return $this->exportExcel(
            'data_mesin_ips4.xls',
            ['Tanggal', 'Produk Preform', 'Brand', 'Warna', 'Stock', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_preform'], $d['brand'], $d['warna'], $d['stock'], $d['no_spk'], $d['shif']];
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
