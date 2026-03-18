<style>
    .btn-delete {
        position: absolute;
        right: 10px;
        bottom: 10px;
    }

    .table-responsive {
        position: relative;
    }

    /* Membuat kolom pertama (Aksi) tetap di kiri saat scroll */
    .table th:first-child,
    .table td:first-child {
        position: sticky;
        left: 0;
        background-color: #fff;
        /* Warna background agar tidak transparan saat ditumpuk */
        z-index: 10;
        border-right: 2px solid #e3e6f0;
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
                        <?php if (session()->get('role') == 'produksi') { ?>
                            <h6 class="m-0 font-weight-bold text-primary">
                                <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDataModal">
                                    <i class="fas fa-plus"></i> Tambah Data
                                </a>
                                <a href="<?= base_url('export/dashboard') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel"></i> Export
                                </a>

                            </h6>
                        <?php } else { ?>
                            <h6 class="m-0 font-weight-bold text-primary">
                                <a href="<?= base_url('export/dashboard') ?>" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel"></i> Export
                                </a>
                            </h6>
                        <?php } ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <h6 class="m-0 font-weight-bold text-primary text-center mb-3">Laporan Produksi Harian</h6>
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th class="small fw-bold">Jam</th>
                                        <th class="small fw-bold">Hasil Produksi</th>
                                        <th class="small fw-bold">No. SPK</th>
                                        <th class="small fw-bold">Nama Mesin</th>
                                        <th class="small fw-bold">Nama Produk</th>
                                        <th class="small fw-bold">Shift</th>
                                        <th class="small fw-bold">Operator</th>
                                        <th class="small fw-bold">Tanggal</th>
                                        <th class="small fw-bold" width="6%">Action</th>
                                    </tr>
                                </thead>

                                <?php
                                if (empty($produksi)) {
                                    echo '<tr><td colspan="12" class="text-center">Tidak ada data produksi untuk tanggal ini.</td></tr>';
                                }
                                ?>

                                <tbody class="text-center">

                                    <?php
                                    $shiftRanges = [
                                        '1' => '07-14',
                                        '2' => '14-22',
                                        '3' => '22-06',
                                    ];

                                    $currentDate = '';
                                    $currentShift = '';

                                    foreach ($produksi as $p):
                                    ?>

                                        <?php if ($currentDate != $p['tanggal']): ?>
                                            <tr class="table-secondary">
                                                <td colspan="11">
                                                    <strong>
                                                        <?php
                                                        if (!empty($p['tanggal'])) {
                                                            $date = new DateTime($p['tanggal']);
                                                            $formatter = new IntlDateFormatter(
                                                                'id_ID',
                                                                IntlDateFormatter::LONG, // Format: 16 Maret 2026
                                                                IntlDateFormatter::NONE  // Tanpa waktu (Jam)
                                                            );
                                                            echo esc($formatter->format($date));
                                                        }
                                                        ?>
                                                    </strong>
                                                </td>
                                            </tr>

                                            <?php
                                            $currentDate = $p['tanggal'];
                                            $currentShift = ''; // reset shift jika tanggal berubah
                                            ?>
                                        <?php endif; ?>

                                        <?php if ($currentShift != $p['shif']): ?>
                                            <tr class="table-primary">
                                                <td colspan="10">
                                                    <strong class="small fw-bold">
                                                        SHIFT <?= $p['shif']; ?>
                                                        <?= isset($shiftRanges[$p['shif']]) ? '(' . $shiftRanges[$p['shif']] . ')' : '' ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <?php $currentShift = $p['shif']; ?>
                                        <?php endif; ?>

                                        <tr>
                                            <td><?= !empty($p['jam']) ? esc($p['jam']) : '-' ?></td>
                                            <td><?= !empty($p['hasil_produksi']) ? esc($p['hasil_produksi']) : '-' ?></td>
                                            <td><?= !empty($p['no_spk']) ? esc($p['no_spk']) : '-' ?></td>
                                            <td><?= !empty($p['nama_mesin']) ? esc($p['nama_mesin']) : '-' ?></td>
                                            <td><?= !empty($p['nama_produk']) ? esc($p['nama_produk']) : '-' ?></td>
                                            <td><?= !empty($p['shif']) ? esc($p['shif']) : '-' ?></td>
                                            <td><?= !empty($p['operator']) ? esc($p['operator']) : '-' ?></td>

                                            <td width="10%">
                                                <?php
                                                if (isset($p['tanggal'])) {
                                                    setlocale(LC_TIME, 'id_ID.UTF-8');
                                                    echo strftime('%e %B %Y', strtotime($p['tanggal']));
                                                }
                                                ?>
                                            </td>

                                            <td class="text-center small" width="7%">
                                                <?php if (session()->get('role') == 'produksi') { ?>
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="#"
                                                            class="btn btn-warning btn-sm btnEdit"
                                                            data-id="<?= $p['id'] ?>"
                                                            data-jam="<?= isset($p['jam']) ? $p['jam'] : '-' ?>"
                                                            data-hasil="<?= isset($p['hasil_produksi']) ? $p['hasil_produksi'] : '' ?>"
                                                            data-no_spk="<?= $p['no_spk'] ?>"
                                                            data-nama_mesin="<?= $p['nama_mesin'] ?>"
                                                            data-nama_produk="<?= $p['nama_produk'] ?>"
                                                            data-shif="<?= $p['shif'] ?>"
                                                            data-operator="<?= isset($p['operator']) ? $p['operator'] : '' ?>"

                                                            data-tanggal="<?= isset($p['tanggal']) ? $p['tanggal'] : '' ?>"
                                                            data-toggle="modal"
                                                            data-bs-toggle="modal"
                                                            data-target="#modalEdit"
                                                            data-bs-target="#modalEdit">
                                                            <i class="fas fa-edit small"></i>
                                                        </a>

                                                        <a href="#"
                                                            class="btn btn-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#modalDelete"
                                                            data-id="<?= $p['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>
                            </table>

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

                    <form method="POST" action="<?= base_url('produksi/tambahData') ?>">
                        <?= csrf_field() ?>

                        <div class="modal-body">

                            <div class="accordion" id="accordionProduksi">

                                <!-- ================= TAMBAH HEADER PRODUKSI ================= -->
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
                                                    <label class="small">Jam</label>
                                                    <select class="form-control" name="jam" id="jamSelect">
                                                        <option value="">-- Pilih Jam --</option>
                                                        <?php
                                                        $listJam = [
                                                            "07-08",
                                                            "08-09",
                                                            "10-11",
                                                            "11-12",
                                                            "12-13",
                                                            "13-14", // Shift 1
                                                            "14-15",
                                                            "15-16",
                                                            "16-17",
                                                            "17-18",
                                                            "18-19",
                                                            "19-20",
                                                            "20-21",
                                                            "21-22", // Shift 2
                                                            "22-23",
                                                            "23-00",
                                                            "00-01",
                                                            "01-02",
                                                            "03-04",
                                                            "04-05",
                                                            "05-06",
                                                            "06-07"  // Shift 3
                                                        ];
                                                        foreach ($listJam as $j) :
                                                        ?>
                                                            <option value="<?= $j ?>"><?= $j ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Hasil Produksi</label>
                                                    <input type="text" class="form-control" name="hasil_produksi">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>No. SPK</label>
                                                    <input type="text" class="form-control" name="no_spk">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Nama Mesin</label>
                                                    <input type="text" class="form-control" name="nama_mesin">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Nama Produk</label>
                                                    <input type="text" class="form-control" name="nama_produk">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Shift</label>
                                                    <select class="form-control" name="shif" id="shiftInput">
                                                        <option value="">-- otomatis --</option>
                                                        <option value="1">Shift 1 (07-14)</option>
                                                        <option value="2">Shift 2 (14-22)</option>
                                                        <option value="3">Shift 3 (22-06)</option>
                                                    </select>
                                                </div>



                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-4">
                                                    <label>Operator</label>
                                                    <input type="text" class="form-control" name="operator">
                                                </div>



                                                <div class="form-group col-md-4">
                                                    <label>tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal">
                                                </div>

                                            </div>



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

<!-- modal edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEdit" action="<?= base_url('produksi/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Produksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label>Jam</label>
                        <input type="text" name="jam" id="edit_jam" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Hasil Produksi</label>
                        <input type="text" name="hasil_produksi" id="edit_hasil_produksi" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>No. SPK</label>
                        <input type="text" name="no_spk" id="edit_no_spk" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Nama Mesin</label>
                        <input type="text" name="nama_mesin" id="edit_nama_mesin" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Shift</label>
                        <select name="shif" id="edit_shif" class="form-control">
                            <option value="">-- otomatis --</option>
                            <option value="1">Shift 1 (07-14)</option>
                            <option value="2">Shift 2 (14-22)</option>
                            <option value="3">Shift 3 (22-06)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Operator</label>
                        <input type="text" name="operator" id="edit_operator" class="form-control">
                    </div>
                    <!-- <div class="mb-3">
                        <label>Target</label>
                        <input type="text" name="target" id="edit_target" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Revisi</label>
                        <input type="text" name="revisi" id="edit_revisi" class="form-control">
                    </div> -->
                    <div class="mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end modal edit -->

<!-- js modal handlers -->
<script>
    window.addEventListener('load', function() {
        if (!window.jQuery) return;

        $('#modalDelete').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            $('#formDelete').attr('action', "<?= base_url('produksi/delete/') ?>" + id);
        });

        $('.btnEdit').on('click', function() {
            const id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_jam').val($(this).data('jam'));
            $('#edit_hasil_produksi').val($(this).data('hasil'));
            $('#edit_no_spk').val($(this).data('no_spk'));
            $('#edit_nama_mesin').val($(this).data('nama_mesin'));
            $('#edit_nama_produk').val($(this).data('nama_produk'));
            $('#edit_shif').val($(this).data('shif'));
            $('#edit_operator').val($(this).data('operator'));
            $('#edit_target').val($(this).data('target'));
            $('#edit_revisi').val($(this).data('revisi'));
            $('#edit_tanggal').val($(this).data('tanggal'));

            $('#formEdit').attr('action', "<?= base_url('produksi/update/') ?>" + id);
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