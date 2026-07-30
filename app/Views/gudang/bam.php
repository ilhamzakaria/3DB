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

$formatDate = static function ($value): string {
    $timestamp = $value ? strtotime((string) $value) : false;
    return $timestamp ? date('d-m-Y', $timestamp) : '-';
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
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                <thead>
                    <tr class="font-weight-bold">
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Type</th>
                        <th>Item Barang</th>
                        <th>Status</th>
                        <th>Kode</th>
                        <th>Jumlah</th>
                        <th>Nomor Lot</th>
                        <th>No SPK</th>
                        <th>Shif</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data_list)) : ?>
                        <?php foreach ($data_list as $item) : ?>
                            <?php
                            $status = (string) ($item['status'] ?? 'bahan_masuk');
                            $statusText = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                            $statusBadge = $statusBadges[$status] ?? 'badge-secondary';
                            ?>
                            <tr>
                                <td><?= esc($formatDate($item['tanggal'] ?? null)) ?></td>
                                <td><?= esc($item['produk'] ?? '-') ?></td>
                                <td><?= esc($item['tipe'] ?? '-') ?></td>
                                <td><?= esc($item['nama_bam'] ?? '-') ?></td>
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
                                 <td>
                                     <?php if (in_array(session()->get('role'), ['gudang', 'admin'], true)) : ?>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm"
                                                onclick="setEditModal(this)"
                                                data-toggle="modal"
                                                data-target="#editDataModal"
                                                data-id="<?= esc($item['id'] ?? 0) ?>"
                                                data-tanggal="<?= esc($item['tanggal'] ?? '') ?>"
                                                data-produk="<?= esc($item['produk'] ?? '') ?>"
                                                data-tipe="<?= esc($item['tipe'] ?? '') ?>"
                                                data-nama="<?= esc($item['nama_bam'] ?? '') ?>"
                                                data-status="<?= esc($item['status'] ?? '') ?>"
                                                data-kode="<?= esc($item['kode'] ?? '') ?>"
                                                data-jumlah="<?= esc($item['jumlah'] ?? '') ?>"
                                                data-lot="<?= esc($item['nomor_lot'] ?? '') ?>"
                                                data-spk="<?= esc($item['no_spk'] ?? '') ?>"
                                                data-shif="<?= esc($item['shif'] ?? '') ?>"
                                                data-action="<?= site_url('gudang/bam/update/' . ($item['id'] ?? 0)) ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm ml-1"
                                                onclick="setDeleteModal(this)"
                                                data-toggle="modal"
                                                data-target="#modalDelete"
                                                data-id="<?= esc($item['id'] ?? 0) ?>"
                                                data-nama="<?= esc($item['nama_bam'] ?? '') ?>"
                                                data-action="<?= site_url('gudang/bam/delete/' . ($item['id'] ?? 0)) ?>">
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
                            <td colspan="11">Tidak ada data BAM</td>
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
                <h5 class="modal-title" id="addDataModalLabel">Tambah BAM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="<?= base_url('gudang/bam/tambahData') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal</label><input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>No SPK</label><input type="number" class="form-control" name="no_spk"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Produk <span class="text-danger">*</span></label>
                                <select class="form-control select2-bam" id="produk" name="produk" required style="width: 100%;">
                                    <?php foreach (($bam_products ?? ['BAM']) as $produk) : ?>
                                        <option value="<?= esc($produk) ?>" selected><?= esc($produk) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control select2-bam" id="tipe" name="tipe" required style="width: 100%;">
                                    <?php foreach (($bam_types ?? []) as $index => $tipe) : ?>
                                        <option value="<?= esc($tipe) ?>" <?= $index === 0 ? 'selected' : '' ?>><?= esc($tipe) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Item Barang <span class="text-danger">*</span></label>
                        <select class="form-control select2-bam" id="nama_bam" name="nama_bam" required style="width: 100%;">
                            <option value="">-- Pilih Item Barang --</option>
                            <?php
                            $defaultType = $bam_types[0] ?? '';
                            foreach (($bam_options[$defaultType] ?? []) as $option) :
                            ?>
                                <option value="<?= esc($option['item']) ?>"><?= esc($option['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status">
                            <?php foreach (($status_options ?? []) as $status) : ?>
                                <option value="<?= esc($status) ?>" <?= $status === 'bahan_masuk' ? 'selected' : '' ?>><?= esc($statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Kode</label><input type="text" class="form-control" id="kode" name="kode"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Jumlah</label><input type="text" class="form-control" name="jumlah"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Shift</label><input type="text" class="form-control" name="shif"></div></div>
                    </div>
                    <div class="form-group"><label>Nomor Lot</label><input type="text" class="form-control" name="nomor_lot"></div>
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
                <h5 class="modal-title" id="editDataModalLabel">Edit BAM</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal</label><input type="date" class="form-control" id="edit_tanggal" name="tanggal" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>No SPK</label><input type="number" class="form-control" id="edit_no_spk" name="no_spk"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Produk <span class="text-danger">*</span></label>
                                <select class="form-control select2-bam" id="edit_produk" name="produk" required style="width: 100%;">
                                    <?php foreach (($bam_products ?? ['BAM']) as $produk) : ?>
                                        <option value="<?= esc($produk) ?>"><?= esc($produk) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type <span class="text-danger">*</span></label>
                                <select class="form-control select2-bam" id="edit_tipe" name="tipe" required style="width: 100%;">
                                    <option value="">-- Pilih Type --</option>
                                    <?php foreach (($bam_types ?? []) as $tipe) : ?>
                                        <option value="<?= esc($tipe) ?>"><?= esc($tipe) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Item Barang <span class="text-danger">*</span></label>
                        <select class="form-control select2-bam" id="edit_nama_bam" name="nama_bam" required style="width: 100%;">
                            <option value="">-- Pilih Type Terlebih Dahulu --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="edit_status" name="status">
                            <?php foreach (($status_options ?? []) as $status) : ?>
                                <option value="<?= esc($status) ?>"><?= esc($statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Kode</label><input type="text" class="form-control" id="edit_kode" name="kode"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Jumlah</label><input type="text" class="form-control" id="edit_jumlah" name="jumlah"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Shift</label><input type="text" class="form-control" id="edit_shif" name="shif"></div></div>
                    </div>
                    <div class="form-group"><label>Nomor Lot</label><input type="text" class="form-control" id="edit_nomor_lot" name="nomor_lot"></div>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="formDelete" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body"><p class="mb-0" id="delete_message">Yakin ingin menghapus data ini?</p></div>
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

<?= $this->endSection() ?>

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

    const bamData = <?= json_encode($bam_options ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    function populateItems(tipeSelectId, itemSelectId, selectedValue = '') {
        const tipeSelect = document.getElementById(tipeSelectId);
        const itemSelect = document.getElementById(itemSelectId);
        const $itemSelect = window.jQuery ? window.jQuery(itemSelect) : null;

        if (!tipeSelect || !itemSelect) {
            return;
        }

        itemSelect.innerHTML = '<option value="">-- Pilih Item Barang --</option>';

        if (tipeSelect.value && bamData[tipeSelect.value]) {
            bamData[tipeSelect.value].forEach(function(opt) {
                const option = document.createElement('option');
                option.value = opt.item;
                option.textContent = opt.label || opt.item;
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

    function autoFillKode(tipeSelectId, itemSelectId, kodeInputId) {
        const tipeSelect = document.getElementById(tipeSelectId);
        const itemSelect = document.getElementById(itemSelectId);
        const kodeInput = document.getElementById(kodeInputId);

        if (!tipeSelect || !itemSelect || !kodeInput || !bamData[tipeSelect.value]) {
            if (kodeInput) kodeInput.value = '';
            return;
        }

        const selectedItem = itemSelect.value;
        const match = bamData[tipeSelect.value].find(function(opt) {
            return opt.item === selectedItem;
        });

        kodeInput.value = match ? (match.kode_barang || '') : '';
    }

    function initSelect2ForModal(modalSelector) {
        if (!window.jQuery || typeof window.jQuery.fn.select2 !== 'function') {
            return;
        }

        const $modal = window.jQuery(modalSelector);
        if (!$modal.length) {
            return;
        }

        $modal.find('select.select2-bam').each(function() {
            const $select = window.jQuery(this);

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

    document.addEventListener('DOMContentLoaded', function() {
        populateItems('tipe', 'nama_bam');
        populateItems('edit_tipe', 'edit_nama_bam');

        document.getElementById('tipe').addEventListener('change', function() {
            populateItems('tipe', 'nama_bam');
            autoFillKode('tipe', 'nama_bam', 'kode');
        });

        document.getElementById('nama_bam').addEventListener('change', function() {
            autoFillKode('tipe', 'nama_bam', 'kode');
        });

        document.getElementById('edit_tipe').addEventListener('change', function() {
            populateItems('edit_tipe', 'edit_nama_bam');
            autoFillKode('edit_tipe', 'edit_nama_bam', 'edit_kode');
        });

        document.getElementById('edit_nama_bam').addEventListener('change', function() {
            autoFillKode('edit_tipe', 'edit_nama_bam', 'edit_kode');
        });

        if (window.jQuery) {
            window.jQuery('#addDataModal').on('shown.bs.modal', function() {
                initSelect2ForModal('#addDataModal');
            });

            window.jQuery('#addDataModal').on('hidden.bs.modal', function() {
                const addForm = document.querySelector('form[action="<?= base_url('gudang/bam/tambahData') ?>"]');
                if (addForm) {
                    addForm.reset();
                }
                document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
                populateItems('tipe', 'nama_bam');
            });

            window.jQuery('#editDataModal').on('shown.bs.modal', function() {
                initSelect2ForModal('#editDataModal');
            });
        }
    });

    function setEditModal(button) {
        document.getElementById('edit_id').value = button.getAttribute('data-id') || '';
        document.getElementById('edit_tanggal').value = button.getAttribute('data-tanggal') || '<?= date('Y-m-d') ?>';
        document.getElementById('edit_no_spk').value = button.getAttribute('data-spk') || '';
        document.getElementById('edit_produk').value = button.getAttribute('data-produk') || 'BAM';
        document.getElementById('edit_tipe').value = button.getAttribute('data-tipe') || '';
        populateItems('edit_tipe', 'edit_nama_bam', button.getAttribute('data-nama') || '');
        document.getElementById('edit_status').value = button.getAttribute('data-status') || 'bahan_masuk';
        document.getElementById('edit_kode').value = button.getAttribute('data-kode') || '';
        document.getElementById('edit_jumlah').value = button.getAttribute('data-jumlah') || '';
        document.getElementById('edit_nomor_lot').value = button.getAttribute('data-lot') || '';
        document.getElementById('edit_shif').value = button.getAttribute('data-shif') || '';
        document.getElementById('formEdit').setAttribute('action', button.getAttribute('data-action') || '<?= site_url('gudang/bam/update') ?>');

        if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
            window.jQuery('#edit_produk').trigger('change.select2');
            window.jQuery('#edit_tipe').trigger('change.select2');
            window.jQuery('#edit_nama_bam').trigger('change.select2');
        }
    }

    function setDeleteModal(button) {
        const id = button.getAttribute('data-id') || '';
        const nama = button.getAttribute('data-nama') || '';
        const action = button.getAttribute('data-action') || '<?= site_url('gudang/bam/delete') ?>';

        document.getElementById('delete_id').value = id;
        document.getElementById('formDelete').setAttribute('action', action);
        document.getElementById('delete_message').textContent = 'Yakin ingin menghapus BAM "' + nama + '"?';
    }
</script>
<?= view('shared/spk_produksi_autofill') ?>
<?= $this->endSection() ?>

<?= $this->section('vendor_scripts') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<?= $this->endSection() ?>
