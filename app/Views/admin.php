<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    /* Enable horizontal scroll seperti Excel */
    #wrapper #content-wrapper {
        overflow-x: auto !important;
    }

    .admin-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .admin-table-wrap table {
        min-width: 1800px;
        margin-bottom: 0;
        border-collapse: collapse;
    }

    .admin-table-wrap th,
    .admin-table-wrap td {
        white-space: nowrap;
        padding: 8px;
        border: 1px solid #dee2e6;
    }

    .admin-table-wrap .section-header {
        background: blue !important;
        color: white !important;
        font-weight: bold;
    }

    .admin-table-wrap tr:nth-child(even) {
        background-color: #f8f9fc;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabel PPIC & Gudang</h6>
        </div>
        <div class="card-body">
            <div class="admin-table-wrap">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="9" class="section-header text-center">PPIC</th>
                            <th colspan="4" class="section-header text-center">Gudang</th>
                        </tr>
                        <tr class="table-light">
                            <!-- PPIC -->
                            <th>Jam</th>
                            <th>No SPK</th>
                            <th>Nama Mesin</th>
                            <th>Nama Produk</th>
                            <th>Shift</th>
                            <th>Operator</th>
                            <th>Target</th>
                            <th>Revisi</th>
                            <th>Tanggal</th>
                            <!-- Gudang -->
                            <th>No SPK</th>
                            <th>Bahan Baku</th>
                            <th>Box</th>
                            <th>Karung</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $p) : ?>
                            <tr>
                                <!-- PPIC -->
                                <td><?= $p['jam'] ?? '-'; ?></td>
                                <td><?= $p['no_spk'] ?? '-'; ?></td>
                                <td><?= $p['nama_mesin'] ?? '-'; ?></td>
                                <td><?= $p['nama_produk'] ?? '-'; ?></td>
                                <td><?= $p['shif'] ?? $p['shift'] ?? '-'; ?></td>
                                <td><?= $p['operator'] ?? '-'; ?></td>
                                <td><?= $p['targett'] ?? '-'; ?></td>
                                <td class="text-danger"><?= $p['revisi'] ?? '-'; ?></td>
                                <td>
                                    <?php
                                    if (!empty($p['tanggal'])) {
                                        setlocale(LC_TIME, 'id_ID.UTF-8');
                                        echo strftime('%e %B %Y', strtotime($p['tanggal']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($p['tanggal'])) {
                                        setlocale(LC_TIME, 'id_ID.UTF-8');
                                        echo strftime('%e %B %Y', strtotime($p['tanggal']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <!-- Gudang -->
                                <td><?= $p['no_spk'] ?? '-'; ?></td>
                                <td><?= $p['bahan_baku'] ?? '-'; ?></td>
                                <td><?= $p['box'] ?? '-'; ?></td>
                                <td><?= $p['karung'] ?? '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?= $this->endSection() ?>