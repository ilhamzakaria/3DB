<?php
$isEdit = isset($header);
$prefix = $isEdit ? 'edit_' : '';

// On a mesin sub-page the controller passes context_mesin_slug so the
// add form is pre-locked to that mesin (SPK list is already filtered).
$contextMesinSlug  = $context_mesin_slug ?? '';
$contextMesinLabel = '';
if ($contextMesinSlug !== '' && !$isEdit) {
    $mesinLabelsMap = \App\Controllers\Produksi::mesinLabels();
    $contextMesinLabel = $mesinLabelsMap[$contextMesinSlug] ?? '';
}

// Flag: mesin yang menggunakan reject inline per jam (Becum, Powerjet 1, Powerjet 2)
// Dideteksi awal agar tersedia untuk tabel jam di atas section Material
if ($isEdit) {
    $_earlyMesinNama  = strtolower(str_replace([' ', '-', '_'], '', $header['nama_mesin'] ?? ''));
    $_earlyIsBecum    = (strpos($_earlyMesinNama, 'becum') === 0); // Check if starts with 'becum'
    $_earlyIsPowerjet = (strpos($_earlyMesinNama, 'powerjet') === 0);
} else {
    $_earlyIsBecum    = ($contextMesinSlug === 'becum');
    $_earlyIsPowerjet = in_array($contextMesinSlug, ['powerjet1', 'powerjet2']);
}
$isJamRejectMesin = $_earlyIsBecum || $_earlyIsPowerjet;

// Flag: Becum machine detection (untuk field-field spesifik Becum seperti Plash Kotor, Sapuan)
// Juga check dari SPK selected value untuk fallback
$isBecumMesin = $_earlyIsBecum;

// DEBUG: Log untuk verify detection
if ($isEdit) {
    error_log("DEBUG: Edit mode - nama_mesin={$header['nama_mesin']}, cleaned={$_earlyMesinNama}, isBecum={$isBecumMesin}");
}
?>

<div class="section-title"><i class="fas fa-info-circle"></i> Informasi Utama</div>
<div class="row g-3 mb-4">
    <!-- Row 1: 4 fields per row -->
    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Nomor SPK</label>
        <select name="nomor_spk" class="form-select select2 nomor_spk" required data-is-edit="<?= $isEdit ? '1' : '0' ?>">
            <option value="">-- Pilih SPK --</option>
            <?php foreach ($spk_list as $spk):
                $selesaiByPpic  = (int)($spk['selesai_spk'] ?? 0) === 1;
                $isCompleted    = $selesaiByPpic;
                $isDisabled     = $isCompleted ? 'disabled' : '';
                $disabledText   = $selesaiByPpic ? ' ✓ Selesai' : '';
                $isSelectedOption = ($isEdit && $spk['no_spk'] == $header['nomor_spk']) ? true : false;

                $displayText = $spk['no_spk'];
                if (!empty($spk['nama_mesin'])) {
                    $displayText .= ' | ' . $spk['nama_mesin'];
                }
                if (!empty($spk['nama_produk'])) {
                    $displayText .= ' | ' . $spk['nama_produk'];
                }
                $displayText .= $disabledText;
            ?>
                <option value="<?= $spk['no_spk'] ?>"
                    data-mesin="<?= esc($spk['nama_mesin']) ?>"
                    data-produk="<?= esc($spk['nama_produk']) ?>"
                    data-brand="<?= esc($spk['brand']) ?>"
                    data-grade="<?= esc($spk['grade']) ?>"
                    data-warna="<?= esc($spk['warna']) ?>"
                    data-nomor-mesin="<?= esc($spk['nomor_mesin']) ?>"
                    data-operator="<?= esc($spk['operator']) ?>"
                    data-targett="<?= esc($spk['targett']) ?>"
                    data-shif="<?= esc($spk['shif'] ?? '') ?>"
                    data-packing="<?= esc($spk['packing'] ?? '') ?>"
                    data-total-spk="<?= esc($spk['total_spk'] ?? '') ?>"
                    data-revisi="<?= esc($spk['revisi'] ?? '') ?>"
                    data-is-completed="<?= ($spk['is_completed'] ?? false) ? '1' : '0' ?>"
                    <?= $isDisabled ?>
                    <?= $isSelectedOption ? 'selected' : '' ?>>
                    <?= esc($displayText) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <small class="text-muted d-block mt-1">SPK Data: <?= count($spk_list) ?> items</small>
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Nama Produk</label>
        <input type="text" class="form-control nama_produk" placeholder="Nama Produk" value="<?= $isEdit ? esc($header['nama_produk']) : '' ?>">
        <input type="hidden" name="nama_produk" class="nama_produk_hidden" value="<?= $isEdit ? esc($header['nama_produk']) : '' ?>">
        <?php if ($contextMesinLabel !== ''): ?>
            <input type="hidden" name="nama_mesin" class="nama_mesin" value="<?= esc($contextMesinLabel) ?>">
            <small class="d-flex align-items-center gap-1 mt-1">
                <span class="badge bg-primary" style="font-size:.7rem;">
                    <i class="fas fa-cog me-1"></i><?= esc($contextMesinLabel) ?>
                </span>
            </small>
        <?php else: ?>
            <input type="hidden" name="nama_mesin" class="nama_mesin" value="<?= $isEdit ? esc($header['nama_mesin']) : '' ?>">
        <?php endif; ?>
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Batch Number</label>
        <input type="text" name="batch_number" class="form-control batch_number" placeholder="No. Batch" value="<?= $isEdit ? esc($header['batch_number']) : '' ?>">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Type Produk</label>
        <input type="text" name="type_produk" class="form-control" placeholder="Type Produk" value="<?= $isEdit ? esc($header['type_produk'] ?? '') : '' ?>">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Shift</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-clock text-muted"></i></span>
            <select class="form-select shift border-start-0 shift-selector" id="shift_select"
                <?= $isEdit ? 'data-is-edit="1"' : '' ?>
                data-saved-shift="<?= $isEdit && isset($header['shift']) ? esc($header['shift']) : '' ?>"
                data-saved-tercapai="<?= $isEdit && isset($header['tercapai']) ? esc($header['tercapai']) : '0' ?>">
                <option value="">-- Pilih Shift --</option>
                <option value="1">Shift 1</option>
                <option value="2">Shift 2</option>
                <option value="3">Shift 3</option>
            </select>
            <input type="hidden" name="shift" class="shift_hidden" value="<?= $isEdit ? trim(esc($header['shift'] ?? '')) : '' ?>">
            <input type="hidden" name="tercapai" class="tercapai_hidden" value="<?= $isEdit ? trim(esc($header['tercapai'] ?? '0')) : '0' ?>">
        </div>
    </div>

    <!-- Row 2: Brand, Warna, Grade, No. Mesin -->
    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Brand</label>
        <input type="text" class="form-control brand" placeholder="Auto-filled" disabled value="<?= $isEdit ? esc($header['brand'] ?? '') : '' ?>">
        <input type="hidden" name="brand" class="brand_hidden" value="<?= $isEdit ? esc($header['brand'] ?? '') : '' ?>">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Warna</label>
        <input type="text" class="form-control warna" placeholder="Auto-filled" disabled value="<?= $isEdit ? esc($header['warna'] ?? '') : '' ?>">
        <input type="hidden" name="warna" class="warna_hidden" value="<?= $isEdit ? esc($header['warna'] ?? '') : '' ?>">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Grade</label>
        <input type="text" class="form-control grade" placeholder="Auto-filled" disabled value="<?= $isEdit ? esc($header['grade'] ?? '') : '' ?>">
        <input type="hidden" name="grade" class="grade_hidden" value="<?= $isEdit ? esc($header['grade'] ?? '') : '' ?>">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">No. Mesin</label>
        <input type="text" class="form-control nomor_mesin" placeholder="M#" disabled value="<?= $isEdit ? esc($header['nomor_mesin'] ?? '') : '' ?>">
        <input type="hidden" name="nomor_mesin" class="nomor_mesin_hidden" value="<?= $isEdit ? esc($header['nomor_mesin'] ?? '') : '' ?>">
    </div>

    <!-- Row 3: Packing, Tanggal, Cycle Time, Target -->
    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Packing</label>
        <input type="text" class="form-control packing" placeholder="Tipe Packing" disabled value="<?= $isEdit ? esc($header['packing'] ?? '') : '' ?>">
        <input type="hidden" name="packing" class="packing_hidden" value="<?= $isEdit ? esc($header['packing'] ?? '') : '' ?>">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Tanggal</label>
        <input type="date" class="form-control tanggal_input" value="<?= $isEdit ? $header['tanggal'] : date('Y-m-d') ?>">
        <input type="hidden" name="tanggal" class="tanggal_hidden" value="<?= $isEdit ? $header['tanggal'] : date('Y-m-d') ?>">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Cycle Time (sec)</label>
        <div class="input-group">
            <input type="number" name="cycle_time" class="form-control cycle_time" placeholder="0.00" step="0.01" value="<?= $isEdit ? esc($header['cycle_time'] ?? '') : '' ?>">
            <span class="input-group-text bg-light small">s</span>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Target (pcs)</label>
        <input type="number" class="form-control target" placeholder="0" disabled value="<?= $isEdit ? esc($header['target'] ?? '') : '' ?>">
        <input type="hidden" name="target" class="target_hidden" value="<?= $isEdit ? esc($header['target'] ?? '') : '' ?>">
    </div>

    <div class="col-lg-3 col-md-4">
        <label class="form-label mb-2">Total SPK</label>
        <input type="number" class="form-control total_spk" placeholder="0" disabled value="<?= $isEdit ? esc($header['total_spk'] ?? '') : '' ?>">
        <input type="hidden" name="total_spk" class="total_spk_hidden" value="<?= $isEdit ? esc($header['total_spk'] ?? '') : '' ?>">
    </div>



    <!-- Row 4: Isi, Operator - same line -->
    <div class="col-lg-3 col-md-4">
        <label class="form-label mb-2">Isi</label>
        <input type="number" name="isi" class="form-control isi" placeholder="0" value="<?= $isEdit ? esc($header['isi'] ?? '') : '' ?>">
    </div>

    <div class="col-lg-3 col-md-4">
        <label class="form-label mb-2">Operator</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
            <input type="text" class="form-control operator border-start-0" placeholder="Nama Operator" disabled value="<?= $isEdit ? esc($header['operator'] ?? esc(session('username') ?? '')) : esc(session('username') ?? '') ?>">
            <input type="hidden" name="operator" class="operator_hidden" value="<?= $isEdit ? esc($header['operator'] ?? esc(session('username') ?? '')) : esc(session('username') ?? '') ?>">
        </div>
    </div>
</div>

<!-- 2x2 Grid Layout -->
<script>
    // Flag mesin Becum/Powerjet untuk reject inline per jam
    // Didefinisikan di sini (form_fields.php) karena scripts.php bisa dirender
    // di konteks berbeda tanpa akses ke $isJamRejectMesin
    // Pakai window property agar aman dari redeclaration
    window.IS_JAM_REJECT_MESIN = <?= ($isJamRejectMesin ?? false) ? 'true' : 'false' ?>;
</script>
<div class="row g-4 mb-4">
    <!-- Grid 1: Data Produksi Per Jam (Top Left) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <!-- Card Header -->
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 text-dark fw-600">
                        <i class="fas fa-clock text-primary me-2"></i> Data Produksi Per Jam
                    </h6>
                    <div class="input-group input-group-sm" style="width: fit-content;">
                        <select class="form-select select_jam form-select-sm" style="border-radius: 6px 0 0 6px; font-size: 0.875rem;">
                            <option value="">-- Pilih Jam --</option>
                            <?php
                            $hours = ['06-07', '07-08', '08-09', '09-10', '10-11', '11-12', '12-13', '13-14', '14-15', '15-16', '16-17', '17-18', '18-19', '19-20', '20-21', '21-22', '22-23', '23-00', '00-01', '01-02', '02-03', '03-04', '04-05', '05-06'];
                            foreach ($hours as $h): ?>
                                <option value="<?= $h ?>"><?= $h ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-primary btn-sm btn_add_jam_row" style="border-radius: 0 6px 6px 0;">
                            <i class="fas fa-plus me-1"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card Body with Table -->
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table_jam_form mb-0 align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px;">JAM</th>
                                <th class="px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px;">HASIL</th>
                                <?php if ($isJamRejectMesin): ?>
                                <th class="px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px; min-width: 220px;">REJECT</th>
                                <?php endif; ?>
                                <th class="text-center px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px; width: 50px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($isEdit && isset($jams)): ?>
                                <?php foreach ($jams as $idx => $jam): ?>
                                    <tr data-jam="<?= $jam['rentang_jam'] ?>" class="border-bottom" style="transition: background-color 0.2s;">
                                        <td class="px-4 py-3">
                                            <span class="badge bg-primary-light text-primary fw-600" style="font-size: 0.85rem;"><?= $jam['rentang_jam'] ?></span>
                                            <input type="hidden" name="jam_data[<?= $idx ?>][rentang_jam]" value="<?= esc($jam['rentang_jam']) ?>">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number"
                                                name="jam_data[<?= $idx ?>][hasil_produksi]"
                                                value="<?= $jam['hasil_produksi'] ?>"
                                                class="form-control form-control-sm jam-hasil"
                                                required
                                                placeholder="0"
                                                style="border-radius: 6px; border: 1px solid #dee2e6;">
                                        </td>
                                        <?php if ($isJamRejectMesin): ?>
                                        <td class="px-2 py-2 jam-reject-cell" data-jam="<?= esc($jam['rentang_jam']) ?>">
                                            <div class="jam-reject-list">
                                                <div class="jam-reject-row d-flex gap-1 mb-1 align-items-center">
                                                    <select class="form-select form-select-sm jam-reject-type flex-grow-1" style="font-size: 0.75rem; min-width: 110px;">
                                                        <option value="">-- Jenis --</option>
                                                        <?php foreach ($reject_types ?? [] as $rt): ?>
                                                            <option value="<?= esc($rt['nama_reject']) ?>"><?= esc($rt['nama_reject']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <input type="number" min="0"
                                                        class="form-control form-control-sm jam-reject-jumlah"
                                                        placeholder="0" style="width: 60px; font-size: 0.75rem;">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-remove-jam-reject"
                                                        style="padding: 0.2rem 0.4rem; font-size: 0.7rem;">✕</button>
                                                </div>
                                            </div>
                                            <button type="button"
                                                class="btn btn-link btn-sm btn-add-jam-reject p-0 mt-1"
                                                style="font-size: 0.72rem; color: #6c757d;">
                                                <i class="fas fa-plus me-1"></i>Reject
                                            </button>
                                        </td>
                                        <?php endif; ?>
                                        <td class="text-center px-4 py-3">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-jam" title="Hapus" style="border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-light sticky-bottom border-top">
                            <tr>
                                <td class="px-4 py-2 fw-600 text-primary" style="font-size: 0.9rem;">TOTAL</td>
                                <td class="px-4 py-2">
                                    <div class="fw-600 text-primary" style="font-size: 1rem;">
                                        <?php
                                        $totalHasil = 0;
                                        if ($isEdit && isset($jams)) {
                                            foreach ($jams as $j) $totalHasil += $j['hasil_produksi'];
                                        }
                                        ?>
                                        <span class="grand_total_hasil_display"><?= $totalHasil ?></span>
                                    </div>
                                </td>
                                <?php if ($isJamRejectMesin): ?><td></td><?php endif; ?>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid 2: Ringkasan Hasil Produksi (Top Right) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                <h6 class="mb-0 text-dark fw-600">
                    <i class="fas fa-chart-bar text-success me-2"></i> Ringkasan Hasil Produksi
                </h6>
            </div>

            <div class="card-body px-4 py-4">
                <div class="row g-3">
                    <!-- Total Bagus -->
                    <div class="col-md-6">
                        <label class="form-label mb-2 text-muted fw-600" style="font-size: 0.875rem;">
                            <i class="fas fa-check-circle text-success me-1"></i> Total Bagus
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="number"
                                class="form-control grand_total_bagus bg-white fw-600 text-success"
                                disabled
                                style="font-size: 1.1rem; border: 2px solid #28a745; border-radius: 6px 0 0 6px;"
                                value="<?= $isEdit ? $header['grand_total_bagus'] ?? '0' : '0' ?>">
                            <input type="hidden" name="grand_total_bagus" class="grand_total_bagus_hidden" value="<?= $isEdit ? $header['grand_total_bagus'] ?? '0' : '0' ?>">
                            <span class="input-group-text bg-success text-white fw-600" style="border-radius: 0 6px 6px 0; font-size: 0.85rem;">pcs</span>
                        </div>
                    </div>

                    <!-- Total Reject -->
                    <div class="col-md-6">
                        <label class="form-label mb-2 text-muted fw-600" style="font-size: 0.875rem;">
                            <i class="fas fa-times-circle text-danger me-1"></i> Total Reject
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="number"
                                class="form-control grand_total_reject bg-white fw-600 text-danger"
                                disabled
                                style="font-size: 1.1rem; border: 2px solid #dc3545; border-radius: 6px 0 0 6px;"
                                value="<?= $isEdit ? $header['grand_total_reject'] ?? '0' : '0' ?>">
                            <input type="hidden" name="grand_total_reject" class="grand_total_reject_hidden" value="<?= $isEdit ? $header['grand_total_reject'] ?? '0' : '0' ?>">
                            <span class="input-group-text bg-danger text-white fw-600" style="border-radius: 0 6px 6px 0; font-size: 0.85rem;">pcs</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="row g-4 mb-4">
    <!-- Grid 3: Detail Jenis Reject (Bottom Left) -->
    <div class="col-lg-6<?= $isJamRejectMesin ? ' jam-reject-hidden' : '' ?>">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; height: 100%;">
            <!-- Card Header -->
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 text-dark fw-600">
                        <i class="fas fa-list text-danger me-2"></i> Detail Jenis Reject
                    </h6>
                    <button type="button" class="btn btn-danger btn-sm rounded-pill btn-add-reject-row-inline" id="btn_add_reject_row_inline" title="Tambah baris reject">
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                </div>
            </div>

            <!-- Card Body with Table -->
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table-hover mb-0 align-middle" id="table_reject_inline">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px;">JENIS REJECT</th>
                                <th class="px-4 py-3 text-muted fw-600 text-center" style="font-size: 0.85rem; letter-spacing: 0.5px; width: 140px;">JUMLAH (PCS)</th>
                                <th class="text-center px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px; width: 60px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="reject_tbody">
                            <?php if ($isEdit && isset($rejects) && !empty($rejects)): ?>
                                <?php foreach ($rejects as $idx => $reject): ?>
                                    <tr class="reject-row border-bottom" data-reject-type="<?= esc($reject['jenis_reject']) ?>" style="transition: background-color 0.2s;">
                                        <td class="px-4 py-3">
                                            <select name="rejects[<?= $idx ?>][jenis_reject]"
                                                class="form-select form-select-sm reject-type"
                                                style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.875rem;"
                                                required>
                                                <option value="">-- Pilih Jenis Reject --</option>
                                                <?php if (isset($reject_types) && !empty($reject_types)): ?>
                                                    <?php foreach ($reject_types as $type): ?>
                                                        <option value="<?= esc($type['nama_reject']) ?>" <?= ($reject['jenis_reject'] == $type['nama_reject']) ? 'selected' : '' ?>>
                                                            <?= esc($type['nama_reject']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number"
                                                name="rejects[<?= $idx ?>][jumlah]"
                                                class="form-control form-control-sm text-center reject-jumlah"
                                                value="<?= $reject['jumlah'] ?>"
                                                placeholder="0"
                                                min="0"
                                                max="99999"
                                                style="border-radius: 6px; border: 1px solid #dee2e6; font-weight: 600; font-size: 0.9rem; padding: 0.5rem;"
                                                required>
                                        </td>
                                        <td class="text-center px-4 py-3">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-reject" title="Hapus" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-light sticky-bottom border-top">
                            <tr>
                                <td class="px-4 py-2 fw-600 text-danger" style="font-size: 0.9rem;">TOTAL</td>
                                <td class="px-4 py-2 text-center">
                                    <span id="reject_total" class="badge bg-danger" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Empty State -->
                <?php if (!$isEdit || empty($rejects)): ?>
                    <div class="p-3 text-center" style="background-color: #f8f9fa; font-size: 0.85rem;">
                        <i class="fas fa-inbox text-muted" style="font-size: 1.5rem; opacity: 0.5; display: block; margin-bottom: 8px;"></i>
                        <small class="text-muted d-block">Belum ada data reject</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Grid 4: Sisa PO, Hold, Gumpalan (Bottom Right) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; height: 100%;">
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                <h6 class="mb-0 text-dark fw-600">
                    <i class="fas fa-boxes text-info me-2"></i> Status Produksi
                </h6>
            </div>

            <div class="card-body px-4 py-4">
                <div class="row g-3">
                    <!-- Sisa PO -->
                    <div class="col-12">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.875rem;">
                            <i class="fas fa-list text-warning me-1"></i> Sisa PO
                        </label>
                        <input type="number"
                            class="form-control form-control-lg sisa_po"
                            placeholder="0"
                            disabled
                            style="border-radius: 8px; border: 1px solid #dee2e6; font-size: 1.05rem; font-weight: 600;"
                            value="<?= $isEdit ? $header['sisa_po'] ?? '' : '' ?>">
                        <input type="hidden" name="sisa_po" class="sisa_po_hidden" value="<?= $isEdit ? $header['sisa_po'] ?? '' : '' ?>">
                        <!-- Menyimpan total_bagus kumulatif dari DB (diisi via AJAX saat SPK dipilih) -->
                        <!-- Mode edit: seed dari total_spk - sisa_po sebagai fallback sebelum AJAX selesai -->
                        <input type="hidden" class="total_bagus_kumulatif_hidden" value="<?= $isEdit ? max(0, (int)($header['total_spk'] ?? 0) - (int)($header['sisa_po'] ?? 0)) : '0' ?>">
                        <small class="text-muted d-block mt-1">Auto-calculated: Total SPK - Total Bagus</small>
                    </div>

                    <!-- Hold -->
                    <div class="col-12">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.875rem;">
                            <i class="fas fa-pause-circle text-info me-1"></i> Hold
                        </label>
                        <input type="number"
                            name="hold"
                            class="form-control form-control-lg hold"
                            placeholder="0"
                            style="border-radius: 8px; border: 1px solid #dee2e6; font-size: 1.05rem; font-weight: 600;"
                            value="<?= $isEdit ? $header['hold'] ?? '' : '' ?>">
                    </div>

                    <!-- Gumpalan -->
                    <div class="col-12">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.875rem;">
                            <i class="fas fa-cube text-secondary me-1"></i> Gumpalan
                        </label>
                        <input type="text"
                            name="gumpalan"
                            class="form-control form-control-lg gumpalan"
                            placeholder="0.0"
                            inputmode="decimal"
                            pattern="[0-9]*\.?[0-9]*"
                            style="border-radius: 8px; border: 1px solid #dee2e6; font-size: 1.05rem; font-weight: 600;"
                            value="<?= $isEdit ? $header['gumpalan'] ?? '' : '' ?>">
                        <small class="text-muted d-block mt-1">Contoh: 1.1, 1.2, 1.6, 2.5</small>
                    </div>

                    <?php if ($isBecumMesin): ?>
                    <!-- Plash Kotor (Becum only) -->
                    <div class="col-12">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.875rem;">
                            <i class="fas fa-droplet text-primary me-1"></i> Plash Kotor
                        </label>
                        <input type="text"
                            name="plash_kotor"
                            class="form-control form-control-lg plash_kotor"
                            placeholder="0"
                            style="border-radius: 8px; border: 1px solid #dee2e6; font-size: 1.05rem; font-weight: 600;"
                            value="<?= $isEdit ? $header['plash_kotor'] ?? '' : '' ?>">
                    </div>

                    <!-- Sapuan (Becum only) -->
                    <div class="col-12">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.875rem;">
                            <i class="fas fa-brush text-success me-1"></i> Sapuan
                        </label>
                        <input type="text"
                            name="sapuan"
                            class="form-control form-control-lg sapuan"
                            placeholder="0"
                            style="border-radius: 8px; border: 1px solid #dee2e6; font-size: 1.05rem; font-weight: 600;"
                            value="<?= $isEdit ? $header['sapuan'] ?? '' : '' ?>">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Kualitas & Reject Section (Regular Form) -->
<?= view('produksi/partials/kualitas_reject_section', [
    'header' => $header ?? null,
    'rejects' => $rejects ?? [],
    'isEdit' => isset($header),
    'prefix' => isset($header) ? 'edit_' : ''
]) ?>

<div class="section-title"><i class="fas fa-boxes"></i> Material, Colorant & Packaging</div>
<?php
// Inisialisasi variabel mesin — dipakai di Primary Material, Packaging, dan section KG
$isCcmMesin      = false;
$isIpsMesin      = false;
$isPowerjetMesin = false;
$isBecumMesin    = false;
if ($isEdit) {
    $editMesinNama   = strtolower(str_replace([' ', '-', '_'], '', $header['nama_mesin'] ?? ''));
    $isCcmMesin      = in_array($editMesinNama, ['ccm1', 'ccm2']);
    $isIpsMesin      = in_array($editMesinNama, ['ips1', 'ips2', 'ips3', 'ips4']);
    $isPowerjetMesin = in_array($editMesinNama, ['powerjet1', 'powerjet2']);
    $isBecumMesin    = ($editMesinNama === 'becum');
} else {
    $isCcmMesin      = in_array($contextMesinSlug, ['ccm1', 'ccm2']);
    $isIpsMesin      = in_array($contextMesinSlug, ['ips1', 'ips2', 'ips3', 'ips4']);
    $isPowerjetMesin = in_array($contextMesinSlug, ['powerjet1', 'powerjet2']);
    $isBecumMesin    = ($contextMesinSlug === 'becum');
}
?>
<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="p-3 border rounded-lg bg-light h-100">
            <label class="form-label text-primary text-xs fw-bold mb-3 d-block"><i class="fas fa-cube me-1"></i> Primary Material</label>
            <?php $mat = ($isEdit && !empty($materials)) ? $materials[0] : null; ?>

            <div class="row g-2">
                <div class="col-8">
                    <input type="text" name="materials[0][merek_kode]" placeholder="Merek Kode" class="form-control form-control-sm merek_kode" value="<?= $mat ? esc($mat['merek_kode']) : '' ?>">
                </div>
                <div class="col-4">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="materials[0][pemakaian]" placeholder="Kg" class="form-control m_pemakaian" value="<?= $mat ? $mat['pemakaian'] : '' ?>">
                        <span class="input-group-text bg-white small">kg</span>
                    </div>
                </div>
                
                <!-- 4 Baris, 2 Kolom Komposisi -->
                <!-- Baris 1 -->
                <div class="col-6">
                    <?php if ($isBecumMesin): ?>
                        <select name="materials[0][komposisi_nama_1]" class="form-select form-select-sm komposisi_nama_1" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <option value="">-- Pilih Nama 1 --</option>
                            <option value="Novarex M 7027 BF"      <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'Novarex M 7027 BF') ? 'selected' : '' ?>>Novarex M 7027 BF</option>
                            <option value="Makrolon WB 1239"        <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'Makrolon WB 1239') ? 'selected' : '' ?>>Makrolon WB 1239</option>
                            <option value="WB2032-9109 (Wanhua)"    <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'WB2032-9109 (Wanhua)') ? 'selected' : '' ?>>WB2032-9109 (Wanhua)</option>
                            <option value="LEXAN PK2870-21317"      <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'LEXAN PK2870-21317') ? 'selected' : '' ?>>LEXAN PK2870-21317</option>
                        </select>
                    <?php else: ?>
                        <input type="text" name="materials[0][komposisi_nama_1]" placeholder="Nama 1" class="form-control form-control-xs komposisi_nama_1" value="<?= $mat ? esc($mat['komposisi_nama_1'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    <?php endif; ?>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="materials[0][komposisi_persen_1]" placeholder="0.00" class="form-control form-control-xs komposisi_persen_1 komposisi-persen-input" value="<?= $mat ? esc($mat['komposisi_persen_1'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                        <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">%</span>
                    </div>
                </div>
                <!-- Lot A -->
                <div class="col-12">
                    <input type="text" name="materials[0][lot_a]" placeholder="Lot A" class="form-control form-control-xs lot_a" value="<?= $mat ? esc($mat['lot_a']) : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                </div>
                
                <!-- Baris 2 -->
                <div class="col-6">
                    <?php if ($isBecumMesin): ?>
                        <select name="materials[0][komposisi_nama_2]" class="form-select form-select-sm komposisi_nama_2" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <option value="">-- Pilih Nama 2 --</option>
                            <option value="OR"       <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OR') ? 'selected' : '' ?>>OR</option>
                            <option value="OR+"      <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OR+') ? 'selected' : '' ?>>OR+</option>
                            <option value="OT"       <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OT') ? 'selected' : '' ?>>OT</option>
                            <option value="OT+"      <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OT+') ? 'selected' : '' ?>>OT+</option>
                            <option value="OTV"      <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OTV') ? 'selected' : '' ?>>OTV</option>
                            <option value="OU"       <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OU') ? 'selected' : '' ?>>OU</option>
                            <option value="OS"       <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OS') ? 'selected' : '' ?>>OS</option>
                            <option value="OS+"      <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OS+') ? 'selected' : '' ?>>OS+</option>
                            <option value="OUV"      <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OUV') ? 'selected' : '' ?>>OUV</option>
                            <option value="RR"       <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RR') ? 'selected' : '' ?>>RR</option>
                            <option value="RS IKRAMA" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RS IKRAMA') ? 'selected' : '' ?>>RS IKRAMA</option>
                            <option value="RT"       <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RT') ? 'selected' : '' ?>>RT</option>
                            <option value="RS VICA"  <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RS VICA') ? 'selected' : '' ?>>RS VICA</option>
                        </select>
                    <?php else: ?>
                        <input type="text" name="materials[0][komposisi_nama_2]" placeholder="Nama 2" class="form-control form-control-xs komposisi_nama_2" value="<?= $mat ? esc($mat['komposisi_nama_2'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    <?php endif; ?>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="materials[0][komposisi_persen_2]" placeholder="0.00" class="form-control form-control-xs komposisi_persen_2 komposisi-persen-input" value="<?= $mat ? esc($mat['komposisi_persen_2'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                        <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">%</span>
                    </div>
                </div>
                <!-- Lot B -->
                <div class="col-12">
                    <input type="text" name="materials[0][lot_b]" placeholder="Lot B" class="form-control form-control-xs lot_b" value="<?= $mat ? esc($mat['lot_b']) : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                </div>
                
                <!-- Baris 3 -->
                <div class="col-6">
                    <?php if ($isBecumMesin): ?>
                        <select name="materials[0][komposisi_nama_3]" class="form-select form-select-sm komposisi_nama_3" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <option value="">-- Pilih Nama 3 --</option>
                            <option value="OR"       <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OR') ? 'selected' : '' ?>>OR</option>
                            <option value="OR+"      <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OR+') ? 'selected' : '' ?>>OR+</option>
                            <option value="OT"       <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OT') ? 'selected' : '' ?>>OT</option>
                            <option value="OT+"      <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OT+') ? 'selected' : '' ?>>OT+</option>
                            <option value="OTV"      <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OTV') ? 'selected' : '' ?>>OTV</option>
                            <option value="OU"       <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OU') ? 'selected' : '' ?>>OU</option>
                            <option value="OS"       <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OS') ? 'selected' : '' ?>>OS</option>
                            <option value="OS+"      <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OS+') ? 'selected' : '' ?>>OS+</option>
                            <option value="OUV"      <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OUV') ? 'selected' : '' ?>>OUV</option>
                            <option value="RR"       <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RR') ? 'selected' : '' ?>>RR</option>
                            <option value="RS IKRAMA" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RS IKRAMA') ? 'selected' : '' ?>>RS IKRAMA</option>
                            <option value="RT"       <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RT') ? 'selected' : '' ?>>RT</option>
                            <option value="RS VICA"  <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RS VICA') ? 'selected' : '' ?>>RS VICA</option>
                        </select>
                    <?php else: ?>
                        <input type="text" name="materials[0][komposisi_nama_3]" placeholder="Nama 3" class="form-control form-control-xs komposisi_nama_3" value="<?= $mat ? esc($mat['komposisi_nama_3'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    <?php endif; ?>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="materials[0][komposisi_persen_3]" placeholder="0.00" class="form-control form-control-xs komposisi_persen_3 komposisi-persen-input" value="<?= $mat ? esc($mat['komposisi_persen_3'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                        <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">%</span>
                    </div>
                </div>
                <!-- Lot C -->
                <div class="col-12">
                    <input type="text" name="materials[0][lot_c]" placeholder="Lot C" class="form-control form-control-xs lot_c" value="<?= $mat ? esc($mat['lot_c']) : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                </div>
                
                <!-- Baris 4 -->
                <div class="col-6">
                    <?php if ($isBecumMesin): ?>
                        <select name="materials[0][komposisi_nama_4]" class="form-select form-select-sm komposisi_nama_4" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <option value="">-- Pilih Nama 4 --</option>
                            <option value="Plast Trans Blue MFPC 15023"       <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Plast Trans Blue MFPC 15023') ? 'selected' : '' ?>>Plast Trans Blue MFPC 15023</option>
                            <option value="Plast Trans Violet MFPC 15999 (A)" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Plast Trans Violet MFPC 15999 (A)') ? 'selected' : '' ?>>Plast Trans Violet MFPC 15999 (A)</option>
                            <option value="Renol Blue PC-C7216R"              <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Renol Blue PC-C7216R') ? 'selected' : '' ?>>Renol Blue PC-C7216R</option>
                            <option value="Renol Blue PC- D8997"              <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Renol Blue PC- D8997') ? 'selected' : '' ?>>Renol Blue PC- D8997</option>
                            <option value="Renol Violet PC-D87226 MG"         <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Renol Violet PC-D87226 MG') ? 'selected' : '' ?>>Renol Violet PC-D87226 MG</option>
                        </select>
                    <?php else: ?>
                        <input type="text" name="materials[0][komposisi_nama_4]" placeholder="Nama 4" class="form-control form-control-xs komposisi_nama_4" value="<?= $mat ? esc($mat['komposisi_nama_4'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    <?php endif; ?>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="materials[0][komposisi_persen_4]" placeholder="0.00" class="form-control form-control-xs komposisi_persen_4 komposisi-persen-input" value="<?= $mat ? esc($mat['komposisi_persen_4'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                        <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">%</span>
                    </div>
                </div>
                <!-- Lot D -->
                <div class="col-12">
                    <input type="text" name="materials[0][lot_d]" placeholder="Lot D" class="form-control form-control-xs lot_d" value="<?= $mat ? esc($mat['lot_d']) : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                </div>
                
                <!-- Total Persen -->
                <div class="col-12">
                    <div class="p-2 bg-primary-light rounded border border-primary" style="border-radius: 6px !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary" style="font-size: 0.8rem;">Total Persen:</span>
                            <span class="komposisi-total-persen badge bg-primary" style="font-size: 0.8rem; padding: 0.35rem 0.6rem;">0.00 %</span>
                        </div>
                    </div>
                    <!-- Warning Message -->
                    <div class="komposisi-warning mt-2 small" style="display: none;">
                        <div class="alert alert-warning py-1 px-2 mb-0" style="font-size: 0.75rem;">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <span class="komposisi-warning-text"></span>
                        </div>
                    </div>
                </div>

                <?php if ($isBecumMesin): ?>
                <!-- KG per Komposisi — hanya Becum -->
                <div class="col-12 becum-komposisi-kg mt-1">
                    <div class="p-2 border rounded" style="background: #f8f9fc; border-radius: 6px !important;">
                        <div class="fw-bold text-secondary mb-2" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.4px;">
                            <i class="fas fa-weight me-1"></i> KG per Komposisi
                        </div>
                        <div class="row g-1">
                            <?php
                            $kgLabels = [
                                1 => $mat ? ($mat['komposisi_nama_1'] ?? 'Nama 1') : 'Nama 1',
                                2 => $mat ? ($mat['komposisi_nama_2'] ?? 'Nama 2') : 'Nama 2',
                                3 => $mat ? ($mat['komposisi_nama_3'] ?? 'Nama 3') : 'Nama 3',
                                4 => $mat ? ($mat['komposisi_nama_4'] ?? 'Nama 4') : 'Nama 4',
                            ];
                            ?>
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="col-12">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white komposisi-kg-label-<?= $i ?>" style="font-size: 0.7rem; min-width: 70px; max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; line-height: 28px; border-right: 0;">
                                        <?= esc($kgLabels[$i]) ?>
                                    </span>
                                    <input type="number"
                                        step="0.001"
                                        min="0"
                                        name="materials[0][komposisi_kg_<?= $i ?>]"
                                        placeholder="0.000"
                                        class="form-control form-control-xs komposisi-kg-input komposisi-kg-input-<?= $i ?>"
                                        value="<?= $mat ? esc($mat['komposisi_kg_' . $i] ?? '') : '' ?>"
                                        style="font-size: 0.75rem; padding: 0.2rem 0.4rem;">
                                    <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">kg</span>
                                </div>
                            </div>
                            <?php endfor; ?>

                            <!-- Total KG display -->
                            <div class="col-12 mt-1">
                                <div class="p-2 bg-white rounded border" style="border-radius: 4px !important;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-secondary" style="font-size: 0.75rem;">Total KG:</span>
                                        <?php
                                        $totalKgInit = 0;
                                        for ($i = 1; $i <= 4; $i++) {
                                            $totalKgInit += (float)($mat['komposisi_kg_' . $i] ?? 0);
                                        }
                                        ?>
                                        <span class="komposisi-total-kg badge bg-secondary" style="font-size: 0.75rem; padding: 0.3rem 0.5rem;">
                                            <?= number_format($totalKgInit, 3) ?> kg
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning / konfirmasi total KG vs pemakaian -->
                            <div class="col-12 komposisi-kg-warning" style="display: none; font-size: 0.72rem; border-radius: 4px; padding: 0.25rem 0.5rem; margin-top: 2px;"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="p-3 border rounded-lg bg-light h-100">
            <label class="form-label text-info text-xs fw-bold mb-3 d-block"><i class="fas fa-tint me-1"></i> Colorant</label>
            <?php $col = ($isEdit && !empty($colorants)) ? $colorants[0] : null; ?>
            <div class="row g-2">
                <div class="col-12"><input type="text" name="colorants[0][code]" placeholder="Color Code" class="form-control form-control-sm c_code" value="<?= $col ? esc($col['code']) : '' ?>"></div>
                <div class="col-7"><input type="text" name="colorants[0][nomor_lot]" placeholder="Lot No" class="form-control form-control-sm c_lot" value="<?= $col ? esc($col['nomor_lot']) : '' ?>"></div>
                <div class="col-5">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="colorants[0][pemakaian]" placeholder="Kg" class="form-control c_pemakaian" value="<?= $col ? $col['pemakaian'] : '' ?>">
                        <span class="input-group-text bg-white small">kg</span>
                    </div>
                </div>
                <div class="col-12 mt-1">
                    <hr class="my-1" style="opacity: 0.15;">
                </div>
                <?php if ($isBecumMesin): ?>
                <!-- TB IN & TB OUT — Becum: list 4 items dengan 2-row layout (Nama+Persen / Lot) -->
                <div class="col-12">
                    <!-- TB IN -->
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2" style="font-size: 0.65rem;">TB IN</span>
                        <span class="text-muted" style="font-size: 0.7rem;">Nama · Persen | Lot</span>
                    </div>
                    <?php
                    $tbInData = [];
                    if ($isEdit && $col && !empty($col['tb_in_details'])) {
                        $tbInData = json_decode($col['tb_in_details'], true) ?: [];
                    }
                    $tbNamaOptions = [
                        'Novarex M 7027 BF', 'Makrolon WB 1239',
                        'WB2032-9109 (Wanhua)', 'LEXAN PK2870-21317',
                        'OR', 'OR+', 'OT', 'OT+', 'OTV', 'OU', 'OS', 'OS+', 'OUV',
                        'RR', 'RS IKRAMA', 'RT', 'RS VICA',
                        'Plast Trans Blue MFPC 15023', 'Plast Trans Violet MFPC 15999 (A)',
                        'Renol Blue PC-C7216R', 'Renol Blue PC- D8997', 'Renol Violet PC-D87226 MG',
                    ];
                    $lotLabels = ['A', 'B', 'C', 'D'];
                    for ($i = 0; $i < 4; $i++):
                        $item = $tbInData[$i] ?? ['nama' => '', 'persen' => '', 'lot' => ''];
                    ?>
                    <div class="p-2 border rounded mb-1" style="background: #f0f7ff; border-color: #cce5ff !important;">
                        <!-- Row 1: Nama (3 cols) + Persen (remainder) -->
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-8">
                                <select name="colorants[0][tb_in][<?= $i ?>][nama]" class="form-select form-select-sm tb-in-nama" style="font-size: 0.75rem; padding: 0.3rem 0.4rem; height: 32px;">
                                    <option value="">-- Nama <?= $i+1 ?> --</option>
                                    <?php foreach ($tbNamaOptions as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= ($item['nama'] === $opt) ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="colorants[0][tb_in][<?= $i ?>][persen]"
                                        class="form-control form-control-sm tb-in-persen"
                                        placeholder="0" style="font-size: 0.75rem; height: 32px;"
                                        value="<?= esc($item['persen'] ?? '') ?>">
                                    <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.3rem 0.4rem;">%</span>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2: Lot (full width) -->
                        <div class="row g-1">
                            <div class="col-12">
                                <input type="text"
                                    name="colorants[0][tb_in][<?= $i ?>][lot]"
                                    class="form-control form-control-sm tb-in-lot"
                                    placeholder="Lot <?= $lotLabels[$i] ?>" style="font-size: 0.75rem; padding: 0.3rem 0.4rem; height: 32px;"
                                    value="<?= esc($item['lot'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>

                    <!-- TB OUT -->
                    <div class="d-flex align-items-center mb-2 mt-3">
                        <span class="badge bg-danger me-2" style="font-size: 0.65rem;">TB OUT</span>
                        <span class="text-muted" style="font-size: 0.7rem;">Nama · Persen | Lot</span>
                    </div>
                    <?php
                    $tbOutData = [];
                    if ($isEdit && $col && !empty($col['tb_out_details'])) {
                        $tbOutData = json_decode($col['tb_out_details'], true) ?: [];
                    }
                    for ($i = 0; $i < 4; $i++):
                        $item = $tbOutData[$i] ?? ['nama' => '', 'persen' => '', 'lot' => ''];
                    ?>
                    <div class="p-2 border rounded mb-1" style="background: #fff3f3; border-color: #ffc9d0 !important;">
                        <!-- Row 1: Nama (3 cols) + Persen (remainder) -->
                        <div class="row g-1 mb-1 align-items-center">
                            <div class="col-8">
                                <select name="colorants[0][tb_out][<?= $i ?>][nama]" class="form-select form-select-sm tb-out-nama" style="font-size: 0.75rem; padding: 0.3rem 0.4rem; height: 32px;">
                                    <option value="">-- Nama <?= $i+1 ?> --</option>
                                    <?php foreach ($tbNamaOptions as $opt): ?>
                                    <option value="<?= esc($opt) ?>" <?= ($item['nama'] === $opt) ? 'selected' : '' ?>><?= esc($opt) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="colorants[0][tb_out][<?= $i ?>][persen]"
                                        class="form-control form-control-sm tb-out-persen"
                                        placeholder="0" style="font-size: 0.75rem; height: 32px;"
                                        value="<?= esc($item['persen'] ?? '') ?>">
                                    <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.3rem 0.4rem;">%</span>
                                </div>
                            </div>
                        </div>
                        <!-- Row 2: Lot (full width) -->
                        <div class="row g-1">
                            <div class="col-12">
                                <input type="text"
                                    name="colorants[0][tb_out][<?= $i ?>][lot]"
                                    class="form-control form-control-sm tb-out-lot"
                                    placeholder="Lot <?= $lotLabels[$i] ?>" style="font-size: 0.75rem; padding: 0.3rem 0.4rem; height: 32px;"
                                    value="<?= esc($item['lot'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php else: ?>
                <!-- Mesin lain: TB in/out tetap input text biasa -->
                <div class="col-6">
                    <input type="text"
                        name="colorants[0][tb_in]"
                        placeholder="TB in"
                        class="form-control form-control-sm tb_in"
                        value="<?= $col ? esc($col['tb_in'] ?? '') : '' ?>">
                </div>
                <div class="col-6">
                    <input type="text"
                        name="colorants[0][tb_out]"
                        placeholder="TB out"
                        class="form-control form-control-sm tb_out"
                        value="<?= $col ? esc($col['tb_out'] ?? '') : '' ?>">
                </div>
                <?php endif; ?>
                <div class="col-12 mt-2">
                    <label class="form-label text-xs fw-bold mb-2 text-muted d-block">Komposisi (dari PPIC)</label>
                    <textarea name="komposisi" class="form-control form-control-sm komposisi" placeholder="Komposisi akan terisi otomatis dari PPIC" rows="4" style="resize: vertical; font-size: 0.8rem;"><?= $isEdit ? esc($header['komposisi'] ?? '') : '' ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="p-3 border rounded-lg bg-light h-100 shadow-sm" style="border-radius: 12px !important;">
            <label class="form-label text-warning text-xs fw-bold mb-3 d-block">
                <i class="fas fa-box me-1"></i> Packaging Details
            </label>

            <!-- Plastik Section -->
            <div class="mb-3">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-vial text-info small"></i></span>
                    <input type="number" name="plastik_bagus" placeholder="Plastik Bagus" class="form-control border-start-0 plastik_bagus" step="0.01" min="0" value="<?= ($isEdit && $packaging) ? ($packaging['plastik_bagus'] ?? '') : '' ?>">
                    <span class="input-group-text bg-white">kg</span>
                </div>
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-vial text-danger small"></i></span>
                    <input type="number" name="plastik_reject" placeholder="Plastik Reject" class="form-control border-start-0 plastik_reject" step="0.01" min="0" value="<?= ($isEdit && $packaging) ? ($packaging['plastik_reject'] ?? '') : '' ?>">
                    <span class="input-group-text bg-white">kg</span>
                </div>
                <?php
                // Tampilkan dropdown ukuran plastik untuk CCM 1 & CCM 2
                // $isCcmMesin, $isIpsMesin, $isPowerjetMesin, $isBecumMesin sudah di-set di atas section Material
                $plastikUkuranOptions   = ['60x90x0,03', '50x75x0.03'];
                $ipsPlastikOptions      = ['65x115x0.03', '200/120x210x0,03', '600x370x425'];
                $powerjetPlastikOptions = ['65x115x0,03 (0,045)', '135x110x0,03 (0,080)', '100x80x0,03', '60x90x0,03 (0,035)'];
                $becumPlastikOptions    = ['73.5x171x0.03', '50 X 75 X 0,03'];
                $currentPlastikUkuran = ($isEdit && $packaging) ? ($packaging['plastik_ukuran'] ?? '') : '';
                if ($isCcmMesin): ?>
                    <select name="plastik_ukuran" class="form-select form-select-sm plastik_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Plastik --</option>
                        <?php foreach ($plastikUkuranOptions as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= ($currentPlastikUkuran === $opt) ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($isIpsMesin): ?>
                    <select name="plastik_ukuran" class="form-select form-select-sm plastik_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Plastik --</option>
                        <?php foreach ($ipsPlastikOptions as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= ($currentPlastikUkuran === $opt) ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($isPowerjetMesin): ?>
                    <select name="plastik_ukuran" class="form-select form-select-sm plastik_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Plastik --</option>
                        <?php foreach ($powerjetPlastikOptions as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= ($currentPlastikUkuran === $opt) ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($isBecumMesin): ?>
                    <select name="plastik_ukuran" class="form-select form-select-sm plastik_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Plastik --</option>
                        <?php foreach ($becumPlastikOptions as $opt): ?>
                            <option value="<?= esc($opt) ?>" <?= ($currentPlastikUkuran === $opt) ? 'selected' : '' ?>><?= esc($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="text" name="plastik_ukuran" placeholder="Ukuran Plastik" class="form-control form-control-xs plastik_ukuran" value="<?= esc($currentPlastikUkuran) ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                <?php endif; ?>
            </div>

            <!-- Box Section -->
            <div class="mb-3">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-box text-warning small"></i></span>
                    <input type="text" name="box" placeholder="Box (pcs)" class="form-control border-start-0 box" value="<?= ($isEdit && $packaging) ? $packaging['box'] : '' ?>">
                </div>
                <?php
                $currentBoxUkuran = ($isEdit && $packaging) ? ($packaging['box_ukuran'] ?? '') : '';
                if ($isCcmMesin): ?>
                    <select name="box_ukuran" class="form-select form-select-sm box_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Box --</option>
                        <option value="500x370x300" <?= ($currentBoxUkuran === '500x370x300') ? 'selected' : '' ?>>500x370x300</option>
                    </select>
                <?php elseif ($isIpsMesin): ?>
                    <select name="box_ukuran" class="form-select form-select-sm box_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Box --</option>
                        <option value="600x365x420" <?= ($currentBoxUkuran === '600x365x420') ? 'selected' : '' ?>>600x365x420</option>
                    </select>
                <?php elseif ($isPowerjetMesin): ?>
                    <select name="box_ukuran" class="form-select form-select-sm box_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Box --</option>
                        <option value="600x400x300 (SW)" <?= ($currentBoxUkuran === '600x400x300 (SW)') ? 'selected' : '' ?>>600x400x300 (SW)</option>
                        <option value="578x540x541 (SW)" <?= ($currentBoxUkuran === '578x540x541 (SW)') ? 'selected' : '' ?>>578x540x541 (SW)</option>
                    </select>
                <?php else: ?>
                    <input type="text" name="box_ukuran" placeholder="Ukuran Box" class="form-control form-control-xs box_ukuran" value="<?= esc($currentBoxUkuran) ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                <?php endif; ?>
            </div>

            <!-- Karung Section -->
            <div class="mb-3">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-archive text-secondary small"></i></span>
                    <input type="text" name="karung" placeholder="Karung (pcs)" class="form-control border-start-0 karung" value="<?= ($isEdit && $packaging) ? $packaging['karung'] : '' ?>">
                </div>
                <?php $currentKarungUkuran = ($isEdit && $packaging) ? ($packaging['karung_ukuran'] ?? '') : ''; ?>
                <?php if ($isIpsMesin || $isCcmMesin): ?>
                    <select name="karung_ukuran" class="form-select form-select-sm karung_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Karung --</option>
                        <option value="65x105" <?= ($currentKarungUkuran === '65x105') ? 'selected' : '' ?>>65 x 105</option>
                        <option value="90x130" <?= ($currentKarungUkuran === '90x130') ? 'selected' : '' ?>>90 x 130</option>
                    </select>
                <?php elseif ($isPowerjetMesin || $isBecumMesin): ?>
                    <select name="karung_ukuran" class="form-select form-select-sm karung_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <option value="">-- Ukuran Karung --</option>
                        <option value="65x105" <?= ($currentKarungUkuran === '65x105') ? 'selected' : '' ?>>65 x 105</option>
                        <option value="90x130" <?= ($currentKarungUkuran === '90x130') ? 'selected' : '' ?>>90 x 130</option>
                    </select>
                <?php else: ?>
                    <input type="text" name="karung_ukuran" placeholder="Ukuran Karung" class="form-control form-control-xs karung_ukuran" value="<?= esc($currentKarungUkuran) ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                <?php endif; ?>
            </div>

            <!-- Nicktainer Section -->
            <div class="mb-0">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-drum text-primary small"></i></span>
                    <input type="text" name="nicktainer" placeholder="Nicktainer (pcs)" class="form-control border-start-0 nicktainer" value="<?= ($isEdit && $packaging) ? ($packaging['nicktainer'] ?? '') : '' ?>">
                </div>
                <input type="text" name="nicktainer_ukuran" placeholder="Ukuran Nicktainer" class="form-control form-control-xs nicktainer_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['nicktainer_ukuran'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
            </div>

            <!-- Awal / Akhir / Pemakaian Section -->
            <div class="mt-2 pt-2" style="border-top: 1px dashed #dee2e6;">
                <div class="text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                    <i class="fas fa-tachometer-alt me-1"></i> AWAL — AKHIR = PEMAKAIAN
                </div>
                <!-- Awal -->
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Awal</span>
                    <input type="number" step="0.01" name="meter_awal"
                        class="form-control form-control-xs meter_awal"
                        placeholder="0.00"
                        value="<?= ($isEdit && $packaging) ? esc($packaging['meter_awal'] ?? '') : '' ?>">
                </div>
                <!-- Akhir -->
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Akhir</span>
                    <input type="number" step="0.01" name="meter_akhir"
                        class="form-control form-control-xs meter_akhir"
                        placeholder="0.00"
                        value="<?= ($isEdit && $packaging) ? esc($packaging['meter_akhir'] ?? '') : '' ?>">
                </div>
                <!-- Pemakaian: read-only, auto-calculated -->
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light" style="font-size: 0.7rem; width: 70px;">Pakai</span>
                    <input type="number" step="0.01"
                        class="form-control form-control-xs meter_pemakaian bg-light"
                        placeholder="0.00" readonly
                        value="<?= ($isEdit && $packaging) ? esc($packaging['meter_pemakaian'] ?? '') : '' ?>">
                    <input type="hidden" name="meter_pemakaian" class="meter_pemakaian_hidden"
                        value="<?= ($isEdit && $packaging) ? esc($packaging['meter_pemakaian'] ?? '') : '' ?>">
                </div>
            </div>

            <!-- Hidden field for legacy support if needed -->
            <input type="hidden" name="box_karung_nicktainer" value="<?= ($isEdit && $packaging) ? $packaging['box_karung_nicktainer'] : '0' ?>">
        </div>
    </div>
</div>

<div class="section-title d-flex justify-content-between align-items-center">
    <span><i class="fas fa-stopwatch"></i> Downtime & Machine Stops</span>
    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn_add_dt">
        <i class="fas fa-plus me-1"></i> Downtime
    </button>
</div>
<?php
$dtReasons = [
    'DT1'  => 'Improvement',
    'DT2'  => 'PLN Off',
    'DT3'  => 'Change Product',
    'DT4'  => 'Planned Maintenance',
    'DT5'  => 'Trial Produk',
    'DT6'  => 'Trial mould dan emboss',
    'DT7'  => 'Bencana Alam',
    'DT8'  => 'Hari libur dan istirahat',
    'DT10' => 'Sanitasi',
    'DT11' => 'Warm up',
    'DT12' => 'Stop msn atas permint khusus',
    'DT13' => 'Quality Inspection',
    'DT14' => 'Kerusakan Mesin Utama',
    'DT15' => 'Kerusakan Utility',
    'DT16' => 'Kerusakan Tools',
    'DT17' => 'Alarm karena Error',
    'DT18' => 'Alarm karena kesalahan proses',
    'DT20' => 'Salah memasukan material',
    'DT21' => 'Material tidak tersedia',
    'DT22' => 'Man power tidak mencukupi',
    'DT23' => 'Tidak ada tmpt Menyimpan produk',
    'DT24' => 'Product Tidak Standar',
    'DT25' => 'Material tidak standar',
    'DT26' => 'Coloran tidak standar',
    'DT27' => 'No Order',
    'DT28' => 'Cleaning Mold',
    'DT29' => 'Cleaning Tooling',
    'DT30' => 'Cleaning Mesin dan Tools',
    'DT31' => 'Kegagalan Design Produk',
    'DT32' => 'Ketiadaan kelengkapan kerja',
];
?>
<div class="dt-list-container mb-4" id="dt_list_container">
    <?php if ($isEdit && isset($downtimes) && !empty($downtimes)): ?>
        <?php foreach ($downtimes as $idx => $dt): ?>
        <div class="dt-card">
            <div class="dt-card-header">
                <span class="dt-card-title">
                    <i class="fas fa-exclamation-circle me-2"></i>Downtime #<?= $idx + 1 ?>
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-dt dt-remove-btn">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
            <div class="dt-card-body">
                <div class="dt-field">
                    <label class="dt-label">Alasan</label>
                    <select name="downtimes[<?= $idx ?>][alasan]" class="form-select form-select-sm">
                        <option value="">-- Pilih Alasan --</option>
                        <?php foreach ($dtReasons as $code => $desc):
                            $val = "$code - $desc";
                        ?>
                            <option value="<?= esc($val) ?>" <?= ($dt['alasan_downtime'] == $val) ? 'selected' : '' ?>>
                                <?= esc($val) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="dt-field">
                    <label class="dt-label">Problem</label>
                    <textarea name="downtimes[<?= $idx ?>][problem]"
                        class="form-control form-control-sm"
                        rows="2"
                        placeholder="Deskripsi problem..."><?= esc($dt['problem'] ?? '') ?></textarea>
                </div>
                <div class="dt-field">
                    <label class="dt-label">Action Plant</label>
                    <textarea name="downtimes[<?= $idx ?>][action_plant]"
                        class="form-control form-control-sm"
                        rows="2"
                        placeholder="Tindakan perbaikan..."><?= esc($dt['action_plant'] ?? '') ?></textarea>
                </div>
                <div class="dt-field-row">
                    <div class="dt-field">
                        <label class="dt-label">Mulai</label>
                        <input type="datetime-local"
                            name="downtimes[<?= $idx ?>][mulai]"
                            value="<?= str_replace(' ', 'T', substr($dt['waktu_mulai'] ?? '', 0, 16)) ?>"
                            class="form-control form-control-sm dt-mulai">
                    </div>
                    <div class="dt-field">
                        <label class="dt-label">Selesai</label>
                        <input type="datetime-local"
                            name="downtimes[<?= $idx ?>][selesai]"
                            value="<?= str_replace(' ', 'T', substr($dt['waktu_selesai'] ?? '', 0, 16)) ?>"
                            class="form-control form-control-sm dt-selesai">
                    </div>
                </div>
                <div class="dt-field" style="max-width: 200px;">
                    <label class="dt-label">Durasi (menit)</label>
                    <input type="number"
                        name="downtimes[<?= $idx ?>][durasi]"
                        value="<?= $dt['durasi_menit'] ?>"
                        class="form-control form-control-sm dt-durasi"
                        placeholder="0">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="dt-empty-state" id="dt_empty_state">
            <i class="fas fa-check-circle text-success mb-2" style="font-size: 1.5rem; display: block;"></i>
            Belum ada downtime. Klik "+ Downtime" untuk menambahkan.
        </div>
    <?php endif; ?>
</div>

<div class="section-title"><i class="fas fa-comment-alt"></i> Catatan & Tanda Tangan Operator</div>
<div class="row g-4 mb-4">
    <div class="col-md-5">
        <label class="form-label text-xs fw-bold text-muted mb-2 uppercase">Catatan Produksi</label>
        <textarea name="catatan" class="form-control shadow-sm" rows="6" placeholder="Masukkan catatan atau kendala produksi di sini..." style="border-radius: 12px;"><?= $isEdit ? esc($header['catatan'] ?? '') : '' ?></textarea>
    </div>
    <div class="col-md-7">
        <label class="form-label text-xs fw-bold text-muted mb-2 uppercase">
            <i class="fas fa-signature me-1"></i>Tanda Tangan Operator (Auto-filled)
        </label>
        <div class="p-3 border rounded-lg bg-white shadow-sm" style="border-radius: 12px !important; min-height: 120px;">
            <div class="auto-ttd-container" style="height: 80px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 8px; margin-bottom: 12px; position: relative;">
                <img id="auto-ttd-preview" class="img-fluid" style="max-height: 70px; display: none;" alt="Tanda Tangan">
                <div id="auto-ttd-placeholder" style="text-align: center; color: #999;">
                    <i class="fas fa-pen-fancy" style="font-size: 1.5rem; margin-bottom: 5px; display: block;"></i>
                    <small>Tanda tangan akan muncul otomatis</small>
                </div>
            </div>
            <input type="hidden" name="auto_user_ttd" id="auto-user-ttd" value="<?= $isEdit && isset($header['auto_user_ttd']) ? esc($header['auto_user_ttd']) : '' ?>">
            <small class="text-muted d-block text-center">
                <i class="fas fa-info-circle me-1"></i>Diambil dari profil user berdasarkan shift yang dipilih
            </small>
        </div>
    </div>
</div>


<!-- Tambah Reject Type Modal (Inline Management) -->
<div class="modal fade" id="tambahRejectTypeModal" tabindex="-1" role="dialog" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title font-weight-bold mb-0">
                    <i class="fas fa-ban me-2"></i>Kelola Jenis Reject
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body pb-2">

                <!-- Tambah baru -->
                <div class="input-group input-group-sm mb-1">
                    <input type="text" id="rt_reject_type_name" class="form-control" placeholder="Contoh: Goresan, Warna Tidak Merata" maxlength="100" autocomplete="off">
                    <div class="input-group-append">
                        <button class="btn btn-danger btn-sm" type="button" id="rt_submit_btn" onclick="submitTambahRejectType()">
                            <i class="fas fa-plus me-1"></i>Tambah
                        </button>
                    </div>
                </div>
                <div id="rt_feedback" class="mt-1 small"></div>

                <hr class="my-2">

                <!-- Daftar jenis reject yang ada -->
                <p class="small text-muted mb-1">Jenis reject yang sudah ada:</p>
                <ul id="rt_reject_type_list" class="list-group list-group-flush" style="max-height: 240px; overflow-y: auto; font-size: 0.82rem;">
                    <li class="list-group-item text-muted small">Memuat...</li>
                </ul>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>