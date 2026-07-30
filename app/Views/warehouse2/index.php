<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        padding: 0.75rem 1.5rem;
    }

    .nav-tabs .nav-link.active {
        color: #007bff;
        background-color: transparent;
        border-bottom-color: #007bff;
    }

    .nav-tabs .nav-link:hover {
        color: #0056b3;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .table-responsive {
        border-radius: 0.5rem;
    }

    .btn-group-sm {
        gap: 0.25rem;
    }

    .badge {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
    }

    .form-modal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .filter-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .data-table {
        font-size: 0.9rem;
    }

    .data-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-top: none;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* ===== STYLE SPK, SHIFT, STATUS ===== */

    /* SPK Dropdown - Styling */
    #oroSpk {
        font-weight: 500;
        padding: 0.5rem 0.75rem !important;
        background-color: #f8f9ff;
        border: 2px solid #dee2e6 !important;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    #oroSpk:hover {
        background-color: #f0f2ff;
        border-color: #0d6efd !important;
    }

    #oroSpk:focus {
        background-color: white;
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    }

    #oroSpk option {
        padding: 0.5rem;
        background-color: white;
    }

    #oroSpk option:checked {
        background: linear-gradient(#0d6efd, #0d6efd);
        background-color: #0d6efd !important;
        color: white;
    }

    /* SPK Label dengan Icon */
    .oroSpk-label {
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .oroSpk-label::before {
        content: "📋";
        font-size: 1.1rem;
    }

    /* Shift Dropdown - Styling */
    #oroShift {
        font-weight: 500;
        padding: 0.5rem 0.75rem !important;
        background-color: #fff8f0;
        border: 2px solid #dee2e6 !important;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    #oroShift:hover {
        background-color: #ffe8d4;
        border-color: #fd7e14 !important;
    }

    #oroShift:focus {
        background-color: white;
        border-color: #fd7e14 !important;
        box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25) !important;
    }

    #oroShift option {
        padding: 0.5rem;
        background-color: white;
    }

    #oroShift option:checked {
        background: linear-gradient(#fd7e14, #fd7e14);
        background-color: #fd7e14 !important;
        color: white;
    }

    /* Shift Label dengan Icon */
    .oroShift-label {
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .oroShift-label::before {
        content: "⏰";
        font-size: 1.1rem;
    }

    /* Status Dropdown - Styling */
    #oroStatus {
        font-weight: 500;
        padding: 0.5rem 0.75rem !important;
        background-color: #f0f8f7;
        border: 2px solid #dee2e6 !important;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    #oroStatus:hover {
        background-color: #d4f5f2;
        border-color: #20c997 !important;
    }

    #oroStatus:focus {
        background-color: white;
        border-color: #20c997 !important;
        box-shadow: 0 0 0 0.2rem rgba(32, 201, 151, 0.25) !important;
    }

    #oroStatus option {
        padding: 0.5rem;
        background-color: white;
    }

    #oroStatus option:checked {
        background: linear-gradient(#20c997, #20c997);
        background-color: #20c997 !important;
        color: white;
    }

    /* Status Label dengan Icon */
    .oroStatus-label {
        font-weight: 600;
        color: #212529;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .oroStatus-label::before {
        content: "✓";
        font-size: 1.1rem;
        color: #20c997;
    }

    /* Row spacing untuk field yang lebih rapi */
    .form-group-spk-shift {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    @media (max-width: 768px) {
        .form-group-spk-shift {
            grid-template-columns: 1fr;
        }
    }

    /* Styling untuk form label */
    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Styling untuk form control */
    .form-select,
    .form-control {
        border-radius: 0.375rem;
        font-size: 0.95rem;
    }

    .form-select:disabled,
    .form-control:disabled {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
</style>

<?php
$canEdit = in_array(session()->get('role'), ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi']);
?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-warehouse"></i> Warehouse 2</h2>
            <p class="text-muted small mb-0">Manajemen inventori barang masuk dan keluar</p>
        </div>
        <?php if ($canEdit): ?>
        <button type="button" class="btn btn-primary" id="btnTambahData">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
        <?php endif; ?>
    </div>

    <!-- Tabs Navigation -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-pemasok" data-tab="pemasok" role="tab">
                        <i class="fas fa-truck me-2"></i> Barang Masuk dari Pemasok
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-produksi" data-tab="produksi" role="tab">
                        <i class="fas fa-industry me-2"></i> Barang Masuk ke Produksi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-pembeli" data-tab="pembeli" role="tab">
                        <i class="fas fa-shipping-fast me-2"></i> Barang Keluar ke Pembeli
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-masuk-produksi" data-tab="masuk-produksi" role="tab">
                        <i class="fas fa-box me-2"></i> Barang Masuk dari Produksi
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- TAB 1: Barang Masuk Pemasok (Updated with Orowarehouse structure) -->
    <div class="tab-content active" data-tab="pemasok">
        <div class="card shadow mb-4 border-0">
            <!-- Toolbar Section -->
            <div class="card-header py-3 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <!-- Left Group: Action Buttons -->
                    <div class="btn-group flex-wrap">
                        <?php if ($canEdit): ?>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" id="btnTambahPemasok">
                            <i class="fas fa-plus me-1"></i> Tambah Data
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('warehouse2/export-pemasok') ?>" id="exportPemasokLink" class="btn btn-outline-success btn-sm shadow-sm">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                        <?php if ($canEdit): ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_open_pemasok_trash" data-bs-toggle="modal" data-bs-target="#trashModalPemasok">
                            <i class="fas fa-trash-restore me-1"></i> Sampah
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Right Group: Search & Filter -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Search Box -->
                        <form action="#" method="get" class="d-flex gap-1" id="pemasokSearchForm">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" class="form-control border-end-0" id="pemasokSearchQuery" placeholder="Cari nomor spk..." value="">
                                <button class="btn btn-outline-primary border-start-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Custom Filter Button -->
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#pemasokCustomFilterDrawer">
                            <i class="fas fa-sliders-h me-1"></i> Filter
                            <span class="badge bg-primary ms-1" id="pemasokFilterCount" style="display:none;"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm data-table" id="tablePemasok">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Tanggal</th>
                                <th style="width: 90px;">Nomor SPK</th>
                                <th style="width: 110px;">Pemasok</th>
                                <th style="width: 80px;">Kode Barang</th>
                                <th style="width: 110px;">Nama Barang</th>
                                <th style="width: 70px;">Satuan</th>
                                <th style="width: 80px;">Jumlah</th>
                                <th style="width: 100px;">No Surat Jalan</th>
                                <th style="width: 80px;">Status</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPemasok">
                            <tr>
                                <td colspan="10" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada data</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Pagination" id="paginationContainerPemasok" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item disabled" id="prevBtnPemasok">
                            <a class="page-link" href="#" tabindex="-1">Sebelumnya</a>
                        </li>
                        <li class="page-item active" id="currentPagePemasok">
                            <span class="page-link">Halaman <span id="currentPageNumPemasok">1</span> dari <span id="totalPagesPemasok">1</span></span>
                        </li>
                        <li class="page-item" id="nextBtnPemasok">
                            <a class="page-link" href="#">Selanjutnya</a>
                        </li>
                        <li class="page-item">
                            <select class="form-select form-select-sm" id="perPageSelectPemasok" style="width: 80px; margin-left: 10px;">
                                <option value="10">10 / hal</option>
                                <option value="25">25 / hal</option>
                                <option value="50">50 / hal</option>
                            </select>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- TAB 2: Barang Masuk Produksi (Updated with Orowarehouse structure) -->
    <div class="tab-content" data-tab="produksi">
        <div class="card shadow mb-4 border-0">
            <!-- Toolbar Section -->
            <div class="card-header py-3 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <!-- Left Group: Action Buttons -->
                    <div class="btn-group flex-wrap">
                        <?php if ($canEdit): ?>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" id="btnTambahProduksi" onclick="openProduksiForm()">
                            <i class="fas fa-plus me-1"></i> Tambah Data
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('warehouse2/export-produksi') ?>" id="exportProduksiLink" class="btn btn-outline-success btn-sm shadow-sm">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                        <?php if ($canEdit): ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_open_produksi_trash" data-bs-toggle="modal" data-bs-target="#trashModalProduksi">
                            <i class="fas fa-trash-restore me-1"></i> Sampah
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Right Group: Search & Filter -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Search Box -->
                        <form action="#" method="get" class="d-flex gap-1" id="produksiSearchForm">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" class="form-control border-end-0" id="produksiSearchQuery" placeholder="Cari nomor spk..." value="">
                                <button class="btn btn-outline-primary border-start-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Custom Filter Button -->
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#produksiCustomFilterDrawer">
                            <i class="fas fa-sliders-h me-1"></i> Filter
                            <span class="badge bg-primary ms-1" id="produksiFilterCount" style="display:none;"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm data-table" id="tableProduksi">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Tanggal</th>
                                <th style="width: 90px;">Nomor SPK</th>
                                <th style="width: 80px;">Kode Barang</th>
                                <th style="width: 110px;">Nama Barang</th>
                                <th style="width: 70px;">Satuan</th>
                                <th style="width: 80px;">Keluar</th>
                                <th style="width: 80px;">Terima</th>
                                <th style="width: 110px;">Nama Penerima</th>
                                <th style="width: 80px;">Status</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyProduksi">
                            <tr>
                                <td colspan="10" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada data</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Pagination" id="paginationContainerProduksi" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item disabled" id="prevBtnProduksi">
                            <a class="page-link" href="#" tabindex="-1">Sebelumnya</a>
                        </li>
                        <li class="page-item active" id="currentPageProduksi">
                            <span class="page-link">Halaman <span id="currentPageNumProduksi">1</span> dari <span id="totalPagesProduksi">1</span></span>
                        </li>
                        <li class="page-item" id="nextBtnProduksi">
                            <a class="page-link" href="#">Selanjutnya</a>
                        </li>
                        <li class="page-item">
                            <select class="form-select form-select-sm" id="perPageSelectProduksi" style="width: 80px; margin-left: 10px;">
                                <option value="10">10 / hal</option>
                                <option value="25">25 / hal</option>
                                <option value="50">50 / hal</option>
                            </select>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- TAB 3: Barang Keluar Pembeli (Updated with Orowarehouse structure) -->
    <div class="tab-content" data-tab="pembeli">
        <div class="card shadow mb-4 border-0">
            <!-- Toolbar Section -->
            <div class="card-header py-3 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <!-- Left Group: Action Buttons -->
                    <div class="btn-group flex-wrap">
                        <?php if ($canEdit): ?>
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" id="btnTambahPembeli">
                            <i class="fas fa-plus me-1"></i> Tambah Data
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('warehouse2/export-pembeli') ?>" id="exportPembeliLink" class="btn btn-outline-success btn-sm shadow-sm">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                        <?php if ($canEdit): ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_open_pembeli_trash" data-bs-toggle="modal" data-bs-target="#trashModalPembeli">
                            <i class="fas fa-trash-restore me-1"></i> Sampah
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Right Group: Search & Filter -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Search Box -->
                        <form action="#" method="get" class="d-flex gap-1" id="pembeliSearchForm">
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" class="form-control border-end-0" id="pembeliSearchQuery" placeholder="Cari nomor spk..." value="">
                                <button class="btn btn-outline-primary border-start-0" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Custom Filter Button -->
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#pembeliCustomFilterDrawer">
                            <i class="fas fa-sliders-h me-1"></i> Filter
                            <span class="badge bg-primary ms-1" id="pembeliFilterCount" style="display:none;"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm data-table" id="tablePembeli">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Tanggal</th>
                                <th style="width: 100px;">Nomor SPK</th>
                                <th style="width: 120px;">Pembeli</th>
                                <th style="width: 100px;">Barang</th>
                                <th style="width: 70px;">Jumlah</th>
                                <th style="width: 70px;">Status</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPembeli">
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada data</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Pagination" id="paginationContainerPembeli" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item disabled" id="prevBtnPembeli">
                            <a class="page-link" href="#" tabindex="-1">Sebelumnya</a>
                        </li>
                        <li class="page-item active" id="currentPagePembeli">
                            <span class="page-link">Halaman <span id="currentPageNumPembeli">1</span> dari <span id="totalPagesPembeli">1</span></span>
                        </li>
                        <li class="page-item" id="nextBtnPembeli">
                            <a class="page-link" href="#">Selanjutnya</a>
                        </li>
                        <li class="page-item">
                            <select class="form-select form-select-sm" id="perPageSelectPembeli" style="width: 80px; margin-left: 10px;">
                                <option value="10">10 / hal</option>
                                <option value="25">25 / hal</option>
                                <option value="50">50 / hal</option>
                            </select>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- TAB 4: Barang Masuk dari Produksi (OROWAREHOUSE) -->
<div class="tab-content" data-tab="masuk-produksi">
    <div class="card shadow mb-4 border-0">
        <!-- Toolbar Section -->
        <div class="card-header py-3 bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Left Group: Action Buttons -->
                <div class="btn-group flex-wrap">
                    <?php if ($canEdit): ?>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" id="btnTambahOrowarehouse">
                        <i class="fas fa-plus me-1"></i> Tambah Laporan
                    </button>
                    <?php endif; ?>
                    <a href="<?= base_url('warehouse2/export-orowarehouse') ?>" id="exportOroLink" class="btn btn-outline-success btn-sm shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <?php if ($canEdit): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_open_oro_trash" data-bs-toggle="modal" data-bs-target="#trashModalOro">
                        <i class="fas fa-trash-restore me-1"></i> Sampah
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Right Group: Filters & Search -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Period Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm" type="button" id="oroFilterPeriod" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <span id="oroPeriodLabel">Semua Periode</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" aria-labelledby="oroFilterPeriod">
                            <li><a class="dropdown-item py-2" href="#" onclick="setPeriodFilter('', this)"><i class="fas fa-times me-2"></i> Semua Periode</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2" href="#" onclick="setPeriodFilter('day', this)"><i class="fas fa-calendar-day me-2"></i> Hari Ini</a></li>
                            <li><a class="dropdown-item py-2" href="#" onclick="setPeriodFilter('week', this)"><i class="fas fa-calendar-week me-2"></i> Minggu Ini</a></li>
                            <li><a class="dropdown-item py-2" href="#" onclick="setPeriodFilter('month', this)"><i class="fas fa-calendar-alt me-2"></i> Bulan Ini</a></li>
                        </ul>
                    </div>

                    <!-- Search Box -->
                    <form action="#" method="get" class="d-flex gap-1" id="oroSearchForm">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" class="form-control border-end-0" id="oroSearchQuery" placeholder="Cari SPK/Mesin..." value="">
                            <button class="btn btn-outline-primary border-start-0" type="submit" id="oroSearchBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Custom Filter Button -->
                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#oroCustomFilterDrawer">
                        <i class="fas fa-sliders-h me-1"></i> Custom
                        <span class="badge bg-primary ms-1" id="oroFilterCount" style="display:none;"></span>
                    </button>

                    <!-- Summary Badges -->
                    <div class="d-flex align-items-center gap-2 ms-2">
                        <span class="badge bg-success" id="totalBagusDisplay" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                            <i class="fas fa-check-circle me-1"></i>Bagus: 0
                        </span>
                        <span class="badge bg-danger" id="totalRejectDisplay" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                            <i class="fas fa-times-circle me-1"></i>Reject: 0
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm data-table" id="tableOrowarehouse">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Tanggal</th>
                            <th style="width: 60px;">SPK</th>
                            <th style="width: 100px;">Produk</th>
                            <th style="width: 50px;">Shift</th>
                            <th style="width: 70px;">Grup</th>
                            <th style="width: 70px;">Bagus</th>
                            <th style="width: 70px;">Reject</th>
                            <th style="width: 80px;">Material</th>
                            <th style="width: 80px;">Colorant</th>
                            <th style="width: 80px;">Packaging</th>
                            <th style="width: 80px;">Operator</th>
                            <th style="width: 70px;">Status</th>
                            <th style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyOrowarehouse">
                        <tr>
                            <td colspan="13" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada data</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Pagination" id="paginationContainer" class="mt-4">
                <ul class="pagination pagination-sm justify-content-center">
                    <li class="page-item disabled" id="prevBtn">
                        <a class="page-link" href="#" tabindex="-1">Sebelumnya</a>
                    </li>
                    <li class="page-item active" id="currentPage">
                        <span class="page-link">Halaman <span id="currentPageNum">1</span> dari <span id="totalPages">1</span></span>
                    </li>
                    <li class="page-item" id="nextBtn">
                        <a class="page-link" href="#">Selanjutnya</a>
                    </li>
                    <li class="page-item">
                        <select class="form-select form-select-sm" id="perPageSelect" style="width: 80px; margin-left: 10px;">
                            <option value="10">10 / hal</option>
                            <option value="25">25 / hal</option>
                            <option value="50">50 / hal</option>
                        </select>
                    </li>
                </ul>
            </nav>


        </div>
    </div>
</div>

<!-- Custom Filter Offcanvas for Pemasok -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="pemasokCustomFilterDrawer" aria-labelledby="pemasokCustomFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="pemasokCustomFilterLabel">Filter Custom</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="pemasokCustomFilterForm">
            <div class="mb-3">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" class="form-control form-control-sm" id="pemasokFilterTanggalDari">
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" class="form-control form-control-sm" id="pemasokFilterTanggalSampai">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select form-select-sm" id="pemasokFilterStatus">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Pemasok</label>
                <input type="text" class="form-control form-control-sm" id="pemasokFilterNamaPemasok" placeholder="Cari pemasok...">
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="applyPemasokCustomFilter()">
                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetPemasokCustomFilter()">
                    <i class="fas fa-redo me-1"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Trash Modal for Pemasok -->
<div class="modal fade" id="trashModalPemasok" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i> Sampah Sementara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="trashTablePemasok">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 100px;">Tanggal</th>
                                <th style="width: 100px;">No Dokumen</th>
                                <th style="width: 120px;">Pemasok</th>
                                <th style="width: 100px;">Barang</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="trashTbodyPemasok">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data di sampah
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Filter Offcanvas for Produksi -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="produksiCustomFilterDrawer" aria-labelledby="produksiCustomFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="produksiCustomFilterLabel">Filter Custom</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="produksiCustomFilterForm">
            <div class="mb-3">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" class="form-control form-control-sm" id="produksiFilterTanggalDari">
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" class="form-control form-control-sm" id="produksiFilterTanggalSampai">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select form-select-sm" id="produksiFilterStatus">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="terima">Terima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">No SPK</label>
                <input type="text" class="form-control form-control-sm" id="produksiFilterNoSpk" placeholder="Cari SPK...">
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="applyProduksiCustomFilter()">
                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetProduksiCustomFilter()">
                    <i class="fas fa-redo me-1"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Trash Modal for Produksi -->
<div class="modal fade" id="trashModalProduksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i> Sampah Sementara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="trashTableProduksi">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 100px;">Tanggal</th>
                                <th style="width: 100px;">No Dokumen</th>
                                <th style="width: 80px;">No SPK</th>
                                <th style="width: 120px;">Barang</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="trashTbodyProduksi">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data di sampah
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Filter Offcanvas for Pembeli -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="pembeliCustomFilterDrawer" aria-labelledby="pembeliCustomFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="pembeliCustomFilterLabel">Filter Custom</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="pembeliCustomFilterForm">
            <div class="mb-3">
                <label class="form-label">Tanggal Dari</label>
                <input type="date" class="form-control form-control-sm" id="pembeliFilterTanggalDari">
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Sampai</label>
                <input type="date" class="form-control form-control-sm" id="pembeliFilterTanggalSampai">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select form-select-sm" id="pembeliFilterStatus">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Pembeli</label>
                <input type="text" class="form-control form-control-sm" id="pembeliFilterNamaPembeli" placeholder="Cari pembeli...">
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="applyPembeliCustomFilter()">
                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetPembeliCustomFilter()">
                    <i class="fas fa-redo me-1"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Trash Modal for Pembeli -->
<div class="modal fade" id="trashModalPembeli" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i> Sampah Sementara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="trashTablePembeli">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 100px;">Tanggal</th>
                                <th style="width: 100px;">No Dokumen</th>
                                <th style="width: 120px;">Pembeli</th>
                                <th style="width: 100px;">Barang</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="trashTbodyPembeli">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data di sampah
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Custom Filter Offcanvas for Orowarehouse -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="oroCustomFilterDrawer" aria-labelledby="oroCustomFilterLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="oroCustomFilterLabel">Filter Custom</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="oroCustomFilterForm">
            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control form-control-sm" id="oroFilterTanggal">
            </div>
            <div class="mb-3">
                <label class="form-label">Shift</label>
                <select class="form-select form-select-sm" id="oroFilterShift">
                    <option value="">Semua Shift</option>
                    <option value="1">Shift 1</option>
                    <option value="2">Shift 2</option>
                    <option value="3">Shift 3</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-select form-select-sm" id="oroFilterStatus">
                    <option value="">Semua Status</option>
                    <option value="bahan masuk">Bahan Masuk</option>
                    <option value="bahan keluar">Bahan Keluar</option>
                    <option value="menggiling">Menggiling</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Grup</label>
                <select class="form-select form-select-sm" id="oroFilterGrup">
                    <option value="">Semua Grup</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Operator</label>
                <select class="form-select form-select-sm" id="oroFilterOperator">
                    <option value="">Semua Operator</option>
                </select>
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-primary btn-sm" onclick="applyOroCustomFilter()">
                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetOroCustomFilter()">
                    <i class="fas fa-redo me-1"></i> Reset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Trash Modal for Orowarehouse -->
<div class="modal fade" id="trashModalOro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i> Sampah Sementara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="trashTableOro">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 80px;">Tanggal</th>
                                <th style="width: 60px;">SPK</th>
                                <th style="width: 100px;">Produk</th>
                                <th style="width: 50px;">Shift</th>
                                <th style="width: 70px;">Grup</th>
                                <th style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="trashTbodyOro">
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox"></i> Belum ada data di sampah
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade form-modal" id="modalPemasok" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang Masuk Pemasok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPemasok">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" class="form-control" id="tanggalPemasok" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor SPK *</label>
                            <input type="text" class="form-control" id="noDokumenPemasok" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Pemasok *</label>
                            <input type="text" class="form-control" id="namaPemasok" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No PO</label>
                            <input type="text" class="form-control" id="noPoPemasok">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Barang *</label>
                            <input type="text" class="form-control" id="kodeBarangPemasok" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Barang *</label>
                            <input type="text" class="form-control" id="namaBarangPemasok" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Satuan *</label>
                            <select class="form-select" id="satuanPemasok" required>
                                <option value="">Pilih Satuan</option>
                                <option value="kg">kg</option>
                                <option value="pcs">pcs</option>
                                <option value="box">box</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah *</label>
                            <input type="number" class="form-control" id="jumlahPemasok" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No Surat Jalan</label>
                            <input type="text" class="form-control" id="noSuratJalanPemasok">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status *</label>
                            <select class="form-select" id="statusPemasok" required>
                                <option value="bahanmasuk">Bahan Masuk</option>
                                <option value="bahankeluar">Bahan Keluar</option>
                                <!-- <option value="ditolak">Ditolak</option> -->
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatanPemasok" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="savePemasok()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form: Barang Masuk Produksi -->
<div class="modal fade form-modal" id="modalProduksi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProduksiTitle">Tambah Barang Masuk Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formProduksi">
                    <input type="hidden" id="produksiEditId" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor SPK *</label>
                            <input type="text" class="form-control" id="noDokumenProduksi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" class="form-control" id="tanggalProduksi" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Kode Barang *</label>
                            <input type="text" class="form-control" id="kodeBarangProduksi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Barang *</label>
                            <input type="text" class="form-control" id="namaBarangProduksi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Satuan *</label>
                            <input type="text" class="form-control" id="satuanProduksi" placeholder="kg, liter, pcs..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Keluar *</label>
                            <input type="number" class="form-control" id="jumlahKeluarProduksi" step="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Terima</label>
                            <input type="number" class="form-control" id="jumlahTerimaProduksi" step="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" class="form-control" id="namaPenerimaProduksi">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatanProduksi" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status *</label>
                            <select class="form-select" id="statusProduksi" required>
                                <option value="pending">Pending</option>
                                <option value="terima">Terima</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveProduksi()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form: Barang Keluar Pembeli -->
<div class="modal fade form-modal" id="modalPembeli" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPembeliTitle">Tambah Barang Keluar Pembeli</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPembeli">
                    <input type="hidden" id="pembeliEditId" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor SPK *</label>
                            <input type="text" class="form-control" id="noDokumenPembeli" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" class="form-control" id="tanggalPembeli" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Pembeli *</label>
                            <input type="text" class="form-control" id="namaPembeli" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Barang *</label>
                            <input type="text" class="form-control" id="kodeBarangPembeli" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Barang *</label>
                            <input type="text" class="form-control" id="namaBarangPembeli" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Satuan *</label>
                            <input type="text" class="form-control" id="satuanPembeli" placeholder="kg, liter, pcs..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah *</label>
                            <input type="number" class="form-control" id="jumlahPembeli" step="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No Invoice</label>
                            <input type="text" class="form-control" id="noInvoicePembeli">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Pengiriman</label>
                            <textarea class="form-control" id="alamatPengirimanPembeli" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatanPembeli" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status *</label>
                            <select class="form-select" id="statusPembeli" required>
                                <option value="bahanmasuk">Bahan Masuk</option>
                                <option value="bahankeluar">Bahan Keluar</option>
                                
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="savePembeli()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form: Barang Masuk dari Produksi -->
<div class="modal fade form-modal" id="modalMasukProduksi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Barang Masuk dari Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formMasukProduksi">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor SPK *</label>
                            <input type="text" class="form-control" id="noDokumenMasukProduksi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" class="form-control" id="tanggalMasukProduksi" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Nama Produksi *</label>
                            <input type="text" class="form-control" id="namaProduksiMasuk" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Barang *</label>
                            <input type="text" class="form-control" id="kodeBarangMasukProduksi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Barang *</label>
                            <input type="text" class="form-control" id="namaBarangMasukProduksi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Satuan *</label>
                            <input type="text" class="form-control" id="satuanMasukProduksi" placeholder="kg, liter, pcs..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Produksi *</label>
                            <input type="number" class="form-control" id="jumlahProduksiMasuk" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Reject</label>
                            <input type="number" class="form-control" id="jumlahRejectMasuk" step="0.01" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah Diterima *</label>
                            <input type="number" class="form-control" id="jumlahDiterimaMasuk" step="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" class="form-control" id="namaPenerimaMasukProduksi">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="catatanMasukProduksi" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status *</label>
                            <select class="form-select" id="statusMasukProduksi" required>
                                <option value="pending">Pending</option>
                                <option value="diterima">Diterima</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveMasukProduksi()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form: Orowarehouse (Barang Masuk dari Produksi - dengan field detail) -->
<div class="modal fade form-modal" id="modalOrowarehouse" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalOrowarehouseTitle">Tambah Barang Masuk dari Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Autofill alert -->
                <div id="oroAutofillAlert" class="alert alert-info align-items-center py-2 mb-3">
                    <!-- <div class="spinner-border spinner-border-sm me-2" role="status"></div> -->
                    <!-- <span>Mengambil data dari produksi...</span> -->
                </div>
                <div id="oroAutofillSuccess" class="alert alert-success py-2 mb-3" style="display:none;">
                    <i class="fas fa-check-circle me-1"></i> Data berhasil diisi otomatis dari produksi.
                </div>
                <div id="oroAutofillError" class="alert alert-warning py-2 mb-3" style="display:none;">
                    <i class="fas fa-exclamation-triangle me-1"></i> <span id="oroAutofillErrorMsg">SPK tidak ditemukan di produksi.</span>
                </div>

                <form id="formOrowarehouse">
                    <input type="hidden" id="oroId" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label oroSpk-label">SPK *</label>
                            <select class="form-select" id="oroSpk" required onchange="autofillFromSpkSelect()">
                                <option value="">-- Pilih SPK dari Produksi --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal *</label>
                            <input type="date" class="form-control" id="oroTanggal" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Produk *</label>
                            <input type="text" class="form-control" id="oroProduk" placeholder="Terisi otomatis dari SPK" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label oroShift-label">Shift *</label>
                            <select class="form-select" id="oroShift" required>
                                <option value="">-- Pilih Shift --</option>
                                <option value="1">Shift 1</option>
                                <option value="2">Shift 2</option>
                                <option value="3">Shift 3</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Grup</label>
                            <input type="text" class="form-control" id="oroGrup" placeholder="Terisi otomatis dari SPK" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Operator</label>
                            <input type="text" class="form-control" id="oroOperator" placeholder="Terisi otomatis dari SPK" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hasil Bagus *</label>
                            <input type="number" class="form-control" id="oroHasilBagus" step="0.01" placeholder="Terisi otomatis dari SPK" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hasil Reject *</label>
                            <input type="number" class="form-control" id="oroHasilReject" step="0.01" placeholder="Terisi otomatis dari SPK" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Material</label>
                            <input type="text" class="form-control" id="oroMaterial" placeholder="Terisi otomatis dari SPK">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Colorant</label>
                            <input type="text" class="form-control" id="oroColorant" placeholder="Terisi otomatis dari SPK">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor Lot</label>
                            <input type="text" class="form-control" id="oroNomorLot" placeholder="Masukkan nomor lot">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Packaging</label>
                            <input type="text" class="form-control" id="oroPackaging" placeholder="Terisi otomatis dari SPK" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label oroStatus-label">Status *</label>
                            <select class="form-select" id="oroStatus" required>
                                <option value="bahan masuk">Bahan Masuk</option>
                                <option value="bahan keluar">Bahan Keluar</option>
                                <option value="menggiling">Menggiling</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" id="oroCatatan" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveOrowarehouse" onclick="saveOrowarehouse()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk tab switching dan data loading -->
<script>
    const canEdit = <?= $canEdit ? 'true' : 'false' ?>;
    const currentUsername = '<?= $currentUsername ?? 'System' ?>';
    let currentTab = 'pemasok';

    document.addEventListener('DOMContentLoaded', function() {
        // Tambah Data button click handler (for first 3 tabs)
        const btnTambahDataEl = document.getElementById('btnTambahData');
        if (btnTambahDataEl) {
        btnTambahDataEl.addEventListener('click', function() {
            const modals = {
                'pemasok': 'modalPemasok',
                'produksi': 'modalProduksi',
                'pembeli': 'modalPembeli',
                'masuk-produksi': 'modalOrowarehouse'
            };

            // Reset form before opening modal
            const formNames = {
                'pemasok': 'formPemasok',
                'produksi': 'formProduksi',
                'pembeli': 'formPembeli',
                'masuk-produksi': 'formOrowarehouse'
            };

            const form = document.getElementById(formNames[currentTab]);
            if (form) form.reset();

            // Reset orowarehouse modal to "Tambah" mode
            if (currentTab === 'masuk-produksi') {
                document.getElementById('oroId').value = '';
                document.getElementById('modalOrowarehouseTitle').textContent = 'Tambah Barang Masuk dari Produksi';
            }

            // Show modal - use simple, reliable method
            const modalId = modals[currentTab];
            const modalElement = document.getElementById(modalId);
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        });
        } // end if (btnTambahDataEl)

        // Tambah buttons for each tab (new method)
        const btnTambahPemasok = document.getElementById('btnTambahPemasok');
        if (btnTambahPemasok) {
            btnTambahPemasok.addEventListener('click', function() {
                const form = document.getElementById('formPemasok');
                if (form) form.reset();
                const modalElement = document.getElementById('modalPemasok');
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            });
        }

        const btnTambahPembeli = document.getElementById('btnTambahPembeli');
        if (btnTambahPembeli) {
            btnTambahPembeli.addEventListener('click', function() {
                const form = document.getElementById('formPembeli');
                if (form) form.reset();
                document.getElementById('pembeliEditId').value = '';
                document.getElementById('modalPembeliTitle').textContent = 'Tambah Barang Keluar Pembeli';
                const modalElement = document.getElementById('modalPembeli');
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            });
        }

        // Tambah Orowarehouse button click handler (in masuk-produksi tab)
        const btnTambahOro = document.getElementById('btnTambahOrowarehouse');
        if (btnTambahOro) {
            btnTambahOro.addEventListener('click', function() {
                const form = document.getElementById('formOrowarehouse');
                if (form) form.reset();
                document.getElementById('oroId').value = '';
                document.getElementById('modalOrowarehouseTitle').textContent = 'Tambah Barang Masuk dari Produksi';
                
                // Auto-fill operator field with current username
                document.getElementById('oroOperator').value = currentUsername;

                const modalElement = document.getElementById('modalOrowarehouse');
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });
                modal.show();
            });
        }

        // Tab switching
        document.querySelectorAll('[data-tab]').forEach(btn => {
            if (btn.classList.contains('nav-link')) {
                btn.addEventListener('click', function() {
                    const tabName = this.getAttribute('id').replace('tab-', '');
                    switchTab(tabName);
                });
            }
        });

        // Filter event listeners
        document.querySelectorAll('.filter-status, .filter-tanggal-dari, .filter-tanggal-sampai, .filter-search, .filter-no-spk, .filter-pembeli').forEach(el => {
            el.addEventListener('change', function() {
                const tab = this.getAttribute('data-tab');
                loadTabData(tab);
            });
            el.addEventListener('keyup', function() {
                const tab = this.getAttribute('data-tab');
                loadTabData(tab);
            });
        });

        // Load initial data
        loadPemasokData(1);
    });

    function switchTab(tabName) {
        currentTab = tabName;

        // Update active tab button
        document.querySelectorAll('.nav-link').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById('tab-' + tabName).classList.add('active');

        // Update active tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.querySelector('[data-tab="' + tabName + '"].tab-content').classList.add('active');

        // Load data untuk tab
        if (tabName === 'pemasok') {
            initPemasokTab();
        } else if (tabName === 'produksi') {
            initProduksiTab();
        } else if (tabName === 'pembeli') {
            initPembeliTab();
        } else if (tabName === 'masuk-produksi') {
            initOrowarehouseTab();
        }
    }

    function loadTabData(tabName) {
        const apiEndpoints = {
            'pemasok': '<?= base_url('warehouse2/api/barang-masuk-pemasok') ?>',
            'produksi': '<?= base_url('warehouse2/api/barang-masuk-produksi') ?>',
            'pembeli': '<?= base_url('warehouse2/api/barang-keluar-pembeli') ?>',
            'masuk-produksi': '<?= base_url('warehouse2/api/barang-masuk-dari-produksi') ?>'
        };

        const filters = getFiltersForTab(tabName);
        const url = new URL(apiEndpoints[tabName]);
        Object.keys(filters).forEach(key => {
            if (filters[key]) url.searchParams.append(key, filters[key]);
        });

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTable(tabName, data.data);
                }
            })
            .catch(error => console.error('Error loading data:', error));
    }

    function getFiltersForTab(tabName) {
        const filters = {};
        document.querySelectorAll(`[data-tab="${tabName}"]`).forEach(el => {
            if (el.classList.contains('filter-status')) filters.status = el.value;
            if (el.classList.contains('filter-tanggal-dari')) filters.tanggal_dari = el.value;
            if (el.classList.contains('filter-tanggal-sampai')) filters.tanggal_sampai = el.value;
            if (el.classList.contains('filter-search')) filters.search = el.value;
            if (el.classList.contains('filter-no-spk')) filters.no_spk = el.value;
            if (el.classList.contains('filter-pembeli')) filters.nama_pembeli = el.value;
        });
        return filters;
    }

    function renderTable(tabName, data) {
        const tbodyId = 'tbody-' + tabName;
        const tbody = document.getElementById(tbodyId);

        if (!data || data.length === 0) {
            const colspans = {
                'pemasok': 7,
                'produksi': 8,
                'pembeli': 8,
                'masuk-produksi': 9
            };
            const colspan = colspans[tabName] || 8;
            tbody.innerHTML = `<tr><td colspan="${colspan}" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
            return;
        }

        let html = '';
        data.forEach(item => {
            html += renderRow(tabName, item);
        });
        tbody.innerHTML = html;
    }

    function renderRow(tabName, item) {
        const statusBadges = {
            'pemasok': {
                'pending': 'warning',
                'diterima': 'success',
                'ditolak': 'danger'
            },
            'produksi': {
                'pending': 'warning',
                'terima': 'success',
                'ditolak': 'danger'
            },
            'pembeli': {
                'pending': 'warning',
                'dikirim': 'success',
                'dibatalkan': 'danger'
            },
            'masuk-produksi': {
                'pending': 'warning',
                'diterima': 'success',
                'ditolak': 'danger'
            }
        };

        let badgeColor = statusBadges[tabName][item.status] || 'secondary';

        if (tabName === 'pemasok') {
            return `<tr>
            <td>${item.tanggal}</td>
            <td>${item.nomor_spk}</td>
            <td>${formatDate(item.tanggal)}</td>
            <td>${item.nama_pemasok}</td>
            <td>${item.nama_barang}</td>
            <td>${item.jumlah} ${item.satuan}</td>
            <td><span class="badge bg-${badgeColor}">${item.status}</span></td>
            <td>
                ${canEdit ? `<div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-sm" onclick="editData('${tabName}', ${item.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteData('${tabName}', ${item.id})"><i class="fas fa-trash"></i></button>
                </div>` : '<span class="text-muted small">-</span>'}
            </td>
        </tr>`;
        } else if (tabName === 'produksi') {
            return `<tr>
            <td>${formatDate(item.tanggal)}</td>
            <td>${item.nomor_spk}</td>
            <td>${item.no_spk || '-'}</td>
            <td>${item.nama_barang}</td>
            <td>${item.jumlah_keluar} ${item.satuan}</td>
            <td>${item.jumlah_terima || '-'} ${item.satuan}</td>
            <td><span class="badge bg-${badgeColor}">${item.status}</span></td>
            <td>
                ${canEdit ? `<div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-sm" onclick="editData('${tabName}', ${item.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteData('${tabName}', ${item.id})"><i class="fas fa-trash"></i></button>
                </div>` : '<span class="text-muted small">-</span>'}
            </td>
        </tr>`;
        } else if (tabName === 'pembeli') {
            return `<tr>
            <td>${formatDate(item.tanggal)}</td>
            <td>${item.nomor_spk}</td>
            <td>${item.nama_pembeli}</td>
            <td>${item.nama_barang}</td>
            <td>${item.jumlah} ${item.satuan}</td>
            <td>Rp ${formatCurrency(item.total_harga)}</td>
            <td><span class="badge bg-${badgeColor}">${item.status}</span></td>
            <td>
                ${canEdit ? `<div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-sm" onclick="editData('${tabName}', ${item.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteData('${tabName}', ${item.id})"><i class="fas fa-trash"></i></button>
                </div>` : '<span class="text-muted small">-</span>'}
            </td>
        </tr>`;
        } else if (tabName === 'masuk-produksi') {
            return `<tr>
            <td>${formatDate(item.tanggal)}</td>
            <td>${item.nomor_spk}</td>
            <td>${item.no_spk || '-'}</td>
            <td>${item.nama_barang}</td>
            <td>${item.jumlah_produksi} ${item.satuan}</td>
            <td>${item.jumlah_reject || 0} ${item.satuan}</td>
            <td>${item.jumlah_diterima} ${item.satuan}</td>
            <td><span class="badge bg-${badgeColor}">${item.status}</span></td>
            <td>
                ${canEdit ? `<div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-sm" onclick="editData('${tabName}', ${item.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteData('${tabName}', ${item.id})"><i class="fas fa-trash"></i></button>
                </div>` : '<span class="text-muted small">-</span>'}
            </td>
        </tr>`;
        }
    }

    function formatDate(date) {
        return new Date(date).toLocaleDateString('id-ID');
    }

    function formatCurrency(amount) {
        return parseFloat(amount).toLocaleString('id-ID');
    }

    function savePemasok() {
        const form = document.getElementById('formPemasok');
        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Belum Lengkap',
                text: 'Mohon lengkapi semua field yang wajib diisi',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        const data = {
            nomor_spk: document.getElementById('noDokumenPemasok').value,
            tanggal: document.getElementById('tanggalPemasok').value,
            nama_pemasok: document.getElementById('namaPemasok').value,
            kode_barang: document.getElementById('kodeBarangPemasok').value,
            nama_barang: document.getElementById('namaBarangPemasok').value,
            satuan: document.getElementById('satuanPemasok').value,
            jumlah: document.getElementById('jumlahPemasok').value,
            harga_satuan: document.getElementById('hargaSatuanPemasok').value || 0,
            no_po: document.getElementById('noPoPemasok').value,
            no_surat_jalan: document.getElementById('noSuratJalanPemasok').value,
            catatan: document.getElementById('catatanPemasok').value,
            status: document.getElementById('statusPemasok').value,
            total_harga: (parseFloat(document.getElementById('jumlahPemasok').value) * parseFloat(document.getElementById('hargaSatuanPemasok').value || 0))
        };

        fetch('<?= base_url('warehouse2/store-pemasok') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok && response.status !== 201) {
                    return response.text().then(text => { throw new Error('Server error ' + response.status + ': ' + text.substring(0, 300)); });
                }
                return response.json();
            })
            .then(data => {
                if (data.success || data.status === 'success') {
                    // Close modal using simple, reliable method
                    const modalEl = document.getElementById('modalPemasok');
                    const closeBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
                    if (closeBtn) closeBtn.click();

                    // Small delay to ensure modal closes
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Berhasil Ditambahkan',
                            text: 'Data barang masuk pemasok telah ditambahkan',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                        loadPemasokData(1);
                    }, 300);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Data',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Data',
                    text: 'Error: ' + error.message,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            });
    }

    function openProduksiForm() {
        // Reset form
        document.getElementById('formProduksi').reset();
        document.getElementById('produksiEditId').value = '';
        
        // Reset modal title
        document.getElementById('modalProduksiTitle').textContent = 'Tambah Barang Masuk Produksi';
        
        const modalElement = document.getElementById('modalProduksi');
        
        // Destroy existing instance to avoid backdrop stacking
        const existingModal = bootstrap.Modal.getInstance(modalElement);
        if (existingModal) {
            existingModal.dispose();
        }

        // Clean up any orphaned backdrops
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true
        });
        modal.show();
    }

    function saveProduksi() {
        const form = document.getElementById('formProduksi');
        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Belum Lengkap',
                text: 'Mohon lengkapi semua field yang wajib diisi',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        const produksiId = document.getElementById('produksiEditId').value;
        const isEditMode = Boolean(produksiId);

        const data = {
            nomor_spk: document.getElementById('noDokumenProduksi').value,
            tanggal: document.getElementById('tanggalProduksi').value,
            kode_barang: document.getElementById('kodeBarangProduksi').value,
            nama_barang: document.getElementById('namaBarangProduksi').value,
            satuan: document.getElementById('satuanProduksi').value,
            jumlah_keluar: document.getElementById('jumlahKeluarProduksi').value,
            jumlah_terima: document.getElementById('jumlahTerimaProduksi').value || 0,
            nama_penerima: document.getElementById('namaPenerimaProduksi').value,
            catatan: document.getElementById('catatanProduksi').value,
            status: document.getElementById('statusProduksi').value
        };

        const url = isEditMode 
            ? `<?= base_url('warehouse2/update-produksi') ?>/${produksiId}`
            : '<?= base_url('warehouse2/store-produksi') ?>';

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.status === 'success') {
                    // Close modal using Bootstrap Modal API
                    const modalEl = document.getElementById('modalProduksi');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }

                    // Small delay to ensure modal closes
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: isEditMode ? 'Data Berhasil Diubah' : 'Data Berhasil Ditambahkan',
                            text: isEditMode ? 'Data barang masuk produksi telah diperbarui' : 'Data barang masuk produksi telah ditambahkan',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                        loadTabData('produksi');
                    }, 300);
                } else {
                    console.error('Validation errors:', data.errors);
                    const errorText = data.errors ? 
                        Object.values(data.errors).join('\n') : 
                        (data.message || 'Terjadi kesalahan saat menyimpan data');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Data',
                        text: errorText,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Data',
                    text: 'Error: ' + error.message,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            });
    }

    function editProduksi(id) {
        // Fetch data dari API
        fetch(`<?= base_url('warehouse2/api/barang-masuk-produksi') ?>?search=${id}`)
            .then(response => response.json())
            .then(result => {
                if ((result.success || result.status === 'success') && result.data && result.data.length > 0) {
                    const item = result.data.find(x => x.id == id);
                    if (!item) {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Data tidak ditemukan' });
                        return;
                    }

                    // Populate form
                    document.getElementById('produksiEditId').value = item.id;
                    document.getElementById('noDokumenProduksi').value = item.nomor_spk || '';
                    document.getElementById('tanggalProduksi').value = item.tanggal ? item.tanggal.split(' ')[0] : '';
                    document.getElementById('kodeBarangProduksi').value = item.kode_barang || '';
                    document.getElementById('namaBarangProduksi').value = item.nama_barang || '';
                    document.getElementById('satuanProduksi').value = item.satuan || '';
                    document.getElementById('jumlahKeluarProduksi').value = parseInt(item.jumlah_keluar) || '';
                    document.getElementById('jumlahTerimaProduksi').value = parseInt(item.jumlah_terima) || '';
                    document.getElementById('namaPenerimaProduksi').value = item.nama_penerima || '';
                    document.getElementById('catatanProduksi').value = item.catatan || '';
                    document.getElementById('statusProduksi').value = item.status || 'pending';

                    // Update modal title
                    document.getElementById('modalProduksiTitle').textContent = 'Edit Barang Masuk Produksi';

                    // Destroy existing instance to avoid backdrop stacking
                    const modalElement = document.getElementById('modalProduksi');
                    const existingModal = bootstrap.Modal.getInstance(modalElement);
                    if (existingModal) {
                        existingModal.dispose();
                    }
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: true,
                        keyboard: true
                    });
                    modal.show();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Data tidak ditemukan' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memuat data' });
            });
    }

    function openProduksiForm() {
        // Reset form
        document.getElementById('formProduksi').reset();
        document.getElementById('produksiEditId').value = '';
        
        // Reset modal title
        document.getElementById('modalProduksiTitle').textContent = 'Tambah Barang Masuk Produksi';
        
        // Show modal
        const modalElement = document.getElementById('modalProduksi');
        const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });
        modal.show();
    }

    function savePembeli() {
        const form = document.getElementById('formPembeli');
        if (!form.checkValidity()) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Belum Lengkap',
                text: 'Mohon lengkapi semua field yang wajib diisi',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        const pembeliId = document.getElementById('pembeliEditId').value;
        const isEditMode = Boolean(pembeliId);

        const data = {
            nomor_spk: document.getElementById('noDokumenPembeli').value,
            tanggal: document.getElementById('tanggalPembeli').value,
            nama_pembeli: document.getElementById('namaPembeli').value,
            kode_barang: document.getElementById('kodeBarangPembeli').value,
            nama_barang: document.getElementById('namaBarangPembeli').value,
            satuan: document.getElementById('satuanPembeli').value,
            jumlah: document.getElementById('jumlahPembeli').value,
            no_invoice: document.getElementById('noInvoicePembeli').value,
            alamat_pengiriman: document.getElementById('alamatPengirimanPembeli').value,
            catatan: document.getElementById('catatanPembeli').value,
            status: document.getElementById('statusPembeli').value
        };

        const url = isEditMode 
            ? `<?= base_url('warehouse2/update-pembeli') ?>/${pembeliId}`
            : '<?= base_url('warehouse2/store-pembeli') ?>';

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' || data.success) {
                    // Close modal using simple, reliable method
                    const modalEl = document.getElementById('modalPembeli');
                    const closeBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
                    if (closeBtn) closeBtn.click();

                    // Small delay to ensure modal closes
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: isEditMode ? 'Data Berhasil Diubah' : 'Data Berhasil Ditambahkan',
                            text: isEditMode ? 'Data barang keluar pembeli telah diperbarui' : 'Data barang keluar pembeli telah ditambahkan',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                        loadPembeliData(1);
                    }, 300);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Data',
                        text: data.message || 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Data',
                    text: 'Error: ' + error.message,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            });
    }

    function saveMasukProduksi() {
        const form = document.getElementById('formMasukProduksi');
        if (!form.checkValidity()) {
            alert('Mohon lengkapi semua field yang wajib diisi');
            return;
        }

        const data = {
            nomor_spk: document.getElementById('noDokumenMasukProduksi').value,
            tanggal: document.getElementById('tanggalMasukProduksi').value,
            no_spk: document.getElementById('noSpkMasukProduksi').value,
            nama_produksi: document.getElementById('namaProduksiMasuk').value,
            kode_barang: document.getElementById('kodeBarangMasukProduksi').value,
            nama_barang: document.getElementById('namaBarangMasukProduksi').value,
            satuan: document.getElementById('satuanMasukProduksi').value,
            jumlah_produksi: document.getElementById('jumlahProduksiMasuk').value,
            jumlah_reject: document.getElementById('jumlahRejectMasuk').value || 0,
            jumlah_diterima: document.getElementById('jumlahDiterimaMasuk').value,
            nama_penerima: document.getElementById('namaPenerimaMasukProduksi').value,
            catatan: document.getElementById('catatanMasukProduksi').value,
            status: document.getElementById('statusMasukProduksi').value
        };

        fetch('<?= base_url('warehouse2/store-masuk-produksi') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Data berhasil ditambahkan');
                    // Use data-bs-dismiss button to close modal (most reliable)
                    const modalEl = document.getElementById('modalMasukProduksi');
                    const closeBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
                    if (closeBtn) closeBtn.click();
                    loadTabData('masuk-produksi');
                } else {
                    alert('Error: ' + (data.message || 'Gagal menyimpan data'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menyimpan data');
            });
    }

    function editData(tab, id) {
        if (tab === 'pembeli') {
            editPembeli(id);
        } else {
            // Other tabs edit implementation
            alert('Edit functionality for ' + tab + ' with id ' + id);
        }
    }

    function editPembeli(itemOrId) {
        const populateForm = (item) => {
            if (!item) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Data tidak ditemukan'
                });
                return;
            }

            // Populate form fields
            document.getElementById('pembeliEditId').value = item.id || '';
            document.getElementById('noDokumenPembeli').value = item.nomor_spk || item.no_dokumen || '';
            document.getElementById('tanggalPembeli').value = item.tanggal ? item.tanggal.split(' ')[0] : '';
            document.getElementById('namaPembeli').value = item.nama_pembeli || '';
            document.getElementById('kodeBarangPembeli').value = item.kode_barang || '';
            document.getElementById('namaBarangPembeli').value = item.nama_barang || '';
            document.getElementById('satuanPembeli').value = item.satuan || '';
            document.getElementById('jumlahPembeli').value = parseInt(item.jumlah) || '';
            document.getElementById('noInvoicePembeli').value = item.no_invoice || '';
            document.getElementById('alamatPengirimanPembeli').value = item.alamat_pengiriman || '';
            document.getElementById('catatanPembeli').value = item.catatan || '';
            document.getElementById('statusPembeli').value = item.status || 'pending';

            // Update modal title
            document.getElementById('modalPembeliTitle').textContent = 'Edit Barang Keluar Pembeli';

            // Show modal
            const modalElement = document.getElementById('modalPembeli');
            const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        };

        if (typeof itemOrId === 'object' && itemOrId !== null) {
            populateForm(itemOrId);
        } else {
            fetch(`<?= base_url('warehouse2/api/barang-keluar-pembeli') ?>?per_page=100`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const item = (data.data || []).find(x => x.id == itemOrId);
                    populateForm(item);
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat data'
                    });
                });
        }
    }

    function deleteData(tab, id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            const endpoints = {
                'pemasok': '<?= base_url('warehouse2/delete-pemasok') ?>',
                'produksi': '<?= base_url('warehouse2/delete-produksi') ?>',
                'pembeli': '<?= base_url('warehouse2/delete-pembeli') ?>',
                'masuk-produksi': '<?= base_url('warehouse2/delete-orowarehouse') ?>'
            };

            const url = endpoints[tab] + '/' + id;

            fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json().then(data => ({
                    ok: response.ok,
                    status: response.status,
                    data
                })).catch(() => ({
                    ok: response.ok,
                    status: response.status,
                    data: null
                })))
                .then(({
                    ok,
                    status,
                    data
                }) => {
                    // Support both older responses that return { status: 'success' } and newer { success: true }
                    const isSuccess = data && (data.success === true || (typeof data.status === 'string' && data.status.toLowerCase() === 'success'));
                    const message = data && (data.message || data.msg || data.error) ? (data.message || data.msg || data.error) : null;

                    if (isSuccess) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Berhasil Dihapus',
                            text: message || 'Data telah dihapus dari sistem',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK',
                            timer: 2000
                        }).then(() => {
                            loadTabData(tab);
                        });
                        return;
                    }

                    // If not success, show error message from server when available
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus Data',
                        text: message || ('HTTP ' + status),
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus Data',
                        text: 'Error: ' + error.message,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                });
        });
    }

    // ============ OROWAREHOUSE FUNCTIONS ============

    let currentOrowarehousePage = 1;
    const orowarehousePageSize = 10;

    /**
     * Set Period Filter
     */
    function setPeriodFilter(period, element) {
        event.preventDefault();

        // Update label
        const labelMap = {
            '': 'Semua Periode',
            'day': 'Hari Ini',
            'week': 'Minggu Ini',
            'month': 'Bulan Ini'
        };

        document.getElementById('oroPeriodLabel').textContent = labelMap[period] || 'Semua Periode';

        // Store in session/localStorage
        sessionStorage.setItem('oroPeriodFilter', period);

        // Reload data
        loadOrowarehouseData(1);
    }

    /**
     * Get Period Filter Value
     */
    function getOroPeriodFilter() {
        return sessionStorage.getItem('oroPeriodFilter') || '';
    }

    /**
     * Apply Custom Filter
     */
    function applyOroCustomFilter() {
        loadOrowarehouseData(1);
        updateFilterCount();

        // Close offcanvas
        const offcanvas = document.getElementById('oroCustomFilterDrawer');
        const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvas);
        if (offcanvasInstance) offcanvasInstance.hide();
    }

    /**
     * Reset Custom Filter
     */
    function resetOroCustomFilter() {
        document.getElementById('oroFilterTanggal').value = '';
        document.getElementById('oroFilterShift').value = '';
        document.getElementById('oroFilterStatus').value = '';
        document.getElementById('oroFilterGrup').value = '';
        document.getElementById('oroFilterOperator').value = '';
        document.getElementById('oroSearchQuery').value = '';

        sessionStorage.setItem('oroPeriodFilter', '');
        document.getElementById('oroPeriodLabel').textContent = 'Semua Periode';

        updateFilterCount();
        loadOrowarehouseData(1);
    }

    /**
     * Update Filter Count Badge
     */
    function updateFilterCount() {
        let count = 0;
        if (document.getElementById('oroFilterTanggal').value) count++;
        if (document.getElementById('oroFilterShift').value) count++;
        if (document.getElementById('oroFilterStatus').value) count++;
        if (document.getElementById('oroFilterGrup').value) count++;
        if (document.getElementById('oroFilterOperator').value) count++;

        const badge = document.getElementById('oroFilterCount');
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    /**
     * Load orowarehouse data dengan pagination & filter
     */
    function loadOrowarehouseData(page = 1) {
        currentOrowarehousePage = page;

        // Get all filter values with null-safe access
        const perPageSelect = document.getElementById('perPageSelect');
        const oroFilterTanggal = document.getElementById('oroFilterTanggal');
        const oroFilterShift = document.getElementById('oroFilterShift');
        const oroFilterStatus = document.getElementById('oroFilterStatus');
        const oroFilterGrup = document.getElementById('oroFilterGrup');
        const oroFilterOperator = document.getElementById('oroFilterOperator');
        const oroSearchQuery = document.getElementById('oroSearchQuery');

        const filters = {
            page: page,
            per_page: perPageSelect ? (perPageSelect.value || 10) : 10,
            period: getOroPeriodFilter(),
            tanggal: oroFilterTanggal ? (oroFilterTanggal.value || '') : '',
            shift: oroFilterShift ? (oroFilterShift.value || '') : '',
            status: oroFilterStatus ? (oroFilterStatus.value || '') : '',
            grup: oroFilterGrup ? (oroFilterGrup.value || '') : '',
            operator: oroFilterOperator ? (oroFilterOperator.value || '') : '',
            search: oroSearchQuery ? (oroSearchQuery.value || '') : ''
        };

        const url = new URL('<?= base_url('warehouse2/api/orowarehouse') ?>');
        Object.keys(filters).forEach(key => {
            if (filters[key]) url.searchParams.append(key, filters[key]);
        });

        // Update export link with current filters
        updateExportLink(filters);

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderOrowarehouseTable(data.data, data.summary);
                    updateOrowarehousePagination(data.pager);
                }
            })
            .catch(error => {
                console.error('Error loading orowarehouse data:', error);
                alert('Gagal memuat data');
            });
    }

    /**
     * Update export link with current filters
     */
    function updateExportLink(filters) {
        const exportLink = document.getElementById('exportOroLink');
        if (!exportLink) return;

        let url = '<?= base_url('warehouse2/export-orowarehouse') ?>';
        const params = [];

        if (filters.period) params.push('period=' + filters.period);
        if (filters.tanggal) params.push('tanggal=' + filters.tanggal);
        if (filters.shift) params.push('shift=' + filters.shift);
        if (filters.status) params.push('status=' + filters.status);
        if (filters.grup) params.push('grup=' + filters.grup);
        if (filters.operator) params.push('operator=' + filters.operator);
        if (filters.search) params.push('search=' + filters.search);

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        exportLink.href = url;
    }

    /**
     * Load Orowarehouse Trash Data
     */
    function loadOrowarehouseTrash() {
        fetch('<?= base_url('warehouse2/api/orowarehouse-trash') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderOrowarehouseTrashTable(data.data);
                }
            })
            .catch(error => {
                console.error('Error loading trash:', error);
                alert('Gagal memuat data sampah');
            });
    }

    /**
     * Render Orowarehouse Trash Table
     */
    function renderOrowarehouseTrashTable(data) {
        const tbody = document.getElementById('trashTbodyOro');

        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-inbox"></i> Belum ada data di sampah</td></tr>`;
            return;
        }

        let html = '';
        data.forEach(item => {
            html += `<tr>
            <td>${formatDate(item.tanggal)}</td>
            <td><strong>${item.spk}</strong></td>
            <td>${item.produk}</td>
            <td>${item.shift}</td>
            <td>${item.grup || '-'}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-success btn-sm" onclick="restoreOrowarehouseRecord(${item.id})" title="Pulihkan">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deletePermanentOrowarehouseRecord(${item.id})" title="Hapus Permanen">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        </tr>`;
        });

        tbody.innerHTML = html;
    }

    /**
     * Restore Orowarehouse Record from Trash
     */
    function restoreOrowarehouseRecord(id) {
        if (!confirm('Pulihkan data ini dari sampah?')) return;

        fetch(`<?= base_url('warehouse2/restore-orowarehouse') ?>/${id}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Berhasil Dipulihkan',
                        text: 'Data telah dipulihkan dari sampah',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK',
                        timer: 2000
                    }).then(() => {
                        loadOrowarehouseTrash();
                        loadOrowarehouseData(currentOrowarehousePage);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memulihkan Data',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memulihkan Data',
                    text: error.message,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            });
    }

    /**
     * Delete Orowarehouse Record Permanently
     */
    function deletePermanentOrowarehouseRecord(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Permanen?',
            text: 'Data akan dihapus secara permanen dan tidak dapat dikembalikan!',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus Permanen',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`<?= base_url('warehouse2/delete-permanent-orowarehouse') ?>/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data Berhasil Dihapus Permanen',
                            text: 'Data telah dihapus dari sistem',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK',
                            timer: 2000
                        }).then(() => {
                            loadOrowarehouseTrash();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus Data',
                            text: data.message || 'Terjadi kesalahan',
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus Data',
                        text: error.message,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                });
        });
    }

    /**
     * Render orowarehouse table
     */
    function renderOrowarehouseTable(data, summary = null) {
        const tbody = document.getElementById('tbodyOrowarehouse');

        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="13" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
            updateOrowarehouseTotals([], summary);
            return;
        }

        let html = '';
        data.forEach(item => {
            const statusBadge = getStatusBadge(item.status);
            html += `<tr>
            <td>${formatDate(item.tanggal)}</td>
            <td><strong>${item.spk}</strong></td>
            <td>${item.produk}</td>
            <td>${item.shift}</td>
            <td>${item.grup || '-'}</td>
            <td>${parseInt(item.hasil_bagus)}</td>
            <td>${parseInt(item.hasil_reject)}</td>
            <td>${item.material || '-'}</td>
            <td>${item.colorant || '-'}</td>
            <td>${item.packaging || '-'}</td>
            <td>${item.operator || '-'}</td>
            <td>${statusBadge}</td>
            <td>
                ${canEdit ? `<div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-sm" onclick='editOrowarehouse(${JSON.stringify(item)})'><i class="fas fa-edit"></i></button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteOrowarehouseRecord(${item.id})"><i class="fas fa-trash"></i></button>
                </div>` : '<span class="text-muted small">-</span>'}
            </td>
        </tr>`;
        });

        tbody.innerHTML = html;
        updateOrowarehouseTotals(data, summary);
    }

    /**
     * Calculate and update total Bagus and Reject
     */
    function updateOrowarehouseTotals(data, summary = null) {
        let totalBagus = 0;
        let totalReject = 0;

        if (summary && summary.total_bagus !== undefined) {
            totalBagus = parseInt(summary.total_bagus) || 0;
            totalReject = parseInt(summary.total_reject) || 0;
        } else if (data && data.length > 0) {
            data.forEach(item => {
                totalBagus += parseInt(item.hasil_bagus) || 0;
                totalReject += parseInt(item.hasil_reject) || 0;
            });
        }

        const totalBagusDisplay = document.getElementById('totalBagusDisplay');
        const totalRejectDisplay = document.getElementById('totalRejectDisplay');

        if (totalBagusDisplay) {
            totalBagusDisplay.innerHTML = `<i class="fas fa-check-circle me-1"></i>Bagus: ${totalBagus}`;
        }
        if (totalRejectDisplay) {
            totalRejectDisplay.innerHTML = `<i class="fas fa-times-circle me-1"></i>Reject: ${totalReject}`;
        }
    }

    /**
     * Update pagination controls
     */
    function updateOrowarehousePagination(pager) {
        document.getElementById('currentPageNum').textContent = pager.current_page;
        document.getElementById('totalPages').textContent = pager.total_pages;

        // Update prev button
        const prevBtn = document.getElementById('prevBtn');
        if (pager.has_prev) {
            prevBtn.classList.remove('disabled');
            prevBtn.querySelector('a').onclick = (e) => {
                e.preventDefault();
                loadOrowarehouseData(pager.current_page - 1);
            };
        } else {
            prevBtn.classList.add('disabled');
        }

        // Update next button
        const nextBtn = document.getElementById('nextBtn');
        if (pager.has_next) {
            nextBtn.classList.remove('disabled');
            nextBtn.querySelector('a').onclick = (e) => {
                e.preventDefault();
                loadOrowarehouseData(pager.current_page + 1);
            };
        } else {
            nextBtn.classList.add('disabled');
        }
    }

    /**
     * Get status badge HTML
     */
    function getStatusBadge(status) {
        const badges = {
            'bahan masuk': '<span class="badge bg-info">Bahan Masuk</span>',
            'bahan keluar': '<span class="badge bg-warning">Bahan Keluar</span>',
            'menggiling': '<span class="badge bg-primary">Menggiling</span>'
        };
        return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
    }

    /**
     * Load filter dropdown options
     */
    function loadOrowarehouseFilters() {
        fetch('<?= base_url('warehouse2/api/orowarehouse/filters') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate grup dropdown
                    const grupSelect = document.getElementById('oroFilterGrup');
                    if (grupSelect) {
                        data.grups.forEach(grup => {
                            const option = document.createElement('option');
                            option.value = grup;
                            option.textContent = grup;
                            grupSelect.appendChild(option);
                        });
                    }

                    // Populate operator dropdown
                    const operatorSelect = document.getElementById('oroFilterOperator');
                    if (operatorSelect) {
                        data.operators.forEach(operator => {
                            const option = document.createElement('option');
                            option.value = operator;
                            option.textContent = operator;
                            operatorSelect.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => console.error('Error loading filters:', error));
    }

    /**
     * Save orowarehouse data
     */
    function saveOrowarehouse() {
        const form = document.getElementById('formOrowarehouse');
        const submitBtn = document.getElementById('btnSaveOrowarehouse');

        // Validasi field yang diperlukan (tanpa yang disabled)
        const tanggal = document.getElementById('oroTanggal').value.trim();
        const spk = document.getElementById('oroSpk').value.trim();
        const shift = document.getElementById('oroShift').value.trim();
        const status = document.getElementById('oroStatus').value.trim();

        if (!tanggal || !spk || !shift || !status) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Belum Lengkap',
                text: 'Mohon lengkapi semua field yang wajib diisi (Tanggal, SPK, Shift, Status)',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }

        const id = document.getElementById('oroId').value;
        const isEdit = id !== '';

        // Ensure operator is filled with current username if empty
        let operator = document.getElementById('oroOperator').value || currentUsername;

        const data = {
            tanggal: tanggal,
            spk: spk,
            produk: document.getElementById('oroProduk').value,
            shift: shift,
            grup: document.getElementById('oroGrup').value || null,
            hasil_bagus: document.getElementById('oroHasilBagus').value,
            hasil_reject: document.getElementById('oroHasilReject').value,
            material: document.getElementById('oroMaterial').value || null,
            colorant: document.getElementById('oroColorant').value || null,
            packaging: document.getElementById('oroPackaging').value || null,
            operator: operator,
            nomor_lot: document.getElementById('oroNomorLot').value || null,
            status: status,
            catatan: document.getElementById('oroCatatan').value || null
        };

        const url = isEdit ?
            '<?= base_url('warehouse2/update-orowarehouse') ?>/' + id :
            '<?= base_url('warehouse2/store-orowarehouse') ?>';

        // Disable submit button and show loading state
        submitBtn.disabled = true;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    // Close modal using simple, reliable method
                    const modalElement = document.getElementById('modalOrowarehouse');
                    const closeBtn = modalElement.querySelector('[data-bs-dismiss="modal"]');
                    if (closeBtn) closeBtn.click();

                    form.reset();
                    document.getElementById('oroId').value = '';

                    // Small delay to ensure modal closes
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: isEdit ? 'Data Berhasil Diupdate' : 'Data Berhasil Ditambahkan',
                            text: isEdit ? 'Data telah diperbarui' : 'Data barang masuk dari produksi telah ditambahkan',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                        loadOrowarehouseData(isEdit ? currentOrowarehousePage : 1);
                    }, 300);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan Data',
                        text: result.message || 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Data',
                    text: 'Error: ' + error.message,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Re-enable submit button and restore original text
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    }

    /**
     * Autofill dari dropdown SPK selection
     */
    function autofillFromSpkSelect() {
        const spk = document.getElementById('oroSpk').value.trim();

        if (!spk) {
            // Reset all fields except operator
            document.getElementById('oroProduk').value = '';
            document.getElementById('oroShift').value = '';
            document.getElementById('oroGrup').value = '';
            // DO NOT reset operator - keep current username
            document.getElementById('oroMaterial').value = '';
            document.getElementById('oroColorant').value = '';
            document.getElementById('oroPackaging').value = '';
            document.getElementById('oroHasilBagus').value = '';
            document.getElementById('oroHasilReject').value = '';
            showOroAutofillState('hide');
            return;
        }

        // Tampilkan loading state - DISABLED
        // showOroAutofillState('loading');

        fetch(`<?= base_url('warehouse2/api/orowarehouse/by-spk') ?>?spk=${encodeURIComponent(spk)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const d = data.data;
                    document.getElementById('oroProduk').value = d.produk || '';
                    document.getElementById('oroShift').value = d.shift || '';
                    document.getElementById('oroGrup').value = d.grup || '';
                    // DO NOT set operator from produksi - keep current username
                    // document.getElementById('oroOperator').value = d.operator || '';
                    document.getElementById('oroMaterial').value = d.material || '';
                    document.getElementById('oroColorant').value = d.colorant || '';
                    document.getElementById('oroPackaging').value = d.packaging || '';
                    document.getElementById('oroHasilBagus').value = d.hasil_bagus || '';
                    document.getElementById('oroHasilReject').value = d.hasil_reject || '';

                    // Isi tanggal dari produksi jika field tanggal masih kosong
                    if (!document.getElementById('oroTanggal').value && d.tanggal) {
                        document.getElementById('oroTanggal').value = d.tanggal;
                    }

                    showOroAutofillState('success');
                } else {
                    showOroAutofillState('error', data.message || 'SPK tidak ditemukan di data produksi.');
                }
            })
            .catch(error => {
                console.error('Error autofill:', error);
                showOroAutofillState('error', 'Gagal menghubungi server.');
            });
    }

    /**
     * Load pembeli data with pagination
     */
    function loadPembeliData(page = 1) {
        const filters = getPembeliFilters();
        const perPage = document.getElementById('perPageSelectPembeli')?.value || 10;
        
        let url = '<?= base_url('warehouse2/api/barang-keluar-pembeli') ?>';
        const params = new URLSearchParams({
            page: page,
            per_page: perPage,
            ...filters
        });
        
        fetch(`${url}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderPembeliTable(data.data);
                    updatePembeliPagination(data.pager);
                }
            })
            .catch(error => console.error('Error loading pembeli data:', error));
    }

    /**
     * Render pembeli table
     */
    function renderPembeliTable(data) {
        const tbody = document.getElementById('tbodyPembeli');
        
        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
            return;
        }
        
        let html = '';
        data.forEach(item => {
            const statusBadge = getStatusBadgeForPembeli(item.status);
            html += `<tr>
                <td>${formatDateForPembeli(item.tanggal)}</td>
                <td><strong>${item.nomor_spk}</strong></td>
                <td>${item.nama_pembeli}</td>
                <td>${item.nama_barang}</td>
                <td>${item.jumlah} ${item.satuan}</td>
                <td>${statusBadge}</td>
                <td>
                    ${canEdit ? `<div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-sm" onclick="editPembeli(${item.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-outline-danger btn-sm" onclick="deleteData('pembeli', ${item.id})"><i class="fas fa-trash"></i></button>
                    </div>` : '<span class="text-muted small">-</span>'}
                </td>
            </tr>`;
        });
        
        tbody.innerHTML = html;
    }

    /**
     * Update pembeli pagination
     */
    function updatePembeliPagination(pager) {
        document.getElementById('currentPageNumPembeli').textContent = pager.current_page;
        document.getElementById('totalPagesPembeli').textContent = pager.total_pages;
        
        const prevBtn = document.getElementById('prevBtnPembeli');
        if (pager.has_prev) {
            prevBtn.classList.remove('disabled');
            prevBtn.querySelector('a').onclick = (e) => {
                e.preventDefault();
                loadPembeliData(pager.current_page - 1);
            };
        } else {
            prevBtn.classList.add('disabled');
        }
        
        const nextBtn = document.getElementById('nextBtnPembeli');
        if (pager.has_next) {
            nextBtn.classList.remove('disabled');
            nextBtn.querySelector('a').onclick = (e) => {
                e.preventDefault();
                loadPembeliData(pager.current_page + 1);
            };
        } else {
            nextBtn.classList.add('disabled');
        }
    }

    /**
     * Get pembeli filters
     */
    function getPembeliFilters() {
        return {
            status: document.getElementById('pembeliFilterStatus')?.value || '',
            tanggal_dari: document.getElementById('pembeliFilterTanggalDari')?.value || '',
            tanggal_sampai: document.getElementById('pembeliFilterTanggalSampai')?.value || '',
            nama_pembeli: document.getElementById('pembeliFilterNamaPembeli')?.value || '',
            search: document.getElementById('pembeliSearchQuery')?.value || ''
        };
    }

    /**
     * Get status badge for pembeli
     */
    function getStatusBadgeForPembeli(status) {
        const badgeClasses = {
            'pending': 'warning',
            'dikirim': 'success',
            'dibatalkan': 'danger'
        };
        const color = badgeClasses[status] || 'secondary';
        return `<span class="badge bg-${color}">${status}</span>`;
    }

    /**
     * Format date helper
     */
    function formatDateForPembeli(date) {
        return new Date(date).toLocaleDateString('id-ID');
    }

    /**
     * Format currency helper
     */
    function formatCurrencyForPembeli(amount) {
        return parseFloat(amount).toLocaleString('id-ID');
    }

    /**
     * Init functions for each tab - delegates to inlined pagination functions
     */
    function initPemasokTab() {
        loadPemasokData(1);
    }

    function initProduksiTab() {
        loadProduksiData(1);
    }

    function initPembeliTab() {
        loadPembeliData(1);
    }

    /**
     * Tampilkan state autofill: 'loading' | 'success' | 'error' | 'hide'
     */
    function showOroAutofillState(state, msg) {
        const loading = document.getElementById('oroAutofillAlert');
        const success = document.getElementById('oroAutofillSuccess');
        const error = document.getElementById('oroAutofillError');
        const spinner = document.getElementById('oroSpkSpinner');

        loading.style.display = 'none';
        success.style.display = 'none';
        error.style.display = 'none';
        if (spinner) spinner.style.display = 'none';

        if (state === 'loading') {
            loading.style.display = 'flex';
            if (spinner) spinner.style.display = 'inline-block';
        } else if (state === 'success') {
            success.style.display = 'block';
            setTimeout(() => {
                success.style.display = 'none';
            }, 3000);
        } else if (state === 'error') {
            document.getElementById('oroAutofillErrorMsg').textContent = msg || 'SPK tidak ditemukan.';
            error.style.display = 'block';
        }
    }

    /**
     * Load SPK dropdown dari produksi saat modal dibuka
     */
    function loadSpkDropdown() {
        const select = document.getElementById('oroSpk');

        // Clear existing options
        select.innerHTML = '<option value="">-- Pilih SPK dari Produksi --</option>';

        fetch(`<?= base_url('warehouse2/api/orowarehouse/spk-list') ?>`)
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data.length) {
                    // Sort by date (earliest first)
                    res.data.sort((a, b) => {
                        const dateA = new Date(a.tanggal);
                        const dateB = new Date(b.tanggal);
                        return dateA - dateB;
                    });

                    res.data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.spk;
                        // Format: SPK - Produk (Tanggal)
                        const formattedDate = new Date(item.tanggal).toLocaleDateString('id-ID');
                        option.textContent = `${item.spk} - ${item.produk} (${formattedDate})`;
                        select.appendChild(option);
                    });
                }
            })
            .catch(err => console.error('Load SPK error:', err));
    }

    /**
     * Edit orowarehouse record - load data into modal
     */
    function editOrowarehouse(item) {
        populateOrowarehouseEditForm(item);
    }

    /**
     * Populate modal form with existing data for editing
     */
    function populateOrowarehouseEditForm(item) {
        document.getElementById('oroId').value = item.id;
        document.getElementById('modalOrowarehouseTitle').textContent = 'Edit Barang Masuk dari Produksi';

        document.getElementById('oroTanggal').value = item.tanggal ? item.tanggal.substring(0, 10) : '';
        document.getElementById('oroSpk').value = item.spk || '';
        document.getElementById('oroProduk').value = item.produk || '';
        document.getElementById('oroShift').value = item.shift || '';
        document.getElementById('oroGrup').value = item.grup || '';
        document.getElementById('oroHasilBagus').value = item.hasil_bagus || 0;
        document.getElementById('oroHasilReject').value = item.hasil_reject || 0;
        document.getElementById('oroMaterial').value = item.material || '';
        document.getElementById('oroColorant').value = item.colorant || '';
        document.getElementById('oroPackaging').value = item.packaging || '';
        // Always use current username as operator, not from database
        document.getElementById('oroOperator').value = currentUsername;
        document.getElementById('oroNomorLot').value = item.nomor_lot || '';
        document.getElementById('oroStatus').value = item.status || 'bahan masuk';
        document.getElementById('oroCatatan').value = item.catatan || '';

        // Sembunyikan alert autofill saat mode edit
        showOroAutofillState('hide');

        // Use existing modal instance, don't create new one
        const modalElement = document.getElementById('modalOrowarehouse');
        let modal = bootstrap.Modal.getInstance(modalElement);
        if (!modal) {
            modal = new bootstrap.Modal(modalElement);
        }
        modal.show();
    }

    /**
     * Delete orowarehouse record
     */
    function deleteOrowarehouseRecord(id) {
        Swal.fire({
            icon: 'question',
            title: 'Hapus Data?',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            fetch(`<?= base_url('warehouse2/delete-orowarehouse') ?>/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    try {
                        const result = JSON.parse(text);
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data Berhasil Dihapus',
                                text: 'Data telah dihapus dari sistem',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'OK',
                                timer: 2000
                            }).then(() => {
                                loadOrowarehouseData(currentOrowarehousePage);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menghapus Data',
                                text: result.message || 'Terjadi kesalahan',
                                confirmButtonColor: '#dc3545',
                                confirmButtonText: 'OK'
                            });
                        }
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError, 'Response:', text);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memproses Respons',
                            text: parseError.message,
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus Data',
                        text: 'Error: ' + error.message,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                });
        });
    }

    /**
     * Initialize orowarehouse tab
     */
    function initOrowarehouseTab() {
        loadOrowarehouseFilters();
        loadOrowarehouseData(1);
        loadSpkDropdown();

        // Set up event listeners for period filter
        const periodLinks = document.querySelectorAll('#oroFilterPeriod ~ .dropdown-menu a');
        periodLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
            });
        });

        // Set up event listeners for filters
        const filterIds = [
            'oroFilterTanggal', 'oroFilterShift', 'oroFilterStatus', 'oroFilterGrup', 'oroFilterOperator'
        ];
        filterIds.forEach(id => {
            const el = document.getElementById(id);
            if (el && !el.dataset.bound) {
                el.dataset.bound = '1';
                el.addEventListener('change', () => {
                    updateFilterCount();
                });
            }
        });

        // Search form
        const searchForm = document.getElementById('oroSearchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                loadOrowarehouseData(1);
            });
        }

        // Per page select
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect && !perPageSelect.dataset.bound) {
            perPageSelect.dataset.bound = '1';
            perPageSelect.addEventListener('change', () => loadOrowarehouseData(1));
        }

        // Trash modal
        const trashBtn = document.getElementById('btn_open_oro_trash');
        if (trashBtn) {
            trashBtn.addEventListener('click', () => {
                loadOrowarehouseTrash();
            });
        }
    }

    // Reset alert saat modal dibuka untuk tambah baru
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('modalOrowarehouse');
        if (modalEl) {
            modalEl.addEventListener('show.bs.modal', function() {
                // Hanya reset alert, bukan form (form direset oleh tombol Tambah Data)
                showOroAutofillState('hide');
                // Reload SPK dropdown saat modal dibuka HANYA jika dalam mode "tambah" (oroId kosong)
                const oroId = document.getElementById('oroId').value;
                if (!oroId) {
                    loadSpkDropdown();
                }
            });
        }
    });
</script>

<!-- Inline pagination functions (PHP base_url needs to be processed) -->
<script>
// ============ PEMASOK TAB FUNCTIONS ============

let currentPemasokPage = 1;

function initPemasokTab() {
    loadPemasokData(1);
    setupPemasokEventListeners();
}

function loadPemasokData(page = 1) {
    currentPemasokPage = page;
    
    const filters = {
        page: page,
        per_page: document.getElementById('perPageSelectPemasok').value || 10,
        tanggal_dari: document.getElementById('pemasokFilterTanggalDari').value || '',
        tanggal_sampai: document.getElementById('pemasokFilterTanggalSampai').value || '',
        status: document.getElementById('pemasokFilterStatus').value || '',
        nama_pemasok: document.getElementById('pemasokFilterNamaPemasok').value || '',
        search: document.getElementById('pemasokSearchQuery').value || ''
    };

    const url = new URL('<?= base_url('warehouse2/api/barang-masuk-pemasok') ?>');
    Object.keys(filters).forEach(key => {
        if (filters[key]) url.searchParams.append(key, filters[key]);
    });

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                renderPemasokTable(data.data);
                updatePemasokPagination(data.pager || { current_page: 1, total_pages: 1, has_prev: false, has_next: false });
            }
        })
        .catch(error => console.error('Error loading pemasok data:', error));
}

function renderPemasokTable(data) {
    const tbody = document.getElementById('tbodyPemasok');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
        return;
    }

    let html = '';
    data.forEach(item => {
        const statusColor = item.status === 'bahanmasuk' ? 'success' : (item.status === 'bahankeluar' ? 'warning' : 'danger');
        const statusLabel = item.status === 'bahanmasuk' ? 'Bahan Masuk' : (item.status === 'bahankeluar' ? 'Bahan Keluar' : item.status);
        const actionButtons = canEdit ? 
            `<button class="btn btn-outline-danger btn-sm" onclick="deletePemasokRecord(${item.id})"><i class="fas fa-trash"></i></button>` :
            '';
        html += `<tr>
            <td>${new Date(item.tanggal).toLocaleDateString('id-ID')}</td>
            <td><strong>${item.nomor_spk || item.no_dokumen || '-'}</strong></td>
            <td>${item.nama_pemasok}</td>
            <td>${item.kode_barang || '-'}</td>
            <td>${item.nama_barang}</td>
            <td>${item.satuan || '-'}</td>
            <td>${parseInt(item.jumlah || 0).toLocaleString('id-ID')}</td>
            <td>${item.no_surat_jalan || '-'}</td>
            <td><span class="badge bg-${statusColor}">${statusLabel}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    ${actionButtons}
                </div>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
}

function updatePemasokPagination(pager) {
    document.getElementById('currentPageNumPemasok').textContent = pager.current_page || 1;
    document.getElementById('totalPagesPemasok').textContent = pager.total_pages || 1;

    const prevBtn = document.getElementById('prevBtnPemasok');
    if (pager.has_prev) {
        prevBtn.classList.remove('disabled');
        prevBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadPemasokData(pager.current_page - 1);
        };
    } else {
        prevBtn.classList.add('disabled');
    }

    const nextBtn = document.getElementById('nextBtnPemasok');
    if (pager.has_next) {
        nextBtn.classList.remove('disabled');
        nextBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadPemasokData(pager.current_page + 1);
        };
    } else {
        nextBtn.classList.add('disabled');
    }
}

function applyPemasokCustomFilter() {
    loadPemasokData(1);
    updatePemasokFilterCount();
    const offcanvas = document.getElementById('pemasokCustomFilterDrawer');
    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvas);
    if (offcanvasInstance) offcanvasInstance.hide();
}

function resetPemasokCustomFilter() {
    document.getElementById('pemasokFilterTanggalDari').value = '';
    document.getElementById('pemasokFilterTanggalSampai').value = '';
    document.getElementById('pemasokFilterStatus').value = '';
    document.getElementById('pemasokFilterNamaPemasok').value = '';
    document.getElementById('pemasokSearchQuery').value = '';
    updatePemasokFilterCount();
    loadPemasokData(1);
}

function updatePemasokFilterCount() {
    let count = 0;
    if (document.getElementById('pemasokFilterTanggalDari').value) count++;
    if (document.getElementById('pemasokFilterTanggalSampai').value) count++;
    if (document.getElementById('pemasokFilterStatus').value) count++;
    if (document.getElementById('pemasokFilterNamaPemasok').value) count++;

    const badge = document.getElementById('pemasokFilterCount');
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function deletePemasokRecord(id) {
    Swal.fire({
        icon: 'question', title: 'Hapus Data?', text: 'Apakah Anda yakin?',
        showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`<?= base_url('warehouse2/delete-pemasok') ?>/${id}`, {
            method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil', timer: 1500 });
                loadPemasokData(currentPemasokPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
            }
        });
    });
}

function setupPemasokEventListeners() {
    const perPageSelect = document.getElementById('perPageSelectPemasok');
    if (perPageSelect && !perPageSelect.dataset.bound) {
        perPageSelect.dataset.bound = '1';
        perPageSelect.addEventListener('change', () => loadPemasokData(1));
    }

    const searchForm = document.getElementById('pemasokSearchForm');
    if (searchForm && !searchForm.dataset.bound) {
        searchForm.dataset.bound = '1';
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadPemasokData(1);
        });
    }
}

// ============ PRODUKSI TAB FUNCTIONS ============

let currentProduksiPage = 1;

function initProduksiTab() {
    loadProduksiData(1);
    setupProduksiEventListeners();
}

function loadProduksiData(page = 1) {
    currentProduksiPage = page;
    
    const filters = {
        page: page,
        per_page: document.getElementById('perPageSelectProduksi').value || 10,
        tanggal_dari: document.getElementById('produksiFilterTanggalDari').value || '',
        tanggal_sampai: document.getElementById('produksiFilterTanggalSampai').value || '',
        status: document.getElementById('produksiFilterStatus').value || '',
        no_spk: document.getElementById('produksiFilterNoSpk').value || '',
        search: document.getElementById('produksiSearchQuery').value || ''
    };

    const url = new URL('<?= base_url('warehouse2/api/barang-masuk-produksi') ?>');
    Object.keys(filters).forEach(key => {
        if (filters[key]) url.searchParams.append(key, filters[key]);
    });

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                renderProduksiTable(data.data);
                updateProduksiPagination(data.pager || { current_page: 1, total_pages: 1, has_prev: false, has_next: false });
            }
        })
        .catch(error => console.error('Error loading produksi data:', error));
}

function renderProduksiTable(data) {
    const tbody = document.getElementById('tbodyProduksi');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="empty-state"><i class="fas fa-inbox"></i><p>Belum ada data</p></td></tr>`;
        return;
    }

    let html = '';
    data.forEach(item => {
        const statusColor = item.status === 'terima' ? 'success' : (item.status === 'pending' ? 'warning' : 'danger');
        const statusLabel = item.status === 'terima' ? 'Terima' : (item.status === 'pending' ? 'Pending' : (item.status === 'ditolak' ? 'Ditolak' : item.status));
        const actionButtons = canEdit ? 
            `<button class="btn btn-outline-primary btn-sm me-1" onclick="editProduksi(${item.id})" title="Edit"><i class="fas fa-edit"></i></button>
             <button class="btn btn-outline-danger btn-sm" onclick="deleteProduksiRecord(${item.id})" title="Hapus"><i class="fas fa-trash"></i></button>` :
            '';
        html += `<tr>
            <td>${new Date(item.tanggal).toLocaleDateString('id-ID')}</td>
            <td><strong>${item.nomor_spk || '-'}</strong></td>
            <td>${item.kode_barang || '-'}</td>
            <td>${item.nama_barang}</td>
            <td>${item.satuan || '-'}</td>
            <td>${parseInt(item.jumlah_keluar || 0).toLocaleString('id-ID')}</td>
            <td>${item.jumlah_terima ? parseInt(item.jumlah_terima).toLocaleString('id-ID') : '-'}</td>
            <td>${item.nama_penerima || '-'}</td>
            <td><span class="badge bg-${statusColor}">${statusLabel}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    ${actionButtons}
                </div>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
}

function updateProduksiPagination(pager) {
    document.getElementById('currentPageNumProduksi').textContent = pager.current_page || 1;
    document.getElementById('totalPagesProduksi').textContent = pager.total_pages || 1;

    const prevBtn = document.getElementById('prevBtnProduksi');
    if (pager.has_prev) {
        prevBtn.classList.remove('disabled');
        prevBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadProduksiData(pager.current_page - 1);
        };
    } else {
        prevBtn.classList.add('disabled');
    }

    const nextBtn = document.getElementById('nextBtnProduksi');
    if (pager.has_next) {
        nextBtn.classList.remove('disabled');
        nextBtn.querySelector('a').onclick = (e) => {
            e.preventDefault();
            loadProduksiData(pager.current_page + 1);
        };
    } else {
        nextBtn.classList.add('disabled');
    }
}

function applyProduksiCustomFilter() {
    loadProduksiData(1);
    updateProduksiFilterCount();
    const offcanvas = document.getElementById('produksiCustomFilterDrawer');
    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvas);
    if (offcanvasInstance) offcanvasInstance.hide();
}

function resetProduksiCustomFilter() {
    document.getElementById('produksiFilterTanggalDari').value = '';
    document.getElementById('produksiFilterTanggalSampai').value = '';
    document.getElementById('produksiFilterStatus').value = '';
    document.getElementById('produksiFilterNoSpk').value = '';
    document.getElementById('produksiSearchQuery').value = '';
    updateProduksiFilterCount();
    loadProduksiData(1);
}

function updateProduksiFilterCount() {
    let count = 0;
    if (document.getElementById('produksiFilterTanggalDari').value) count++;
    if (document.getElementById('produksiFilterTanggalSampai').value) count++;
    if (document.getElementById('produksiFilterStatus').value) count++;
    if (document.getElementById('produksiFilterNoSpk').value) count++;

    const badge = document.getElementById('produksiFilterCount');
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function deleteProduksiRecord(id) {
    Swal.fire({
        icon: 'question', title: 'Hapus Data?', text: 'Apakah Anda yakin?',
        showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        fetch(`<?= base_url('warehouse2/delete-produksi') ?>/${id}`, {
            method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil', timer: 1500 });
                loadProduksiData(currentProduksiPage);
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
            }
        });
    });
}

function setupProduksiEventListeners() {
    const perPageSelect = document.getElementById('perPageSelectProduksi');
    if (perPageSelect && !perPageSelect.dataset.bound) {
        perPageSelect.dataset.bound = '1';
        perPageSelect.addEventListener('change', () => loadProduksiData(1));
    }

    const searchForm = document.getElementById('produksiSearchForm');
    if (searchForm && !searchForm.dataset.bound) {
        searchForm.dataset.bound = '1';
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadProduksiData(1);
        });
    }
}

// ============ PEMBELI TAB FUNCTIONS (from pagination.js - now inlined) ============

function setupPembeliEventListeners() {
    const perPageSelect = document.getElementById('perPageSelectPembeli');
    if (perPageSelect && !perPageSelect.dataset.bound) {
        perPageSelect.dataset.bound = '1';
        perPageSelect.addEventListener('change', () => loadPembeliData(1));
    }

    const searchForm = document.getElementById('pembeliSearchForm');
    if (searchForm && !searchForm.dataset.bound) {
        searchForm.dataset.bound = '1';
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            loadPembeliData(1);
        });
    }
}

function savePemasok() {
    const form = document.getElementById('formPemasok');
    if (!form.checkValidity()) {
        Swal.fire({
            icon: 'warning',
            title: 'Form Belum Lengkap',
            text: 'Mohon lengkapi semua field yang wajib diisi',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
        return;
    }

    const data = {
        nomor_spk: document.getElementById('noDokumenPemasok').value,
        tanggal: document.getElementById('tanggalPemasok').value,
        nama_pemasok: document.getElementById('namaPemasok').value,
        kode_barang: document.getElementById('kodeBarangPemasok').value,
        nama_barang: document.getElementById('namaBarangPemasok').value,
        satuan: document.getElementById('satuanPemasok').value,
        jumlah: document.getElementById('jumlahPemasok').value,
        no_po: document.getElementById('noPoPemasok').value,
        no_surat_jalan: document.getElementById('noSuratJalanPemasok').value,
        catatan: document.getElementById('catatanPemasok').value,
        status: document.getElementById('statusPemasok').value
    };

    fetch('<?= base_url('warehouse2/store-pemasok') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === 'success') {
                // Close modal using simple, reliable method
                const modalEl = document.getElementById('modalPemasok');
                const closeBtn = modalEl.querySelector('[data-bs-dismiss="modal"]');
                if (closeBtn) closeBtn.click();

                // Small delay to ensure modal closes
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Berhasil Ditambahkan',
                        text: 'Data barang masuk pemasok telah ditambahkan',
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    loadPemasokData();
                }, 300);
            } else {
                console.error('Validation errors:', data.errors);
                const errorText = data.errors ? 
                    Object.values(data.errors).join('\n') : 
                    (data.message || 'Terjadi kesalahan saat menyimpan data');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan Data',
                    text: errorText,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menyimpan Data',
                text: 'Error: ' + error.message,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            });
        });
}

function deletePemasokRecord(id) {
    Swal.fire({
        title: 'Konfirmasi Penghapusan',
        text: 'Apakah Anda yakin ingin menghapus data ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('warehouse2/delete-pemasok') ?>/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success || data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dihapus',
                            text: 'Data telah dipindahkan ke sampah',
                            timer: 2000
                        });
                        loadPemasokData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Gagal menghapus data'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });
                });
        }
    });
}

</script>

<?= $this->endSection() ?>