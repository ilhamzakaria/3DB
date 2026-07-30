<?php $modern_layout = true; ?>
<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --primary-modern: #4361ee;
        --secondary-modern: #2ec4b6;
        --danger-modern: #ef476f;
        --warning-modern: #ff9f1c;
        --bg-modern: #f4f7fe;
        --text-main: #2b3674;
        --text-muted: #a3aed1;
        --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.03), 0 2px 6px rgba(0, 0, 0, 0.04);
        --header-bg: #ffffff;
        --radius-lg: 20px;
        --radius-md: 14px;
        --transition: all 0.3s ease;
    }

    body {
        background-color: var(--bg-modern);
        font-family: 'Inter', 'Nunito', sans-serif;
        color: var(--text-main);
    }

    /* Page Transition Animation */
    .page-content-wrapper {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Modern Card */
    .card {
        background: #ffffff;
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        overflow: hidden;
    }

    .card-header {
        background-color: var(--header-bg);
        border-bottom: 1px solid #f0f2f5;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
        padding: 1.5rem 1.8rem;
    }

    .card-body {
        padding: 1.5rem 1.8rem;
    }



    /* Table Styles */
    .ppic-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }

    #dataTable {
        margin-bottom: 0;
        min-width: 1200px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table thead th {
        background-color: #f8fafc;
        border: none !important;
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 1.2rem 1rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table thead th:first-child {
        border-top-left-radius: var(--radius-md);
    }

    .table thead th:last-child {
        border-top-right-radius: var(--radius-md);
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(67, 97, 238, 0.02);
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f2f5;
        border-top: none;
        font-size: 0.9rem;
        color: #475569;
    }

    /* Row Group Headers */
    .table-secondary td {
        background-color: #f8fafc !important;
        border-bottom: 2px solid #e2e8f0;
    }

    .date-header {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #fff;
        padding: 0.4rem 1rem;
        border-radius: 99px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        color: var(--text-main);
    }

    .table-primary td {
        background-color: rgba(67, 97, 238, 0.04) !important;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        color: var(--primary-modern);
    }

    /* Buttons & Inputs */
    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 8px;
    }

    .btn-primary {
        background-color: var(--primary-modern);
        border-color: var(--primary-modern);
        box-shadow: 0 4px 10px rgba(67, 97, 238, 0.2);
    }

    .btn-primary:hover {
        background-color: #3651d4;
        border-color: #3651d4;
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-outline-success {
        color: var(--secondary-modern);
        border-color: var(--secondary-modern);
    }

    .btn-outline-success:hover {
        background-color: var(--secondary-modern);
        border-color: var(--secondary-modern);
        color: white;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 1rem;
        transition: var(--transition);
        font-size: 0.9rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-modern);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .input-group .form-control {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .input-group .btn {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    /* Badges & Chips */
    .badge-modern {
        padding: 0.35em 0.8em;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.02em;
        font-size: 0.8rem;
    }

    .badge-spk {
        background: rgba(67, 97, 238, 0.1) !important;
        color: #4361ee !important;
        border: 1px solid rgba(67, 97, 238, 0.3) !important;
        font-weight: 700 !important;
    }

    .filter-count {
        background: var(--danger-modern);
        color: white;
        border-radius: 50%;
        padding: 0 6px;
        font-size: 10px;
        min-width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 6px;
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 99px;
        background: rgba(67, 97, 238, 0.1);
        color: var(--primary-modern);
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Drawers */
    .filter-drawer {
        border-left: none;
        box-shadow: -5px 0 25px rgba(0, 0, 0, 0.05);
    }

    .filter-drawer .offcanvas-header {
        border-bottom: 1px solid #f0f2f5;
        padding: 1.5rem;
    }

    .filter-section-title {
        margin-bottom: 8px;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .revisi-text {
        font-size: 0.85rem;
        line-height: 1.4;
    }

    .revisi-item {
        display: inline-block;
        background: rgba(239, 71, 111, 0.1);
        color: var(--danger-modern);
        padding: 2px 8px;
        border-radius: 4px;
        margin: 2px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Instagram Style Modal */
    .ig-modal .modal-content {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        width: 300px;
        margin: auto;
    }

    .ig-modal-body {
        padding: 32px 24px;
        text-align: center;
    }

    .ig-modal-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #262626;
    }

    .ig-modal-text {
        font-size: 0.9rem;
        color: #8e8e8e;
        line-height: 1.4;
    }

    .ig-btn-list {
        display: flex;
        flex-direction: column;
        border-top: 1px solid #dbdbdb;
    }

    .ig-btn {
        padding: 12px;
        background: none;
        border: none;
        border-bottom: 1px solid #dbdbdb;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.1s;
        text-decoration: none !important;
        text-align: center;
    }

    .ig-btn:last-child {
        border-bottom: none;
    }

    .ig-btn:active {
        background: #fafafa;
    }

    .ig-btn-danger {
        color: #ed4956;
        font-weight: 700;
        border: none;
        background: none;
    }

    .ig-btn-secondary {
        color: #262626;
        font-weight: 400;
    }
</style>

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

<?php $modern_layout = true; ?>
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



    <!-- Main Card -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <?php if (session()->get('role') == 'ppic') { ?>
                    <button class="btn btn-primary btn-sm px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addDataModal">
                        <i class="fas fa-plus mr-1"></i> Tambah Data
                    </button>

                    <button class="btn btn-outline-danger btn-sm px-3 ml-2" data-bs-toggle="modal" data-bs-target="#trashModal" id="btn_open_trash">
                        <i class="fas fa-trash-alt mr-1"></i> Tempat Sampah
                    </button>

                <?php } ?>
                <a href="<?= base_url('export/dashboard') ?>" class="btn btn-outline-success btn-sm px-3 ml-2">
                    <i class="fas fa-file-excel mr-1"></i> Export
                </a>

            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm" type="button" id="periodFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-calendar-alt"></i>
                        <?php
                        if ($filter === 'day') echo 'Hari Ini';
                        elseif ($filter === 'week') echo 'Minggu';
                        elseif ($filter === 'month') echo 'Bulan';
                        else echo 'Periode';
                        ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" aria-labelledby="periodFilterDropdown" style="border-radius: 12px;">
                        <li><a class="dropdown-item py-2 <?= ($filter === 'day') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('ppic?filter=day') ?>"><i class="fas fa-calendar-day mr-2 opacity-50"></i> Hari Ini</a></li>
                        <li><a class="dropdown-item py-2 <?= ($filter === 'week') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('ppic?filter=week') ?>"><i class="fas fa-calendar-week mr-2 opacity-50"></i> Minggu</a></li>
                        <li><a class="dropdown-item py-2 <?= ($filter === 'month') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('ppic?filter=month') ?>"><i class="fas fa-calendar-alt mr-2 opacity-50"></i> Bulan</a></li>
                        <?php if ($filter): ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('ppic') ?>"><i class="fas fa-sync-alt mr-2 opacity-50"></i> Reset Filter</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <form method="get" action="<?= base_url('ppic') ?>" class="d-flex align-items-center gap-2 mb-0">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input
                            type="text"
                            name="q"
                            class="form-control border-right-0"
                            placeholder="Cari..."
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
                    <i class="fas fa-sliders-h"></i> Custom
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
                            <th width="140px">Aksi</th>
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
                                        <div class="d-flex align-items-center justify-content-center">
                                            <span class="date-header">
                                                <i class="far fa-calendar-alt text-primary"></i>
                                                <strong class="text-uppercase small font-weight-bold">
                                                    <?php
                                                    if (!empty($p['tanggal'])) {
                                                        $date = new DateTime($p['tanggal']);
                                                        $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                                                        echo esc($formatter->format($date));
                                                    }
                                                    ?>
                                                </strong>
                                            </span>
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
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-clock text-primary"></i>
                                            <strong class="small fw-bold text-primary">
                                                SHIFT <?= $p['shif']; ?>
                                                <?= isset($shiftRanges[$p['shif']]) ? '(' . $shiftRanges[$p['shif']] . ')' : '' ?>
                                            </strong>
                                        </div>
                                    </td>
                                </tr>
                                <?php $currentShift = $p['shif']; ?>
                            <?php endif; ?>

                            <tr>
                                <td class="fw-bold text-primary"><?= esc($p['jam']) ?></td>
                                <td><span class="badge badge-modern badge-spk"><?= esc($p['no_spk']) ?></span></td>
                                <td><?= esc($p['nama_mesin']) ?></td>
                                <td><?= esc($p['nama_produk']) ?></td>
                                <td><?= esc($p['grade']) ?></td>
                                <td><?= esc($p['warna']) ?></td>
                                <td><?= esc($p['nomor_mesin']) ?></td>
                                <td><?= esc($p['operator']) ?></td>
                                <td class="fw-bold"><?= esc($p['targett']) ?></td>

                                <?php
                                $revisiText = trim((string) ($p['revisi_display'] ?? $p['revisi'] ?? ''));
                                $isRevisi = $revisiText !== '';
                                ?>
                                <td class="revisi-text text-center">
                                    <?php if ($isRevisi): ?>
                                        <?php
                                        // Split revisi text if it contains multiple items separated by |
                                        $revisiItems = explode(' | ', $revisiText);
                                        foreach ($revisiItems as $item):
                                        ?>
                                            <span class="revisi-item"><?= esc($item) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (session()->get('role') == 'ppic'): ?>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm btnEdit d-flex align-items-center gap-1"
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
                                                data-bs-target="#modalEdit"
                                                style="border-radius: 8px; font-weight: 600; font-size: 11px; padding: 5px 10px;">
                                                <i class="fas fa-edit" style="font-size: 10px;"></i> Edit
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm d-flex align-items-center gap-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDelete"
                                                data-id="<?= $p['id'] ?>"
                                                data-spk="<?= esc($p['no_spk']) ?>"
                                                style="border-radius: 8px; font-weight: 600; font-size: 11px; padding: 5px 10px;">
                                                <i class="fas fa-trash" style="font-size: 10px;"></i> Hapus
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Menampilkan <?= count($produksi) ?> data
                </div>
                <div class="pagination-modern">
                    <?= $pager->links('ppic', 'bootstrap_pagination') ?>
                </div>
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
                            <label class="filter-section-title">Revisi (R1)</label>
                            <input type="text" class="form-control" name="revisi_1" id="add_revisi_1" placeholder="Input revisi jika ada...">
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-primary" id="modalEditLabel">
                    <i class="fas fa-edit mr-2"></i> Edit Data Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEdit" method="POST" action="<?= base_url('ppic/update') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="filter-section-title">Jam Operasional</label>
                            <select class="form-select" name="jam" id="edit_jam" required>
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
                            <input type="text" class="form-control" name="no_spk" id="edit_no_spk" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Shift</label>
                            <select class="form-select" name="shif" id="edit_shif">
                                <option value="">-- otomatis --</option>
                                <option value="1">Shift 1 (07-14)</option>
                                <option value="2">Shift 2 (14-22)</option>
                                <option value="3">Shift 3 (22-06)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="filter-section-title">Nama Mesin</label>
                            <input type="text" class="form-control" name="nama_mesin" id="edit_nama_mesin" placeholder="...">
                        </div>

                        <div class="col-md-6">
                            <label class="filter-section-title">Nama Produk</label>
                            <input type="text" class="form-control" name="nama_produk" id="edit_nama_produk" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Grade</label>
                            <input type="text" class="form-control" name="grade" id="edit_grade" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Warna</label>
                            <input type="text" class="form-control" name="warna" id="edit_warna" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Nomor Mesin</label>
                            <input type="text" class="form-control" name="nomor_mesin" id="edit_nomor_mesin" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Operator</label>
                            <input type="text" class="form-control" name="operator" id="edit_operator" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Target</label>
                            <input type="text" class="form-control" name="targett" id="edit_targett" placeholder="0">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="edit_tanggal">
                        </div>

                        <div class="col-md-12">
                            <label class="filter-section-title">Revisi / Catatan</label>
                            <input type="text" class="form-control" name="revisi" id="edit_revisi" placeholder="Input revisi jika ada...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button class="btn btn-light px-4" type="button" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button class="btn btn-primary px-4 shadow-sm" type="submit" style="border-radius: 10px;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Instagram Style Delete -->
<div class="modal fade ig-modal" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="ig-modal-body">
                <div class="ig-modal-title">Hapus Data PPIC?</div>
                <div class="ig-modal-text">Data PPIC No. SPK <span id="deleteSpkName" class="font-weight-bold"></span> akan dipindahkan ke tempat sampah.</div>
            </div>
            <div class="ig-btn-list">
                <form id="formDelete" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="ig-btn ig-btn-danger w-100">Hapus</button>
                </form>
                <button type="button" class="ig-btn ig-btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
<!-- Trash Modal -->
<div class="modal fade" id="trashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-secondary">
                    <i class="fas fa-trash-alt mr-2"></i> Tempat Sampah PPIC
                </h5>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="table_trash" style="min-width: auto; table-layout: auto; width: 100%;">
                        <thead class="bg-white">
                            <tr class="text-xs text-muted text-uppercase">
                                <th class="px-3 py-3" style="width: 40%;">SPK / Produk</th>
                                <th class="py-3 text-center" style="width: 35%;">Dihapus Pada</th>
                                <th class="text-end py-3 px-3" style="width: 25%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
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
                const spk = btn.getAttribute('data-spk');

                document.getElementById('deleteSpkName').textContent = spk;
                document.getElementById('formDelete').setAttribute('action', "<?= base_url('ppic/delete/') ?>" + id);
            });
        }
        // Recycle Bin Loading
        const btnOpenTrash = document.getElementById('btn_open_trash');
        if (btnOpenTrash) {
            btnOpenTrash.addEventListener('click', function() {
                const tbody = document.querySelector('#table_trash tbody');
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';
                fetch('<?= base_url('ppic/get_trash') ?>')
                    .then(r => r.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Tempat sampah kosong.</td></tr>';
                            return;
                        }
                        data.forEach(p => {
                            const html = `
                                <tr style="font-size: 13px;">
                                    <td class="px-3 py-2">
                                        <div class="font-weight-bold" style="margin-bottom: -2px;">${p.no_spk}</div>
                                        <div class="text-muted" style="font-size: 11px;">${p.nama_mesin} - ${p.nama_produk}</div>
                                    </td>
                                    <td class="py-2 text-center text-muted">${p.deleted_at}</td>
                                    <td class="text-end py-2 px-3">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="<?= base_url('ppic/restore') ?>/${p.id}" class="btn btn-success btn-xs px-2" style="font-size: 10px; padding: 2px 8px;">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                            <a href="<?= base_url('ppic/delete_permanent') ?>/${p.id}" class="btn btn-outline-danger btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" onclick="return confirm('Hapus permanen?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>`;
                            tbody.insertAdjacentHTML('beforeend', html);
                        });
                    });
            });
        }
    });
</script>

<!-- <?= $this->endSection() ?> -->