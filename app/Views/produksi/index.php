<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('produksi/partials/styles') ?>

<div class="page-content-wrapper container-fluid py-4">
    <?php if (session('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <?= view('produksi/partials/toolbar') ?>
        <?= view('produksi/partials/table') ?>
    </div>
</div>

<!-- Modals -->
<?= view('produksi/modals/add') ?>

<!-- Edit Modal (Container for AJAX) -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" id="editModalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Detail Modal (Container for AJAX) -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" id="detailModalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Reject Modal (Shared) -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Rincian Reject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <?php 
                    $rejectTypes = ['BOLONG', 'SUMBAT', 'TIPIS', 'GOMPAL', 'KOTOR/BINTIK', 'OVERFLOW', 'FLASHING', 'GORES', 'SHORT SHOT', 'OTHERS'];
                    foreach ($rejectTypes as $type): 
                    ?>
                    <div class="col-6">
                        <label class="form-label text-xs fw-bold"><?= $type ?></label>
                        <input type="number" class="form-control input-reject-detail" data-type="<?= $type ?>" placeholder="0">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn_save_reject_generic" class="btn btn-danger px-4" data-bs-dismiss="modal">Simpan Reject</button>
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
                <h5 class="modal-title font-weight-bold text-muted"><i class="fas fa-trash-restore me-2"></i> Tempat Sampah</h5>
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
                        <tbody>
                            <!-- Loaded via JS -->
                        </tbody>
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
            <input type="hidden" name="q" value="<?= esc($q ?? '') ?>">
            
            <div class="mb-4">
                <label class="form-label">Pilih Shift</label>
                <select name="shift" class="form-select">
                    <option value="">Semua Shift</option>
                    <option value="1" <?= ($filter_shift == '1') ? 'selected' : '' ?>>Shift 1</option>
                    <option value="2" <?= ($filter_shift == '2') ? 'selected' : '' ?>>Shift 2</option>
                    <option value="3" <?= ($filter_shift == '3') ? 'selected' : '' ?>>Shift 3</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Tanggal Spesifik</label>
                <input type="date" name="tanggal" class="form-control" value="<?= esc($filter_tanggal ?? '') ?>">
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="<?= base_url('produksi') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<?= view('produksi/scripts') ?>

<?= $this->endSection() ?>
