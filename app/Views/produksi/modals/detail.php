<?php
$totalDuration = 0;
foreach ($downtimes as $dt) {
    $totalDuration += (int)($dt['durasi_menit'] ?? 0);
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
                <tr>
                    <td width="150" class="text-muted">No. SPK</td>
                    <td class="fw-bold"><?= esc($header['no_spk']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Mesin & No Mesin</td>
                    <td><?= esc($header['nama_mesin']) ?><?php if (!empty($ppic_nomor_mesin)): ?> dan <?= esc($ppic_nomor_mesin) ?><?php endif; ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Produk</td>
                    <td><?= esc($header['nama_produk']) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Batch</td>
                    <td><?= esc($header['batch_number'] ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Type Produk</td>
                    <td><?= esc($header['type_produk'] ?? '-' ?: '-') ?></td>
                </tr>

                <?php
                // Detect if record is Becum, Powerjet1, or Powerjet2
                $detailMesinNama  = strtolower(str_replace([' ', '-', '_'], '', $header['nama_mesin'] ?? ''));
                $detailIsBecum    = in_array($detailMesinNama, ['becum', 'powerjet1', 'powerjet2']);
                $detailIsBecumOnly  = ($detailMesinNama === 'becum');
                $detailIsPowerjet   = in_array($detailMesinNama, ['powerjet1', 'powerjet2']);
                $detailHidePackaging = $detailIsBecumOnly || $detailIsPowerjet;
                ?>

                <?php if ($detailIsBecum): ?>
                    <tr>
                        <td class="text-muted">Brand</td>
                        <td class="fw-bold"><?= esc($header['brand'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Warna</td>
                        <td class="fw-bold"><?= esc($header['warna'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Grade</td>
                        <td class="fw-bold"><?= esc($header['grade'] ?: '-') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Packing</td>
                        <td class="fw-bold"><?= esc($header['packing'] ?: '-') ?></td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td class="text-muted">Tanggal</td>
                    <td><?= date('d M Y', strtotime($header['tanggal'])) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Shift / Grup</td>
                    <td><?php
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
                        ?> / <?= esc($header['grup'] ?: '-') ?></td>
                </tr>
                <?php
                $extraShiftsDetail = [];
                if (!empty($header['extra_realisasi_shifts'])) {
                    $decoded = json_decode($header['extra_realisasi_shifts'], true);
                    if (is_array($decoded)) {
                        $extraShiftsDetail = array_filter($decoded, fn($e) => !empty($e['label']));
                    }
                }
                ?>
                <?php if (!empty($extraShiftsDetail)): ?>
                    <tr>
                        <td class="text-muted" style="vertical-align:top;">Shift Lainnya</td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <?php foreach ($extraShiftsDetail as $es): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge" style="background:#e0f2fe; color:#0369a1; font-size:0.72rem; font-weight:600;">
                                            <i class="fas fa-clock me-1"></i><?= esc($es['label']) ?>
                                        </span>
                                        <?php if (!empty($es['realisasi'])): ?>
                                            <span class="text-success fw-bold" style="font-size:0.8rem;">
                                                <?= number_format((int)$es['realisasi']) ?> pcs
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted">Operator</td>
                    <td><?= esc($header['operator']) ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-chart-line me-2 text-success"></i> Kinerja Produksi</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="150" class="text-muted">Total SPK</td>
                    <td><?= number_format($header['total_spk'] ?? 0) ?> pcs</td>
                </tr>
                <tr>
                    <td class="text-muted">Target</td>
                    <td><?= number_format($header['target']) ?> pcs</td>
                </tr>
                <tr>
                    <td class="text-muted">Isi</td>
                    <td><?= number_format($header['isi'] ?: 0) ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Hasil Bagus</td>
                    <td class="text-success fw-bold"><?= number_format($header['grand_total_bagus']) ?> pcs</td>
                </tr>
                <tr>
                    <td class="text-muted">Total Reject</td>
                    <td class="text-danger fw-bold"><?= number_format($header['grand_total_reject']) ?> pcs</td>
                </tr>
                <tr style="border-top: 2px solid #dee2e6;">
                    <td class="text-muted fw-bold">Total Produksi</td>
                    <td class="fw-bold text-primary"><?= number_format((int)($header['grand_total_bagus'] ?? 0) + (int)($header['grand_total_reject'] ?? 0)) ?> pcs</td>
                </tr>
                <?php if ($detailIsBecum): ?>
                    <?php
                    $cAwal  = (int)($header['counter_awal']  ?? 0);
                    $cAkhir = (int)($header['counter_akhir'] ?? 0);
                    $cDiff  = abs($cAkhir - $cAwal);
                    if ($detailIsPowerjet && ($cAwal > 0 || $cAkhir > 0)) {
                        $displayTotalCounter = $cDiff * 12;
                    } else {
                        $displayTotalCounter = (int)($header['total_counter'] ?? 0);
                        if ($displayTotalCounter == 0 && ($cAwal > 0 || $cAkhir > 0)) {
                            $displayTotalCounter = $cDiff;
                        }
                    }
                    ?>
                    <tr>
                        <td class="text-muted"><?= $detailIsPowerjet ? '(Akhir-Awal)×12' : 'Total Counter' ?></td>
                        <td class="fw-bold text-success"><?= number_format($displayTotalCounter) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted">Cycle Time</td>
                    <td><?= esc($header['cycle_time'] ?: '-') ?> s</td>
                </tr>
                <tr>
                    <td class="text-muted">Total Downtime</td>
                    <td class="text-danger fw-bold"><?= number_format($totalDuration) ?> min</td>
                </tr>
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

        <?php if ($detailIsBecum): ?>
            <div class="col-md-6">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-flask me-2 text-primary"></i> Status Spesifik (Becum, Powerjet)</h6>
                <div class="d-flex gap-3">
                    <div class="flex-fill text-center p-2 bg-light rounded">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Plash Kotor</small>
                        <div class="fw-bold text-primary" style="font-size: 0.95rem;"><?= esc($header['plash_kotor'] ?: '-') ?></div>
                    </div>
                    <div class="flex-fill text-center p-2 bg-light rounded">
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Sapuan</small>
                        <div class="fw-bold text-success" style="font-size: 0.95rem;"><?= esc($header['sapuan'] ?: '-') ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($detailIsBecum && (!empty($header['counter_awal']) || !empty($header['counter_akhir']) || !empty($header['total_counter']))): ?>
            <div class="col-md-12">
                <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-tachometer-alt me-2 text-success"></i> Counter Mesin</h6>
                <table class="table table-sm table-bordered align-middle mb-2">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-dark fw-bold">Counter Awal</th>
                            <th class="text-center text-dark fw-bold">Counter Akhir</th>
                            <th class="text-center text-dark fw-bold"><?= $detailIsPowerjet ? '(Akhir-Awal)×12' : 'Total Counter' ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" style="font-size: 0.95rem;"><?= number_format((int)($header['counter_awal'] ?? 0)) ?></td>
                            <td class="text-center" style="font-size: 0.95rem;"><?= number_format((int)($header['counter_akhir'] ?? 0)) ?></td>
                            <?php
                            $cAwal2  = (int)($header['counter_awal']  ?? 0);
                            $cAkhir2 = (int)($header['counter_akhir'] ?? 0);
                            $cDiff2  = abs($cAkhir2 - $cAwal2);
                            if ($detailIsPowerjet && ($cAwal2 > 0 || $cAkhir2 > 0)) {
                                $displayTotalCounter = $cDiff2 * 12;
                            } else {
                                $displayTotalCounter = (int)($header['total_counter'] ?? 0);
                                if ($displayTotalCounter == 0 && ($cAwal2 > 0 || $cAkhir2 > 0)) {
                                    $displayTotalCounter = $cDiff2;
                                }
                            }
                            ?>
                            <td class="text-center fw-bold text-success" style="font-size: 0.95rem;"><?= number_format($displayTotalCounter) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

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
                        <tr>
                            <td colspan="2" class="text-center text-muted">Tidak ada data per jam</td>
                        </tr>
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

        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-3"><i class="fas fa-exclamation-triangle me-2 text-danger"></i> Rincian Reject</h6>
            <table class="table table-sm table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>Jenis Reject</th>
                        <th class="text-center">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rejects)): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">Tidak ada reject</td>
                        </tr>
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

        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-3"><i class="fas fa-stopwatch me-2 text-danger"></i> Rincian Downtime</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-2">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-dark fw-bold" style="width:20%;">Waktu Mulai – Selesai</th>
                            <th class="text-dark fw-bold" style="width:20%;">Problem</th>
                            <th class="text-dark fw-bold" style="width:20%;">Action Plant</th>
                            <th class="text-dark fw-bold" style="width:15%;">Jenis Downtime</th>
                            <th class="text-center text-dark fw-bold" style="width:10%;">Durasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($downtimes)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data downtime</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            foreach ($downtimes as $dt):
                                $start    = !empty($dt['waktu_mulai'])   ? strtotime($dt['waktu_mulai'])   : null;
                                $end      = !empty($dt['waktu_selesai']) ? strtotime($dt['waktu_selesai']) : null;
                                $isActive = ($end === null);
                                $duration = (int)($dt['durasi_menit'] ?? 0);
                            ?>
                                <tr>
                                    <td class="text-center"><small><?= $start ? date('H:i', $start) : '-' ?> – <?= $end ? date('H:i', $end) : '<span class="text-danger fw-bold">Aktif</span>' ?></small></td>
                                    <td><small><?= esc($dt['problem'] ?? '-') ?></small></td>
                                    <td><small><?= esc($dt['action_plant'] ?? '-') ?></small></td>
                                    <td><small><?= esc($dt['alasan_downtime']) ?></small></td>
                                    <td class="text-center fw-bold <?= $isActive ? 'text-warning' : 'text-danger' ?>">
                                        <small><?= $isActive ? '— Aktif' : $duration . ' mnt' ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end text-uppercase text-dark" style="font-size: 0.85rem;">Total Downtime</td>
                            <td class="text-center text-danger" style="font-size: 0.85rem;"><?= $totalDuration ?> mnt</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php
    // Tampilkan Durasi Penimbangan hanya untuk Becum, Powerjet1, Powerjet2
    $detailMesinSlug = strtolower(str_replace([' ', '-', '_'], '', $header['nama_mesin'] ?? ''));
    $showPenimbanganDetail = in_array($detailMesinSlug, ['becum', 'powerjet1', 'powerjet2']);

    // Cek apakah ada data yang non-zero
    $hasPenimbanganData = false;
    if ($showPenimbanganDetail && !empty($penimbanganData)) {
        foreach ($penimbanganData as $row) {
            if (($row['galon'] ?? 0) != 0 || ($row['flash'] ?? 0) != 0) {
                $hasPenimbanganData = true;
                break;
            }
        }
    }
    ?>
    <?php if ($showPenimbanganDetail): ?>
        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-balance-scale me-2 text-primary"></i> Durasi Penimbangan</h6>
            <?php if (!$hasPenimbanganData): ?>
                <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i> Tidak ada data penimbangan untuk laporan ini.</p>
            <?php else: ?>
                <?php
                $totalGalon = 0;
                $totalFlash = 0;
                foreach ($penimbanganData as $row) {
                    $totalGalon += $row['galon'] ?? 0;
                    $totalFlash += $row['flash'] ?? 0;
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-2">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width:80px;">Jam</th>
                                <th class="text-center"><?= in_array($detailMesinSlug, ['powerjet1', 'powerjet2']) ? 'Cap Galon (kg)' : 'Galon (kg)' ?></th>
                                <th class="text-center">Flash (kg)</th>
                                <th class="text-center">Subtotal (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $maxHourDetail = in_array($detailMesinSlug, ['powerjet1', 'powerjet2']) ? 12 : 8;
                            for ($h = 1; $h <= $maxHourDetail; $h++):
                                $galon = (float)($penimbanganData[$h]['galon'] ?? 0);
                                $flash = (float)($penimbanganData[$h]['flash'] ?? 0);
                                $subtotal = $galon + $flash;
                                if ($galon == 0 && $flash == 0) continue; // skip baris kosong
                            ?>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-primary" style="font-size:.8rem;">Jam <?= $h ?></span>
                                    </td>
                                    <td class="text-center fw-bold text-primary"><?= number_format($galon, 2) ?></td>
                                    <td class="text-center fw-bold text-danger"><?= number_format($flash, 2) ?></td>
                                    <td class="text-center fw-bold text-success"><?= number_format($subtotal, 2) ?></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td class="text-end text-uppercase small text-muted">Total</td>
                                <td class="text-center text-primary"><?= number_format($totalGalon, 2) ?> kg</td>
                                <td class="text-center text-danger"><?= number_format($totalFlash, 2) ?> kg</td>
                                <td class="text-center text-success"><?= number_format($totalGalon + $totalFlash, 2) ?> kg</td>
                            </tr>
                            <tr style="background:#f0f7ff;">
                                <?php
                                // Rata-rata dibagi jumlah kolom yang terisi (bukan $maxHourDetail)
                                $filledGalonD = 0;
                                $filledFlashD = 0;
                                for ($h = 1; $h <= $maxHourDetail; $h++) {
                                    if ((float)($penimbanganData[$h]['galon'] ?? 0) > 0) $filledGalonD++;
                                    if ((float)($penimbanganData[$h]['flash'] ?? 0) > 0) $filledFlashD++;
                                }
                                $rataGalonD = $filledGalonD > 0 ? $totalGalon / $filledGalonD : 0;
                                $rataFlashD = $filledFlashD > 0 ? $totalFlash / $filledFlashD : 0;
                                ?>
                                <td class="text-end text-uppercase small" style="color:#0369a1;">Rata²</td>
                                <td class="text-center" style="color:#0369a1;"><?= number_format($rataGalonD, 2) ?> kg</td>
                                <td class="text-center" style="color:#9f1239;"><?= number_format($rataFlashD, 2) ?> kg</td>
                                <td class="text-center text-muted">—</td>
                            </tr>
                            <?php
                            $rataFlash     = $rataFlashD;
                            $totalProduksi = (int)($header['grand_total_bagus'] ?? 0) + (int)($header['grand_total_reject'] ?? 0);
                            $hasilFlashKg  = ($totalProduksi > 0) ? ($rataFlash * $totalProduksi / 1000) : 0;
                            $totalTonase   = ($rataFlash * $totalGalon) / 1000;
                            ?>
                            <tr style="background:#fff7ed; border-top:2px solid #fed7aa;">
                                <td class="text-end text-uppercase small" style="color:#c2410c; font-size:0.72rem; white-space:nowrap;">
                                    <i class="fas fa-calculator me-1"></i>Total Tonase
                                </td>
                                <td class="text-center text-muted small">—</td>
                                <td class="text-center text-muted small">—</td>
                                <td class="text-center fw-bold" style="color:#c2410c; font-size:.9rem;">
                                    <?= number_format($hasilFlashKg, 2) ?> kg
                                </td>
                            </tr>
                            <?php if ($detailIsBecumOnly): ?>
                                <tr style="background:#f0fdf4; border-top:2px solid #86efac;">
                                    <!-- <td class="text-end text-uppercase small fw-bold" style="color:#15803d; font-size:0.72rem; white-space:nowrap;">
                                    <i class="fas fa-weight-hanging me-1"></i>Total Tonase
                                </td>
                                <td class="text-center text-muted small">—</td>
                                <td class="text-center text-muted small">—</td> -->
                                    <!-- <td class="text-center fw-bold" style="color:#15803d; font-size:.95rem;">
                                    <?= number_format($totalTonase, 2) ?> kg
                                </td> -->
                                </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (in_array($detailMesinSlug, ['powerjet1', 'powerjet2']) && !empty($kodeCapData)): ?>
        <div class="col-md-12">
            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-2">
                <i class="fas fa-th text-warning me-2"></i> Kode Cap
            </h6>
            <?php
            $hasAnyCap = false;
            foreach ($kodeCapData as $row) {
                for ($c = 1; $c <= 12; $c++) {
                    if (!empty($row["kode_cap_{$c}"])) {
                        $hasAnyCap = true;
                        break 2;
                    }
                }
            }
            ?>
            <?php if (!$hasAnyCap): ?>
                <p class="text-muted small"><i class="fas fa-info-circle me-1"></i> Tidak ada data kode cap.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle mb-0" style="min-width:800px; font-size:.76rem;">
                        <thead>
                            <tr class="text-center">
                                <th class="bg-secondary text-white" style="width:55px;">Jam ke-</th>
                                <?php for ($c = 1; $c <= 12; $c++): ?>
                                    <th class="bg-primary text-white" style="min-width:50px;"><?= $c ?></th>
                                <?php endfor; ?>
                                <th class="bg-success text-white" style="width:65px;">Runner</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Loop semua jam dari DB, sorted ascending
                            $detailJamKeys = array_keys($kodeCapData);
                            sort($detailJamKeys);
                            foreach ($detailJamKeys as $jam):
                                $row = $kodeCapData[$jam] ?? [];
                                $rowEmpty = true;
                                for ($c = 1; $c <= 12; $c++) {
                                    if (!empty($row["kode_cap_{$c}"])) {
                                        $rowEmpty = false;
                                        break;
                                    }
                                }
                                if ($rowEmpty) continue;
                            ?>
                                <tr>
                                    <td class="text-center fw-bold bg-light"><?= $jam ?></td>
                                    <?php for ($c = 1; $c <= 12; $c++): ?>
                                        <td class="text-center" style="color:<?= !empty($row["kode_cap_{$c}"]) ? '#1d4ed8' : '#9ca3af' ?>;">
                                            <?= !empty($row["kode_cap_{$c}"]) ? esc($row["kode_cap_{$c}"]) : '—' ?>
                                        </td>
                                    <?php endfor; ?>
                                    <td class="text-center fw-bold" style="color:#15803d; background:#f0fdf4;">
                                        <?= number_format((float)($row['runner'] ?? 0), 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="text-center" style="background:#fefce8; border-top:2px solid #fde047;">
                                <td class="fw-bold" style="font-size:.72rem; color:#78350f; background:#fef9c3;">
                                    Rata²
                                </td>
                                <?php
                                $rataKolom = [];
                                for ($c = 1; $c <= 12; $c++):
                                    $sum    = 0;
                                    $filled = 0;
                                    foreach ($detailJamKeys as $jam) {
                                        $val = $kodeCapData[$jam]["kode_cap_{$c}"] ?? '';
                                        if ($val !== '' && $val !== null && is_numeric($val)) {
                                            $sum += (float)$val;
                                            $filled++;
                                        }
                                    }
                                    $rata = $filled > 0 ? $sum / $filled : 0;
                                    $rataKolom[$c] = $rata;
                                ?>
                                    <td class="text-center fw-bold" style="font-size:.76rem; color:<?= $rata > 0 ? '#92400e' : '#9ca3af' ?>; background:#fefce8;">
                                        <?= number_format($rata, 2) ?>
                                    </td>
                                <?php endfor; ?>
                                <?php
                                $totalRunner  = 0;
                                $filledRunner = 0;
                                foreach ($detailJamKeys as $jam) {
                                    $rVal = (float)($kodeCapData[$jam]['runner'] ?? 0);
                                    $totalRunner += $rVal;
                                    if ($rVal > 0) $filledRunner++;
                                }
                                $rataRunner = $filledRunner > 0 ? $totalRunner / count($detailJamKeys) : 0;
                                ?>
                                <td class="text-center fw-bold" style="font-size:.76rem; color:#15803d; background:#f0fdf4;">
                                    <?= number_format($rataRunner, 2) ?>
                                </td>
                            </tr>
                            <tr style="background:#fff7ed; border-top:2px solid #fb923c;">
                                <td class="fw-bold text-center" style="font-size:.72rem; color:#7c2d12; background:#ffedd5; white-space:nowrap;">
                                    Akhir :
                                </td>
                                <td colspan="12" class="text-left fw-bold"
                                    style="font-size:.85rem; color:#c2410c; background:#fff7ed;">
                                    <span style="background:#ffedd5; border:1.5px solid #fb923c;
                                             border-radius:6px; padding:.15rem .6rem; display:inline-block;">
                                        <?php
                                        $nonZero = array_filter($rataKolom, fn($v) => $v > 0);
                                        echo number_format(count($nonZero) > 0 ? array_sum($nonZero) / count($nonZero) : 0, 2);
                                        ?>
                                    </span>
                                </td>
                                <td style="background:#fff7ed;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="col-md-12">
        <h6 class="fw-bold border-bottom pb-2 mb-3 mt-3"><i class="fas fa-boxes me-2 text-warning"></i> Material & Packaging</h6>
        <div class="p-3 bg-light rounded">
            <p class="mb-2 small fw-bold text-dark">Material:</p>
            <?php if (!empty($materials)): $m = $materials[0]; ?>

                <!-- Row 1: Code + Pemakaian -->
                <div class="row g-0 mb-1">
                    <div class="col-6" style="padding:0.45rem 0.75rem; background:#f0f7ff; border:1px solid #d0e4f7; border-right:none;">
                        <span class="text-muted d-block" style="font-size:0.68rem; text-transform:uppercase; letter-spacing:0.4px;">Code</span>
                        <strong style="font-size:0.8rem;"><?= esc($m['merek_kode'] ?: '-') ?></strong>
                    </div>
                    <div class="col-6" style="padding:0.45rem 0.75rem; background:#f0f7ff; border:1px solid #d0e4f7;">
                        <span class="text-muted d-block" style="font-size:0.68rem; text-transform:uppercase; letter-spacing:0.4px;">Pemakaian</span>
                        <strong style="font-size:0.8rem;"><?= esc($m['pemakaian'] ?: '0.00') ?> kg</strong>
                    </div>
                </div>

                <!-- Row 2: Lot A B C D — 4 col on desktop, list down on mobile -->
                <div class="row g-0" style="border:1px solid #c3e6cb; background:#f6fdf8;">
                    <?php foreach (['A' => $m['lot_a'] ?? '', 'B' => $m['lot_b'] ?? '', 'C' => $m['lot_c'] ?? '', 'D' => $m['lot_d'] ?? ''] as $lbl => $val): ?>
                        <div class="col-12 col-md-3" style="padding:0.45rem 0.75rem; border-bottom:1px solid #c3e6cb; border-right:1px solid #c3e6cb;">
                            <span class="text-muted d-block" style="font-size:0.68rem; text-transform:uppercase; letter-spacing:0.4px;">Lot <?= $lbl ?></span>
                            <strong style="font-size:0.8rem;"><?= esc($val ?: '-') ?></strong>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <p class="text-muted small">Tidak ada data material.</p>
            <?php endif; ?>

            <!-- Komposisi Detail -->
            <?php if (!empty($materials) && ($materials[0]['komposisi_nama_1'] || $materials[0]['komposisi_persen_1'])): ?>
                <hr class="my-2">
                <p class="mb-2 small"><strong class="text-dark">Komposisi:</strong></p>
                <?php
                $isDetailBecum    = strtolower(trim($header['nama_mesin'] ?? '')) === 'becum';
                $isDetailPowerjet = in_array(strtolower(str_replace([' ', '-', '_'], '', $header['nama_mesin'] ?? '')), ['powerjet1', 'powerjet2']);
                $isDetailCcm      = strpos(strtolower($header['nama_mesin'] ?? ''), 'ccm') !== false;
                $isDetailIps      = strpos(strtolower($header['nama_mesin'] ?? ''), 'ips') !== false;
                $showKgCol        = $isDetailBecum || $isDetailPowerjet;
                $komposisiList = [];
                $totalPersen   = 0;
                $totalKgDetail = 0;
                $hasKgData     = false;
                for ($i = 1; $i <= 4; $i++) {
                    $nama   = $materials[0]["komposisi_nama_$i"]   ?? '';
                    $persen = $materials[0]["komposisi_persen_$i"] ?? 0;
                    $kg     = $materials[0]["komposisi_kg_$i"]     ?? null;
                    if (!empty($nama) || $persen > 0) {
                        $komposisiList[] = ['nama' => $nama, 'persen' => $persen, 'kg' => $kg];
                        $totalPersen    += (float)$persen;
                        if ($kg !== null && $kg !== '') {
                            $totalKgDetail += (float)$kg;
                            $hasKgData      = true;
                        }
                    }
                }
                ?>
                <table class="table table-sm mb-0" style="font-size: 0.7rem; border-collapse:collapse;">
                    <thead>
                        <tr style="background: #e0f2fe;">
                            <th class="ps-3 py-2 text-muted fw-bold">Nama</th>
                            <th class="text-end pe-3 py-2 text-muted fw-bold">Persen</th>
                            <?php if ($showKgCol && $hasKgData): ?>
                                <th class="text-end pe-3 py-2 text-muted fw-bold">KG</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($komposisiList as $idx => $comp):
                            $rowBg = ($idx % 2 === 0)
                                ? '#dbeafe'   /* even – light blue */
                                : '#dcfce7';  /* odd  – light green */
                        ?>
                            <tr style="background:<?= $rowBg ?>;">
                                <td class="ps-3 py-2"><strong><?= esc($comp['nama']) ?></strong></td>
                                <td class="text-end pe-3 py-2"><strong><?= number_format((float)$comp['persen'], 2) ?> %</strong></td>
                                <?php if ($showKgCol && $hasKgData): ?>
                                    <td class="text-end pe-3 py-2">
                                        <?= ($comp['kg'] !== null && $comp['kg'] !== '') ? number_format((float)$comp['kg'], 3) . ' kg' : '-' ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f1f5f9; border-top: 2px solid #cbd5e1;">
                            <td class="ps-3 py-2"><strong class="text-primary">Total:</strong></td>
                            <td class="text-end pe-3 py-2">
                                <span class="badge <?= $totalPersen == 100 ? 'bg-success' : ($totalPersen < 100 ? 'bg-warning' : 'bg-danger') ?>" style="font-size: 0.65rem;">
                                    <strong><?= number_format($totalPersen, 2) ?> %</strong>
                                </span>
                            </td>
                            <?php if ($showKgCol && $hasKgData): ?>
                                <td class="text-end pe-3 py-2">
                                    <span class="badge bg-secondary" style="font-size: 0.65rem;">
                                        <strong><?= number_format($totalKgDetail, 3) ?> kg</strong>
                                    </span>
                                </td>
                            <?php endif; ?>
                        </tr>
                    </tfoot>
                </table>

                <!-- Total Material (untuk CCM & IPS) -->
                <?php if ($isDetailCcm || $isDetailIps): ?>
                    <?php
                    // Berat fixed per mesin (gram)
                    $beratPerMesin = [
                        'ccm 1'  => 1.29,
                        'ccm 2'  => 1.29,
                        'ips 1'  => 12.5,
                        'ips 2'  => 12.5,
                        'ips 3'  => 11.05,
                        'ips 4'  => 11.05,
                    ];
                    // Normalize: lowercase, collapse multiple spaces
                    $mesinKey  = preg_replace('/\s+/', ' ', strtolower(trim($header['nama_mesin'] ?? '')));
                    $beratGram = $beratPerMesin[$mesinKey] ?? 0;

                    $bagus  = (float)($header['grand_total_bagus']  ?? 0);
                    $reject = (float)($header['grand_total_reject'] ?? 0);

                    // Sum hold dari jams
                    $totalHold = 0;
                    if (!empty($jams)) {
                        foreach ($jams as $jam) {
                            $totalHold += (float)($jam['hold'] ?? 0);
                        }
                    }

                    // Gumpalan dari header (satuan kg)
                    $gumpalan = (float)($header['gumpalan'] ?? 0);

                    // Rumus: (Bagus + Reject + Hold) × Berat ÷ 1000 + Gumpalan(kg)
                    $totalMaterial = (($bagus + $reject + $totalHold) * $beratGram / 1000) + $gumpalan;
                    ?>
                    <div class="p-2 border rounded mt-2" style="background: rgba(25, 135, 84, 0.08); border-color: #198754 !important;">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted fw-bold" style="font-size: 0.8rem;">
                                <i class="fas fa-weight me-1"></i>Total Material:
                            </span>
                            <span class="badge bg-success" style="font-size: 0.8rem; padding: 0.4rem 0.7rem; font-family: 'Courier New', monospace;">
                                <strong><?= number_format($totalMaterial, 3, ',', '.') ?> kg</strong>
                            </span>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">
                            <i class="fas fa-calculator me-1"></i>
                            (<?= number_format($bagus, 0, ',', '.') ?> + <?= number_format($reject, 0, ',', '.') ?> + <?= number_format($totalHold, 0, ',', '.') ?>) × <?= number_format($beratGram, 2, ',', '.') ?> gr ÷ 1000 + <?= number_format($gumpalan, 2, ',', '.') ?> kg
                        </small>
                        <?php if ($beratGram === 0.0): ?>
                            <small class="text-warning d-block mt-1" style="font-size: 0.65rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i>Berat tidak ditemukan untuk mesin "<?= esc($header['nama_mesin']) ?>"
                            </small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <hr class="my-2">

            <p class="mb-1 small"><strong class="text-dark">Colorant:</strong></p>
            <p class="mb-1 small">
                <strong>Code:</strong> <?= !empty($colorants) ? esc($colorants[0]['code']) : '-' ?><br>
                <strong>Pemakaian:</strong> <?= !empty($colorants) ? $colorants[0]['pemakaian'] . ' kg' : '-' ?>
            </p>
            <p class="mb-1 small"><strong>Lot:</strong> <?= !empty($colorants) ? esc($colorants[0]['nomor_lot']) : '-' ?></p>
            <?php
            $detailHasTbDetails = !empty($colorants[0]['tb_in_details']) || !empty($colorants[0]['tb_out_details']);
            if ($detailHasTbDetails):
                // Becum: tampilkan TB details sebagai tabel
                $tbInItems  = json_decode($colorants[0]['tb_in_details']  ?? '[]', true) ?: [];
                $tbOutItems = json_decode($colorants[0]['tb_out_details'] ?? '[]', true) ?: [];
                $tbInItems  = array_filter($tbInItems,  fn($x) => !empty($x['nama']));
                $tbOutItems = array_filter($tbOutItems, fn($x) => !empty($x['nama']));
            ?>
                <div class="row g-3">
                    <!-- TB IN Section -->
                    <?php if (!empty($tbInItems) || !empty($colorants[0]['tb_in']) || !empty($colorants[0]['tb_in_runner'])): ?>
                        <div class="col-md-6">
                            <div class="p-2 border rounded" style="background: #f0f7ff; border-color: #cce5ff !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary me-2" style="font-size: 0.75rem; padding: 0.4rem 0.7rem;">TB IN</span>
                                    <span class="text-primary fw-500" style="font-size: 0.8rem; letter-spacing: 0.3px;">Bahan Baku Masuk</span>
                                </div>
                                <?php if (!empty($tbInItems)): ?>
                                    <table class="table table-sm table-bordered mb-2" style="font-size: 0.75rem;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Nama</th>
                                                <th class="text-center">%</th>
                                                <th>Lot</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tbInItems as $item): ?>
                                                <tr>
                                                    <td><?= esc($item['nama']) ?></td>
                                                    <td class="text-center"><?= esc($item['persen']) ?></td>
                                                    <td><?= esc($item['lot']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                                <?php if (!empty($colorants[0]['tb_in'])): ?>
                                    <div class="p-1 rounded mb-1" style="background: #e0f1ff; font-size: 0.7rem;">
                                        <strong>TB IN:</strong> <?= esc($colorants[0]['tb_in']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($colorants[0]['tb_in_runner']) && !$detailIsBecumOnly): ?>
                                    <div class="p-1 rounded" style="background: #eef6ff; font-size: 0.7rem;">
                                        <strong>TB IN RUNNER:</strong> <span class="text-primary fw-bold"><?= esc($colorants[0]['tb_in_runner']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- TB OUT Section -->
                    <?php if (!empty($tbOutItems) || !empty($colorants[0]['tb_out']) || !empty($colorants[0]['tb_out_runner'])): ?>
                        <div class="col-md-6">
                            <div class="p-2 border rounded" style="background: #fff3f3; border-color: #ffc9d0 !important;">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-danger me-2" style="font-size: 0.75rem; padding: 0.4rem 0.7rem;">TB OUT BAHAN</span>
                                    <span class="text-danger fw-500" style="font-size: 0.8rem; letter-spacing: 0.3px;">Bahan Baku Keluar</span>
                                </div>
                                <?php if (!empty($tbOutItems)): ?>
                                    <table class="table table-sm table-bordered mb-2" style="font-size: 0.75rem;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Nama</th>
                                                <th class="text-center">%</th>
                                                <th>Lot</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tbOutItems as $item): ?>
                                                <tr>
                                                    <td><?= esc($item['nama']) ?></td>
                                                    <td class="text-center"><?= esc($item['persen']) ?></td>
                                                    <td><?= esc($item['lot']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                                <?php if (!empty($colorants[0]['tb_out'])): ?>
                                    <div class="p-1 rounded mb-1" style="background: #ffe7e7; font-size: 0.7rem;">
                                        <strong>TB OUT BAHAN:</strong> <?= esc($colorants[0]['tb_out']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($colorants[0]['tb_out_runner']) && !$detailIsBecumOnly): ?>
                                    <div class="p-1 rounded" style="background: #fff3e0; font-size: 0.7rem;">
                                        <strong>TB OUT RUNNER:</strong> <span class="text-warning fw-bold"><?= esc($colorants[0]['tb_out_runner']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif (!empty($colorants[0]['tb_in']) || !empty($colorants[0]['tb_out']) || !empty($colorants[0]['tb_out_runner']) || !empty($colorants[0]['tb_in_runner'])): ?>
                <div class="p-2 border rounded" style="background: #f9f9f9; border-color: #dee2e6 !important;">
                    <div class="row g-2">
                        <?php if (!empty($colorants[0]['tb_in']) || !empty($colorants[0]['tb_in_runner'])): ?>
                            <div class="col-md-6">
                                <small class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                    <i class="fas fa-arrow-circle-down text-primary me-1"></i>TB IN
                                </small>
                                <div class="p-1 rounded" style="background: #f0f7ff; font-size: 0.7rem;">
                                    <strong>Bahan:</strong> <?= esc($colorants[0]['tb_in'] ?: '-') ?>
                                    <?php if (!empty($colorants[0]['tb_in_runner'])): ?>
                                        <br><strong>Runner:</strong> <?= esc($colorants[0]['tb_in_runner']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($colorants[0]['tb_out']) || !empty($colorants[0]['tb_out_runner'])): ?>
                            <div class="col-md-6">
                                <small class="d-block text-muted fw-bold mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.3px;">
                                    <i class="fas fa-arrow-circle-up text-danger me-1"></i>TB OUT
                                </small>
                                <div class="p-1 rounded" style="background: #fff3f3; font-size: 0.7rem;">
                                    <strong>Bahan:</strong> <?= esc($colorants[0]['tb_out'] ?: '-') ?>
                                    <?php if (!empty($colorants[0]['tb_out_runner'])): ?>
                                        <br><strong>Runner:</strong> <?= esc($colorants[0]['tb_out_runner']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php
            // TB lain IN details — always evaluated, independent of TB IN/OUT
            $tbLainInItems = [];
            if (!empty($colorants[0]['tb_lain_in_details'])) {
                $decoded = json_decode($colorants[0]['tb_lain_in_details'], true) ?: [];
                $tbLainInItems = array_filter($decoded, fn($x) => !empty($x['nama']));
            }
            ?>
            <?php if (!empty($tbLainInItems) || !empty($colorants[0]['tb_lain_in'])): ?>
                <div class="mt-3 p-2 border rounded" style="background: #f0fff8; border-color: #b2f0d8 !important;">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge me-2" style="font-size: 0.7rem; padding: 0.3rem 0.5rem; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">TB lain IN</span>
                        <span class="fw-500" style="font-size: 0.75rem; letter-spacing: 0.3px; color: #1a7a4a;">Bahan Lain Masuk</span>
                    </div>
                    <?php if (!empty($tbLainInItems)): ?>
                        <table class="table table-sm table-bordered mb-0" style="font-size: 0.7rem;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nama</th>
                                    <th class="text-center">%</th>
                                    <th>Lot</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tbLainInItems as $item): ?>
                                    <tr>
                                        <td><?= esc($item['nama']) ?></td>
                                        <td class="text-center"><?= esc($item['persen']) ?></td>
                                        <td><?= esc($item['lot']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <?php if (!empty($colorants[0]['tb_lain_in'])): ?>
                        <div class="mt-2 p-1 rounded" style="background: #e6fff4; font-size: 0.7rem;">
                            <strong>Summary:</strong> <?= esc($colorants[0]['tb_lain_in']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="mt-2 pt-2 border-top">
                <p class="mb-2 small"><strong class="text-dark">Packaging :</strong></p>
                <?php if ($packaging): ?>
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-dark fw-bold" style="width:<?= $detailHidePackaging ? '30%' : '25%' ?>;">Item</th>
                                <?php if (!$detailHidePackaging): ?>
                                    <th class="text-center text-dark fw-bold" style="width:15%;">Jumlah</th>
                                <?php endif; ?>
                                <th class="text-dark fw-bold" style="width:<?= $detailHidePackaging ? '25%' : '20%' ?>;">Ukuran</th>
                                <th class="text-dark fw-bold" style="width:<?= $detailHidePackaging ? '45%' : '40%' ?>;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Plastik/Box/Karung/Nicktainer (hidden for Becum & Powerjet) -->
                            <?php if (!$detailHidePackaging): ?>
                                <tr>
                                    <td><i class="fas fa-vial text-info me-1"></i> Plastik</td>
                                    <td class="text-center">-</td>
                                    <td><small><?= esc($packaging['plastik_ukuran'] ?: '-') ?></small></td>
                                    <td>
                                        <small>
                                            Bagus: <strong class="text-success"><?= $packaging['plastik_bagus'] ?? '-' ?> kg</strong>
                                            &nbsp;|&nbsp;
                                            Reject: <strong class="text-danger"><?= $packaging['plastik_reject'] ?? '-' ?> kg</strong>
                                        </small>
                                    </td>
                                </tr>
                                <!-- Box (hidden for Becum) -->
                                <tr>
                                    <td><i class="fas fa-box text-warning me-1"></i> Box</td>
                                    <td class="text-center fw-bold"><?= $packaging['box'] ?? '-' ?> pcs</td>
                                    <td><small><?= esc($packaging['box_ukuran'] ?: '-') ?></small></td>
                                    <td>-</td>
                                </tr>
                                <!-- Karung (hidden for Becum) -->
                                <tr>
                                    <td><i class="fas fa-archive text-secondary me-1"></i> Karung</td>
                                    <td class="text-center fw-bold"><?= $packaging['karung'] ?? '-' ?> pcs</td>
                                    <td><small><?= esc($packaging['karung_ukuran'] ?: '-') ?></small></td>
                                    <td>-</td>
                                </tr>
                                <?php if (!empty($packaging['nicktainer'])): ?>
                                    <!-- Nicktainer (hidden for Becum) -->
                                    <tr>
                                        <td><i class="fas fa-drum text-primary me-1"></i> Nicktainer</td>
                                        <td class="text-center fw-bold"><?= $packaging['nicktainer'] ?> pcs</td>
                                        <td><small><?= esc($packaging['nicktainer_ukuran'] ?: '-') ?></small></td>
                                        <td>-</td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- Plastik Besar (Becum only) - ukuran + awal/akhir/pemakaian dalam 1 baris -->
                            <?php if ($detailIsBecumOnly && (!empty($packaging['ukuran_plastik_besar']) || !empty($packaging['plastik_besar_awal']) || !empty($packaging['plastik_besar_akhir']))): ?>
                                <tr>
                                    <td><i class="fas fa-expand-alt text-primary me-1"></i> Plastik Besar</td>
                                    <td><small><?= esc($packaging['ukuran_plastik_besar'] ?: '-') ?></small></td>
                                    <td>
                                        <small>
                                            Awal: <strong><?= number_format((float)($packaging['plastik_besar_awal'] ?? 0), 2) ?></strong>
                                            &rarr;
                                            Akhir: <strong><?= number_format((float)($packaging['plastik_besar_akhir'] ?? 0), 2) ?></strong>
                                            &nbsp;=&nbsp;
                                            Pakai: <strong class="text-primary"><?= number_format((float)($packaging['plastik_besar_pemakaian'] ?? 0), 2) ?></strong>
                                        </small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- Plastik Kecil (Becum only) - ukuran + awal/akhir/pemakaian dalam 1 baris -->
                            <?php if ($detailIsBecumOnly && (!empty($packaging['ukuran_plastik_kecil']) || !empty($packaging['plastik_kecil_awal']) || !empty($packaging['plastik_kecil_akhir']))): ?>
                                <tr>
                                    <td><i class="fas fa-compress-alt text-secondary me-1"></i> Plastik Kecil</td>
                                    <td><small><?= esc($packaging['ukuran_plastik_kecil'] ?: '-') ?></small></td>
                                    <td>
                                        <small>
                                            Awal: <strong><?= number_format((float)($packaging['plastik_kecil_awal'] ?? 0), 2) ?></strong>
                                            &rarr;
                                            Akhir: <strong><?= number_format((float)($packaging['plastik_kecil_akhir'] ?? 0), 2) ?></strong>
                                            &nbsp;=&nbsp;
                                            Pakai: <strong class="text-primary"><?= number_format((float)($packaging['plastik_kecil_pemakaian'] ?? 0), 2) ?></strong>
                                        </small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- Ukuran PE + Awal/Akhir/Pemakaian (Powerjet only) -->
                            <?php if ($detailIsPowerjet && (!empty($packaging['ukuran_pe']) || !empty($packaging['pe_awal']) || !empty($packaging['pe_akhir']))): ?>
                                <tr>
                                    <td><i class="fas fa-film text-info me-1"></i> PE</td>
                                    <td><small><?= esc($packaging['ukuran_pe'] ?: '-') ?></small></td>
                                    <td>
                                        <small>
                                            Awal: <strong><?= number_format((float)($packaging['pe_awal'] ?? 0), 2) ?></strong>
                                            &rarr;
                                            Akhir: <strong><?= number_format((float)($packaging['pe_akhir'] ?? 0), 2) ?></strong>
                                            &nbsp;=&nbsp;
                                            Pakai: <strong class="text-primary"><?= number_format((float)($packaging['pe_pemakaian'] ?? 0), 2) ?></strong>
                                        </small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <!-- Ukuran Box/Karung + Awal/Akhir/Pemakaian (Powerjet only) -->
                            <?php if ($detailIsPowerjet && (!empty($packaging['ukuran_box_karung']) || !empty($packaging['box_karung_awal']) || !empty($packaging['box_karung_akhir']))): ?>
                                <tr>
                                    <td><i class="fas fa-box text-warning me-1"></i> Box / Karung</td>
                                    <td><small><?= esc($packaging['ukuran_box_karung'] ?: '-') ?></small></td>
                                    <td>
                                        <small>
                                            Awal: <strong><?= number_format((float)($packaging['box_karung_awal'] ?? 0), 2) ?></strong>
                                            &rarr;
                                            Akhir: <strong><?= number_format((float)($packaging['box_karung_akhir'] ?? 0), 2) ?></strong>
                                            &nbsp;=&nbsp;
                                            Pakai: <strong class="text-primary"><?= number_format((float)($packaging['box_karung_pemakaian'] ?? 0), 2) ?></strong>
                                        </small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($packaging['meter_awal']) || !empty($packaging['meter_akhir'])): ?>
                                <?php if (!$detailHidePackaging): ?>
                                    <!-- Meter Awal/Akhir -->
                                    <tr>
                                        <td><i class="fas fa-tachometer-alt text-info me-1"></i> Meter</td>
                                        <td class="text-center">-</td>
                                        <td>-</td>
                                        <td>
                                            <small>
                                                Awal: <strong><?= number_format((float)($packaging['meter_awal'] ?? 0), 2) ?></strong>
                                                &rarr;
                                                Akhir: <strong><?= number_format((float)($packaging['meter_akhir'] ?? 0), 2) ?></strong>
                                                &nbsp;=&nbsp;
                                                Pakai: <strong class="text-primary"><?= number_format((float)($packaging['meter_pemakaian'] ?? 0), 2) ?></strong>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($isCcmMesin || $isIpsMesin): ?>
                                <!-- Plastik Awal/Akhir (CCM/IPS) -->
                                <tr>
                                    <td><i class="fas fa-vial text-warning me-1"></i> Plastik (Meter)</td>
                                    <td class="text-center">-</td>
                                    <td>-</td>
                                    <td>
                                        <small>
                                            Awal: <strong><?= number_format((float)($packaging['plastik_awal'] ?? 0), 2) ?></strong>
                                            &rarr;
                                            Akhir: <strong><?= number_format((float)($packaging['plastik_akhir'] ?? 0), 2) ?></strong>
                                            &nbsp;=&nbsp;
                                            Pakai: <strong class="text-primary"><?= number_format((float)abs(($packaging['plastik_awal'] ?? 0) - ($packaging['plastik_akhir'] ?? 0)), 2) ?></strong>
                                        </small>
                                    </td>
                                </tr>
                                <!-- Material Awal/Akhir (CCM/IPS) -->
                                <tr>
                                    <td><i class="fas fa-cube text-info me-1"></i> Material (Meter)</td>
                                    <td class="text-center">-</td>
                                    <td>-</td>
                                    <td>
                                        <small>
                                            Awal: <strong><?= number_format((float)($packaging['material_awal'] ?? 0), 2) ?></strong>
                                            &rarr;
                                            Akhir: <strong><?= number_format((float)($packaging['material_akhir'] ?? 0), 2) ?></strong>
                                            &nbsp;=&nbsp;
                                            Pakai: <strong class="text-primary"><?= number_format((float)abs(($packaging['material_awal'] ?? 0) - ($packaging['material_akhir'] ?? 0)), 2) ?></strong>
                                        </small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="mb-0 small text-muted">Tidak ada data packaging</p>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <div class="col-md-12">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-signature me-2 text-info"></i> Tanda Tangan Operator</h6>
        <div class="p-3 border rounded bg-light" style="display: flex; align-items: center; gap: 20px;">
            <!-- TTD Image Section -->
            <div style="flex-shrink: 0; min-width: 120px;">
                <div class="ttd-preview-container" style="height: 100px; width: 120px; display: flex; align-items: center; justify-content: center; background: white; border-radius: 8px; border: 2px solid #dee2e6;">
                    <?php if (!empty($header['auto_user_ttd'])): ?>
                        <?php
                        $ttdPath = $header['auto_user_ttd'];
                        $ttdUrl = base_url($ttdPath);
                        ?>
                        <img src="<?= $ttdUrl ?>" class="img-fluid" style="max-height: 95px; max-width: 115px;" alt="TTD Operator"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <div class="text-muted text-center small" style="display:none;">
                            <i class="fas fa-exclamation-circle mb-1 d-block text-warning"></i>
                            TTD tidak ditemukan
                        </div>
                    <?php else: ?>
                        <div class="text-muted text-center small">
                            <i class="fas fa-pen-fancy mb-1 d-block"></i>
                            TTD kosong
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Info Section -->
            <div style="flex-grow: 1;">
                <div class="fw-bold mb-2" style="font-size: 0.95rem;">
                    <i class="fas fa-user-check text-primary me-2"></i>
                    Operator <?php
                                $shiftRaw2 = (string)($header['shift'] ?? '');
                                if ($shiftRaw2 === '' || $shiftRaw2 === '0') {
                                    $shiftLabel = '-';
                                } elseif (strpos($shiftRaw2, 'extra_') === 0) {
                                    $shiftLabel = 'Extra Shift ' . str_replace('extra_', '', $shiftRaw2);
                                } else {
                                    $shiftLabel = 'Shift ' . $shiftRaw2;
                                }
                                echo $shiftLabel;
                                ?>
                    <span class="badge bg-primary ms-2" style="font-size: 0.75rem;">Auto-filled</span>
                </div>
                <small class="text-muted" style="line-height: 1.5;">
                    <i class="fas fa-info-circle me-1 text-info"></i>
                    Diambil otomatis dari profil operator berdasarkan shift
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-comment-alt me-2 text-secondary"></i> Catatan & Verifikasi</h6>
        <div class="p-3 border rounded mb-3 bg-light" style="min-height: 60px; max-height: 120px; overflow-y: auto;">
            <?= esc($header['catatan'] ?: 'Tidak ada catatan.') ?>
        </div>
        <div class="row g-2">
            <?php
            $labels = ['Shift 1', 'Shift 2', 'Shift 3', 'Supervisor'];
            $fields = ['ttd_shift_1', 'ttd_shift_2', 'ttd_shift_3', 'ttd_spv'];
            foreach ($fields as $idx => $f):
            ?>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-2 bg-white h-100" style="min-height: 110px; display: flex; flex-direction: column; align-items: center; justify-content: space-between;">
                        <div style="flex-grow: 1; display: flex; align-items: center; justify-content: center; width: 100%;">
                            <?php if (!empty($header[$f])): ?>
                                <img src="<?= base_url('uploads/ttd/' . $header[$f]) ?>" class="img-fluid" style="max-height: 65px; max-width: 85px;">
                            <?php else: ?>
                                <div class="text-muted text-center" style="font-size: 0.75rem; py-2;">
                                    <i class="fas fa-pen-fancy mb-1 d-block"></i>Belum TTD
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="fw-bold text-center border-top pt-1 w-100" style="font-size: 0.8rem; line-height: 1.2;">
                            <?= $labels[$idx] ?>
                        </div>
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