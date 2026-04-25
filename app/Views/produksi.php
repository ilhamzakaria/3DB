<style>
    :root {
        --primary-modern: #4e73df;
        --secondary-modern: #1cc88a;
        --bg-modern: #f8fafc;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: var(--bg-modern);
        font-family: 'Inter', 'Nunito', sans-serif;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        transition: transform 0.2s;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #edf2f7;
        border-radius: 15px 15px 0 0 !important;
        padding: 1.5rem;
    }

    .table-responsive {
        border-radius: 0 0 15px 15px;
    }

    .table thead th {
        background-color: #f8fafc;
        border-top: none;
        border-bottom: 2px solid #edf2f7;
        color: #4e73df;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #edf2f7;
    }

    .table-secondary {
        background-color: #f1f5f9 !important;
        color: #334155;
    }

    .table-primary {
        background-color: #eff6ff !important;
        color: #1e40af;
    }

    .btn-sm {
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
    }

    .filter-count {
        background: #4e73df;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 10px;
        margin-left: 5px;
    }

    .page-title-gradient {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }

    .stat-card-mini {
        padding: 1rem;
        border-radius: 12px;
        background: #fff;
        border-left: 4px solid #4e73df;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stat-card-mini i {
        font-size: 1.5rem;
        color: #cbd5e1;
    }
</style>

<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
$searchValue = trim((string) ($q ?? ''));
$filterStartDate = trim((string) ($start_date ?? ''));
$filterEndDate = trim((string) ($end_date ?? ''));
$activeFilterCount = 0;
$shiftOptions = [
    '1' => 'Shift 1 (07-14)',
    '2' => 'Shift 2 (14-22)',
    '3' => 'Shift 3 (22-06)',
];
$listJam = [
    '07-08',
    '08-09',
    '10-11',
    '11-12',
    '12-13',
    '13-14',
    '14-15',
    '15-16',
    '16-17',
    '17-18',
    '18-19',
    '19-20',
    '20-21',
    '21-22',
    '22-23',
    '23-00',
    '00-01',
    '01-02',
    '03-04',
    '04-05',
    '05-06',
    '06-07',
];

if ($filterStartDate !== '' || $filterEndDate !== '') {
    $activeFilterCount++;
}

$dateSummary = '';
if ($filterStartDate !== '' && $filterEndDate !== '') {
    $dateSummary = $filterStartDate . ' s/d ' . $filterEndDate;
} elseif ($filterStartDate !== '') {
    $dateSummary = 'Mulai ' . $filterStartDate;
} elseif ($filterEndDate !== '') {
    $dateSummary = 'Sampai ' . $filterEndDate;
}
?>

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

                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3 class="page-title-gradient">Laporan Produksi Harian</h3>
                        <p class="text-muted">Pantau dan kelola data produksi pabrik secara real-time.</p>
                    </div>
                </div>

                <!-- Stats Summary -->
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="stat-card-mini shadow-sm">
                            <i class="fas fa-boxes"></i>
                            <div>
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Data</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($produksi) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card-mini shadow-sm" style="border-left-color: #1cc88a;">
                            <i class="fas fa-calendar-check" style="color: #1cc88a;"></i>
                            <div>
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Update Terakhir</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= !empty($produksi) ? date('d/m/Y', strtotime($produksi[0]['tanggal'])) : '-' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4 border-0">
                    <div class="card-header py-3 bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <?php if (session()->get('role') == 'produksi') { ?>
                                <button
                                    type="button"
                                    class="btn btn-primary btn-sm px-4 shadow-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addDataModal">
                                    <i class="fas fa-plus mr-1"></i> Tambah Data
                                </button>
                            <?php } ?>
                            <a href="<?= base_url('export/dashboard') ?>" class="btn btn-outline-success btn-sm px-3 ml-2">
                                <i class="fas fa-file-excel mr-1"></i> Export
                            </a>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <form method="get" action="<?= base_url('produksi') ?>" class="d-flex align-items-center gap-2 mb-0">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input
                                        type="text"
                                        name="q"
                                        class="form-control border-right-0"
                                        placeholder="Cari data..."
                                        value="<?= esc($searchValue) ?>">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary border-left-0" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <button
                                class="btn btn-light btn-sm ml-2"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#produksiFilterDrawer">
                                <i class="fas fa-sliders-h"></i> Filter
                                <?php if ($activeFilterCount > 0): ?>
                                    <span class="filter-count"><?= $activeFilterCount ?></span>
                                <?php endif; ?>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="produksiFilterDrawer" aria-labelledby="produksiFilterDrawerLabel">
                            <div class="offcanvas-header">
                                <div>
                                    <h5 class="offcanvas-title mb-1" id="produksiFilterDrawerLabel">Filter Produksi</h5>
                                    <p class="filter-hint">Panel ini bekerja seperti filter samping: buka, pilih kriteria, lalu terapkan.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body">
                                <form method="get" action="<?= base_url('produksi') ?>" class="d-flex flex-column gap-3">
                                    <input type="hidden" name="q" value="<?= esc($searchValue) ?>">

                                    <div>
                                        <label class="filter-section-title" for="produksiFilterStartDate">Tanggal Mulai</label>
                                        <input
                                            type="date"
                                            id="produksiFilterStartDate"
                                            name="start_date"
                                            class="form-control"
                                            value="<?= esc($filterStartDate) ?>">
                                    </div>

                                    <div>
                                        <label class="filter-section-title" for="produksiFilterEndDate">Tanggal Selesai</label>
                                        <input
                                            type="date"
                                            id="produksiFilterEndDate"
                                            name="end_date"
                                            class="form-control"
                                            value="<?= esc($filterEndDate) ?>">
                                    </div>

                                    <div class="filter-actions">
                                        <button class="btn btn-primary flex-fill" type="submit">Terapkan</button>
                                        <a href="<?= base_url('produksi') ?>" class="btn btn-outline-secondary flex-fill">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <h6 class="m-0 font-weight-bold text-primary text-center mb-3">Laporan Produksi Harian</h6>
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th class="small fw-bold">Nama Mesin</th>
                                        <th class="small fw-bold">Nama Produk</th>
                                        <th class="small fw-bold">Batch Number</th>
                                        <th class="small fw-bold">Shif</th>
                                        <th class="small fw-bold">Group</th>
                                        <th class="small fw-bold">Nomor Mesin</th>
                                        <th class="small fw-bold">Packing</th>
                                        <th class="small fw-bold">Isi</th>
                                        <th class="small fw-bold">Cycle Time</th>
                                        <th class="small fw-bold">Target</th>
                                        <!-- <th class="small fw-bold">Tanggal</th> -->
                                        <th class="small fw-bold">No. SPK</th>
                                        <th class="small fw-bold">Operator</th>
                                        <th class="small fw-bold">Jam</th>
                                        <th class="small fw-bold">Hasil Produksi</th>
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
                                                <td colspan="16">
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
                                                <td colspan="16">
                                                    <strong class="small fw-bold">
                                                        SHIFT <?= $p['shif']; ?>
                                                        <?= isset($shiftRanges[$p['shif']]) ? '(' . $shiftRanges[$p['shif']] . ')' : '' ?>
                                                    </strong>
                                                </td>
                                            </tr>
                                            <?php $currentShift = $p['shif']; ?>
                                        <?php endif; ?>

                                        <tr>
                                            <td><?= !empty($p['nama_mesin']) ? esc($p['nama_mesin']) : '-' ?></td>
                                            <td><?= !empty($p['nama_produk']) ? esc($p['nama_produk']) : '-' ?></td>
                                            <td><?= !empty($p['batch_number']) ? esc($p['batch_number']) : '-' ?></td>
                                            <td><?= !empty($p['shif']) ? esc($p['shif']) : '-' ?></td>
                                            <td><?= !empty($p['grup']) ? esc($p['grup']) : '-' ?></td>
                                            <td><?= !empty($p['nomor_mesin']) ? esc($p['nomor_mesin']) : '-' ?></td>
                                            <td><?= !empty($p['packing']) ? esc($p['packing']) : '-' ?></td>
                                            <td><?= !empty($p['isi']) ? esc($p['isi']) : '-' ?></td>
                                            <td><?= !empty($p['cycle_time']) ? esc($p['cycle_time']) : '-' ?></td>
                                            <td><?= !empty($p['target']) ? esc($p['target']) : '-' ?></td>
                                            <td><?= !empty($p['no_spk']) ? esc($p['no_spk']) : '-' ?></td>
                                            <td><?= !empty($p['operator']) ? esc($p['operator']) : '-' ?></td>
                                            <td><?= !empty($p['jam']) ? esc($p['jam']) : '-' ?></td>
                                            <td><?= !empty($p['hasil_produksi']) ? esc($p['hasil_produksi']) : '-' ?></td>

                                            <td class="text-center small" width="7%">
                                                <?php if (session()->get('role') == 'produksi') { ?>
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <button
                                                            type="button"
                                                            class="btn btn-warning btn-sm btnEdit"
                                                            data-id="<?= (int) $p['id'] ?>"
                                                            data-nama_mesin="<?= esc($p['nama_mesin'] ?? '') ?>"
                                                            data-nama_produk="<?= esc($p['nama_produk'] ?? '') ?>"
                                                            data-batch_number="<?= esc($p['batch_number'] ?? '') ?>"
                                                            data-shif="<?= esc($p['shif'] ?? '') ?>"
                                                            data-grup="<?= esc($p['grup'] ?? '') ?>"
                                                            data-nomor_mesin="<?= esc($p['nomor_mesin'] ?? '') ?>"
                                                            data-packing="<?= esc($p['packing'] ?? '') ?>"
                                                            data-isi="<?= esc($p['isi'] ?? '') ?>"
                                                            data-cycle_time="<?= esc($p['cycle_time'] ?? '') ?>"
                                                            data-target="<?= esc($p['target'] ?? '') ?>"
                                                            data-no_spk="<?= esc($p['no_spk'] ?? '') ?>"
                                                            data-operator="<?= esc($p['operator'] ?? '') ?>"
                                                            data-jam="<?= isset($p['jam']) ? esc($p['jam']) : '' ?>"
                                                            data-hasil="<?= isset($p['hasil_produksi']) ? esc($p['hasil_produksi']) : '' ?>"
                                                            data-tanggal="<?= isset($p['tanggal']) ? $p['tanggal'] : '' ?>"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalEdit">
                                                            <i class="fas fa-edit small"></i>
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="btn btn-danger btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDelete"
                                                            data-id="<?= (int) $p['id'] ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
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
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title font-weight-bold text-primary" id="addDataModalLabel">Tambah Data Produksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        <div class="accordion-body p-4">

                                            <div class="form-row mb-3">
                                                <div class="form-group col-md-12">
                                                    <label class="small font-weight-bold text-muted text-uppercase">Auto-Fill dari Master Data SPK</label>
                                                    <select class="form-control form-control-lg border-primary shadow-sm" id="produksiMasterNoSpkSelect" style="border-radius: 12px;">
                                                        <option value="">-- Pilih No. SPK untuk isi otomatis --</option>
                                                        <?php foreach (($spk_master ?? []) as $master): ?>
                                                            <option value="<?= esc($master['no_spk']) ?>">
                                                                <?= esc($master['no_spk']) ?> - <?= esc($master['nama_produk']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-row mb-3">
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Nama Mesin</label>
                                                    <input type="text" class="form-control" name="nama_mesin" id="add_nama_mesin" placeholder="...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Nama Produk</label>
                                                    <input type="text" class="form-control" name="nama_produk" id="add_nama_produk" placeholder="...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Batch Number</label>
                                                    <input type="text" class="form-control" name="batch_number" id="add_batch_number" placeholder="...">
                                                </div>
                                            </div>

                                            <div class="form-row mb-3">
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Shift</label>
                                                    <select class="form-control" name="shif" id="add_shif">
                                                        <option value="">-- Pilih --</option>
                                                        <?php foreach ($shiftOptions as $value => $label): ?>
                                                            <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Group</label>
                                                    <input type="text" class="form-control" name="grup" id="add_grup" placeholder="...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Nomor Mesin</label>
                                                    <input type="text" class="form-control" name="nomor_mesin" id="add_nomor_mesin" placeholder="...">
                                                </div>
                                            </div>

                                            <div class="form-row mb-3">
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Packing</label>
                                                    <input type="text" class="form-control" name="packing" id="add_packing" placeholder="...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Isi</label>
                                                    <input type="text" class="form-control" name="isi" id="add_isi" placeholder="...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Cycle Time</label>
                                                    <input type="text" class="form-control" name="cycle_time" id="add_cycle_time" placeholder="...">
                                                </div>
                                            </div>

                                            <div class="form-row mb-3">
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Target</label>
                                                    <input type="text" class="form-control" name="target" id="add_target" placeholder="...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">No. SPK</label>
                                                    <input type="text" class="form-control" name="no_spk" id="add_no_spk" placeholder="...">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Operator</label>
                                                    <input type="text" class="form-control" name="operator" id="add_operator" placeholder="...">
                                                </div>
                                            </div>

                                            <div class="form-row mb-3">
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Jam</label>
                                                    <select class="form-control" name="jam" id="add_jam">
                                                        <option value="">-- Pilih Jam --</option>
                                                        <?php foreach ($listJam as $jamOption): ?>
                                                            <option value="<?= esc($jamOption) ?>"><?= esc($jamOption) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Hasil Produksi</label>
                                                    <input type="text" class="form-control" name="hasil_produksi" id="add_hasil_produksi" placeholder="0">
                                                </div>
                                                <div class="form-group col-md-4">
                                                    <label class="small font-weight-bold">Tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal" id="add_tanggal">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEdit" action="<?= base_url('produksi/update') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Produksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Nama Mesin</label>
                            <input type="text" name="nama_mesin" id="edit_nama_mesin" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Nama Produk</label>
                            <input type="text" name="nama_produk" id="edit_nama_produk" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Batch Number</label>
                            <input type="text" name="batch_number" id="edit_batch_number" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Shif</label>
                            <select name="shif" id="edit_shif" class="form-control">
                                <option value="">-- Pilih Shift --</option>
                                <?php foreach ($shiftOptions as $value => $label): ?>
                                    <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Group</label>
                            <input type="text" name="grup" id="edit_grup" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Nomor Mesin</label>
                            <input type="text" name="nomor_mesin" id="edit_nomor_mesin" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Packing</label>
                            <input type="text" name="packing" id="edit_packing" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Isi</label>
                            <input type="text" name="isi" id="edit_isi" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Cycle Time</label>
                            <input type="text" name="cycle_time" id="edit_cycle_time" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Target</label>
                            <input type="text" name="target" id="edit_target" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>No. SPK</label>
                            <input type="text" name="no_spk" id="edit_no_spk" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Operator</label>
                            <input type="text" name="operator" id="edit_operator" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Jam</label>
                            <select name="jam" id="edit_jam" class="form-control">
                                <option value="">-- Pilih Jam --</option>
                                <?php foreach ($listJam as $jamOption): ?>
                                    <option value="<?= esc($jamOption) ?>"><?= esc($jamOption) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Hasil Produksi</label>
                            <input type="text" name="hasil_produksi" id="edit_hasil_produksi" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control">
                        </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('modalDelete');
        const editModal = document.getElementById('modalEdit');
        const formDelete = document.getElementById('formDelete');
        const formEdit = document.getElementById('formEdit');

        if (deleteModal && formDelete) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button?.getAttribute('data-id') || '';

                formDelete.setAttribute('action', "<?= base_url('produksi/delete/') ?>" + id);
            });
        }

        if (editModal && formEdit) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button?.getAttribute('data-id') || '';

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nama_mesin').value = button?.getAttribute('data-nama_mesin') || '';
                document.getElementById('edit_nama_produk').value = button?.getAttribute('data-nama_produk') || '';
                document.getElementById('edit_batch_number').value = button?.getAttribute('data-batch_number') || '';
                document.getElementById('edit_shif').value = button?.getAttribute('data-shif') || '';
                document.getElementById('edit_grup').value = button?.getAttribute('data-grup') || '';
                document.getElementById('edit_nomor_mesin').value = button?.getAttribute('data-nomor_mesin') || '';
                document.getElementById('edit_packing').value = button?.getAttribute('data-packing') || '';
                document.getElementById('edit_isi').value = button?.getAttribute('data-isi') || '';
                document.getElementById('edit_cycle_time').value = button?.getAttribute('data-cycle_time') || '';
                document.getElementById('edit_target').value = button?.getAttribute('data-target') || '';
                document.getElementById('edit_no_spk').value = button?.getAttribute('data-no_spk') || '';
                document.getElementById('edit_operator').value = button?.getAttribute('data-operator') || '';
                document.getElementById('edit_jam').value = button?.getAttribute('data-jam') || '';
                document.getElementById('edit_hasil_produksi').value = button?.getAttribute('data-hasil') || '';
                document.getElementById('edit_tanggal').value = button?.getAttribute('data-tanggal') || '';

                formEdit.setAttribute('action', "<?= base_url('produksi/update/') ?>" + id);
            });
        }
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!window.jQuery || !$.fn.DataTable || !$.fn.DataTable.isDataTable('#dataTable')) {
            return;
        }

        $('#dataTable').DataTable();
    });
</script>

<script>
    const produksiSpkMaster = <?= json_encode($spk_master ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const produksiSpkMasterMap = Object.fromEntries(
        produksiSpkMaster.map((item) => [String(item.no_spk || '').trim(), item])
    );

    function applyProduksiMasterData(noSpk) {
        const key = String(noSpk || '').trim();
        const master = produksiSpkMasterMap[key];

        if (!master) {
            return;
        }

        const noSpkInput = document.getElementById('add_no_spk');
        const namaMesinInput = document.getElementById('add_nama_mesin');
        const namaProdukInput = document.getElementById('add_nama_produk');
        const batchNumberInput = document.getElementById('add_batch_number');
        const shifInput = document.getElementById('add_shif');
        const grupInput = document.getElementById('add_grup');
        const nomorMesinInput = document.getElementById('add_nomor_mesin');
        const packingInput = document.getElementById('add_packing');
        const isiInput = document.getElementById('add_isi');
        const cycleTimeInput = document.getElementById('add_cycle_time');
        const operatorInput = document.getElementById('add_operator');
        const targetInput = document.getElementById('add_target');
        const tanggalInput = document.getElementById('add_tanggal');

        if (noSpkInput) noSpkInput.value = master.no_spk || '';
        if (namaMesinInput) namaMesinInput.value = master.nama_mesin || '';
        if (namaProdukInput) namaProdukInput.value = master.nama_produk || '';
        if (batchNumberInput) batchNumberInput.value = master.batch_number || '';
        if (shifInput) shifInput.value = master.shif || '';
        if (grupInput) grupInput.value = master.grup || '';
        if (nomorMesinInput) nomorMesinInput.value = master.nomor_mesin || '';
        if (packingInput) packingInput.value = master.packing || '';
        if (isiInput) isiInput.value = master.isi || '';
        if (cycleTimeInput) cycleTimeInput.value = master.cycle_time || '';
        if (operatorInput) operatorInput.value = master.operator || '';
        if (targetInput) targetInput.value = master.target || master.targett || '';
        if (tanggalInput && !tanggalInput.value) tanggalInput.value = master.tanggal || '';
    }

    const produksiMasterNoSpkSelect = document.getElementById('produksiMasterNoSpkSelect');
    if (produksiMasterNoSpkSelect) {
        produksiMasterNoSpkSelect.addEventListener('change', function() {
            applyProduksiMasterData(this.value);
        });
    }

    const produksiNoSpkInput = document.getElementById('add_no_spk');
    if (produksiNoSpkInput) {
        produksiNoSpkInput.addEventListener('change', function() {
            applyProduksiMasterData(this.value);
        });
    }
</script>

<?= $this->endSection() ?>