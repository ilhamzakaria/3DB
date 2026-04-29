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
