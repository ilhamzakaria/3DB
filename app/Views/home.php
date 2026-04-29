<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    /* Mengaktifkan scroll horizontal seperti Excel pada pembungkus utama */
    #wrapper #content-wrapper {
        overflow-x: auto !important;
    }

    /* Pembungkus tabel untuk memastikan scroll berjalan lancar di mobile/desktop */
    .home-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        margin-bottom: 1.5rem;
    }

    .home-table-wrap table {
        min-width: 1800px;
        /* Lebar minimum agar scroll horizontal muncul */
        margin-bottom: 0;
        border-collapse: collapse;
        background-color: white;
    }

    .home-table-wrap th,
    .home-table-wrap td {
        white-space: nowrap;
        /* Mencegah teks turun ke bawah (wrap) */
        padding: 10px 12px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }

    /* Styling Header Seksi mirip halaman Admin */
    .home-table-wrap .section-header1 {
        background: #4e73df !important;
        /* Warna biru primary */
        color: white !important;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .section-header2 {
        background: #10b981 !important; /* Emerald Green for Produksi */
        color: white !important;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .section-header3 {
        background: #0ea5e9 !important; /* Sky Blue for Gudang */
        color: white !important;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .sub-header-gudang {
        background: #f0f9ff !important;
        color: #0369a1 !important;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .home-table-wrap thead .table-light th {
        background-color: #f8f9fc;
        font-weight: 700;
        color: #4e73df;
    }

    /* Warna baris selang-seling */
    .home-table-wrap tr:nth-child(even) {
        background-color: #f8f9fc;
    }

    .home-table-wrap tr:hover {
        background-color: #f1f4ff;
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

    .filter-hint {
        margin: 0;
        color: #6c757d;
        font-size: 0.84rem;
    }


</style>



<div class="container-fluid">
    <?php
    $searchValue = trim((string) ($q ?? ''));
    $filterPeriode = trim((string) ($periode ?? ''));
    $filterTanggal = trim((string) ($tanggal ?? ''));
    $filterStartDate = trim((string) ($start_date ?? ''));
    $filterEndDate = trim((string) ($end_date ?? ''));
    $homeBaseUrl = base_url('home');

    $activeFilterCount = 0;
    if ($filterPeriode !== '') $activeFilterCount++;
    if ($filterTanggal !== '') $activeFilterCount++;
    if ($filterStartDate !== '' || $filterEndDate !== '') $activeFilterCount++;
    
    // Add count for column filters
    foreach ($col_filter as $field => $values) {
        if (!empty($values)) $activeFilterCount++;
    }

    // Helper to render sidebar filter section
    function renderSidebarFilter($field, $displayName, $uniqueValues, $activeFilters) {
        $selected = $activeFilters[$field] ?? [];
        if (empty($uniqueValues)) return;
        ?>
        <div class="mb-3">
            <label class="filter-section-title"><?= esc($displayName) ?></label>
            <div class="filter-options-list border rounded p-2 bg-light" style="max-height: 150px; overflow-y: auto;">
                <?php foreach ($uniqueValues as $val): ?>
                    <label class="filter-option-item d-block mb-1">
                        <input type="checkbox" name="col_filter[<?= esc($field) ?>][]" value="<?= esc($val) ?>" 
                            <?= in_array($val, $selected) ? 'checked' : '' ?>>
                        <?= esc($val) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    $periodLabels = [
        'hari' => 'Harian',
        'minggu' => 'Mingguan',
        'bulan' => 'Bulanan',
    ];

    $dateSummary = '';
    if ($filterStartDate !== '' && $filterEndDate !== '') {
        $dateSummary = $filterStartDate . ' s/d ' . $filterEndDate;
    } elseif ($filterStartDate !== '') {
        $dateSummary = 'Mulai ' . $filterStartDate;
    } elseif ($filterEndDate !== '') {
        $dateSummary = 'Sampai ' . $filterEndDate;
    }
    ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">


                <div class="ms-auto d-flex align-items-center gap-2">
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
                            <li><a class="dropdown-item py-2 <?= ($filter === 'day') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('home?filter=day') ?>"><i class="fas fa-calendar-day mr-2 opacity-50"></i> Hari Ini</a></li>
                            <li><a class="dropdown-item py-2 <?= ($filter === 'week') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('home?filter=week') ?>"><i class="fas fa-calendar-week mr-2 opacity-50"></i> Minggu</a></li>
                            <li><a class="dropdown-item py-2 <?= ($filter === 'month') ? 'active bg-primary text-white' : '' ?>" href="<?= base_url('home?filter=month') ?>"><i class="fas fa-calendar-alt mr-2 opacity-50"></i> Bulan</a></li>
                            <?php if ($filter): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('home') ?>"><i class="fas fa-sync-alt mr-2 opacity-50"></i> Reset Filter</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <form method="get" action="<?= esc($homeBaseUrl) ?>" class="d-flex align-items-center gap-2 mb-0" id="mainSearchForm">
                        <!-- Keep existing filters -->
                        <input type="hidden" name="periode" value="<?= esc($filterPeriode) ?>">
                        <input type="hidden" name="tanggal" value="<?= esc($filterTanggal) ?>">
                        <input type="hidden" name="start_date" value="<?= esc($filterStartDate) ?>">
                        <input type="hidden" name="end_date" value="<?= esc($filterEndDate) ?>">
                        
                        <?php foreach ($col_filter as $field => $values): ?>
                            <?php foreach ($values as $val): ?>
                                <input type="hidden" name="col_filter[<?= esc($field) ?>][]" value="<?= esc($val) ?>">
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        


                        <input
                            type="text"
                            name="q"
                            class="form-control form-control-sm"
                            style="min-width: 260px;"
                            placeholder="Cari data..."
                            value="<?= esc($searchValue) ?>">
                        <button class="btn btn-primary btn-sm" type="submit">Cari</button>
                    </form>

                    <button
                        class="btn btn-outline-primary btn-sm filter-toggle"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#homeFilterDrawer"
                        aria-controls="homeFilterDrawer">
                        <i class="fas fa-filter"></i> Filter
                        <?php if ($activeFilterCount > 0): ?>
                            <span class="filter-count"><?= $activeFilterCount ?></span>
                        <?php endif; ?>
                    </button>


                </div>
            </div>

            <?php if ($activeFilterCount > 0): ?>
                <div class="filter-summary">
                    <?php if ($filterPeriode !== ''): ?>
                        <span class="filter-chip">Periode: <?= esc($periodLabels[$filterPeriode] ?? ucfirst($filterPeriode)) ?></span>
                    <?php endif; ?>
                    <?php if ($filterTanggal !== ''): ?>
                        <span class="filter-chip">Tanggal: <?= esc($filterTanggal) ?></span>
                    <?php endif; ?>
                    <?php if ($dateSummary !== ''): ?>
                        <span class="filter-chip">Rentang: <?= esc($dateSummary) ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="homeFilterDrawer" aria-labelledby="homeFilterDrawerLabel">
                <div class="offcanvas-header">
                    <div>
                        <h5 class="offcanvas-title mb-1" id="homeFilterDrawerLabel">Filter Home</h5>
                        <p class="filter-hint">Pilih periode, tanggal acuan, dan rentang tanggal agar data lebih spesifik.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <form method="get" action="<?= esc($homeBaseUrl) ?>" class="d-flex flex-column gap-3">
                        <input type="hidden" name="q" value="<?= esc($searchValue) ?>">

                        <div>
                            <label class="filter-section-title" for="homeFilterPeriode">Periode</label>
                            <select id="homeFilterPeriode" name="periode" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="hari" <?= $filterPeriode === 'hari' ? 'selected' : '' ?>>Harian</option>
                                <option value="minggu" <?= $filterPeriode === 'minggu' ? 'selected' : '' ?>>Mingguan</option>
                                <option value="bulan" <?= $filterPeriode === 'bulan' ? 'selected' : '' ?>>Bulanan</option>
                            </select>
                        </div>

                        <div>
                            <label class="filter-section-title" for="homeFilterStartDate">Tanggal Mulai</label>
                            <input
                                type="date"
                                id="homeFilterStartDate"
                                name="start_date"
                                class="form-control"
                                value="<?= esc($filterStartDate) ?>">
                        </div>

                        <div>
                            <label class="filter-section-title" for="homeFilterEndDate">Tanggal Selesai</label>
                            <input
                                type="date"
                                id="homeFilterEndDate"
                                name="end_date"
                                class="form-control"
                                value="<?= esc($filterEndDate) ?>">
                        </div>

                        <hr>
                        <h6 class="text-primary fw-bold mb-3">Filter Kolom</h6>

                        <!-- PPIC Filters -->
                        <div class="mb-4">
                            <small class="text-muted fw-bold d-block mb-2 text-uppercase">Departemen PPIC</small>
                            <?php renderSidebarFilter('nama_mesin', 'Nama Mesin', $unique_values['ppic_nama_mesin'] ?? [], $col_filter); ?>
                            <?php renderSidebarFilter('nama_produk', 'Nama Produk', $unique_values['ppic_nama_produk'] ?? [], $col_filter); ?>
                            <?php renderSidebarFilter('shif', 'Shift', $unique_values['ppic_shif'] ?? [], $col_filter); ?>
                            <?php renderSidebarFilter('operator', 'Operator', $unique_values['ppic_operator'] ?? [], $col_filter); ?>
                        </div>

                        <!-- Production Filters -->
                        <div class="mb-4">
                            <small class="text-muted fw-bold d-block mb-2 text-uppercase">Departemen Produksi</small>
                            <?php renderSidebarFilter('nama_mesin', 'Mesin', $unique_values['prod_nama_mesin'] ?? [], $col_filter); ?>
                            <?php renderSidebarFilter('nama_produksi', 'Produk', $unique_values['prod_nama_produksi'] ?? [], $col_filter); ?>
                            <?php renderSidebarFilter('shift', 'Shift', $unique_values['prod_shift'] ?? [], $col_filter); ?>
                            <?php renderSidebarFilter('operator', 'Operator', $unique_values['prod_operator'] ?? [], $col_filter); ?>
                        </div>

                        <!-- Gudang Filters -->
                        <div class="mb-4">
                            <small class="text-muted fw-bold d-block mb-2 text-uppercase">Departemen Gudang</small>
                            <?php renderSidebarFilter('shif', 'Shift', $unique_values['gudang_shif'] ?? [], $col_filter); ?>
                            <?php renderSidebarFilter('bahan_baku', 'Bahan Baku', $unique_values['gudang_bahan_baku'] ?? [], $col_filter); ?>
                        </div>

                        <div class="filter-actions sticky-bottom bg-white pt-3 border-top">
                            <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
                            <a href="<?= esc($homeBaseUrl) ?>" class="btn btn-outline-secondary flex-fill">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="home-table-wrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="9" class="section-header1 text-center">Departemen PPIC</th>
                            <th colspan="9" class="section-header2 text-center">Departemen Produksi</th>
                            <th colspan="17" class="section-header3 text-center">Departemen Gudang</th>
                        </tr>
                        <tr>
                            <th colspan="9" class="bg-light text-center small text-muted font-weight-bold">Info & Target</th>
                            <th colspan="9" class="bg-light text-center small text-muted font-weight-bold">Hasil Produksi</th>
                            <th colspan="2" class="sub-header-gudang text-center">Info</th>
                            <th colspan="1" class="sub-header-gudang text-center">Bahan</th>
                            <th colspan="3" class="sub-header-gudang text-center">Packaging</th>
                            <th colspan="1" class="sub-header-gudang text-center">MB</th>
                            <th colspan="5" class="sub-header-gudang text-center">Reject</th>
                            <th colspan="1" class="sub-header-gudang text-center">Stiker</th>
                            <th colspan="3" class="sub-header-gudang text-center">Lainnya</th>
                            <th colspan="1" class="sub-header-gudang text-center">Tgl</th>
                        </tr>
                        <tr class="table-light text-center">
                            <th>Tanggal</th>
                            <th>No SPK</th>
                            <th>Nama Mesin</th>
                            <th>Nama Produk</th>
                            <th>Shift</th>
                            <th>Operator</th>
                            <th>Target</th>
                            <th>Jam</th>
                            <th>Revisi</th>
                            <!-- Produksi -->
                            <th>No SPK</th>
                            <th>Mesin</th>
                            <th>Produk</th>
                            <th>Shift</th>
                            <th>Operator</th>
                            <th class="text-right">Hasil Bagus</th>
                            <th class="text-right">Reject</th>
                            <th class="text-center">Selisih</th>
                            <th>Tanggal</th>
                            <!-- Gudang -->
                            <th>No SPK</th>
                            <th>Shift</th>
                            <th>Bahan Baku</th>
                            <th>Box</th>
                            <th>Karung</th>
                            <th>Plastik</th>
                            <th>Masterbatch</th>
                            <th>G. Reject Sup</th>
                            <th>G. Reject Prod</th>
                            <th>Gil. Reject Sup</th>
                            <th>Gil. Reject Prod</th>
                            <th>Stiker</th>
                            <th>Reject Preform</th>
                            <th>Bekuat PET</th>
                            <th>bekuan capgalon</th>
                            <th>Gilingan Screwcap</th>
                            <th>tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ppic) || !empty($produksi) || !empty($gudang)): ?>
                            <?php
                            // Mengambil jumlah baris terbanyak untuk iterasi gabungan
                            $rowCount = max(count($ppic ?? []), count($produksi ?? []), count($gudang ?? []));
                            for ($i = 0; $i < $rowCount; $i++):
                                $p = $ppic[$i] ?? [];
                                $prod = $produksi[$i] ?? [];
                                $g = $gudang[$i] ?? [];
                            ?>
                                <tr class="align-middle">
                                    <td><?= !empty($p['tanggal']) ? date('d M Y', strtotime($p['tanggal'])) : '-' ?></td>
                                    <td><span class="badge badge-modern badge-spk" style="background: rgba(67, 97, 238, 0.1) !important; color: #4361ee !important; border: 1px solid rgba(67, 97, 238, 0.3) !important; font-weight: 700 !important; padding: 0.35em 0.8em; border-radius: 6px; font-size: 0.8rem;"><?= esc($p['no_spk'] ?? '-') ?></span></td>
                                    <td><?= esc($p['nama_mesin'] ?? '-') ?></td>
                                    <td><?= esc($p['nama_produk'] ?? '-') ?></td>
                                    <td><?= esc($p['shif'] ?? $p['shif'] ?? '-') ?></td>
                                    <td><?= esc($p['operator'] ?? '-') ?></td>
                                    <td><?= esc($p['targett'] ?? '-') ?></td>
                                    <td><?= esc($p['jam'] ?? '-') ?></td>

                                    <?php
                                    $revisiItems = $p['revisi_items'] ?? [];
                                    $hasAnyRevisi = false;
                                    for ($ke = 1; $ke <= 5; $ke++) {
                                        $nilai = trim((string) ($revisiItems[$ke]['nilai'] ?? ''));
                                        if ($nilai !== '') {
                                            $hasAnyRevisi = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <td class="<?= $hasAnyRevisi ? 'text-danger' : '' ?>">
                                        <?php for ($ke = 1; $ke <= 5; $ke++): ?>
                                            <?php
                                            $item = $revisiItems[$ke] ?? [];
                                            $nilai = trim((string) ($item['nilai'] ?? ''));
                                            $tanggalLabel = $item['tanggal_label'] ?? '-';
                                            $jamLabel = $item['jam_label'] ?? '-';
                                            ?>

                                            <?php
                                            $nilaiLabel = $nilai !== '' ? $nilai : '-';
                                            $tanggalLabel = $tanggalLabel !== '' ? $tanggalLabel : '-';
                                            $jamLabel = $jamLabel !== '' ? $jamLabel : '-';
                                            $textClass = $nilai !== '' ? 'text-danger' : 'text-muted';
                                            ?>
                                            <a href="#"
                                                class="btn btn-link p-0 <?= $textClass ?> revisi-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalRevisi"
                                                data-revisi-ke="<?= (int) $ke ?>"
                                                data-nilai="<?= esc((string) $nilaiLabel) ?>"
                                                data-tanggal="<?= esc($item['tanggal_revisi'] ?? '-') ?>"
                                                data-jam="<?= esc((string) $jamLabel) ?>">
                                                <?= $nilai ?? $item['nilai'] ?>
                                            </a>

                                            <?php if ($ke < 5): ?>
                                                <span class="<?= $hasAnyRevisi ? 'text-danger' : 'text-muted' ?>">
                                                    <?php if (!empty($nilai) && $ke < 5): ?>
                                                        |
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </td>
                                    <!-- Produksi Section -->
                                    <td><span class="badge badge-modern badge-spk" style="background: rgba(67, 97, 238, 0.1) !important; color: #4361ee !important; border: 1px solid rgba(67, 97, 238, 0.3) !important; font-weight: 700 !important; padding: 0.35em 0.8em; border-radius: 6px; font-size: 0.8rem;"><?= esc($prod['nomor_spk'] ?? '-') ?></span></td>
                                    <td><?= esc($prod['nama_mesin'] ?? '-') ?></td>
                                    <td><?= esc($prod['nama_produksi'] ?? '-') ?></td>
                                    <td class="text-center"><?= esc($prod['shift'] ?? '-') ?></td>
                                    <td><?= esc($prod['operator'] ?? '-') ?></td>
                                    <td class="text-right font-weight-bold text-success"><?= esc($prod['grand_total_bagus'] ?? '0') ?></td>
                                    <td class="text-right text-danger"><?= esc($prod['grand_total_reject'] ?? '0') ?></td>
                                    <?php
                                    $targetPpic = is_numeric($p['nilai'] ?? null) ? (int) $p['nilai'] : (int) ($p['revisi'] ?? 0);
                                    $hasilBagus = is_numeric($prod['grand_total_bagus'] ?? null) ? (int) $prod['grand_total_bagus'] : 0;
                                    $selisih = $targetPpic - $hasilBagus;
                                    ?>
                                    <td class="text-center <?= $selisih < 0 ? 'text-danger' : 'text-primary font-weight-bold' ?>">
                                        <?= esc($selisih) ?>
                                    </td>
                                    <td><?= !empty($prod['tanggal']) ? date('d M Y', strtotime($prod['tanggal'])) : '-' ?></td>

                                    <!-- Gudang -->
                                    <td class="text-center"><span class="badge badge-modern badge-spk" style="background: rgba(67, 97, 238, 0.1) !important; color: #4361ee !important; border: 1px solid rgba(67, 97, 238, 0.3) !important; font-weight: 700 !important; padding: 0.35em 0.8em; border-radius: 6px; font-size: 0.8rem;"><?= esc($g['no_spk'] ?? '-') ?></span></td>
                                    <td class="text-center"><?= esc($g['shif'] ?? '-') ?></td>
                                    <td><?= esc($g['bahan_baku'] ?? '-') ?></td>
                                    <td class="text-right"><?= ($v = $g['box'] ?? '-') !== '-' && $v != 0 ? '<span class="fw-bold">' . esc($v) . '</span>' : esc($v) ?></td>
                                    <td class="text-right"><?= ($v = $g['karung'] ?? '-') !== '-' && $v != 0 ? '<span class="fw-bold">' . esc($v) . '</span>' : esc($v) ?></td>
                                    <td class="text-right"><?= ($v = $g['plastik'] ?? '-') !== '-' && $v != 0 ? '<span class="fw-bold">' . esc($v) . '</span>' : esc($v) ?></td>
                                    <td class="text-right"><?= ($v = $g['masterbatch'] ?? '-') !== '-' && $v != 0 ? '<span class="fw-bold">' . esc($v) . '</span>' : esc($v) ?></td>
                                    <td class="text-right text-danger"><?= ($v = $g['galon_reject_supplier'] ?? '-') !== '-' && $v != 0 ? esc($v) : '-' ?></td>
                                    <td class="text-right text-danger"><?= ($v = $g['galon_reject_produksi'] ?? '-') !== '-' && $v != 0 ? esc($v) : '-' ?></td>
                                    <td class="text-right text-danger"><?= ($v = $g['gilingan_reject_supplier'] ?? '-') !== '-' && $v != 0 ? esc($v) : '-' ?></td>
                                    <td class="text-right text-danger"><?= ($v = $g['gilingan_reject_produksi'] ?? '-') !== '-' && $v != 0 ? esc($v) : '-' ?></td>
                                    <td class="text-right"><?= ($v = $g['stiker'] ?? '-') !== '-' && $v != 0 ? '<span class="fw-bold">' . esc($v) . '</span>' : esc($v) ?></td>
                                    <td class="text-right text-danger"><?= ($v = $g['reject_preform'] ?? '-') !== '-' && $v != 0 ? esc($v) : '-' ?></td>
                                    <td class="text-right"><?= esc($g['bekuat_pet'] ?? '-') ?></td>
                                    <td class="text-right"><?= esc($g['bekuan_capgalon'] ?? '-') ?></td>
                                    <td class="text-right"><?= esc($g['gilingan_screwcap'] ?? '-') ?></td>
                                    <td class="text-center small"><?= !empty($g['tanggal']) ? date('d M Y', strtotime($g['tanggal'])) : '-' ?></td>
                                </tr>
                            <?php endfor; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="22" class="text-center text-muted py-4">
                                    <?php $message = 'Belum ada data dari departemen terkait.'; ?>
                                    <?= esc($message) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div class="modal fade" id="modalRevisi" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Detail Revisi</h5>
                                <br>
                            </div>

                            <div class="modal-body" id="isiRevisi">
                                <!-- isi dari klik akan masuk sini -->
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="px-3 py-2 border-top d-flex justify-content-between align-items-center bg-white">
                    <div class="small text-muted">
                        Menampilkan <?= ($current_page - 1) * $per_page + 1 ?> - <?= min($current_page * $per_page, $total_rows) ?> dari <?= $total_rows ?> data
                    </div>
                    <div>
                        <?= $pager ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const buttons = document.querySelectorAll(".revisi-btn");
        const isiModal = document.getElementById("isiRevisi");
        if (!isiModal) return;

        buttons.forEach(btn => {
            btn.addEventListener("click", function() {
                const ke = this.getAttribute("data-revisi-ke") || "-";
                const nilai = this.getAttribute("data-nilai") || "-";
                const tanggal = this.getAttribute("data-tanggal") || "-";


                let tanggalFormat = "-";
                let jamFormat = "-";

                if (tanggal.includes(" ")) {
                    const tglObj = new Date(tanggal);
                    tanggalFormat = tglObj.toLocaleDateString("id-ID", {
                        day: "2-digit",
                        month: "long",
                        year: "numeric"
                    });
                    jamFormat = tglObj.toLocaleTimeString("id-ID", {
                        hour: "2-digit",
                        minute: "2-digit",
                        hour12: false
                    });
                } else {
                    tanggalFormat = tanggal;
                }

                isiModal.innerHTML = "";
                const items = [
                    ["Revisi Ke", ke],
                    ["Target", nilai],
                    ["Tanggal", tanggalFormat],
                    ["Jam", jamFormat],
                ];

                items.forEach(([label, value]) => {
                    const row = document.createElement("div");
                    row.style.display = "flex";
                    row.style.marginBottom = "6px";
                    const labelDiv = document.createElement("div");
                    labelDiv.style.minWidth = "80px";
                    labelDiv.style.fontWeight = "bold";
                    labelDiv.innerText = label;
                    const colon = document.createElement("div");
                    colon.style.marginRight = "6px";
                    colon.innerText = ":";
                    const valueDiv = document.createElement("div");
                    let displayValue = value ?? "-";
                    if (label.toLowerCase().includes("jam") && displayValue !== "-") {
                        displayValue = displayValue.replace(".", ":");
                    }
                    valueDiv.innerText = displayValue;
                    row.appendChild(labelDiv);
                    row.appendChild(colon);
                    row.appendChild(valueDiv);
                    isiModal.appendChild(row);
                });
            });
        });
    });


</script>

<?= $this->endSection() ?>