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

<?= $this->section('content') ?>

<?php
$statusLabels = [
    'menggiling' => 'Menggiling',
    'bahan_masuk' => 'Bahan Masuk',
    'bahan_keluar' => 'Bahan Keluar',
];

$statusBadges = [
    'menggiling' => 'badge-warning',
    'bahan_masuk' => 'badge-success',
    'bahan_keluar' => 'badge-info',
];

$formatDateTime = static function ($value): string {
    $timestamp = $value ? strtotime((string) $value) : false;

    return $timestamp ? date('d-m-Y H:i', $timestamp) : '-';
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
                    <?php if (!empty($start_date) || !empty($end_date) || !empty($sumber_filter)) : ?>
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
        
        <?php if (!empty($start_date) || !empty($end_date) || !empty($sumber_filter)) : ?>
            <div class="mt-3">
                <small class="text-muted mr-2">Filter aktif:</small>
                <?php if (!empty($start_date)) : ?><span class="filter-chip">Mulai: <?= esc($start_date) ?></span><?php endif; ?>
                <?php if (!empty($end_date)) : ?><span class="filter-chip">Selesai: <?= esc($end_date) ?></span><?php endif; ?>
                <?php if (!empty($sumber_filter)) : ?><span class="filter-chip">Sumber: <?= esc($sumberLabels[$sumber_filter] ?? $sumber_filter) ?></span><?php endif; ?>
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
                        <th>Produk</th>
                        <th>Type</th>
                        <th>Item Barang</th>
                        <th>Status</th>
                        <th>Kode</th>
                        <th>Jumlah</th>
                        <th>Nomor Lot</th>
                        <th>No SPK</th>
                        <th>Shif</th>
                        <th>Updated At</th>
                        <th>Created At</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if (!empty($bahan_baku)) : ?>
                        <?php foreach ($bahan_baku as $item) : ?>
                            <?php
                            $status = (string) ($item['status'] ?? 'menggiling');
                            $statusText = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                            $statusBadge = $statusBadges[$status] ?? 'badge-secondary';
                            ?>
                            <tr>
                                <td><?= esc($item['produk'] ?? '-') ?></td>
                                <td><?= esc($item['tipe'] ?? '-') ?></td>
                                <td><?= esc($item['nama_bahan'] ?? '-') ?></td>
                                <td><span class="badge <?= esc($statusBadge) ?>"><?= esc($statusText) ?></span></td>
                                <td><?= esc($item['kode'] ?? '-') ?></td>
                                <td><?= esc($item['jumlah'] ?? '-') ?></td>
                                <td><?= esc($item['nomor_lot'] ?? '-') ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-link btn-spk-produksi-autofill p-0" data-spk="<?= esc($item['no_spk'] ?? '') ?>" style="text-decoration: none; color: #4361ee;">
                                        <?= esc($item['no_spk'] ?? '-') ?>
                                    </button>
                                </td>
                                <td><?= esc($item['shif'] ?? '-') ?></td>
                                <td><?= esc($formatDateTime($item['updated_at'] ?? null)) ?></td>
                                <td><?= esc($formatDateTime($item['created_at'] ?? null)) ?></td>
                                <td>
                                    <?php if (in_array(session()->get('role'), ['gudang', 'admin'], true)) : ?>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <span>
                                                <button
                                                    type="button"
                                                    class="btn btn-warning btn-sm edit-btn"
                                                    onclick="setEditModal(this)"
                                                    data-toggle="modal"
                                                    data-target="#editDataModal"
                                                    data-id="<?= esc($item['id']) ?>"
                                                    data-produk="<?= esc($item['produk'] ?? '') ?>"
                                                    data-tipe="<?= esc($item['tipe'] ?? '') ?>"
                                                    data-nama="<?= esc($item['nama_bahan'] ?? '') ?>"
                                                    data-status="<?= esc($item['status'] ?? '') ?>"
                                                    data-kode="<?= esc($item['kode'] ?? '') ?>"
                                                    data-jumlah="<?= esc($item['jumlah'] ?? '') ?>"
                                                    data-lot="<?= esc($item['nomor_lot'] ?? '') ?>"
                                                    data-spk="<?= esc($item['no_spk'] ?? '') ?>"
                                                    data-shif="<?= esc($item['shif'] ?? '') ?>"

                                                    data-action="<?= site_url('gudang/bahan_baku/update/' . ($item['id'] ?? 0)) ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </span>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm ml-1"
                                                onclick="setDeleteModal(this)"
                                                data-toggle="modal"
                                                data-target="#modalDelete"
                                                data-id="<?= esc($item['id'] ?? 0) ?>"
                                                data-nama="<?= esc($item['nama_bahan'] ?? '') ?>"
                                                data-action="<?= site_url('gudang/bahan_baku/delete/' . ($item['id'] ?? 0)) ?>">
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
                            <td colspan="10" class="text-center">Tidak ada data bahan baku</td>
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
                <h5 class="modal-title" id="addDataModalLabel">Tambah Bahan Baku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('gudang/bahan_baku/tambahData') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="no_spk">No SPK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="no_spk" name="no_spk" inputmode="numeric" placeholder="Input No SPK..." required>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <p class="text-muted mb-3"><small>Semua data pada form ini diisi manual.</small></p>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produk">Produk <span class="text-danger">*</span></label>
                                <select class="form-control select2-bahan-baku" id="produk" name="produk" required style="width: 100%;">
                                    <?php foreach (($bahan_baku_products ?? []) as $produk) : ?>
                                        <option value="<?= esc($produk) ?>" <?= $produk === 'Galon' ? 'selected' : '' ?>><?= esc($produk) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipe">Type <span class="text-danger">*</span></label>
                                <select class="form-control select2-bahan-baku" id="tipe" name="tipe" required style="width: 100%;">
                                    <option value="">-- Pilih Type --</option>
                                    <?php foreach (($bahan_baku_types ?? []) as $tipe) : ?>
                                        <option value="<?= esc($tipe) ?>"><?= esc($tipe) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="nama_bahan">Item Barang <span class="text-danger">*</span></label>
                                <select class="form-control select2-bahan-baku" id="nama_bahan" name="nama_bahan" required style="width: 100%;">
                                    <option value="">-- Pilih Type Terlebih Dahulu --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <?php foreach (($status_options ?? []) as $status) : ?>
                                        <option value="<?= esc($status) ?>"><?= esc($statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kode">Kode</label>
                                <input type="text" class="form-control" id="kode" name="kode">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shif">Shift</label>
                                <input type="text" class="form-control" id="shif" name="shif" placeholder="Input shift">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nomor_lot">Nomor Lot</label>
                                <input type="text" class="form-control" id="nomor_lot" name="nomor_lot">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="text" class="form-control" id="jumlah" name="jumlah">
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


<!-- modal edit -->
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDataModalLabel">Edit Bahan Baku</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit" action="<?= base_url('gudang/bahan_baku/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipe">Type <span class="text-danger">*</span></label>
                                <select class="form-control select2-bahan-baku" id="edit_tipe" name="tipe" required style="width: 100%;">
                                    <option value="">-- Pilih Type --</option>
                                    <?php foreach (($bahan_baku_types ?? []) as $tipe) : ?>
                                        <option value="<?= esc($tipe) ?>"><?= esc($tipe) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nama_bahan">Item Barang <span class="text-danger">*</span></label>
                                <select class="form-control select2-bahan-baku" id="edit_nama_bahan" name="nama_bahan" required style="width: 100%;">
                                    <option value="">-- Pilih Type Terlebih Dahulu --</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nomor_lot">Nomor Lot</label>
                                <input type="text" class="form-control" id="edit_nomor_lot" name="nomor_lot" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_jumlah">Jumlah</label>
                                <input type="text" class="form-control" id="edit_jumlah" name="jumlah" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_kode">Kode</label>
                                <input type="text" class="form-control" id="edit_kode" name="kode">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_no_spk">No SPK</label>
                                <input type="number" class="form-control" id="edit_no_spk" name="no_spk" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_produk">Produk <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_produk" name="produk" required>
                                    <?php foreach (($bahan_baku_products ?? []) as $produk) : ?>
                                        <option value="<?= esc($produk) ?>" <?= $produk === 'Galon' ? 'selected' : '' ?>><?= esc($produk) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <?php foreach (($status_options ?? []) as $status) : ?>
                                        <option value="<?= esc($status) ?>"><?= esc($statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))) ?></option>
                                    <?php endforeach; ?>
                                </select>
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

<!-- Sidebar Filter -->
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
            <div class="form-group">
                <label class="small font-weight-bold">Sumber</label>
                <select name="sumber" class="form-control">
                    <option value="">Semua Sumber</option>
                    <?php foreach ($sumber_options ?? [] as $opt) : ?>
                        <option value="<?= esc($opt) ?>" <?= (isset($sumber_filter) && $sumber_filter === $opt) ? 'selected' : '' ?>><?= esc($sumberLabels[$opt] ?? ucfirst($opt)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="filter-footer">
            <a href="<?= current_url() ?>" class="btn btn-secondary flex-fill">Reset</a>
            <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        </div>
    </form>
</div>

<?= $this->section('scripts') ?>
<script>
    function toggleFilter(show) {
        if (show) {
            $('.filter-sidebar').addClass('active');
            $('.filter-overlay').addClass('active');
        } else {
            $('.filter-sidebar').removeClass('active');
            $('.filter-overlay').removeClass('active');
        }
    }

    const itemData = <?= json_encode($bahan_baku_options ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    function populateItems(tipeSelectId, itemSelectId, selectedValue = '') {
        const tipeSelect = document.getElementById(tipeSelectId);
        const itemSelect = document.getElementById(itemSelectId);
        const $itemSelect = window.jQuery ? window.jQuery(itemSelect) : null;
        
        itemSelect.innerHTML = '<option value="">-- Pilih Item Barang --</option>';
        itemSelect.disabled = false;
        itemSelect.removeAttribute('disabled');
        
        if (tipeSelect.value && itemData[tipeSelect.value]) {
            itemData[tipeSelect.value].forEach(function(item) {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                if (item === selectedValue) {
                    option.selected = true;
                }
                itemSelect.appendChild(option);
            });
        } else {
            itemSelect.innerHTML = '<option value="">-- Pilih Type Terlebih Dahulu --</option>';
        }

        if ($itemSelect && typeof window.jQuery.fn.select2 === 'function') {
            const modal = itemSelect.closest('.modal');
            const select2Options = {
                width: '100%',
                minimumResultsForSearch: 0,
            };

            if (modal) {
                select2Options.dropdownParent = window.jQuery(modal);
            }

            if ($itemSelect.hasClass('select2-hidden-accessible')) {
                $itemSelect.select2('destroy');
            }

            $itemSelect.select2(select2Options);
        }
    }

    document.getElementById('tipe').addEventListener('change', function() {
        populateItems('tipe', 'nama_bahan');
    });

    document.getElementById('edit_tipe').addEventListener('change', function() {
        populateItems('edit_tipe', 'edit_nama_bahan');
    });

    function setEditModal(button) {
        var id = button.getAttribute('data-id') || '';
        var produk = button.getAttribute('data-produk') || '';
        var tipe = button.getAttribute('data-tipe') || '';
        var nama = button.getAttribute('data-nama') || '';
        var status = button.getAttribute('data-status') || '';
        var kode = button.getAttribute('data-kode') || '';
        var jumlah = button.getAttribute('data-jumlah') || '';
        var lot = button.getAttribute('data-lot') || '';
        var spk = button.getAttribute('data-spk') || '';
        var action = button.getAttribute('data-action') || "<?= site_url('gudang/bahan_baku/update') ?>";

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_produk').value = produk;
        document.getElementById('edit_tipe').value = tipe;
        populateItems('edit_tipe', 'edit_nama_bahan', nama);
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_kode').value = kode;
        document.getElementById('edit_jumlah').value = jumlah;
        document.getElementById('edit_nomor_lot').value = lot;
        document.getElementById('edit_no_spk').value = spk;
        document.getElementById('formEdit').setAttribute('action', action);
    }

    function setDeleteModal(button) {
        var id = $(button).data('id');
        var nama = $(button).data('nama');
        var action = $(button).data('action');

        $('#delete_id').val(id);
        $('#formDelete').attr('action', action);
        $('#delete_message').text('Yakin ingin menghapus bahan baku "' + nama + '"?');
    }

    function initSelect2ForModal(modalSelector) {
        if (!window.jQuery || typeof window.jQuery.fn.select2 !== 'function') {
            return;
        }

        var $modal = window.jQuery(modalSelector);
        if (!$modal.length) {
            return;
        }

        $modal.find('select.select2-bahan-baku').each(function() {
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

        if (addModal) {
            $(addModal).on('shown.bs.modal', function() {
                initSelect2ForModal('#addDataModal');
                populateItems('tipe', 'nama_bahan');
                var noSpkInput = document.getElementById('no_spk');
                if (noSpkInput) {
                    noSpkInput.focus();
                }
            });

            $(addModal).on('hidden.bs.modal', function() {
                document.querySelector('form[action="<?= base_url('gudang/bahan_baku/tambahData') ?>"]').reset();
                populateItems('tipe', 'nama_bahan');
            });
        }

        if (editModal) {
            $(editModal).on('shown.bs.modal', function() {
                initSelect2ForModal('#editDataModal');
            });
        }

        populateItems('tipe', 'nama_bahan');
        populateItems('edit_tipe', 'edit_nama_bahan');
    });
</script>
<?= view('shared/spk_produksi_autofill') ?>
<?= $this->endSection() ?>

<?= $this->section('vendor_scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<?= $this->endSection() ?>


<?= $this->endSection() ?>
