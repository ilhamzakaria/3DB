<div class="card-header py-3 bg-white">
    <div class="production-toolbar">
        <?php
        $canWrite  = in_array(session('role'), ['admin', 'manager', 'produksi', 'manager_warehouse2', 'operator', 'operator_produksi', 'operator_produksi1', 'operator_produksi2']);
        $baseUrl   = $base_url ?? base_url('produksi/mesin_' . ($mesin_slug ?? ''));
        $mesinSlug = $mesin_slug ?? '';
        ?>
        <div class="toolbar-group">
            <?php if ($canWrite): ?>
                <button class="btn btn-primary btn-sm shadow-sm toolbar-btn"
                        data-bs-toggle="modal" data-bs-target="#addDataModal">
                    <i class="fas fa-plus me-1"></i> Tambah Laporan
                </button>
            <?php endif; ?>

            <?php
            $exportParams = array_filter([
                'q'      => $q              ?? '',
                'shift'  => $filter_shift   ?? '',
                'tanggal'=> $filter_tanggal ?? '',
                'filter' => $filter         ?? '',
                'mesin'  => $mesin_slug     ?? '',
            ]);
            $exportUrl = base_url('export/produksi') . ($exportParams ? '?' . http_build_query($exportParams) : '');
            ?>
            <a href="<?= $exportUrl ?>" class="btn btn-outline-success btn-sm shadow-sm toolbar-btn">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>

            <?php if ($canWrite): ?>
                <button class="btn btn-outline-secondary btn-sm toolbar-btn"
                        data-bs-toggle="modal" data-bs-target="#trashModal" id="btn_open_trash">
                    <i class="fas fa-trash-restore me-1"></i> Sampah
                </button>
            <?php endif; ?>
        </div>

        <div class="toolbar-group">
            <!-- Active mesin badge (always shown on mesin page, non-removable — use "Semua Mesin" link instead) -->
            <span class="btn btn-sm toolbar-btn"
                  style="background:<?= esc($mesin_color ?? '#4e73df') ?>18;color:<?= esc($mesin_color ?? '#4e73df') ?>;
                         border:1.5px solid <?= esc($mesin_color ?? '#4e73df') ?>40;font-weight:700;cursor:default;">
                <i class="fas fa-cog me-1"></i><?= esc($mesin_label ?? '') ?>
            </span>

            <!-- Period filter dropdown — preserves mesin-scoped URL -->
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm toolbar-btn"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <?php
                    if ($filter === 'day')   echo 'Hari Ini';
                    elseif ($filter === 'week')  echo 'Minggu Ini';
                    elseif ($filter === 'month') echo 'Bulan Ini';
                    else echo 'Semua Periode';
                    ?>
                </button>
                <?php
                $shiftQs = !empty($filter_shift)   ? '&shift='   . urlencode($filter_shift)   : '';
                $tglQs   = !empty($filter_tanggal) ? '&tanggal=' . urlencode($filter_tanggal) : '';
                $qQs     = !empty($q)              ? '&q='       . urlencode($q)               : '';
                ?>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg toolbar-filter-menu"
                    aria-labelledby="periodFilterDropdown">
                    <li><a class="dropdown-item py-2 <?= $filter==='day'   ? 'active bg-primary text-white' : '' ?>"
                           href="<?= $baseUrl ?>?filter=day<?= $shiftQs . $tglQs . $qQs ?>">
                        <i class="fas fa-calendar-day me-2 opacity-50"></i> Hari Ini</a></li>
                    <li><a class="dropdown-item py-2 <?= $filter==='week'  ? 'active bg-primary text-white' : '' ?>"
                           href="<?= $baseUrl ?>?filter=week<?= $shiftQs . $tglQs . $qQs ?>">
                        <i class="fas fa-calendar-week me-2 opacity-50"></i> Minggu Ini</a></li>
                    <li><a class="dropdown-item py-2 <?= $filter==='month' ? 'active bg-primary text-white' : '' ?>"
                           href="<?= $baseUrl ?>?filter=month<?= $shiftQs . $tglQs . $qQs ?>">
                        <i class="fas fa-calendar-alt me-2 opacity-50"></i> Bulan Ini</a></li>
                    <?php if ($filter): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger"
                               href="<?= $baseUrl ?><?= $shiftQs || $tglQs || $qQs ? '?' . ltrim($shiftQs . $tglQs . $qQs, '&') : '' ?>">
                            <i class="fas fa-sync-alt me-2 opacity-50"></i> Reset Filter</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Search — form action points to mesin-scoped URL -->
            <form action="<?= esc($baseUrl) ?>" method="get" class="toolbar-search-form">
                <?php if (!empty($filter)): ?>
                    <input type="hidden" name="filter" value="<?= esc($filter) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_shift)): ?>
                    <input type="hidden" name="shift" value="<?= esc($filter_shift) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_tanggal)): ?>
                    <input type="hidden" name="tanggal" value="<?= esc($filter_tanggal) ?>">
                <?php endif; ?>
                <div class="input-group input-group-sm toolbar-search">
                    <input type="text" name="q" class="form-control border-end-0"
                           placeholder="Cari SPK/Produk..." value="<?= esc($q ?? '') ?>">
                    <button class="btn btn-outline-primary border-start-0" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Custom filter drawer -->
            <button class="btn btn-light btn-sm toolbar-btn" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#plantFilterDrawer">
                <i class="fas fa-sliders-h me-1"></i> Custom
                <?php
                $activeCount = (int)!empty($filter_shift) + (int)!empty($filter_tanggal);
                if ($activeCount > 0): ?>
                    <span class="filter-count"><?= $activeCount ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>
</div>
