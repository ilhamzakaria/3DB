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
        $data = $this->produkModel->orderBy('tanggal', 'DESC')->orderBy('shif', 'ASC')->orderBy('jam', 'ASC')->findAll();

        return $this->exportExcel(
            'Laporan_PPIC_Harian.xls',
            ['jam', 'nomor spk', 'nama mesin', 'Nama Produk', 'Grade', 'Warna', 'Nomor Mesin', 'Shif', 'Operator', 'Target', 'Revisi', 'Tanggal', 'Target Awal', 'Target Akhir', 'Realisasi Awal', 'Realisasi Akhir', 'Created At', 'Updated At', 'Deleted At'],
            $data,
            function ($d) {
                return [
                    $d['jam'] ?? '',
                    $d['no_spk'] ?? '',
                    ($d['nama_mesin'] ?? '') . ' (No Mesin' . ($d['nomor_mesin'] ?? '') . ')',
                    $d['nama_produk'] ?? '',
                    $d['grade'] ?? '',
                    $d['warna'] ?? '',
                    $d['nomor_mesin'] ?? '',
                    $d['shif'] ?? '',
                    $d['operator'] ?? '',
                    $d['targett'] ?? '',
                    $d['revisi'] ?? '',
                    $d['tanggal'] ?? '',
                    $d['target_awal'] ?? '',
                    $d['target_akhir'] ?? '',
                    $d['realisasi_awal'] ?? '',
                    $d['realisasi_akhir'] ?? '',
                    $d['created_at'] ?? '',
                    $d['updated_at'] ?? '',
                    $d['deleted_at'] ?? '',
                ];
            }
        );
    }

    public function produksi()
    {
        $model = new \App\Models\MProduksiHeader();

        $q       = $this->request->getGet('q');
        $shift   = $this->request->getGet('shift');
        $tanggal = $this->request->getGet('tanggal');
        $filter  = $this->request->getGet('filter');
        $mesin   = $this->request->getGet('mesin');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Map slug → display label stored in nama_mesin column
        $mesinLabels = \App\Controllers\Produksi::mesinLabels();
        $mesinLabel  = ($mesin && isset($mesinLabels[$mesin])) ? $mesinLabels[$mesin] : null;

        $query = $model;

        // Filter by mesin (nama_mesin column stores the display label)
        if ($mesinLabel !== null) {
            $query = $query->where('nama_mesin', $mesinLabel);
        }

        // Time-based filtering
        if ($filter === 'day') {
            $query->where('tanggal', date('Y-m-d'));
        } elseif ($filter === 'week') {
            $query->where('tanggal >=', date('Y-m-d', strtotime('-7 days')));
        } elseif ($filter === 'month') {
            $query->where('tanggal >=', date('Y-m-d', strtotime('-30 days')));
        }

        // Date range filtering
        if (!empty($startDate) && !empty($endDate)) {
            if ($endDate < $startDate) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }
            $query->where('tanggal >=', $startDate);
            $query->where('tanggal <=', $endDate);
        }

        if ($q) {
            $query = $query->groupStart()
                ->like('no_spk', $q)
                ->orLike('nama_mesin', $q)
                ->orLike('nama_produk', $q)
                ->orLike('operator', $q)
                ->groupEnd();
        }

        if ($shift) {
            $query = $query->where('shift', $shift);
        }

        if ($tanggal) {
            $query = $query->where('tanggal', $tanggal);
        }

        $headersData = $query->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        $headers = [
            'Tanggal',
            'Shift',
            'Grup',
            'No. SPK',
            'Nama Mesin',
            'No Mesin',
            'Nama Produk',
            'Batch Number',
            'Packing',
            'Cycle Time',
            'Target/shift',
            'isi',
            'Operator',
            // tabel jam produksi
            'Rentang Jam',
            'Hasil Produksi',
            'Total Bagus',
            'Sisa PO',
            'Hold',
            'Gumpalan',
            'Total Reject',
            // produksi downtime
            'Alasan Downtime',
            'Durasi (Menit)',
            'Total Downtime',
            // produksi material
            'Merek/Kode Material',
            'Pemakaian Material',
            'Lot A',
            'Lot B',
            'Lot C',
            'Lot D',
            // produksi colorant
            'Kode Colorant',
            'Nomor Lot Colorant',
            'Pemakaian Colorant',
            // produksi packaging
            'Box',
            'Ukuran Box',
            'Plastik',
            'Ukuran Plastik',
            'Karung',
            'Ukuran Karung',
            'Catatan'
        ];

        if (empty($headersData)) {
            $filename = 'produksi' . ($mesinLabel ? '-' . strtolower(str_replace(' ', '_', $mesinLabel)) : '') . '-' . date('Y-m-d-His');
            return $this->exportAsExcel($filename, $headers, []);
        }

        $headerIds = array_column($headersData, 'id');

        $jamModel = new \App\Models\MProduksiJam();
        $downtimeModel = new \App\Models\MProduksiDowntime();
        $materialModel = new \App\Models\MProduksiMaterial();
        $colorantModel = new \App\Models\MProduksiColorant();
        $packagingModel = new \App\Models\MProduksiPackaging();

        // Ambil data detail, CodeIgniter model findAll() otomatis menyaring deleted_at IS NULL
        $jams = $jamModel->whereIn('produksi_header_id', $headerIds)->orderBy('id', 'ASC')->findAll();
        $downtimes = $downtimeModel->whereIn('produksi_header_id', $headerIds)->orderBy('id', 'ASC')->findAll();
        $materials = $materialModel->whereIn('produksi_header_id', $headerIds)->orderBy('id', 'ASC')->findAll();
        $colorants = $colorantModel->whereIn('produksi_header_id', $headerIds)->orderBy('id', 'ASC')->findAll();
        $packagings = $packagingModel->whereIn('produksi_header_id', $headerIds)->orderBy('id', 'ASC')->findAll();

        // Kelompokkan data detail berdasarkan produksi_header_id
        $jamsGrouped = [];
        foreach ($jams as $jam) {
            $jamsGrouped[$jam['produksi_header_id']][] = $jam;
        }

        $downtimesGrouped = [];
        foreach ($downtimes as $dt) {
            $downtimesGrouped[$dt['produksi_header_id']][] = $dt;
        }

        $materialsGrouped = [];
        foreach ($materials as $mat) {
            $materialsGrouped[$mat['produksi_header_id']][] = $mat;
        }

        $colorantsGrouped = [];
        foreach ($colorants as $col) {
            $colorantsGrouped[$col['produksi_header_id']][] = $col;
        }

        $packagingsGrouped = [];
        foreach ($packagings as $pack) {
            $packagingsGrouped[$pack['produksi_header_id']][] = $pack;
        }

        $exportData = [];
        foreach ($headersData as $h) {
            $hId = $h['id'];
            $hJams = $jamsGrouped[$hId] ?? [];
            $hDowntimes = $downtimesGrouped[$hId] ?? [];
            $hMaterials = $materialsGrouped[$hId] ?? [];
            $hColorants = $colorantsGrouped[$hId] ?? [];
            $hPackagings = $packagingsGrouped[$hId] ?? [];

            $maxRows = max(count($hJams), count($hDowntimes), count($hMaterials), count($hColorants), count($hPackagings), 1);

            for ($i = 0; $i < $maxRows; $i++) {
                $jamRow = $hJams[$i] ?? null;
                $dtRow = $hDowntimes[$i] ?? null;
                $matRow = $hMaterials[$i] ?? null;
                $colRow = $hColorants[$i] ?? null;
                $packRow = $hPackagings[$i] ?? null;

                $exportData[] = [
                    // tabel header
                    $h['tanggal'] ?? '',
                    $h['shift'] ?? '',
                    $h['grup'] ?? '',
                    $h['no_spk'] ?? '',
                    $h['nama_mesin'] ?? '',
                    $h['nomor_mesin'] ?? '',
                    $h['nama_produk'] ?? '',
                    $h['batch_number'] ?? '',
                    $h['packing'] ?? '',
                    $h['cycle_time'] ?? '',
                    $h['target'] ?? '',
                    $h['isi'] ?? '',
                    $h['operator'] ?? '',
                    // tabel jam produksi
                    $jamRow['rentang_jam'] ?? '',
                    $jamRow['hasil_produksi'] ?? '',
                    $h['grand_total_bagus'] ?? 0,
                    $h['sisa_po'] ?? 0,
                    $h['hold'] ?? 0,
                    $h['gumpalan'] ?? 0,
                    $h['grand_total_reject'] ?? 0,
                    // produksi reject (downtime)
                    $dtRow['alasan_downtime'] ?? '',
                    $dtRow['durasi_menit'] ?? 0,
                    $h['total_downtime'] ?? 0,
                    // produksi material
                    $matRow['merek_kode'] ?? '',
                    $matRow['pemakaian'] ?? '',
                    $matRow['lot_a'] ?? '',
                    $matRow['lot_b'] ?? '',
                    $matRow['lot_c'] ?? '',
                    $matRow['lot_d'] ?? '',
                    // produksi colorant
                    $colRow['code'] ?? '',
                    $colRow['nomor_lot'] ?? '',
                    $colRow['pemakaian'] ?? '',
                    // produksi packaging
                    $packRow['box'] ?? '',
                    $packRow['box_ukuran'] ?? '',
                    $packRow['plastik'] ?? '',
                    $packRow['plastik_ukuran'] ?? '',
                    $packRow['karung'] ?? '',
                    $packRow['karung_ukuran'] ?? '',

                    $h['catatan'] ?? '',
                ];
            }
        }

        $filename = 'produksi' . ($mesinLabel ? '-' . strtolower(str_replace(' ', '_', $mesinLabel)) : '') . '-' . date('Y-m-d-His');
        return $this->exportAsExcel($filename, $headers, $exportData);
    }

    /**
     * Export as Excel (HTML table format - works everywhere without library)
     */
    private function exportAsExcel($filename, $headers, $data)
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');

        // Start HTML table
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        echo '<x:Name>Produksi</x:Name>';
        echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>';
        echo '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        echo '</head>';
        echo '<body>';
        echo '<table border="1">';

        // Header row
        echo '<thead><tr style="background-color: #4361EE; color: #FFFFFF; font-weight: bold;">';
        foreach ($headers as $header) {
            echo '<th>' . htmlspecialchars($header, ENT_QUOTES, 'UTF-8') . '</th>';
        }
        echo '</tr></thead>';

        // Data rows
        echo '<tbody>';
        foreach ($data as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo '<td>' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            echo '</tr>';
        }
        echo '</tbody>';

        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit();
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
            ['Produk', 'Type', 'Item Packaging', 'Status', 'Kode', 'Jumlah', 'Nomor Lot', 'No SPK', 'Shift', 'Dibuat', 'Diupdate'],
            $data,
            function ($d) {
                return [
                    $d['produk'] ?? '',
                    $d['jenis_packaging'] ?? '',
                    $d['nama_packaging'] ?? '',
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
            ['Produk', 'Type', 'Item Masterbatch', 'Status', 'Kode', 'Jumlah', 'Nomor Lot', 'No SPK', 'Shift', 'Dibuat', 'Diupdate'],
            $data,
            function ($d) {
                return [
                    $d['produk'] ?? '',
                    $d['tipe'] ?? '',
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
            ['Tanggal', 'Produk Galon', 'Brand', 'Grade', 'Berat', 'Stock', 'Nomor Lot', 'Status', 'SPK', 'Shift'],
            $data,
            function ($d) {
                return [$d['tanggal'], $d['produk_galon'], $d['brand'], $d['grade'], $d['berat'], $d['stock'], $d['nomor_lot'], $d['status'], $d['no_spk'], $d['shif']];
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
