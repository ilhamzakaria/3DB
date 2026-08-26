<?php 
$totalDuration = 0;
foreach ($downtimes as $dt) {
    $totalDuration += round((strtotime($dt['waktu_selesai']) - strtotime($dt['waktu_mulai'])) / 60);
}
?>
<div class="modal-header">
    <h5 class="modal-title font-weight-bold text-primary">
        <i class="fas fa-eye mr-2"></i> Detail Laporan Produksi
    </h5>
</div>
<div class="modal-body p-4">
    <div class="row g-4">
        <div class="col-md-6">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2 text-primary"></i> Informasi Umum</h6>
            <table class="table table-sm table-borderless">
                <tr><td width="150" class="text-muted">No. SPK</td><td class="fw-bold"><?= esc($header['nomor_spk']) ?></td></tr>
                <tr><td class="text-muted">Mesin</td><td><?= esc($header['nama_mesin']) ?> (M#<?= esc($header['nomor_mesin']) ?>)</td></tr>
                <tr><td class="text-muted">Produk</td><td><?= esc($header['nama_produk']) ?></td></tr>
                <tr><td class="text-muted">Batch</td><td><?= esc($header['batch_number'] ?: '-') ?></td></tr>
                <tr><td class="text-muted">Tanggal</td><td><?= date('d M Y', strtotime($header['tanggal'])) ?></td></tr>
                <tr><td class="text-muted">Shift / Grup</td><td><?php 
                    $shiftRaw = (string)($header['shift'] ?? '');
                    if ($shiftRaw === '' || $shiftRaw === '0' || $shiftRaw === null) {
                        $shiftDisplay = '-';
                    } elseif (strpos($shiftRaw, 'extra_') === 0) {
                        $shiftNum = str_replace('extra_', '', $shiftRaw);
                        $shiftDisplay = 'Extra Shift ' . $shiftNum;
                    } else {
                        $shiftDisplay = 'Shift ' . $shiftRaw;
                    }
                    echo $shiftDisplay;
                ?> / <?= esc($header['grup'] ?: '-') ?></td></tr>
                <tr><td class="text-muted">Operator</td><td><?= esc($header['operator']) ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-chart-line me-2 text-success"></i> Kinerja Produksi</h6>
            <table class="table table-sm table-borderless">
                <tr><td width="150" class="text-muted">Total SPK</td><td><?= number_format($header['total_spk'] ?? 0) ?> pcs</td></tr>
                <tr><td class="text-muted">Target</td><td><?= number_format($header['target']) ?> pcs</td></tr>
                <tr><td class="text-muted">Isi</td><td><?= number_format($header['isi'] ?: 0) ?></td></tr>
                <tr>
                    <td class="text-muted">Hasil Bagus</td>
                    <td class="text-success fw-bold"><?= number_format($header['grand_total_bagus']) ?> pcs</td>
                </tr>
                <tr><td class="text-muted">Total Reject</td><td class="text-danger fw-bold"><?= number_format($header['grand_total_reject']) ?> pcs</td></tr>
                <tr><td class="text-muted">Cycle Time</td><td><?= esc($header['cycle_time'] ?: '-') ?> s</td></tr>
                <tr><td class="text-muted">Total Downtime</td><td class="text-danger fw-bold"><?= number_format($totalDuration) ?> min</td></tr>
            </table>
        </div>

        <div class="col-md-6">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-boxes me-2 text-info"></i> Status Produksi</h6>
            <div class="d-flex gap-3">
                <div class="flex-fill text-center p-2 bg-light rounded">
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Sisa PO</small>
                    <div class="fw-bold text-info" style="font-size: 0.95rem;"><?= number_format($header['sisa_po'] ?: 0) ?></div>
                    <small class="text-muted" style="font-size: 0.7rem;">pcs</small>
                </div>
                <div class="flex-fill text-center p-2 bg-light rounded">
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Hold</small>
                    <div class="fw-bold text-warning" style="font-size: 0.95rem;"><?= number_format($header['hold'] ?: 0) ?></div>
                    <small class="text-muted" style="font-size: 0.7rem;">pcs</small>
                </div>
                <div class="flex-fill text-center p-2 bg-light rounded">
                    <small class="text-muted d-block" style="font-size: 0.75rem;">Gumpalan</small>
                    <div class="fw-bold text-secondary" style="font-size: 0.95rem;"><?= number_format($header['gumpalan'] ?: 0, 2) ?></div>
                    <small class="text-muted" style="font-size: 0.7rem;">kg</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-history me-2 text-info"></i> Hasil Produksi Per Jam</h6>
            <table class="table table-sm table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th class="text-center">Jam</th>
                        <th class="text-center">Hasil (pcs)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jams)): ?>
                        <tr><td colspan="2" class="text-center text-muted">Tidak ada data per jam</td></tr>
                    <?php else: ?>
                        <?php foreach ($jams as $j): ?>
                            <tr>
                                <td class="text-center"><?= $j['rentang_jam'] ?></td>
                                <td class="text-center"><?= number_format($j['hasil_produksi']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-3"><i class="fas fa-exclamation-triangle me-2 text-danger"></i> Rincian Reject</h6>
            <table class="table table-sm table-bordered">
                <thead class="bg-light">
                    <tr><th>Jenis Reject</th><th class="text-center">Jumlah</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($rejects)): ?>
                        <tr><td colspan="2" class="text-center text-muted">Tidak ada reject</td></tr>
                    <?php else: ?>
                        <?php foreach ($rejects as $r): ?>
                            <tr>
                                <td><?= esc($r['jenis_reject']) ?></td>
                                <td class="text-center text-danger fw-bold"><?= number_format($r['jumlah']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-3"><i class="fas fa-boxes me-2 text-warning"></i> Material & Packaging</h6>
            <div class="p-3 bg-light rounded">
                <p class="mb-1 small"><strong class="text-dark">Material:</strong></p>
                <p class="mb-1 small"> 
                    <strong>Code:</strong> <?= !empty($materials) ? esc($materials[0]['merek_kode']) : '-' ?><br>
                    <strong>Pemakaian:</strong> <?= !empty($materials) ? $materials[0]['pemakaian'] . ' kg' : '-' ?>
                </p>
                <p class="mb-1 small"><strong>Lot:</strong> <?= !empty($materials) ? implode(', ', array_filter([$materials[0]['lot_a'], $materials[0]['lot_b'], $materials[0]['lot_c'], $materials[0]['lot_d']])) : '-' ?></p>
                
                <hr class="my-2">
                
                <p class="mb-1 small"><strong class="text-dark">Colorant:</strong></p>
                <p class="mb-1 small">
                    <strong>Code:</strong> <?= !empty($colorants) ? esc($colorants[0]['code']) : '-' ?><br>
                    <strong>Pemakaian:</strong> <?= !empty($colorants) ? $colorants[0]['pemakaian'] . ' kg' : '-' ?>
                </p>
                <p class="mb-1 small"><strong>Lot:</strong> <?= !empty($colorants) ? esc($colorants[0]['nomor_lot']) : '-' ?></p>
            
                <div class="mt-2 pt-2 border-top">
                    <p class="mb-1 small"><strong class="text-dark">Packaging :</strong></p>
                    <?php if ($packaging): ?>
                    <div class="row g-1">
                        <div class="col-6">
                            <span class="badge bg-white text-dark border w-100 text-start font-weight-normal">
                                <i class="fas fa-vial text-info me-1"></i> Plastik Bagus: <?= $packaging['plastik_bagus'] ?? '-' ?> kg
                                <br><i class="fas fa-vial text-danger me-1 ms-0"></i> Plastik Reject: <?= $packaging['plastik_reject'] ?? '-' ?> kg
                                <br><small class="text-muted ms-4">Ukuran: <?= esc($packaging['plastik_ukuran'] ?: '-') ?></small>
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-white text-dark border w-100 text-start font-weight-normal">
                                <i class="fas fa-box text-warning me-1"></i> Box: <?= $packaging['box'] ?> pcs
                                <br><small class="text-muted ms-4">Ukuran: <?= esc($packaging['box_ukuran'] ?: '-') ?></small>
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="badge bg-white text-dark border w-100 text-start font-weight-normal">
                                <i class="fas fa-archive text-secondary me-1"></i> Karung: <?= $packaging['karung'] ?> pcs
                                <br><small class="text-muted ms-4">Ukuran: <?= esc($packaging['karung_ukuran'] ?: '-') ?></small>
                            </span>
                        </div>
                        <?php if (!empty($packaging['nicktainer'])): ?>
                        <div class="col-6">
                            <span class="badge bg-white text-dark border w-100 text-start font-weight-normal">
                                <i class="fas fa-drum text-primary me-1"></i> Nicktainer: <?= $packaging['nicktainer'] ?> pcs
                                <br><small class="text-muted ms-4">Ukuran: <?= esc($packaging['nicktainer_ukuran'] ?: '-') ?></small>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                        <p class="mb-0 small text-muted">Tidak ada data packaging</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-3"><i class="fas fa-stopwatch me-2 text-danger"></i> Rincian Downtime</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Alasan</th>
                            <th>Problem</th>
                            <th>Action Plant</th>
                            <th class="text-center" width="150">Mulai</th>
                            <th class="text-center" width="150">Selesai</th>
                            <th class="text-center" width="100">Durasi (Mnt)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($downtimes)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Tidak ada data downtime</td></tr>
                        <?php else: ?>
                            <?php 
                            foreach ($downtimes as $dt): 
                                $start = strtotime($dt['waktu_mulai']);
                                $end = strtotime($dt['waktu_selesai']);
                                $duration = round(($end - $start) / 60);
                            ?>
                                <tr>
                                    <td><?= esc($dt['alasan_downtime']) ?></td>
                                    <td><small><?= esc($dt['problem'] ?? '-') ?></small></td>
                                    <td><small><?= esc($dt['action_plant'] ?? '-') ?></small></td>
                                    <td class="text-center"><small><?= date('d M Y, H:i', $start) ?></small></td>
                                    <td class="text-center"><small><?= date('d M Y, H:i', $end) ?></small></td>
                                    <td class="text-center text-danger fw-bold"><?= $duration ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="5" class="text-end text-uppercase small">Total Downtime</td>
                                <td class="text-center text-danger"><?= $totalDuration ?> Menit</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-signature me-2 text-info"></i> Tanda Tangan Operator</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="p-3 border rounded bg-light">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user-check text-primary me-2"></i>
                            <span class="fw-bold small">Operator <?php 
                                $shiftRaw2 = (string)($header['shift'] ?? '');
                                if ($shiftRaw2 === '' || $shiftRaw2 === '0') {
                                    $shiftLabel = '-';
                                } elseif (strpos($shiftRaw2, 'extra_') === 0) {
                                    $shiftLabel = 'Extra Shift ' . str_replace('extra_', '', $shiftRaw2);
                                } else {
                                    $shiftLabel = 'Shift ' . $shiftRaw2;
                                }
                                echo $shiftLabel;
                            ?></span>
                            <span class="badge bg-primary ms-2 small">Auto-filled</span>
                        </div>
                        <div class="ttd-preview-container" style="height: 80px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 8px; border: 1px solid #dee2e6;">
                            <?php if (!empty($header['auto_user_ttd'])): ?>
                                <?php
                                    $ttdPath = $header['auto_user_ttd'];
                                    // Nilai di DB sudah include "uploads/ttd/...", jadi cukup base_url() saja
                                    $ttdUrl = base_url($ttdPath);
                                ?>
                                <img src="<?= $ttdUrl ?>" class="img-fluid" style="max-height: 70px;" alt="TTD Operator"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="text-muted text-center small" style="display:none;">
                                    <i class="fas fa-exclamation-circle mb-1 d-block text-warning"></i>
                                    File TTD tidak ditemukan
                                </div>
                            <?php else: ?>
                                <div class="text-muted text-center small">
                                    <i class="fas fa-pen-fancy mb-1 d-block"></i>
                                    TTD tidak tersedia
                                </div>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted d-block mt-2 text-center">
                            <i class="fas fa-info-circle me-1"></i>Diambil otomatis dari profil operator berdasarkan shift
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-comment-alt me-2 text-secondary"></i> Catatan & Verifikasi</h6>
            <div class="p-3 border rounded mb-3 bg-light italic">
                <?= esc($header['catatan'] ?: 'Tidak ada catatan.') ?>
            </div>
            <div class="row g-2">
                <?php 
                $labels = ['Shift 1', 'Shift 2', 'Shift 3', 'Supervisor'];
                $fields = ['ttd_shift_1', 'ttd_shift_2', 'ttd_shift_3', 'ttd_spv'];
                foreach ($fields as $idx => $f): 
                ?>
                <div class="col-3 text-center">
                    <div class="border rounded p-2 bg-white">
                        <?php if (!empty($header[$f])): ?>
                            <img src="<?= base_url('uploads/ttd/' . $header[$f]) ?>" class="img-fluid mb-2" style="max-height: 60px;">
                        <?php else: ?>
                            <div class="py-4 text-muted small">Belum TTD</div>
                        <?php endif; ?>
                        <div class="fw-bold small border-top pt-1"><?= $labels[$idx] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer border-top-0 pt-0 pb-4 px-4">
    <button type="button" class="btn btn-secondary px-5 shadow-sm" data-bs-dismiss="modal">Tutup</button>
</div>
