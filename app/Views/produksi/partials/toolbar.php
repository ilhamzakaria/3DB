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
