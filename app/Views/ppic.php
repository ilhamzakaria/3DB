<style>
    thead th {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .btn-delete {
        position: absolute;
        right: 10px;
        bottom: 10px;
    }

    /* Override sb-admin-2: enable horizontal scroll (content-wrapper has overflow-x: hidden) */
    #wrapper #content-wrapper {
        overflow-x: auto !important;
    }

    /* horizontal scroll inside table wrapper */
    .ppic-table-wrap {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        min-width: 0;
    }

    .ppic-table-wrap table {
        min-width: 1100px;
        margin-bottom: 0;
    }

    .ppic-table-wrap th,
    .ppic-table-wrap td {
        white-space: nowrap;
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
                        <?php if (session()->get('role') == 'ppic') { ?>
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
                        <div class="table-responsive ppic-table-wrap">
                            <h6 class="m-0 font-weight-bold text-primary text-center mb-3">Laporan ppic harian</h6>

                            <table class="table table-bordered" id="dataTable" cellspacing="0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th class="small fw-bold" width="5%">Jam</th>
                                        <th class="small fw-bold">No. SPK</th>
                                        <th class="small fw-bold">Nama Mesin</th>
                                        <th class="small fw-bold">Nama Produk</th>
                                        <th class="small fw-bold">Operator</th>
                                        <th class="small fw-bold">Target</th>
                                        <th class="small fw-bold">Revisi</th>
                                        <?php if (session()->get('role') == 'ppic'): ?>
                                            <th class="small fw-bold" width="6%">Action</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>

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
                                                <td colspan="11">
                                                    <strong class="small fw-bold">
                                                        SHIFT <?= $p['shif']; ?>
                                                        <?= isset($shiftRanges[$p['shif']]) ? '(' . $shiftRanges[$p['shif']] . ')' : '' ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <?php $currentShift = $p['shif']; ?>
                                        <?php endif; ?>


                                        <tr>
                                            <td><?= $p['jam']; ?></td>
                                            <td><?= $p['no_spk'] ?></td>
                                            <td><?= $p['nama_mesin'] ?></td>
                                            <td><?= $p['nama_produk'] ?></td>
                                            <td><?= $p['operator'] ?></td>
                                            <td><?= $p['targett'] ?></td>

                                            <?php
                                            $revisiText = trim((string) ($p['revisi_display'] ?? $p['revisi'] ?? ''));
                                            ?>
                                            <?php if ($revisiText !== ''): ?>
                                                <td class="text-danger"><?= esc($revisiText) ?></td>
                                            <?php else: ?>
                                                <td>-</td>
                                            <?php endif; ?>


                                            <?php if (session()->get('role') == 'ppic'): ?>
                                                <td class="text-center d-flex gap-1 justify-content-center">
                                                    <a href="#"
                                                        class="btnEdit"
                                                        data-id="<?= $p['id'] ?>"
                                                        data-jam="<?= $p['jam'] ?>"
                                                        data-no_spk="<?= $p['no_spk'] ?>"
                                                        data-nama_mesin="<?= $p['nama_mesin'] ?>"
                                                        data-nama_produk="<?= $p['nama_produk'] ?>"
                                                        data-shif="<?= $p['shif'] ?>"
                                                        data-operator="<?= $p['operator'] ?>"
                                                        data-targett="<?= $p['targett'] ?>"
                                                        data-revisi="<?= $p['revisi'] ?>"
                                                        data-tanggal="<?= $p['tanggal'] ?>"
                                                        data-toggle="modal"
                                                        data-bs-toggle="modal"
                                                        data-target="#modalEdit"
                                                        data-bs-target="#modalEdit">
                                                        <span class="small text-primary">Edit</span>
                                                    </a>
                                                    |
                                                    <a href="#"
                                                        data-toggle="modal"
                                                        data-target="#modalDelete"
                                                        data-id="<?= $p['id'] ?>">
                                                        <span class="small text-danger">Hapus</span>
                                                    </a>
                                                </td>
                                            <?php endif; ?>
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

                    </div>

                    <form method="POST" action="<?= base_url('ppic/tambahData') ?>">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

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
                                                    <label class="fw-bold small">Jam</label>
                                                    <select class="form-control" name="jam" id="jamSelect" required>
                                                        <option value="">-- Pilih Jam --</option>
                                                        <?php
                                                        $listJam = [
                                                            "07-08",
                                                            "08-09",
                                                            "09-10",
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
                                                    <label>No. SPK</label>
                                                    <input type="text" class="form-control" name="no_spk">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Nama Mesin</label>
                                                    <input type="text" class="form-control" name="nama_mesin">
                                                </div>



                                            </div>

                                            <div class="form-row">
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

                                                <div class="form-group col-md-4">
                                                    <label>Operator</label>
                                                    <input type="text" class="form-control" name="operator">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Target</label>
                                                    <input type="text" class="form-control" name="targett">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Revisi 1</label>
                                                    <input type="text" class="form-control" name="revisi_1" placeholder="Opsional">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Revisi 2</label>
                                                    <input type="text" class="form-control" name="revisi_2" placeholder="Opsional">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Revisi 3</label>
                                                    <input type="text" class="form-control" name="revisi_3" placeholder="Opsional">
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formDelete" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="DELETE">

                <div class="modal-body">
                    <p>Yakin ingin menghapus data ini?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
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

            <form id="formEdit" action="<?= base_url('ppic/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Produksi</h5>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Jam</label>
                                <input type="text" name="jam" id="edit_jam" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>No SPK</label>
                                <input type="text" name="no_spk" id="edit_no_spk" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Nama Mesin</label>
                        <input type="text" name="nama_mesin" id="edit_nama_mesin" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Shift</label>
                                <select name="shif" id="edit_shif" class="form-control">
                                    <option value="">-- otomatis --</option>
                                    <option value="1">Shift 1 (07-14)</option>
                                    <option value="2">Shift 2 (14-22)</option>
                                    <option value="3">Shift 3 (22-06)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Operator</label>
                                <input type="text" name="operator" id="edit_operator" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Target</label>
                                <input type="text" name="targett" id="edit_targett" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Revisi</label>
                                <input type="text" name="revisi" id="edit_revisi" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- end modal edit -->

<!-- js modal edit (moved below jQuery includes) -->
<!-- placeholder: will be defined after jQuery -->
<!-- end js modal edit -->


<!-- js modal delete -->
<script>
    window.addEventListener('load', function() {
        if (!window.jQuery) return;

        $('#modalDelete').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            $('#formDelete').attr('action', "<?= base_url('ppic/delete/') ?>" + id);
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('vendor/jquery/jquery.min.js') ?>"></script>

<script>
    // edit modal setup
    $(function() {
        $('.btnEdit').on('click', function() {
            const id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_jam').val($(this).data('jam'));
            $('#edit_no_spk').val($(this).data('no_spk'));
            $('#edit_nama_mesin').val($(this).data('nama_mesin'));
            $('#edit_nama_produk').val($(this).data('nama_produk'));
            $('#edit_shif').val($(this).data('shif'));
            $('#edit_operator').val($(this).data('operator'));
            $('#edit_targett').val($(this).data('targett'));
            $('#edit_revisi').val($(this).data('revisi'));
            $('#edit_tanggal').val($(this).data('tanggal'));

            // update form action to include id
            $('#formEdit').attr('action', "<?= base_url('ppic/update/') ?>" + id);
        });
    });
</script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="<?= base_url('vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<!-- Page level custom scripts -->
<script src="<?= base_url('js/demo/datatables-demo.js') ?>"></script>

<!-- detail rijek input muncul saat di centang -->
<script>
    document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            let input = this.closest('tr').querySelector('input[type="number"]');
            input.disabled = !this.checked;
            if (!this.checked) input.value = '';
        });
    });
</script>


<!-- js detail jam produksi -->
<script>
    let daftarJam = [
        "06-07", "07-08", "08-09", "09-10",
        "10-11", "11-12", "12-13", "13-14",
        "14-15", "15-16", "16-17", "17-18",
        "18-19", "19-20", "20-21", "21-22",
        "22-23", "23-00", "00-01", "01-02",
        "02-03", "03-04", "04-05", "05-06"
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
        </div>
    `;

        document.getElementById("containerJam")
            .insertAdjacentHTML("beforeend", html);
    }

    // otomatis tampil 1 baris saat buka
    tambahJam();
</script>

<script>
    document.getElementById('jamSelect').addEventListener('change', function() {
        const jam = this.value;
        const shiftSelect = document.getElementById('shiftInput');

        if (!shiftSelect) {
            return;
        }

        const shift1 = ["07-08", "08-09", "09-10", "10-11", "11-12", "12-13", "13-14"];
        const shift2 = ["14-15", "15-16", "16-17", "17-18", "18-19", "19-20", "20-21", "21-22"];
        const shift3 = ["22-23", "23-00", "00-01", "01-02", "02-03", "03-04", "04-05", "05-06", "06-07"];

        if (shift1.includes(jam)) {
            shiftSelect.value = "1";
        } else if (shift2.includes(jam)) {
            shiftSelect.value = "2";
        } else if (shift3.includes(jam)) {
            shiftSelect.value = "3";
        } else {
            shiftSelect.value = "";
        }
    });
</script>



<?= $this->endSection() ?>
