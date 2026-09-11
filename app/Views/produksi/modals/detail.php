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
                <tr><td class="text-muted">Mesin & No Mesin</td><td><?= esc($header['nama_mesin']) ?><?php if (!empty($ppic_nomor_mesin)): ?> dan <?= esc($ppic_nomor_mesin) ?><?php endif; ?></td></tr>
                <tr><td class="text-muted">Produk</td><td><?= esc($header['nama_produk']) ?></td></tr>
                <tr><td class="text-muted">Batch</td><td><?= esc($header['batch_number'] ?: '-') ?></td></tr>
                <tr><td class="text-muted">Type Produk</td><td><?= esc($header['type_produk'] ?? '-' ?: '-') ?></td></tr>
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

        <?php 
        // Detect if record is Becum machine
        $detailMesinNama = strtolower(str_replace([' ', '-', '_'], '', $header['nama_mesin'] ?? ''));
        $detailIsBecum   = ($detailMesinNama === 'becum');
        ?>

        <?php if ($detailIsBecum): ?>
        <div class="col-md-6">
            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="fas fa-flask me-2 text-primary"></i> Status Spesifik Becum</h6>
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
                
                <!-- Komposisi Detail -->
                <?php if (!empty($materials) && ($materials[0]['komposisi_nama_1'] || $materials[0]['komposisi_persen_1'])): ?>
                <hr class="my-2">
                <p class="mb-2 small"><strong class="text-dark">Komposisi:</strong></p>
                <?php
                $isDetailBecum = strtolower(trim($header['nama_mesin'] ?? '')) === 'becum';
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
                <table class="table table-sm table-borderless mb-0" style="font-size: 0.7rem;">
                    <thead>
                        <tr>
                            <th class="ps-0 py-1 text-muted fw-normal">Nama</th>
                            <th class="text-end pe-0 py-1 text-muted fw-normal">Persen</th>
                            <?php if ($isDetailBecum && $hasKgData): ?>
                            <th class="text-end pe-0 py-1 text-muted fw-normal">KG</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($komposisiList as $comp): ?>
                    <tr style="line-height: 1.2;">
                        <td class="ps-0 py-1"><strong><?= esc($comp['nama']) ?></strong></td>
                        <td class="text-end pe-0 py-1"><strong><?= number_format((float)$comp['persen'], 2) ?> %</strong></td>
                        <?php if ($isDetailBecum && $hasKgData): ?>
                        <td class="text-end pe-0 py-1">
                            <?= ($comp['kg'] !== null && $comp['kg'] !== '') ? number_format((float)$comp['kg'], 3) . ' kg' : '-' ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr style="line-height: 1.2; border-top: 1px solid #dee2e6;">
                        <td class="ps-0 py-1"><strong class="text-primary">Total:</strong></td>
                        <td class="text-end pe-0 py-1">
                            <span class="badge <?= $totalPersen == 100 ? 'bg-success' : ($totalPersen < 100 ? 'bg-warning' : 'bg-danger') ?>" style="font-size: 0.65rem;">
                                <strong><?= number_format($totalPersen, 2) ?> %</strong>
                            </span>
                        </td>
                        <?php if ($isDetailBecum && $hasKgData): ?>
                        <td class="text-end pe-0 py-1">
                            <span class="badge bg-secondary" style="font-size: 0.65rem;">
                                <strong><?= number_format($totalKgDetail, 3) ?> kg</strong>
                            </span>
                        </td>
                        <?php endif; ?>
                    </tr>
                    </tfoot>
                </table>
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
                <?php if (!empty($tbInItems)): ?>
                <div class="mt-3 p-2 border rounded" style="background: #f0f7ff; border-color: #cce5ff !important;">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-primary me-2" style="font-size: 0.75rem; padding: 0.4rem 0.7rem;">TB IN</span>
                        <span class="text-primary fw-500" style="font-size: 0.8rem; letter-spacing: 0.3px;">Bahan Baku Masuk</span>
                    </div>
                    <table class="table table-sm table-bordered mb-0" style="font-size: 0.75rem;">
                        <thead class="bg-light"><tr><th>Nama</th><th class="text-center">%</th><th>Lot</th></tr></thead>
                        <tbody>
                        <?php foreach ($tbInItems as $item): ?>
                            <tr><td><?= esc($item['nama']) ?></td><td class="text-center"><?= esc($item['persen']) ?></td><td><?= esc($item['lot']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <?php if (!empty($tbOutItems)): ?>
                <div class="mt-3 p-2 border rounded" style="background: #fff3f3; border-color: #ffc9d0 !important;">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-danger me-2" style="font-size: 0.75rem; padding: 0.4rem 0.7rem;">TB OUT</span>
                        <span class="text-danger fw-500" style="font-size: 0.8rem; letter-spacing: 0.3px;">Bahan Baku Keluar</span>
                    </div>
                    <table class="table table-sm table-bordered mb-0" style="font-size: 0.75rem;">
                        <thead class="bg-light"><tr><th>Nama</th><th class="text-center">%</th><th>Lot</th></tr></thead>
                        <tbody>
                        <?php foreach ($tbOutItems as $item): ?>
                            <tr><td><?= esc($item['nama']) ?></td><td class="text-center"><?= esc($item['persen']) ?></td><td><?= esc($item['lot']) ?></td></tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                <?php elseif (!empty($colorants[0]['tb_in']) || !empty($colorants[0]['tb_out'])): ?>
                <p class="mb-1 small">
                    <strong>TB in:</strong> <?= esc($colorants[0]['tb_in'] ?: '-') ?>
                    &nbsp;|&nbsp;
                    <strong>TB out:</strong> <?= esc($colorants[0]['tb_out'] ?: '-') ?>
                </p>
                <?php endif; ?>
            
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
                        <?php if (!empty($packaging['meter_awal']) || !empty($packaging['meter_akhir'])): ?>
                        <div class="col-12 mt-1">
                            <span class="badge bg-white text-dark border w-100 text-start font-weight-normal">
                                <i class="fas fa-tachometer-alt text-info me-1"></i>
                                Awal: <?= number_format((float)($packaging['meter_awal'] ?? 0), 2) ?>
                                &nbsp;&rarr;&nbsp;
                                Akhir: <?= number_format((float)($packaging['meter_akhir'] ?? 0), 2) ?>
                                &nbsp;=&nbsp;
                                <strong>Pakai: <?= number_format((float)($packaging['meter_pemakaian'] ?? 0), 2) ?></strong>
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
