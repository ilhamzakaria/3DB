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

    .page-content-wrapper {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

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

    .production-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem 1.25rem;
    }

    .toolbar-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    .toolbar-btn {
        min-height: 40px;
        padding: 0.55rem 1rem;
        border-radius: 12px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .toolbar-btn i {
        font-size: 0.85rem;
    }

    .toolbar-search-form {
        margin: 0;
    }

    .toolbar-search {
        width: 240px;
    }

    .toolbar-search .input-group {
        width: 100%;
        flex-wrap: nowrap;
    }

    .toolbar-search .form-control,
    .toolbar-search .btn {
        min-height: 40px;
    }

    .toolbar-search .form-control {
        border-radius: 12px 0 0 12px !important;
    }

    .toolbar-search .btn {
        width: 42px;
        padding: 0;
        border-radius: 0 12px 12px 0 !important;
        flex: 0 0 42px;
    }

    .toolbar-filter-menu {
        border-radius: 12px;
        overflow: hidden;
    }

    .plant-table-wrap {
        overflow-x: auto !important;
        width: 100%;
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }

    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-main {
        min-width: 1000px;
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
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: #fcfdfe;
    }

    .table td {
        padding: 1.2rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #f0f2f5;
        color: #4a5568;
        font-size: 0.875rem;
    }

    .badge-modern {
        padding: 0.4em 0.8em;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-shift-1 { background: #4361ee; color: #ffffff; }
    .badge-shift-2 { background: #ff9f1c; color: #ffffff; }
    .badge-shift-3 { background: #ef476f; color: #ffffff; }

    .date-divider {
        background-color: #f8fafc;
        font-weight: 700;
        color: var(--text-main);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .shift-divider {
        background-color: #ffffff;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: var(--transition);
        border: none;
    }

    .btn-edit { background: rgba(67, 97, 238, 0.1); color: var(--primary-modern); }
    .btn-edit:hover { background: var(--primary-modern); color: white; }
    .btn-detail { background: rgba(46, 196, 182, 0.1); color: var(--secondary-modern); }
    .btn-detail:hover { background: var(--secondary-modern); color: white; }
    .btn-delete { background: rgba(239, 71, 111, 0.1); color: var(--danger-modern); }
    .btn-delete:hover { background: var(--danger-modern); color: white; }

    /* Modal Styling */
    .modal-content {
        border-radius: var(--radius-lg);
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .modal-header {
        border-bottom: 1px solid #f0f2f5;
        padding: 1.5rem 2rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
        font-size: 0.9rem;
    }

    .form-control:focus {
        border-color: var(--primary-modern);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--primary-modern);
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f0f2f5;
    }

    .signature-box {
        border: 1px dashed #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: var(--transition);
        cursor: pointer;
    }

    .signature-box:hover {
        border-color: var(--primary-modern);
        background: rgba(67, 97, 238, 0.02);
    }

    .signature-preview {
        max-height: 50px;
        margin-bottom: 0.3rem;
    }

    .upload-text {
        font-size: 9px;
        color: var(--text-muted);
        display: block;
        margin-bottom: 2px;
    }

    /* Filter Drawer */
    .filter-drawer {
        border-left: none;
        box-shadow: -5px 0 25px rgba(0,0,0,0.05);
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

    @media (max-width: 991.98px) {
        .page-content-wrapper {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .card-header {
            padding: 1.25rem;
        }

        .production-toolbar,
        .toolbar-group {
            align-items: stretch;
        }

        .toolbar-group {
            width: 100%;
        }

        .toolbar-group:last-child {
            justify-content: flex-start;
        }

        .toolbar-search {
            width: 100%;
            min-width: 0;
            flex: 1 1 220px;
        }
    }

    @media (max-width: 575.98px) {
        .toolbar-btn,
        .toolbar-search,
        .toolbar-group .dropdown,
        .toolbar-group form {
            width: 100%;
        }

        .toolbar-group .dropdown .btn,
        .toolbar-group > .btn {
            width: 100%;
        }
    }

    /* Minimal local fallback for Bootstrap 5 components when CDN JS/CSS is unavailable */
    .dropdown-menu.show {
        display: block;
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1055;
        display: none;
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        overflow-y: auto;
        outline: 0;
    }

    .modal.show {
        display: block;
    }

    .modal-backdrop,
    .offcanvas-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1050;
        width: 100vw;
        height: 100vh;
        background-color: #000;
    }

    .modal-backdrop.show,
    .offcanvas-backdrop.show {
        opacity: 0.5;
    }

    .offcanvas {
        position: fixed;
        bottom: 0;
        z-index: 1045;
        display: flex;
        flex-direction: column;
        max-width: 100%;
        visibility: hidden;
        background-color: #fff;
        background-clip: padding-box;
        outline: 0;
        transition: transform .3s ease-in-out;
    }

    .offcanvas-end {
        top: 0;
        right: 0;
        width: 400px;
        border-left: 1px solid rgba(0, 0, 0, .2);
        transform: translateX(100%);
    }

    .offcanvas.show {
        transform: none;
        visibility: visible;
    }

    .offcanvas-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
    }

    .offcanvas-body {
        flex-grow: 1;
        padding: 1rem;
        overflow-y: auto;
    }

    .btn-close {
        box-sizing: content-box;
        width: 1em;
        height: 1em;
        padding: .25em;
        color: #000;
        background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 0 1 1.414 0L8 6.586 14.293.293a1 1 0 1 1 1.414 1.414L9.414 8l6.293 6.293a1 1 0 0 1-1.414 1.414L8 9.414l-6.293 6.293A1 1 0 0 1 .293 14.293L6.586 8 .293 1.707A1 1 0 0 1 .293.293z'/%3e%3c/svg%3e") center / 1em auto no-repeat;
        border: 0;
        border-radius: .25rem;
        opacity: .5;
    }
</style>

<div class="page-content-wrapper px-md-5 px-4">

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-lg mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header py-3 bg-white">
            <div class="production-toolbar">
                <div class="toolbar-group">
                    <?php if (session('role') == 'produksi') : ?>
                        <button class="btn btn-primary btn-sm shadow-sm toolbar-btn" data-bs-toggle="modal" data-bs-target="#addDataModal">
                            <i class="fas fa-plus me-1"></i> Tambah Laporan
                        </button>
                    <?php endif; ?>
                    <a href="<?= base_url('export/produksi') ?>" class="btn btn-outline-success btn-sm shadow-sm toolbar-btn">
                        <i class="fas fa-file-excel me-1"></i> Export
                    </a>
                    <?php if (session('role') == 'produksi') : ?>
                        <button class="btn btn-outline-secondary btn-sm toolbar-btn" data-bs-toggle="modal" data-bs-target="#trashModal" id="btn_open_trash">
                            <i class="fas fa-trash-restore me-1"></i> Sampah
                        </button>
                    <?php endif; ?>
                </div>

            <div class="toolbar-group">
                <div class="dropdown">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm toolbar-btn" type="button" id="periodFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php
                        if ($filter === 'day') echo 'Hari Ini';
                        elseif ($filter === 'week') echo 'Minggu Ini';
                        elseif ($filter === 'month') echo 'Bulan Ini';
                        else echo 'Semua Periode';
                        ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg toolbar-filter-menu" aria-labelledby="periodFilterDropdown">
                        <li><a class="dropdown-item py-2 <?= ($filter === 'day') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('produksi?filter=day') ?>"><i class="fas fa-calendar-day me-2 opacity-50"></i> Hari Ini</a></li>
                        <li><a class="dropdown-item py-2 <?= ($filter === 'week') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('produksi?filter=week') ?>"><i class="fas fa-calendar-week me-2 opacity-50"></i> Minggu Ini</a></li>
                        <li><a class="dropdown-item py-2 <?= ($filter === 'month') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('produksi?filter=month') ?>"><i class="fas fa-calendar-alt me-2 opacity-50"></i> Bulan Ini</a></li>
                        <?php if ($filter): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('produksi') ?>"><i class="fas fa-sync-alt me-2 opacity-50"></i> Reset Filter</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <form action="<?= base_url('produksi') ?>" method="get" class="toolbar-search-form">
                    <div class="input-group input-group-sm toolbar-search">
                        <input type="text" name="q" class="form-control border-end-0" placeholder="Cari SPK/Mesin..." value="<?= esc($q ?? '') ?>">
                        <button class="btn btn-outline-primary border-start-0" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <button class="btn btn-light btn-sm toolbar-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#plantFilterDrawer">
                    <i class="fas fa-sliders-h me-1"></i> Custom
                    <?php 
                    $activeFilterCount = 0;
                    if ($filter_shift) $activeFilterCount++;
                    if ($filter_tanggal) $activeFilterCount++;
                    if ($activeFilterCount > 0): ?>
                        <span class="filter-count"><?= $activeFilterCount ?></span>
                    <?php endif; ?>
                </button>
            </div>
            </div>
        </div>

        <div class="plant-table-wrap">
            <table class="table table-main">
                <thead>
                    <tr>
                        <th>No. SPK</th>
                        <th>Mesin</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Grup</th>
                        <th>Operator</th>
                        <th class="text-right">Hasil Bagus</th>
                        <th class="text-right">Reject</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produksi)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-20"></i>
                                <p class="mb-0">Tidak ada data produksi yang ditemukan.</p>
                            </td>
                        </tr>
                    <?php else: 
                        $currentDate = '';
                        $currentShift = '';
                        foreach ($produksi as $p): 
                            if ($currentDate != $p['tanggal']): 
                                $currentDate = $p['tanggal'];
                                $currentShift = ''; 
                                $formattedDate = date('d M Y', strtotime($currentDate));
                    ?>
                        <tr class="date-divider text-center">
                            <td colspan="8"><i class="far fa-calendar-alt me-2"></i> <?= $formattedDate ?></td>
                        </tr>
                    <?php endif; ?>

                    <?php if ($currentShift != $p['shift']): 
                        $currentShift = $p['shift'];
                    ?>
                        <tr class="shift-divider">
                            <td colspan="8" class="ps-4">
                                <span class="badge badge-modern badge-shift-<?= $p['shift'] ?>">
                                    <i class="fas fa-clock me-1"></i> SHIFT <?= $p['shift'] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr>
                        <td>
                            <span class="badge badge-modern badge-spk" style="background: rgba(67, 97, 238, 0.1) !important; color: #4361ee !important; border: 1px solid rgba(67, 97, 238, 0.3) !important; font-weight: 700 !important; padding: 0.35em 0.8em; border-radius: 6px; font-size: 0.8rem;">
                                <?= esc($p['nomor_spk']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                    <i class="fas fa-cog text-muted small"></i>
                                </div>
                                <div>
                                    <div class="font-weight-600"><?= esc($p['nama_mesin']) ?></div>
                                    <div class="text-xs text-muted">M#<?= esc($p['nomor_mesin']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= esc($p['nama_produksi']) ?></td>
                        <td class="text-center">
                            <span class="badge badge-pill badge-light text-muted small px-3"><?= esc($p['grup'] ?? '-') ?></span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="far fa-user me-2 text-muted small"></i>
                                <span class="small font-weight-600"><?= esc($p['operator']) ?></span>
                            </div>
                        </td>
                        <td class="text-right font-weight-bold text-success"><?= number_format($p['grand_total_bagus']) ?></td>
                        <td class="text-right text-danger font-weight-bold"><?= number_format($p['grand_total_reject']) ?></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn-action btn-detail btn_view_detail" data-id="<?= $p['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalDetail" title="Detail">
                                    <i class="fas fa-eye small"></i>
                                </button>
                                <?php if (session('role') == 'produksi') : ?>
                                    <button class="btn-action btn-edit btn_edit_laporan" data-id="<?= $p['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalEdit" title="Edit">
                                        <i class="fas fa-edit small"></i>
                                    </button>
                                    <button class="btn-action btn-delete btn_confirm_delete" data-id="<?= $p['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalDeleteConfirm" title="Hapus">
                                        <i class="fas fa-trash-alt small"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Menampilkan <?= count($produksi) ?> data
                </div>
                <?= $pager->links('produksi', 'bootstrap_pagination') ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Data Modal -->
<div class="modal fade" id="addDataModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Laporan Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddLaporan" action="<?= base_url('produksi/simpan') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="section-title"><i class="fas fa-info-circle"></i> Informasi Utama</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Nomor SPK</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input check_spk_baru" type="checkbox" id="check_spk_baru_add">
                                    <label class="form-check-label text-xs" for="check_spk_baru_add">Baru</label>
                                </div>
                            </div>
                            <div class="spk_select_wrapper">
                                <select name="nomor_spk" class="form-select select2 nomor_spk" required>
                                    <option value="">-- Pilih SPK --</option>
                                    <?php foreach ($spk_list as $spk): ?>
                                        <option value="<?= $spk['no_spk'] ?>" data-mesin="<?= $spk['nama_mesin'] ?>" data-produk="<?= $spk['nama_produk'] ?>">
                                            <?= $spk['no_spk'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="spk_input_wrapper" style="display:none;">
                                <input type="text" class="form-control nomor_spk_baru" placeholder="Input SPK Baru">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nama Mesin</label>
                            <input type="text" name="nama_mesin" class="form-control nama_mesin">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nama Produksi</label>
                            <input type="text" name="nama_produksi" class="form-control nama_produksi">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Batch Number</label>
                            <input type="text" name="batch_number" class="form-control batch_number">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Shift</label>
                            <select name="shift" class="form-select shift">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Grup</label>
                            <input type="text" name="grup" class="form-control grup">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">No. Mesin</label>
                            <input type="text" name="nomor_mesin" class="form-control nomor_mesin">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Packing</label>
                            <input type="text" name="packing" class="form-control packing">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Cycle Time</label>
                            <input type="text" name="cycle_time" class="form-control cycle_time">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Target</label>
                            <input type="number" name="target" class="form-control target">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Operator</label>
                            <input type="text" name="operator" class="form-control operator">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-7">
                            <div class="section-title d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-clock"></i> Data Produksi Per Jam</span>
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm select_jam" style="width: 120px;">
                                        <option value="">-- Jam --</option>
                                        <?php 
                                        $hours = ['06-07','07-08','08-09','09-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-00','00-01','01-02','02-03','03-04','04-05','05-06'];
                                        foreach ($hours as $h): ?>
                                            <option value="<?= $h ?>"><?= $h ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-primary btn-sm btn_add_jam_row"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="table-responsive border rounded-lg bg-light" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm table_jam_form mb-0">
                                    <thead class="bg-white">
                                        <tr>
                                            <th class="px-3" width="100">Jam</th>
                                            <th>Hasil (pcs)</th>
                                            <th class="text-center" width="50">#</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="bg-white sticky-bottom">
                                        <tr class="font-weight-bold">
                                            <td class="px-3">Total:</td>
                                            <td class="grand_total_hasil_display">0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="section-title d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-exclamation-triangle"></i> Kualitas & Reject</span>
                                <button type="button" class="btn btn-danger btn-sm btn_open_reject_modal" data-bs-toggle="modal" data-bs-target="#modalReject" data-target-storage="reject_storage_add">
                                    <i class="fas fa-plus mr-1"></i> Reject
                                </button>
                            </div>
                            <div class="p-3 border rounded-lg bg-light">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label text-xs">Total Bagus</label>
                                        <input type="number" name="grand_total_bagus" class="form-control grand_total_bagus bg-white font-weight-bold text-primary" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-xs">Total Reject</label>
                                        <input type="number" name="grand_total_reject" class="form-control grand_total_reject bg-white font-weight-bold text-danger" readonly>
                                        <div class="reject_storage" id="reject_storage_add"></div>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-xs">Sisa PO</label>
                                        <input type="number" name="sisa_po" class="form-control sisa_po">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-xs">Hold</label>
                                        <input type="number" name="hold" class="form-control hold">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-xs">Gumpalan</label>
                                        <input type="number" name="gumpalan" class="form-control gumpalan">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-xs">Isi</label>
                                        <input type="number" name="isi" class="form-control isi">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-title"><i class="fas fa-boxes"></i> Material, Colorant & Packaging</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-5 border-right">
                            <label class="form-label text-primary text-xs">Primary Material</label>
                            <div class="row g-2">
                                <div class="col-7">
                                    <input type="text" name="materials[0][merek_kode]" placeholder="Merek Kode" class="form-control form-control-sm merek_kode">
                                </div>
                                <div class="col-5">
                                    <input type="number" step="0.01" name="materials[0][pemakaian]" placeholder="Kg" class="form-control form-control-sm m_pemakaian">
                                </div>
                                <div class="col-3"><input type="text" name="materials[0][lot_a]" placeholder="Lot A" class="form-control form-control-xs"></div>
                                <div class="col-3"><input type="text" name="materials[0][lot_b]" placeholder="Lot B" class="form-control form-control-xs"></div>
                                <div class="col-3"><input type="text" name="materials[0][lot_c]" placeholder="Lot C" class="form-control form-control-xs"></div>
                                <div class="col-3"><input type="text" name="materials[0][lot_d]" placeholder="Lot D" class="form-control form-control-xs"></div>
                            </div>
                        </div>
                        <div class="col-md-4 border-right">
                            <label class="form-label text-info text-xs">Colorant</label>
                            <div class="row g-2">
                                <div class="col-12"><input type="text" name="colorants[0][code]" placeholder="Color Code" class="form-control form-control-sm"></div>
                                <div class="col-7"><input type="text" name="colorants[0][nomor_lot]" placeholder="Lot No" class="form-control form-control-sm"></div>
                                <div class="col-5"><input type="number" step="0.01" name="colorants[0][pemakaian]" placeholder="Kg" class="form-control form-control-sm"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-warning text-xs">Packaging</label>
                            <div class="mb-2"><input type="number" name="box_karung_nicktainer" placeholder="Box (pcs)" class="form-control form-control-sm"></div>
                            <div><input type="number" name="plastik" placeholder="Plastik (pcs)" class="form-control form-control-sm"></div>
                        </div>
                    </div>

                    <div class="section-title d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-stopwatch"></i> Downtime & Machine Stops</span>
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn_add_dt"><i class="fas fa-plus mr-1"></i> Downtime</button>
                    </div>
                    <div class="table-responsive border rounded-lg mb-4">
                        <table class="table table-sm table_downtime_form mb-0">
                            <thead class="bg-light">
                                <tr class="text-xs">
                                    <th class="px-3">Alasan</th>
                                    <th>Mulai</th>
                                    <th>Selesai</th>
                                    <th width="80">Mnt</th>
                                    <th width="40">#</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="section-title"><i class="fas fa-comment-alt"></i> Catatan & Tanda Tangan</div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <textarea name="catatan" class="form-control h-100" rows="5" placeholder="Catatan produksi..."></textarea>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-2">
                                <?php 
                                $labels = ['Shift 1', 'Shift 2', 'Shift 3', 'Supervisor'];
                                $fields = ['ttd_shift_1', 'ttd_shift_2', 'ttd_shift_3', 'ttd_spv'];
                                foreach ($fields as $idx => $f): 
                                ?>
                                <div class="col-3 text-center">
                                    <div class="signature-box" onclick="this.querySelector('input').click()">
                                        <img class="signature-preview img-fluid" src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 50'%3E%3Crect width='100' height='50' fill='%23f0f2f5'/%3E%3Ctext x='50' y='25' font-family='sans-serif' font-size='12' fill='%23a3aed1' text-anchor='middle' dominant-baseline='middle'%3EPreview%3C/text%3E%3C/svg%3E">
                                        <span class="upload-text">upload ttd</span>
                                        <input type="file" name="<?= $f ?>" class="d-none sig-input" onchange="previewSig(this)">
                                        <div class="text-xs font-weight-bold"><?= $labels[$idx] ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Data Modal (Structure same as Add) -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary">
                    <i class="fas fa-edit mr-2"></i> Edit Laporan Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formEditLaporan" action="" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <!-- Content will be same as Add Modal, but populated via JS -->
                    <div id="editModalContent"></div>
                    <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success px-5 shadow-sm">Update Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Detail Data Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-primary">
                    <i class="fas fa-info-circle mr-2"></i> Detail Laporan Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailModalContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Instagram Style Delete Modal -->
<div class="modal fade" id="modalDeleteConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" style="max-width: 320px;">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-2">Hapus Laporan?</h5>
                <p class="text-muted small mb-0">Data akan dipindahkan ke tempat sampah. Anda dapat memulihkannya nanti.</p>
            </div>
            <div class="d-flex flex-column border-top">
                <a href="#" id="btn_final_delete" class="py-3 text-danger fw-bold text-decoration-none border-bottom hover-bg-light">Hapus</a>
                <button type="button" id="btn_cancel_delete" class="py-3 text-dark text-decoration-none bg-transparent border-0 hover-bg-light" data-bs-dismiss="modal">Batalkan</button>
            </div>
        </div>
    </div>
</div>

<style>
.hover-bg-light:hover { background-color: #f8f9fa; }
</style>
<div class="modal fade" id="modalReject" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold">Input Reject</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 400px; overflow-y: auto;">
                <?php 
                $rejectTypes = ["Start Up", "Karantina", "Trial", "Camera", "Button Putih", "Oval", "Flashing", "Short Shoot", "Kotor", "Beda Warna", "Sampling QC", "Kontaminasi", "Black Spot", "Gosong", "Struktur Tidak Standar", "Inject Point Tidak Standar", "Bolong", "Bubble", "Berair", "Neck Panjang"];
                foreach ($rejectTypes as $type): ?>
                <div class="row mb-2 align-items-center">
                    <div class="col-7"><label class="small fw-bold text-muted mb-0"><?= $type ?></label></div>
                    <div class="col-5">
                        <input type="number" class="form-control form-control-sm input-reject-detail" data-type="<?= $type ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger px-4" id="btn_save_reject_generic" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Drawer -->
<div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="plantFilterDrawer">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title font-weight-bold"><i class="fas fa-filter mr-2"></i> Custom Filter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="<?= base_url('produksi') ?>" method="get">
            <div class="mb-4">
                <label class="form-label">Tanggal Spesifik</label>
                <input type="date" name="tanggal" class="form-control" value="<?= esc($filter_tanggal ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="form-label">Shift</label>
                <select name="shift" class="form-select">
                    <option value="">Semua Shift</option>
                    <option value="1" <?= $filter_shift == '1' ? 'selected' : '' ?>>Shift 1</option>
                    <option value="2" <?= $filter_shift == '2' ? 'selected' : '' ?>>Shift 2</option>
                    <option value="3" <?= $filter_shift == '3' ? 'selected' : '' ?>>Shift 3</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="<?= base_url('produksi') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Trash Modal -->
<div class="modal fade" id="trashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title font-weight-bold text-secondary">
                    <i class="fas fa-trash-alt mr-2"></i> Tempat Sampah Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="table_trash">
                        <thead class="bg-white">
                            <tr class="text-xs text-muted text-uppercase">
                                <th class="px-3 py-3">SPK / Mesin</th>
                                <th class="py-3">Dihapus Pada</th>
                                <th class="text-center py-3" width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let activeRejectStorage = null;
let jamRowCount = 1000;
let dtRowCount = 1000;

function initBootstrapFallback() {
    if (window.bootstrap && window.bootstrap.Modal && window.bootstrap.Dropdown && window.bootstrap.Offcanvas) return;

    const getTarget = (trigger) => {
        const selector = trigger.getAttribute('data-bs-target') || trigger.getAttribute('href');
        return selector && selector !== '#' ? document.querySelector(selector) : null;
    };

    const makeBackdrop = (type, zIndex) => {
        const backdrop = document.createElement('div');
        backdrop.className = `${type}-backdrop fade show`;
        backdrop.style.zIndex = zIndex;
        document.body.appendChild(backdrop);
        return backdrop;
    };

    const openModal = (modal) => {
        if (!modal || modal.classList.contains('show')) return;
        const openCount = document.querySelectorAll('.modal.show').length;
        const zIndex = 1055 + (openCount * 20);
        const backdrop = makeBackdrop('modal', zIndex - 5);
        backdrop.dataset.modalFallbackFor = modal.id || '';
        modal.style.zIndex = zIndex;
        modal.style.display = 'block';
        modal.removeAttribute('aria-hidden');
        modal.setAttribute('aria-modal', 'true');
        modal.classList.add('show');
        document.body.classList.add('modal-open');
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.classList.remove('show');
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        modal.removeAttribute('aria-modal');
        const selector = modal.id
            ? `.modal-backdrop[data-modal-fallback-for="${modal.id}"]`
            : '.modal-backdrop';
        const backdrop = document.querySelector(selector);
        if (backdrop) backdrop.remove();
        if (!document.querySelector('.modal.show')) document.body.classList.remove('modal-open');
    };

    const openOffcanvas = (panel) => {
        if (!panel || panel.classList.contains('show')) return;
        const backdrop = makeBackdrop('offcanvas', 1040);
        backdrop.dataset.offcanvasFallbackFor = panel.id || '';
        panel.classList.add('show');
        panel.style.visibility = 'visible';
        document.body.style.overflow = 'hidden';
    };

    const closeOffcanvas = (panel) => {
        if (!panel) return;
        panel.classList.remove('show');
        panel.style.visibility = '';
        const selector = panel.id
            ? `.offcanvas-backdrop[data-offcanvas-fallback-for="${panel.id}"]`
            : '.offcanvas-backdrop';
        const backdrop = document.querySelector(selector);
        if (backdrop) backdrop.remove();
        document.body.style.overflow = '';
    };

    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-bs-toggle]');
        if (trigger) {
            const toggle = trigger.getAttribute('data-bs-toggle');
            const target = getTarget(trigger);

            if (toggle === 'modal') {
                e.preventDefault();
                openModal(target);
                return;
            }

            if (toggle === 'offcanvas') {
                e.preventDefault();
                openOffcanvas(target);
                return;
            }

            if (toggle === 'dropdown') {
                e.preventDefault();
                e.stopPropagation();
                const menu = trigger.parentElement ? trigger.parentElement.querySelector('.dropdown-menu') : null;
                document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                    if (openMenu !== menu) openMenu.classList.remove('show');
                });
                if (menu) {
                    menu.classList.toggle('show');
                    trigger.setAttribute('aria-expanded', menu.classList.contains('show') ? 'true' : 'false');
                }
            }
        }

        const dismiss = e.target.closest('[data-bs-dismiss]');
        if (dismiss) {
            const action = dismiss.getAttribute('data-bs-dismiss');
            if (action === 'modal') closeModal(dismiss.closest('.modal'));
            if (action === 'offcanvas') closeOffcanvas(dismiss.closest('.offcanvas'));
            if (action === 'alert') {
                const alert = dismiss.closest('.alert');
                if (alert) alert.remove();
            }
        }

        const modalBackdrop = e.target.closest('.modal-backdrop');
        if (modalBackdrop && e.target === modalBackdrop) {
            const modal = document.getElementById(modalBackdrop.dataset.modalFallbackFor);
            closeModal(modal);
        }

        const offcanvasBackdrop = e.target.closest('.offcanvas-backdrop');
        if (offcanvasBackdrop && e.target === offcanvasBackdrop) {
            const panel = document.getElementById(offcanvasBackdrop.dataset.offcanvasFallbackFor);
            closeOffcanvas(panel);
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => menu.classList.remove('show'));
        }
    });
}

function previewSig(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            input.parentElement.querySelector('img').src = e.target.result;
            input.parentElement.classList.add('border-primary');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initBootstrapFallback();


    // Helper functions for dynamic rows
    function addJamRow(tableBody, jam, val = '') {
        const rowId = jamRowCount++;
        const html = `
            <tr data-jam="${jam}">
                <td class="px-3" width="100">
                    <input type="text" name="jam_data[${rowId}][rentang_jam]" value="${jam}" class="form-control-plaintext text-center font-weight-bold" readonly style="width: 80px;">
                </td>
                <td><input type="number" name="jam_data[${rowId}][hasil_produksi]" value="${val}" class="form-control form-control-sm jam-hasil text-center" required placeholder="0"></td>
                <td class="text-center" width="50">
                    <button type="button" class="btn btn-link btn-sm text-danger remove-jam" title="Hapus" style="text-decoration: none; font-size: 1.2rem;">×</button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', html);
    }

    function addDtRow(tableBody, data = {}) {
        const rowId = dtRowCount++;
        const html = `
            <tr>
                <td class="px-3"><input type="text" name="downtimes[${rowId}][alasan]" value="${data.alasan_downtime || ''}" class="form-control form-control-sm"></td>
                <td><input type="datetime-local" name="downtimes[${rowId}][mulai]" value="${data.waktu_mulai ? data.waktu_mulai.replace(' ', 'T').substring(0,16) : ''}" class="form-control form-control-sm dt-mulai"></td>
                <td><input type="datetime-local" name="downtimes[${rowId}][selesai]" value="${data.waktu_selesai ? data.waktu_selesai.replace(' ', 'T').substring(0,16) : ''}" class="form-control form-control-sm dt-selesai"></td>
                <td><input type="number" name="downtimes[${rowId}][durasi]" value="${data.durasi_menit || ''}" class="form-control form-control-sm dt-durasi"></td>
                <td class="text-center"><button type="button" class="btn btn-link btn-sm text-danger remove-dt">×</button></td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', html);
    }

    function updateTotals(container) {
        if (!container) return;
        let totalHasil = 0;
        container.querySelectorAll('.jam-hasil').forEach(inp => totalHasil += (parseInt(inp.value) || 0));
        const display = container.querySelector('.grand_total_hasil_display');
        if (display) display.innerText = totalHasil;
        const totalReject = parseInt(container.querySelector('.grand_total_reject').value) || 0;
        const gBagus = container.querySelector('.grand_total_bagus');
        if (gBagus) gBagus.value = totalHasil - totalReject;
    }

    // Clone form content to Edit Modal
    const addForm = document.getElementById('formAddLaporan');
    const editContainer = document.getElementById('editModalContent');
    if (addForm && editContainer) {
        const formContent = addForm.innerHTML;
        editContainer.innerHTML = formContent;
        editContainer.querySelectorAll('[id]').forEach(el => { el.id = 'edit_' + el.id; });
    }

    // Recycle Bin Loading
    const btnTrash = document.getElementById('btn_open_trash');
    if (btnTrash) {
        btnTrash.addEventListener('click', function() {
            const tbody = document.querySelector('#table_trash tbody');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';
            fetch('<?= base_url('produksi/get_trash') ?>')
                .then(r => r.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Tempat sampah kosong.</td></tr>'; return; }
                    data.forEach(p => {
                        const html = `<tr><td class="px-3 py-3"><div class="font-weight-bold">${p.nomor_spk}</div><div class="text-xs text-muted">${p.nama_mesin} - ${p.nama_produksi}</div></td><td class="py-3 small">${p.deleted_at}</td><td class="text-center py-3"><div class="d-flex justify-content-center gap-1"><a href="<?= base_url('produksi/restore') ?>/${p.id}" class="btn btn-xs btn-success px-2 py-1" style="font-size: 10px;"><i class="fas fa-undo mr-1"></i> Pulihkan</a><a href="<?= base_url('produksi/delete_permanent') ?>/${p.id}" class="btn btn-xs btn-outline-danger px-2 py-1" style="font-size: 10px;" onclick="return confirm('Hapus permanen?')"><i class="fas fa-times"></i></a></div></td></tr>`;
                        tbody.insertAdjacentHTML('beforeend', html);
                    });
                });
        });
    }

    // Shared listeners for Add/Edit forms
    document.querySelectorAll('.btn_add_jam_row').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('.modal-body');
            const select = container.querySelector('.select_jam');
            const tableBody = container.querySelector('.table_jam_form tbody');
            const jam = select.value;
            if (!jam) return;
            if (tableBody.querySelector(`tr[data-jam="${jam}"]`)) { alert('Jam ini sudah ada'); return; }
            addJamRow(tableBody, jam);
            updateTotals(container);
        });
    });

    document.querySelectorAll('.btn_add_dt').forEach(btn => {
        btn.addEventListener('click', function() {
            const tableBody = this.closest('.modal-body').querySelector('.table_downtime_form tbody');
            addDtRow(tableBody);
        });
    });

    document.querySelectorAll('.check_spk_baru').forEach(chk => {
        chk.addEventListener('change', function() {
            const container = this.closest('.modal-body');
            const selectWrap = container.querySelector('.spk_select_wrapper');
            const inputWrap = container.querySelector('.spk_input_wrapper');
            const select = container.querySelector('.nomor_spk');
            const input = container.querySelector('.nomor_spk_baru');
            if (this.checked) {
                selectWrap.style.display = 'none'; inputWrap.style.display = 'block';
                select.removeAttribute('name'); input.setAttribute('name', 'nomor_spk');
                input.setAttribute('required', true); select.removeAttribute('required');
            } else {
                selectWrap.style.display = 'block'; inputWrap.style.display = 'none';
                input.removeAttribute('name'); select.setAttribute('name', 'nomor_spk');
                select.setAttribute('required', true); input.removeAttribute('required');
            }
        });
    });

    document.querySelectorAll('.nomor_spk').forEach(sel => {
        sel.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!opt.value) return;
            const container = this.closest('.modal-body');
            container.querySelector('.nama_mesin').value = opt.dataset.mesin || '';
            container.querySelector('.nama_produksi').value = opt.dataset.produk || '';
            fetch('<?= base_url('produksi/get_last_data') ?>/' + encodeURIComponent(opt.value))
                .then(r => r.json())
                .then(data => {
                    const d = data.last || data.legacy;
                    if (d) {
                        container.querySelector('.nomor_mesin').value = d.nomor_mesin || '';
                        container.querySelector('.batch_number').value = d.batch_number || '';
                        container.querySelector('.grup').value = d.grup || '';
                        container.querySelector('.packing').value = d.packing || '';
                        container.querySelector('.cycle_time').value = d.cycle_time || '';
                        container.querySelector('.target').value = d.target || '';
                        container.querySelector('.operator').value = d.operator || '';
                        container.querySelector('.merek_kode').value = d.merek_kode || '';
                        container.querySelector('.m_pemakaian').value = d.m_pemakaian || d.pemakaian || '';
                    }
                });
        });
    });

    document.querySelectorAll('.btn_open_reject_modal').forEach(btn => {
        btn.addEventListener('click', function() {
            activeRejectStorage = document.getElementById(this.dataset.targetStorage);
            document.querySelectorAll('.input-reject-detail').forEach(inp => inp.value = '');
            activeRejectStorage.querySelectorAll('input').forEach(inp => {
                const modalInp = document.querySelector(`.input-reject-detail[data-type="${inp.dataset.type}"]`);
                if (modalInp) modalInp.value = inp.value;
            });
        });
    });

    const btnSaveReject = document.getElementById('btn_save_reject_generic');
    if (btnSaveReject) {
        btnSaveReject.addEventListener('click', function() {
            if (!activeRejectStorage) return;
            activeRejectStorage.innerHTML = '';
            let total = 0;
            document.querySelectorAll('.input-reject-detail').forEach(inp => {
                const val = parseInt(inp.value) || 0;
                if (val > 0) {
                    total += val;
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden'; hidden.name = `rejects[${inp.dataset.type}]`;
                    hidden.value = val; hidden.dataset.type = inp.dataset.type;
                    activeRejectStorage.appendChild(hidden);
                }
            });
            const container = activeRejectStorage.closest('.modal-body');
            container.querySelector('.grand_total_reject').value = total;
            updateTotals(container);
        });
    }

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jam-hasil')) { updateTotals(e.target.closest('.modal-body')); }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-jam')) {
            const container = e.target.closest('.modal-body');
            e.target.closest('tr').remove();
            if (container) updateTotals(container);
        }
        if (e.target.classList.contains('remove-dt')) { e.target.closest('tr').remove(); }

        const editBtn = e.target.closest('.btn_edit_laporan');
        if (editBtn) {
            const id = editBtn.dataset.id;
            const container = document.getElementById('editModalContent');
            const form = document.getElementById('formEditLaporan');
            if (!container || !form) return;
            form.action = '<?= base_url('produksi/update') ?>/' + id;
            const tJam = container.querySelector('.table_jam_form tbody');
            const tDt = container.querySelector('.table_downtime_form tbody');
            const rStorage = container.querySelector('.reject_storage');
            if (tJam) tJam.innerHTML = ''; if (tDt) tDt.innerHTML = ''; if (rStorage) rStorage.innerHTML = '';
            container.querySelectorAll('img').forEach(img => img.src = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 50'%3E%3Crect width='100' height='50' fill='%23f0f2f5'/%3E%3Ctext x='50' y='25' font-family='sans-serif' font-size='12' fill='%23a3aed1' text-anchor='middle' dominant-baseline='middle'%3EPreview%3C/text%3E%3C/svg%3E");
            fetch('<?= base_url('produksi/detail') ?>/' + id)
                .then(r => r.json())
                .then(data => {
                    const h = data.header; if (!h) return;
                    const set = (cls, val) => { const el = container.querySelector('.' + cls); if (el) el.value = val || ''; };
                    set('nomor_spk', h.nomor_spk); set('nama_mesin', h.nama_mesin); set('nama_produksi', h.nama_produksi);
                    set('batch_number', h.batch_number); set('shift', h.shift); set('grup', h.grup);
                    set('nomor_mesin', h.nomor_mesin); set('packing', h.packing); set('tanggal', h.tanggal);
                    set('cycle_time', h.cycle_time); set('target', h.target); set('operator', h.operator);
                    set('grand_total_reject', h.grand_total_reject); set('sisa_po', h.sisa_po);
                    set('hold', h.hold); set('gumpalan', h.gumpalan); set('isi', h.isi);
                    const txt = container.querySelector('textarea'); if (txt) txt.value = h.catatan || '';
                    if (data.materials && data.materials[0]) { set('merek_kode', data.materials[0].merek_kode); set('m_pemakaian', data.materials[0].pemakaian); }
                    const imgs = container.querySelectorAll('img[src*="upload_placeholder"]');
                    if (h.ttd_shift_1 && imgs[0]) imgs[0].src = '<?= base_url('uploads/ttd') ?>/' + h.ttd_shift_1;
                    if (h.ttd_shift_2 && imgs[1]) imgs[1].src = '<?= base_url('uploads/ttd') ?>/' + h.ttd_shift_2;
                    if (h.ttd_shift_3 && imgs[2]) imgs[2].src = '<?= base_url('uploads/ttd') ?>/' + h.ttd_shift_3;
                    if (h.ttd_spv && imgs[3]) imgs[3].src = '<?= base_url('uploads/ttd') ?>/' + h.ttd_spv;
                    if (tJam) data.jams.forEach(j => addJamRow(tJam, j.rentang_jam, j.hasil_produksi));
                    if (tDt) data.downtimes.forEach(dt => addDtRow(tDt, dt));
                    if (rStorage) { data.rejects.forEach(r => { const hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = `rejects[${r.jenis_reject}]`; hidden.value = r.jumlah; hidden.dataset.type = r.jenis_reject; rStorage.appendChild(hidden); }); }
                    updateTotals(container);
                });
        }

        const detailBtn = e.target.closest('.btn_view_detail');
        if (detailBtn) {
            const id = detailBtn.dataset.id;
            const container = document.getElementById('detailModalContent');
            if (!container) return;
            container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat data...</p></div>';
            fetch('<?= base_url('produksi/detail') ?>/' + id)
                .then(r => r.json())
                .then(data => {
                    const h = data.header; if (!h) return;
                    let html = `<div class="row g-4"><div class="col-md-6"><h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Umum</h6><table class="table table-sm table-borderless"><tr><td width="150" class="text-muted">No. SPK</td><td class="fw-bold">${h.nomor_spk}</td></tr><tr><td class="text-muted">Mesin</td><td>${h.nama_mesin} (M#${h.nomor_mesin})</td></tr><tr><td class="text-muted">Produk</td><td>${h.nama_produksi}</td></tr><tr><td class="text-muted">Batch</td><td>${h.batch_number || '-'}</td></tr><tr><td class="text-muted">Tanggal</td><td>${h.tanggal}</td></tr><tr><td class="text-muted">Shift / Grup</td><td>Shift ${h.shift} / ${h.grup || '-'}</td></tr><tr><td class="text-muted">Operator</td><td>${h.operator}</td></tr></table></div><div class="col-md-6"><h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-chart-line me-2 text-success"></i>Kinerja Produksi</h6><table class="table table-sm table-borderless"><tr><td width="150" class="text-muted">Target</td><td>${h.target} pcs</td></tr><tr><td class="text-muted">Hasil Bagus</td><td class="text-success fw-bold">${(parseInt(h.grand_total_bagus)||0).toLocaleString()} pcs</td></tr><tr><td class="text-muted">Total Reject</td><td class="text-danger fw-bold">${(parseInt(h.grand_total_reject)||0).toLocaleString()} pcs</td></tr><tr><td class="text-muted">Downtime</td><td>${h.total_downtime} menit</td></tr><tr><td class="text-muted">Cycle Time</td><td>${h.cycle_time || '-'}</td></tr></table></div><div class="col-md-12"><h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-history me-2 text-info"></i>Hasil Produksi Per Jam</h6><table class="table table-sm table-bordered"><thead class="bg-light"><tr><th class="text-center">Jam</th><th class="text-center">Hasil (pcs)</th></tr></thead><tbody>${data.jams.map(j => `<tr><td class="text-center">${j.rentang_jam}</td><td class="text-center">${(parseInt(j.hasil_produksi)||0).toLocaleString()}</td></tr>`).join('')}</tbody></table></div><div class="col-md-6"><h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Rincian Reject</h6><table class="table table-sm table-bordered"><thead class="bg-light"><tr><th>Jenis Reject</th><th class="text-center">Jumlah</th></tr></thead><tbody>${data.rejects.length ? data.rejects.map(r => `<tr><td>${r.jenis_reject}</td><td class="text-center text-danger fw-bold">${(parseInt(r.jumlah)||0).toLocaleString()}</td></tr>`).join('') : '<tr><td colspan="2" class="text-center text-muted">Tidak ada reject</td></tr>'}</tbody></table></div><div class="col-md-6"><h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-boxes me-2 text-warning"></i>Material & Packaging</h6><div class="p-3 bg-light rounded"><p class="mb-1 small"><strong>Material:</strong> ${data.materials[0] ? data.materials[0].merek_kode + ' (' + data.materials[0].pemakaian + ' kg)' : '-'}</p><p class="mb-1 small"><strong>Lot:</strong> ${data.materials[0] ? [data.materials[0].lot_a, data.materials[0].lot_b, data.materials[0].lot_c, data.materials[0].lot_d].filter(x => x).join(', ') : '-'}</p><hr class="my-2"><p class="mb-1 small"><strong>Colorant:</strong> ${data.colorants[0] ? data.colorants[0].code + ' (' + data.colorants[0].pemakaian + ' kg)' : '-'}</p><p class="mb-1 small"><strong>Packaging:</strong> ${data.packaging ? 'Box: ' + data.packaging.box_karung_nicktainer + ' pcs, Plastik: ' + data.packaging.plastik + ' pcs' : '-'}</p></div></div><div class="col-md-12"><h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-comment-alt me-2 text-secondary"></i>Catatan & Verifikasi</h6><div class="p-3 border rounded mb-3 bg-light italic">${h.catatan || 'Tidak ada catatan.'}</div><div class="row g-2">${['Shift 1', 'Shift 2', 'Shift 3', 'Supervisor'].map((label, i) => { const field = ['ttd_shift_1', 'ttd_shift_2', 'ttd_shift_3', 'ttd_spv'][i]; return `<div class="col-3 text-center"><div class="border rounded p-2 bg-white">${h[field] ? `<img src="<?= base_url('uploads/ttd') ?>/${h[field]}" class="img-fluid mb-2" style="max-height: 60px;">` : `<div class="py-4 text-muted small">Belum TTD</div>`}<div class="fw-bold small border-top pt-1">${label}</div></div></div>`; }).join('')}</div></div></div>`;
                    container.innerHTML = html;
                });
        }

        const deleteBtn = e.target.closest('.btn_confirm_delete');
        if (deleteBtn) {
            const id = deleteBtn.dataset.id;
            const finalDeleteBtn = document.getElementById('btn_final_delete');
            if (finalDeleteBtn) finalDeleteBtn.href = '<?= base_url('produksi/delete') ?>/' + id;
        }
    });
});
</script>
<?= $this->endSection() ?>
