<?= $this->extend('layout/main') ?>


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
<?= $this->section('content') ?>


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
<?php
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
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
        <?php endif; ?>


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
        <div class="table-responsive">
            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                <thead>
                    <tr class="font-weight-bold">
                        <th>Tanggal</th>
                        <th>Produk Cap Galon</th>
                        <th>Brand</th>
                        <th>Warna</th>
                        <th>Packaging</th>
                        <th>Stock</th>
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
                                <td><?= esc($item['produk_cap_galon'] ?? '-') ?></td>
                                <td><?= esc($item['brand'] ?? '-') ?></td>
                                <td><?= esc($item['warna'] ?? '-') ?></td>
                                <td><?= esc($item['packaging'] ?? '-') ?></td>
                                <td><?= esc($item['stock'] ?? '-') ?></td>
                                <td><?= esc($item['no_spk'] ?? '-') ?></td>
                                <td><?= esc($item['shif'] ?? '-') ?></td>
                                <td>
                                    <?php if (in_array(session()->get('role'), ['gudang', 'admin'], true)) : ?>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-warning btn-sm" onclick="setEditModal(<?= htmlspecialchars(json_encode($item)) ?>)" data-toggle="modal" data-target="#editDataModal"><i class="fas fa-edit"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm ml-1" onclick="setDeleteModal(<?= $item['id'] ?>, '<?= esc($item['produk_cap_galon']) ?>')" data-toggle="modal" data-target="#modalDelete"><i class="fas fa-trash"></i></button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="9">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Data <?= $title ?></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <form action="<?= current_url() . '/tambahData' ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal</label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>No SPK</label><input type="number" class="form-control" name="no_spk"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Produk Cap Galon</label><input type="text" class="form-control" name="produk_cap_galon" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Brand</label><input type="text" class="form-control" name="brand"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Packaging</label><input type="text" class="form-control" name="packaging"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Stock</label><input type="text" class="form-control" name="stock"></div></div>
                    </div>
                    <div class="form-group"><label>Shift</label><input type="text" class="form-control" name="shif"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Data <?= $title ?></h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?><input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Tanggal</label><input type="date" class="form-control" name="tanggal" id="edit_tanggal" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>No SPK</label><input type="number" class="form-control" name="no_spk" id="edit_spk"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Produk Cap Galon</label><input type="text" class="form-control" name="produk_cap_galon" id="edit_produk" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Brand</label><input type="text" class="form-control" name="brand" id="edit_brand"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna" id="edit_warna"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Packaging</label><input type="text" class="form-control" name="packaging" id="edit_packaging"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Stock</label><input type="text" class="form-control" name="stock" id="edit_stock"></div></div>
                    </div>
                    <div class="form-group"><label>Shift</label><input type="text" class="form-control" name="shif" id="edit_shif"></div>
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
                <div class="modal-body text-center"><p id="delete_message"></p></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger btn-sm">Hapus</button></div>
            </form>
        </div>
    </div>
</div>

<script>
    function setEditModal(data) {
        $('#edit_id').val(data.id);
        $('#edit_tanggal').val(data.tanggal);
        $('#edit_spk').val(data.no_spk);
        $('#edit_produk').val(data.produk_cap_galon);
        $('#edit_brand').val(data.brand);
        $('#edit_warna').val(data.warna);
        $('#edit_packaging').val(data.packaging);
        $('#edit_stock').val(data.stock);
        $('#edit_shif').val(data.shif);
        $('#formEdit').attr('action', '<?= current_url() ?>/update/' + data.id);
    }
    function setDeleteModal(id, nama) {
        $('#delete_id').val(id);
        $('#delete_message').text('Hapus data "' + nama + '"?');
        $('#formDelete').attr('action', '<?= current_url() ?>/delete/' + id);
    }
</script>

<?= $this->endSection() ?>

