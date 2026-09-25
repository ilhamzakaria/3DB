<?= $this->extend('layout/main') ?>
<?= $this->section('vendor_styles') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
<style>
    /* Custom Select2 styling for reject dropdowns */
    .select2-container--default .select2-selection--single {
        height: 36px !important;
        border-radius: 6px !important;
        border: 1px solid #dee2e6 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px !important;
        font-size: 0.875rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 34px !important;
    }
    .select2-dropdown {
        border-radius: 6px !important;
        border: 1px solid #dee2e6 !important;
        font-size: 0.875rem;
        z-index: 9999 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius: 4px !important;
        font-size: 0.875rem;
        padding: 0.5rem !important;
    }
    /* Dropdown harus overlay modal, bukan terpotong */
    .modal .select2-dropdown {
        z-index: 10060 !important;
    }
    /* Pastikan .select2-container tidak ter-clip */
    .select2-container {
        z-index: 10060 !important;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?= view('produksi/partials/styles') ?>

<div class="page-content-wrapper container-fluid py-4">
    <?php if (session('success')) : ?>
        <div class="alert alert-success d-flex align-items-center fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i>
            <span><?= session('success') ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session('error')) : ?>
        <div class="alert alert-danger d-flex align-items-center fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i>
            <span><?= session('error') ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- LOG MESIN navigation buttons (matching PPIC layout) -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white">
            <?= view('produksi/partials/machine_links', [
                'filter_mesin' => $filter_mesin ?? '',
            ]) ?>
        </div>
    </div>

    <!-- Laporan table -->
    <div class="card shadow mb-4 border-0">
        <?= view('produksi/partials/toolbar', [
            'filter_mesin'   => $filter_mesin   ?? '',
            'mesin_labels'   => $mesin_labels   ?? [],
            'q'              => $q              ?? '',
            'filter'         => $filter         ?? '',
            'filter_shift'   => $filter_shift   ?? '',
            'filter_tanggal' => $filter_tanggal ?? '',
        ]) ?>

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

<!-- Modals -->
<?= view('produksi/modals/add', compact('spk_list')) ?>

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
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
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
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
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
                <div class="table-responsive" style="max-height: 400px;">
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

<!-- Filter Drawer -->
<div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="plantFilterDrawer">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title font-weight-bold">Filter Laporan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form action="<?= base_url('produksi') ?>" method="get">
            <!-- Preserve active filters across submit -->
            <input type="hidden" name="q" value="<?= esc($q ?? '') ?>">
            <?php if (!empty($filter_mesin)): ?>
                <input type="hidden" name="mesin" value="<?= esc($filter_mesin) ?>">
            <?php endif; ?>
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
                <!-- Reset keeps mesin selection but clears shift/tanggal -->
                <a href="<?= base_url('produksi' . (!empty($filter_mesin) ? '?mesin=' . urlencode($filter_mesin) : '')) ?>"
                   class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<?= view('produksi/scripts') ?>

<?= $this->section('vendor_scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
