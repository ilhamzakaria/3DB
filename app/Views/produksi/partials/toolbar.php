<div class="card-header py-3 bg-white">
    <div class="production-toolbar">
        <?php
        $canAdd    = in_array(session('role'), ['manager', 'produksi', 'operator', 'operator_produksi', 'operator_produksi1', 'operator_produksi2']);
        $canDelete = in_array(session('role'), ['manager', 'produksi']);
        ?>        <div class="toolbar-group">
            <?php if ($canAdd) : ?>
                <button class="btn btn-primary btn-sm shadow-sm toolbar-btn" data-bs-toggle="modal" data-bs-target="#addDataModal">
                    <i class="fas fa-plus me-1"></i> Tambah Laporan
                </button>
            <?php endif; ?>
            <?php
            $exportParams = array_filter([
                'q'      => $q              ?? '',
                'shift'  => $filter_shift   ?? '',
                'tanggal'=> $filter_tanggal ?? '',
                'filter' => $filter         ?? '',
                'mesin'  => $filter_mesin   ?? '',
            ]);
            $exportUrl = base_url('export/produksi') . ($exportParams ? '?' . http_build_query($exportParams) : '');
            ?>
            <a href="<?= $exportUrl ?>" class="btn btn-outline-success btn-sm shadow-sm toolbar-btn">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <?php if ($canDelete) : ?>
                <button class="btn btn-outline-secondary btn-sm toolbar-btn" data-bs-toggle="modal" data-bs-target="#trashModal" id="btn_open_trash">
                    <i class="fas fa-trash-restore me-1"></i> Sampah
                </button>
            <?php endif; ?>
        </div>

        <div class="toolbar-group">
            <!-- Active mesin badge -->
            <?php if (!empty($filter_mesin)): ?>
                <?php $mesinLabel = $mesin_labels[$filter_mesin] ?? $filter_mesin; ?>
                <a href="<?= base_url('produksi?' . http_build_query(array_filter([
                    'q'      => $q            ?? '',
                    'filter' => $filter       ?? '',
                    'shift'  => $filter_shift ?? '',
                    'tanggal'=> $filter_tanggal ?? '',
                ]))) ?>"
                   class="btn btn-sm toolbar-btn text-decoration-none"
                   style="background:#eef0fb; color:#4e73df; border:1.5px solid #b7c3f4; font-weight:700;"
                   title="Hapus filter mesin">
                    <i class="fas fa-cog me-1"></i><?= esc($mesinLabel) ?>
                    <i class="fas fa-times ms-1 opacity-75"></i>
                </a>
            <?php endif; ?>

            <!-- Period filter dropdown — preserves mesin -->
            <div class="dropdown">
                <button class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm toolbar-btn" type="button"
                        id="periodFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <?php
                    if ($filter === 'day')   echo 'Hari Ini';
                    elseif ($filter === 'week')  echo 'Minggu Ini';
                    elseif ($filter === 'month') echo 'Bulan Ini';
                    else echo 'Semua Periode';
                    ?>
                </button>
                <?php
                $mesinQs  = !empty($filter_mesin) ? '&mesin=' . urlencode($filter_mesin) : '';
                $shiftQs  = !empty($filter_shift) ? '&shift=' . urlencode($filter_shift) : '';
                $tglQs    = !empty($filter_tanggal) ? '&tanggal=' . urlencode($filter_tanggal) : '';
                $qQs      = !empty($q) ? '&q=' . urlencode($q) : '';
                ?>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg toolbar-filter-menu"
                    aria-labelledby="periodFilterDropdown">
                    <li><a class="dropdown-item py-2 <?= $filter==='day'   ? 'active bg-primary text-white' : '' ?>"
                           href="<?= base_url('produksi?filter=day'  . $mesinQs . $shiftQs . $tglQs . $qQs) ?>">
                        <i class="fas fa-calendar-day me-2 opacity-50"></i> Hari Ini</a></li>
                    <li><a class="dropdown-item py-2 <?= $filter==='week'  ? 'active bg-primary text-white' : '' ?>"
                           href="<?= base_url('produksi?filter=week' . $mesinQs . $shiftQs . $tglQs . $qQs) ?>">
                        <i class="fas fa-calendar-week me-2 opacity-50"></i> Minggu Ini</a></li>
                    <li><a class="dropdown-item py-2 <?= $filter==='month' ? 'active bg-primary text-white' : '' ?>"
                           href="<?= base_url('produksi?filter=month'. $mesinQs . $shiftQs . $tglQs . $qQs) ?>">
                        <i class="fas fa-calendar-alt me-2 opacity-50"></i> Bulan Ini</a></li>
                    <?php if ($filter): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger"
                               href="<?= base_url('produksi?' . http_build_query(array_filter([
                                   'mesin'  => $filter_mesin   ?? '',
                                   'shift'  => $filter_shift   ?? '',
                                   'tanggal'=> $filter_tanggal ?? '',
                                   'q'      => $q              ?? '',
                               ]))) ?>">
                            <i class="fas fa-sync-alt me-2 opacity-50"></i> Reset Filter</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Search — keeps mesin param as hidden input -->
            <form action="<?= base_url('produksi') ?>" method="get" class="toolbar-search-form">
                <?php if (!empty($filter_mesin)): ?>
                    <input type="hidden" name="mesin" value="<?= esc($filter_mesin) ?>">
                <?php endif; ?>
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
                           placeholder="Cari SPK/Mesin..." value="<?= esc($q ?? '') ?>">
                    <button class="btn btn-outline-primary border-start-0" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Custom filter drawer — keeps mesin -->
            <button class="btn btn-light btn-sm toolbar-btn" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#plantFilterDrawer">
                <i class="fas fa-sliders-h me-1"></i> Custom
                <?php
                $activeFilterCount = 0;
                if ($filter_shift)   $activeFilterCount++;
                if ($filter_tanggal) $activeFilterCount++;
                if ($activeFilterCount > 0): ?>
                    <span class="filter-count"><?= $activeFilterCount ?></span>
                <?php endif; ?>
            </button>
        </div>
    </div>
</div>
