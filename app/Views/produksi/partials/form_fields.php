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

    <div class="col-lg-6 col-md-4">
        <label class="form-label mb-2">Operator</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
            <input type="text" class="form-control operator border-start-0" placeholder="Nama Operator" disabled value="<?= $isEdit ? esc($header['operator'] ?? esc(session('username') ?? '')) : esc(session('username') ?? '') ?>">
            <input type="hidden" name="operator" class="operator_hidden" value="<?= $isEdit ? esc($header['operator'] ?? esc(session('username') ?? '')) : esc(session('username') ?? '') ?>">
        </div>
    </div>
</div>

<!-- 2x2 Grid Layout -->
<div class="row g-4 mb-4">
    <!-- Grid 1: Data Produksi Per Jam (Top Left) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; height: 100%;">
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
                            $hours = ['06-07','07-08','08-09','09-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-00','00-01','01-02','02-03','03-04','04-05','05-06'];
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
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm table_jam_form mb-0 align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px;">JAM</th>
                                <th class="px-4 py-3 text-muted fw-600" style="font-size: 0.85rem; letter-spacing: 0.5px;">HASIL</th>
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
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid 2: Ringkasan Hasil Produksi (Top Right) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; height: 100%;">
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                <h6 class="mb-0 text-dark fw-600">
                    <i class="fas fa-chart-bar text-success me-2"></i> Ringkasan Hasil Produksi
                </h6>
            </div>
            
            <div class="card-body px-4 py-4">
                <div class="row g-3">
                    <!-- Total Bagus -->
                    <div class="col-12">
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
                    <div class="col-12">
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
    <div class="col-lg-6">
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
                <div class="col-3"><input type="text" name="materials[0][lot_a]" placeholder="Lot A" class="form-control form-control-xs lot_a" value="<?= $mat ? esc($mat['lot_a']) : '' ?>"></div>
                <div class="col-3"><input type="text" name="materials[0][lot_b]" placeholder="Lot B" class="form-control form-control-xs lot_b" value="<?= $mat ? esc($mat['lot_b']) : '' ?>"></div>
                <div class="col-3"><input type="text" name="materials[0][lot_c]" placeholder="Lot C" class="form-control form-control-xs lot_c" value="<?= $mat ? esc($mat['lot_c']) : '' ?>"></div>
                <div class="col-3"><input type="text" name="materials[0][lot_d]" placeholder="Lot D" class="form-control form-control-xs lot_d" value="<?= $mat ? esc($mat['lot_d']) : '' ?>"></div>
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
                <input type="text" name="plastik_ukuran" placeholder="Ukuran Plastik" class="form-control form-control-xs plastik_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_ukuran']) : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
            </div>

            <!-- Box Section -->
            <div class="mb-3">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-box text-warning small"></i></span>
                    <input type="text" name="box" placeholder="Box (pcs)" class="form-control border-start-0 box" value="<?= ($isEdit && $packaging) ? $packaging['box'] : '' ?>">
                </div>
                <input type="text" name="box_ukuran" placeholder="Ukuran Box" class="form-control form-control-xs box_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['box_ukuran']) : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
            </div>

            <!-- Karung Section -->
            <div class="mb-3">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-archive text-secondary small"></i></span>
                    <input type="text" name="karung" placeholder="Karung (pcs)" class="form-control border-start-0 karung" value="<?= ($isEdit && $packaging) ? $packaging['karung'] : '' ?>">
                </div>
                <input type="text" name="karung_ukuran" placeholder="Ukuran Karung" class="form-control form-control-xs karung_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['karung_ukuran']) : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
            </div>

            <!-- Nicktainer Section -->
            <div class="mb-0">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-drum text-primary small"></i></span>
                    <input type="text" name="nicktainer" placeholder="Nicktainer (pcs)" class="form-control border-start-0 nicktainer" value="<?= ($isEdit && $packaging) ? ($packaging['nicktainer'] ?? '') : '' ?>">
                </div>
                <input type="text" name="nicktainer_ukuran" placeholder="Ukuran Nicktainer" class="form-control form-control-xs nicktainer_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['nicktainer_ukuran'] ?? '') : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
            </div>

            <!-- Hidden field for legacy support if needed -->
            <input type="hidden" name="box_karung_nicktainer" value="<?= ($isEdit && $packaging) ? $packaging['box_karung_nicktainer'] : '0' ?>">
        </div>
    </div>
</div>

<div class="section-title d-flex justify-content-between align-items-center">
    <span><i class="fas fa-stopwatch"></i> Downtime & Machine Stops</span>
    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill btn_add_dt"><i class="fas fa-plus mr-1"></i> Downtime</button>
</div>
<div class="table-responsive border rounded-lg mb-4">
    <table class="table table-sm table_downtime_form mb-0">
        <thead class="bg-light">
            <tr class="text-xs">
                <th class="px-3">Alasan</th>
                <th>Problem</th>
                <th>Action Plant</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th width="120">Mnt</th>
                <th width="50">#</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $dtReasons = [
                'DT1' => 'Improvement', 'DT2' => 'PLN Off', 'DT3' => 'Change Product', 'DT4' => 'Planned Maintenance', 
                'DT5' => 'Trial Produk', 'DT6' => 'Trial mould dan emboss', 'DT7' => 'Bencana Alam', 'DT8' => 'Hari libur dan istirahat',
                'DT10' => 'Sanitasi', 'DT11' => 'Warm up', 'DT12' => 'Stop msn atas permint khusus', 'DT13' => 'Quality Inspection',
                'DT14' => 'Kerusakan Mesin Utama', 'DT15' => 'Kerusakan Utility', 'DT16' => 'Kerusakan Tools', 'DT17' => 'Alarm karena Error',
                'DT18' => 'Alarm karena kesalahan proses', 'DT20' => 'Salah memasukan material', 'DT21' => 'Material tidak tersedia',
                'DT22' => 'Man power tidak mencukupi', 'DT23' => 'Tidak ada tmpt Menyimpan produk', 'DT24' => 'Product Tidak Standar',
                'DT25' => 'Material tidak standar', 'DT26' => 'Coloran tidak standar', 'DT27' => 'No Order', 'DT28' => 'Cleaning Mold',
                'DT29' => 'Cleaning Tooling', 'DT30' => 'Cleaning Mesin dan Tools', 'DT31' => 'Kegagalan Design Produk', 'DT32' => 'Ketiadaan kelengkapan kerja'
            ];
            if ($isEdit && isset($downtimes)): ?>
                <?php foreach ($downtimes as $idx => $dt): ?>
                    <tr>
                        <td class="px-3">
                            <select name="downtimes[<?= $idx ?>][alasan]" class="form-select form-select-sm">
                                <option value="">-- Pilih Alasan --</option>
                                <?php foreach ($dtReasons as $code => $desc): 
                                    $val = "$code - $desc";
                                ?>
                                    <option value="<?= $val ?>" <?= ($dt['alasan_downtime'] == $val) ? 'selected' : '' ?>><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><textarea name="downtimes[<?= $idx ?>][problem]" class="form-control form-control-sm" rows="2" placeholder="Problem..."><?= esc($dt['problem'] ?? '') ?></textarea></td>
                        <td><textarea name="downtimes[<?= $idx ?>][action_plant]" class="form-control form-control-sm" rows="2" placeholder="Action Plant..."><?= esc($dt['action_plant'] ?? '') ?></textarea></td>
                        <td><input type="datetime-local" name="downtimes[<?= $idx ?>][mulai]" value="<?= str_replace(' ', 'T', substr($dt['waktu_mulai'], 0, 16)) ?>" class="form-control form-control-sm dt-mulai"></td>
                        <td><input type="datetime-local" name="downtimes[<?= $idx ?>][selesai]" value="<?= str_replace(' ', 'T', substr($dt['waktu_selesai'], 0, 16)) ?>" class="form-control form-control-sm dt-selesai"></td>
                        <td><input type="number" name="downtimes[<?= $idx ?>][durasi]" value="<?= $dt['durasi_menit'] ?>" class="form-control form-control-sm dt-durasi"></td>
                        <td class="text-center"><button type="button" class="btn btn-link btn-sm text-danger remove-dt">×</button></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
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
