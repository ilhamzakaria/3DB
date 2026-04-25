<style>
    thead th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .btn-delete {
        position: absolute;
        right: 10px;
        bottom: 10px;
    }

    /* Override sb-admin-2: enable horizontal scroll (content-wrapper has overflow-x: hidden) */
    #wrapper #content-wrapper {
        overflow-x: auto !important;
    }

    /* horizontal scroll inside table wrapper */
    .ppic-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        min-width: 0;
    }

    .ppic-table-wrap table {
        min-width: 1100px;
        margin-bottom: 0;
    }

    .ppic-table-wrap th,
    .ppic-table-wrap td {
        white-space: nowrap;
    }

    .filter-toggle {
        position: relative;
    }

    .filter-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        margin-left: 6px;
        padding: 0 6px;
        border-radius: 999px;
        background: #0d6efd;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }

    .filter-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-top: 12px;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #eef4ff;
        color: #1d4ed8;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .filter-drawer {
        max-width: 360px;
    }

    .filter-drawer .offcanvas-header {
        border-bottom: 1px solid #e9ecef;
    }

    .filter-drawer .offcanvas-body {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .filter-section-title {
        margin-bottom: 8px;
        color: #6c757d;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .filter-actions {
        display: flex;
        gap: 10px;
    }

    .filter-hint {
        margin: 0;
        color: #6c757d;
        font-size: 0.84rem;
    }
</style>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
$searchValue = trim((string) ($q ?? ''));
$filterStartDate = trim((string) ($start_date ?? ''));
$filterEndDate = trim((string) ($end_date ?? ''));
$activeFilterCount = 0;

if ($filterStartDate !== '' || $filterEndDate !== '') {
    $activeFilterCount++;
}

$dateSummary = '';
if ($filterStartDate !== '' && $filterEndDate !== '') {
    $dateSummary = $filterStartDate . ' s/d ' . $filterEndDate;
} elseif ($filterStartDate !== '') {
    $dateSummary = 'Mulai ' . $filterStartDate;
} elseif ($filterEndDate !== '') {
    $dateSummary = 'Sampai ' . $filterEndDate;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <?php if (session()->get('role') == 'ppic') { ?>
                                    <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDataModal">
                                        <i class="fas fa-plus"></i> Tambah Data
                                    </a>
                                <?php } ?>
                                <a href="<?= base_url('export/dashboard') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel"></i> Export
                                </a>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <form method="get" action="<?= base_url('ppic') ?>" class="d-flex align-items-center gap-2 mb-0">
                                    <input type="hidden" name="start_date" value="<?= esc($filterStartDate) ?>">
                                    <input type="hidden" name="end_date" value="<?= esc($filterEndDate) ?>">
                                    <input
                                        type="text"
                                        name="q"
                                        class="form-control form-control-sm"
                                        style="min-width: 260px;"
                                        placeholder="Cari data..."
                                        value="<?= esc($searchValue) ?>">
                                    <button class="btn btn-outline-primary btn-sm" type="submit">Cari</button>
                                </form>

                                <button
                                    class="btn btn-outline-primary btn-sm filter-toggle"
                                    type="button"
                                    data-bs-toggle="offcanvas"
                                    data-bs-target="#ppicFilterDrawer"
                                    aria-controls="ppicFilterDrawer">
                                    <i class="fas fa-filter"></i> Filter
                                    <?php if ($activeFilterCount > 0): ?>
                                        <span class="filter-count"><?= $activeFilterCount ?></span>
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>

                        <?php if ($activeFilterCount > 0): ?>
                            <div class="filter-summary">
                                <?php if ($dateSummary !== ''): ?>
                                    <span class="filter-chip">Tanggal: <?= esc($dateSummary) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="ppicFilterDrawer" aria-labelledby="ppicFilterDrawerLabel">
                            <div class="offcanvas-header">
                                <div>
                                    <h5 class="offcanvas-title mb-1" id="ppicFilterDrawerLabel">Filter PPIC</h5>
                                    <p class="filter-hint">Pilih kata kunci dan rentang tanggal untuk melihat data yang lebih spesifik.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form method="get" action="<?= base_url('ppic') ?>" class="d-flex flex-column gap-3">
                                    <input type="hidden" name="q" value="<?= esc($searchValue) ?>">

                                    <div>
                                        <label class="filter-section-title" for="ppicFilterStartDate">Tanggal Mulai</label>
                                        <input
                                            type="date"
                                            id="ppicFilterStartDate"
                                            name="start_date"
                                            class="form-control"
                                            value="<?= esc($filterStartDate) ?>">
                                    </div>

                                    <div>
                                        <label class="filter-section-title" for="ppicFilterEndDate">Tanggal Selesai</label>
                                        <input
                                            type="date"
                                            id="ppicFilterEndDate"
                                            name="end_date"
                                            class="form-control"
                                            value="<?= esc($filterEndDate) ?>">
                                    </div>

                                    <div class="filter-actions">
                                        <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
                                        <a href="<?= base_url('ppic') ?>" class="btn btn-outline-secondary flex-fill">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive ppic-table-wrap">
                            <h6 class="m-0 font-weight-bold text-primary text-center mb-3">Laporan ppic harian</h6>

                            <table class="table table-bordered" id="dataTable" cellspacing="0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th class="small fw-bold" width="5%">Jam</th>
                                        <th class="small fw-bold">No. SPK</th>
                                        <th class="small fw-bold">Nama Mesin</th>
                                        <th class="small fw-bold">Nama Produk</th>
                                        <th class="small fw-bold">Grade</th>
                                        <th class="small fw-bold">Warna</th>
                                        <th class="small fw-bold">Nomor Mesin</th>
                                        <th class="small fw-bold">Operator</th>
                                        <th class="small fw-bold">Target</th>
                                        <th class="small fw-bold">Revisi</th>
                                        <?php if (session()->get('role') == 'ppic'): ?>
                                            <th class="small fw-bold" width="6%">Action</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>

                                <tbody class="text-center">

                                    <?php
                                    $shiftRanges = [
                                        '1' => '07-14',
                                        '2' => '14-22',
                                        '3' => '22-06',
                                    ];

                                    $currentDate = '';
                                    $currentShift = '';

                                    foreach ($produksi as $p):
                                    ?>

                                        <?php if ($currentDate != $p['tanggal']): ?>
                                            <tr class="table-secondary">
                                                <td colspan="11">
                                                    <strong>
                                                        <?php
                                                        if (!empty($p['tanggal'])) {
                                                            $date = new DateTime($p['tanggal']);
                                                            $formatter = new IntlDateFormatter(
                                                                'id_ID',
                                                                IntlDateFormatter::LONG, // Format: 16 Maret 2026
                                                                IntlDateFormatter::NONE  // Tanpa waktu (Jam)
                                                            );
                                                            echo esc($formatter->format($date));
                                                        }
                                                        ?>
                                                    </strong>
                                                </td>
                                            </tr>

                                            <?php
                                            $currentDate = $p['tanggal'];
                                            $currentShift = ''; // reset shift jika tanggal berubah
                                            ?>
                                        <?php endif; ?>


                                        <?php if ($currentShift != $p['shif']): ?>
                                            <tr class="table-primary">
                                                <td colspan="11">
                                                    <strong class="small fw-bold">
                                                        SHIFT <?= $p['shif']; ?>
                                                        <?= isset($shiftRanges[$p['shif']]) ? '(' . $shiftRanges[$p['shif']] . ')' : '' ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <?php $currentShift = $p['shif']; ?>
                                        <?php endif; ?>


                                        <tr>
                                            <td><?= $p['jam']; ?></td>
                                            <td><?= $p['no_spk'] ?></td>
                                            <td><?= $p['nama_mesin'] ?></td>
                                            <td><?= $p['nama_produk'] ?></td>
                                            <td><?= $p['grade'] ?></td>
                                            <td><?= $p['warna'] ?></td>
                                            <td><?= $p['nomor_mesin'] ?></td>
                                            <td><?= $p['operator'] ?></td>
                                            <td><?= $p['targett'] ?></td>

                                            <?php
                                            $revisiText = trim((string) ($p['revisi_display'] ?? $p['revisi'] ?? ''));
                                            ?>
                                            <?php if ($revisiText !== ''): ?>
                                                <td class="text-danger"><?= esc($revisiText) ?></td>
                                            <?php else: ?>
                                                <td>-</td>
                                            <?php endif; ?>


                                            <?php if (session()->get('role') == 'ppic'): ?>
                                                <td class="text-center d-flex gap-1 justify-content-center">
                                                    <a href="#"
                                                        class="btnEdit"
                                                        data-id="<?= $p['id'] ?>"
                                                        data-jam="<?= $p['jam'] ?>"
                                                        data-no_spk="<?= $p['no_spk'] ?>"
                                                        data-nama_mesin="<?= $p['nama_mesin'] ?>"
                                                        data-nama_produk="<?= $p['nama_produk'] ?>"
                                                        data-grade="<?= $p['grade'] ?>"
                                                        data-warna="<?= $p['warna'] ?>"
                                                        data-nomor_mesin="<?= $p['nomor_mesin'] ?>"
                                                        data-shif="<?= $p['shif'] ?>"
                                                        data-operator="<?= $p['operator'] ?>"
                                                        data-targett="<?= $p['targett'] ?>"
                                                        data-revisi="<?= $p['revisi'] ?>"
                                                        data-tanggal="<?= $p['tanggal'] ?>"
                                                        data-toggle="modal"
                                                        data-bs-toggle="modal"
                                                        data-target="#modalEdit"
                                                        data-bs-target="#modalEdit">
                                                        <span class="small text-primary">Edit</span>
                                                    </a>
                                                    |
                                                    <a href="#"
                                                        data-toggle="modal"
                                                        data-target="#modalDelete"
                                                        data-id="<?= $p['id'] ?>">
                                                        <span class="small text-danger">Hapus</span>
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>
                            </table>



                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Modal Tambah Data -->
        <div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="addDataModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary acordion-header" id="addDataModalLabel">Tambah Data Produksi</h5>

                    </div>

                    <form method="POST" action="<?= base_url('ppic/tambahData') ?>">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                        <div class="modal-body">

                            <div class="accordion" id="accordionProduksi">

                                <!-- ================= TAMBAH HEADER PRODUKSI ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#headerProduksi">
                                            Data Utama
                                        </button>
                                    </h2>

                                    <div id="headerProduksi" class="accordion-collapse collapse show" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label class="fw-bold small">Master Data SPK</label>
                                                    <select class="form-control" id="masterNoSpkSelect">
                                                        <option value="">-- Pilih No. SPK --</option>
                                                        <?php foreach (($spk_master ?? []) as $master): ?>
                                                            <option value="<?= esc($master['no_spk']) ?>">
                                                                <?= esc($master['no_spk']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <!-- <small class="form-text text-muted">
                                                        Pilih 1x, data utama akan terisi otomatis.
                                                    </small> -->
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label class="fw-bold small">Jam</label>
                                                    <select class="form-control" name="jam" id="jamSelect" required>
                                                        <option value="">-- Pilih Jam --</option>
                                                        <?php
                                                        $listJam = [
                                                            "07-08",
                                                            "08-09",
                                                            "09-10",
                                                            "10-11",
                                                            "11-12",
                                                            "12-13",
                                                            "13-14", // Shift 1
                                                            "14-15",
                                                            "15-16",
                                                            "16-17",
                                                            "17-18",
                                                            "18-19",
                                                            "19-20",
                                                            "20-21",
                                                            "21-22", // Shift 2
                                                            "22-23",
                                                            "23-00",
                                                            "00-01",
                                                            "01-02",
                                                            "03-04",
                                                            "04-05",
                                                            "05-06",
                                                            "06-07"  // Shift 3
                                                        ];
                                                        foreach ($listJam as $j) :
                                                        ?>
                                                            <option value="<?= $j ?>"><?= $j ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>No. SPK</label>
                                                    <input type="text" class="form-control" name="no_spk" id="add_no_spk">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Nama Mesin</label>
                                                    <input type="text" class="form-control" name="nama_mesin" id="add_nama_mesin">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Nama Produk</label>
                                                    <input type="text" class="form-control" name="nama_produk" id="add_nama_produk">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Shift</label>
                                                    <select class="form-control" name="shif" id="shiftInput">
                                                        <option value="">-- otomatis --</option>
                                                        <option value="1">Shift 1 (07-14)</option>
                                                        <option value="2">Shift 2 (14-22)</option>
                                                        <option value="3">Shift 3 (22-06)</option>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Grade</label>
                                                    <input type="text" class="form-control" name="grade" id="add_grade">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Warna</label>
                                                    <input type="text" class="form-control" name="warna" id="add_warna">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Nomor Mesin</label>
                                                    <input type="text" class="form-control" name="nomor_mesin" id="add_nomor_mesin">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Operator</label>
                                                    <input type="text" class="form-control" name="operator" id="add_operator">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Target</label>
                                                    <input type="text" class="form-control" name="targett" id="add_targett">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- modal delete -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formDelete" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">

                <div class="modal-body">
                    <p>Yakin ingin menghapus data ini?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end modal delete -->

<!-- modal edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEdit" action="<?= base_url('ppic/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Produksi</h5>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold small">Jam</label>
                                <select class="form-control" name="jam" id="edit_jam" required>
                                    <option value="">-- Pilih Jam --</option>
                                    <?php
                                    $listJam = [
                                        "07-08",
                                        "08-09",
                                        "09-10",
                                        "10-11",
                                        "11-12",
                                        "12-13",
                                        "13-14", // Shift 1
                                        "14-15",
                                        "15-16",
                                        "16-17",
                                        "17-18",
                                        "18-19",
                                        "19-20",
                                        "20-21",
                                        "21-22", // Shift 2
                                        "22-23",
                                        "23-00",
                                        "00-01",
                                        "01-02",
                                        "03-04",
                                        "04-05",
                                        "05-06",
                                        "06-07"  // Shift 3
                                    ];
                                    foreach ($listJam as $j) :
                                    ?>
                                        <option value="<?= $j ?>"><?= $j ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>No SPK</label>
                                <input type="text" name="no_spk" id="edit_no_spk" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Mesin</label>
                                <input type="text" name="nama_mesin" id="edit_nama_mesin" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Produk</label>
                                <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Grade</label>
                                <input type="text" name="grade" id="edit_grade" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Warna</label>
                                <input type="text" name="warna" id="edit_warna" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nomor Mesin</label>
                                <input type="text" name="nomor_mesin" id="edit_nomor_mesin" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Shift</label>
                                <select name="shif" id="edit_shif" class="form-control">
                                    <option value="">-- otomatis --</option>
                                    <option value="1">Shift 1 (07-14)</option>
                                    <option value="2">Shift 2 (14-22)</option>
                                    <option value="3">Shift 3 (22-06)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Operator</label>
                                <input type="text" name="operator" id="edit_operator" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Target</label>
                                <input type="text" name="targett" id="edit_targett" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Revisi</label>
                                <input type="text" name="revisi" id="edit_revisi" class="form-control">
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- end modal edit -->

<!-- js modal delete -->
<script>
    window.addEventListener('load', function() {
        if (!window.jQuery) return;

        $('#modalDelete').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            $('#formDelete').attr('action', "<?= base_url('ppic/delete/') ?>" + id);
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('vendor/jquery/jquery.min.js') ?>"></script>

<script>
    // edit modal setup
    $(function() {
        $('.btnEdit').on('click', function() {
            const id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_jam').val($(this).data('jam'));
            $('#edit_no_spk').val($(this).data('no_spk'));
            $('#edit_nama_mesin').val($(this).data('nama_mesin'));
            $('#edit_nama_produk').val($(this).data('nama_produk'));
            $('#edit_grade').val($(this).data('grade'));
            $('#edit_warna').val($(this).data('warna'));
            $('#edit_nomor_mesin').val($(this).data('nomor_mesin'));
            $('#edit_shif').val($(this).data('shif'));
            $('#edit_operator').val($(this).data('operator'));
            $('#edit_targett').val($(this).data('targett'));
            $('#edit_revisi').val($(this).data('revisi'));
            $('#edit_tanggal').val($(this).data('tanggal'));

            // update form action to include id
            $('#formEdit').attr('action', "<?= base_url('ppic/update/') ?>" + id);
        });
    });
</script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="<?= base_url('vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<!-- Page level custom scripts -->
<script src="<?= base_url('js/demo/datatables-demo.js') ?>"></script>

<script>
    const ppicSpkMaster = <?= json_encode($spk_master ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const ppicSpkMasterMap = Object.fromEntries(
        ppicSpkMaster.map((item) => [String(item.no_spk || '').trim(), item])
    );

    function applyPpicMasterData(noSpk) {
        const key = String(noSpk || '').trim();
        const master = ppicSpkMasterMap[key];

        if (!master) {
            return;
        }

        const noSpkInput = document.getElementById('add_no_spk');
        const namaMesinInput = document.getElementById('add_nama_mesin');
        const namaProdukInput = document.getElementById('add_nama_produk');
        const gradeInput = document.getElementById('add_grade');
        const warnaInput = document.getElementById('add_warna');
        const nomorMesinInput = document.getElementById('add_nomor_mesin');
        const operatorInput = document.getElementById('add_operator');
        const targetInput = document.getElementById('add_targett');

        if (noSpkInput) noSpkInput.value = master.no_spk || '';
        if (namaMesinInput) namaMesinInput.value = master.nama_mesin || '';
        if (namaProdukInput) namaProdukInput.value = master.nama_produk || '';
        if (gradeInput) gradeInput.value = master.grade || '';
        if (warnaInput) warnaInput.value = master.warna || '';
        if (nomorMesinInput) nomorMesinInput.value = master.nomor_mesin || '';
        if (operatorInput) operatorInput.value = master.operator || '';
        if (targetInput) targetInput.value = master.targett || '';
    }

    const masterNoSpkSelect = document.getElementById('masterNoSpkSelect');
    if (masterNoSpkSelect) {
        masterNoSpkSelect.addEventListener('change', function() {
            applyPpicMasterData(this.value);
        });
    }

    const addNoSpkInput = document.getElementById('add_no_spk');
    if (addNoSpkInput) {
        addNoSpkInput.addEventListener('change', function() {
            applyPpicMasterData(this.value);
        });
    }

    document.getElementById('jamSelect').addEventListener('change', function() {
        const jam = this.value;
        const shiftSelect = document.getElementById('shiftInput');

        if (!shiftSelect) {
            return;
        }

        const shift1 = ["07-08", "08-09", "09-10", "10-11", "11-12", "12-13", "13-14"];
        const shift2 = ["14-15", "15-16", "16-17", "17-18", "18-19", "19-20", "20-21", "21-22"];
        const shift3 = ["22-23", "23-00", "00-01", "01-02", "02-03", "03-04", "04-05", "05-06", "06-07"];

        if (shift1.includes(jam)) {
            shiftSelect.value = "1";
        } else if (shift2.includes(jam)) {
            shiftSelect.value = "2";
        } else if (shift3.includes(jam)) {
            shiftSelect.value = "3";
        } else {
            shiftSelect.value = "";
        }
    });
</script>



<?= $this->endSection() ?>