<?= $this->extend('layout/main') ?>

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

$sumberLabels = [
    'supplier' => 'Reject Supplier',
    'produksi' => 'Reject Produksi',
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
                        <th>Item Galon Reject</th>
                        <th>Sumber</th>
                        <th>Status</th>
                        <th>Kode</th>
                        <th>Jumlah</th>
                        <th>Nomor Lot</th>
                        <th>No SPK</th>
                        <th>Shif</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php if (!empty($galon_reject)) : ?>
                        <?php foreach ($galon_reject as $item) : ?>
                            <?php
                            $status = (string) ($item['status'] ?? 'bahan_masuk');
                            $statusText = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));
                            $statusBadge = $statusBadges[$status] ?? 'badge-secondary';
                            $sumber = (string) ($item['sumber_reject'] ?? 'produksi');
                            $sumberText = $sumberLabels[$sumber] ?? ucfirst($sumber);
                            ?>
                            <tr>
                                <td><?= esc($item['produk'] ?? '-') ?></td>
                                <td><?= esc($item['nama_galon_reject'] ?? '-') ?></td>
                                <td><span class="badge badge-light border"><?= esc($sumberText) ?></span></td>
                                <td><span class="badge <?= esc($statusBadge) ?>"><?= esc($statusText) ?></span></td>
                                <td><?= esc($item['kode'] ?? '-') ?></td>
                                <td><?= esc($item['jumlah'] ?? '-') ?></td>
                                <td><?= esc($item['nomor_lot'] ?? '-') ?></td>
                                <td><?= esc($item['no_spk'] ?? '-') ?></td>
                                <td><?= esc($item['shif'] ?? '-') ?></td>
                                <td><?= esc($formatDateTime($item['created_at'] ?? null)) ?></td>
                                <td><?= esc($formatDateTime($item['updated_at'] ?? null)) ?></td>
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
                                                    data-nama="<?= esc($item['nama_galon_reject'] ?? '') ?>"
                                                    data-sumber="<?= esc($item['sumber_reject'] ?? '') ?>"
                                                    data-status="<?= esc($item['status'] ?? '') ?>"
                                                    data-kode="<?= esc($item['kode'] ?? '') ?>"
                                                    data-jumlah="<?= esc($item['jumlah'] ?? '') ?>"
                                                    data-lot="<?= esc($item['nomor_lot'] ?? '') ?>"
                                                    data-spk="<?= esc($item['no_spk'] ?? '') ?>"
                                                    data-shif="<?= esc($item['shif'] ?? '') ?>"
                                                    data-action="<?= site_url('gudang/galon_reject/update/' . ($item['id'] ?? 0)) ?>">
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
                                                data-nama="<?= esc($item['nama_galon_reject'] ?? '') ?>"
                                                data-action="<?= site_url('gudang/galon_reject/delete/' . ($item['id'] ?? 0)) ?>">
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
                            <td colspan="12" class="text-center">Tidak ada data galon reject</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="addDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDataModalLabel">Tambah Galon Reject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('gudang/galon_reject/tambahData') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no_spk">No SPK</label>
                                <input type="text" class="form-control" id="no_spk" name="no_spk" inputmode="numeric" placeholder="Input No SPK...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="produk">Produk</label>
                                <input type="text" class="form-control" id="produk" name="produk" placeholder="Input produk">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="nama_galon_reject">Item Galon Reject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_galon_reject" name="nama_galon_reject" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sumber_reject">Sumber Reject <span class="text-danger">*</span></label>
                                <select class="form-control" id="sumber_reject" name="sumber_reject" required>
                                    <?php foreach ($sumber_options as $opt) : ?>
                                        <option value="<?= esc($opt) ?>"><?= esc($sumberLabels[$opt] ?? ucfirst($opt)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <?php foreach ($status_options as $status) : ?>
                                        <option value="<?= esc($status) ?>" <?= ($status === 'bahan_masuk' ? 'selected' : '') ?>><?= esc($statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="kode">Kode</label>
                                <input type="text" class="form-control" id="kode" name="kode">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nomor_lot">Nomor Lot</label>
                                <input type="text" class="form-control" id="nomor_lot" name="nomor_lot">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="text" class="form-control" id="jumlah" name="jumlah">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="shif">Shift</label>
                                <input type="text" class="form-control" id="shif" name="shif" placeholder="Input shift">
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
                <h5 class="modal-title" id="editDataModalLabel">Edit Galon Reject</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit" action="<?= base_url('gudang/galon_reject/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="edit_nama_galon_reject">Nama Galon Reject</label>
                                <input type="text" class="form-control" id="edit_nama_galon_reject" name="nama_galon_reject" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_sumber_reject">Sumber Reject</label>
                                <select class="form-control" id="edit_sumber_reject" name="sumber_reject" required>
                                    <?php foreach ($sumber_options as $opt) : ?>
                                        <option value="<?= esc($opt) ?>"><?= esc($sumberLabels[$opt] ?? ucfirst($opt)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <?php foreach ($status_options as $status) : ?>
                                        <option value="<?= esc($status) ?>"><?= esc($statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status))) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_kode">Kode</label>
                                <input type="text" class="form-control" id="edit_kode" name="kode">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nomor_lot">Nomor Lot</label>
                                <input type="text" class="form-control" id="edit_nomor_lot" name="nomor_lot">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_jumlah">Jumlah</label>
                                <input type="text" class="form-control" id="edit_jumlah" name="jumlah">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_no_spk">No SPK</label>
                                <input type="number" class="form-control" id="edit_no_spk" name="no_spk">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_produk">Produk</label>
                                <input type="text" class="form-control" id="edit_produk" name="produk">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_shif">Shift</label>
                                <input type="text" class="form-control" id="edit_shif" name="shif">
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

    function setEditModal(button) {
        var id = button.getAttribute('data-id') || '';
        var produk = button.getAttribute('data-produk') || '';
        var nama = button.getAttribute('data-nama') || '';
        var sumber = button.getAttribute('data-sumber') || '';
        var status = button.getAttribute('data-status') || '';
        var kode = button.getAttribute('data-kode') || '';
        var jumlah = button.getAttribute('data-jumlah') || '';
        var lot = button.getAttribute('data-lot') || '';
        var spk = button.getAttribute('data-spk') || '';
        var shif = button.getAttribute('data-shif') || '';
        var action = button.getAttribute('data-action') || "<?= site_url('gudang/galon_reject/update') ?>";

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_produk').value = produk;
        document.getElementById('edit_nama_galon_reject').value = nama;
        document.getElementById('edit_sumber_reject').value = sumber;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_kode').value = kode;
        document.getElementById('edit_jumlah').value = jumlah;
        document.getElementById('edit_nomor_lot').value = lot;
        document.getElementById('edit_no_spk').value = spk;
        document.getElementById('edit_shif').value = shif;
        document.getElementById('formEdit').setAttribute('action', action);
    }

    function setDeleteModal(button) {
        var id = $(button).data('id');
        var nama = $(button).data('nama');
        var action = $(button).data('action');

        $('#delete_id').val(id);
        $('#formDelete').attr('action', action);
        $('#delete_message').text('Yakin ingin menghapus galon reject "' + nama + '"?');
    }

    $(function() {
        var addModal = document.getElementById('addDataModal');

        if (addModal) {
            $(addModal).on('shown.bs.modal', function() {
                var noSpkInput = document.getElementById('no_spk');
                if (noSpkInput) {
                    noSpkInput.focus();
                }
            });

            $(addModal).on('hidden.bs.modal', function() {
                document.querySelector('form[action="<?= base_url('gudang/galon_reject/tambahData') ?>"]').reset();
            });
        }
    });
</script>

<?= $this->endSection() ?>
