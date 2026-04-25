<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-secondary">Tempat Sampah Produksi</h6>
            <a href="<?= base_url('plant_produksi') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-center">
                            <th>Tanggal</th>
                            <th>No. SPK</th>
                            <th>Mesin</th>
                            <th>Produk</th>
                            <th>Dihapus Pada</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($produksi)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data di tempat sampah.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($produksi as $p): ?>
                        <tr>
                            <td><?= $p['tanggal'] ?></td>
                            <td><?= $p['nomor_spk'] ?></td>
                            <td><?= $p['nama_mesin'] ?></td>
                            <td><?= $p['nama_produksi'] ?></td>
                            <td><?= $p['deleted_at'] ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('plant_produksi/restore/' . $p['id']) ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-undo"></i> Pulihkan
                                </a>
                                <a href="<?= base_url('plant_produksi/delete_permanent/' . $p['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus permanen? Data tidak bisa dikembalikan!')">
                                    <i class="fas fa-times-circle"></i> Hapus Permanen
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
