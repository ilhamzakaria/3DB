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
    'supplier' => 'Gilingan Supplier',
    'produksi' => 'Gilingan Produksi',
];

$formatDateTime = static function ($value): string {
    $timestamp = $value ? strtotime((string) $value) : false;
    return $timestamp ? date('d-m-Y H:i', $timestamp) : '-';
};

$formatDate = static function ($value): string {
    $timestamp = $value ? strtotime((string) $value) : false;
    return $timestamp ? date('d-m-Y', $timestamp) : '-';
};
?>

<style>
    .filter-sidebar {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100vh;
        background: #fff;
        z-index: 1100;
        box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .filter-sidebar.active {
        right: 0;
    }
    .filter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0,0,0,0.5);
        z-index: 1090;
        display: none;
        backdrop-filter: blur(2px);
    }
    .filter-overlay.active {
        display: block;
    }
    .filter-header {
        padding: 1.25rem;
        border-bottom: 1px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fc;
    }
    .filter-body {
        padding: 1.5rem;
        flex-grow: 1;
        overflow-y: auto;
    }
    .filter-footer {
        padding: 1.25rem;
        border-top: 1px solid #e3e6f0;
        background: #f8f9fc;
        display: flex;
        gap: 10px;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        background: #eaecf4;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
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

            <form method="get" action="<?= base_url('gudang/gilingan_galon') ?>" class="d-flex align-items-center mb-0 mt-2 mt-md-0">
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
                <a href="<?= base_url('gudang/gilingan_galon') ?>" class="text-danger small ml-2"><i class="fas fa-times"></i> Reset</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                <thead>
                    <tr class="font-weight-bold">
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Item</th>
                        <th>Sumber</th>
                        <th>Status</th>
                        <th>Kode</th>
                        <th>Jumlah</th>
                        <th>Lot</th>
                        <th>SPK</th>
                        <th>Shift</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data_list)) : ?>
                        <?php foreach ($data_list as $item) : ?>
                            <tr>
                                <td><?= esc($formatDate($item['tanggal'])) ?></td>
                                <td><?= esc($item['produk'] ?? '-') ?></td>
                                <td><?= esc($item['nama_gilingan'] ?? '-') ?></td>
                                <td><span class="badge badge-light border"><?= esc($sumberLabels[$item['sumber_gilingan']] ?? $item['sumber_gilingan']) ?></span></td>
                                <td><span class="badge <?= esc($statusBadges[$item['status']] ?? 'badge-secondary') ?>"><?= esc($statusLabels[$item['status']] ?? $item['status']) ?></span></td>
                                <td><?= esc($item['kode'] ?? '-') ?></td>
                                <td><?= esc($item['jumlah'] ?? '-') ?></td>
                                <td><?= esc($item['nomor_lot'] ?? '-') ?></td>
                                <td><?= esc($item['no_spk'] ?? '-') ?></td>
                                <td><?= esc($item['shif'] ?? '-') ?></td>
                                <td>
                                    <?php if (in_array(session()->get('role'), ['gudang', 'admin'], true)) : ?>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-warning btn-sm" onclick="setEditModal(<?= htmlspecialchars(json_encode($item)) ?>)" data-toggle="modal" data-target="#editDataModal"><i class="fas fa-edit"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm ml-1" onclick="setDeleteModal(<?= $item['id'] ?>, '<?= esc($item['nama_gilingan']) ?>')" data-toggle="modal" data-target="#modalDelete"><i class="fas fa-trash"></i></button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="11">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Gilingan Galon</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('gudang/gilingan_galon/tambahData') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal</label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>No SPK</label><input type="number" class="form-control" name="no_spk"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Produk</label><input type="text" class="form-control" name="produk"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nama Item</label><input type="text" class="form-control" name="nama_gilingan" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sumber</label>
                                <select class="form-control" name="sumber_gilingan">
                                    <?php foreach ($sumber_options as $opt) : ?><option value="<?= $opt ?>"><?= $sumberLabels[$opt] ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <?php foreach ($status_options as $opt) : ?><option value="<?= $opt ?>" <?= $opt == 'bahan_masuk' ? 'selected' : '' ?>><?= $statusLabels[$opt] ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Kode</label><input type="text" class="form-control" name="kode"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Jumlah</label><input type="text" class="form-control" name="jumlah"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Shift</label><input type="text" class="form-control" name="shif"></div></div>
                    </div>
                    <div class="form-group"><label>Nomor Lot</label><input type="text" class="form-control" name="nomor_lot"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Gilingan Galon</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEdit" action="<?= base_url('gudang/gilingan_galon/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal</label><input type="date" class="form-control" name="tanggal" id="edit_tanggal" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>No SPK</label><input type="number" class="form-control" name="no_spk" id="edit_spk"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Produk</label><input type="text" class="form-control" name="produk" id="edit_produk"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Nama Item</label><input type="text" class="form-control" name="nama_gilingan" id="edit_nama" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sumber</label>
                                <select class="form-control" name="sumber_gilingan" id="edit_sumber">
                                    <?php foreach ($sumber_options as $opt) : ?><option value="<?= $opt ?>"><?= $sumberLabels[$opt] ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="edit_status">
                                    <?php foreach ($status_options as $opt) : ?><option value="<?= $opt ?>"><?= $statusLabels[$opt] ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Kode</label><input type="text" class="form-control" name="kode" id="edit_kode"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Jumlah</label><input type="text" class="form-control" name="jumlah" id="edit_jumlah"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Shift</label><input type="text" class="form-control" name="shif" id="edit_shif"></div></div>
                    </div>
                    <div class="form-group"><label>Nomor Lot</label><input type="text" class="form-control" name="nomor_lot" id="edit_lot"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form id="formDelete" method="post">
                <?= csrf_field() ?><input type="hidden" name="id" id="delete_id">
                <div class="modal-body"><p id="delete_message"></p></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger btn-sm">Hapus</button></div>
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
    <form method="get" action="<?= base_url('gudang/gilingan_galon') ?>">
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
                    <?php foreach ($sumber_options as $opt) : ?>
                        <option value="<?= esc($opt) ?>" <?= ($sumber_filter === $opt) ? 'selected' : '' ?>><?= esc($sumberLabels[$opt] ?? ucfirst($opt)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="filter-footer">
            <a href="<?= base_url('gudang/gilingan_galon') ?>" class="btn btn-secondary flex-fill">Reset</a>
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
    
    function setEditModal(data) {
        $('#edit_id').val(data.id);
        $('#edit_tanggal').val(data.tanggal);
        $('#edit_spk').val(data.no_spk);
        $('#edit_produk').val(data.produk);
        $('#edit_nama').val(data.nama_gilingan);
        $('#edit_sumber').val(data.sumber_gilingan);
        $('#edit_status').val(data.status);
        $('#edit_kode').val(data.kode);
        $('#edit_jumlah').val(data.jumlah);
        $('#edit_shif').val(data.shif);
        $('#edit_lot').val(data.nomor_lot);
        $('#formEdit').attr('action', '<?= base_url('gudang/gilingan_galon/update') ?>/' + data.id);
    }
    function setDeleteModal(id, nama) {
        $('#delete_id').val(id);
        $('#delete_message').text('Hapus data "' + nama + '"?');
        $('#formDelete').attr('action', '<?= base_url('gudang/gilingan_galon/delete') ?>/' + id);
    }
</script>

<?= $this->endSection() ?>
