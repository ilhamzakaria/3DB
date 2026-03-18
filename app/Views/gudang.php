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
                        <?php if (session()->get('role') == 'gudang') { ?>
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
                            <h6 class="m-0 font-weight-bold text-primary text-center mb-3">Laporan Stok Harian</h6>

                            <table class="table table-sm table-bordered text-center">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="small">No SPK</th>
                                        <th class="small">Polycarbonate</th>
                                        <th class="small">Sisa PO</th>
                                        <th class="small">Hold</th>
                                        <th class="small">Gumpalan</th>
                                        <th class="small">Tanggal</th>
                                        <th class="small">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($gudang)) : ?>
                                        <?php foreach ($gudang as $p): ?>
                                            <tr>
                                                <td><?= !empty($p['no_spk']) ? esc($p['no_spk']) : '-' ?></td>
                                                <td><?= !empty($p['polycarbonate']) ? esc($p['polycarbonate']) : '-' ?></td>
                                                <td><?= !empty($p['sisa_po']) ? esc($p['sisa_po']) : '-' ?></td>
                                                <td><?= !empty($p['hold']) ? esc($p['hold']) : '-' ?></td>
                                                <td><?= !empty($p['gumpalan']) ? esc($p['gumpalan']) : '-' ?></td>
                                                <td width="9%">
                                                    <span>
                                                        <?php
                                                        if (!empty($p['tanggal'])) {
                                                            $date = new DateTime($p['tanggal']);
                                                            $formatter = new IntlDateFormatter(
                                                                'id_ID',
                                                                IntlDateFormatter::LONG, // Format: 16 Maret 2026
                                                                IntlDateFormatter::NONE  // Tanpa waktu (Jam)
                                                            );
                                                            echo esc($formatter->format($date));
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </span>
                                                </td>


                                                <td class="text-center small" width="7%">
                                                    <?php if (session()->get('role') == 'gudang') { ?>
                                                        <div class="d-flex gap-1 justify-content-center">

                                                            <a href="#"
                                                                class="btn btn-warning btn-sm btnEdit"
                                                                data-id="<?= $p['id'] ?>"
                                                                data-no_spk="<?= $p['no_spk'] ?>"
                                                                data-polycarbonate="<?= $p['polycarbonate'] ?>"
                                                                data-sisa_po="<?= $p['sisa_po'] ?>"
                                                                data-hold="<?= $p['hold'] ?>"
                                                                data-gumpalan="<?= $p['gumpalan'] ?? '' ?>"
                                                                data-tanggal="<?= $p['tanggal'] ?? '' ?>"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalEdit">
                                                                <i class="fas fa-edit small"></i>
                                                            </a>

                                                            <a href="#"
                                                                class="btn btn-danger btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalDelete"
                                                                data-id="<?= $p['id'] ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </a>

                                                        </div>
                                                    <?php } else { ?> - <?php } ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    <?php else : ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                Data gudang kosong
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
                        <h5 class="modal-title text-primary acordion-header" id="addDataModalLabel">Tambah Data Gudang</h5>
                        <button class="close acordion-button" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <form method="POST" action="<?= base_url('gudang/tambahData') ?>">
                        <?= csrf_field() ?>

                        <div class="modal-body">

                            <div class="accordion" id="accordionGudang">



                                <!-- ================= TAMBAH MATERIAL ================= -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#material">
                                            Stok Gudang
                                        </button>
                                    </h2>

                                    <div id="material" class="accordion-collapse collapse" data-bs-parent="#accordionGudang">
                                        <div class="accordion-body">

                                            <div class="form-row">
                                                <div class="form-group col-md-4">
                                                    <label>No SPK</label>
                                                    <input type="text" class="form-control" name="no_spk">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Polycarbonate</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="polycarbonate">
                                                        <span class="input-group-text">kg</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal">
                                                </div>

                                                <div class="form-group col-md-4">
                                                    <label>Sisa PO</label>
                                                    <input type="text" class="form-control" name="sisa_po">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Hold</label>
                                                    <input type="text" class="form-control" name="hold">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label>Gumpalan</label>
                                                    <input type="text" class="form-control" name="gumpalan">
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

<!-- modal edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEdit" action="<?= base_url('gudang/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Gudang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">

                    <div class="mb-3">
                        <label>No. SPK</label>
                        <input type="text" name="no_spk" id="edit_no_spk" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Polycarbonate</label>
                        <input type="text" name="polycarbonate" id="edit_polycarbonate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Sisa PO</label>
                        <input type="text" name="sisa_po" id="edit_sisa_po" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>hold</label>
                        <input type="text" name="hold" id="edit_hold" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>gumpalan</label>
                        <input type="text" name="gumpalan" id="edit_gumpalan" class="form-control">
                    </div>
                    <div class="mb-3">
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


<!-- js modal delete -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalDelete = document.getElementById('modalDelete');

        modalDelete.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');

            const form = document.getElementById('formDelete');
            form.action = "<?= base_url('gudang/delete/') ?>" + id;
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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

<!-- edit table -->
<script>
    window.addEventListener('load', function() {
        if (!window.jQuery) return;

        $('#modalDelete').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            $('#formDelete').attr('action', "<?= base_url('gudang/delete/') ?>" + id);
        });

        $('.btnEdit').on('click', function() {
            const id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_no_spk').val($(this).data('no_spk'));
            $('#edit_polycarbonate').val($(this).data('polycarbonate'));
            $('#edit_sisa_po').val($(this).data('sisa_po'));
            $('#edit_hold').val($(this).data('hold'));
            $('#edit_gumpalan').val($(this).data('gumpalan'));
            $('#edit_tanggal').val($(this).data('tanggal'));

            $('#formEdit').attr('action', "<?= base_url('gudang/update/') ?>" + id);
        });
    });

    // paksa close 
    document.querySelectorAll('.btn-close, .btn-batal').forEach(function(btn) {
        btn.addEventListener('click', function() {

            document.querySelectorAll('.modal-backdrop').forEach(function(el) {
                el.remove();
            });

            document.body.classList.remove('modal-open');
            document.body.style = "";

        });
    });
</script>

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
<!-- <script>
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
</script> -->

<!-- form submits normally (CSRF handled by <?= "csrf_field()" ?>) -->



<?= $this->endSection() ?>