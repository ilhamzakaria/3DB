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
// Updated: juga untuk Powerjet1 dan Powerjet2
// Juga check dari SPK selected value untuk fallback
$isBecumMesin = $_earlyIsBecum || $_earlyIsPowerjet;

// DEBUG: Log untuk verify detection
if ($isEdit) {
    error_log("DEBUG: Edit mode - nama_mesin={$header['nama_mesin']}, cleaned={$_earlyMesinNama}, isBecum={$isBecumMesin}");
}
?>

<!-- Marker untuk CCM/IPS form -->
<?php
// Deteksi apakah ini CCM atau IPS mesin
$_mesinName = $isEdit ? strtolower($header['nama_mesin'] ?? '') : strtolower($contextMesinLabel ?? '');
$isCcmMesin = strpos($_mesinName, 'ccm') !== false;
$isIpsMesin = strpos($_mesinName, 'ips') !== false;
?>
<?php if ($isCcmMesin || $isIpsMesin): ?>
    <div class="isCcmIps" style="display: none;"></div>
<?php endif; ?>

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
                $isDisabled     = '';
                $disabledText   = $selesaiByPpic ? ' ✓ Selesai' : '';
                $isSelectedOption = ($isEdit && $spk['no_spk'] == $header['no_spk']) ? true : false;

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
                <option value="1">Shift 1 (06.00-14.00)</option>
                <option value="2">Shift 2 (14.00-22.00)</option>
                <option value="3">Shift 3 (22.00-06.00)</option>
            </select>
            <input type="hidden" name="shift" class="shift_hidden" value="<?= $isEdit ? trim(esc($header['shift'] ?? '')) : '' ?>">
            <input type="hidden" name="tercapai" class="tercapai_hidden" value="<?= $isEdit ? trim(esc($header['tercapai'] ?? '0')) : '0' ?>">
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="form-label mb-2">Grup</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-users text-muted"></i></span>
            <input type="text" name="grup" class="form-control border-start-0"
                placeholder="Contoh: A, B, C"
                value="<?= $isEdit ? esc($header['grup'] ?? '') : '' ?>">
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

    <!-- Extra Shifts Input (Produksi: Optional, for recording non-standard shifts) -->
    <div class="col-lg-12 mt-3 pt-2" style="border-top: 1px dashed #dee2e6;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="form-label mb-0 fw-bold text-info" style="font-size: 0.9rem;">
                <i class="fas fa-plus-circle me-1"></i> Extra Shifts (Shift Lainnya)
            </label>
            <button type="button" class="btn btn-outline-info btn-sm" onclick="addExtraShiftRowProduksi()" title="Tambah extra shift">
                <i class="fas fa-plus me-1"></i>Shift
            </button>
        </div>
        <small class="text-muted d-block mb-2">Masukkan data untuk shift luar biasa (cth: Shift 4, Shift Malam, dll)</small>
        <div id="produksi_extra_shifts_container" style="background: #f8f9fc; padding: 1rem; border-radius: 8px; border: 1px solid #e3e6f0;">
            <?php
            // Load extra shifts from hidden field if edit mode
            $extraShiftsData = [];
            if ($isEdit && isset($header['extra_realisasi_shifts'])) {
                try {
                    $decoded = json_decode($header['extra_realisasi_shifts'], true);
                    if (is_array($decoded)) {
                        $extraShiftsData = $decoded;
                    }
                } catch (\Exception $e) {
                    // Invalid JSON, skip
                }
            }
            ?>

            <?php if (!empty($extraShiftsData)): ?>
                <?php foreach ($extraShiftsData as $idx => $es): ?>
                    <div class="row align-items-center mb-2 extra-shift-row-produksi">
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>
                                <input type="text" class="form-control extra-shift-label" placeholder="Nama shift (cth: Shift 4)" value="<?= esc($es['label'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control extra-shift-realisasi" placeholder="Realisasi (pcs)" value="<?= esc($es['realisasi'] ?? '') ?>">
                                <div class="input-group-append"><span class="input-group-text">pcs</span></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeExtraShiftRowProduksi(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-muted small text-center py-2">
                    <i class="fas fa-inbox me-1"></i> Belum ada extra shift
                </div>
            <?php endif; ?>
        </div>
        <input type="hidden" name="extra_realisasi_shifts" id="extra_realisasi_shifts_hidden" value="<?= $isEdit && isset($header['extra_realisasi_shifts']) ? esc($header['extra_realisasi_shifts']) : '[]' ?>">
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

<?php if ($_earlyIsBecum || $_earlyIsPowerjet): ?>
    <!-- Counter Awal / Akhir / Total (Becum & Powerjet) -->
    <div class="row g-4 mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-0 pt-3 pb-2 px-4">
                    <h6 class="mb-0 text-dark fw-600">
                        <i class="fas fa-tachometer-alt text-success me-2"></i> Counter Mesin
                    </h6>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="row g-3">
                        <!-- Counter Awal -->
                        <div class="col-4">
                            <label class="form-label small fw-bold text-muted mb-1">Counter Awal</label>
                            <input type="number" min="0" name="counter_awal"
                                class="form-control form-control-sm counter_awal"
                                placeholder="0"
                                value="<?= $isEdit ? esc($header['counter_awal'] ?? '') : '' ?>">
                        </div>
                        <!-- Counter Akhir -->
                        <div class="col-4">
                            <label class="form-label small fw-bold text-muted mb-1">Counter Akhir</label>
                            <input type="number" min="0" name="counter_akhir"
                                class="form-control form-control-sm counter_akhir"
                                placeholder="0"
                                value="<?= $isEdit ? esc($header['counter_akhir'] ?? '') : '' ?>">
                        </div>
                        <!-- Total Counter (readonly) -->
                        <div class="col-4">
                            <label class="form-label small fw-bold text-muted mb-1">
                                <?= $_earlyIsPowerjet ? '(Akhir-Awal)×12' : 'Total Counter' ?>
                            </label>
                            <?php
                            // Kalkulasi ulang dari awal & akhir agar selalu konsisten
                            if ($isEdit) {
                                $cAwal  = (float)($header['counter_awal']  ?? 0);
                                $cAkhir = (float)($header['counter_akhir'] ?? 0);
                                $cDiff  = abs($cAkhir - $cAwal);
                                $cValue = $_earlyIsPowerjet ? $cDiff * 12 : $cDiff;
                                // Fallback ke DB jika awal & akhir kosong
                                if ($cAwal == 0 && $cAkhir == 0) {
                                    $cValue = (float)($header['total_counter'] ?? 0);
                                }
                            } else {
                                $cValue = '';
                            }
                            ?>
                            <input type="number" min="0" name="total_counter"
                                class="form-control form-control-sm counter_total bg-light"
                                data-multiply="<?= $_earlyIsPowerjet ? '12' : '1' ?>"
                                placeholder="0" readonly
                                value="<?= $isEdit ? $cValue : '' ?>">
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block" style="font-size:0.7rem;">
                        <i class="fas fa-info-circle me-1"></i>
                        <?= $_earlyIsPowerjet
                            ? '(Akhir &minus; Awal) &times; 12'
                            : 'Total Counter = Counter Akhir &minus; Counter Awal'
                        ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function() {
            'use strict';

            function calcCounter() {
                var awalEl = document.querySelector('input[name="counter_awal"]');
                var akhirEl = document.querySelector('input[name="counter_akhir"]');
                var totalEl = document.querySelector('input[name="total_counter"]');
                if (!awalEl || !akhirEl || !totalEl) return;
                var awal = parseFloat(awalEl.value) || 0;
                var akhir = parseFloat(akhirEl.value) || 0;
                var diff = Math.abs(akhir - awal);
                var multiply = parseFloat(totalEl.dataset.multiply) || 1;
                totalEl.value = diff * multiply;
            }

            // Event delegation — works even after AJAX re-render
            if (!document.__counterDelegated) {
                document.__counterDelegated = true;
                document.addEventListener('input', function(e) {
                    if (e.target && (e.target.name === 'counter_awal' || e.target.name === 'counter_akhir')) {
                        calcCounter();
                    }
                });
            }

            // Run immediately to handle edit form pre-filled values
            calcCounter();
            // Extra safety: re-run after short delay in case form loads via AJAX
            setTimeout(calcCounter, 150);
            setTimeout(calcCounter, 400);
        })();
    </script>
<?php endif; ?>

<div class="row g-4 mb-4">
    <!-- Grid 1: Data Produksi Per Jam -->
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
                                                    <?php
                                                    // Restore reject rows dari rejects_json (mode edit)
                                                    // atau render 1 empty row (mode tambah)
                                                    $savedRejects = [];
                                                    if (!empty($jam['rejects_json'])) {
                                                        $decoded = json_decode($jam['rejects_json'], true);
                                                        if (is_array($decoded) && count($decoded) > 0) {
                                                            $savedRejects = $decoded;
                                                        }
                                                    }
                                                    // Pastikan minimal 1 row kosong jika tidak ada data
                                                    if (empty($savedRejects)) {
                                                        $savedRejects = [['jenis' => '', 'jumlah' => 0]];
                                                    }
                                                    foreach ($savedRejects as $savedReject):
                                                        $savedJenis  = $savedReject['jenis']  ?? '';
                                                        $savedJumlah = (int)($savedReject['jumlah'] ?? 0);
                                                    ?>
                                                        <div class="jam-reject-row d-flex gap-1 mb-1 align-items-center">
                                                            <select class="form-select form-select-sm jam-reject-type flex-grow-1" style="font-size: 0.75rem; min-width: 110px;">
                                                                <option value="">-- Jenis --</option>
                                                                <?php foreach ($reject_types ?? [] as $rt): ?>
                                                                    <option value="<?= esc($rt['nama_reject']) ?>"
                                                                        <?= ($rt['nama_reject'] === $savedJenis) ? 'selected' : '' ?>>
                                                                        <?= esc($rt['nama_reject']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <input type="number" min="0"
                                                                class="form-control form-control-sm jam-reject-jumlah"
                                                                placeholder="0"
                                                                value="<?= ($savedJumlah > 0) ? $savedJumlah : '' ?>"
                                                                style="width: 60px; font-size: 0.75rem;">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger btn-remove-jam-reject"
                                                                style="padding: 0.2rem 0.4rem; font-size: 0.7rem;">✕</button>
                                                        </div>
                                                    <?php endforeach; ?>
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

                <!-- Ringkasan Hasil Produksi — langsung di bawah tabel jam -->
                <div class="px-4 py-3 border-top" style="background:#f8fdf9;">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <span class="fw-bold text-muted" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.5px;">
                                <i class="fas fa-sigma me-1 text-success"></i> Ringkasan
                            </span>
                        </div>
                        <div class="col">
                            <div class="d-flex gap-3 flex-wrap">
                                <!-- Total Bagus -->
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted fw-600" style="font-size:0.78rem;">
                                        <i class="fas fa-check-circle text-success me-1"></i> Total Bagus
                                    </span>
                                    <div class="input-group input-group-sm" style="width:160px;">
                                        <input type="number"
                                            class="form-control grand_total_bagus bg-white fw-600 text-success text-center"
                                            disabled
                                            style="font-size:1rem; border:2px solid #28a745; border-radius:6px 0 0 6px;"
                                            value="<?= $isEdit ? $header['grand_total_bagus'] ?? '0' : '0' ?>">
                                        <input type="hidden" name="grand_total_bagus" class="grand_total_bagus_hidden" value="<?= $isEdit ? $header['grand_total_bagus'] ?? '0' : '0' ?>">
                                        <span class="input-group-text bg-success text-white fw-600" style="border-radius:0 6px 6px 0; font-size:0.8rem;">pcs</span>
                                    </div>
                                </div>
                                <!-- Total Reject -->
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted fw-600" style="font-size:0.78rem;">
                                        <i class="fas fa-times-circle text-danger me-1"></i> Total Reject
                                    </span>
                                    <div class="input-group input-group-sm" style="width:160px;">
                                        <input type="number"
                                            class="form-control grand_total_reject bg-white fw-600 text-danger text-center"
                                            disabled
                                            style="font-size:1rem; border:2px solid #dc3545; border-radius:6px 0 0 6px;"
                                            value="<?= $isEdit ? $header['grand_total_reject'] ?? '0' : '0' ?>">
                                        <input type="hidden" name="grand_total_reject" class="grand_total_reject_hidden" value="<?= $isEdit ? $header['grand_total_reject'] ?? '0' : '0' ?>">
                                        <span class="input-group-text bg-danger text-white fw-600" style="border-radius:0 6px 6px 0; font-size:0.8rem;">pcs</span>
                                    </div>
                                </div>

                                <!-- Pemisah -->
                                <span class="text-muted fw-bold" style="font-size:1rem;">=</span>

                                <!-- Total Bagus + Reject -->
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted fw-600" style="font-size:0.78rem;">
                                        <i class="fas fa-boxes text-secondary me-1"></i> Total B+R
                                    </span>
                                    <div class="input-group input-group-sm" style="width:160px;">
                                        <input type="number"
                                            class="form-control grand_total_br bg-white fw-600 text-center"
                                            disabled
                                            style="font-size:1rem; border:2px solid #6c757d; border-radius:6px 0 0 6px; color:#495057;"
                                            value="<?= $isEdit ? (($header['grand_total_bagus'] ?? 0) + ($header['grand_total_reject'] ?? 0)) : '0' ?>">
                                        <span class="input-group-text text-white fw-600" style="background:#6c757d; border-radius:0 6px 6px 0; font-size:0.8rem;">pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Produksi Row (Full Width) -->
<div class="row g-4 mb-4">
    <!-- Grid 4: Status Produksi (Full Width) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                <h6 class="mb-0 text-dark fw-600">
                    <i class="fas fa-boxes text-info me-2"></i> Status Produksi
                </h6>
            </div>

            <div class="card-body px-4 py-3">
                <div class="row g-3" style="align-items: flex-end;">
                    <!-- Sisa PO -->
                    <div class="col-6 col-md-4 col-lg">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.75rem; display: block; white-space: nowrap;">
                            <i class="fas fa-list text-warning me-1"></i> Sisa PO
                        </label>
                        <input type="number"
                            class="form-control form-control-sm sisa_po"
                            placeholder="0"
                            disabled
                            style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.9rem; font-weight: 600; padding: 0.45rem 0.65rem;"
                            value="<?= $isEdit ? $header['sisa_po'] ?? '' : '' ?>">
                        <input type="hidden" name="sisa_po" class="sisa_po_hidden" value="<?= $isEdit ? $header['sisa_po'] ?? '' : '' ?>">
                        <input type="hidden" class="total_bagus_kumulatif_hidden" value="0">
                        <!-- <small class="text-muted d-block mt-1" style="font-size: 0.68rem;">Auto-calc</small> -->
                    </div>

                    <!-- Hold -->
                    <div class="col-6 col-md-4 col-lg">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.75rem; display: block; white-space: nowrap;">
                            <i class="fas fa-pause-circle text-info me-1"></i> Hold
                        </label>
                        <input type="number"
                            name="hold"
                            class="form-control form-control-sm hold"
                            placeholder="0"
                            style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.9rem; font-weight: 600; padding: 0.45rem 0.65rem;"
                            value="<?= $isEdit ? $header['hold'] ?? '' : '' ?>">
                    </div>

                    <!-- Gumpalan -->
                    <div class="col-6 col-md-4 col-lg">
                        <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.75rem; display: block; white-space: nowrap;">
                            <i class="fas fa-cube text-secondary me-1"></i> Gumpalan
                        </label>
                        <input type="text"
                            name="gumpalan"
                            class="form-control form-control-sm gumpalan"
                            placeholder="0.0"
                            inputmode="decimal"
                            pattern="[0-9]*\.?[0-9]*"
                            style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.9rem; font-weight: 600; padding: 0.45rem 0.65rem;"
                            value="<?= $isEdit ? $header['gumpalan'] ?? '' : '' ?>">
                        <!-- <small class="text-muted d-block mt-1" style="font-size: 0.68rem;">Contoh: 1.1</small> -->
                    </div>

                    <?php if ($isBecumMesin): ?>
                        <!-- Plash Kotor (Becum only) -->
                        <div class="col-6 col-md-4 col-lg">
                            <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.75rem; display: block; white-space: nowrap;">
                                <i class="fas fa-droplet text-primary me-1"></i> Plash Kotor
                            </label>
                            <input type="text"
                                name="plash_kotor"
                                class="form-control form-control-sm plash_kotor"
                                placeholder="0"
                                style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.9rem; font-weight: 600; padding: 0.45rem 0.65rem;"
                                value="<?= $isEdit ? $header['plash_kotor'] ?? '' : '' ?>">
                        </div>

                        <!-- Sapuan (Becum only) -->
                        <div class="col-6 col-md-4 col-lg">
                            <label class="form-label text-muted fw-600 mb-2" style="font-size: 0.75rem; display: block; white-space: nowrap;">
                                <i class="fas fa-brush text-success me-1"></i> Sapuan
                            </label>
                            <input type="text"
                                name="sapuan"
                                class="form-control form-control-sm sapuan"
                                placeholder="0"
                                style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.9rem; font-weight: 600; padding: 0.45rem 0.65rem;"
                                value="<?= $isEdit ? $header['sapuan'] ?? '' : '' ?>">
                        </div>
                    <?php endif; ?>
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
                                                style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.875rem;">
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
                                                style="border-radius: 6px; border: 1px solid #dee2e6; font-weight: 600; font-size: 0.9rem; padding: 0.5rem;">
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
                    <input type="text" name="materials[0][merek_kode]" placeholder="Merek Kode" class="form-control form-control-sm merek_kode" value="<?= $mat ? esc($mat['merek_kode'] ?? '') : '' ?>">
                </div>
                <div class="col-4">
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.01" name="materials[0][pemakaian]" placeholder="Kg" class="form-control m_pemakaian" value="<?= $mat ? ($mat['pemakaian'] ?? '') : '' ?>">
                        <span class="input-group-text bg-white small">kg</span>
                    </div>
                </div>

                <!-- Lot 1-4 berurutan (CCM & IPS: di atas Nama/%) -->
                <?php if ($isCcmMesin || $isIpsMesin): ?>
                    <div class="col-12">
                        <div class="row g-1">
                            <div class="col-6 col-md-3">
                                <input type="text" name="materials[0][lot_a]" placeholder="Lot 1" class="form-control form-control-xs lot_a" value="<?= $mat ? esc($mat['lot_a'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="text" name="materials[0][lot_b]" placeholder="Lot 2" class="form-control form-control-xs lot_b" value="<?= $mat ? esc($mat['lot_b'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="text" name="materials[0][lot_c]" placeholder="Lot 3" class="form-control form-control-xs lot_c" value="<?= $mat ? esc($mat['lot_c'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="text" name="materials[0][lot_d]" placeholder="Lot 4" class="form-control form-control-xs lot_d" value="<?= $mat ? esc($mat['lot_d'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 4 Baris, 2 Kolom Komposisi -->
                <!-- Baris 1 -->
                <div class="col-6">
                    <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                        <select name="materials[0][komposisi_nama_1]" class="form-select form-select-sm komposisi_nama_1" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <?php if ($isPowerjetMesin): ?>
                                <!-- Powerjet: only LD 2426 K -->
                                <option value="LD 2426 K" <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'LD 2426 K') ? 'selected' : 'selected' ?>>LD 2426 K</option>
                            <?php else: ?>
                                <!-- Becum: multiple options -->
                                <option value="">-- Pilih Nama 1 --</option>
                                <option value="Novarex M 7027 BF" <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'Novarex M 7027 BF') ? 'selected' : '' ?>>Novarex M 7027 BF</option>
                                <option value="Makrolon WB 1239" <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'Makrolon WB 1239') ? 'selected' : '' ?>>Makrolon WB 1239</option>
                                <option value="WB2032-9109 (Wanhua)" <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'WB2032-9109 (Wanhua)') ? 'selected' : '' ?>>WB2032-9109 (Wanhua)</option>
                                <option value="LEXAN PK2870-21317" <?= ($mat && ($mat['komposisi_nama_1'] ?? '') === 'LEXAN PK2870-21317') ? 'selected' : '' ?>>LEXAN PK2870-21317</option>
                            <?php endif; ?>
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
                <!-- Lot A (Becum & Powerjet only: di sini. CCM/IPS: di atas) -->
                <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                    <div class="col-12">
                        <input type="text" name="materials[0][lot_a]" placeholder="Lot 1" class="form-control form-control-xs lot_a" value="<?= $mat ? esc($mat['lot_a'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    </div>
                <?php endif; ?>

                <!-- Baris 2 -->
                <div class="col-6">
                    <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                        <select name="materials[0][komposisi_nama_2]" class="form-select form-select-sm komposisi_nama_2" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <?php if ($isPowerjetMesin): ?>
                                <!-- Powerjet: HD 6070EA, Innoplus HD 2200JP -->
                                <option value="">-- Pilih Nama 2 --</option>
                                <option value="HD 6070EA" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'HD 6070EA') ? 'selected' : '' ?>>HD 6070EA</option>
                                <option value="Innoplus HD 2200JP" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'Innoplus HD 2200JP') ? 'selected' : '' ?>>Innoplus HD 2200JP</option>
                            <?php else: ?>
                                <!-- Becum: existing options -->
                                <option value="">-- Pilih Nama 2 --</option>
                                <option value="OR" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OR') ? 'selected' : '' ?>>OR</option>
                                <option value="OR+" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OR+') ? 'selected' : '' ?>>OR+</option>
                                <option value="OT" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OT') ? 'selected' : '' ?>>OT</option>
                                <option value="OT+" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OT+') ? 'selected' : '' ?>>OT+</option>
                                <option value="OTV" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OTV') ? 'selected' : '' ?>>OTV</option>
                                <option value="OU" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OU') ? 'selected' : '' ?>>OU</option>
                                <option value="OS" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OS') ? 'selected' : '' ?>>OS</option>
                                <option value="OS+" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OS+') ? 'selected' : '' ?>>OS+</option>
                                <option value="OUV" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'OUV') ? 'selected' : '' ?>>OUV</option>
                                <option value="RR" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RR') ? 'selected' : '' ?>>RR</option>
                                <option value="RR IKRAMA" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RR IKRAMA') ? 'selected' : '' ?>>RR IKRAMA</option>
                                <option value="RS IKRAMA" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RS IKRAMA') ? 'selected' : '' ?>>RS IKRAMA</option>
                                <option value="RT" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RT') ? 'selected' : '' ?>>RT</option>
                                <option value="RS VICA" <?= ($mat && ($mat['komposisi_nama_2'] ?? '') === 'RS VICA') ? 'selected' : '' ?>>RS VICA</option>
                            <?php endif; ?>
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
                <!-- Lot B (Becum & Powerjet only: di sini. CCM/IPS: di atas) -->
                <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                    <div class="col-12">
                        <input type="text" name="materials[0][lot_b]" placeholder="Lot 2" class="form-control form-control-xs lot_b" value="<?= $mat ? esc($mat['lot_b'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    </div>
                <?php endif; ?>

                <!-- Baris 3 -->
                <div class="col-6">
                    <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                        <select name="materials[0][komposisi_nama_3]" class="form-select form-select-sm komposisi_nama_3" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <?php if ($isPowerjetMesin): ?>
                                <!-- Powerjet: Color options -->
                                <option value="">-- Pilih Nama 3 --</option>
                                <option value="Blue 07" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Blue 07') ? 'selected' : '' ?>>Blue 07</option>
                                <option value="White 01" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'White 01') ? 'selected' : '' ?>>White 01</option>
                                <option value="Red 01" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Red 01') ? 'selected' : '' ?>>Red 01</option>
                                <option value="Orange 01" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Orange 01') ? 'selected' : '' ?>>Orange 01</option>
                                <option value="Clear" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Clear') ? 'selected' : '' ?>>Clear</option>
                                <option value="Blue 10" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Blue 10') ? 'selected' : '' ?>>Blue 10</option>
                                <option value="Blue 10-1" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Blue 10-1') ? 'selected' : '' ?>>Blue 10-1</option>
                                <option value="Blue 08" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Blue 08') ? 'selected' : '' ?>>Blue 08</option>
                                <option value="Blue 04" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Blue 04') ? 'selected' : '' ?>>Blue 04</option>
                                <option value="Green 03" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Green 03') ? 'selected' : '' ?>>Green 03</option>
                                <option value="blue 11" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'blue 11') ? 'selected' : '' ?>>blue 11</option>
                                <option value="Purple 01" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'Purple 01') ? 'selected' : '' ?>>Purple 01</option>
                            <?php else: ?>
                                <!-- Becum: existing options -->
                                <option value="">-- Pilih Nama 3 --</option>
                                <option value="OR" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OR') ? 'selected' : '' ?>>OR</option>
                                <option value="OR+" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OR+') ? 'selected' : '' ?>>OR+</option>
                                <option value="OT" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OT') ? 'selected' : '' ?>>OT</option>
                                <option value="OT+" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OT+') ? 'selected' : '' ?>>OT+</option>
                                <option value="OTV" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OTV') ? 'selected' : '' ?>>OTV</option>
                                <option value="OU" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OU') ? 'selected' : '' ?>>OU</option>
                                <option value="OS" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OS') ? 'selected' : '' ?>>OS</option>
                                <option value="OS+" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OS+') ? 'selected' : '' ?>>OS+</option>
                                <option value="OUV" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'OUV') ? 'selected' : '' ?>>OUV</option>
                                <option value="RR" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RR') ? 'selected' : '' ?>>RR</option>
                                <option value="RR IKRAMA" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RR IKRAMA') ? 'selected' : '' ?>>RR IKRAMA</option>
                                <option value="RS IKRAMA" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RS IKRAMA') ? 'selected' : '' ?>>RS IKRAMA</option>
                                <option value="RT" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RT') ? 'selected' : '' ?>>RT</option>
                                <option value="RS VICA" <?= ($mat && ($mat['komposisi_nama_3'] ?? '') === 'RS VICA') ? 'selected' : '' ?>>RS VICA</option>
                            <?php endif; ?>
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
                <!-- Lot C (Becum & Powerjet only: di sini. CCM/IPS: di atas) -->
                <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                    <div class="col-12">
                        <input type="text" name="materials[0][lot_c]" placeholder="Lot 3" class="form-control form-control-xs lot_c" value="<?= $mat ? esc($mat['lot_c'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    </div>
                <?php endif; ?>

                <!-- Baris 4 -->
                <div class="col-6">
                    <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                        <select name="materials[0][komposisi_nama_4]" class="form-select form-select-sm komposisi_nama_4" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <?php if ($isPowerjetMesin): ?>
                                <!-- Powerjet: Additive options -->
                                <option value="">-- Pilih Nama 4 --</option>
                                <option value="Plast Green MFPE 20564-22" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Plast Green MFPE 20564-22') ? 'selected' : '' ?>>Plast Green MFPE 20564-22</option>
                                <option value="Lucent Blue 0878-BL LM5300729" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Blue 0878-BL LM5300729') ? 'selected' : '' ?>>Lucent Blue 0878-BL LM5300729</option>
                                <option value="Lucent Blue 1761 (A)-BL LM5201494" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Blue 1761 (A)-BL LM5201494') ? 'selected' : '' ?>>Lucent Blue 1761 (A)-BL LM5201494</option>
                                <option value="Lucent Red 0157-BL LM3300115" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Red 0157-BL LM3300115') ? 'selected' : '' ?>>Lucent Red 0157-BL LM3300115</option>
                                <option value="Lucent Green 1749-BL LM6301490" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Green 1749-BL LM6301490') ? 'selected' : '' ?>>Lucent Green 1749-BL LM6301490</option>
                                <option value="Lucent Green 5771-BL" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Green 5771-BL') ? 'selected' : '' ?>>Lucent Green 5771-BL</option>
                                <option value="Lucent Blue 6019 BL LM5305779" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Blue 6019 BL LM5305779') ? 'selected' : '' ?>>Lucent Blue 6019 BL LM5305779</option>
                                <option value="Lucent Blue 6172 (A) LM5106030" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Blue 6172 (A) LM5106030') ? 'selected' : '' ?>>Lucent Blue 6172 (A) LM5106030</option>
                                <option value="Lucent Blue 6420 (A) SWS" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Blue 6420 (A) SWS') ? 'selected' : '' ?>>Lucent Blue 6420 (A) SWS</option>
                                <option value="Lucent Green 2221-BL LM6301973" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Green 2221-BL LM6301973') ? 'selected' : '' ?>>Lucent Green 2221-BL LM6301973</option>
                                <option value="Lucent Violet 6407 BL" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Lucent Violet 6407 BL') ? 'selected' : '' ?>>Lucent Violet 6407 BL</option>
                                <option value="Finolene Green GSE-1407" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Finolene Green GSE-1407') ? 'selected' : '' ?>>Finolene Green GSE-1407</option>
                            <?php else: ?>
                                <!-- Becum: existing options -->
                                <option value="">-- Pilih Nama 4 --</option>
                                <option value="Plast Trans Blue MFPC 15023" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Plast Trans Blue MFPC 15023') ? 'selected' : '' ?>>Plast Trans Blue MFPC 15023</option>
                                <option value="Plast Trans Violet MFPC 15999 (A)" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Plast Trans Violet MFPC 15999 (A)') ? 'selected' : '' ?>>Plast Trans Violet MFPC 15999 (A)</option>
                                <option value="Renol Blue PC-C7216R" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Renol Blue PC-C7216R') ? 'selected' : '' ?>>Renol Blue PC-C7216R</option>
                                <option value="Renol Blue PC- D8997" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Renol Blue PC- D8997') ? 'selected' : '' ?>>Renol Blue PC- D8997</option>
                                <option value="Renol Violet PC-D87226 MG" <?= ($mat && ($mat['komposisi_nama_4'] ?? '') === 'Renol Violet PC-D87226 MG') ? 'selected' : '' ?>>Renol Violet PC-D87226 MG</option>
                            <?php endif; ?>
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
                <!-- Lot D (Becum & Powerjet only: di sini. CCM/IPS: di atas) -->
                <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                    <div class="col-12">
                        <input type="text" name="materials[0][lot_d]" placeholder="Lot 4" class="form-control form-control-xs lot_d" value="<?= $mat ? esc($mat['lot_d'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                    </div>
                <?php endif; ?>

                <!-- Lot 1-4 berurutan (CCM & IPS: setelah semua Nama/%) sudah dipindah ke atas -->
                <?php if ($isCcmMesin || $isIpsMesin): ?>
                <?php endif; ?>

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

                <!-- Total Material (CCM & IPS Only) -->
                <?php if ($isCcmMesin || $isIpsMesin): ?>
                    <div class="col-12">
                        <div class="p-2 bg-success-light rounded border border-success" style="border-radius: 6px !important; background: rgba(25, 135, 84, 0.1);">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success" style="font-size: 0.8rem;">
                                    <i class="fas fa-weight me-1"></i>Total Material:
                                </span>
                                <span class="total-material-display badge bg-success" style="font-size: 0.8rem; padding: 0.35rem 0.6rem; font-family: 'Courier New', monospace;">
                                    0.000 kg
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                <i class="fas fa-calculator me-1"></i>
                                Rumus: (Bagus + Reject + Hold) &times; <span class="total-material-berat-hint">?</span> gr &divide; 1000 + Gumpalan
                            </small>
                        </div>
                        <!-- Hidden input to store calculated value -->
                        <input type="hidden" name="total_material" class="total_material_hidden" value="0.000">
                    </div>
                <?php endif; ?>

                <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                    <!-- KG per Komposisi — Becum & Powerjet -->
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
                <?php if ($isBecumMesin || $isPowerjetMesin): ?>
                    <!-- TB IN/OUT Collapsible Cards (Becum & Powerjet) -->

                    <!-- TB IN Collapsible Card -->
                    <div class="col-12 mt-3">
                        <div class="card mb-2 shadow-sm border-0" style="border-radius: 8px; overflow: hidden;">
                            <!-- Clickable Header -->
                            <div class="card-header d-flex justify-content-between align-items-center p-3"
                                data-bs-toggle="collapse"
                                data-bs-target="#tbInCollapse"
                                aria-expanded="false"
                                style="cursor: pointer; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; user-select: none;">
                                <span class="fw-bold text-white" style="font-size: 0.9rem;">
                                    <i class="fas fa-arrow-circle-down me-2"></i> TB IN - Bahan Baku Masuk
                                </span>
                                <i class="fas fa-chevron-down text-white toggle-icon-tb-in" style="transition: transform 0.3s; font-size: 0.85rem;"></i>
                            </div>

                            <!-- Collapsible Body -->
                            <div id="tbInCollapse" class="collapse <?= ($isEdit && $col && !empty($col['tb_in_details'])) ? 'show' : '' ?>">
                                <div class="card-body p-3" style="background: #f8f9fc;">
                                    <?php
                                    $tbInData = [];
                                    if ($isEdit && $col && !empty($col['tb_in_details'])) {
                                        $tbInData = json_decode($col['tb_in_details'], true) ?: [];
                                    }

                                    $tbNamaOptions = [
                                        'Novarex M 7027 BF',
                                        'Makrolon WB 1239',
                                        'WB2032-9109 (Wanhua)',
                                        'LEXAN PK2870-21317',
                                        'OR',
                                        'OR+',
                                        'OT',
                                        'OT+',
                                        'OTV',
                                        'OU',
                                        'OS',
                                        'OS+',
                                        'OUV',
                                        'RR',
                                        'RR IKRAMA',
                                        'RS IKRAMA',
                                        'RT',
                                        'RS VICA',
                                        'Plast Trans Blue MFPC 15023',
                                        'Plast Trans Violet MFPC 15999 (A)',
                                        'Renol Blue PC-C7216R',
                                        'Renol Blue PC- D8997',
                                        'Renol Violet PC-D87226 MG',
                                    ];
                                    $lotLabels = ['A', 'B', 'C', 'D'];

                                    for ($i = 0; $i < 4; $i++):
                                        $item = $tbInData[$i] ?? ['nama' => '', 'persen' => '', 'lot' => ''];
                                        $lotLabel = $lotLabels[$i];
                                        $itemNum = $i + 1;
                                    ?>

                                        <!-- Item Row -->
                                        <div class="mb-3 p-2 border rounded" style="background: white; border-color: #cce5ff !important;">
                                            <div class="row g-2 align-items-end">
                                                <!-- Item Label -->
                                                <div class="col-12 mb-1">
                                                    <small class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                        <i class="fas fa-circle" style="font-size: 0.35rem; color: #667eea;"></i> ITEM <?= $itemNum ?>
                                                    </small>
                                                </div>

                                                <!-- Dropdown Nama -->
                                                <div class="col-12">
                                                    <select name="colorants[0][tb_in][<?= $i ?>][nama]"
                                                        class="form-select form-select-sm tb-in-nama"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem; height: auto; min-height: 28px;">
                                                        <option value="">-- Pilih Nama --</option>
                                                        <?php foreach ($tbNamaOptions as $opt): ?>
                                                            <option value="<?= esc($opt) ?>" <?= ($item['nama'] === $opt) ? 'selected' : '' ?>>
                                                                <?= esc($opt) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Persentase & Lot (same row) -->
                                                <div class="col-6">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number"
                                                            step="0.01"
                                                            min="0"
                                                            max="100"
                                                            name="colorants[0][tb_in][<?= $i ?>][persen]"
                                                            class="form-control form-control-xs tb-in-persen"
                                                            placeholder="0.00"
                                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                                            value="<?= esc($item['persen'] ?? '') ?>">
                                                        <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">%</span>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <input type="text"
                                                        name="colorants[0][tb_in][<?= $i ?>][lot]"
                                                        class="form-control form-control-xs tb-in-lot"
                                                        placeholder="Lot <?= $lotLabel ?>"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                                        value="<?= esc($item['lot'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>

                                    <?php endfor; ?>

                                    <!-- TB IN Value Field -->
                                    <div class="mt-3 p-2 border rounded" style="background: #e7f1ff; border-color: #667eea !important;">
                                        <label class="form-label mb-2 text-muted fw-bold" style="font-size: 0.75rem;">TB IN (Bahan)</label>
                                        <input type="text"
                                            name="colorants[0][tb_in_summary]"
                                            class="form-control form-control-sm"
                                            placeholder="Contoh: A100, B200, C150, D180"
                                            style="font-size: 0.8rem; height: 36px;"
                                            value="<?= $col ? esc($col['tb_in'] ?? '') : '' ?>">
                                    </div>

                                    <!-- TB IN Runner Field (Hide for Becum only) -->
                                    <?php if (!$isBecumMesin): ?>
                                        <div class="mt-2 p-2 border rounded" style="background: #eef6ff; border-color: #93c5fd !important;">
                                            <label class="form-label mb-2 text-muted fw-bold" style="font-size: 0.75rem;">
                                                <i class="fas fa-running me-1" style="color:#2563eb;"></i> TB in Runner
                                            </label>
                                            <input type="text"
                                                name="colorants[0][tb_in_runner]"
                                                class="form-control form-control-sm"
                                                placeholder="Contoh: R100, R200"
                                                style="font-size: 0.8rem; height: 36px;"
                                                value="<?= $col ? esc($col['tb_in_runner'] ?? '') : '' ?>">
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="colorants[0][tb_in_runner]" value="<?= $col ? esc($col['tb_in_runner'] ?? '') : '' ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TB OUT Collapsible Card -->
                    <div class="col-12 mt-2">
                        <div class="card mb-2 shadow-sm border-0" style="border-radius: 8px; overflow: hidden;">
                            <!-- Clickable Header -->
                            <div class="card-header d-flex justify-content-between align-items-center p-3"
                                data-bs-toggle="collapse"
                                data-bs-target="#tbOutCollapse"
                                aria-expanded="false"
                                style="cursor: pointer; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; user-select: none;">
                                <span class="fw-bold text-white" style="font-size: 0.9rem;">
                                    <i class="fas fa-arrow-circle-up me-2"></i> TB OUT BAHAN
                                </span>
                                <i class="fas fa-chevron-down text-white toggle-icon-tb-out" style="transition: transform 0.3s; font-size: 0.85rem;"></i>
                            </div>

                            <!-- Collapsible Body -->
                            <div id="tbOutCollapse" class="collapse <?= ($isEdit && $col && !empty($col['tb_out_details'])) ? 'show' : '' ?>">
                                <div class="card-body p-3" style="background: #fff8f7;">
                                    <?php
                                    $tbOutData = [];
                                    if ($isEdit && $col && !empty($col['tb_out_details'])) {
                                        $tbOutData = json_decode($col['tb_out_details'], true) ?: [];
                                    }

                                    for ($i = 0; $i < 4; $i++):
                                        $item = $tbOutData[$i] ?? ['nama' => '', 'persen' => '', 'lot' => ''];
                                        $lotLabel = $lotLabels[$i];
                                        $itemNum = $i + 1;
                                    ?>

                                        <!-- Item Row -->
                                        <div class="mb-3 p-2 border rounded" style="background: white; border-color: #ffc9d0 !important;">
                                            <div class="row g-2 align-items-end">
                                                <!-- Item Label -->
                                                <div class="col-12 mb-1">
                                                    <small class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                        <i class="fas fa-circle" style="font-size: 0.35rem; color: #f5576c;"></i> ITEM <?= $itemNum ?>
                                                    </small>
                                                </div>

                                                <!-- Dropdown Nama -->
                                                <div class="col-12">
                                                    <select name="colorants[0][tb_out][<?= $i ?>][nama]"
                                                        class="form-select form-select-sm tb-out-nama"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem; height: auto; min-height: 28px;">
                                                        <option value="">-- Pilih Nama --</option>
                                                        <?php foreach ($tbNamaOptions as $opt): ?>
                                                            <option value="<?= esc($opt) ?>" <?= ($item['nama'] === $opt) ? 'selected' : '' ?>>
                                                                <?= esc($opt) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Persentase & Lot (same row) -->
                                                <div class="col-6">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number"
                                                            step="0.01"
                                                            min="0"
                                                            max="100"
                                                            name="colorants[0][tb_out][<?= $i ?>][persen]"
                                                            class="form-control form-control-xs tb-out-persen"
                                                            placeholder="0.00"
                                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                                            value="<?= esc($item['persen'] ?? '') ?>">
                                                        <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">%</span>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <input type="text"
                                                        name="colorants[0][tb_out][<?= $i ?>][lot]"
                                                        class="form-control form-control-xs tb-out-lot"
                                                        placeholder="Lot <?= $lotLabel ?>"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                                        value="<?= esc($item['lot'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>

                                    <?php endfor; ?>

                                    <!-- TB OUT BAHAN Value Field -->
                                    <div class="mt-3 p-2 border rounded" style="background: #ffe7e7; border-color: #f5576c !important;">
                                        <label class="form-label mb-2 text-muted fw-bold" style="font-size: 0.75rem;">TB OUT BAHAN</label>
                                        <input type="text"
                                            name="colorants[0][tb_out_summary]"
                                            class="form-control form-control-sm"
                                            placeholder="Contoh: A95, B195, C145, D175"
                                            style="font-size: 0.8rem; height: 36px;"
                                            value="<?= $col ? esc($col['tb_out'] ?? '') : '' ?>">
                                    </div>

                                    <!-- TB OUT RUNNER Value Field (Hide for Becum only) -->
                                    <?php if (!$isBecumMesin): ?>
                                        <div class="mt-2 p-2 border rounded" style="background: #fff3e0; border-color: #ff9800 !important;">
                                            <label class="form-label mb-2 text-muted fw-bold" style="font-size: 0.75rem;">TB OUT RUNNER</label>
                                            <input type="text"
                                                name="colorants[0][tb_out_runner]"
                                                class="form-control form-control-sm"
                                                placeholder="Masukkan nilai TB OUT RUNNER"
                                                style="font-size: 0.8rem; height: 36px;"
                                                value="<?= $col ? esc($col['tb_out_runner'] ?? '') : '' ?>">
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="colorants[0][tb_out_runner]" value="<?= $col ? esc($col['tb_out_runner'] ?? '') : '' ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TB lain IN Collapsible Card -->
                    <div class="col-12 mt-2">
                        <div class="card mb-2 shadow-sm border-0" style="border-radius: 8px; overflow: hidden;">
                            <!-- Clickable Header -->
                            <div class="card-header d-flex justify-content-between align-items-center p-3"
                                data-bs-toggle="collapse"
                                data-bs-target="#tbLainInCollapse"
                                aria-expanded="false"
                                style="cursor: pointer; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; user-select: none;">
                                <span class="fw-bold text-white" style="font-size: 0.9rem;">
                                    <i class="fas fa-arrow-circle-down me-2"></i> TB lain IN - Bahan Lain Masuk
                                </span>
                                <i class="fas fa-chevron-down text-white toggle-icon-tb-lain-in" style="transition: transform 0.3s; font-size: 0.85rem;"></i>
                            </div>

                            <!-- Collapsible Body -->
                            <div id="tbLainInCollapse" class="collapse <?= ($isEdit && $col && !empty($col['tb_lain_in_details'])) ? 'show' : '' ?>">
                                <div class="card-body p-3" style="background: #f0fff8;">
                                    <?php
                                    $tbLainInData = [];
                                    if ($isEdit && $col && !empty($col['tb_lain_in_details'])) {
                                        $tbLainInData = json_decode($col['tb_lain_in_details'], true) ?: [];
                                    }

                                    for ($i = 0; $i < 4; $i++):
                                        $item     = $tbLainInData[$i] ?? ['nama' => '', 'persen' => '', 'lot' => ''];
                                        $lotLabel = $lotLabels[$i];
                                        $itemNum  = $i + 1;
                                    ?>

                                        <!-- Item Row -->
                                        <div class="mb-3 p-2 border rounded" style="background: white; border-color: #b2f0d8 !important;">
                                            <div class="row g-2 align-items-end">
                                                <!-- Item Label -->
                                                <div class="col-12 mb-1">
                                                    <small class="text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                        <i class="fas fa-circle" style="font-size: 0.35rem; color: #38f9d7;"></i> ITEM <?= $itemNum ?>
                                                    </small>
                                                </div>

                                                <!-- Dropdown Nama -->
                                                <div class="col-12">
                                                    <select name="colorants[0][tb_lain_in][<?= $i ?>][nama]"
                                                        class="form-select form-select-sm tb-lain-in-nama"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem; height: auto; min-height: 28px;">
                                                        <option value="">-- Pilih Nama --</option>
                                                        <?php foreach ($tbNamaOptions as $opt): ?>
                                                            <option value="<?= esc($opt) ?>" <?= ($item['nama'] === $opt) ? 'selected' : '' ?>>
                                                                <?= esc($opt) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Persentase & Lot (same row) -->
                                                <div class="col-6">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number"
                                                            step="0.01"
                                                            min="0"
                                                            max="100"
                                                            name="colorants[0][tb_lain_in][<?= $i ?>][persen]"
                                                            class="form-control form-control-xs tb-lain-in-persen"
                                                            placeholder="0.00"
                                                            style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                                            value="<?= esc($item['persen'] ?? '') ?>">
                                                        <span class="input-group-text bg-white" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">%</span>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <input type="text"
                                                        name="colorants[0][tb_lain_in][<?= $i ?>][lot]"
                                                        class="form-control form-control-xs tb-lain-in-lot"
                                                        placeholder="Lot <?= $lotLabel ?>"
                                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"
                                                        value="<?= esc($item['lot'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>

                                    <?php endfor; ?>

                                    <!-- TB lain IN Value Field -->
                                    <div class="mt-3 p-2 border rounded" style="background: #e6fff4; border-color: #43e97b !important;">
                                        <label class="form-label mb-2 text-muted fw-bold" style="font-size: 0.75rem;">TB lain IN (Summary)</label>
                                        <input type="text"
                                            name="colorants[0][tb_lain_in_summary]"
                                            class="form-control form-control-sm"
                                            placeholder="Contoh: A100, B200, C150, D180"
                                            style="font-size: 0.8rem; height: 36px;"
                                            value="<?= $col ? esc($col['tb_lain_in'] ?? '') : '' ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Non-Becum: Simple text inputs (hidden for CCM & IPS machines) -->
                    <?php
                    // Hide TB IN/OUT for CCM1, CCM2, IPS1, IPS2, IPS3, IPS4
                    $hideTbForMesins = $isCcmMesin || $isIpsMesin;
                    ?>
                    <?php if (!$hideTbForMesins): ?>
                        <div class="col-6">
                            <input type="text"
                                name="colorants[0][tb_in]"
                                placeholder="TB in"
                                class="form-control form-control-sm tb_in"
                                value="<?= $col ? esc($col['tb_in'] ?? '') : '' ?>"
                                style="font-size: 0.8rem; height: 36px;">
                        </div>
                        <div class="col-6">
                            <input type="text"
                                name="colorants[0][tb_out]"
                                placeholder="TB out"
                                class="form-control form-control-sm tb_out"
                                value="<?= $col ? esc($col['tb_out'] ?? '') : '' ?>"
                                style="font-size: 0.8rem; height: 36px;">
                        </div>
                    <?php endif; ?>
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

            <!-- Plastik Section (hidden for Becum & Powerjet) -->
            <?php if (!$isBecumMesin && !$isPowerjetMesin): ?>
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
                    $plastikUkuranOptions   = ['60x90x0,03', '50x75x0.03'];
                    $ipsPlastikOptions      = ['65x115x0.03', '200/120x210x0,03', '600x370x425'];
                    $powerjetPlastikOptions = ['65x115x0,03 (0,045)', '135x110x0,03 (0,080)', '100x80x0,03', '60x90x0,03 (0,035)'];
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
                    <?php else: ?>
                        <input type="text" name="plastik_ukuran" placeholder="Ukuran Plastik" class="form-control form-control-xs plastik_ukuran" value="<?= esc($currentPlastikUkuran) ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Box Section (hidden for Becum & Powerjet) -->
            <?php if (!$isBecumMesin && !$isPowerjetMesin): ?>
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
                            <option value="600x370x425" <?= ($currentBoxUkuran === '600x370x425') ? 'selected' : '' ?>>600x370x425</option>
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
            <?php endif; ?>

            <!-- Karung Section (hidden for Becum & Powerjet) -->
            <?php if (!$isBecumMesin && !$isPowerjetMesin): ?>
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
                    <?php elseif ($isPowerjetMesin): ?>
                        <select name="karung_ukuran" class="form-select form-select-sm karung_ukuran" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                            <option value="">-- Ukuran Karung --</option>
                            <option value="65x105" <?= ($currentKarungUkuran === '65x105') ? 'selected' : '' ?>>65 x 105</option>
                            <option value="90x130" <?= ($currentKarungUkuran === '90x130') ? 'selected' : '' ?>>90 x 130</option>
                        </select>
                    <?php else: ?>
                        <input type="text" name="karung_ukuran" placeholder="Ukuran Karung" class="form-control form-control-xs karung_ukuran" value="<?= esc($currentKarungUkuran) ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Nicktainer Section (hidden for Becum & Powerjet) -->
            <?php if (!$isBecumMesin && !$isPowerjetMesin): ?>
                <div class="mb-0">
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-drum text-primary small"></i></span>
                        <input type="text" name="nicktainer" placeholder="Nicktainer (pcs)" class="form-control border-start-0 nicktainer" value="<?= ($isEdit && $packaging) ? ($packaging['nicktainer'] ?? '') : '' ?>">
                    </div>
                    <input type="text" name="nicktainer_ukuran" placeholder="Ukuran Nicktainer" class="form-control form-control-xs nicktainer_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['nicktainer_ukuran'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                </div>
            <?php endif; ?>

            <!-- Awal / Akhir / Pemakaian Section (tidak untuk CCM, IPS & Powerjet) -->
            <?php if (!$isCcmMesin && !$isIpsMesin && !$isPowerjetMesin): ?>
                <div class="mt-2 pt-2" style="border-top: 1px dashed #dee2e6;">

                    <?php if ($isBecumMesin): ?>
                        <!-- Ukuran Plastik Besar (Becum only) -->
                        <div class="mb-3">
                            <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                                <i class="fas fa-expand-alt me-1"></i> Ukuran Plastik Besar
                            </label>
                            <input type="text" name="ukuran_plastik_besar"
                                class="form-control form-control-sm ukuran_plastik_besar"
                                placeholder="73.5 x 171 x 0.03"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['ukuran_plastik_besar'] ?? '73.5 x 171 x 0.03') : '73.5 x 171 x 0.03' ?>">
                        </div>

                        <!-- Plastik Besar Awal / Akhir / Pemakaian (Becum only) -->
                        <div class="mb-3 p-2 border rounded" style="background: #f8f9fc; border-radius: 6px !important;">
                            <div class="text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                                <i class="fas fa-expand-alt text-primary me-1"></i> Plastik Besar – Awal / Akhir / Pemakaian
                            </div>
                            <!-- Awal -->
                            <div class="input-group input-group-sm mb-1">
                                <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Awal</span>
                                <input type="number" step="0.01" name="plastik_besar_awal"
                                    class="form-control form-control-xs plastik_besar_awal"
                                    placeholder="0.00"
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_besar_awal'] ?? '') : '' ?>">
                            </div>
                            <!-- Akhir -->
                            <div class="input-group input-group-sm mb-1">
                                <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Akhir</span>
                                <input type="number" step="0.01" name="plastik_besar_akhir"
                                    class="form-control form-control-xs plastik_besar_akhir"
                                    placeholder="0.00"
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_besar_akhir'] ?? '') : '' ?>">
                            </div>
                            <!-- Pemakaian: readonly, auto-calculated -->
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light" style="font-size: 0.7rem; width: 70px;">Pakai</span>
                                <input type="number" step="0.01"
                                    class="form-control form-control-xs plastik_besar_pemakaian bg-light"
                                    placeholder="0.00" readonly
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_besar_pemakaian'] ?? '') : '' ?>">
                                <input type="hidden" name="plastik_besar_pemakaian" class="plastik_besar_pemakaian_hidden"
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_besar_pemakaian'] ?? '') : '' ?>">
                            </div>
                        </div>
                        <script>
                            (function() {
                                function updatePlastikBesarPemakaian() {
                                    const awal = parseFloat(document.querySelector('[name="plastik_besar_awal"]')?.value) || 0;
                                    const akhir = parseFloat(document.querySelector('[name="plastik_besar_akhir"]')?.value) || 0;
                                    const pakai = Math.abs(awal - akhir);
                                    const display = document.querySelector('.plastik_besar_pemakaian');
                                    const hidden = document.querySelector('.plastik_besar_pemakaian_hidden');
                                    if (display) display.value = pakai.toFixed(2);
                                    if (hidden) hidden.value = pakai.toFixed(2);
                                }

                                function attach() {
                                    ['plastik_besar_awal', 'plastik_besar_akhir'].forEach(function(name) {
                                        const el = document.querySelector('[name="' + name + '"]');
                                        if (el && !el.dataset.plasticBesarBound) {
                                            el.dataset.plasticBesarBound = '1';
                                            el.addEventListener('input', updatePlastikBesarPemakaian);
                                            el.addEventListener('change', updatePlastikBesarPemakaian);
                                        }
                                    });
                                    updatePlastikBesarPemakaian();
                                }
                                attach();
                                setTimeout(attach, 200);
                            })();
                        </script>

                        <!-- Ukuran Plastik Kecil (Becum only) -->
                        <div class="mb-2 mt-3" style="border-top: 1px dashed #dee2e6; padding-top: 0.75rem;">
                            <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                                <i class="fas fa-compress-alt me-1"></i> Ukuran Plastik Kecil
                            </label>
                            <input type="text" name="ukuran_plastik_kecil"
                                class="form-control form-control-sm ukuran_plastik_kecil"
                                placeholder="50 x 75 x 0.03"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['ukuran_plastik_kecil'] ?? '50 x 75 x 0.03') : '50 x 75 x 0.03' ?>">
                        </div>

                        <!-- Plastik Kecil Awal / Akhir / Pemakaian (Becum only) -->
                        <div class="mb-3 p-2 border rounded" style="background: #f8f9fc; border-radius: 6px !important;">
                            <div class="text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                                <i class="fas fa-compress-alt text-info me-1"></i> Plastik Kecil – Awal / Akhir / Pemakaian
                            </div>
                            <div class="input-group input-group-sm mb-1">
                                <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Awal</span>
                                <input type="number" step="0.01" name="plastik_kecil_awal"
                                    class="form-control form-control-xs plastik_kecil_awal"
                                    placeholder="0.00"
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_kecil_awal'] ?? '') : '' ?>">
                            </div>
                            <div class="input-group input-group-sm mb-1">
                                <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Akhir</span>
                                <input type="number" step="0.01" name="plastik_kecil_akhir"
                                    class="form-control form-control-xs plastik_kecil_akhir"
                                    placeholder="0.00"
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_kecil_akhir'] ?? '') : '' ?>">
                            </div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light" style="font-size: 0.7rem; width: 70px;">Pakai</span>
                                <input type="number" step="0.01"
                                    class="form-control form-control-xs plastik_kecil_pemakaian bg-light"
                                    placeholder="0.00" readonly
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_kecil_pemakaian'] ?? '') : '' ?>">
                                <input type="hidden" name="plastik_kecil_pemakaian" class="plastik_kecil_pemakaian_hidden"
                                    value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_kecil_pemakaian'] ?? '') : '' ?>">
                            </div>
                        </div>
                        <script>
                            (function() {
                                function updatePlastikKecilPemakaian() {
                                    const awal = parseFloat(document.querySelector('[name="plastik_kecil_awal"]')?.value) || 0;
                                    const akhir = parseFloat(document.querySelector('[name="plastik_kecil_akhir"]')?.value) || 0;
                                    const pakai = Math.abs(awal - akhir);
                                    const display = document.querySelector('.plastik_kecil_pemakaian');
                                    const hidden = document.querySelector('.plastik_kecil_pemakaian_hidden');
                                    if (display) display.value = pakai.toFixed(2);
                                    if (hidden) hidden.value = pakai.toFixed(2);
                                }

                                function attach() {
                                    ['plastik_kecil_awal', 'plastik_kecil_akhir'].forEach(function(name) {
                                        const el = document.querySelector('[name="' + name + '"]');
                                        if (el && !el.dataset.plasticKecilBound) {
                                            el.dataset.plasticKecilBound = '1';
                                            el.addEventListener('input', updatePlastikKecilPemakaian);
                                            el.addEventListener('change', updatePlastikKecilPemakaian);
                                        }
                                    });
                                    updatePlastikKecilPemakaian();
                                }
                                attach();
                                setTimeout(attach, 200);
                            })();
                        </script>
                    <?php endif; ?>
                    <div class="text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.3px;">
                        <i class="fas fa-tachometer-alt me-1"></i> AWAL - AKHIR = PEMAKAIAN
                    </div>
                    <small class="text-muted d-block mb-2" style="font-size: 0.65rem;">
                        <i class="fas fa-info-circle me-1"></i> Stok Awal dikurangi Stok Akhir = Pemakaian
                    </small>
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
                    <!-- Pemakaian: read-only, auto-calculated (Akhir - Awal) -->
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

                <!-- Script: Auto-calculate pemakaian = akhir - awal (positif) -->
                <script>
                    (function() {
                        'use strict';

                        // Find parent form to scope the listeners
                        const formElement = document.querySelector('form') || document;

                        // Function to update pemakaian calculation
                        function updateMeterPemakaian() {
                            const awalInput = formElement.querySelector('.meter_awal');
                            const akhirInput = formElement.querySelector('.meter_akhir');
                            const pemakaianDisplay = formElement.querySelector('.meter_pemakaian');
                            const pemakaianHidden = formElement.querySelector('.meter_pemakaian_hidden');

                            if (!awalInput || !akhirInput || !pemakaianDisplay) return;

                            const awal = parseFloat(awalInput.value) || 0;
                            const akhir = parseFloat(akhirInput.value) || 0;

                            // Formula: Pemakaian = Awal - Akhir (stock awal dikurangi stock akhir)
                            // Gunakan Math.abs() untuk memastikan hasil selalu positif
                            const pemakaian = Math.abs(awal - akhir);

                            // Update display field
                            pemakaianDisplay.value = pemakaian.toFixed(2);

                            // Update hidden field for form submission
                            if (pemakaianHidden) {
                                pemakaianHidden.value = pemakaian.toFixed(2);
                            }

                            console.log('[METER] Updated pemakaian:', {
                                awal: awal,
                                akhir: akhir,
                                pemakaian: pemakaian.toFixed(2)
                            });
                        }

                        // Attach event listeners to meter_awal and meter_akhir
                        const awalInput = formElement.querySelector('.meter_awal');
                        const akhirInput = formElement.querySelector('.meter_akhir');

                        if (awalInput) {
                            awalInput.addEventListener('change', updateMeterPemakaian);
                            awalInput.addEventListener('input', updateMeterPemakaian);
                        }

                        if (akhirInput) {
                            akhirInput.addEventListener('change', updateMeterPemakaian);
                            akhirInput.addEventListener('input', updateMeterPemakaian);
                        }

                        // Initialize on page load if values exist
                        updateMeterPemakaian();
                    })();
                </script>
            <?php endif; ?>

            <!-- Plastik Awal/Akhir/Pakai (untuk CCM & IPS saja) -->
            <?php if ($isCcmMesin || $isIpsMesin): ?>
                <div class="mt-3 pt-3" style="border-top: 1px dashed #dee2e6; background:#fff8f7; padding:.75rem; border-radius:6px;">
                    <div class="text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.3px; color:#d97706;">
                        <i class="fas fa-vial text-warning me-1"></i> PLASTIK - AWAL / AKHIR / PAKAI
                    </div>
                    <!-- Awal -->
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 60px;">Awal</span>
                        <input type="number" step="0.01" name="plastik_awal"
                            class="form-control form-control-xs plastik_awal"
                            placeholder="0.00"
                            value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_awal'] ?? '') : '' ?>">
                    </div>
                    <!-- Akhir -->
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 60px;">Akhir</span>
                        <input type="number" step="0.01" name="plastik_akhir"
                            class="form-control form-control-xs plastik_akhir"
                            placeholder="0.00"
                            value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_akhir'] ?? '') : '' ?>">
                    </div>
                    <!-- Pakai (readonly) -->
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light" style="font-size: 0.7rem; width: 60px;">Pakai</span>
                        <input type="number" step="0.01"
                            class="form-control form-control-xs plastik_pakai bg-light"
                            placeholder="0.00" readonly
                            value="<?= ($isEdit && $packaging) ? esc(number_format((float)abs(($packaging['plastik_awal'] ?? 0) - ($packaging['plastik_akhir'] ?? 0)), 2)) : '' ?>">
                        <input type="hidden" name="plastik_pakai" class="plastik_pakai_hidden"
                            value="<?= ($isEdit && $packaging) ? esc(number_format((float)abs(($packaging['plastik_awal'] ?? 0) - ($packaging['plastik_akhir'] ?? 0)), 2)) : '' ?>">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Material Awal/Akhir/Pakai (untuk CCM & IPS saja) -->
            <?php if ($isCcmMesin || $isIpsMesin): ?>
                <div class="mt-2 pt-2" style="border-top: 1px dashed #dee2e6; background:#f0f7ff; padding:.75rem; border-radius:6px;">
                    <div class="text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.3px; color:#2563eb;">
                        <i class="fas fa-cube text-primary me-1"></i> MATERIAL - AWAL / AKHIR / PAKAI
                    </div>
                    <!-- Awal -->
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 60px;">Awal</span>
                        <input type="number" step="0.01" name="material_awal"
                            class="form-control form-control-xs material_awal"
                            placeholder="0.00"
                            value="<?= ($isEdit && $packaging) ? esc($packaging['material_awal'] ?? '') : '' ?>">
                    </div>
                    <!-- Akhir -->
                    <div class="input-group input-group-sm mb-1">
                        <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 60px;">Akhir</span>
                        <input type="number" step="0.01" name="material_akhir"
                            class="form-control form-control-xs material_akhir"
                            placeholder="0.00"
                            value="<?= ($isEdit && $packaging) ? esc($packaging['material_akhir'] ?? '') : '' ?>">
                    </div>
                    <!-- Pakai (readonly) -->
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light" style="font-size: 0.7rem; width: 60px;">Pakai</span>
                        <input type="number" step="0.01"
                            class="form-control form-control-xs material_pakai bg-light"
                            placeholder="0.00" readonly
                            value="<?= ($isEdit && $packaging) ? esc(number_format((float)abs(($packaging['material_awal'] ?? 0) - ($packaging['material_akhir'] ?? 0)), 2)) : '' ?>">
                        <input type="hidden" name="material_pakai" class="material_pakai_hidden"
                            value="<?= ($isEdit && $packaging) ? esc(number_format((float)abs(($packaging['material_awal'] ?? 0) - ($packaging['material_akhir'] ?? 0)), 2)) : '' ?>">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Script: Auto-calculate plastik & material pemakaian -->
            <?php if ($isCcmMesin || $isIpsMesin): ?>
                <script>
                    (function() {
                        'use strict';

                        function updatePlastikPakai() {
                            const awal = parseFloat(document.querySelector('.plastik_awal')?.value) || 0;
                            const akhir = parseFloat(document.querySelector('.plastik_akhir')?.value) || 0;
                            const pakai = Math.abs(awal - akhir);
                            const display = document.querySelector('.plastik_pakai');
                            const hidden = document.querySelector('.plastik_pakai_hidden');
                            if (display) display.value = pakai.toFixed(2);
                            if (hidden) hidden.value = pakai.toFixed(2);
                        }

                        function updateMaterialPakai() {
                            const awal = parseFloat(document.querySelector('.material_awal')?.value) || 0;
                            const akhir = parseFloat(document.querySelector('.material_akhir')?.value) || 0;
                            const pakai = Math.abs(awal - akhir);
                            const display = document.querySelector('.material_pakai');
                            const hidden = document.querySelector('.material_pakai_hidden');
                            if (display) display.value = pakai.toFixed(2);
                            if (hidden) hidden.value = pakai.toFixed(2);
                        }

                        // Attach listeners
                        ['plastik_awal', 'plastik_akhir'].forEach(function(cls) {
                            const el = document.querySelector('.' + cls);
                            if (el) {
                                el.addEventListener('input', updatePlastikPakai);
                                el.addEventListener('change', updatePlastikPakai);
                            }
                        });

                        ['material_awal', 'material_akhir'].forEach(function(cls) {
                            const el = document.querySelector('.' + cls);
                            if (el) {
                                el.addEventListener('input', updateMaterialPakai);
                                el.addEventListener('change', updateMaterialPakai);
                            }
                        });

                        // Run immediately (form loaded via AJAX, DOMContentLoaded already fired)
                        updatePlastikPakai();
                        updateMaterialPakai();
                    })();
                </script>
            <?php endif; ?>

            <!-- Ukuran PE + Awal/Akhir/Pemakaian (Powerjet only) -->
            <?php if ($isPowerjetMesin): ?>
                <div class="mt-2 pt-2" style="border-top: 1px dashed #dee2e6;">
                    <div class="mb-2">
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                            <i class="fas fa-film text-info me-1"></i> Ukuran PE
                        </label>
                        <select name="ukuran_pe" class="form-select form-select-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <option value="">-- Pilih Ukuran PE --</option>
                            <option value="65x115x0,03 (0,045)" <?= ($isEdit && $packaging && $packaging['ukuran_pe'] === '65x115x0,03 (0,045)') ? 'selected' : '' ?>>65x115x0,03 (0,045)</option>
                            <option value="135x110x0,03 (0,080)" <?= ($isEdit && $packaging && $packaging['ukuran_pe'] === '135x110x0,03 (0,080)') ? 'selected' : '' ?>>135x110x0,03 (0,080)</option>
                            <option value="100x80x0,03" <?= ($isEdit && $packaging && $packaging['ukuran_pe'] === '100x80x0,03') ? 'selected' : '' ?>>100x80x0,03</option>
                            <option value="60x90x0,03 (0,035)" <?= ($isEdit && $packaging && $packaging['ukuran_pe'] === '60x90x0,03 (0,035)') ? 'selected' : '' ?>>60x90x0,03 (0,035)</option>
                        </select>
                    </div>
                    <div class="p-2 border rounded" style="background: #f8f9fc; border-radius: 6px !important;">
                        <div class="text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                            <i class="fas fa-film text-info me-1"></i> PE – Awal / Akhir / Pemakaian
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Awal</span>
                            <input type="number" step="0.01" name="pe_awal"
                                class="form-control form-control-xs pe_awal"
                                placeholder="0.00"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['pe_awal'] ?? '') : '' ?>">
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Akhir</span>
                            <input type="number" step="0.01" name="pe_akhir"
                                class="form-control form-control-xs pe_akhir"
                                placeholder="0.00"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['pe_akhir'] ?? '') : '' ?>">
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light" style="font-size: 0.7rem; width: 70px;">Pakai</span>
                            <input type="number" step="0.01"
                                class="form-control form-control-xs pe_pemakaian bg-light"
                                placeholder="0.00" readonly
                                value="<?= ($isEdit && $packaging) ? esc($packaging['pe_pemakaian'] ?? '') : '' ?>">
                            <input type="hidden" name="pe_pemakaian" class="pe_pemakaian_hidden"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['pe_pemakaian'] ?? '') : '' ?>">
                        </div>
                    </div>
                    <script>
                        (function() {
                            function calcPe(scope) {
                                const awal = parseFloat(scope.querySelector('.pe_awal')?.value) || 0;
                                const akhir = parseFloat(scope.querySelector('.pe_akhir')?.value) || 0;
                                const pakai = Math.abs(awal - akhir);
                                const display = scope.querySelector('.pe_pemakaian');
                                const hidden = scope.querySelector('.pe_pemakaian_hidden');
                                if (display) display.value = pakai.toFixed(2);
                                if (hidden) hidden.value = pakai.toFixed(2);
                            }

                            function attachPe() {
                                document.querySelectorAll('.pe_awal, .pe_akhir').forEach(function(el) {
                                    if (el.dataset.peBound) return;
                                    el.dataset.peBound = '1';
                                    el.addEventListener('input', function() {
                                        const scope = el.closest('form') || el.closest('.modal-content') || document.body;
                                        calcPe(scope);
                                    });
                                    el.addEventListener('change', function() {
                                        const scope = el.closest('form') || el.closest('.modal-content') || document.body;
                                        calcPe(scope);
                                    });
                                });
                            }
                            window.initPePemakaian = function(scope) {
                                scope = scope || document.body;
                                calcPe(scope);
                                attachPe();
                            };
                            attachPe();
                            setTimeout(attachPe, 300);
                        })();
                    </script>
                </div>
            <?php endif; ?>

            <!-- Ukuran Box/Karung + Awal/Akhir/Pemakaian (Powerjet only) -->
            <?php if ($isPowerjetMesin): ?>
                <div class="mt-2 pt-2" style="border-top: 1px dashed #dee2e6;">
                    <div class="mb-2">
                        <label class="form-label text-muted fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                            <i class="fas fa-box text-warning me-1"></i> Ukuran Box / Karung
                        </label>
                        <select name="ukuran_box_karung" class="form-select form-select-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            <option value="">-- Pilih Ukuran Box / Karung --</option>
                            <option value="90x130" <?= ($isEdit && $packaging && $packaging['ukuran_box_karung'] === '90x130') ? 'selected' : '' ?>>90x130</option>
                            <option value="600x400x300 (SW)" <?= ($isEdit && $packaging && $packaging['ukuran_box_karung'] === '600x400x300 (SW)') ? 'selected' : '' ?>>600x400x300 (SW)</option>
                            <option value="578x540x541 (SW)" <?= ($isEdit && $packaging && $packaging['ukuran_box_karung'] === '578x540x541 (SW)') ? 'selected' : '' ?>>578x540x541 (SW)</option>
                        </select>
                    </div>
                    <div class="p-2 border rounded" style="background: #f8f9fc; border-radius: 6px !important;">
                        <div class="text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.3px; text-transform: uppercase;">
                            <i class="fas fa-box text-warning me-1"></i> Box / Karung – Awal / Akhir / Pemakaian
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Awal</span>
                            <input type="number" step="0.01" name="box_karung_awal"
                                class="form-control form-control-xs box_karung_awal"
                                placeholder="0.00"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['box_karung_awal'] ?? '') : '' ?>">
                        </div>
                        <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text bg-white" style="font-size: 0.7rem; width: 70px;">Akhir</span>
                            <input type="number" step="0.01" name="box_karung_akhir"
                                class="form-control form-control-xs box_karung_akhir"
                                placeholder="0.00"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['box_karung_akhir'] ?? '') : '' ?>">
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light" style="font-size: 0.7rem; width: 70px;">Pakai</span>
                            <input type="number" step="0.01"
                                class="form-control form-control-xs box_karung_pemakaian bg-light"
                                placeholder="0.00" readonly
                                value="<?= ($isEdit && $packaging) ? esc($packaging['box_karung_pemakaian'] ?? '') : '' ?>">
                            <input type="hidden" name="box_karung_pemakaian" class="box_karung_pemakaian_hidden"
                                value="<?= ($isEdit && $packaging) ? esc($packaging['box_karung_pemakaian'] ?? '') : '' ?>">
                        </div>
                    </div>
                    <script>
                        (function() {
                            function calcBk(scope) {
                                const awal = parseFloat(scope.querySelector('.box_karung_awal')?.value) || 0;
                                const akhir = parseFloat(scope.querySelector('.box_karung_akhir')?.value) || 0;
                                const pakai = Math.abs(awal - akhir);
                                const display = scope.querySelector('.box_karung_pemakaian');
                                const hidden = scope.querySelector('.box_karung_pemakaian_hidden');
                                if (display) display.value = pakai.toFixed(2);
                                if (hidden) hidden.value = pakai.toFixed(2);
                            }

                            function attachBk() {
                                document.querySelectorAll('.box_karung_awal, .box_karung_akhir').forEach(function(el) {
                                    if (el.dataset.bkBound) return;
                                    el.dataset.bkBound = '1';
                                    el.addEventListener('input', function() {
                                        const scope = el.closest('form') || el.closest('.modal-content') || document.body;
                                        calcBk(scope);
                                    });
                                    el.addEventListener('change', function() {
                                        const scope = el.closest('form') || el.closest('.modal-content') || document.body;
                                        calcBk(scope);
                                    });
                                });
                            }
                            window.initBoxKarungPemakaian = function(scope) {
                                scope = scope || document.body;
                                calcBk(scope);
                                attachBk();
                            };
                            attachBk();
                            setTimeout(attachBk, 300);
                        })();
                    </script>
                </div>
            <?php endif; ?>

            <!-- Hidden field for legacy support if needed -->
            <input type="hidden" name="box_karung_nicktainer" value="<?= ($isEdit && $packaging) ? $packaging['box_karung_nicktainer'] : '0' ?>">
        </div>
    </div>
</div>

<!-- Durasi Penimbangan Section (8 Hours Timed Weighing) - ONLY FOR BECUM, POWERJET1, POWERJET2 -->
<?php
// Only show penimbangan section for specific mesin: Becum, Powerjet1, Powerjet2
$showPenimbanganSection = false;

if ($isEdit) {
    $editMesinNama = strtolower(str_replace([' ', '-', '_'], '', $header['nama_mesin'] ?? ''));
    $showPenimbanganSection = in_array($editMesinNama, ['becum', 'powerjet1', 'powerjet2']);
} else {
    $showPenimbanganSection = in_array($contextMesinSlug, ['becum', 'powerjet1', 'powerjet2']);
}
?>

<?php if ($showPenimbanganSection): ?>
    <?php
    // Get penimbangan data for form (Add or Edit)
    // In edit mode: $penimbanganData is passed from controller (loaded from oromaster.penimbangan_jam)
    // In add mode: default zeros
    if (!isset($penimbanganData) || !is_array($penimbanganData)) {
        $penimbanganData = [];
        for ($h = 1; $h <= 8; $h++) {
            $penimbanganData["jam_{$h}_galon"] = 0;
            $penimbanganData["jam_{$h}_flash"] = 0;
        }
    }
    ?>
    <?= view('produksi/partials/penimbangan_jam_section', [
        'mode'             => $isEdit ? 'edit' : 'add',
        'penimbanganData'  => $penimbanganData,
        'errors'           => session('errors') ?? [],
        'isPowerjetMesin'  => $isPowerjetMesin ?? false,
    ]) ?>
<?php endif; ?>

<?php if ($isPowerjetMesin ?? false): ?>
    <?= view('produksi/partials/kode_cap_section', [
        'kodeCapData' => $kodeCapData ?? [],
        'isEdit'      => $isEdit,
    ]) ?>
<?php endif; ?>

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
    'DT19' => 'Setting mesin',
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

// Pisahkan downtime: active (masih berjalan) vs completed (sudah selesai)
$activeDowntimes = [];
$completedDowntimes = [];
if ($isEdit && isset($downtimes) && !empty($downtimes)) {
    foreach ($downtimes as $dt) {
        if (empty($dt['waktu_selesai'])) {
            $activeDowntimes[] = $dt;
        } else {
            $completedDowntimes[] = $dt;
        }
    }
}
?>

<!-- ACTIVE DOWNTIME STATUS - Read Only Display -->
<?php if (!empty($activeDowntimes)): ?>
    <div class="mb-3 p-3 bg-warning bg-opacity-10 border border-warning rounded" style="border-radius: 12px;">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-spinner fa-spin text-warning" style="font-size: 1.2rem;"></i>
            <strong class="text-warning">Downtime Sedang Berjalan</strong>
        </div>
        <?php foreach ($activeDowntimes as $act): ?>
            <div class="small mb-1">
                <span class="badge bg-warning text-dark">Mulai: <?= date('d/m/Y H:i', strtotime($act['waktu_mulai'] ?? 'now')) ?></span>
                <span class="badge bg-light text-dark">Status: Aktif</span>
                <?php if (!empty($act['problem'])): ?>
                    <br><small class="text-muted">Problem: <?= esc(substr($act['problem'], 0, 50)) ?><?= strlen($act['problem']) > 50 ? '...' : '' ?></small>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <small class="text-muted d-block mt-2"><i class="fas fa-info-circle me-1"></i>Downtime aktif ini akan muncul untuk diedit setelah dihentikan.</small>
    </div>
<?php endif; ?>

<!-- COMPLETED DOWNTIMES - Editable Cards -->
<div class="dt-list-container mb-4" id="dt_list_container">
    <?php if (!empty($completedDowntimes)): ?>
        <?php foreach ($completedDowntimes as $idx => $dt): ?>
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
                    <input type="hidden" name="downtimes[<?= $idx ?>][id]" value="<?= $dt['id'] ?? '' ?>">
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
                    <div class="dt-field">
                        <label class="dt-label">Jenis Downtime</label>
                        <select name="downtimes[<?= $idx ?>][alasan]" class="form-select form-select-sm">
                            <option value="">-- Pilih Jenis --</option>
                            <?php foreach ($dtReasons as $code => $desc):
                                $val = "$code - $desc";
                            ?>
                                <option value="<?= esc($val) ?>" <?= ($dt['alasan_downtime'] == $val) ? 'selected' : '' ?>>
                                    <?= esc($val) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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

<!-- JavaScript for Extra Shifts Management (Produksi) -->
<script>
    (function() {
        'use strict';

        let _extraShiftCounterProduksi = 0;

        /**
         * Add a new extra shift row to the Produksi form
         */
        window.addExtraShiftRowProduksi = function(labelVal, realisasiVal) {
            const container = document.getElementById('produksi_extra_shifts_container');
            if (!container) return;

            const idx = _extraShiftCounterProduksi++;
            const row = document.createElement('div');
            row.className = 'row align-items-center mb-2 extra-shift-row-produksi';
            row.innerHTML =
                '<div class="col-md-6">' +
                '<div class="input-group input-group-sm">' +
                '<div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>' +
                '<input type="text" class="form-control extra-shift-label" placeholder="Nama shift (cth: Shift 4)" value="' + escapeHtml(labelVal || '') + '">' +
                '</div>' +
                '</div>' +
                '<div class="col-md-4">' +
                '<div class="input-group input-group-sm">' +
                '<input type="number" class="form-control extra-shift-realisasi" placeholder="Realisasi (pcs)" value="' + escapeHtml(realisasiVal || '') + '">' +
                '<div class="input-group-append"><span class="input-group-text">pcs</span></div>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-2">' +
                '<button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeExtraShiftRowProduksi(this)">' +
                '<i class="fas fa-times"></i>' +
                '</button>' +
                '</div>';

            // Remove empty state if exists
            const emptyState = container.querySelector('.text-muted');
            if (emptyState) emptyState.remove();

            container.appendChild(row);
            updateExtraShiftsHiddenField();
        };

        /**
         * Remove an extra shift row from the Produksi form
         */
        window.removeExtraShiftRowProduksi = function(btn) {
            const row = btn.closest('.extra-shift-row-produksi');
            if (row) row.remove();

            // Show empty state if no rows left
            const container = document.getElementById('produksi_extra_shifts_container');
            const rowCount = container ? container.querySelectorAll('.extra-shift-row-produksi').length : 0;
            if (rowCount === 0 && container) {
                container.innerHTML = '<div class="text-muted small text-center py-2">' +
                    '<i class="fas fa-inbox me-1"></i> Belum ada extra shift' +
                    '</div>';
            }

            updateExtraShiftsHiddenField();
        };

        /**
         * Update hidden field with JSON serialized extra shifts data
         */
        function updateExtraShiftsHiddenField() {
            const container = document.getElementById('produksi_extra_shifts_container');
            const hiddenField = document.getElementById('extra_realisasi_shifts_hidden');

            if (!container || !hiddenField) return;

            const rows = container.querySelectorAll('.extra-shift-row-produksi');
            const data = [];

            rows.forEach(function(row) {
                const label = row.querySelector('.extra-shift-label')?.value || '';
                const realisasi = row.querySelector('.extra-shift-realisasi')?.value || '';

                if (label.trim()) {
                    data.push({
                        label: label.trim(),
                        realisasi: realisasi.trim()
                    });
                }
            });

            hiddenField.value = JSON.stringify(data);
        }

        /**
         * Escape HTML special characters
         */
        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        /**
         * Update hidden field whenever extra shift inputs change
         */
        document.addEventListener('input', function(e) {
            if (e.target && (e.target.classList.contains('extra-shift-label') ||
                    e.target.classList.contains('extra-shift-realisasi'))) {
                updateExtraShiftsHiddenField();
            }
        });

        /**
         * Clear extra shifts when Tambah modal opens
         */
        document.addEventListener('DOMContentLoaded', function() {
            const addProduksiModal = document.querySelector('[id*="addProduksi"]') ||
                document.querySelector('[data-bs-target*="addProduksi"]');

            if (addProduksiModal) {
                // For bootstrap 5
                addProduksiModal.addEventListener('show.bs.modal', function() {
                    const container = document.getElementById('produksi_extra_shifts_container');
                    if (container) {
                        container.innerHTML = '<div class="text-muted small text-center py-2">' +
                            '<i class="fas fa-inbox me-1"></i> Belum ada extra shift' +
                            '</div>';
                    }
                    const hiddenField = document.getElementById('extra_realisasi_shifts_hidden');
                    if (hiddenField) hiddenField.value = '[]';
                });
            }
        });
    })();
</script>