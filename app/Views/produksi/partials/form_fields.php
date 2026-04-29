<?php
$isEdit = isset($header);
$prefix = $isEdit ? 'edit_' : '';
?>

<div class="section-title"><i class="fas fa-info-circle"></i> Informasi Utama</div>
<div class="row g-3 mb-4">
    <!-- Row 1 -->
    <div class="col-md-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Nomor SPK</label>
            <div class="form-check form-switch ms-2">
                <?php 
                $isNewSpk = false;
                if ($isEdit) {
                    $isNewSpk = true;
                    foreach ($spk_list as $s) {
                        if ($s['no_spk'] == $header['nomor_spk']) {
                            $isNewSpk = false;
                            break;
                        }
                    }
                }
                ?>
                <input class="form-check-input check_spk_baru" type="checkbox" id="<?= $prefix ?>check_spk_baru" style="cursor: pointer;" <?= $isNewSpk ? 'checked' : '' ?>>
                <label class="form-check-label text-xs fw-bold text-muted" for="<?= $prefix ?>check_spk_baru" style="cursor: pointer;">BARU</label>
            </div>
        </div>
        <div class="spk_select_wrapper" style="<?= $isNewSpk ? 'display:none;' : '' ?>">
            <select name="<?= $isNewSpk ? '' : 'nomor_spk' ?>" class="form-select select2 nomor_spk" <?= $isNewSpk ? '' : 'required' ?>>
                <option value="">-- Pilih SPK --</option>
                <?php foreach ($spk_list as $spk): ?>
                    <option value="<?= $spk['no_spk'] ?>" data-mesin="<?= $spk['nama_mesin'] ?>" data-produk="<?= $spk['nama_produk'] ?>" <?= ($isEdit && $spk['no_spk'] == $header['nomor_spk']) ? 'selected' : '' ?>>
                        <?= $spk['no_spk'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="spk_input_wrapper" style="<?= $isNewSpk ? '' : 'display:none;' ?>">
            <input type="text" name="<?= $isNewSpk ? 'nomor_spk' : '' ?>" class="form-control nomor_spk_baru" placeholder="Input SPK Baru" value="<?= $isNewSpk ? esc($header['nomor_spk']) : '' ?>">
        </div>
    </div>
    <div class="col-md-3">
        <label class="form-label">Nama Mesin</label>
        <input type="text" name="nama_mesin" class="form-control nama_mesin" placeholder="Auto-filled" value="<?= $isEdit ? esc($header['nama_mesin']) : '' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Nama Produksi</label>
        <input type="text" name="nama_produksi" class="form-control nama_produksi" placeholder="Auto-filled" value="<?= $isEdit ? esc($header['nama_produksi']) : '' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Batch Number</label>
        <input type="text" name="batch_number" class="form-control batch_number" placeholder="No. Batch" value="<?= $isEdit ? esc($header['batch_number']) : '' ?>">
    </div>
    
    <!-- Row 2 -->
    <div class="col-md-2">
        <label class="form-label">Shift</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-clock text-muted"></i></span>
            <select name="shift" class="form-select shift border-start-0">
                <option value="1" <?= ($isEdit && $header['shift'] == '1') ? 'selected' : '' ?>>1</option>
                <option value="2" <?= ($isEdit && $header['shift'] == '2') ? 'selected' : '' ?>>2</option>
                <option value="3" <?= ($isEdit && $header['shift'] == '3') ? 'selected' : '' ?>>3</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <label class="form-label">Grup</label>
        <input type="text" name="grup" class="form-control grup" placeholder="Grup" value="<?= $isEdit ? esc($header['grup']) : '' ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">No. Mesin</label>
        <input type="text" name="nomor_mesin" class="form-control nomor_mesin" placeholder="M#" value="<?= $isEdit ? esc($header['nomor_mesin']) : '' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Packing</label>
        <input type="text" name="packing" class="form-control packing" placeholder="Tipe Packing" value="<?= $isEdit ? esc($header['packing']) : '' ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" class="form-control" value="<?= $isEdit ? $header['tanggal'] : date('Y-m-d') ?>">
    </div>
    
    <!-- Row 3 -->
    <div class="col-md-2">
        <label class="form-label">Cycle Time (sec)</label>
        <div class="input-group">
            <input type="text" name="cycle_time" class="form-control cycle_time" placeholder="0.00" value="<?= $isEdit ? esc($header['cycle_time']) : '' ?>">
            <span class="input-group-text bg-light small">s</span>
        </div>
    </div>
    <div class="col-md-2">
        <label class="form-label">Target (pcs)</label>
        <input type="number" name="target" class="form-control target" placeholder="0" value="<?= $isEdit ? esc($header['target']) : '' ?>">
    </div>
    <div class="col-md-2">
        <label class="form-label">Isi</label>
        <input type="number" name="isi" class="form-control isi" placeholder="0" value="<?= $isEdit ? esc($header['isi']) : '' ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Operator</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
            <input type="text" name="operator" class="form-control operator border-start-0" placeholder="Nama Operator" value="<?= $isEdit ? esc($header['operator']) : '' ?>">
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-7">
        <div class="section-title d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clock"></i> Data Produksi Per Jam</span>
            <div class="input-group input-group-sm" style="width: auto;">
                <select class="form-select select_jam" style="width: 110px; border-radius: 8px 0 0 8px !important;">
                    <option value="">-- Jam --</option>
                    <?php 
                    $hours = ['06-07','07-08','08-09','09-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-00','00-01','01-02','02-03','03-04','04-05','05-06'];
                    foreach ($hours as $h): ?>
                        <option value="<?= $h ?>"><?= $h ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-primary btn_add_jam_row" style="border-radius: 0 8px 8px 0 !important;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="table-responsive border rounded-lg bg-light shadow-sm" style="max-height: 300px; overflow-y: auto; border-radius: 12px !important;">
            <table class="table table-sm table_jam_form mb-0">
                <thead class="bg-white sticky-top">
                    <tr class="text-xs text-muted text-uppercase">
                        <th class="px-3 py-2" width="120">Rentang Jam</th>
                        <th class="py-2">Hasil Produksi (Box/Pack)</th>
                        <th class="text-center py-2" width="60">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($isEdit && isset($jams)): ?>
                        <?php foreach ($jams as $idx => $jam): ?>
                            <tr data-jam="<?= $jam['rentang_jam'] ?>">
                                <td class="px-3" width="100">
                                    <input type="text" name="jam_data[<?= $idx ?>][rentang_jam]" value="<?= $jam['rentang_jam'] ?>" class="form-control-plaintext text-center font-weight-bold" readonly style="width: 80px;">
                                </td>
                                <td><input type="number" name="jam_data[<?= $idx ?>][hasil_produksi]" value="<?= $jam['hasil_produksi'] ?>" class="form-control form-control-sm jam-hasil text-center" required placeholder="0"></td>
                                <td class="text-center" width="50">
                                    <button type="button" class="btn btn-link btn-sm text-danger remove-jam" title="Hapus" style="text-decoration: none; font-size: 1.2rem;">×</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-white sticky-bottom border-top">
                    <tr class="font-weight-bold">
                        <td class="px-3 py-2 text-primary">TOTAL HASIL</td>
                        <?php 
                        $totalHasil = 0;
                        if ($isEdit && isset($jams)) {
                            foreach ($jams as $j) $totalHasil += $j['hasil_produksi'];
                        }
                        ?>
                        <td class="py-2 text-primary grand_total_hasil_display"><?= $totalHasil ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="col-md-5">
        <div class="section-title d-flex justify-content-between align-items-center">
            <span><i class="fas fa-exclamation-triangle"></i> Kualitas & Reject</span>
            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 btn_open_reject_modal" data-bs-toggle="modal" data-bs-target="#modalReject" data-target-storage="<?= $prefix ?>reject_storage">
                <i class="fas fa-plus me-1"></i> Input Reject
            </button>
        </div>
        <div class="p-4 border rounded-lg bg-light shadow-sm" style="border-radius: 12px !important;">
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label text-xs fw-bold text-success mb-1 uppercase">HASIL BAGUS</label>
                    <div class="input-group">
                        <input type="number" name="grand_total_bagus" class="form-control grand_total_bagus bg-white font-weight-bold text-success border-success" readonly style="font-size: 1.1rem;" value="<?= $isEdit ? $header['grand_total_bagus'] : '' ?>">
                        <span class="input-group-text bg-success text-white small">pcs</span>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label text-xs fw-bold text-danger mb-1 uppercase">TOTAL REJECT</label>
                    <div class="input-group">
                        <input type="number" name="grand_total_reject" class="form-control grand_total_reject bg-white font-weight-bold text-danger border-danger" readonly style="font-size: 1.1rem;" value="<?= $isEdit ? $header['grand_total_reject'] : '' ?>">
                        <span class="input-group-text bg-danger text-white small">pcs</span>
                    </div>
                    <div class="reject_storage" id="<?= $prefix ?>reject_storage">
                        <?php if ($isEdit && isset($rejects)): ?>
                            <?php foreach ($rejects as $r): ?>
                                <input type="hidden" name="rejects[<?= $r['jenis_reject'] ?>]" value="<?= $r['jumlah'] ?>" data-type="<?= $r['jenis_reject'] ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-4">
                    <label class="form-label text-xs fw-bold text-muted mb-1 uppercase">SISA PO</label>
                    <input type="number" name="sisa_po" class="form-control sisa_po shadow-sm" placeholder="0" value="<?= $isEdit ? $header['sisa_po'] : '' ?>">
                </div>
                <div class="col-4">
                    <label class="form-label text-xs fw-bold text-muted mb-1 uppercase">HOLD</label>
                    <input type="number" name="hold" class="form-control hold shadow-sm" placeholder="0" value="<?= $isEdit ? $header['hold'] : '' ?>">
                </div>
                <div class="col-4">
                    <label class="form-label text-xs fw-bold text-muted mb-1 uppercase">GUMPALAN</label>
                    <input type="number" name="gumpalan" class="form-control gumpalan shadow-sm" placeholder="0" value="<?= $isEdit ? $header['gumpalan'] : '' ?>">
                </div>

            </div>
        </div>
    </div>
</div>

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
                    <input type="number" name="plastik" placeholder="Plastik (pcs)" class="form-control border-start-0 plastik" value="<?= ($isEdit && $packaging) ? $packaging['plastik'] : '' ?>">
                </div>
                <input type="text" name="plastik_ukuran" placeholder="Ukuran Plastik" class="form-control form-control-xs plastik_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['plastik_ukuran']) : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
            </div>

            <!-- Box Section -->
            <div class="mb-3">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-box text-warning small"></i></span>
                    <input type="number" name="box" placeholder="Box (pcs)" class="form-control border-start-0 box" value="<?= ($isEdit && $packaging) ? $packaging['box'] : '' ?>">
                </div>
                <input type="text" name="box_ukuran" placeholder="Ukuran Box" class="form-control form-control-xs box_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['box_ukuran']) : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
            </div>

            <!-- Karung Section -->
            <div class="mb-0">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text bg-white border-end-0" style="width: 32px;"><i class="fas fa-archive text-secondary small"></i></span>
                    <input type="number" name="karung" placeholder="Karung (pcs)" class="form-control border-start-0 karung" value="<?= ($isEdit && $packaging) ? $packaging['karung'] : '' ?>">
                </div>
                <input type="text" name="karung_ukuran" placeholder="Ukuran Karung" class="form-control form-control-xs karung_ukuran" value="<?= ($isEdit && $packaging) ? esc($packaging['karung_ukuran']) : '' ?>" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
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
                <th>Mulai</th>
                <th>Selesai</th>
                <th width="80">Mnt</th>
                <th width="40">#</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($isEdit && isset($downtimes)): ?>
                <?php foreach ($downtimes as $idx => $dt): ?>
                    <tr>
                        <td class="px-3"><input type="text" name="downtimes[<?= $idx ?>][alasan]" value="<?= esc($dt['alasan_downtime']) ?>" class="form-control form-control-sm"></td>
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

<div class="section-title"><i class="fas fa-comment-alt"></i> Catatan & Tanda Tangan</div>
<div class="row g-4 mb-4">
    <div class="col-md-5">
        <label class="form-label text-xs fw-bold text-muted mb-2 uppercase">Catatan Produksi</label>
        <textarea name="catatan" class="form-control shadow-sm" rows="6" placeholder="Masukkan catatan atau kendala produksi di sini..." style="border-radius: 12px;"><?= $isEdit ? esc($header['catatan']) : '' ?></textarea>
    </div>
    <div class="col-md-7">
        <label class="form-label text-xs fw-bold text-muted mb-2 uppercase">Verifikasi Tanda Tangan</label>
        <div class="row g-2">
            <?php 
            $labels = ['Shift 1', 'Shift 2', 'Shift 3', 'Supervisor'];
            $fields = ['ttd_shift_1', 'ttd_shift_2', 'ttd_shift_3', 'ttd_spv'];
            foreach ($fields as $idx => $f): 
            ?>
            <div class="col-3 text-center">
                <div class="signature-box shadow-sm bg-white border" style="border-radius: 15px; padding: 12px;" onclick="this.querySelector('input').click()">
                    <div class="mb-2" style="height: 60px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 10px;">
                        <?php if ($isEdit && !empty($header[$f])): ?>
                            <img class="signature-preview img-fluid" style="max-height: 50px;" src="<?= base_url('uploads/ttd/' . $header[$f]) ?>">
                        <?php else: ?>
                            <img class="signature-preview img-fluid" style="max-height: 50px;" src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 50'%3E%3Ctext x='50' y='25' font-family='sans-serif' font-size='8' fill='%23a3aed1' text-anchor='middle' dominant-baseline='middle'%3EKLIK UNTUK UPLOAD%3C/text%3E%3C/svg%3E">
                        <?php endif; ?>
                    </div>
                    <input type="file" name="<?= $f ?>" class="d-none sig-input" onchange="previewSig(this)">
                    <div class="text-xs font-weight-bold text-uppercase mt-1"><?= $labels[$idx] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
