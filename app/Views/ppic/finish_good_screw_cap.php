<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$formatDate = static fn($v): string => ($t = strtotime((string)$v)) ? date('d-m-Y', $t) : '-';
$baseUrl    = 'ppic/finish_good/screw_cap';
$typeLabel  = 'Screw Cap';

// Array lengkap produk screw cap — edit di sini untuk mengubah data referensi
// fields: nomor, nama_produk, warna, berat, jumlah, keterangan
$produkScrewCap = [
    ['nomor' => 1,  'nama_produk' => "ALMA'SOEM",         'warna' => 'BLUE 01',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 2,  'nama_produk' => 'AMIDIS',             'warna' => 'ORANGE 01',  'berat' => 1.30, 'jumlah' => 1496000, 'keterangan' => null],
    ['nomor' => 3,  'nama_produk' => 'BLANK',              'warna' => 'BLACK 01',   'berat' => 1.30, 'jumlah' => 406000,  'keterangan' => null],
    ['nomor' => 4,  'nama_produk' => 'BLANK',              'warna' => 'BLUE 01-2',  'berat' => 1.30, 'jumlah' => 40000,   'keterangan' => null],
    ['nomor' => 5,  'nama_produk' => 'BLANK (CA)',         'warna' => 'BLUE 01-2',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 6,  'nama_produk' => 'BLANK (J WATER)',    'warna' => 'GREY 02',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 7,  'nama_produk' => 'BLANK',              'warna' => 'BLUE 02',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 8,  'nama_produk' => 'BLANK',              'warna' => 'BLUE 04',    'berat' => 1.30, 'jumlah' => 40000,   'keterangan' => null],
    ['nomor' => 9,  'nama_produk' => 'BLANK (stream)',     'warna' => 'BLUE 04',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 10, 'nama_produk' => 'BLANK',              'warna' => 'BLUE 05',    'berat' => 1.30, 'jumlah' => 208000,  'keterangan' => null],
    ['nomor' => 11, 'nama_produk' => 'BLANK',              'warna' => 'CLEAR',      'berat' => 1.30, 'jumlah' => 1104000, 'keterangan' => null],
    ['nomor' => 12, 'nama_produk' => 'BLANK',              'warna' => 'PURPLE 01',  'berat' => 1.30, 'jumlah' => 72000,   'keterangan' => null],
    ['nomor' => 13, 'nama_produk' => 'BLANK',              'warna' => 'GOLD 01',    'berat' => 1.30, 'jumlah' => 48000,   'keterangan' => null],
    ['nomor' => 14, 'nama_produk' => 'BLANK',              'warna' => 'GREEN 04',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 15, 'nama_produk' => 'BLANK',              'warna' => 'GREEN 06',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 16, 'nama_produk' => 'BLANK',              'warna' => 'GREEN 02',   'berat' => 1.30, 'jumlah' => 432000,  'keterangan' => null],
    ['nomor' => 17, 'nama_produk' => 'BLANK',              'warna' => 'GREEN 03',   'berat' => 1.30, 'jumlah' => 8000,    'keterangan' => null],
    ['nomor' => 18, 'nama_produk' => 'BLANK (Lakers)',     'warna' => 'GREEN 07',   'berat' => 1.30, 'jumlah' => 12000,   'keterangan' => null],
    ['nomor' => 19, 'nama_produk' => 'BLANK',              'warna' => 'GREEN 08',   'berat' => 1.30, 'jumlah' => 64000,   'keterangan' => null],
    ['nomor' => 20, 'nama_produk' => 'BLANK',              'warna' => 'RED 01',     'berat' => 1.30, 'jumlah' => 1136000, 'keterangan' => null],
    ['nomor' => 21, 'nama_produk' => 'BLANK',              'warna' => 'WHITE 01',   'berat' => 1.30, 'jumlah' => 64000,   'keterangan' => null],
    ['nomor' => 22, 'nama_produk' => 'BLANK',              'warna' => 'YELLOW 01',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 23, 'nama_produk' => 'BLANK',              'warna' => 'ORANGE 01',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 24, 'nama_produk' => 'BISTO',              'warna' => 'ORANGE 01',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 25, 'nama_produk' => 'BLU 9 (DW)',         'warna' => 'BLUE 01',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 26, 'nama_produk' => 'BLANK (vica)',       'warna' => 'YELLOW 02',  'berat' => 1.30, 'jumlah' => 500000,  'keterangan' => null],
    ['nomor' => 27, 'nama_produk' => 'BLANK (Guardian)',   'warna' => 'Orange 02',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 28, 'nama_produk' => 'NESTLE',             'warna' => 'BLUE 01',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 29, 'nama_produk' => 'BLANK (Watsons)',    'warna' => 'BLUE 13',    'berat' => 1.30, 'jumlah' => 3160000, 'keterangan' => null],
    ['nomor' => 30, 'nama_produk' => 'BLANK (Superindo)',  'warna' => 'RED 01',     'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 31, 'nama_produk' => 'ES ER CE',           'warna' => 'RED 01',     'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 32, 'nama_produk' => 'FATQUA',             'warna' => 'BLUE 04',    'berat' => 1.30, 'jumlah' => 160000,  'keterangan' => null],
    ['nomor' => 33, 'nama_produk' => 'BLANK (SIERRA)',     'warna' => 'BLUE 03',    'berat' => 1.30, 'jumlah' => 1504000, 'keterangan' => null],
    ['nomor' => 34, 'nama_produk' => 'OMBE (DW)',          'warna' => 'BLUE 17',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 35, 'nama_produk' => 'DELQUA',             'warna' => 'GREEN 11',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 36, 'nama_produk' => 'DELQUA',             'warna' => 'GREEN 02',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 37, 'nama_produk' => 'TIRTA',              'warna' => 'BLUE 01-2',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 38, 'nama_produk' => 'SHOFA',              'warna' => 'GREEN 06',   'berat' => 1.30, 'jumlah' => 152000,  'keterangan' => null],
    ['nomor' => 39, 'nama_produk' => 'PRIMA PANDAAN',      'warna' => 'BLUE 21',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 40, 'nama_produk' => 'BLANK',              'warna' => 'PINK 02',    'berat' => 1.30, 'jumlah' => 136000,  'keterangan' => null],
    ['nomor' => 41, 'nama_produk' => 'BLANK',              'warna' => 'BLUE 01',    'berat' => 1.30, 'jumlah' => 136000,  'keterangan' => null],
    ['nomor' => 42, 'nama_produk' => 'BLANK (Sanqua)',     'warna' => 'BLUE 23',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 43, 'nama_produk' => 'AIR ALAM',           'warna' => 'BLUE 22',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 44, 'nama_produk' => 'SISESA',             'warna' => 'PURPLE 01',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 45, 'nama_produk' => 'PRIMA SENTUL',       'warna' => 'BLUE 21',    'berat' => 1.30, 'jumlah' => 8000,    'keterangan' => null],
    ['nomor' => 46, 'nama_produk' => 'TRIPANCA',           'warna' => 'BLUE 04',    'berat' => 1.30, 'jumlah' => 1000000, 'keterangan' => null],
    ['nomor' => 47, 'nama_produk' => 'BLANK (HN)',         'warna' => 'BLUE 24',    'berat' => 1.30, 'jumlah' => 152000,  'keterangan' => null],
    ['nomor' => 48, 'nama_produk' => 'BLANK (BI)',         'warna' => 'GREEN 09',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 49, 'nama_produk' => 'INDOMARET',          'warna' => 'BLUE 01',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 50, 'nama_produk' => 'LEVIOS',             'warna' => 'BLUE 01',    'berat' => 1.30, 'jumlah' => 8000,    'keterangan' => null],
    ['nomor' => 51, 'nama_produk' => 'BLANK (SIERRA)',     'warna' => 'BLACK 01',   'berat' => 1.30, 'jumlah' => 220000,  'keterangan' => null],
    ['nomor' => 52, 'nama_produk' => 'BLANK (SIERRA)',     'warna' => 'RED 01',     'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 53, 'nama_produk' => 'LEVIOS',             'warna' => 'BLUE 17',    'berat' => 1.30, 'jumlah' => 1056000, 'keterangan' => null],
    ['nomor' => 54, 'nama_produk' => 'BLANK (ASMI)',       'warna' => 'BROWN 01',   'berat' => 1.30, 'jumlah' => 104000,  'keterangan' => null],
    ['nomor' => 55, 'nama_produk' => 'BLANK',              'warna' => 'GREEN 01',   'berat' => 1.30, 'jumlah' => 152000,  'keterangan' => null],
    ['nomor' => 56, 'nama_produk' => 'BLANK (CLUB)',       'warna' => 'BLUE 01-1',  'berat' => 1.30, 'jumlah' => 8000,    'keterangan' => null],
    ['nomor' => 57, 'nama_produk' => 'BLANK (VIONA)',      'warna' => 'GREEN 02',   'berat' => 1.30, 'jumlah' => 104000,  'keterangan' => null],
    ['nomor' => 58, 'nama_produk' => 'ALKAKUA',            'warna' => 'BLACK 01',   'berat' => 1.30, 'jumlah' => 208000,  'keterangan' => null],
    ['nomor' => 59, 'nama_produk' => 'BLANK (CLUB)',       'warna' => 'BLUE 25',    'berat' => 1.30, 'jumlah' => 8000,    'keterangan' => null],
    ['nomor' => 60, 'nama_produk' => 'HEVITRO',            'warna' => 'GREEN 10',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 61, 'nama_produk' => 'BLANK',              'warna' => 'BLUE 03',    'berat' => 1.30, 'jumlah' => 80000,   'keterangan' => null],
    ['nomor' => 62, 'nama_produk' => 'BLANK',              'warna' => 'BLUE 13',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 63, 'nama_produk' => 'BLANK (BOX POLOS)',  'warna' => 'GREEN 03',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 64, 'nama_produk' => 'BLANK (BOX POLOS)',  'warna' => 'BLACK 01',   'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 65, 'nama_produk' => 'BLANK (UMEGA)',      'warna' => 'BLUE 05',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 66, 'nama_produk' => 'BLANK (UMEGA)',      'warna' => 'BLUE 02',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 67, 'nama_produk' => 'BLANK (AMOZ)',       'warna' => 'SILVER 01',  'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 68, 'nama_produk' => 'BLANK (SST)',        'warna' => 'PINK 03',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 69, 'nama_produk' => 'BLANK (POLOS)',      'warna' => 'BLUE 02',    'berat' => 1.30, 'jumlah' => 352000,  'keterangan' => null],
    ['nomor' => 70, 'nama_produk' => 'BLANK (PELANGI)',    'warna' => 'PINK 02',    'berat' => 1.30, 'jumlah' => 0,       'keterangan' => null],
    ['nomor' => 71, 'nama_produk' => 'BLANK (ASPEN)',      'warna' => 'BLUE 30',    'berat' => 1.45, 'jumlah' => 780000,  'keterangan' => null],
];
?>

<style>
    .filter-sidebar {
        position: fixed;
        top: 0;
        right: -360px;
        width: 360px;
        height: 100vh;
        background: #fff;
        z-index: 1100;
        box-shadow: -5px 0 15px rgba(0, 0, 0, .1);
        transition: all .3s ease;
        display: flex;
        flex-direction: column
    }

    .filter-sidebar.active {
        right: 0
    }

    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, .5);
        z-index: 1090;
        display: none;
        backdrop-filter: blur(2px)
    }

    .filter-overlay.active {
        display: block
    }

    .filter-header {
        padding: 1.25rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fc
    }

    .filter-body {
        padding: 1.5rem;
        flex-grow: 1;
        overflow-y: auto
    }

    .filter-footer {
        padding: 1.25rem;
        border-top: 1px solid #e3e6f0;
        background: #f8f9fc;
        display: flex;
        gap: 10px
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        background: #eaecf4;
        padding: .25rem .75rem;
        border-radius: 20px;
        font-size: .85rem;
        margin-right: .5rem;
        margin-bottom: .5rem
    }
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?= view('ppic/partials/machine_links', ['current_machine' => $current_machine]) ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <a href="<?= base_url('ppic') ?>" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button class="btn btn-primary btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#addDataModal">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
                <button type="button" class="btn btn-success btn-sm mr-2" data-bs-toggle="modal" data-bs-target="#pasteImportModal">
                    <i class="fas fa-paste"></i> Paste dari Excel
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="toggleFilter(true)">
                    <i class="fas fa-filter"></i> Filter
                    <?php if (!empty($start_date) || !empty($end_date)): ?>
                        <span class="badge badge-primary ml-1">!</span>
                    <?php endif; ?>
                </button>
                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#trashModalFg">
                    <i class="fas fa-trash-restore"></i> Sampah
                </button>
            </div>
            <form method="get" action="<?= base_url($baseUrl) ?>" class="d-flex align-items-center mb-0 mt-2 mt-md-0">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" style="min-width:200px" placeholder="Cari data..." value="<?= esc($q ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <?php if (!empty($start_date) || !empty($end_date)): ?>
            <div class="mt-2">
                <?php if (!empty($start_date)): ?><span class="filter-chip">Mulai: <?= esc($start_date) ?></span><?php endif; ?>
                <?php if (!empty($end_date)): ?><span class="filter-chip">Selesai: <?= esc($end_date) ?></span><?php endif; ?>
                <a href="<?= base_url($baseUrl) ?>" class="text-danger small ml-2"><i class="fas fa-times"></i> Reset</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="close" data-bs-dismiss="alert">&times;</button></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="close" data-bs-dismiss="alert">&times;</button></div>
        <?php endif; ?>

        <!-- Total Summary Cards -->
        <div class="d-flex gap-2 mb-3">
            <div class="bg-white border rounded px-3 py-2 d-flex align-items-center flex-fill" style="border-left:4px solid #f6c23e!important;gap:12px">
                <i class="fas fa-boxes text-warning" style="font-size:1rem;opacity:.7"></i>
                <div>
                    <div style="font-size:.65rem;font-weight:700;color:#f6c23e;text-transform:uppercase;letter-spacing:.05em">Total Jumlah (Filter)</div>
                    <div style="font-size:.9rem;font-weight:700;line-height:1"><?= number_format($totals['jumlah'] ?? 0) ?> pcs</div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Warna</th>
                        <th>Berat (gr)</th>
                        <th>Jumlah (pcs)</th>
                        <th>Keterangan</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data_list)): ?>
                        <?php foreach ($data_list as $item): ?>
                            <tr>
                                <td><?= esc($formatDate($item['tanggal'])) ?></td>
                                <td><?= esc($item['nomor'] ?? '-') ?></td>
                                <td><?= esc($item['nama_produk'] ?? '-') ?></td>
                                <td><?= esc($item['warna'] ?? '-') ?></td>
                                <td><?= esc($item['berat'] ?? '-') ?></td>
                                <td><?= esc($item['jumlah'] ?? '-') ?></td>
                                <td><?= esc($item['keterangan'] ?? '-') ?></td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button" class="btn btn-warning btn-sm"
                                            onclick="setEditModal(this)" data-bs-toggle="modal" data-bs-target="#editDataModal"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-tanggal="<?= esc($item['tanggal'] ?? '') ?>"
                                            data-nomor="<?= esc($item['nomor'] ?? '') ?>"
                                            data-nama_produk="<?= esc($item['nama_produk'] ?? '') ?>"
                                            data-warna="<?= esc($item['warna'] ?? '') ?>"
                                            data-berat="<?= esc($item['berat'] ?? '') ?>"
                                            data-jumlah="<?= esc($item['jumlah'] ?? '') ?>"
                                            data-keterangan="<?= esc($item['keterangan'] ?? '') ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                            data-bs-toggle="modal" data-bs-target="#modalDelete"
                                            data-id="<?= esc($item['id']) ?>"
                                            data-nama="<?= esc($item['nama_produk'] ?? '') ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Tidak ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="thead-light font-weight-bold">
                    <tr>
                        <td colspan="5" class="text-right">TOTAL</td>
                        <td class="text-warning"><?= number_format($totals['jumlah'] ?? 0) ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between mt-3">
            <div class="text-muted small">
                Total: <strong><?= count($data_list ?? []) ?></strong> entri
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle mr-2 text-primary"></i>Tambah Finish Good <?= $typeLabel ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('ppic/finish_good/screw_cap/tambah') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label>Tanggal <span class="text-danger">*</span></label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Nomor</label><input type="text" class="form-control" name="nomor" placeholder="Nomor..."></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <div class="input-group">
                                    <select class="form-control select2-produk" name="nama_produk" id="add_nama_produk">
                                        <option value="">-- Pilih Produk --</option>
                                        <?php foreach ($produkScrewCap as $p): ?>
                                            <option value="<?= esc($p['nama_produk']) ?>"
                                                data-nomor="<?= esc($p['nomor']) ?>"
                                                data-warna="<?= esc($p['warna']) ?>"
                                                data-berat="<?= esc($p['berat']) ?>"
                                                data-jumlah="<?= esc($p['jumlah']) ?>">
                                                <?= esc($p['nomor'] . '. ' . $p['nama_produk'] . ' — ' . $p['warna']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addProdukModal" title="Tambah produk baru">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna" id="add_warna" placeholder="Warna..."></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat" id="add_berat" placeholder="Berat..."></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Jumlah (pcs)</label><input type="text" class="form-control" name="jumlah" id="add_jumlah" placeholder="Jumlah..."></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Keterangan</label><input type="text" class="form-control" name="keterangan" placeholder="Keterangan..."></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2 text-warning"></i>Edit Finish Good <?= $typeLabel ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group"><label>Tanggal</label><input type="date" class="form-control" name="tanggal" id="edit_tanggal" required></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Nomor</label><input type="text" class="form-control" name="nomor" id="edit_nomor"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <select class="form-control select2-produk" name="nama_produk" id="edit_nama_produk">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach ($produkScrewCap as $p): ?>
                                        <option value="<?= esc($p['nama_produk']) ?>"
                                            data-nomor="<?= esc($p['nomor']) ?>"
                                            data-warna="<?= esc($p['warna']) ?>"
                                            data-berat="<?= esc($p['berat']) ?>"
                                            data-jumlah="<?= esc($p['jumlah']) ?>">
                                            <?= esc($p['nomor'] . '. ' . $p['nama_produk'] . ' — ' . $p['warna']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna" id="edit_warna"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat" id="edit_berat"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Jumlah (pcs)</label><input type="text" class="form-control" name="jumlah" id="edit_jumlah"></div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group"><label>Keterangan</label><input type="text" class="form-control" name="keterangan" id="edit_keterangan"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="formDelete" method="post" action="<?= base_url('ppic/finish_good/screw_cap/delete/0') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt text-danger fa-3x mb-3"></i>
                    <p id="delete_message" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Filter Sidebar -->
<div class="filter-overlay" onclick="toggleFilter(false)"></div>
<div class="filter-sidebar">
    <div class="filter-header">
        <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i>Filter</h5>
        <button type="button" class="close" onclick="toggleFilter(false)">&times;</button>
    </div>
    <form method="get" action="<?= base_url($baseUrl) ?>">
        <div class="filter-body">
            <input type="hidden" name="q" value="<?= esc($q ?? '') ?>">
            <div class="form-group"><label class="small font-weight-bold">Tanggal Mulai</label><input type="date" name="start_date" class="form-control" value="<?= esc($start_date ?? '') ?>"></div>
            <div class="form-group"><label class="small font-weight-bold">Tanggal Selesai</label><input type="date" name="end_date" class="form-control" value="<?= esc($end_date ?? '') ?>"></div>
        </div>
        <div class="filter-footer">
            <a href="<?= base_url($baseUrl) ?>" class="btn btn-secondary flex-fill">Reset</a>
            <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        </div>
    </form>
</div>

<script>
    function toggleFilter(show) {
        document.querySelector('.filter-sidebar').classList.toggle('active', show);
        document.querySelector('.filter-overlay').classList.toggle('active', show);
    }

    function setEditModal(btn) {
        const d = btn.dataset;
        document.getElementById('edit_id').value = d.id;
        document.getElementById('edit_tanggal').value = d.tanggal || '';
        document.getElementById('edit_nomor').value = d.nomor || '';

        // Handle nama_produk select
        const selProduk = document.getElementById('edit_nama_produk');
        selProduk.value = d.nama_produk || '';
        if (selProduk.value === '' && d.nama_produk) {
            const opt = new Option(d.nama_produk, d.nama_produk, true, true);
            selProduk.add(opt);
        }
        if (typeof $(selProduk).select2 === 'function') {
            $(selProduk).trigger('change');
        }

        document.getElementById('edit_warna').value = d.warna || '';
        document.getElementById('edit_berat').value = d.berat || '';
        document.getElementById('edit_jumlah').value = d.jumlah || '';
        document.getElementById('edit_keterangan').value = d.keterangan || '';
        document.getElementById('formEdit').action = '<?= base_url('ppic/finish_good/screw_cap/update') ?>/' + d.id;
    }

    function setDeleteModal(btn) {
        const id = btn.dataset.id;
        const nama = btn.dataset.nama || 'data ini';
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_message').textContent = 'Hapus "' + nama + '"?';
        document.getElementById('formDelete').action = '<?= base_url('ppic/finish_good/screw_cap/delete') ?>/' + id;
    }

    // Load Trash Data
    function loadTrashScrewCap() {
        const tbody = document.querySelector('#trashTableScrewCap tbody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';

        const url = '<?= base_url('ppic/finish_good/screw_cap/get-trash') ?>';
        console.log('Fetching trash from:', url);

        fetch(url)
            .then(r => {
                console.log('Response status:', r.status);
                if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
                return r.json();
            })
            .then(data => {
                console.log('Trash data:', data);
                tbody.innerHTML = '';
                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Tempat sampah kosong.</td></tr>';
                    return;
                }
                data.forEach(item => {
                    const html = `<tr style="font-size: 13px;">
                    <td class="px-3 py-2">
                        <div class="font-weight-bold">${item.nama_produk || '-'}</div>
                        <div class="text-muted" style="font-size: 11px;">Warna: ${item.warna || '-'}</div>
                    </td>
                    <td class="py-2">${item.nomor || '-'}</td>
                    <td class="py-2 text-muted text-center">${item.deleted_at}</td>
                    <td class="text-end py-2 px-3">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="<?= base_url('ppic/finish_good/screw_cap/restore') ?>/${item.id}" class="btn btn-success btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" title="Pulihkan">
                                <i class="fas fa-undo"></i>
                            </a>
                            <a href="<?= base_url('ppic/finish_good/screw_cap/delete-permanent') ?>/${item.id}" class="btn btn-outline-danger btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" title="Hapus Permanen" onclick="return confirm('Hapus permanen? Tidak dapat dikembalikan!')">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </td>
                </tr>`;
                    tbody.insertAdjacentHTML('beforeend', html);
                });
            })
            .catch(err => {
                console.error('Error loading trash:', err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-danger">Gagal memuat data sampah: ' + err.message + '</td></tr>';
            });
    }

    // Initialize modals
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded: Initializing delete modal handler');

        // Delete modal handler
        const deleteModal = document.getElementById('modalDelete');
        console.log('Delete modal element:', deleteModal);

        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                console.log('Delete modal showing', event);
                const btn = event.relatedTarget;
                console.log('Related target button:', btn);

                if (!btn) {
                    console.error('No related target button found');
                    return;
                }

                const id = btn.getAttribute('data-id');
                const nama = btn.getAttribute('data-nama') || 'data ini';
                console.log('Delete ID:', id, 'Nama:', nama);

                document.getElementById('delete_id').value = id;
                document.getElementById('delete_message').textContent = 'Hapus "' + nama + '"?';
                document.getElementById('formDelete').action = '<?= base_url('ppic/finish_good/screw_cap/delete') ?>/' + id;
                console.log('Form action set to:', document.getElementById('formDelete').action);
            });
            console.log('Delete modal event listener attached');
        }

        // Trash modal handler
        const trashModal = document.getElementById('trashModalFg');
        if (trashModal) {
            trashModal.addEventListener('show.bs.modal', loadTrashScrewCap);
            console.log('Trash modal event listener registered');
        }

        // Initialize Select2 for nama_produk dropdowns
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-produk').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Produk --',
                allowClear: true,
                width: '100%'
            });
        }

        // Autofill form tambah saat produk dipilih
        const addProduk = document.getElementById('add_nama_produk');
        if (addProduk) {
            $(addProduk).on('change', function() {
                const sel = this.options[this.selectedIndex];
                if (!sel || !sel.value) return;
                const nomorEl = document.querySelector('#addDataModal [name="nomor"]');
                if (nomorEl && !nomorEl.value) nomorEl.value = sel.getAttribute('data-nomor') || '';
                document.getElementById('add_warna').value = sel.getAttribute('data-warna') || '';
                document.getElementById('add_berat').value = sel.getAttribute('data-berat') || '';
                document.getElementById('add_jumlah').value = sel.getAttribute('data-jumlah') || '';
            });
        }
    });
</script>

<!-- Trash Modal -->
<div class="modal fade" id="trashModalFg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-secondary">
                    <i class="fas fa-trash-alt mr-2"></i> Tempat Sampah - Finish Good <?= $typeLabel ?>
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="trashTableScrewCap" style="min-width: auto;">
                        <thead class="bg-white text-xs text-muted text-uppercase">
                            <tr>
                                <th class="px-3 py-3" style="width: 35%;">Nama Produk</th>
                                <th class="py-3" style="width: 20%;">Nomor</th>
                                <th class="py-3 text-center" style="width: 25%;">Dihapus Pada</th>
                                <th class="text-end py-3 px-3" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Klik tombol untuk memuat...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= view('ppic/partials/paste_import_modal', ['type' => 'screw_cap', 'type_label' => $typeLabel]) ?>

<?= view('ppic/partials/add_produk_modal', ['type' => 'screw_cap', 'type_label' => $typeLabel]) ?>