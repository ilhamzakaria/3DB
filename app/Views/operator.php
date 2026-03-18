<style>
    .btn-delete {
        position: absolute;
        right: 10px;
        bottom: 10px;
    }
</style>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDataModal">
                                <i class="fas fa-plus"></i> Tambah Data
                            </a>
                            <a href="<?= base_url('export/dashboard') ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export
                            </a>

                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <h6 class="m-0 font-weight-bold text-primary text-center mb-3">Laporan Produksi Harian</h6>

                            <!-- dropdown -->
                            <div class="accordion" id="accordionOperator">

                                <?php if (!empty($produksi)): ?>
                                    <?php foreach ($produksi as $i => $p): ?>

                                        <div class="card mb-2">
                                            <div class="card-header small" id="heading<?= $i ?>">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-light text-left w-100" type="button" data-toggle="collapse" data-target="#collapse<?= $i ?>" aria-expanded="false">
                                                        📊 Laporan Produksi - <?= date('d F Y', strtotime($p['tanggal'])) ?> | <?= $p['nama_mesin'] ?> - <?= $p['nama_produk'] ?>
                                                    </button>
                                                </h2>
                                            </div>

                                            <!-- switch alert -->
                                            <?php if (session()->getFlashdata('success_add')): ?>
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    <i class="fas fa-check-circle"></i>
                                                    <?= session()->getFlashdata('success_add') ?>
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                </div>
                                            <?php elseif (session()->getFlashdata('success_edit')): ?>
                                                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                                                    <i class="fas fa-edit"></i>
                                                    <?= session()->getFlashdata('success_edit') ?>
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                </div>
                                            <?php elseif (session()->getFlashdata('success_delete')): ?>
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <i class="fas fa-trash"></i>
                                                    <?= session()->getFlashdata('success_delete') ?>
                                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                                </div>
                                            <?php endif; ?>
                                            <!-- end switch alert -->

                                            <div id="collapse<?= $i ?>" class="collapse" data-parent="#accordionOperator">
                                                <div class="card-body">
                                                    <div class="list-group small">

                                                        <!-- BARIS 1 -->
                                                        <div class="list-group-item small">
                                                            <div class="row text-start">
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Nama Mesin</div>
                                                                    <?= $p['nama_mesin'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">No Mesin</div>
                                                                    <?= $p['nomor_mesin'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Nama Produk</div>
                                                                    <?= $p['nama_produk'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Tanggal</div>
                                                                    <?= date('d F Y', strtotime($p['tanggal'])) ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- BARIS 2 -->
                                                        <div class="list-group-item small">
                                                            <div class="row text-start">
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Packing</div>
                                                                    <?= $p['packing'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Isi</div>
                                                                    <?= $p['isi'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">No SPK</div>
                                                                    <?= $p['no_spk'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Batch Number</div>
                                                                    <?= $p['batch_number'] ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- BARIS 3 -->
                                                        <div class="list-group-item small">
                                                            <div class="row text-start">
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Cycle Time</div>
                                                                    <?= $p['cycle_time'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Shift / Group</div>
                                                                    <?= $p['shif'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Target / Shift</div>
                                                                    <?= $p['target'] ?>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="fw-bold">Operator</div>
                                                                    <?= $p['operator'] ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <!-- JAM PRODUKSI   -->
                                                    <h6 class="text-primary mt-3 small">Jam Produksi</h6>
                                                    <div class="row ml-1 mr-1">
                                                        <table class="table table-bordered table-sm small">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th width="6%">Jam</th>
                                                                    <th>Hasil Produksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $daftarJam = [
                                                                    "06-07",
                                                                    "07-08",
                                                                    "08-09",
                                                                    "09-10",
                                                                    "10-11",
                                                                    "11-12",
                                                                    "12-13",
                                                                    "13-14"
                                                                ];

                                                                foreach ($daftarJam as $jam):

                                                                    $kolomHasil = 'hasil_produksi_' . str_replace('-', '_', $jam);

                                                                    if (!empty($p[$kolomHasil])) :
                                                                        $hasil = $p[$kolomHasil] ?? '';

                                                                        // Jika hasil produksi kosong, tampilkan tanda "-"
                                                                        $hasilTampil = ($hasil === '' || $hasil === null) ? '-' : $hasil;
                                                                ?>
                                                                        <tr>
                                                                            <td><?= $jam ?></td>
                                                                            <td><?= $p[$kolomHasil] ?></td>
                                                                        </tr>
                                                                <?php
                                                                    endif;

                                                                endforeach;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <ol class="list-group small">
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">Total bagus</div>
                                                                <?= $p['total_bagus'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">Sisa PO</div>
                                                                <?= $p['sisa_po'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">Hold</div>
                                                                <?= $p['hold'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">Gumpalan</div>
                                                                <?= $p['gumpalan'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">Total Rijek</div>
                                                                <?= $p['total_rijek'] ?>
                                                            </div>
                                                        </li>
                                                    </ol>

                                                    <!--  detail rijek -->
                                                    <h6 class="text-primary mt-3 small">Detail Rijek</h6>

                                                    <?php
                                                    $rejectFields = [
                                                        'start_up' => 'Start Up',
                                                        'karantina' => 'Karantina',
                                                        'trial' => 'Trial',
                                                        'camera' => 'Camera',
                                                        'bottom_putih' => 'Bottom Putih',
                                                        'oval' => 'Oval',
                                                        'flashing' => 'Flashing',
                                                        'short_shoot' => 'Short Shoot',
                                                        'kotor' => 'Kotor',
                                                        'beda_warna' => 'Beda Warna',
                                                        'sampling_qc' => 'Sampling QC',
                                                        'kontaminasi' => 'Kontaminasi',
                                                        'black_spot' => 'Black Spot',
                                                        'gosong' => 'Gosong',
                                                        'struktur_tdk_std' => 'Struktur Tdk Std',
                                                        'inject_poin_tdk_std' => 'Inject Point Tdk Std',
                                                        'bolong' => 'Bolong',
                                                        'bubble' => 'Bubble',
                                                        'berair' => 'Berair',
                                                        'neck_panjang' => 'Neck Panjang'
                                                    ];

                                                    $rejectIsi = [];
                                                    foreach ($rejectFields as $field => $label) {
                                                        if (!empty($p[$field])) {
                                                            $rejectIsi[$label] = $p[$field];
                                                        }
                                                    }
                                                    ?>

                                                    <?php if (!empty($rejectIsi)): ?>
                                                        <ol class="list-group small">
                                                            <li class="list-group-item d-flex flex-wrap gap-4">

                                                                <?php foreach ($rejectIsi as $label => $value): ?>
                                                                    <div class="ms-2">
                                                                        <div class="fw-bold small"><?= $label ?></div>
                                                                        <?= $value ?>
                                                                    </div>
                                                                <?php endforeach; ?>

                                                            </li>
                                                        </ol>
                                                    <?php endif; ?>

                                                    <h6 class="text-primary mt-3 small">Material</h6>
                                                    <ol class="list-group small">
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">No lot 1</div>
                                                                <?= $p['m_no_lot1'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">No lot 2</div>
                                                                <?= $p['m_no_lot2'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">No lot 3</div>
                                                                <?= $p['m_no_lot3'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">No lot 4</div>
                                                                <?= $p['m_no_lot4'] ?>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                    <ol class="list-group small mt-1">
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">Merek kode</div>
                                                                <?= $p['merek_kode'] ?>
                                                            </div>
                                                            <div class="ms-2 me-auto">
                                                                <div class="fw-bold small">Pemakaian</div>
                                                                <?= $p['m_pemakaian'] ?>
                                                            </div>
                                                        </li>
                                                    </ol>

                                                    <h6 class="text-primary mt-3 small">Colorant</h6>
                                                    <table class="table table-bordered table-sm small">
                                                        <tr>
                                                            <td>Kode</td>
                                                            <td>:</td>
                                                            <td><?= $p['c_kode'] ?></td>
                                                            <td>Pemakaian</td>
                                                            <td>:</td>
                                                            <td><?= $p['c_pemakaian'] ?></td>
                                                            <td>No lot</td>
                                                            <td>:</td>
                                                            <td><?= $p['c_no_lot'] ?></td>
                                                        </tr>
                                                    </table>

                                                    <h6 class="text-primary mt-3 small">Packaging</h6>
                                                    <table class="table table-bordered table-sm small">
                                                        <tr>
                                                            <td width="14%">Box/karung/nicktainer</td>
                                                            <td>:</td>
                                                            <td><?= $p['box'] ?> pcs</td>
                                                            <td>Plastik</td>
                                                            <td>:</td>
                                                            <td><?= $p['plastik'] ?> pcs</td>
                                                        </tr>
                                                    </table>

                                                    <h6 class="text-primary mt-3 small">Catatan</h6>
                                                    <table class="table table-bordered table-sm small">
                                                        <tr>
                                                            <td colspan="3"><?= $p['catatan'] ?></td>
                                                        </tr>
                                                    </table>

                                                    <div class="modal fade" id="editModal<?= $p['id'] ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">

                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Data Produksi</h5>
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <form action="<?= base_url('operator/update/' . $p['id']) ?>" method="post">
                                                                    <?= csrf_field() ?>

                                                                    <div class="modal-body">

                                                                        <div class="accordion" id="accordionProduksi">

                                                                            <!-- ================= HEADER PRODUKSI update ================= -->
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header">
                                                                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#headerProduksi">
                                                                                        Data Utama
                                                                                    </button>
                                                                                </h2>

                                                                                <div id="headerProduksi" class="accordion-collapse collapse show" data-bs-parent="#accordionProduksi">
                                                                                    <div class="accordion-body">

                                                                                        <div class="form-row">
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Nama Mesin</label>
                                                                                                <input type="text" class="form-control" name="nama_mesin" value="<?= $p['nama_mesin'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>No. SPK</label>
                                                                                                <input type="text" class="form-control" name="no_spk" value="<?= $p['no_spk'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Tanggal</label>
                                                                                                <input type="date" class="form-control" name="tanggal" value="<?= $p['tanggal'] ?>">
                                                                                            </div>

                                                                                        </div>

                                                                                        <div class="form-row">
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Nama Produk</label>
                                                                                                <input type="text" class="form-control" name="nama_produk" value="<?= $p['nama_produk'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Batch Number</label>
                                                                                                <input type="text" class="form-control" name="batch_number" value="<?= $p['batch_number'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Shift / Group</label>
                                                                                                <input type="text" class="form-control" name="shift" value="<?= $p['shif'] ?>">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="form-row">
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Nomor Mesin</label>
                                                                                                <input type="text" class="form-control" name="nomor_mesin" value="<?= $p['nomor_mesin'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Packing</label>
                                                                                                <input type="text" class="form-control" name="packing" value="<?= $p['packing'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Isi</label>
                                                                                                <input type="text" class="form-control" name="isi" value="<?= $p['isi'] ?>">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="form-row">
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Cycle Time</label>
                                                                                                <input type="text" class="form-control" name="cycle_time" value="<?= $p['cycle_time'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Target</label>
                                                                                                <input type="text" class="form-control" name="target" value="<?= $p['target'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Operator</label>
                                                                                                <input type="text" class="form-control" name="operator" value="<?= $p['operator'] ?>">
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- ================= JAM PRODUKSI update ================= -->
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header">
                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#jamProduksi">
                                                                                        Jam Produksi
                                                                                    </button>
                                                                                </h2>

                                                                                <div id="jamProduksi" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row">
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="06-07" value="06-07">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_06_07" value="<?= $p['hasil_produksi_06_07'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="07-08" value="07-08">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_07_08" value="<?= $p['hasil_produksi_07_08'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="08-09" value="08-09">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_08_09" value="<?= $p['hasil_produksi_08_09'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="09-10" value="09-10">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_09_10" value="<?= $p['hasil_produksi_09_10'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="10-11" value="10-11">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_10_11" value="<?= $p['hasil_produksi_10_11'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="11-12" value="11-12">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_11_12" value="<?= $p['hasil_produksi_11_12'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="12-13" value="12-13">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_12_13" value="<?= $p['hasil_produksi_12_13'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Jam</label>
                                                                                                <input type="text" class="form-control" name="13-14" value="13-14">
                                                                                            </div>
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Hasil Produksi</label>
                                                                                                <input type="text" class="form-control" name="hasil_produksi_13_14" value="<?= $p['hasil_produksi_13_14'] ?>">
                                                                                            </div>


                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Total Bagus</label>
                                                                                                <input type="text" class="form-control" name="total_bagus" value="<?= $p['total_bagus'] ?>">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="row">
                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Sisa PO</label>
                                                                                                <input type="text" class="form-control" name="sisa_po" value="<?= $p['sisa_po'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Hold</label>
                                                                                                <input type="text" class="form-control" name="hold" value="<?= $p['hold'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Gumpalan</label>
                                                                                                <input type="text" class="form-control" name="gumpalan" value="<?= $p['gumpalan'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Total Rijek</label>
                                                                                                <input type="text" class="form-control" name="total_rijek" value="<?= $p['total_rijek'] ?>">
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>

                                                                            </div>

                                                                            <!-- ================= DETAIL REJECT update ================= -->
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header">
                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rejectProduksi">
                                                                                        Detail Reject
                                                                                    </button>
                                                                                </h2>

                                                                                <div id="rejectProduksi" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                                                                    <div class="accordion-body">

                                                                                        <div class="row">

                                                                                            <?php
                                                                                            $reject = [
                                                                                                'start_up',
                                                                                                'karantina',
                                                                                                'trial',
                                                                                                'camera',
                                                                                                'bottom_putih',
                                                                                                'oval',
                                                                                                'flashing',
                                                                                                'short_shoot',
                                                                                                'kotor',
                                                                                                'beda_warna',
                                                                                                'sampling_qc',
                                                                                                'kontaminasi',
                                                                                                'black_spot',
                                                                                                'gosong',
                                                                                                'struktur_tdk_std',
                                                                                                'inject_poin_tdk_std',
                                                                                                'bolong',
                                                                                                'bubble',
                                                                                                'berair',
                                                                                                'neck_panjang'
                                                                                            ];
                                                                                            foreach ($reject as $r): ?>
                                                                                                <div class="col-md-3 mb-2">
                                                                                                    <label><?= ucwords(str_replace('_', ' ', $r)) ?></label>
                                                                                                    <input type="number" class="form-control" name="<?= $r ?>" value="<?= $p[$r] ?>">
                                                                                                </div>
                                                                                            <?php endforeach; ?>

                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- ================= MATERIAL ================= -->
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header">
                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#material">
                                                                                        Material
                                                                                    </button>
                                                                                </h2>

                                                                                <div id="material" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                                                                    <div class="accordion-body">

                                                                                        <div class="form-row">
                                                                                            <div class="form-group col-md-6">
                                                                                                <label>Merek Kode</label>
                                                                                                <input type="text" class="form-control" name="merek_kode" value="<?= $p['merek_kode'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-6">
                                                                                                <label>Pemakaian</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="number" class="form-control" name="m_pemakaian" value="<?= $p['m_pemakaian'] ?>">
                                                                                                    <span class="input-group-text">kg</span>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Nomor Lot 1</label>
                                                                                                <input type="text" class="form-control" name="m_no_lot1" value="<?= $p['m_no_lot1'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Nomor Lot 2</label>
                                                                                                <input type="text" class="form-control" name="m_no_lot2" value="<?= $p['m_no_lot2'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Nomor Lot 3</label>
                                                                                                <input type="text" class="form-control" name="m_no_lot3" value="<?= $p['m_no_lot3'] ?>">
                                                                                            </div>
                                                                                            <div class="form-group col-md-3">
                                                                                                <label>Nomor Lot 4</label>
                                                                                                <input type="text" class="form-control" name="m_no_lot4" value="<?= $p['m_no_lot4'] ?>">
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- ================= COLORANT ================= -->
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header">
                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colorant">
                                                                                        Colorant
                                                                                    </button>
                                                                                </h2>

                                                                                <div id="colorant" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                                                                    <div class="accordion-body">

                                                                                        <div class="form-row">
                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Kode</label>
                                                                                                <input type="text" class="form-control" name="c_kode" value="<?= $p['c_kode'] ?>">
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Pemakaian</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="number" class="form-control" name="c_pemakaian" value="<?= $p['c_pemakaian'] ?>">
                                                                                                    <span class="input-group-text">kg</span>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="form-group col-md-4">
                                                                                                <label>Nomor Lot</label>
                                                                                                <input type="text" class="form-control" name="c_no_lot" value="<?= $p['c_no_lot'] ?>">
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- ================= PACKAGING ================= -->
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header">
                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#packaging">
                                                                                        Packaging
                                                                                    </button>
                                                                                </h2>

                                                                                <div id="packaging" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                                                                    <div class="accordion-body">

                                                                                        <div class="form-row">
                                                                                            <div class="form-group col-md-6">
                                                                                                <label>Box / Karung / Nicktainer</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="number" class="form-control" name="box" value="<?= $p['box'] ?>">
                                                                                                    <span class="input-group-text">pcs</span>
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="form-group col-md-6">
                                                                                                <label>Plastik</label>
                                                                                                <div class="input-group">
                                                                                                    <input type="number" class="form-control" name="plastik" value="<?= $p['plastik'] ?>">
                                                                                                    <span class="input-group-text">pcs</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- ================= CATATAN ================= -->
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header">
                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#catatan">
                                                                                        Catatan
                                                                                    </button>
                                                                                </h2>

                                                                                <div id="catatan" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                                                                    <div class="accordion-body">
                                                                                        <input type="text" class="form-control" name="catatan" value="<?= $p['catatan'] ?>" rows="4"></input>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                                                        <button class="btn btn-primary" type="submit">Simpan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning text-center">Belum ada data</div>
                                <?php endif; ?>

                            </div>


                            <!-- end dropdown -->

                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Modal Tambah Data -->
        <div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="addDataModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary acordion-header" id="addDataModalLabel">Tambah Data Produksi</h5>
                        <button class="close acordion-button" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <form method="POST" action="<?= base_url('operator/tambahData') ?>">
                        <?= csrf_field() ?>

                        <div class="modal-body">

                            <div class="accordion" id="accordionProduksi">

                                <!-- ================= TAMBAH HEADER PRODUKSI ================= -->
                                <!-- <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#headerProduksi">
                                            Data Utama
                                        </button>
                                    </h2>

                                    <div id="headerProduksi" class="accordion-collapse collapse show" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Nama Mesin</label>
                                                    <input type="text" class="form-control" name="nama_mesin">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>No. SPK</label>
                                                    <input type="text" class="form-control" name="no_spk">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal">
                                                </div>

                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Nama Produk</label>
                                                    <input type="text" class="form-control" name="nama_produk">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Batch Number</label>
                                                    <input type="text" class="form-control" name="batch_number">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Shift / Group</label>
                                                    <input type="text" class="form-control" name="shift">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Nomor Mesin</label>
                                                    <input type="text" class="form-control" name="nomor_mesin">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Packing</label>
                                                    <input type="text" class="form-control" name="packing">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Isi</label>
                                                    <input type="text" class="form-control" name="isi">
                                                </div>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Cycle Time</label>
                                                    <input type="text" class="form-control" name="cycle_time">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Target</label>
                                                    <input type="text" class="form-control" name="target">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Operator</label>
                                                    <input type="text" class="form-control" name="operator">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div> -->

                                <!-- ================= TAMBAH JAM PRODUKSI ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#jamProduksi">
                                            Jam Produksi
                                        </button>
                                    </h2>

                                    <div id="jamProduksi" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">

                                            <div id="containerJam"></div>

                                            <button type="button" class="btn btn-sm btn-primary mt-2" onclick="tambahJam()">
                                                + Tambah Jam
                                            </button>

                                        </div>

                                    </div>
                                </div>

                                <!-- ================= TAMBAH DETAIL REJECT ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rejectProduksi">
                                            Detail Reject
                                        </button>
                                    </h2>

                                    <div id="rejectProduksi" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">

                                            <div id="containerReject"></div>

                                            <button type="button" class="btn btn-sm btn-primary mt-2" onclick="tambahBaris()">
                                                + Tambah Reject
                                            </button>

                                        </div>
                                    </div>
                                </div>

                                <!-- ================= TAMBAH MATERIAL ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#material">
                                            Material
                                        </button>
                                    </h2>

                                    <div id="material" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Merek Kode</label>
                                                    <input type="text" class="form-control" name="merek_kode">
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Pemakaian</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="m_pemakaian">
                                                        <span class="input-group-text">kg</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label>Nomor Lot 1</label>
                                                    <input type="text" class="form-control" name="m_no_lot1">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Nomor Lot 2</label>
                                                    <input type="text" class="form-control" name="m_no_lot2">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Nomor Lot 3</label>
                                                    <input type="text" class="form-control" name="m_no_lot3">
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Nomor Lot 4</label>
                                                    <input type="text" class="form-control" name="m_no_lot4">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- ================= TAMBAH COLORANT ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colorant">
                                            Colorant
                                        </button>
                                    </h2>

                                    <div id="colorant" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>Kode</label>
                                                    <input type="text" class="form-control" name="c_kode">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Pemakaian</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="c_pemakaian">
                                                        <span class="input-group-text">kg</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Nomor Lot</label>
                                                    <input type="text" class="form-control" name="c_no_lot">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- ================= TAMBAH PACKAGING ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#packaging">
                                            Packaging
                                        </button>
                                    </h2>

                                    <div id="packaging" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label>Box / Karung / Nicktainer</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="box">
                                                        <span class="input-group-text">pcs</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Plastik</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="plastik">
                                                        <span class="input-group-text">pcs</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- ================= TAMBAH CATATAN ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#catatan">
                                            Catatan
                                        </button>
                                    </h2>

                                    <div id="catatan" class="accordion-collapse collapse" data-bs-parent="#accordionProduksi">
                                        <div class="accordion-body">
                                            <textarea class="form-control" name="catatan" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<!-- <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="login.html">Logout</a>
            </div>
        </div>
    </div>
</div> -->

<!-- modal delete -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="formDelete" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">

                <div class="modal-body">
                    <p>Yakin ingin menghapus data ini?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end modal delete -->


<!-- js modal delete -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalDelete = document.getElementById('modalDelete');

        modalDelete.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            const form = document.getElementById('formDelete');
            form.action = "<?= base_url('operator/delete/') ?>" + id;
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>

<!-- js detail rijek -->
<script>
    let daftarReject = [
        'start_up', 'karantina', 'trial', 'camera', 'button_putih',
        'oval', 'flashing', 'short_shoot', 'kotor', 'beda_warna',
        'sampling_qc', 'kontaminasi', 'black_spot', 'gosong',
        'struktur_tdk_std', 'inject_poin_tdk_std',
        'bolong', 'bubble', 'berair', 'neck_panjang'
    ];

    function tambahBaris() {

        let html = `
        <div class="row mb-2 align-items-center" id="row_${Date.now()}">
            <div class="col-md-5">
                <select class="form-select" name="jenis_reject[]">
                    <option value="">-- Pilih Reject --</option>
                    ${daftarReject.map(r => 
                        `<option value="${r}">${r.replaceAll('_',' ').toUpperCase()}</option>`
                    ).join('')}
                </select>
            </div>

            <div class="col-md-4">
                <input type="number" class="form-control" name="jumlah_reject[]" placeholder="Jumlah">
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-danger w-100" onclick="this.closest('.row').remove()">
                    Hapus
                </button>
            </div>
        </div>
    `;

        document.getElementById("containerReject")
            .insertAdjacentHTML("beforeend", html);
    }

    // otomatis tambah 1 baris saat pertama buka
    tambahBaris();
</script>

<!-- js detail jam produksi -->
<script>
    let daftarJam = [
        "06-07", "07-08", "08-09", "09-10",
        "10-11", "11-12", "12-13", "13-14"
    ];

    function tambahJam() {

        let html = `
        <div class="row mb-2 align-items-center">
            <div class="col-md-4">
                <select class="form-select" name="jam[]">
                    <option value="">-- Pilih Jam --</option>
                    ${daftarJam.map(j => `<option value="${j}">${j}</option>`).join('')}
                </select>
            </div>

            <div class="col-md-4">
                <input type="number" class="form-control" name="hasil_produksi[]" placeholder="Hasil Produksi">
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-danger w-100" onclick="this.closest('.row').remove()">
                    Hapus
                </button>
            </div>
        </div>
    `;

        document.getElementById("containerJam")
            .insertAdjacentHTML("beforeend", html);
    }

    // otomatis tampil 1 baris saat buka
    tambahJam();
</script>

<!-- form submits normally (CSRF handled by <?= "csrf_field()" ?>) -->



<?= $this->endSection() ?>