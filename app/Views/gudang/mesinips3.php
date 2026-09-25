<?= $this->extend('layout/main') ?>
<?php $view_version = '20260718_01'; ?>

<?= $this->section('content') ?>

<?php
$statusLabels = [
    'menggiling' => 'Menggiling',
    'bahan_masuk' => 'Bahan Masuk',
    'bahan_keluar' => 'Bahan Keluar',
];

$statusBadges = [
    'menggiling' => 'badge-warning',
    'bahan_masuk' => 'badge-success',
    'bahan_keluar' => 'badge-info',
];

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

<style>
.mesin-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
/* Top scrollbar mirror */
.mesin-table-scroll-top { overflow-x: auto; overflow-y: hidden; height: 12px; margin-bottom: 2px; -webkit-overflow-scrolling: touch; }
.mesin-table-scroll-inner { height: 1px; }
.mesin-table { min-width: 1400px; font-size: 0.78rem; }
.mesin-table thead tr th {
    vertical-align: middle !important;
    text-align: center;
    padding: 4px 6px;
    white-space: nowrap;
    line-height: 1.3;
    font-size: 0.75rem;
}
.mesin-table tbody td {
    vertical-align: middle;
    padding: 3px 5px;
    white-space: nowrap;
}
.mesin-table .bg-info    { background-color: #17a2b8 !important; color: #fff; }
.mesin-table .bg-success { background-color: #28a745 !important; color: #fff; }
</style>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <?php if ($is_ppic_context ?? false) : ?>
            <?= view('ppic/partials/machine_links', ['current_machine' => $current_machine]) ?>
        <?php endif; ?>
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="<?= base_url($back_url ?? 'gudang') ?>" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <?php if ($can_edit ?? false) : ?>
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
                <a href="<?= base_url(($export_url ?? 'export/mesin_ips3')) . ((!empty($q) || !empty($start_date) || !empty($end_date)) ? '?' . http_build_query(array_filter(['q' => $q ?? '', 'start_date' => $start_date ?? '', 'end_date' => $end_date ?? ''])) : '') ?>" class="btn btn-outline-success btn-sm px-3 ml-2">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
                <button class="btn btn-outline-secondary btn-sm ml-2" data-toggle="modal" data-target="#trashModalMesin" onclick="loadTrashMesin()">
                    <i class="fas fa-trash-restore"></i> Sampah
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

    <!-- MESIN_CONTENT_START -->
<div class="card-body" id="mesin-content">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
        <?php endif; ?>
        <!-- Top scroll mirror -->
        <div class="mesin-table-scroll-top" id="mesinScrollTop"><div class="mesin-table-scroll-inner" id="mesinScrollTopInner"></div></div>
        <div class="table-responsive mesin-table-wrap" id="mesinTableWrap">
            <table class="table table-bordered table-sm text-center mesin-table" cellspacing="0" id="mesinTable">
                                <thead>
                    <tr class="font-weight-bold bg-light">
                        <th rowspan="2">Tanggal</th>
                        <th rowspan="2">No. SPK</th>
                        <th rowspan="2">Status SPK</th>
                        <th rowspan="2">Nama Produk</th>
                        <th rowspan="2">Brand</th>
                        <th rowspan="2">Warna</th>
                        <th rowspan="2">Mesin</th>
                        <th rowspan="2">Status</th>
                        <th colspan="2" class="bg-warning text-dark">Jam Status</th>
                        <th colspan="2" class="bg-info text-white">Waktu Target Produksi</th>
                        <th colspan="4" class="bg-info text-white">Jumlah Target (pcs)</th>
                        <?php if (!($is_ppic_context ?? false)) : ?>
                        <th colspan="2" class="bg-success text-white">Waktu Realisasi Produksi</th>
                        <?php endif; ?>
                        <th colspan="<?= ($is_ppic_context ?? false) ? 4 : 3 ?>" class="bg-success text-white">
                            Jumlah Realisasi Produksi (pcs)
                            <?php if ($is_ppic_context ?? false) : ?>
                            <br><small class="font-weight-normal">(dari Produksi)</small>
                            <?php endif; ?>
                        </th>
                        <th rowspan="2">Berat (gr)</th>
                        <th rowspan="2">Komposisi</th>
                        <th rowspan="2">Total SPK</th>
                        <th rowspan="2">Packing</th>
                        <th rowspan="2">Keterangan</th>
                        <th rowspan="2">#</th>
                    </tr>
                    <tr class="font-weight-bold">
                        <th class="bg-warning text-dark">Awal</th>
                        <th class="bg-warning text-dark">Akhir</th>
                        <th class="bg-info text-white">Awal</th>
                        <th class="bg-info text-white">Akhir</th>
                        <th class="bg-info text-white">Shift 1<br><small>06.00–14.00</small></th>
                        <th class="bg-info text-white">Shift 2<br><small>14.00–22.00</small></th>
                        <th class="bg-info text-white">Shift 3<br><small>22.00–06.00</small></th>
                        <th class="bg-info text-white">Shift Lainnya</th>
                        <?php if (!($is_ppic_context ?? false)) : ?>
                        <th class="bg-success text-white">Awal</th>
                        <th class="bg-success text-white">Akhir</th>
                        <?php endif; ?>
                        <th class="bg-success text-white">Shift 1<br><small>06.00–14.00</small></th>
                        <th class="bg-success text-white">Shift 2<br><small>14.00–22.00</small></th>
                        <th class="bg-success text-white">Shift 3<br><small>22.00–06.00</small></th>
                        <?php if ($is_ppic_context ?? false) : ?>
                        <th class="bg-success text-white">Shift Lainnya</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                                <tbody>
                    <?php if (!empty($data_list)) : ?>
                        <?php foreach ($data_list as $item) : ?>
                            <?php
                            // Helper to render a realisasi cell in ppic context
                            $renderRealisasi = static function ($val): string {
                                if ($val === null) {
                                    return '<td class="text-center text-muted" '
                                        . 'title="Belum ada data di modul Produksi">'
                                        . '<span style="letter-spacing:.05em">—</span></td>';
                                }
                                return '<td class="font-weight-bold text-success text-center">'
                                    . number_format((int) $val)
                                    . '</td>';
                            };
                            ?>
                            <tr>
                                <td><?= esc($formatDate($item['tanggal'])) ?></td>
                                <td style="color: darkgreen;"><?= esc($item['no_spk'] ?? '-') ?></td>
                                <td class="text-center">
                                    <?php if (!empty($item['no_spk'])) : ?>
                                        <?php if ($item['selesai_spk'] == 1) : ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle mr-1"></i>Selesai
                                            </span>
                                        <?php else : ?>
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock mr-1"></i>Proses
                                            </span>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($item['produk_preform'] ?? '-') ?></td>
                                <td><?= esc($item['brand'] ?? '-') ?></td>
                                <td><?= esc($item['warna'] ?? '-') ?></td>
                                <td><?= esc($item['nomor_mesin'] ?? '-') ?></td>
                                <td><?= esc(ucwords(str_replace('_', ' ', $item['status'] ?? '-'))) ?></td>
                                <td><?= esc(isset($item['jam_awal']) && strlen((string)$item['jam_awal']) >= 5 ? substr((string)$item['jam_awal'], 0, 5) : ($item['jam_awal'] ?? '-')) ?></td>
                                <td><?= esc(isset($item['jam_akhir']) && strlen((string)$item['jam_akhir']) >= 5 ? substr((string)$item['jam_akhir'], 0, 5) : ($item['jam_akhir'] ?? '-')) ?></td>
                                <td><?= esc($item['jam'] ?? '-') ?></td>
                                <td><?= esc($item['waktu_akhir_target'] ?? '-') ?></td>
                                <td><?= esc($item['target_shift1'] ?? '-') ?></td>
                                <td><?= esc($item['target_shift2'] ?? '-') ?></td>
                                <td><?= esc($item['target_shift3'] ?? '-') ?></td>
                                <?php
                                $extraShifts = [];
                                if (!empty($item['extra_target_shifts'])) {
                                    $decoded = json_decode($item['extra_target_shifts'], true);
                                    if (is_array($decoded)) { $extraShifts = $decoded; }
                                }
                                ?>
                                <td>
                                    <?php if (!empty($extraShifts)) : ?>
                                        <?php foreach ($extraShifts as $es) : ?>
                                            <span class="badge badge-info d-block mb-1" style="white-space:nowrap;">
                                                <?= esc($es['label'] ?? '-') ?>: <?= esc($es['target'] ?? '-') ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($is_ppic_context ?? false) : ?>
                                <?= $renderRealisasi($item['produksi_realisasi_shift1'] ?? null) ?>
                                <?= $renderRealisasi($item['produksi_realisasi_shift2'] ?? null) ?>
                                <?= $renderRealisasi($item['produksi_realisasi_shift3'] ?? null) ?>
                                <?php
                                // Render extra realisasi shifts
                                $extraRealisasi = [];
                                if (!empty($item['extra_realisasi_shifts'])) {
                                    $decoded = json_decode($item['extra_realisasi_shifts'], true);
                                    if (is_array($decoded)) {
                                        $extraRealisasi = $decoded;
                                    }
                                }
                                ?>
                                <td>
                                    <?php if (!empty($extraRealisasi)) : ?>
                                        <?php foreach ($extraRealisasi as $er) : ?>
                                            <span class="badge badge-success d-block mb-1" style="white-space:nowrap;">
                                                <?= esc($er['label'] ?? '-') ?>: <?= esc($er['realisasi'] ?? '-') ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <?php else : ?>
                                <td><?= esc($item['waktu_awal_realisasi'] ?? '-') ?></td>
                                <td><?= esc($item['waktu_akhir_realisasi'] ?? '-') ?></td>
                                <td><?= esc($item['realisasi_shift1'] ?? '-') ?></td>
                                <td><?= esc($item['realisasi_shift2'] ?? '-') ?></td>
                                <td><?= esc($item['realisasi_shift3'] ?? '-') ?></td>
                                <?php endif; ?>
                                <td><?= esc($item['berat'] ?? '-') ?></td>
                                <td class="text-left" style="min-width:250px;white-space:normal;"><?= esc($item['revisi'] ?? '-') ?></td>
                                <td><?= esc($item['total_spk'] ?? '-') ?></td>
                                <td><?= esc($item['packing'] ?? '-') ?></td>
                                <td><?= esc($item['keterangan'] ?? '-') ?></td>
                                <td>
                                    <?php if ($can_edit ?? false) : ?>
                                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                                            <?php if (!empty($item['no_spk'])) : ?>
                                            <button type="button"
                                                class="btn btn-sm <?= ($item['selesai_spk'] == 1) ? 'btn-success' : 'btn-outline-success' ?> mb-1"
                                                onclick="toggleSpkStatus(this)"
                                                data-toggle="modal" data-target="#confirmSpkModal"
                                                data-id="<?= esc($item['id']) ?>"
                                                data-spk="<?= esc($item['no_spk']) ?>"
                                                data-current-status="<?= esc($item['selesai_spk'] ?? 0) ?>"
                                                data-action="<?= site_url((($is_ppic_context ?? false) ? 'ppic/mesin_ips3/completeSpk/' : 'gudang/mesin_ips3/completeSpk/') . ($item['id'] ?? 0)) ?>"
                                                title="<?= ($item['selesai_spk'] == 1) ? 'Ubah ke Belum Selesai' : 'Tandai Selesai' ?>">
                                                <i class="fas fa-<?= ($item['selesai_spk'] == 1) ? 'undo' : 'check' ?>"></i>
                                                <?= ($item['selesai_spk'] == 1) ? 'Batal' : 'Selesai' ?>
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-warning btn-sm edit-btn mb-1" onclick="setEditModal(this)" data-toggle="modal" data-target="#editDataModal"
                                                data-id="<?= esc($item['id']) ?>" data-tanggal="<?= esc($item['tanggal'] ?? '') ?>" data-spk="<?= esc($item['no_spk'] ?? '') ?>"
                                                data-nomor_mesin="<?= esc($item['nomor_mesin'] ?? '') ?>" data-shif="<?= esc($item['shif'] ?? '') ?>"
                                                data-status="<?= esc($item['status'] ?? 'ganti bottom rata') ?>"
                                                data-jam_awal="<?= esc($item['jam_awal'] ?? '') ?>" data-jam_akhir="<?= esc($item['jam_akhir'] ?? '') ?>"
                                                data-produk="<?= esc($item['produk_preform'] ?? '') ?>" data-brand="<?= esc($item['brand'] ?? '') ?>"
                                                data-warna="<?= esc($item['warna'] ?? '') ?>" data-berat="<?= esc($item['berat'] ?? '') ?>"
                                                data-jam="<?= esc($item['jam'] ?? '') ?>" data-waktu_akhir_target="<?= esc($item['waktu_akhir_target'] ?? '') ?>"
                                                data-target_shift1="<?= esc($item['target_shift1'] ?? '') ?>" data-target_shift2="<?= esc($item['target_shift2'] ?? '') ?>" data-target_shift3="<?= esc($item['target_shift3'] ?? '') ?>"
                                                data-extra_target_shifts="<?= esc($item['extra_target_shifts'] ?? '') ?>"
                                <?php if (!($is_ppic_context ?? false)) : ?>
                                                data-waktu_awal_realisasi="<?= esc($item['waktu_awal_realisasi'] ?? '') ?>" data-waktu_akhir_realisasi="<?= esc($item['waktu_akhir_realisasi'] ?? '') ?>"
                                                data-realisasi_shift1="<?= esc($item['realisasi_shift1'] ?? '') ?>" data-realisasi_shift2="<?= esc($item['realisasi_shift2'] ?? '') ?>" data-realisasi_shift3="<?= esc($item['realisasi_shift3'] ?? '') ?>"
                                <?php endif; ?>
                                                data-revisi="<?= esc($item['revisi'] ?? '') ?>" data-total_spk="<?= esc($item['total_spk'] ?? '') ?>" data-packing="<?= esc($item['packing'] ?? '') ?>" data-keterangan="<?= esc($item['keterangan'] ?? '') ?>"
                                                data-action="<?= site_url((($is_ppic_context ?? false) ? 'ppic/mesin_ips3/update/' : 'gudang/mesin_ips3/update/') . ($item['id'] ?? 0)) ?>"><i class="fas fa-edit"></i></button>
                                            <button type="button" class="btn btn-danger btn-sm ml-1" onclick="setDeleteModal(this)" data-toggle="modal" data-target="#modalDelete"
                                                data-id="<?= esc($item['id'] ?? 0) ?>" data-nama="<?= esc($item['produk_preform'] ?? '') ?>"
                                                data-action="<?= site_url((($is_ppic_context ?? false) ? 'ppic/mesin_ips3/delete/' : 'gudang/mesin_ips3/delete/') . ($item['id'] ?? 0)) ?>"><i class="fas fa-trash"></i></button>
                                        </div>
                                    <?php else : ?><span class="text-muted">-</span><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="21" class="text-center">Tidak ada data</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MESIN_CONTENT_END -->
<!-- Modals -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Data Mesin IPS 3</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
    <form id="formTambahMesin" action="<?= base_url(($is_ppic_context ?? false) ? 'ppic/' . ($current_machine ?? 'mesin_ips3') . '/tambahData' : 'gudang/mesin_ips3/tambahData') ?>" method="post"><?= csrf_field() ?>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-2"><div class="form-group"><label>Tanggal *</label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required></div></div>
          <div class="col-md-2"><div class="form-group"><label>No. SPK</label><input type="number" class="form-control" name="no_spk"></div></div>
          <div class="col-md-2"><div class="form-group"><label>No Mesin</label><input type="text" class="form-control" name="nomor_mesin" placeholder="cth: I.01"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Status</label><select class="form-control" name="status"><option value="ganti bottom rata">Ganti Bottom Rata</option><option value="ganti bottom reguler">Ganti Bottom Reguler</option><option value="ganti grade">Ganti Grade</option><option value="ganti mould aguaria">Ganti Mould Aguaria</option><option value="ganti mould reguler">Ganti Mould Reguler</option><option value="ganti mould pristine">Ganti Mould Pristine</option><option value="ganti mould 10,9 gr">Ganti Mould 10,9 gr</option><option value="ganti mould 15,8 gr">Ganti Mould 15,8 gr</option><option value="ganti mould ln">Ganti Mould LN</option><option value="ganti mould sn">Ganti Mould SN</option><option value="ganti bahan">Ganti Bahan</option><option value="ganti bahan & warna">GANTI BAHAN & WARNA</option><option value="bongkar as">BONGKAR AS</option><option value="ganti embos">GANTI EMBOS</option><option value="ganti warna">GANTI WARNA</option><option value="ganti packing">GANTI PACKING</option><option value="Ganti Packing">Ganti Packing</option><option value="Ganti SPK">Ganti SPK</option></select></div></div>
          <div class="col-md-2"><div class="form-group"><label>Jam Awal</label><input type="time" class="form-control" name="jam_awal"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Jam Akhir</label><input type="time" class="form-control" name="jam_akhir"></div></div>
        </div>
        <div class="row">
          <div class="col-md-4"><div class="form-group"><label>Nama Produk (Preform)</label><input type="text" class="form-control" name="produk_preform"></div></div>
          <div class="col-md-4"><div class="form-group"><label>Brand</label><input type="text" class="form-control" name="brand"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat"></div></div>
        </div>
        <hr class="my-2"><h6 class="text-info font-weight-bold mb-1">Waktu &amp; Jumlah Target Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Target</label><input type="datetime-local" class="form-control" name="jam"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Target</label><input type="datetime-local" class="form-control" name="waktu_akhir_target"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 1</label><input type="number" class="form-control" name="target_shift1" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 2</label><input type="number" class="form-control" name="target_shift2" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 3</label><input type="number" class="form-control" name="target_shift3" placeholder="pcs"></div></div>
        </div>
        <div id="add_extra_shifts_container"></div>
        <button type="button" class="btn btn-outline-info btn-sm mb-3" onclick="addExtraShiftRow('add_extra_shifts_container')"><i class="fas fa-plus mr-1"></i> Tambah Shift</button>
        <?php if (!($is_ppic_context ?? false)) : ?>
        <hr class="my-2"><h6 class="text-success font-weight-bold mb-1">Waktu &amp; Jumlah Realisasi Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Realisasi</label><input type="datetime-local" class="form-control" name="waktu_awal_realisasi"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Realisasi</label><input type="datetime-local" class="form-control" name="waktu_akhir_realisasi"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 1</label><input type="number" class="form-control" name="realisasi_shift1" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 2</label><input type="number" class="form-control" name="realisasi_shift2" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 3</label><input type="number" class="form-control" name="realisasi_shift3" placeholder="pcs"></div></div>
        </div>
        <?php endif; ?>
        <hr class="my-2">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Komposisi</label>
              <div class="input-group">
                <select class="form-control" name="revisi" id="komposisi">
                  <option value="">-- Pilih Komposisi --</option>
                  <?php foreach (($ips3_komposisi ?? []) as $k) : ?>
                    <option value="<?= esc($k) ?>"><?= esc($k) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="btn_tambah_komposisi_add"
                    onclick="openTambahKomposisiModal('komposisi')" title="Tambah komposisi baru">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3"><div class="form-group"><label>Total SPK</label><input type="text" class="form-control" name="total_spk" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Packing</label><input type="text" class="form-control" name="packing" placeholder="cth: 5-5"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Keterangan</label><input type="text" class="form-control" name="keterangan"></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" id="btnTambahMesin" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button></div>
    </form>
  </div></div>
</div>

<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
    <div class="modal-header bg-warning"><h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Data Mesin IPS 3</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
    <form id="formEditMesin" method="post" action="<?= base_url(($is_ppic_context ?? false) ? 'ppic/mesin_ips3/update/' : 'gudang/mesin_ips3/update/') ?>"><?= csrf_field() ?><input type="hidden" name="id" id="edit_id">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-2"><div class="form-group"><label>Tanggal *</label><input type="date" class="form-control" name="tanggal" id="edit_tanggal" required></div></div>
          <div class="col-md-2"><div class="form-group"><label>No. SPK</label><input type="number" class="form-control" name="no_spk" id="edit_spk"></div></div>
          <div class="col-md-2"><div class="form-group"><label>No Mesin</label><input type="text" class="form-control" name="nomor_mesin" id="edit_nomor_mesin"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Status</label><select class="form-control" name="status" id="edit_status"><option value="ganti bottom rata">Ganti Bottom Rata</option><option value="ganti bottom reguler">Ganti Bottom Reguler</option><option value="ganti grade">Ganti Grade</option><option value="ganti mould aguaria">Ganti Mould Aguaria</option><option value="ganti mould reguler">Ganti Mould Reguler</option><option value="ganti mould pristine">Ganti Mould Pristine</option><option value="ganti mould 10,9 gr">Ganti Mould 10,9 gr</option><option value="ganti mould 15,8 gr">Ganti Mould 15,8 gr</option><option value="ganti mould ln">Ganti Mould LN</option><option value="ganti mould sn">Ganti Mould SN</option><option value="ganti bahan">Ganti Bahan</option><option value="ganti bahan & warna">GANTI BAHAN & WARNA</option><option value="bongkar as">BONGKAR AS</option><option value="ganti embos">GANTI EMBOS</option><option value="ganti warna">GANTI WARNA</option><option value="ganti packing">GANTI PACKING</option><option value="Ganti SPK">Ganti SPK</option></select></div></div>
          <div class="col-md-2"><div class="form-group"><label>Jam Awal</label><input type="time" class="form-control" name="jam_awal" id="edit_jam_awal"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Jam Akhir</label><input type="time" class="form-control" name="jam_akhir" id="edit_jam_akhir"></div></div>
        </div>
        <div class="row">
          <div class="col-md-4"><div class="form-group"><label>Nama Produk (Preform)</label><input type="text" class="form-control" name="produk_preform" id="edit_produk"></div></div>
          <div class="col-md-4"><div class="form-group"><label>Brand</label><input type="text" class="form-control" name="brand" id="edit_brand"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna" id="edit_warna"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat" id="edit_berat"></div></div>
        </div>
        <hr class="my-2"><h6 class="text-info font-weight-bold mb-1">Waktu &amp; Jumlah Target Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Target</label><input type="datetime-local" class="form-control" name="jam" id="edit_jam"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Target</label><input type="datetime-local" class="form-control" name="waktu_akhir_target" id="edit_waktu_akhir_target"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 1</label><input type="number" class="form-control" name="target_shift1" id="edit_target_shift1"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 2</label><input type="number" class="form-control" name="target_shift2" id="edit_target_shift2"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 3</label><input type="number" class="form-control" name="target_shift3" id="edit_target_shift3"></div></div>
        </div>
        <!-- Dynamic extra shifts -->
        <div id="edit_extra_shifts_container"></div>
        <button type="button" class="btn btn-outline-info btn-sm mb-3" onclick="addExtraShiftRow('edit_extra_shifts_container')"><i class="fas fa-plus mr-1"></i> Tambah Shift</button>
        <?php if (!($is_ppic_context ?? false)) : ?>
        <hr class="my-2"><h6 class="text-success font-weight-bold mb-1">Waktu &amp; Jumlah Realisasi Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Realisasi</label><input type="datetime-local" class="form-control" name="waktu_awal_realisasi" id="edit_waktu_awal_real"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Realisasi</label><input type="datetime-local" class="form-control" name="waktu_akhir_realisasi" id="edit_waktu_akhir_real"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 1</label><input type="number" class="form-control" name="realisasi_shift1" id="edit_real1"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 2</label><input type="number" class="form-control" name="realisasi_shift2" id="edit_real2"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 3</label><input type="number" class="form-control" name="realisasi_shift3" id="edit_real3"></div></div>
        </div>
        <?php endif; ?>
        <hr class="my-2">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>Komposisi</label>
              <div class="input-group">
                <select class="form-control" name="revisi" id="edit_komposisi">
                  <option value="">-- Pilih Komposisi --</option>
                  <?php foreach (($ips3_komposisi ?? []) as $k) : ?>
                    <option value="<?= esc($k) ?>"><?= esc($k) ?></option>
                  <?php endforeach; ?>
                </select>
                <div class="input-group-append">
                  <button type="button" class="btn btn-outline-primary btn-sm" id="btn_tambah_komposisi_edit"
                    onclick="openTambahKomposisiModal('edit_komposisi')" title="Tambah komposisi baru">
                    <i class="fas fa-plus"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3"><div class="form-group"><label>Total SPK</label><input type="text" class="form-control" name="total_spk" id="edit_total_spk"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Packing</label><input type="text" class="form-control" name="packing" id="edit_packing"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Keterangan</label><input type="text" class="form-control" name="keterangan" id="edit_keterangan"></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" id="btnEditMesin" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update</button></div>
    </form>
  </div></div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <form id="formDelete" method="post"><?= csrf_field() ?><input type="hidden" name="id" id="delete_id">
      <div class="modal-body text-center"><p id="delete_message"></p></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger btn-sm">Hapus</button></div>
    </form>
  </div></div>
</div>

<script>
    const toDatetimeLocal = v => v ? v.replace(' ', 'T').substring(0, 16) : '';
    const toTimeLocal = v => {
        if (!v) return '';
        const str = String(v).trim();
        if (str.length >= 5) return str.substring(0, 5);
        return str;
    };
    function setEditModal(btn) {
        const data = btn;
        document.getElementById('edit_id').value = data.getAttribute('data-id');
        document.getElementById('edit_tanggal').value = data.getAttribute('data-tanggal');
        document.getElementById('edit_spk').value = data.getAttribute('data-spk');
        document.getElementById('edit_nomor_mesin').value = data.getAttribute('data-nomor_mesin') || '';
        document.getElementById('edit_status').value = data.getAttribute('data-status') || 'ganti bottom rata';
        document.getElementById('edit_jam_awal').value = toTimeLocal(data.getAttribute('data-jam_awal') || '');
        document.getElementById('edit_jam_akhir').value = toTimeLocal(data.getAttribute('data-jam_akhir') || '');
        document.getElementById('edit_produk').value = data.getAttribute('data-produk');
        document.getElementById('edit_brand').value = data.getAttribute('data-brand');
        document.getElementById('edit_warna').value = data.getAttribute('data-warna');
        document.getElementById('edit_berat').value = data.getAttribute('data-berat') || '';
        document.getElementById('edit_jam').value = toDatetimeLocal(data.getAttribute('data-jam'));
        document.getElementById('edit_waktu_akhir_target').value = toDatetimeLocal(data.getAttribute('data-waktu_akhir_target'));
        document.getElementById('edit_target_shift1').value = data.getAttribute('data-target_shift1') || '';
        document.getElementById('edit_target_shift2').value = data.getAttribute('data-target_shift2') || '';
        document.getElementById('edit_target_shift3').value = data.getAttribute('data-target_shift3') || '';
        // Load extra shifts from JSON
        const container = document.getElementById('edit_extra_shifts_container');
        container.innerHTML = '';
        const extraRaw = data.getAttribute('data-extra_target_shifts') || '';
        if (extraRaw) {
            try {
                const extras = JSON.parse(extraRaw);
                if (Array.isArray(extras)) {
                    extras.forEach(function(es) {
                        addExtraShiftRow('edit_extra_shifts_container', es.label || '', es.target || '');
                    });
                }
            } catch (e) { /* invalid JSON, skip */ }
        }
        // waktu realisasi — only present in gudang context, guard against null
        const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = val; };
        setVal('edit_waktu_awal_real', toDatetimeLocal(data.getAttribute('data-waktu_awal_realisasi')));
        setVal('edit_waktu_akhir_real', toDatetimeLocal(data.getAttribute('data-waktu_akhir_realisasi')));
        setVal('edit_real1', data.getAttribute('data-realisasi_shift1') || '');
        setVal('edit_real2', data.getAttribute('data-realisasi_shift2') || '');
        setVal('edit_real3', data.getAttribute('data-realisasi_shift3') || '');
        document.getElementById('edit_komposisi').value = data.getAttribute('data-revisi') || '';
        document.getElementById('edit_total_spk').value = data.getAttribute('data-total_spk') || '';
        document.getElementById('edit_packing').value = data.getAttribute('data-packing') || '';
        document.getElementById('edit_keterangan').value = data.getAttribute('data-keterangan') || '';
        document.getElementById('formEditMesin').action = data.getAttribute('data-action');
    }
    function setDeleteModal(btn) {
        document.getElementById('delete_id').value = btn.dataset.id;
        document.getElementById('delete_message').textContent = 'Hapus data "' + btn.dataset.nama + '"?';
        document.getElementById('formDelete').action = btn.dataset.action;
    }
    function toggleFilter(show) {
        if (show) { $('.filter-sidebar').addClass('active'); $('.filter-overlay').addClass('active'); }
        else { $('.filter-sidebar').removeClass('active'); $('.filter-overlay').removeClass('active'); }
    }

    // -----------------------------------------------------------------------
    // Dynamic extra shift rows
    // -----------------------------------------------------------------------
    let _extraShiftCounter = 0;

    function addExtraShiftRow(containerId, labelVal, targetVal) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const idx = _extraShiftCounter++;
        const row = document.createElement('div');
        row.className = 'row align-items-center mb-1 extra-shift-row';
        row.innerHTML =
            '<div class="col-md-5">' +
                '<div class="input-group input-group-sm">' +
                    '<div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>' +
                    '<input type="text" class="form-control" name="extra_shift_label[]" placeholder="Nama shift (cth: Shift 4)" value="' + escHtml(labelVal || '') + '">' +
                '</div>' +
            '</div>' +
            '<div class="col-md-4">' +
                '<div class="input-group input-group-sm">' +
                    '<input type="number" class="form-control" name="extra_shift_target[]" placeholder="Target (pcs)" value="' + escHtml(targetVal || '') + '">' +
                    '<div class="input-group-append"><span class="input-group-text">pcs</span></div>' +
                '</div>' +
            '</div>' +
            '<div class="col-md-3">' +
                '<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeExtraShiftRow(this)">' +
                    '<i class="fas fa-times mr-1"></i>Hapus' +
                '</button>' +
            '</div>';
        container.appendChild(row);
    }

    function removeExtraShiftRow(btn) {
        const row = btn.closest('.extra-shift-row');
        if (row) row.remove();
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // Clear extra shifts when Tambah modal opens
    document.addEventListener('DOMContentLoaded', function() {
        var addModal = document.getElementById('addDataModal');
        if (addModal) {
            addModal.addEventListener('show.bs.modal', function() {
                document.getElementById('add_extra_shifts_container').innerHTML = '';
            });
            // Bootstrap 4 fallback
            $(addModal).on('show.modal, show.bs.modal', function() {
                document.getElementById('add_extra_shifts_container').innerHTML = '';
            });
        }
    });

    // SPK Completion Functions
    function toggleSpkStatus(button) {
        const spkId = button.getAttribute('data-id');
        const spkNo = button.getAttribute('data-spk');
        const currentStatus = button.getAttribute('data-current-status');
        const action = button.getAttribute('data-action');
        document.getElementById('spk_id').value = spkId;
        document.getElementById('formCompleteSpk').action = action;
        const isCompleted = currentStatus == '1';
        const newAction = isCompleted ? 'membatalkan status selesai' : 'menandai sebagai selesai';
        const confirmButton = document.getElementById('confirmSpkBtn');
        document.getElementById('spk_message').innerHTML =
            `Apakah Anda yakin ingin <strong>${newAction}</strong> SPK <strong>${spkNo}</strong>?`;
        if (isCompleted) {
            confirmButton.className = 'btn btn-warning btn-sm';
            confirmButton.innerHTML = '<i class="fas fa-undo mr-1"></i>Ya, Batalkan';
        } else {
            confirmButton.className = 'btn btn-success btn-sm';
            confirmButton.innerHTML = '<i class="fas fa-check mr-1"></i>Ya, Selesai';
        }
    }

    // Load Trash Data
    function loadTrashMesin() {
        const tbody = document.querySelector('#trashTableMesin tbody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';
        
        const machineType = '<?= $current_machine ?? 'mesin_ips3' ?>';  // For PPIC context
        const url = '<?= base_url((($is_ppic_context ?? false) ? 'ppic/' : 'gudang/') . ($current_machine ?? 'mesin_ips3') . '/get-trash') ?>';
        console.log('Fetching trash from:', url);
        
        fetch(url)
            .then(r => {
                console.log('Response status:', r.status);
                if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
                return r.json();
            })
            .then(data => {
                console.log('Trash data:', data);
                tbody.innerHTML = '';
                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">Tempat sampah kosong.</td></tr>';
                    return;
                }
                data.forEach(item => {
                    const baseUrl = '<?= (($is_ppic_context ?? false) ? base_url('ppic/' . ($current_machine ?? 'mesin_ips3')) : base_url('gudang/' . ($current_machine ?? 'mesin_ips3'))) ?>';
                    const productField = item.produk_cap_galon || item.produk_preform || item.produk_galon || '-';
                    const html = `<tr style="font-size: 13px;">
                        <td class="px-3 py-2">
                            <div class="font-weight-bold">${productField}</div>
                            <div class="text-muted" style="font-size: 11px;">Grade: ${item.grade || '-'}</div>
                        </td>
                        <td class="py-2">${item.nomor_mesin || '-'}</td>
                        <td class="py-2">${item.no_spk || '-'}</td>
                        <td class="py-2 text-muted text-center">${item.deleted_at}</td>
                        <td class="text-end py-2 px-3">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="${baseUrl}/restore/${item.id}" class="btn btn-success btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" title="Pulihkan">
                                    <i class="fas fa-undo"></i>
                                </a>
                                <a href="${baseUrl}/delete-permanent/${item.id}" class="btn btn-outline-danger btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" title="Hapus Permanen" onclick="return confirm('Hapus permanen? Tidak dapat dikembalikan!')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </td>
                    </tr>`;
                    tbody.insertAdjacentHTML('beforeend', html);
                });
            })
            .catch(err => {
                console.error('Error loading trash:', err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-danger">Gagal memuat data sampah: ' + err.message + '</td></tr>';
            });
    }

    // Initialize modals
    document.addEventListener('DOMContentLoaded', function() {
        // Trash modal handler
        const trashModal = document.getElementById('trashModalMesin');
        if (trashModal) {
            trashModal.addEventListener('show.modal', loadTrashMesin);
            console.log('Trash modal event listener registered (Bootstrap 4)');
        }
    });

    // Top scrollbar mirror
    document.addEventListener('DOMContentLoaded', function () {
        var topBar    = document.getElementById('mesinScrollTop');
        var topInner  = document.getElementById('mesinScrollTopInner');
        var tableWrap = document.getElementById('mesinTableWrap');
        var table     = document.getElementById('mesinTable');
        if (!topBar || !topInner || !tableWrap || !table) return;
        function syncWidth() { topInner.style.width = table.scrollWidth + 'px'; }
        syncWidth();
        var syncing = false;
        topBar.addEventListener('scroll', function () { if (syncing) return; syncing = true; tableWrap.scrollLeft = topBar.scrollLeft; syncing = false; });
        tableWrap.addEventListener('scroll', function () { if (syncing) return; syncing = true; topBar.scrollLeft = tableWrap.scrollLeft; syncing = false; });
        window.addEventListener('resize', syncWidth);
    });

    // ──────────────────────────────────────────────────────────
    // KOMPOSISI management
    // ──────────────────────────────────────────────────────────
    const _komposisiApiBase = '<?= base_url(($is_ppic_context ?? false) ? 'ppic/mesin_ips3' : 'gudang/mesin_ips3') ?>';
    let   _activeKomposisiSelectId = '';

    // Daftar komposisi aktif di memory (di-seed dari PHP)
    let _komposisiList = <?= json_encode($ips3_komposisi ?? [], JSON_UNESCAPED_UNICODE) ?>;

    function openTambahKomposisiModal(selectId) {
        _activeKomposisiSelectId = selectId;
        document.getElementById('tk_komposisi_name').value = '';
        document.getElementById('tk_feedback').textContent  = '';
        document.getElementById('tk_feedback').className    = 'mt-1 small';
        renderKomposisiList(_komposisiList);
        $('#tambahKomposisiModal').modal('show');
    }

    function renderKomposisiList(list) {
        const ul = document.getElementById('tk_komposisi_list');
        ul.innerHTML = '';
        if (!list || list.length === 0) {
            ul.innerHTML = '<li class="list-group-item text-muted small">Belum ada komposisi.</li>';
            return;
        }
        list.forEach(function(k) {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center py-1 px-2 small';
            li.innerHTML = '<span>' + escHtml(k) + '</span>';
            ul.appendChild(li);
        });
    }

    function submitTambahKomposisi() {
        const nama    = document.getElementById('tk_komposisi_name').value.trim();
        const feedback = document.getElementById('tk_feedback');
        const btn      = document.getElementById('tk_submit_btn');

        if (!nama) {
            feedback.textContent = 'Nama komposisi tidak boleh kosong.';
            feedback.className   = 'mt-1 small text-danger';
            return;
        }

        btn.disabled  = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';

        const formData = new FormData();
        formData.append('nama', nama);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch(_komposisiApiBase + '/store-komposisi', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    feedback.textContent = data.message;
                    feedback.className   = 'mt-1 small text-success';
                    document.getElementById('tk_komposisi_name').value = '';

                    // Update memory list
                    _komposisiList = data.komposisi;

                    // Refresh dropdown yang aktif (tambah atau edit)
                    refreshKomposisiSelect(_activeKomposisiSelectId, nama);

                    // Refresh daftar di modal
                    renderKomposisiList(_komposisiList);
                } else {
                    feedback.textContent = data.message;
                    feedback.className   = 'mt-1 small text-danger';
                }
            })
            .catch(() => {
                feedback.textContent = 'Terjadi kesalahan. Coba lagi.';
                feedback.className   = 'mt-1 small text-danger';
            })
            .finally(() => {
                btn.disabled  = false;
                btn.innerHTML = '<i class="fas fa-plus mr-1"></i>Tambah';
            });
    }

    function refreshKomposisiSelect(selectId, selectedValue) {
        const sel = document.getElementById(selectId);
        if (!sel) return;

        // Simpan nilai terpilih saat ini sebelum rebuild
        const current = selectedValue || sel.value;

        // Kosongkan opsi lama (kecuali placeholder)
        while (sel.options.length > 1) sel.remove(1);

        // Isi ulang dari _komposisiList
        _komposisiList.forEach(function(k) {
            const opt = document.createElement('option');
            opt.value       = k;
            opt.textContent = k;
            if (k === current) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    // Allow pressing Enter in komposisi input
    document.addEventListener('DOMContentLoaded', function () {
        const inp = document.getElementById('tk_komposisi_name');
        if (inp) inp.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); submitTambahKomposisi(); }
        });

        // Seed dropdown tambah & edit dari memory list saat halaman load
        refreshKomposisiSelect('komposisi', '');
        refreshKomposisiSelect('edit_komposisi', '');
    });</script>
<?= view('shared/spk_produksi_autofill') ?>

<!-- SPK Confirmation Modal -->
<div class="modal fade" id="confirmSpkModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-check-circle mr-2"></i>Konfirmasi SPK</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formCompleteSpk" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="spk_id">
                <div class="modal-body text-center">
                    <p id="spk_message"></p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        <small>Status SPK dapat diubah kembali jika diperlukan</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm" id="confirmSpkBtn">
                        <i class="fas fa-check mr-1"></i>Ya, Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Trash Modal -->
<div class="modal fade" id="trashModalMesin" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-secondary">
                    <i class="fas fa-trash-alt mr-2"></i> Tempat Sampah - Mesin IPS 3
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0" id="trashTableMesin" style="min-width: auto;">
                        <thead class="bg-white text-xs text-muted text-uppercase">
                            <tr>
                                <th class="px-3 py-3" style="width: 25%;">Produk</th>
                                <th class="py-3" style="width: 15%;">No Mesin</th>
                                <th class="py-3" style="width: 15%;">No SPK</th>
                                <th class="py-3 text-center" style="width: 25%;">Dihapus Pada</th>
                                <th class="text-end py-3 px-3" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="5" class="text-center py-3 text-muted">Klik tombol untuk memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Tambah Komposisi Modal -->
<div class="modal fade" id="tambahKomposisiModal" tabindex="-1" role="dialog" style="z-index: 1070;">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white py-2">
        <h6 class="modal-title font-weight-bold mb-0">
          <i class="fas fa-flask mr-2"></i>Kelola Komposisi
        </h6>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body pb-2">

        <!-- Tambah baru -->
        <div class="input-group input-group-sm mb-1">
          <input type="text" id="tk_komposisi_name" class="form-control" placeholder="Contoh: V:70%, OR+:30%" maxlength="200" autocomplete="off">
          <div class="input-group-append">
            <button class="btn btn-success btn-sm" type="button" id="tk_submit_btn" onclick="submitTambahKomposisi()">
              <i class="fas fa-plus mr-1"></i>Tambah
            </button>
          </div>
        </div>
        <div id="tk_feedback" class="mt-1 small"></div>

        <hr class="my-2">

        <!-- Daftar komposisi yang ada -->
        <p class="small text-muted mb-1">Komposisi yang sudah ada:</p>
        <ul id="tk_komposisi_list" class="list-group list-group-flush" style="max-height: 240px; overflow-y: auto; font-size: 0.82rem;">
          <li class="list-group-item text-muted small">Memuat...</li>
        </ul>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<?= view('gudang/partials/mesin_toast') ?>

<?= $this->endSection() ?>
