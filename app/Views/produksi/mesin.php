<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('produksi/partials/styles') ?>

<?php
/**
 * Produksi — Dedicated mesin sub-page
 * Route: /produksi/mesin_{slug}  (e.g. /produksi/mesin_becum)
 *
 * Matches the PPIC pattern: each mesin has its own URL.
 * The toolbar, table, and modals are the same as the main index —
 * they receive filter_mesin so they stay context-aware automatically.
 * The key difference: all forms/links here use the mesin-scoped base URL
 * instead of /produksi, so pagination and search stay on the right page.
 */

$baseUrl     = base_url('produksi/mesin_' . ($mesin_slug ?? ''));
$produksiUrl = base_url('produksi');

// Colour map (matches mesin_menu_list.php)
$mesinColors = [
    'becum'     => '#4e73df',
    'powerjet1' => '#f6c23e',
    'powerjet2' => '#f6c23e',
    'ccm1'      => '#36b9cc',
    'ccm2'      => '#36b9cc',
    'ips1'      => '#1cc88a',
    'ips2'      => '#1cc88a',
    'ips3'      => '#1cc88a',
    'ips4'      => '#1cc88a',
];
$mesinColor  = $mesinColors[$mesin_slug ?? ''] ?? '#4e73df';
$mesinLabel  = $mesin_label ?? ($mesin_labels[$mesin_slug ?? ''] ?? '');
?>

<div class="page-content-wrapper container-fluid py-4">

    <!-- Flash messages -->
    <?php if (session('success')): ?>
        <div class="alert alert-success d-flex align-items-center fade show border-0 shadow-sm mb-4" role="alert" style="border-radius:12px;">
            <i class="fas fa-check-circle me-2"></i>
            <span><?= session('success') ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger d-flex align-items-center fade show border-0 shadow-sm mb-4" role="alert" style="border-radius:12px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            <span><?= session('error') ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- ── Mesin header bar ─────────────────────────────────────────── -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <!-- Back to produksi index -->
            <a href="<?= $produksiUrl ?>" class="btn btn-sm btn-light border shadow-sm"
               title="Kembali ke semua mesin">
                <i class="fas fa-arrow-left me-1"></i> Semua Mesin
            </a>

            <!-- Mesin title pill -->
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                      style="width:36px;height:36px;background:<?= esc($mesinColor) ?>20;border:2px solid <?= esc($mesinColor) ?>40;">
                    <i class="fas fa-cog" style="color:<?= esc($mesinColor) ?>;font-size:.9rem;"></i>
                </span>
                <div>
                    <h5 class="mb-0 fw-bold" style="color:<?= esc($mesinColor) ?>;line-height:1.2;">
                        <?= esc($mesinLabel) ?>
                    </h5>
                    <span class="text-muted" style="font-size:.75rem;letter-spacing:.4px;text-transform:uppercase;">
                        Log Produksi
                    </span>
                </div>
            </div>
        </div>

        <!-- Mesin switcher — quick jump to other mesin -->
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-exchange-alt me-1"></i> Ganti Mesin
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="min-width:170px;">
                <?php foreach ($mesin_labels as $slug => $label): ?>
                    <li>
                        <a class="dropdown-item py-2 <?= $slug === ($mesin_slug ?? '') ? 'fw-bold text-primary' : '' ?>"
                           href="<?= base_url('produksi/mesin_' . $slug) ?>">
                            <?php if ($slug === ($mesin_slug ?? '')): ?>
                                <i class="fas fa-check me-2 text-primary small"></i>
                            <?php else: ?>
                                <i class="fas fa-chevron-right me-2 text-muted small opacity-50"></i>
                            <?php endif; ?>
                            <?= esc($label) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- ── LOG MESIN nav buttons (matching PPIC layout) ──────── -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <?= view('produksi/partials/machine_links', [
                'filter_mesin' => $mesin_slug ?? '',
            ]) ?>
        </div>
    </div>

    <!-- ── Laporan table ─────────────────────────────────────────────── -->
    <div class="card shadow mb-4 border-0">

        <!-- Toolbar — mesin-scoped URLs -->
        <?= view('produksi/partials/mesin_toolbar', [
            'filter_mesin'   => $filter_mesin   ?? '',
            'mesin_labels'   => $mesin_labels   ?? [],
            'mesin_slug'     => $mesin_slug     ?? '',
            'mesin_label'    => $mesinLabel,
            'mesin_color'    => $mesinColor,
            'q'              => $q              ?? '',
            'filter'         => $filter         ?? '',
            'filter_shift'   => $filter_shift   ?? '',
            'filter_tanggal' => $filter_tanggal ?? '',
            'base_url'       => $baseUrl,
        ]) ?>

        <!-- Table — passes filter_mesin so header/empty-state are mesin-aware -->
        <div class="card-body p-0">
            <?= view('produksi/partials/table', [
                'produksi'       => $produksi       ?? [],
                'pager'          => $pager          ?? null,
                'filter_mesin'   => $filter_mesin   ?? '',
                'mesin_labels'   => $mesin_labels   ?? [],
                'q'              => $q              ?? '',
                'filter'         => $filter         ?? '',
                'filter_shift'   => $filter_shift   ?? '',
                'filter_tanggal' => $filter_tanggal ?? '',
            ]) ?>
        </div>
    </div>
</div>

<!-- ── Modals (same as index.php) ──────────────────────────────────── -->
<?= view('produksi/modals/add', [
    'spk_list'           => $spk_list,
    'context_mesin_slug' => $context_mesin_slug ?? '',
]) ?>

<!-- Edit Modal -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" id="editModalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" id="detailModalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true" style="z-index:1065;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i> Rincian Reject
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="table_reject_modal">
                        <thead class="bg-light">
                            <tr>
                                <th>Jenis Reject</th>
                                <th width="150">Jumlah</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm mt-2" id="btn_add_reject_row">
                    <i class="fas fa-plus"></i> Tambah Baris Reject
                </button>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_save_reject_generic" class="btn btn-danger px-4">Simpan Reject</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="modalDeleteConfirm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content border-0 shadow-lg text-center p-4">
            <div class="mb-3">
                <i class="fas fa-trash-alt text-danger fa-4x opacity-20"></i>
            </div>
            <h5 class="font-weight-bold">Hapus Laporan?</h5>
            <p class="text-muted small">Data akan dipindahkan ke tempat sampah dan dapat dipulihkan nanti.</p>
            <div class="d-grid gap-2 mt-4">
                <a href="#" id="btn_final_delete" class="btn btn-danger">Ya, Hapus Data</a>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Trash Modal -->
<div class="modal fade" id="trashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-muted">
                    <i class="fas fa-trash-restore me-2"></i> Tempat Sampah
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height:400px;">
                    <table class="table table-hover mb-0" id="table_trash">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="px-3 py-2">Laporan</th>
                                <th class="py-2">Dihapus</th>
                                <th class="text-center py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody><!-- Loaded via JS --></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Drawer — uses mesin-scoped base URL -->
<div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="plantFilterDrawer">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title font-weight-bold">
            Filter — <?= esc($mesinLabel) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form action="<?= esc($baseUrl) ?>" method="get">
            <input type="hidden" name="q" value="<?= esc($q ?? '') ?>">
            <?php if (!empty($filter)): ?>
                <input type="hidden" name="filter" value="<?= esc($filter) ?>">
            <?php endif; ?>

            <div class="mb-4">
                <label class="form-label fw-semibold">Pilih Shift</label>
                <select name="shift" class="form-select">
                    <option value="">Semua Shift</option>
                    <option value="1" <?= ($filter_shift ?? '') == '1' ? 'selected' : '' ?>>Shift 1</option>
                    <option value="2" <?= ($filter_shift ?? '') == '2' ? 'selected' : '' ?>>Shift 2</option>
                    <option value="3" <?= ($filter_shift ?? '') == '3' ? 'selected' : '' ?>>Shift 3</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Tanggal Spesifik</label>
                <input type="date" name="tanggal" class="form-control" value="<?= esc($filter_tanggal ?? '') ?>">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="<?= esc($baseUrl) ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<?= view('produksi/scripts') ?>

<?= $this->endSection() ?>
