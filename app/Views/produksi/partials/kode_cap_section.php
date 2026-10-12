<?php
/**
 * Kode Cap Section — Powerjet 1 & 2 only
 * Variables: $kodeCapData (array keyed by jam_ke from DB), $isEdit (bool)
 *
 * Form structure: 4 fixed rows, each row has a <select> for jam_ke (1–12).
 * Defaults: row 0=2, row 1=4, row 2=6, row 3=8.
 * POST name: kode_cap_rows[{rowIndex}][jam_ke|kode_cap_1..12|runner]
 */
$kodeCapData    = $kodeCapData ?? [];
$defaultJamRows = [2, 4, 6, 8];

// Ambil hanya baris yang punya data nyata dari DB (kode_cap atau runner terisi).
// Seed default dari controller selalu ada, tapi semua kode_cap-nya null dan runner=0 —
// kita filter itu agar hanya baris DB asli yang masuk.
$dbRows = [];
foreach ($kodeCapData as $jam => $row) {
    $hasData = false;
    for ($c = 1; $c <= 12; $c++) {
        if (!empty($row["kode_cap_{$c}"])) { $hasData = true; break; }
    }
    if (!$hasData && (float)($row['runner'] ?? 0) > 0) $hasData = true;
    if ($hasData) {
        $dbRows[(int)$jam] = $row;
    }
}
ksort($dbRows);
$dbJams = array_keys($dbRows);

// Build tepat 4 baris untuk form.
// Prioritas: pakai data DB (urut ascending), sisa baris pakai default.
$formRows = [];
for ($i = 0; $i < 4; $i++) {
    if (isset($dbJams[$i])) {
        // Baris dari DB — jam dan kode_cap sudah terisi
        $formRows[] = $dbRows[$dbJams[$i]];
    } else {
        // Baris kosong — pakai default jam
        $jam = $defaultJamRows[$i];
        $emptyRow = ['jam_ke' => $jam, 'runner' => 0];
        for ($c = 1; $c <= 12; $c++) $emptyRow["kode_cap_{$c}"] = '';
        $formRows[] = $emptyRow;
    }
}
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
                        <th class="bg-secondary text-white" style="width:75px;">Jam ke-</th>
                        <?php for ($c = 1; $c <= 12; $c++): ?>
                        <th class="bg-primary text-white" style="min-width:60px;"><?= $c ?></th>
                        <?php endfor; ?>
                        <th class="bg-success text-white" style="width:70px;">Runner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formRows as $rowIdx => $row):
                        $currentJam = (int)($row['jam_ke'] ?? $defaultJamRows[$rowIdx]);
                    ?>
                    <tr class="kode-cap-row" data-row="<?= $rowIdx ?>">
                        <!-- Jam ke- (dropdown, pilih 1–12) -->
                        <td class="p-1 text-center">
                            <select name="kode_cap_rows[<?= $rowIdx ?>][jam_ke]"
                                    class="form-select form-select-sm jam-ke-select fw-bold text-center"
                                    data-row="<?= $rowIdx ?>"
                                    style="font-size:.8rem; padding:.2rem .3rem; border-radius:4px;
                                           color:#374151; border:1.5px solid #cbd5e1; background:#f8fafc;
                                           text-align-last:center;">
                                <?php for ($j = 1; $j <= 12; $j++): ?>
                                <option value="<?= $j ?>" <?= $currentJam === $j ? 'selected' : '' ?>><?= $j ?></option>
                                <?php endfor; ?>
                            </select>
                        </td>

                        <!-- Kode Cap 1–12 -->
                        <?php for ($c = 1; $c <= 12; $c++):
                            $val = $row["kode_cap_{$c}"] ?? '';
                        ?>
                        <td class="p-1">
                            <input type="text"
                                   name="kode_cap_rows[<?= $rowIdx ?>][kode_cap_<?= $c ?>]"
                                   class="form-control form-control-sm kode-cap-input text-center"
                                   data-row="<?= $rowIdx ?>"
                                   data-col="<?= $c ?>"
                                   value="<?= esc($val) ?>"
                                   placeholder="-"
                                   style="font-size:.75rem; padding:.2rem .3rem; border-radius:4px;">
                        </td>
                        <?php endfor; ?>

                        <!-- Runner (manual input - decimal) -->
                        <td class="p-1 text-center">
                            <input type="text"
                                   name="kode_cap_rows[<?= $rowIdx ?>][runner]"
                                   class="form-control form-control-sm runner-input text-center fw-bold"
                                   data-row="<?= $rowIdx ?>"
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
        return el ? el.closest('.kode-cap-table') : null;
    }

    function qAll(scope, sel) {
        return (scope || document).querySelectorAll(sel);
    }

    function q(scope, sel) {
        return (scope || document).querySelector(sel);
    }

    function parseDecimal(str) {
        if (typeof str !== 'string') str = String(str);
        return parseFloat(str.trim().replace(',', '.')) || 0;
    }

    function updateRata(table) {
        // Scan actual rows in the table — no hardcoded jam numbers needed.
        var rows = (table || document).querySelectorAll('.kode-cap-row');
        var totalRows = rows.length;

        // Rata-rata per kolom
        for (var c = 1; c <= 12; c++) {
            var sum    = 0;
            var filled = 0;
            rows.forEach(function (row) {
                var inp = row.querySelector('.kode-cap-input[data-col="' + c + '"]');
                if (inp && inp.value.trim() !== '') {
                    var num = parseDecimal(inp.value);
                    if (!isNaN(num)) { sum += num; filled++; }
                }
            });
            var rataEl = q(table, '.rata-col[data-col="' + c + '"]');
            if (rataEl) rataEl.textContent = filled > 0 ? (sum / filled).toFixed(2) : '0.00';
        }

        // Rata-rata runner
        var totalRunner  = 0;
        var filledRunner = 0;
        rows.forEach(function (row) {
            var runnerEl = row.querySelector('.runner-input');
            if (runnerEl) {
                var val = parseDecimal(runnerEl.value);
                totalRunner += val;
                if (val > 0) filledRunner++;
            }
        });
        var rataRunnerEl = q(table, '.rata-runner');
        if (rataRunnerEl) {
            rataRunnerEl.textContent = (totalRows > 0 && filledRunner > 0)
                ? (totalRunner / totalRows).toFixed(2)
                : '0.00';
        }

        // Akhir: rata-rata dari kolom yang punya nilai
        var sumRataKol = 0;
        var filledKol  = 0;
        for (var cc = 1; cc <= 12; cc++) {
            var rataEl2 = q(table, '.rata-col[data-col="' + cc + '"]');
            var val = rataEl2 ? parseFloat(rataEl2.textContent) : 0;
            if (val > 0) { sumRataKol += val; filledKol++; }
        }
        var rataAkhirEl = q(table, '.rata-akhir');
        if (rataAkhirEl) {
            rataAkhirEl.textContent = filledKol > 0 ? (sumRataKol / filledKol).toFixed(2) : '0.00';
        }
    }

    function initOneTable(table) {
        qAll(table, '.kode-cap-input').forEach(function (inp) {
            if (inp.dataset.kodeCapBound) return;
            inp.dataset.kodeCapBound = '1';
            inp.addEventListener('input', function () { updateRata(getTable(this)); });
        });

        qAll(table, '.runner-input').forEach(function (inp) {
            if (inp.dataset.runnerBound) return;
            inp.dataset.runnerBound = '1';
            inp.addEventListener('input',  function () { updateRata(getTable(this)); });
            inp.addEventListener('change', function () { updateRata(getTable(this)); });
        });

        updateRata(table);
    }

    function initKodeCap() {
        document.querySelectorAll('.kode-cap-table').forEach(function (table) {
            initOneTable(table);
        });
    }

    window.initKodeCap = initKodeCap;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKodeCap);
    } else {
        initKodeCap();
    }
    setTimeout(initKodeCap, 300);
    setTimeout(initKodeCap, 800);
})();
</script>
