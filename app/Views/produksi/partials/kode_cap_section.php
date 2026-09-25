<?php
/**
 * Kode Cap Section — Powerjet 1 & 2 only
 * Variables: $kodeCapData (array keyed by jam_ke: 2,4,6,8), $isEdit (bool)
 */
$kodeCapData = $kodeCapData ?? [];
$jamRows     = [2, 4, 6, 8];
?>
<div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
    <div class="card-header bg-white border-bottom px-4 py-3">
        <h6 class="mb-0 fw-bold text-dark" style="font-size:.95rem;">
            <i class="fas fa-th text-warning me-2"></i>Kode Cap
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0 align-middle kode-cap-table"
                   style="min-width:900px; font-size:.78rem;">
                <thead>
                    <tr class="text-center">
                        <th class="bg-secondary text-white" style="width:60px;">Jam ke-</th>
                        <?php for ($c = 1; $c <= 12; $c++): ?>
                        <th class="bg-primary text-white" style="min-width:60px;"><?= $c ?></th>
                        <?php endfor; ?>
                        <th class="bg-success text-white" style="width:70px;">Runner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jamRows as $jam):
                        $row = $kodeCapData[$jam] ?? [];
                    ?>
                    <tr>
                        <!-- Jam ke- -->
                        <td class="text-center fw-bold bg-light" style="color:#374151;"><?= $jam ?></td>

                        <!-- Kode Cap 1–12 -->
                        <?php for ($c = 1; $c <= 12; $c++):
                            $val = $row["kode_cap_{$c}"] ?? '';
                        ?>
                        <td class="p-1">
                            <input type="text"
                                   name="kode_cap[<?= $jam ?>][kode_cap_<?= $c ?>]"
                                   class="form-control form-control-sm kode-cap-input text-center"
                                   data-jam="<?= $jam ?>"
                                   data-col="<?= $c ?>"
                                   value="<?= esc($val) ?>"
                                   placeholder="-"
                                   style="font-size:.75rem; padding:.2rem .3rem; border-radius:4px;">
                        </td>
                        <?php endfor; ?>

                        <!-- Runner (manual input - decimal) -->
                        <td class="p-1 text-center">
                            <input type="text"
                                   name="kode_cap[<?= $jam ?>][runner]"
                                   class="form-control form-control-sm runner-input text-center fw-bold"
                                   data-jam="<?= $jam ?>"
                                   value="<?= esc($row['runner'] ?? 0) ?>"
                                   placeholder="0.00"
                                   style="font-size:.8rem; padding:.2rem .3rem; border-radius:4px;
                                          color:#15803d; border:1.5px solid #86efac; background:#f0fdf4;">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="text-center" style="background:#fefce8; border-top:2px solid #fde047;">
                        <td class="fw-bold text-center" style="font-size:.72rem; color:#78350f; background:#fef9c3;">
                            Rata²
                        </td>
                        <?php for ($c = 1; $c <= 12; $c++): ?>
                        <td class="rata-col" data-col="<?= $c ?>"
                            style="font-size:.78rem; font-weight:700; color:#92400e; background:#fefce8;">
                            0.00
                        </td>
                        <?php endfor; ?>
                        <!-- rata-rata runner (rata2 dari 4 runner) -->
                        <td class="rata-runner fw-bold text-center"
                            style="font-size:.78rem; color:#15803d; background:#f0fdf4;">
                            0.00
                        </td>
                    </tr>
                    <tr style="background:#fff7ed; border-top:2px solid #fb923c;">
                        <td class="fw-bold text-center" style="font-size:.72rem; color:#7c2d12; background:#ffedd5; white-space:nowrap;">
                            Akhir :
                        </td>
                        <td colspan="12" class="text-left fw-bold"
                            style="font-size:.85rem; color:#c2410c; background:#fff7ed; border:none;">
                            <span class="rata-akhir"
                                  style="background:#ffedd5; border:1.5px solid #fb923c;
                                         border-radius:6px; padding:.15rem .6rem; display:inline-block;">
                                0.00
                            </span>
                        </td>
                        <!-- sel Runner kosong agar kolom tetap aligned -->
                        <td style="background:#fff7ed;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    function getTable(el) {
        // Scope ke tabel kode-cap terdekat dari elemen, atau seluruh document
        return el ? el.closest('.kode-cap-table') : null;
    }

    function qAll(scope, sel) {
        return (scope || document).querySelectorAll(sel);
    }

    function q(scope, sel) {
        return (scope || document).querySelector(sel);
    }

    function parseDecimal(str) {
        // Handle both comma (,) and period (.) as decimal separator
        if (typeof str !== 'string') str = String(str);
        str = str.trim();
        // Replace comma with period for parsing
        var normalized = str.replace(',', '.');
        return parseFloat(normalized) || 0;
    }

    function countRunner(jam, table) {
        // Runner is now manually input - removed auto-count
    }

    function updateRata(table) {
        var jams = [2, 4, 6, 8];
        var totalRows = jams.length;

        // Rata-rata per kolom: rata-rata nilai angka dari sel yang terisi
        for (var c = 1; c <= 12; c++) {
            var sum    = 0;
            var filled = 0;
            jams.forEach(function (jam) {
                var inp = q(table, '.kode-cap-input[data-jam="' + jam + '"][data-col="' + c + '"]');
                if (inp && inp.value.trim() !== '') {
                    var num = parseDecimal(inp.value);
                    if (!isNaN(num)) { sum += num; filled++; }
                }
            });
            var rataEl = q(table, '.rata-col[data-col="' + c + '"]');
            if (rataEl) rataEl.textContent = filled > 0 ? (sum / filled).toFixed(2) : '0.00';
        }

        // Rata-rata runner: rata-rata dari 4 nilai runner (manual inputs)
        var totalRunner = 0;
        var filledRunner = 0;
        jams.forEach(function (jam) {
            var runnerEl = q(table, '.runner-input[data-jam="' + jam + '"]');
            if (runnerEl) {
                var val = parseDecimal(runnerEl.value);
                totalRunner += val;
                if (val > 0) filledRunner++;
            }
        });
        var rataRunnerEl = q(table, '.rata-runner');
        if (rataRunnerEl) rataRunnerEl.textContent = filledRunner > 0 ? (totalRunner / totalRows).toFixed(2) : '0.00';

        // Akhir: rata-rata dari kolom yang punya nilai (rata-rata dari rata-rata)
        var sumRataKol  = 0;
        var filledKol   = 0;
        for (var cc = 1; cc <= 12; cc++) {
            var rataEl2 = q(table, '.rata-col[data-col="' + cc + '"]');
            var val = rataEl2 ? parseFloat(rataEl2.textContent) : 0;
            if (val > 0) { sumRataKol += val; filledKol++; }
        }
        var rataAkhirEl = q(table, '.rata-akhir');
        if (rataAkhirEl) rataAkhirEl.textContent = filledKol > 0 ? (sumRataKol / filledKol).toFixed(2) : '0.00';
    }

    function initOneTable(table) {
        qAll(table, '.kode-cap-input').forEach(function (inp) {
            if (inp.dataset.kodeCapBound) return;
            inp.dataset.kodeCapBound = '1';
            inp.addEventListener('input', function () {
                var tbl = getTable(this);
                updateRata(tbl);
            });
        });
        
        // Add event listener to runner inputs for manual entry
        qAll(table, '.runner-input').forEach(function (inp) {
            if (inp.dataset.runnerBound) return;
            inp.dataset.runnerBound = '1';
            inp.addEventListener('input', function () {
                var tbl = getTable(this);
                updateRata(tbl);
            });
            inp.addEventListener('change', function () {
                var tbl = getTable(this);
                updateRata(tbl);
            });
        });
        
        updateRata(table);
    }

    function initKodeCap() {
        document.querySelectorAll('.kode-cap-table').forEach(function (table) {
            initOneTable(table);
        });
    }

    // Expose globally so scripts.php can call after AJAX edit load
    window.initKodeCap = initKodeCap;

    // Run on DOMContentLoaded and after short delay for AJAX-loaded modals
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKodeCap);
    } else {
        initKodeCap();
    }
    setTimeout(initKodeCap, 300);
    setTimeout(initKodeCap, 800);
})();
</script>
