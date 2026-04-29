<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('ppic/partials/styles') ?>

<div class="page-content-wrapper container-fluid py-4">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <i class="fas fa-exclamation-circle mr-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0">
        <?php
        // Prepare variables for toolbar
        $activeFilterCount = 0;
        if (!empty($q)) $activeFilterCount++;
        if (!empty($start_date) || !empty($end_date)) $activeFilterCount++;
        
        $dateSummary = '';
        if (!empty($start_date) && !empty($end_date)) {
            $dateSummary = date('d/m/y', strtotime($start_date)) . ' - ' . date('d/m/y', strtotime($end_date));
        }

        $searchValue = $q ?? '';
        ?>
        
        <?= view('ppic/partials/toolbar', [
            'filter' => $filter,
            'searchValue' => $searchValue,
            'activeFilterCount' => $activeFilterCount,
            'dateSummary' => $dateSummary
        ]) ?>

        <div class="card-body p-0">
            <?= view('ppic/partials/table', [
                'produksi' => $produksi,
                'pager' => $pager
            ]) ?>
        </div>
    </div>
</div>

<!-- Modals -->
<?= view('ppic/modals/add', ['spk_master' => $spk_master]) ?>
<?= view('ppic/modals/edit') ?>

<!-- Modal Instagram Style Delete -->
<div class="modal fade ig-modal" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="ig-modal-body">
                <div class="ig-modal-title">Hapus Data PPIC?</div>
                <div class="ig-modal-text">Data PPIC No. SPK <span id="deleteSpkName" class="font-weight-bold"></span> akan dipindahkan ke tempat sampah.</div>
            </div>
            <div class="ig-btn-list">
                <form id="formDelete" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="ig-btn ig-btn-danger w-100">Hapus</button>
                </form>
                <button type="button" class="ig-btn ig-btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Trash Modal -->
<div class="modal fade" id="trashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-secondary">
                    <i class="fas fa-trash-alt mr-2"></i> Tempat Sampah PPIC
                </h5>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="table_trash" style="min-width: auto; table-layout: auto; width: 100%;">
                        <thead class="bg-white">
                            <tr class="text-xs text-muted text-uppercase">
                                <th class="px-3 py-3" style="width: 40%;">SPK / Produk</th>
                                <th class="py-3 text-center" style="width: 35%;">Dihapus Pada</th>
                                <th class="text-end py-3 px-3" style="width: 25%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-3">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Drawer -->
<div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="ppicFilterDrawer">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title font-weight-bold">Custom Filter</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <form action="<?= base_url('ppic') ?>" method="get">
            <div class="mb-4">
                <label class="filter-section-title">Rentang Tanggal</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?= $start_date ?? '' ?>">
                    </div>
                    <div class="col-6">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?= $end_date ?? '' ?>">
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mt-5">
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                <a href="<?= base_url('ppic') ?>" class="btn btn-light">Reset Semua</a>
            </div>
        </form>
    </div>
</div>

<?= view('ppic/scripts', ['spk_master' => $spk_master]) ?>

<?= $this->endSection() ?>
