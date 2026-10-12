<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs me-2"></i>Setting Mesin
        </h1>
        <a href="<?= base_url('settings-mesin/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Data
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <?php if (!empty($mesin)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>User ID</th>
                            <th>Produk</th>
                            <th>Mesin</th>
                            <th>Tipe</th>
                            <th>No. Mesin</th>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($mesin as $item): ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark"><?= $no++ ?></span>
                            </td>
                            <td>
                                <small class="text-muted"><?= esc($item['id_user']) ?></small>
                            </td>
                            <td><?= esc($item['product']) ?></td>
                            <td><?= esc($item['machine']) ?></td>
                            <td><?= esc($item['type']) ?></td>
                            <td>
                                <code class="text-primary"><?= esc($item['no_mc']) ?></code>
                            </td>
                            <td>
                                <span class="badge bg-info text-white">
                                    <?= date('d/m/Y', strtotime($item['tanggal'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">Shift <?= $item['shift'] ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= base_url('settings-mesin/edit/' . $item['id']) ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?= base_url('settings-mesin/delete/' . $item['id']) ?>" 
                                          method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Yakin hapus data ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <small class="text-muted">
                    Total: <?= count($mesin) ?> data ditampilkan
                </small>
                <nav aria-label="Page navigation">
                    <?php if ($pager): ?>
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                    <?php endif; ?>
                </nav>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox text-gray-300" style="font-size: 3rem;"></i>
                <p class="text-gray-500 mt-3">Belum ada data Setting Mesin</p>
                <a href="<?= base_url('settings-mesin/create') ?>" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Tambah Data Pertama
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
