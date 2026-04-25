<style>
    :root {
        --primary-modern: #4e73df;
        --secondary-modern: #1cc88a;
        --bg-modern: #f8fafc;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: var(--bg-modern);
        font-family: 'Inter', 'Nunito', sans-serif;
    }

    /* Page Transition Animation */
    .page-content-wrapper {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        transition: transform 0.2s;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #edf2f7;
        border-radius: 15px 15px 0 0 !important;
        padding: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .page-title-gradient {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
        font-size: 1.75rem;
    }

    .table thead th {
        background-color: #f8fafc;
        border-top: none !important;
        border-bottom: 2px solid #edf2f7 !important;
        color: #4e73df;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.9rem;
    }

    .table-secondary {
        background-color: #f1f5f9 !important;
        color: #334155;
    }

    .table-primary {
        background-color: #eff6ff !important;
        color: #1e40af;
    }

    .btn-sm {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        font-weight: 600;
    }

    .stat-card-mini {
        padding: 1.25rem;
        border-radius: 12px;
        background: #fff;
        border-left: 4px solid #4e73df;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--card-shadow);
    }

    .stat-card-mini i {
        font-size: 1.5rem;
        color: #cbd5e1;
    }

    .ppic-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        border-radius: 0 0 15px 15px;
    }

    .ppic-table-wrap table {
        min-width: 1100px;
        margin-bottom: 0;
    }

    .filter-count {
        background: #4e73df;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 10px;
        margin-left: 5px;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1e40af;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .filter-drawer {
        max-width: 360px;
        border-left: none;
    }

    .filter-drawer .offcanvas-header {
        border-bottom: 1px solid #edf2f7;
    }

    .filter-section-title {
        margin-bottom: 8px;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
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

<div class="page-content-wrapper container-fluid py-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle mr-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Welcome & Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="page-title-gradient">Laporan PPIC Harian</h3>
            <p class="text-muted">Kelola data perencanaan produksi dan monitoring SPK secara efisien.</p>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card-mini shadow-sm">
                <i class="fas fa-clipboard-list"></i>
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Entri</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($produksi) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-mini shadow-sm" style="border-left-color: #1cc88a;">
                <i class="fas fa-calendar-alt" style="color: #1cc88a;"></i>
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Update Terakhir</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= !empty($produksi) ? date('d/m/Y', strtotime($produksi[0]['tanggal'])) : '-' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <?php if (session()->get('role') == 'ppic') { ?>
                    <button class="btn btn-primary btn-sm px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addDataModal">
                        <i class="fas fa-plus mr-1"></i> Tambah Data
                    </button>
                <?php } ?>
                <a href="<?= base_url('export/dashboard') ?>" class="btn btn-outline-success btn-sm px-3 ml-2">
                    <i class="fas fa-file-excel mr-1"></i> Export
                </a>
            </div>

            <div class="d-flex align-items-center gap-2">
                <form method="get" action="<?= base_url('ppic') ?>" class="d-flex align-items-center gap-2 mb-0">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input
                            type="text"
                            name="q"
                            class="form-control border-right-0"
                            placeholder="Cari data..."
                            value="<?= esc($searchValue) ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary border-left-0" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <button
                    class="btn btn-light btn-sm ml-2"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#ppicFilterDrawer">
                    <i class="fas fa-sliders-h"></i> Filter
                    <?php if ($activeFilterCount > 0): ?>
                        <span class="filter-count"><?= $activeFilterCount ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>

        <?php if ($activeFilterCount > 0): ?>
            <div class="px-4 py-2 bg-light border-bottom d-flex gap-2 align-items-center">
                <span class="small text-muted font-weight-bold">Filter Aktif:</span>
                <?php if ($dateSummary !== ''): ?>
                    <span class="filter-chip">Tanggal: <?= esc($dateSummary) ?></span>
                <?php endif; ?>
                <a href="<?= base_url('ppic') ?>" class="text-danger small ml-2 text-decoration-none">Reset Filter</a>
            </div>
        <?php endif; ?>

        <div class="card-body p-0">
            <!-- Filter Drawer (Offcanvas) -->
            <div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="ppicFilterDrawer">
                <div class="offcanvas-header">
                    <div>
                        <h5 class="offcanvas-title mb-1" id="ppicFilterDrawerLabel">Filter PPIC</h5>
                        <p class="small text-muted mb-0">Pilih rentang tanggal untuk menyaring data.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form method="get" action="<?= base_url('ppic') ?>" class="d-flex flex-column gap-4">
                        <input type="hidden" name="q" value="<?= esc($searchValue) ?>">

                        <div>
                            <label class="filter-section-title">Rentang Tanggal</label>
                            <div class="d-flex flex-column gap-2 mt-2">
                                <input
                                    type="date"
                                    name="start_date"
                                    class="form-control"
                                    value="<?= esc($filterStartDate) ?>"
                                    placeholder="Dari">
                                <input
                                    type="date"
                                    name="end_date"
                                    class="form-control"
                                    value="<?= esc($filterEndDate) ?>"
                                    placeholder="Sampai">
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3">
                            <button class="btn btn-primary flex-fill" type="submit">Terapkan Filter</button>
                            <a href="<?= base_url('ppic') ?>" class="btn btn-outline-secondary flex-fill">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive ppic-table-wrap">

                            <table class="table table-hover border-0" id="dataTable" cellspacing="0">
                                <thead class="text-center">
                                    <tr>
                                        <th>Jam</th>
                                        <th>No. SPK</th>
                                        <th>Nama Mesin</th>
                                        <th>Nama Produk</th>
                                        <th>Grade</th>
                                        <th>Warna</th>
                                        <th>No. Mesin</th>
                                        <th>Operator</th>
                                        <th>Target</th>
                                        <th>Revisi</th>
                                        <th width="10%">Aksi</th>
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
                                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                                        <i class="far fa-calendar-alt text-muted"></i>
                                                        <strong class="text-uppercase small">
                                                            <?php
                                                            if (!empty($p['tanggal'])) {
                                                                $date = new DateTime($p['tanggal']);
                                                                $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                                                                echo esc($formatter->format($date));
                                                            }
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $currentDate = $p['tanggal'];
                                            $currentShift = '';
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
                                            <td class="fw-bold text-primary"><?= esc($p['jam']) ?></td>
                                            <td><span class="badge bg-light text-primary border px-2 py-1"><?= esc($p['no_spk']) ?></span></td>
                                            <td><?= esc($p['nama_mesin']) ?></td>
                                            <td><?= esc($p['nama_produk']) ?></td>
                                            <td><?= esc($p['grade']) ?></td>
                                            <td><?= esc($p['warna']) ?></td>
                                            <td><?= esc($p['nomor_mesin']) ?></td>
                                            <td><?= esc($p['operator']) ?></td>
                                            <td><?= esc($p['targett']) ?></td>

                                            <?php
                                            $revisiText = trim((string) ($p['revisi_display'] ?? $p['revisi'] ?? ''));
                                            ?>
                                            <td class="<?= $revisiText !== '' ? 'text-danger fw-bold' : 'text-muted' ?>">
                                                <?= $revisiText !== '' ? esc($revisiText) : '-' ?>
                                            </td>

                                            <td>
                                                <?php if (session()->get('role') == 'ppic'): ?>
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <button
                                                            type="button"
                                                            class="btn btn-warning btn-sm btnEdit"
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
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEdit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDelete"
                                                            data-id="<?= $p['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
        </div>
    </div>
</div>

        <div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <h5 class="modal-title font-weight-bold text-primary" id="addDataModalLabel">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Data PPIC
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form method="POST" action="<?= base_url('ppic/tambahData') ?>">
                        <?= csrf_field() ?>

                        <div class="modal-body p-4">
                            <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #f0f7ff;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary mr-3 fa-lg"></i>
                                    <span class="small text-primary font-weight-bold">Tip: Gunakan Master Data SPK untuk mengisi formulir secara otomatis.</span>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="filter-section-title">Master Data SPK</label>
                                    <select class="form-select form-select-lg border-primary shadow-sm" id="masterNoSpkSelect" style="border-radius: 12px; font-size: 1rem;">
                                        <option value="">-- Pilih No. SPK untuk autofill --</option>
                                        <?php foreach (($spk_master ?? []) as $master): ?>
                                            <option value="<?= esc($master['no_spk']) ?>">
                                                <?= esc($master['no_spk']) ?> - <?= esc($master['nama_produk']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Jam Operasional</label>
                                    <select class="form-select" name="jam" id="jamSelect" required>
                                        <option value="">-- Pilih Jam --</option>
                                        <?php
                                        $listJam = ["07-08", "08-09", "09-10", "10-11", "11-12", "12-13", "13-14", "14-15", "15-16", "16-17", "17-18", "18-19", "19-20", "20-21", "21-22", "22-23", "23-00", "00-01", "01-02", "03-04", "04-05", "05-06", "06-07"];
                                        foreach ($listJam as $j) :
                                        ?>
                                            <option value="<?= $j ?>"><?= $j ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">No. SPK</label>
                                    <input type="text" class="form-control" name="no_spk" id="add_no_spk" placeholder="Input atau pilih SPK...">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Shift</label>
                                    <select class="form-select" name="shif" id="shiftInput">
                                        <option value="">-- otomatis --</option>
                                        <option value="1">Shift 1 (07-14)</option>
                                        <option value="2">Shift 2 (14-22)</option>
                                        <option value="3">Shift 3 (22-06)</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="filter-section-title">Nama Mesin</label>
                                    <input type="text" class="form-control" name="nama_mesin" id="add_nama_mesin" placeholder="...">
                                </div>

                                <div class="col-md-6">
                                    <label class="filter-section-title">Nama Produk</label>
                                    <input type="text" class="form-control" name="nama_produk" id="add_nama_produk" placeholder="...">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Grade</label>
                                    <input type="text" class="form-control" name="grade" id="add_grade" placeholder="...">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Warna</label>
                                    <input type="text" class="form-control" name="warna" id="add_warna" placeholder="...">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Nomor Mesin</label>
                                    <input type="text" class="form-control" name="nomor_mesin" id="add_nomor_mesin" placeholder="...">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Operator</label>
                                    <input type="text" class="form-control" name="operator" id="add_operator" placeholder="...">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Target</label>
                                    <input type="text" class="form-control" name="targett" id="add_targett" placeholder="0">
                                </div>

                                <div class="col-md-4">
                                    <label class="filter-section-title">Tanggal</label>
                                    <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 p-4 pt-0">
                            <button class="btn btn-light px-4" type="button" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                            <button class="btn btn-primary px-4 shadow-sm" type="submit" style="border-radius: 10px;">Simpan Data</button>
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
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="filter-section-title">Jam</label>
                                <select class="form-select" name="jam" id="edit_jam" required>
                                    <option value="">-- Pilih Jam --</option>
                                    <?php foreach ($listJam as $j) : ?>
                                        <option value="<?= $j ?>"><?= $j ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="filter-section-title">No. SPK</label>
                                <input type="text" name="no_spk" id="edit_no_spk" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="filter-section-title">Nama Mesin</label>
                                <input type="text" name="nama_mesin" id="edit_nama_mesin" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="filter-section-title">Nama Produk</label>
                                <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="filter-section-title">Grade</label>
                                <input type="text" name="grade" id="edit_grade" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="filter-section-title">Warna</label>
                                <input type="text" name="warna" id="edit_warna" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="filter-section-title">Nomor Mesin</label>
                                <input type="text" name="nomor_mesin" id="edit_nomor_mesin" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="filter-section-title">Shift</label>
                                <select name="shif" id="edit_shif" class="form-select">
                                    <option value="">-- otomatis --</option>
                                    <option value="1">Shift 1 (07-14)</option>
                                    <option value="2">Shift 2 (14-22)</option>
                                    <option value="3">Shift 3 (22-06)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="filter-section-title">Operator</label>
                                <input type="text" name="operator" id="edit_operator" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="filter-section-title">Target</label>
                                <input type="text" name="targett" id="edit_targett" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="filter-section-title">Revisi</label>
                                <input type="text" name="revisi" id="edit_revisi" class="form-control" placeholder="Input revisi jika ada...">
                            </div>
                            <div class="col-md-6">
                                <label class="filter-section-title">Tanggal</label>
                                <input type="date" name="tanggal" id="edit_tanggal" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="border-radius: 10px;">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-circle text-danger fa-4x"></i>
                    </div>
                    <h5 class="font-weight-bold mb-2">Konfirmasi Hapus</h5>
                    <p class="text-muted small px-3">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                    
                    <form id="formDelete" method="post" class="mt-4">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger py-2" style="border-radius: 10px; font-weight: 600;">Hapus Sekarang</button>
                            <button type="button" class="btn btn-light py-2" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ppicSpkMaster = <?= json_encode($spk_master ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const ppicSpkMasterMap = Object.fromEntries(
            ppicSpkMaster.map((item) => [String(item.no_spk || '').trim(), item])
        );

        function applyPpicMasterData(noSpk) {
            const key = String(noSpk || '').trim();
            const master = ppicSpkMasterMap[key];
            if (!master) return;

            const fields = {
                'add_no_spk': master.no_spk,
                'add_nama_mesin': master.nama_mesin,
                'add_nama_produk': master.nama_produk,
                'add_grade': master.grade,
                'add_warna': master.warna,
                'add_nomor_mesin': master.nomor_mesin,
                'add_operator': master.operator,
                'add_targett': master.targett
            };

            for (const [id, value] of Object.entries(fields)) {
                const el = document.getElementById(id);
                if (el) el.value = value || '';
            }
        }

        // Master Data Select Handler
        const masterSelect = document.getElementById('masterNoSpkSelect');
        if (masterSelect) {
            masterSelect.addEventListener('change', function() {
                applyPpicMasterData(this.value);
            });
        }

        // Shift Auto-calculation
        const jamSelect = document.getElementById('jamSelect');
        if (jamSelect) {
            jamSelect.addEventListener('change', function() {
                const jam = this.value;
                const shiftInput = document.getElementById('shiftInput');
                if (!shiftInput) return;

                const s1 = ["07-08", "08-09", "09-10", "10-11", "11-12", "12-13", "13-14"];
                const s2 = ["14-15", "15-16", "16-17", "17-18", "18-19", "19-20", "20-21", "21-22"];
                const s3 = ["22-23", "23-00", "00-01", "01-02", "02-03", "03-04", "04-05", "05-06", "06-07"];

                if (s1.includes(jam)) shiftInput.value = "1";
                else if (s2.includes(jam)) shiftInput.value = "2";
                else if (s3.includes(jam)) shiftInput.value = "3";
                else shiftInput.value = "";
            });
        }

        // Edit Modal Handler
        const modalEdit = document.getElementById('modalEdit');
        if (modalEdit) {
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const id = btn.getAttribute('data-id');
                const fields = ['jam', 'no_spk', 'nama_mesin', 'nama_produk', 'grade', 'warna', 'nomor_mesin', 'shif', 'operator', 'targett', 'revisi', 'tanggal'];
                
                fields.forEach(field => {
                    const el = document.getElementById('edit_' + field);
                    if (el) el.value = btn.getAttribute('data-' + field) || '';
                });

                document.getElementById('edit_id').value = id;
                document.getElementById('formEdit').setAttribute('action', "<?= base_url('ppic/update/') ?>" + id);
            });
        }

        // Delete Modal Handler
        const modalDelete = document.getElementById('modalDelete');
        if (modalDelete) {
            modalDelete.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                document.getElementById('formDelete').setAttribute('action', "<?= base_url('ppic/delete/') ?>" + id);
            });
        }
    });
</script>

<?= $this->endSection() ?>