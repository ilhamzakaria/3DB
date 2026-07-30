<?= $this->extend('layout/main') ?>

<?= $this->section('vendor_styles') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
<style>
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        border: 1px solid #d1d3e2;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        padding-right: 1.5rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 0.4rem;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('vendor_scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$sourceLabels = [
    'suplier' => 'Suplier',
    'produksi' => 'Produksi',
];

$finishGoodOptionsJson = json_encode(
    $finish_good_options ?? [],
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
) ?: '{}';

$formatDate = static function ($value): string {
    $timestamp = $value ? strtotime((string) $value) : false;

    return $timestamp ? date('d-m-Y', $timestamp) : '-';
};

$formatDateTime = static function ($value): string {
    $timestamp = $value ? strtotime((string) $value) : false;

    return $timestamp ? date('d-m-Y H:i', $timestamp) : '-';
};

$formatNumber = static function ($value): string {
    if ($value === null || $value === '') {
        return '0';
    }

    return number_format((float) $value, 0, ',', '.');
};
?>

<style>
    .filter-sidebar { position: fixed; top: 0; right: -350px; width: 350px; height: 100vh; background: #fff; z-index: 1100; box-shadow: -5px 0 15px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; }
    .filter-sidebar.active { right: 0; }
    .filter-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1090; display: none; backdrop-filter: blur(2px); }
    .filter-overlay.active { display: block; }
    .filter-header { padding: 1.25rem; border-bottom: 1px solid #e3e6f0; display: flex; justify-content: space-between; align-items: center; background: #f8f9fc; }
    .filter-body { padding: 1.5rem; flex-grow: 1; overflow-y: auto; }
    .filter-footer { padding: 1.25rem; border-top: 1px solid #e3e6f0; background: #f8f9fc; display: flex; gap: 10px; }
    .filter-chip { display: inline-flex; align-items: center; background: #eaecf4; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem; margin-right: 0.5rem; margin-bottom: 0.5rem; }
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="<?= base_url('gudang') ?>" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <?php if (in_array(session()->get('role'), ['gudang', 'admin'], true)) : ?>
                    <button class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#addDataModal">
                        <i class="fas fa-plus"></i> Tambah Data
                    </button>
                <?php endif; ?>
                <button class="btn btn-outline-primary btn-sm" onclick="toggleFilter(true)">
                    <i class="fas fa-filter"></i> Filter
                    <?php if (!empty($start_date) || !empty($end_date)) : ?>
                        <span class="badge badge-primary ml-1">!</span>
                    <?php endif; ?>
                </button>
            </div>

            <form method="get" action="<?= current_url() ?>" class="d-flex align-items-center mb-0 mt-2 mt-md-0">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" style="min-width: 200px;" placeholder="Cari data..." value="<?= esc($q ?? '') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($start_date) || !empty($end_date)) : ?>
            <div class="mt-3">
                <small class="text-muted mr-2">Filter aktif:</small>
                <?php if (!empty($start_date)) : ?><span class="filter-chip">Mulai: <?= esc($start_date) ?></span><?php endif; ?>
                <?php if (!empty($end_date)) : ?><span class="filter-chip">Selesai: <?= esc($end_date) ?></span><?php endif; ?>
                <a href="<?= current_url() ?>" class="text-danger small ml-2"><i class="fas fa-times"></i> Reset</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center font-weight-bold">
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Type</th>
                        <th>Kode Barang</th>
                        <th>Kode Warna</th>
                        <th>Item</th>
                        <th>Stok Maksimum</th>
                        <th>Stok Minimum</th>
                        <th>Unit</th>
                        <th>Awal</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Akhir</th>
                        <th>Updated At</th>
                        <th>Created At</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if (!empty($finish_good)) : ?>
                        <?php foreach ($finish_good as $item) : ?>
                            <?php
                            $source = (string) ($item['sumber_in'] ?? 'suplier');
                            $sourceText = $sourceLabels[$source] ?? ucfirst($source);
                            ?>
                            <tr>
                                <td><?= esc($formatDate($item['tanggal'] ?? null)) ?></td>
                                <td><?= esc($item['produk'] ?? '-') ?></td>
                                <td><?= esc($item['type'] ?? '-') ?></td>
                                <td><?= esc($item['kode_barang'] ?? '-') ?></td>
                                <td><?= esc($item['kode_warna'] ?? '-') ?></td>
                                <td><?= esc($item['item'] ?? '-') ?></td>
                                <td><?= esc($formatNumber($item['stok_maximum'] ?? 0)) ?></td>
                                <td><?= esc($formatNumber($item['stok_minimum'] ?? 0)) ?></td>
                                <td><?= esc($item['unit'] ?? '-') ?></td>
                                <td><?= esc($formatNumber($item['awal'] ?? 0)) ?></td>
                                <td><span class="badge badge-light border"><?= esc($sourceText) ?></span></td>
                                <td><?= esc($formatNumber($item['out'] ?? 0)) ?></td>
                                <td><?= esc($formatNumber($item['akhir'] ?? 0)) ?></td>
                                <td><?= esc($formatDateTime($item['updated_at'] ?? null)) ?></td>
                                <td><?= esc($formatDateTime($item['created_at'] ?? null)) ?></td>
                                <td>
                                    <?php if (in_array(session()->get('role'), ['gudang', 'admin'], true)) : ?>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm"
                                                onclick="setEditModal(this)"
                                                data-toggle="modal"
                                                data-target="#editDataModal"
                                                data-id="<?= esc($item['id']) ?>"
                                                data-tanggal="<?= esc($item['tanggal'] ?? '') ?>"
                                                data-produk="<?= esc($item['produk'] ?? '') ?>"
                                                data-type="<?= esc($item['type'] ?? '') ?>"
                                                data-kode-barang="<?= esc($item['kode_barang'] ?? '') ?>"
                                                data-kode-warna="<?= esc($item['kode_warna'] ?? '') ?>"
                                                data-item="<?= esc($item['item'] ?? '') ?>"
                                                data-stok-maximum="<?= esc($item['stok_maximum'] ?? '') ?>"
                                                data-stok-minimum="<?= esc($item['stok_minimum'] ?? '') ?>"
                                                data-unit="<?= esc($item['unit'] ?? '') ?>"
                                                data-awal="<?= esc($item['awal'] ?? '') ?>"
                                                data-sumber-in="<?= esc($item['sumber_in'] ?? '') ?>"
                                                data-out="<?= esc($item['out'] ?? '') ?>"
                                                data-akhir="<?= esc($item['akhir'] ?? '') ?>"
                                                data-action="<?= site_url('gudang/finish_good/update/' . ($item['id'] ?? 0)) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm ml-1"
                                                onclick="setDeleteModal(this)"
                                                data-toggle="modal"
                                                data-target="#modalDelete"
                                                data-id="<?= esc($item['id'] ?? 0) ?>"
                                                data-nama="<?= esc($item['item'] ?? '') ?>"
                                                data-action="<?= site_url('gudang/finish_good/delete/' . ($item['id'] ?? 0)) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="16" class="text-center">Tidak ada data finish good</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="addDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDataModalLabel">Tambah Finish Good</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('gudang/finish_good/tambahData') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit">Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="unit" name="unit" value="pcs" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produk">Produk <span class="text-danger">*</span></label>
                                <select class="form-control select2-finish-good" id="produk" name="produk" required style="width: 100%;">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach (($finish_good_products ?? []) as $prod) : ?>
                                        <option value="<?= esc($prod) ?>"><?= esc($prod) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select class="form-control select2-finish-good" id="type" name="type" required style="width: 100%;">
                                    <option value="">-- Pilih Type --</option>
                                    <?php foreach (($finish_good_types ?? []) as $tipe) : ?>
                                        <option value="<?= esc($tipe) ?>"><?= esc($tipe) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="item">Item <span class="text-danger">*</span></label>
                                <select class="form-control select2-finish-good" id="item" name="item" required style="width: 100%;">
                                    <option value="">-- Pilih Type Terlebih Dahulu --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_barang">Kode Barang</label>
                                <input type="text" class="form-control" id="kode_barang" name="kode_barang">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode_warna">Kode Warna</label>
                                <input type="text" class="form-control" id="kode_warna" name="kode_warna">
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stok_maximum">Stok Maksimum</label>
                                <input type="number" class="form-control" id="stok_maximum" name="stok_maximum" min="0" step="1" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="stok_minimum">Stok Minimum</label>
                                <input type="number" class="form-control" id="stok_minimum" name="stok_minimum" min="0" step="1" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="awal">Awal</label>
                                <input type="number" class="form-control" id="awal" name="awal" min="0" step="1" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sumber_in">In</label>
                                <select class="form-control" id="sumber_in" name="sumber_in">
                                    <?php foreach (($source_options ?? []) as $source) : ?>
                                        <option value="<?= esc($source) ?>"><?= esc($sourceLabels[$source] ?? ucfirst($source)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="out">Out</label>
                                <input type="number" class="form-control" id="out" name="out" min="0" step="1" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="akhir">Akhir</label>
                                <input type="number" class="form-control" id="akhir" name="akhir" min="0" step="1" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDataModalLabel">Edit Finish Good</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit" action="<?= base_url('gudang/finish_good/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tanggal">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_unit">Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_unit" name="unit" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_produk">Produk <span class="text-danger">*</span></label>
                                <select class="form-control select2-finish-good" id="edit_produk" name="produk" required style="width: 100%;">
                                    <option value="">-- Pilih Produk --</option>
                                    <?php foreach (($finish_good_products ?? []) as $prod) : ?>
                                        <option value="<?= esc($prod) ?>"><?= esc($prod) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_type">Type <span class="text-danger">*</span></label>
                                <select class="form-control select2-finish-good" id="edit_type" name="type" required style="width: 100%;">
                                    <option value="">-- Pilih Type --</option>
                                    <?php foreach (($finish_good_types ?? []) as $tipe) : ?>
                                        <option value="<?= esc($tipe) ?>"><?= esc($tipe) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_item">Item <span class="text-danger">*</span></label>
                                <select class="form-control select2-finish-good" id="edit_item" name="item" required style="width: 100%;">
                                    <option value="">-- Pilih Type Terlebih Dahulu --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_kode_barang">Kode Barang</label>
                                <input type="text" class="form-control" id="edit_kode_barang" name="kode_barang">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_kode_warna">Kode Warna</label>
                                <input type="text" class="form-control" id="edit_kode_warna" name="kode_warna">
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_stok_maximum">Stok Maksimum</label>
                                <input type="number" class="form-control" id="edit_stok_maximum" name="stok_maximum" min="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_stok_minimum">Stok Minimum</label>
                                <input type="number" class="form-control" id="edit_stok_minimum" name="stok_minimum" min="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_awal">Awal</label>
                                <input type="number" class="form-control" id="edit_awal" name="awal" min="0" step="1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_sumber_in">In</label>
                                <select class="form-control" id="edit_sumber_in" name="sumber_in">
                                    <?php foreach (($source_options ?? []) as $source) : ?>
                                        <option value="<?= esc($source) ?>"><?= esc($sourceLabels[$source] ?? ucfirst($source)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_out">Out</label>
                                <input type="number" class="form-control" id="edit_out" name="out" min="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_akhir">Akhir</label>
                                <input type="number" class="form-control" id="edit_akhir" name="akhir" min="0" step="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formDelete" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_id">

                <div class="modal-body">
                    <p class="mb-0" id="delete_message">Yakin ingin menghapus data ini?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="filter-overlay" onclick="toggleFilter(false)"></div>
<div class="filter-sidebar">
    <div class="filter-header">
        <h5 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-2"></i> Filter Data</h5>
        <button type="button" class="close" onclick="toggleFilter(false)">&times;</button>
    </div>
    <form method="get" action="<?= current_url() ?>">
        <div class="filter-body">
            <input type="hidden" name="q" value="<?= esc($q ?? '') ?>">
            <div class="form-group">
                <label class="small font-weight-bold">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="<?= esc($start_date ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="small font-weight-bold">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="<?= esc($end_date ?? '') ?>">
            </div>
        </div>
        <div class="filter-footer">
            <a href="<?= current_url() ?>" class="btn btn-secondary flex-fill">Reset</a>
            <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        </div>
    </form>
</div>

<script>
    var itemData = <?= $finishGoodOptionsJson ?>;

    function toggleFilter(show) {
        if (show) {
            $('.filter-sidebar').addClass('active');
            $('.filter-overlay').addClass('active');
        } else {
            $('.filter-sidebar').removeClass('active');
            $('.filter-overlay').removeClass('active');
        }
    }

    function populateItems(prefix, selectedValue = '') {
        var typeSelect = document.getElementById(prefix + 'type');
        var itemSelect = document.getElementById(prefix + 'item');
        var $itemSelect = window.jQuery ? window.jQuery(itemSelect) : null;
        
        itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
        
        if (typeSelect && typeSelect.value && itemData[typeSelect.value]) {
            itemData[typeSelect.value].forEach(function(opt) {
                var option = document.createElement('option');
                option.value = opt.item;
                option.textContent = opt.item;
                if (opt.item === selectedValue) {
                    option.selected = true;
                }
                itemSelect.appendChild(option);
            });
        } else {
            itemSelect.innerHTML = '<option value="">-- Pilih Type Terlebih Dahulu --</option>';
        }

        if ($itemSelect && typeof window.jQuery.fn.select2 === 'function') {
            $itemSelect.trigger('change.select2');
        }
    }

    function autoFillKode(prefix) {
        var typeSelect = document.getElementById(prefix + 'type');
        var itemSelect = document.getElementById(prefix + 'item');
        var kodeBarangInput = document.getElementById(prefix + 'kode_barang');
        var kodeWarnaInput = document.getElementById(prefix + 'kode_warna');
        
        if (typeSelect && itemSelect && typeSelect.value && itemData[typeSelect.value]) {
            var selectedItem = itemSelect.value;
            var match = itemData[typeSelect.value].find(function(opt) {
                return opt.item === selectedItem;
            });
            if (match) {
                if (kodeBarangInput) kodeBarangInput.value = match.kode_barang !== '-' ? match.kode_barang : '';
                if (kodeWarnaInput) kodeWarnaInput.value = match.kode_warna !== '-' ? match.kode_warna : '';
                return;
            }
        }
        
        if (kodeBarangInput) kodeBarangInput.value = '';
        if (kodeWarnaInput) kodeWarnaInput.value = '';
    }

    document.getElementById('type').addEventListener('change', function() {
        populateItems('', '');
        autoFillKode('');
    });

    document.getElementById('item').addEventListener('change', function() {
        autoFillKode('');
    });

    document.getElementById('edit_type').addEventListener('change', function() {
        populateItems('edit_', '');
        autoFillKode('edit_');
    });

    document.getElementById('edit_item').addEventListener('change', function() {
        autoFillKode('edit_');
    });

    function setEditModal(button) {
        var id = button.getAttribute('data-id') || '';
        var tanggal = button.getAttribute('data-tanggal') || '';
        var produk = button.getAttribute('data-produk') || '';
        var type = button.getAttribute('data-type') || '';
        var kodeBarang = button.getAttribute('data-kode-barang') || '';
        var kodeWarna = button.getAttribute('data-kode-warna') || '';
        var item = button.getAttribute('data-item') || '';
        var stokMaximum = button.getAttribute('data-stok-maximum') || '';
        var stokMinimum = button.getAttribute('data-stok-minimum') || '';
        var unit = button.getAttribute('data-unit') || '';
        var awal = button.getAttribute('data-awal') || '';
        var sumberIn = button.getAttribute('data-sumber-in') || '';
        var out = button.getAttribute('data-out') || '';
        var akhir = button.getAttribute('data-akhir') || '';
        var action = button.getAttribute('data-action') || "<?= site_url('gudang/finish_good/update') ?>";

        document.getElementById('edit_produk').value = produk;
        document.getElementById('edit_type').value = type;
        
        populateItems('edit_', item);
        
        document.getElementById('edit_kode_barang').value = kodeBarang !== '-' ? kodeBarang : '';
        document.getElementById('edit_kode_warna').value = kodeWarna !== '-' ? kodeWarna : '';

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_tanggal').value = tanggal;
        document.getElementById('edit_stok_maximum').value = stokMaximum;
        document.getElementById('edit_stok_minimum').value = stokMinimum;
        document.getElementById('edit_unit').value = unit;
        document.getElementById('edit_awal').value = awal;
        document.getElementById('edit_sumber_in').value = sumberIn;
        document.getElementById('edit_out').value = out;
        document.getElementById('edit_akhir').value = akhir;
        document.getElementById('formEdit').setAttribute('action', action);

        if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            window.jQuery('#edit_produk').trigger('change.select2');
            window.jQuery('#edit_type').trigger('change.select2');
            window.jQuery('#edit_item').trigger('change.select2');
        }
    }

    function setDeleteModal(button) {
        var id = $(button).data('id');
        var nama = $(button).data('nama');
        var action = $(button).data('action');

        $('#delete_id').val(id);
        $('#formDelete').attr('action', action);
        $('#delete_message').text('Yakin ingin menghapus finish good "' + nama + '"?');
    }

    function initSelect2FinishGood(modalSelector) {
        if (!window.jQuery || typeof window.jQuery.fn.select2 !== 'function') {
            return;
        }

        var $modal = window.jQuery(modalSelector);

        if (!$modal.length) {
            return;
        }

        $modal.find('select.select2-finish-good').each(function() {
            var $select = window.jQuery(this);

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            $select.select2({
                width: '100%',
                dropdownParent: $modal,
                minimumResultsForSearch: 0
            });
        });
    }

    $(function() {
        var addModal = document.getElementById('addDataModal');
        var editModal = document.getElementById('editDataModal');

        populateItems('', '');
        populateItems('edit_', '');

        if (addModal) {
            $(addModal).on('shown.bs.modal', function() {
                initSelect2FinishGood('#addDataModal');
            });

            $(addModal).on('hidden.bs.modal', function() {
                document.querySelector('form[action="<?= base_url('gudang/finish_good/tambahData') ?>"]').reset();
                document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
                document.getElementById('unit').value = 'pcs';
                populateItems('', '');
                if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
                    window.jQuery('#produk').trigger('change.select2');
                    window.jQuery('#type').trigger('change.select2');
                }
            });
        }

        if (editModal) {
            $(editModal).on('shown.bs.modal', function() {
                initSelect2FinishGood('#editDataModal');
            });
        }
    });
</script>

<?= $this->endSection() ?>
