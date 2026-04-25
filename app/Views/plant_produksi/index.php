<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        --secondary-gradient: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        --surface-card: #ffffff;
        --text-main: #2d3748;
        --text-muted: #718096;
        --bg-body: #f7fafc;
    }

    .page-header {
        background: white;
        padding: 1.5rem 2rem;
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title h1 {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        letter-spacing: -0.025em;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #edf2f7;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        margin-top: 0.5rem;
    }

    .stat-icon {
        float: right;
        font-size: 2rem;
        opacity: 0.2;
        color: #4e73df;
    }

    .modern-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-container {
        padding: 0;
    }

    .modern-table {
        margin-bottom: 0;
        width: 100%;
    }

    .modern-table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        color: #4a5568;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem 1.5rem;
    }

    .modern-table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        color: #2d3748;
        border-bottom: 1px solid #edf2f7;
    }

    .modern-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .date-divider {
        background: #ebf4ff !important;
        font-weight: 800;
        color: #2c5282;
        font-size: 0.9rem;
    }

    .shift-divider {
        background: #f0fff4 !important;
        font-weight: 700;
        color: #276749;
        font-size: 0.8rem;
    }

    .badge-modern {
        padding: 0.5em 1em;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.7rem;
    }

    .badge-shift-1 { background: #e2e8f0; color: #4a5568; }
    .badge-shift-2 { background: #feebc8; color: #7b341e; }
    .badge-shift-3 { background: #bee3f8; color: #2a4365; }

    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: none;
        margin: 0 2px;
    }

    .btn-edit-modern { background: #ebf4ff; color: #3182ce; }
    .btn-edit-modern:hover { background: #3182ce; color: white; }

    .btn-delete-modern { background: #fff5f5; color: #e53e3e; }
    .btn-delete-modern:hover { background: #e53e3e; color: white; }

    .search-filter-bar {
        background: #f8fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #edf2f7;
    }

    .custom-input {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .custom-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
    }
    .detail-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .detail-section-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #4a5568;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #edf2f7;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .detail-item {
        background: #f8fafc;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid #edf2f7;
    }

    .detail-label {
        font-size: 0.7rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 0.9rem;
        color: #2d3748;
        font-weight: 700;
    }

    .table-detail {
        font-size: 0.85rem;
    }

    .table-detail thead th {
        background: #f1f5f9;
        font-weight: 700;
        border: none;
    }

    .btn-detail-modern { background: #f0fff4; color: #38a169; }
    .btn-detail-modern:hover { background: #38a169; color: white; }

    /* Instagram Style Modal */
    .ig-modal .modal-content {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        width: 300px;
        margin: auto;
    }
    .ig-modal-body {
        padding: 32px 24px;
        text-align: center;
    }
    .ig-modal-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #262626;
    }
    .ig-modal-text {
        font-size: 0.9rem;
        color: #8e8e8e;
        line-height: 1.4;
    }
    .ig-btn-list {
        display: flex;
        flex-direction: column;
        border-top: 1px solid #dbdbdb;
    }
    .ig-btn {
        padding: 12px;
        background: none;
        border: none;
        border-bottom: 1px solid #dbdbdb;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.1s;
        text-decoration: none !important;
        text-align: center;
    }
    .ig-btn:last-child {
        border-bottom: none;
    }
    .ig-btn:active {
        background: #fafafa;
    }
    .ig-btn-danger {
        color: #ed4956;
        font-weight: 700;
    }
    .ig-btn-secondary {
        color: #262626;
        font-weight: 400;
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Plant Produksi</h1>
        <p class="text-muted small mb-0">Manajemen data produksi harian pabrik</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('plant_produksi/trash') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mr-2">
            <i class="fas fa-trash-restore mr-1"></i> Sampah
        </a>
        <a href="<?= base_url('plant_produksi/tambah') ?>" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Produksi
        </a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <i class="fas fa-calendar-day stat-icon"></i>
        <div class="stat-label">Total Input Hari Ini</div>
        <div class="stat-value">
            <?php 
                $today = date('Y-m-d');
                $countToday = 0;
                $totalBagus = 0;
                $totalReject = 0;
                foreach($produksi as $p) {
                    if($p['tanggal'] == $today) {
                        $countToday++;
                        $totalBagus += $p['grand_total_bagus'];
                        $totalReject += $p['grand_total_reject'];
                    }
                }
                echo $countToday;
            ?>
        </div>
    </div>
    <div class="stat-card">
        <i class="fas fa-check-circle stat-icon text-success" style="opacity:0.1"></i>
        <div class="stat-label">Produksi Bagus (Hari Ini)</div>
        <div class="stat-value text-success"><?= number_format($totalBagus) ?></div>
    </div>
    <div class="stat-card">
        <i class="fas fa-times-circle stat-icon text-danger" style="opacity:0.1"></i>
        <div class="stat-label">Total Reject (Hari Ini)</div>
        <div class="stat-value text-danger"><?= number_format($totalReject) ?></div>
    </div>
    <div class="stat-card">
        <i class="fas fa-percentage stat-icon text-info" style="opacity:0.1"></i>
        <div class="stat-label">Efficiency</div>
        <div class="stat-value text-info">
            <?php 
                $total = $totalBagus + $totalReject;
                echo $total > 0 ? round(($totalBagus / $total) * 100, 1) . '%' : '0%';
            ?>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="search-filter-bar">
        <form action="<?= base_url('plant_produksi') ?>" method="get" class="row no-gutters align-items-center">
            <div class="col-md-4 pr-md-2 mb-2 mb-md-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0" style="border-radius: 10px 0 0 10px;">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" name="q" class="form-control custom-input border-left-0" placeholder="Cari SPK, Mesin, Produk..." value="<?= esc($q ?? '') ?>" style="border-radius: 0 10px 10px 0;">
                </div>
            </div>
            <div class="col-md-3 pr-md-2 mb-2 mb-md-0">
                <select name="shift" class="form-control custom-input">
                    <option value="">-- Semua Shift --</option>
                    <option value="1" <?= ($filter_shift == '1') ? 'selected' : '' ?>>Shift 1</option>
                    <option value="2" <?= ($filter_shift == '2') ? 'selected' : '' ?>>Shift 2</option>
                    <option value="3" <?= ($filter_shift == '3') ? 'selected' : '' ?>>Shift 3</option>
                </select>
            </div>
            <div class="col-md-3 pr-md-2 mb-2 mb-md-0">
                <input type="date" name="tanggal" class="form-control custom-input" value="<?= esc($filter_tanggal ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 flex-grow-1 shadow-sm">
                    <i class="fas fa-filter mr-1"></i> Terapkan
                </button>
                <a href="<?= base_url('plant_produksi') ?>" class="btn btn-light btn-sm rounded-circle ml-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;" title="Reset">
                    <i class="fas fa-sync-alt small"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="table-responsive table-container">
        <table class="table modern-table" id="productionTable">
            <thead>
                <tr>
                    <th>No. SPK</th>
                    <th>Mesin</th>
                    <th>Produk</th>
                    <th class="text-center">Shift</th>
                    <th>Operator</th>
                    <th class="text-right">Hasil Bagus</th>
                    <th class="text-right">Reject</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $currentDate = '';
                $currentShift = '';
                if(empty($produksi)): ?>
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <img src="https://illustrations.popsy.co/gray/data-analysis.svg" style="width: 150px; opacity: 0.5;" alt="No Data">
                        <p class="text-muted mt-3">Belum ada data produksi yang tercatat.</p>
                        <a href="<?= base_url('plant_produksi/tambah') ?>" class="btn btn-primary btn-sm rounded-pill">Input Sekarang</a>
                    </td>
                </tr>
                <?php else:
                    foreach ($produksi as $p): 
                        if ($currentDate != $p['tanggal']): 
                            $currentDate = $p['tanggal'];
                            $currentShift = ''; // Reset shift when date changes
                            $dateObj = new DateTime($currentDate);
                            $formattedDate = $dateObj->format('d M Y');
                ?>
                    <tr class="date-divider">
                        <td colspan="8"><i class="far fa-calendar-alt mr-2"></i> <?= $formattedDate ?></td>
                    </tr>
                <?php endif; ?>

                <?php if ($currentShift != $p['shift']): 
                    $currentShift = $p['shift'];
                ?>
                    <tr class="shift-divider">
                        <td colspan="8" class="pl-4">
                            <span class="badge badge-modern badge-shift-<?= $p['shift'] ?>">
                                <i class="fas fa-clock mr-1"></i> SHIFT <?= $p['shift'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td class="font-weight-bold" style="color: #4e73df;"><?= esc($p['nomor_spk']) ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-2" style="width: 30px; height: 30px;">
                                <i class="fas fa-cog text-muted small"></i>
                            </div>
                            <?= esc($p['nama_mesin']) ?>
                        </div>
                    </td>
                    <td><?= esc($p['nama_produksi']) ?></td>
                    <td class="text-center">
                        <span class="badge badge-pill badge-light small px-3"><?= esc($p['grup'] ?? '-') ?></span>
                    </td>
                    <td>
                        <span class="small font-weight-600 text-muted"><?= esc($p['operator']) ?></span>
                    </td>
                    <td class="text-right font-weight-bold"><?= number_format($p['grand_total_bagus']) ?></td>
                    <td class="text-right text-danger font-weight-bold"><?= number_format($p['grand_total_reject']) ?></td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="action-btn btn-detail-modern btnDetail" data-id="<?= $p['id'] ?>" title="Detail">
                                <i class="fas fa-eye small"></i>
                            </button>
                            <a href="<?= base_url('plant_produksi/edit/' . $p['id']) ?>" class="action-btn btn-edit-modern" title="Edit">
                                <i class="fas fa-pen-alt small"></i>
                            </a>
                            <a href="#" 
                               class="action-btn btn-delete-modern btnDeletePlant" 
                               data-id="<?= $p['id'] ?>"
                               data-spk="<?= esc($p['nomor_spk']) ?>"
                               title="Hapus">
                                <i class="fas fa-trash-alt small"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade detail-modal" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-primary" id="detailTitle">Detail Produksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div id="detailLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
                <div id="detailContent" style="display: none;">
                    <!-- Section 1: Header Info -->
                    <h6 class="detail-section-title"><i class="fas fa-info-circle"></i> Informasi Utama</h6>
                    <div class="detail-grid">
                        <div class="detail-item"><div class="detail-label">No. SPK</div><div class="detail-value" id="det_spk"></div></div>
                        <div class="detail-item"><div class="detail-label">Nama Mesin</div><div class="detail-value" id="det_mesin"></div></div>
                        <div class="detail-item"><div class="detail-label">Produk</div><div class="detail-value" id="det_produk"></div></div>
                        <div class="detail-item"><div class="detail-label">Shift</div><div class="detail-value" id="det_shift"></div></div>
                        <div class="detail-item"><div class="detail-label">Grup</div><div class="detail-value" id="det_grup"></div></div>
                        <div class="detail-item"><div class="detail-label">Operator</div><div class="detail-value" id="det_operator"></div></div>
                        <div class="detail-item"><div class="detail-label">Tanggal</div><div class="detail-value" id="det_tanggal"></div></div>
                    </div>

                    <!-- Section 2: Produksi & Rejects -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="detail-section-title"><i class="fas fa-clock"></i> Laporan Per Jam</h6>
                            <table class="table table-sm table-detail">
                                <thead><tr><th>Jam</th><th class="text-right">Hasil</th></tr></thead>
                                <tbody id="det_jams_body"></tbody>
                                <tfoot><tr class="font-weight-bold"><td>Total Bagus</td><td class="text-right" id="det_total_bagus"></td></tr></tfoot>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="detail-section-title"><i class="fas fa-exclamation-triangle"></i> Reject Details</h6>
                            <table class="table table-sm table-detail">
                                <thead><tr><th>Jenis Reject</th><th class="text-right">Jumlah</th></tr></thead>
                                <tbody id="det_rejects_body"></tbody>
                                <tfoot><tr class="font-weight-bold"><td>Total Reject</td><td class="text-right" id="det_total_reject"></td></tr></tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Section 3: Materials & Colorants -->
                    <div class="row mt-3">
                        <div class="col-md-7">
                            <h6 class="detail-section-title"><i class="fas fa-flask"></i> Material & Colorant</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-detail">
                                    <thead><tr><th>Item</th><th>Merek/Kode</th><th>Lot</th><th class="text-right">Pakai</th></tr></thead>
                                    <tbody id="det_mats_body"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h6 class="detail-section-title"><i class="fas fa-stopwatch"></i> Downtime</h6>
                            <table class="table table-sm table-detail">
                                <thead><tr><th>Alasan</th><th class="text-right">Durasi</th></tr></thead>
                                <tbody id="det_downtimes_body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Instagram Style Delete -->
<div class="modal fade ig-modal" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="ig-modal-body">
                <div class="ig-modal-title">Pindahkan ke Sampah?</div>
                <div class="ig-modal-text">Data produksi SPK <span id="deleteSpkName" class="font-weight-bold"></span> akan dipindahkan ke folder sampah.</div>
            </div>
            <div class="ig-btn-list">
                <a href="#" id="confirmDeleteBtn" class="ig-btn ig-btn-danger">Hapus</a>
                <button type="button" class="ig-btn ig-btn-secondary" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
window.addEventListener('load', function() {
    if (typeof jQuery === 'undefined') return;
    
    jQuery(document).ready(function($) {
        // Detail Modal Logic
        $('.btnDetail').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#detailModal').modal('show');
            $('#detailLoading').show();
            $('#detailContent').hide();

            $.ajax({
                url: '<?= base_url('plant_produksi/detail') ?>/' + id,
                method: 'GET',
                success: function(res) {
                    $('#detailLoading').hide();
                    $('#detailContent').fadeIn();
                    
                    $('#det_spk').text(res.header.nomor_spk);
                    $('#det_mesin').text(res.header.nama_mesin);
                    $('#det_produk').text(res.header.nama_produksi);
                    $('#det_shift').text('Shift ' + res.header.shift);
                    $('#det_grup').text(res.header.grup || '-');
                    $('#det_operator').text(res.header.operator);
                    $('#det_tanggal').text(res.header.tanggal);
                    $('#det_total_bagus').text(parseInt(res.header.grand_total_bagus).toLocaleString());
                    $('#det_total_reject').text(parseInt(res.header.grand_total_reject).toLocaleString());

                    let jamHtml = '';
                    res.jams.forEach(j => {
                        jamHtml += `<tr><td>${j.rentang_jam}</td><td class="text-right">${parseInt(j.hasil_produksi).toLocaleString()}</td></tr>`;
                    });
                    $('#det_jams_body').html(jamHtml);

                    let rejectHtml = '';
                    res.rejects.forEach(r => {
                        rejectHtml += `<tr><td>${r.jenis_reject}</td><td class="text-right">${parseInt(r.jumlah).toLocaleString()}</td></tr>`;
                    });
                    $('#det_rejects_body').html(rejectHtml || '<tr><td colspan="2" class="text-center">-</td></tr>');

                    let matHtml = '';
                    res.materials.forEach(m => {
                        matHtml += `<tr><td>Material</td><td>${m.merek_kode}</td><td>${m.lot_a || '-'}</td><td class="text-right">${m.pemakaian}</td></tr>`;
                    });
                    res.colorants.forEach(c => {
                        matHtml += `<tr><td>Colorant</td><td>${c.code}</td><td>${c.nomor_lot}</td><td class="text-right">${c.pemakaian}</td></tr>`;
                    });
                    $('#det_mats_body').html(matHtml || '<tr><td colspan="4" class="text-center">-</td></tr>');

                    let dtHtml = '';
                    res.downtimes.forEach(dt => {
                        dtHtml += `<tr><td>${dt.alasan_downtime}</td><td class="text-right">${dt.durasi_menit}m</td></tr>`;
                    });
                    $('#det_downtimes_body').html(dtHtml || '<tr><td colspan="2" class="text-center">-</td></tr>');
                },
                error: function() {
                    alert('Gagal memuat data detail.');
                    $('#detailModal').modal('hide');
                }
            });
        });

        // Delete Confirmation Modal (Instagram Style)
        $('.btnDeletePlant').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const spk = $(this).data('spk');
            
            $('#deleteSpkName').text(spk);
            $('#confirmDeleteBtn').attr('href', '<?= base_url('plant_produksi/delete') ?>/' + id);
            $('#deleteConfirmModal').modal('show');
        });
    });
});
</script>


<?= $this->endSection() ?>

<?= $this->endSection() ?>
