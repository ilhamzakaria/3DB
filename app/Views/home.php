<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$fmtDate = static fn($v) => ($t = ($v ? strtotime($v) : false)) ? date('d M Y', $t) : '-';
$dash    = static fn($v) => trim((string) $v) !== '' ? esc($v) : '-';
$num     = static fn($v) => ($v !== '' && $v !== null && $v !== '-') ? number_format((int)$v) : '-';

$shiftBadge = static function ($s) {
  if ($s === '' || $s === null) return '-';
  $cls = match ((string)$s) {
    '1' => 'primary',
    '2' => 'warning',
    '3' => 'dark',
    default => 'secondary'
  };
  return '<span class="badge badge-' . $cls . ($s == '2' ? ' text-dark' : '') . '">' . esc($s) . '</span>';
};
?>

<style>
  /* ── Wrapper scroll ── */
  .home-scroll-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
  }

  /* ── Tabel ── */
  .home-tbl {
    min-width: 1500px;
    width: 100%;
    border-collapse: collapse;
    font-size: .85rem;
    /* lebih besar dari sebelumnya */
  }

  .home-tbl th,
  .home-tbl td {
    padding: 7px 10px;
    border: 1px solid #dee2e6;
    white-space: nowrap;
    vertical-align: middle;
  }

  /* ── Header departemen ── */
  .hdr-dept {
    font-size: .8rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    text-align: center;
    padding: 8px 10px;
    color: #fff;
  }

  .hdr-ppic {
    background: #4e73df;
  }

  .hdr-prod {
    background: #1cc88a;
  }

  .hdr-gudang {
    background: #36b9cc;
  }

  /* ── Sub-header ── */
  .home-tbl thead tr.sub-row th {
    background: #f0f3ff;
    font-size: .78rem;
    font-weight: 700;
    color: #444;
    text-align: center;
  }

  /* ── Pembatas kiri departemen ── */
  /* .sep-prod   { border-left: 3px solid #1cc88a !important; }
.sep-gudang { border-left: 3px solid #36b9cc !important; } */

  /* ── Garis tanggal sejajar ── */
  .tanggal-prod {
    border-left: 3px solid #1cc88a !important;
  }

  .tanggal-gudang {
    border-left: 3px solid #36b9cc !important;
  }

  /* ── Body rows ── */
  .home-tbl tbody tr:nth-child(even) {
    background: #fafbff;
  }

  .home-tbl tbody tr:hover {
    background: #eef2ff;
  }

  /* ── Badge SPK ── */
  .spk-badge {
    display: inline-block;
    background: rgba(78, 115, 223, .1);
    color: #4e73df;
    border: 1px solid rgba(78, 115, 223, .3);
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: .78rem;
  }

  /* ── Angka produksi ── */
  .good {
    color: #1cc88a;
    font-weight: 700;
  }

  .bad {
    color: #e74a3b;
  }

  /* ── Chip filter aktif ── */
  .fchip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #eef4ff;
    color: #1d4ed8;
    padding: 3px 12px;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 600;
  }

  /* ── Target/Realisasi shift ── */
  .shift-grid {
    display: flex;
    gap: 4px;
    justify-content: center;
  }

  .shift-cell {
    min-width: 38px;
    text-align: center;
    font-size: .72rem;
    padding: 2px 4px;
    border-radius: 4px;
    border: 1px solid #e3e6f0;
    background: #f8f9fc;
    line-height: 1.4;
  }

  .shift-cell small {
    display: block;
    font-size: .62rem;
    color: #999;
    font-weight: 600;
  }

  .shift-cell.has-val {
    background: #eef2ff;
    border-color: #c7d2fe;
    color: #3730a3;
  }

  .shift-cell.real-val {
    background: #f0fdf4;
    border-color: #86efac;
    color: #166534;
  }

  /* ── Info icon dengan tooltip ── */
  .fa-info-circle[title] {
    position: relative;
  }

  .fa-info-circle[title]:hover {
    color: #4e73df !important;
    transition: color 0.2s ease;
  }

  /* ── Info icon dengan tooltip ── */
  .fa-info-circle[title] {
    position: relative;
  }

  .fa-info-circle[title]:hover {
    color: #4e73df !important;
    transition: color 0.2s ease;
  }
</style>

<div class="container-fluid">

  <!-- Production Summary Section -->
  <?= view('partials/production_summary', compact('prodData')) ?>

  <div class="card shadow mb-3">

    <!-- ── Header filter bar ── -->
    <div class="card-header py-3">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

        <!-- Kiri: judul + chips aktif -->
        <div class="d-flex align-items-center flex-wrap gap-2">
          <span class="font-weight-bold text-primary mr-2">
            <i class="fas fa-chart-bar mr-1"></i> Dashboard Produksi
          </span>
          <?php if ($filter === 'day'):   ?><span class="fchip"><i class="fas fa-dot-circle fa-xs"></i> Hari Ini</span><?php endif; ?>
          <?php if ($filter === 'week'):  ?><span class="fchip"><i class="fas fa-dot-circle fa-xs"></i> 7 Hari</span><?php endif; ?>
          <?php if ($filter === 'month'): ?><span class="fchip"><i class="fas fa-dot-circle fa-xs"></i> 30 Hari</span><?php endif; ?>
          <?php if (!empty($start_date) || !empty($end_date)): ?>
            <span class="fchip">
              <i class="fas fa-calendar-alt fa-xs"></i>
              <?= esc($start_date ?: '...') ?> – <?= esc($end_date ?: '...') ?>
              <a href="<?= base_url('home') ?>" class="text-danger ml-1"><i class="fas fa-times-circle"></i></a>
            </span>
          <?php endif; ?>
          <?php if ($q !== ''): ?>
            <span class="fchip">
              <i class="fas fa-search fa-xs"></i> "<?= esc($q) ?>"
              <a href="<?= base_url('home') ?>" class="text-danger ml-1"><i class="fas fa-times-circle"></i></a>
            </span>
          <?php endif; ?>
        </div>

        <!-- Kanan: kontrol -->
        <div class="d-flex flex-wrap align-items-center gap-2">

          <!-- Shortcut -->
          <div class="btn-group btn-group-sm">
            <a href="<?= base_url('home?filter=day') ?>"
              class="btn <?= $filter === 'day'   ? 'btn-primary' : 'btn-outline-primary' ?>">Hari Ini</a>
            <a href="<?= base_url('home?filter=week') ?>"
              class="btn <?= $filter === 'week'  ? 'btn-primary' : 'btn-outline-primary' ?>">7 Hari</a>
            <a href="<?= base_url('home?filter=month') ?>"
              class="btn <?= $filter === 'month' ? 'btn-primary' : 'btn-outline-primary' ?>">30 Hari</a>
            <?php if ($filter): ?>
              <a href="<?= base_url('home') ?>" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
          </div>

          <!-- Date range -->
          <form method="get" action="<?= base_url('home') ?>" class="d-flex align-items-center gap-1 mb-0">
            <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= esc($q) ?>"><?php endif; ?>
            <input type="date" name="start_date" class="form-control form-control-sm"
              value="<?= esc($start_date) ?>" style="width:135px">
            <span class="text-muted small">–</span>
            <input type="date" name="end_date" class="form-control form-control-sm"
              value="<?= esc($end_date) ?>" style="width:135px">
            <button class="btn btn-sm btn-outline-primary px-2" type="submit">
              <i class="fas fa-filter"></i>
            </button>
          </form>

          <!-- Search -->
          <form method="get" action="<?= base_url('home') ?>" class="d-flex align-items-center gap-1 mb-0">
            <?php if ($filter): ?><input type="hidden" name="filter" value="<?= esc($filter) ?>"><?php endif; ?>
            <input type="text" name="q" class="form-control form-control-sm"
              style="min-width:200px" placeholder="Cari SPK, mesin, produk..."
              value="<?= esc($q) ?>">
            <button class="btn btn-sm btn-primary px-2" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </form>

        </div>
      </div>
    </div>

    <!-- ── Body tabel ── -->
    <div class="card-body p-0">
      <div class="home-scroll-wrap">
        <table class="home-tbl">
          <thead>

            <!-- Baris 1: Nama departemen -->
            <tr>
              <th colspan="8" class="hdr-dept hdr-ppic">
                <i class="fas fa-cogs mr-1"></i> LOG MESIN (PPIC2)
              </th>
              <th colspan="8" class="hdr-dept hdr-prod" style="border-left:3px solid #17a37a;">
                <i class="fas fa-industry mr-1"></i> Produksi
              </th>
              <th colspan="9" class="hdr-dept hdr-gudang" style="border-left:3px solid #1e8fa3;">
                <i class="fas fa-warehouse mr-1"></i> Warehouse 2
              </th>
            </tr>

            <!-- Baris 2: Nama kolom -->
            <tr class="sub-row">
              <!-- LOG MESIN (8 kolom) -->
              <th>Tanggal</th>
              <th>No. SPK</th>
              <th>Mesin</th>
              <th>Nama Produk</th>
              <th>Grade / Brand</th>
              <th>Warna</th>
              <th>Target (S1/S2/S3)</th>
              <th>Realisasi (S1/S2/S3)</th>
              <!-- Produksi (8 kolom) -->
              <th style="border-left:3px solid #17a37a;">Tanggal</th>
              <th>No. SPK</th>
              <th>Mesin</th>
              <th>Nama Produk</th>
              <th>Operator</th>
              <th>Shift</th>
              <th class="good">Hasil Bagus (S1/S2/S3)</th>
              <th class="bad">Reject (S1/S2/S3)</th>
              <!-- Warehouse 2 (9 kolom) -->
              <th style="border-left:3px solid #1e8fa3;">Tanggal</th>
              <th>SPK</th>
              <th>Produk</th>
              <th>Hasil Bagus</th>
              <th>Hasil Reject</th>
              <th>Material</th>
              <th>Colorant</th>
              <th>Packaging</th>
              <th>Operator</th>
            </tr>
          </thead>

          <tbody>
            <?php if (empty($rows)): ?>
              <tr>
                <td colspan="25" class="text-center text-muted py-5">
                  <i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>
                  Tidak ada data<?= $q !== '' ? ' untuk "<b>' . esc($q) . '</b>"' : '' ?>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($rows as $row):
                $m = $row['mesin']      ?? [];
                $p = $row['produksi']   ?? [];
                $w = $row['warehouse2'] ?? [];

                // Shift warna berdasarkan LOG MESIN atau Produksi
                $shif = $m['shif'] ?? $p['shift'] ?? '';

                // Grade/brand combo
                $grade = trim($m['grade'] ?? '');
                $brand = trim($m['brand'] ?? '');
                $gradeBrand = implode(' / ', array_filter([$grade, $brand]));

                // Target dan Realisasi per shift
                $t1 = $m['target_shift1']    ?? '';
                $t2 = $m['target_shift2']    ?? '';
                $t3 = $m['target_shift3']    ?? '';
                $r1 = $m['realisasi_shift1'] ?? '';
                $r2 = $m['realisasi_shift2'] ?? '';
                $r3 = $m['realisasi_shift3'] ?? '';
              ?>
                <tr>
                  <!-- ── LOG MESIN ── -->
                  <td><?= esc($fmtDate($m['tanggal'] ?? '')) ?></td>
                  <td><span class="spk-badge"><?= $dash($m['no_spk'] ?? '') ?></span></td>
                  <td>
                    <?php if (!empty($m['mesin_label'])): ?>
                      <span class="badge badge-light border text-secondary font-weight-bold"><?= esc($m['mesin_label']) ?></span>
                      <?php else: ?><?= $dash($m['nama_mesin'] ?? '') ?><?php endif; ?>
                      <?php if (!empty($m['nomor_mesin'])): ?>
                        <small class="text-muted ml-1"><?= esc($m['nomor_mesin']) ?></small>
                      <?php endif; ?>
                  </td>
                  <td><?= $dash($m['nama_produk'] ?? '') ?></td>
                  <td><?= $dash($gradeBrand) ?></td>
                  <td><?= $dash($m['warna'] ?? '') ?></td>
                  <!-- <td class="text-center"><?= $shiftBadge($shif) ?></td> -->

                  <!-- Target S1/S2/S3 -->
                  <td>
                    <div class="shift-grid">
                      <?php foreach ([['S1', $t1], ['S2', $t2], ['S3', $t3]] as [$lbl, $val]): ?>
                        <div class="shift-cell <?= $val !== '' ? 'has-val' : '' ?>">
                          <small><?= $lbl ?></small>
                          <?= $val !== '' ? esc(number_format((int)$val)) : '-' ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </td>

                  <!-- Realisasi S1/S2/S3 -->
                  <td>
                    <div class="shift-grid">
                      <?php
                      // Prefer produksi realisasi if available, otherwise use mesin table
                      $r1 = $m['produksi_realisasi_shift1'] ?? $m['realisasi_shift1'] ?? '';
                      $r2 = $m['produksi_realisasi_shift2'] ?? $m['realisasi_shift2'] ?? '';
                      $r3 = $m['produksi_realisasi_shift3'] ?? $m['realisasi_shift3'] ?? '';
                      $jamAwal = $m['waktu_awal_realisasi'] ?? '';
                      $jamAkhir = $m['waktu_akhir_realisasi'] ?? '';
                      $jamDisplay = '';
                      if ($jamAwal || $jamAkhir) {
                        $jamDisplay = 'Jam Realisasi: ' . ($jamAwal ? date('H:i', strtotime($jamAwal)) : '-') . ' - ' . ($jamAkhir ? date('H:i', strtotime($jamAkhir)) : '-');
                      }

                      // Helper function to determine cell color based on target vs realisasi
                      $getCellStyle = function ($target, $realisasi) {
                        $t = (int)($target ?? 0);
                        $r = (int)($realisasi ?? 0);
                        if ($r === 0 && $t === 0) return 'background: #f8f9fc; border-color: #e3e6f0;'; // No data
                        if ($r >= $t) return 'background: #f0fdf4; border-color: #86efac; color: #166534;'; // Green - target achieved
                        return 'background: #fef2f2; border-color: #fca5a5; color: #991b1b;'; // Red - target not met
                      };
                      ?>
                      <?php foreach ([['S1', $r1, $t1], ['S2', $r2, $t2], ['S3', $r3, $t3]] as [$lbl, $val, $target]): ?>
                        <div class="shift-cell" style="<?= $getCellStyle($target, $val) ?>" title="<?= $jamDisplay ?: 'Waktu realisasi' ?>">
                          <small><?= $lbl ?></small>
                          <div style="display: flex; align-items: center; justify-content: center; gap: 2px; font-size: 0.85rem;">
                            <?= $val !== '' && $val !== null ? esc(number_format((int)$val)) : '-' ?>
                            <i class="fas fa-clock" style="font-size: 0.55rem; opacity: 0.8;" title="<?= $jamDisplay ?: 'Waktu realisasi' ?>"></i>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </td>

                  <!-- ── Produksi ── -->
                  <td style="border-left:3px solid #17a37a;"><?= esc($fmtDate($p['tanggal'] ?? '')) ?></td>
                  <td><span class="spk-badge"><?= $dash($p['no_spk'] ?? $p['nomor_spk'] ?? '') ?></span></td>
                  <td><?= $dash($p['nama_mesin'] ?? '') ?></td>
                  <td><?= $dash($p['nama_produk'] ?? '') ?></td>
                  <td><?= $dash($p['operator']   ?? '') ?></td>
                  <td class="text-center"><?= $shiftBadge($p['shift'] ?? '') ?></td>

                  <!-- Hasil Bagus S1/S2/S3 -->
                  <td>
                    <div class="shift-grid">
                      <?php
                      $b1 = $m['produksi_bagus_shift1'] ?? null;
                      $b2 = $m['produksi_bagus_shift2'] ?? null;
                      $b3 = $m['produksi_bagus_shift3'] ?? null;
                      $hasShiftData = ($b1 !== null || $b2 !== null || $b3 !== null);
                      // Fallback: jika tidak ada data per-shift dari mesin, pakai grand_total dari produksi
                      if (!$hasShiftData && isset($p['grand_total_bagus']) && $p['grand_total_bagus'] !== null && $p['grand_total_bagus'] !== '') {
                        $b1 = $p['grand_total_bagus'];
                      }
                      ?>
                      <?php foreach ([['S1', $b1], ['S2', $b2], ['S3', $b3]] as [$lbl, $val]): ?>
                        <div class="shift-cell <?= $val !== null && $val !== '' ? 'real-val' : '' ?>" style="border-color: #86efac;">
                          <small><?= (!$hasShiftData && $lbl !== 'S1') ? '' : $lbl ?></small>
                          <?= $val !== null && $val !== '' ? esc(number_format((int)$val)) : '-' ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </td>

                  <!-- Reject S1/S2/S3 -->
                  <td>
                    <div class="shift-grid">
                      <?php
                      $rj1 = $m['produksi_reject_shift1'] ?? null;
                      $rj2 = $m['produksi_reject_shift2'] ?? null;
                      $rj3 = $m['produksi_reject_shift3'] ?? null;
                      $hasShiftDataReject = ($rj1 !== null || $rj2 !== null || $rj3 !== null);
                      // Fallback: jika tidak ada data per-shift dari mesin, pakai grand_total dari produksi
                      if (!$hasShiftDataReject && isset($p['grand_total_reject']) && $p['grand_total_reject'] !== null && $p['grand_total_reject'] !== '') {
                        $rj1 = $p['grand_total_reject'];
                      }
                      ?>
                      <?php foreach ([['S1', $rj1], ['S2', $rj2], ['S3', $rj3]] as [$lbl, $val]): ?>
                        <div class="shift-cell <?= $val !== null && $val !== '' ? 'real-val' : '' ?>" style="border-color: #fca5a5;">
                          <small><?= (!$hasShiftDataReject && $lbl !== 'S1') ? '' : $lbl ?></small>
                          <?= $val !== null && $val !== '' ? esc(number_format((int)$val)) : '-' ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </td>

                  <!-- ── Warehouse 2 ── -->
                  <td style="border-left:3px solid #1e8fa3;"><?= esc($fmtDate($w['tanggal'] ?? '')) ?></td>
                  <td><?= $dash($w['spk']          ?? '') ?></td>
                  <td style="max-width:160px;white-space:normal"><?= $dash($w['produk']       ?? '') ?></td>
                  <td class="text-right good"><?= ($w['hasil_bagus']  ?? '') !== '' ? esc(number_format((float)$w['hasil_bagus'],  2)) : '-' ?></td>
                  <td class="text-right bad"><?= ($w['hasil_reject'] ?? '') !== '' ? esc(number_format((float)$w['hasil_reject'], 2)) : '-' ?></td>
                  <td style="max-width:140px;white-space:normal"><?= $dash($w['material']     ?? '') ?></td>
                  <td style="max-width:120px;white-space:normal"><?= $dash($w['colorant']     ?? '') ?></td>
                  <td style="max-width:140px;white-space:normal"><?= $dash($w['packaging']    ?? '') ?></td>
                  <td style="max-width:140px;white-space:normal"><?= $dash($w['operator']     ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Footer paginasi -->
      <div class="px-3 py-2 border-top d-flex flex-wrap justify-content-between align-items-center bg-light gap-2">
        <small class="text-muted">
          Menampilkan
          <strong><?= number_format(($current_page - 1) * $per_page + 1) ?></strong> –
          <strong><?= number_format(min($current_page * $per_page, $total_rows)) ?></strong>
          dari <strong><?= number_format($total_rows) ?></strong> baris
        </small>
        <div><?= $pager ?></div>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>