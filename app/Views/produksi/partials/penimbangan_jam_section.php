<?php
if (!function_exists('_render_penimbangan_card')):
function _render_penimbangan_card(int $hour, float $galonVal, float $flashVal, array $errors, bool $isPowerjet = false): string
{
    $galonKey  = "jam_{$hour}_galon";
    $flashKey  = "jam_{$hour}_flash";
    $galonErr  = $errors[$galonKey] ?? '';
    $flashErr  = $errors[$flashKey] ?? '';
    $hasErr    = !empty($galonErr) || !empty($flashErr);
    $subtotal  = $galonVal + $flashVal;
    $galonLabel = $isPowerjet ? 'Cap Galon (kg)' : 'Galon (kg)';

    // Warna header: biru (1-4), cyan (5-8), ungu (9-12)
    if ($hour <= 4) {
        $headerBg = 'linear-gradient(135deg,#3b82f6,#2563eb)';
    } elseif ($hour <= 8) {
        $headerBg = 'linear-gradient(135deg,#0ea5e9,#0284c7)';
    } else {
        $headerBg = 'linear-gradient(135deg,#8b5cf6,#7c3aed)';
    }
    $borderColor = $hasErr ? '#fca5a5' : '#e2e8f0';

    ob_start(); ?>
    <div class="penimbangan-hour-card h-100"
         style="border:1.5px solid <?= $borderColor ?>;border-radius:10px;overflow:hidden;background:#fff;transition:box-shadow .2s,transform .2s;">

        <!-- Header -->
        <div style="background:<?= $headerBg ?>;padding:.45rem .75rem;display:flex;align-items:center;gap:.4rem;">
            <i class="fas fa-clock" style="color:rgba(255,255,255,.75);font-size:.7rem;"></i>
            <span style="color:#fff;font-weight:700;font-size:.82rem;letter-spacing:.3px;">Jam <?= $hour ?></span>
        </div>

        <!-- Inputs -->
        <div style="padding:.7rem;">
            <!-- Galon -->
            <div style="margin-bottom:.55rem;">
                <label style="display:block;font-size:.68rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.2rem;">
                    <i class="fas fa-water" style="color:#3b82f6;"></i> <?= esc($galonLabel) ?>
                </label>
                <input type="number"
                       name="penimbangan_jam[<?= $galonKey ?>]"
                       class="form-control form-control-sm penimbangan-galon-input <?= $hasErr ? 'is-invalid' : '' ?>"
                       data-hour="<?= $hour ?>"
                       value="<?= esc($galonVal ?: '') ?>"
                       min="0" max="999.99" step="0.01"
                       placeholder="0.00"
                       style="border-radius:6px;font-weight:600;font-size:.85rem;text-align:right;">
                <?php if (!empty($galonErr)): ?>
                    <div class="text-danger" style="font-size:.67rem;margin-top:.1rem;"><?= esc($galonErr) ?></div>
                <?php endif; ?>
            </div>

            <!-- Flash -->
            <div style="margin-bottom:.55rem;">
                <label style="display:block;font-size:.68rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.4px;margin-bottom:.2rem;">
                    <i class="fas fa-fire" style="color:#ef4444;"></i> Flash (kg)
                </label>
                <input type="number"
                       name="penimbangan_jam[<?= $flashKey ?>]"
                       class="form-control form-control-sm penimbangan-flash-input <?= $hasErr ? 'is-invalid' : '' ?>"
                       data-hour="<?= $hour ?>"
                       value="<?= esc($flashVal ?: '') ?>"
                       min="0" max="999.99" step="0.01"
                       placeholder="0.00"
                       style="border-radius:6px;font-weight:600;font-size:.85rem;text-align:right;">
                <?php if (!empty($flashErr)): ?>
                    <div class="text-danger" style="font-size:.67rem;margin-top:.1rem;"><?= esc($flashErr) ?></div>
                <?php endif; ?>
            </div>

            <!-- Subtotal -->
            <div style="border-top:1.5px dashed #cbd5e1;padding-top:.45rem;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:.68rem;font-weight:600;color:#64748b;">Subtotal</span>
                <span class="penimbangan-subtotal-value"
                      data-hour="<?= $hour ?>"
                      style="font-size:.78rem;font-weight:800;color:#2563eb;background:#eff6ff;padding:.1rem .4rem;border-radius:4px;">
                    <?= number_format($subtotal, 2) ?> kg
                </span>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
}
endif;
?>
<?php
/**
 * Penimbangan Jam Section - collapsible dropdown style
 * Variables: $mode ('add'|'edit'), $penimbanganData (flat array), $errors,
 *            $isPowerjetMesin (bool) — passed from form_fields.php
 */
$mode            = $mode            ?? 'add';
$penimbanganData = $penimbanganData ?? [];
$errors          = $errors          ?? [];

// Powerjet = 12 jam, Becum = 8 jam
$maxHour  = ($isPowerjetMesin ?? false) ? 12 : 8;

$totalGalon  = 0;
$totalFlash  = 0;
$filledGalon = 0;
$filledFlash = 0;
for ($h = 1; $h <= $maxHour; $h++) {
    $g = (float)($penimbanganData["jam_{$h}_galon"] ?? 0);
    $f = (float)($penimbanganData["jam_{$h}_flash"] ?? 0);
    $totalGalon += $g;
    $totalFlash += $f;
    if ($g > 0) $filledGalon++;
    if ($f > 0) $filledFlash++;
}
$grandTotal = $totalGalon + $totalFlash;
// Rata-rata dibagi jumlah kolom yang terisi (bukan total jam)
$rataGalon  = $filledGalon > 0 ? $totalGalon / $filledGalon : 0;
$rataFlash  = $filledFlash > 0 ? $totalFlash / $filledFlash : 0;

// Auto-expand jika ada data tersimpan (mode edit dengan data) atau ada error
$hasExistingData = $grandTotal > 0;
$isExpanded      = $hasExistingData || !empty($errors);
$collapseShow    = $isExpanded ? 'show' : '';
$rangeLabel      = ($isPowerjetMesin ?? false) ? '1–12' : '1–8';
?>

<div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">

    <!-- Toggle Button (acts as dropdown trigger) -->
    <div class="card-header bg-white border-bottom px-4 py-0" style="cursor:pointer;"
         data-bs-toggle="collapse"
         data-bs-target="#penimbanganCollapse"
         aria-expanded="<?= $isExpanded ? 'true' : 'false' ?>"
         aria-controls="penimbanganCollapse">
        <div class="d-flex align-items-center justify-content-between py-3">
            <!-- Left: title + summary pill -->
            <div class="d-flex align-items-center gap-3">
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:.95rem;">
                        <i class="fas fa-balance-scale text-primary me-2"></i>Durasi Penimbangan
                    </h6>
                    <small class="text-muted" style="font-size:.75rem;">Galon & Flash per jam (<?= $rangeLabel ?>)</small>
                </div>

                <!-- Live summary — hanya tampil saat collapsed -->
                <div class="penimbangan-collapsed-summary d-flex gap-2 <?= $isExpanded ? 'd-none' : '' ?>" style="<?= $isExpanded ? 'display:none!important' : '' ?>">
                    <span class="badge rounded-pill px-2 py-1" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:.72rem;font-weight:700;">
                        <i class="fas fa-water me-1"></i><span class="cs-galon"><?= number_format($totalGalon, 2) ?></span> kg
                    </span>
                    <span class="badge rounded-pill px-2 py-1" style="background:#fff1f2;color:#be123c;border:1px solid #fecdd3;font-size:.72rem;font-weight:700;">
                        <i class="fas fa-fire me-1"></i><span class="cs-flash"><?= number_format($totalFlash, 2) ?></span> kg
                    </span>
                    <span class="badge rounded-pill px-2 py-1" style="background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;font-size:.72rem;font-weight:700;">
                        Total: <span class="cs-grand"><?= number_format($grandTotal, 2) ?></span> kg
                    </span>
                </div>
            </div>

            <!-- Right: chevron -->
            <div class="d-flex align-items-center gap-2">
                <?php if ($hasExistingData): ?>
                <span class="badge bg-success rounded-pill px-2" style="font-size:.7rem;">
                    <i class="fas fa-check me-1"></i>Ada data
                </span>
                <?php else: ?>
                <span class="badge rounded-pill px-2" style="background:#f1f5f9;color:#94a3b8;font-size:.7rem;">
                    Belum diisi
                </span>
                <?php endif; ?>
                <i class="fas fa-chevron-down text-muted penimbangan-chevron"
                   style="transition:transform .25s ease; <?= $isExpanded ? 'transform:rotate(180deg)' : '' ?>; font-size:.8rem;"></i>
            </div>
        </div>
    </div>

    <!-- Collapsible body -->
    <div class="collapse <?= $collapseShow ?>" id="penimbanganCollapse">
        <div class="card-body p-4">

            <!-- Baris 1: Jam 1–4 -->
            <div class="d-flex align-items-center mb-2">
                <span class="badge me-2" style="background:#2563eb;font-size:.7rem;letter-spacing:.3px;">JAM 1 – 4</span>
                <hr class="flex-grow-1 m-0" style="border-color:#e2e8f0;">
            </div>
            <div class="row g-3 mb-4">
                <?php for ($hour = 1; $hour <= 4; $hour++):
                    $galonVal = (float)($penimbanganData["jam_{$hour}_galon"] ?? 0);
                    $flashVal = (float)($penimbanganData["jam_{$hour}_flash"] ?? 0);
                ?>
                <div class="col-6 col-md-3">
                    <?= _render_penimbangan_card($hour, $galonVal, $flashVal, $errors, $isPowerjetMesin ?? false) ?>
                </div>
                <?php endfor; ?>
            </div>

            <!-- Baris 2: Jam 5–8 -->
            <div class="d-flex align-items-center mb-2">
                <span class="badge me-2" style="background:#0ea5e9;font-size:.7rem;letter-spacing:.3px;">JAM 5 – 8</span>
                <hr class="flex-grow-1 m-0" style="border-color:#e2e8f0;">
            </div>
            <div class="row g-3 mb-4">
                <?php for ($hour = 5; $hour <= 8; $hour++):
                    $galonVal = (float)($penimbanganData["jam_{$hour}_galon"] ?? 0);
                    $flashVal = (float)($penimbanganData["jam_{$hour}_flash"] ?? 0);
                ?>
                <div class="col-6 col-md-3">
                    <?= _render_penimbangan_card($hour, $galonVal, $flashVal, $errors, $isPowerjetMesin ?? false) ?>
                </div>
                <?php endfor; ?>
            </div>

            <?php if ($isPowerjetMesin ?? false): ?>
            <!-- Baris 3: Jam 9–12 (Powerjet only) -->
            <div class="d-flex align-items-center mb-2">
                <span class="badge me-2" style="background:#7c3aed;font-size:.7rem;letter-spacing:.3px;">JAM 9 – 12</span>
                <hr class="flex-grow-1 m-0" style="border-color:#e2e8f0;">
            </div>
            <div class="row g-3 mb-4">
                <?php for ($hour = 9; $hour <= 12; $hour++):
                    $galonVal = (float)($penimbanganData["jam_{$hour}_galon"] ?? 0);
                    $flashVal = (float)($penimbanganData["jam_{$hour}_flash"] ?? 0);
                ?>
                <div class="col-6 col-md-3">
                    <?= _render_penimbangan_card($hour, $galonVal, $flashVal, $errors, true) ?>
                </div>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <!-- Summary bar -->
            <div class="row g-2">
                <div class="col-4">
                    <div class="d-flex align-items-center justify-content-between rounded px-3 py-2"
                         style="background:#eff6ff;border:1px solid #bfdbfe;">
                        <span class="small fw-bold text-primary">
                            <i class="fas fa-water me-1"></i>Total Galon
                        </span>
                        <span class="penimbangan-total-galon fw-bold" style="color:#1d4ed8;font-size:.88rem;">
                            <?= number_format($totalGalon, 2) ?> kg
                        </span>
                    </div>
                </div>
                <!-- Rata-rata Galon -->
                <div class="col-4">
                    <div class="d-flex align-items-center justify-content-between rounded px-3 py-2"
                         style="background:#e0f2fe;border:1px solid #7dd3fc;">
                        <span class="small fw-bold" style="color:#0369a1;">
                            <i class="fas fa-chart-line me-1"></i>Rata² Galon
                        </span>
                        <span class="penimbangan-rata-galon fw-bold" style="color:#0369a1;font-size:.88rem;">
                            <?= number_format($rataGalon, 2) ?> kg
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="d-flex align-items-center justify-content-between rounded px-3 py-2"
                         style="background:#fff1f2;border:1px solid #fecdd3;">
                        <span class="small fw-bold text-danger">
                            <i class="fas fa-fire me-1"></i>Total Flash
                        </span>
                        <span class="penimbangan-total-flash fw-bold" style="color:#be123c;font-size:.88rem;">
                            <?= number_format($totalFlash, 2) ?> kg
                        </span>
                    </div>
                </div>
                <!-- Rata-rata Flash -->
                <div class="col-4">
                    <div class="d-flex align-items-center justify-content-between rounded px-3 py-2"
                         style="background:#fef2f2;border:1px solid #fca5a5;">
                        <span class="small fw-bold" style="color:#b91c1c;">
                            <i class="fas fa-chart-line me-1"></i>Rata² Flash
                        </span>
                        <span class="penimbangan-rata-flash fw-bold" style="color:#b91c1c;font-size:.88rem;">
                            <?= number_format($rataFlash, 2) ?> kg
                        </span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="d-flex align-items-center justify-content-between rounded px-3 py-2"
                         style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <span class="small fw-bold text-success">
                            <i class="fas fa-balance-scale me-1"></i>Grand Total
                        </span>
                        <span class="penimbangan-grand-total fw-bold" style="color:#15803d;font-size:.88rem;">
                            <?= number_format($grandTotal, 2) ?> kg
                        </span>
                    </div>
                </div>
                <?php if (!($isPowerjetMesin ?? false)): ?>
                <div class="col-4">
                    <div class="d-flex align-items-center justify-content-between rounded px-3 py-2"
                         style="background:#fefce8;border:1px solid #fde047;">
                        <span class="small fw-bold" style="color:#854d0e;">
                            <i class="fas fa-weight-hanging me-1"></i>Total Tonase
                        </span>
                        <?php
                        // Rumus: rata² flash × (total bagus + total reject) / 1000
                        $totalBagusReject = (int)($header['grand_total_bagus'] ?? 0) + (int)($header['grand_total_reject'] ?? 0);
                        $tonasePhp = ($rataFlash * $totalBagusReject) / 1000;
                        ?>
                        <span class="penimbangan-total-tonase fw-bold" style="color:#854d0e;font-size:.88rem;">
                            <?= number_format($tonasePhp, 2) ?> kg
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<!-- end penimbangan section -->
<script>
(function () {
    // maxHour dikirim dari PHP ke JS
    const MAX_HOUR = <?= $maxHour ?>;

    // ── Chevron rotate on collapse toggle ──────────────────────────
    const collapseEl = document.getElementById('penimbanganCollapse');
    const chevron    = document.querySelector('.penimbangan-chevron');
    const summary    = document.querySelector('.penimbangan-collapsed-summary');

    if (collapseEl) {
        collapseEl.addEventListener('show.bs.collapse', () => {
            if (chevron) chevron.style.transform = 'rotate(180deg)';
            if (summary) summary.style.display = 'none';
        });
        collapseEl.addEventListener('hide.bs.collapse', () => {
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            if (summary) summary.style.display = '';
            updateCollapsedSummary();
        });
    }

    // ── Real-time calculation ──────────────────────────────────────
    function updateCalc() {
        let totalGalon = 0, totalFlash = 0;
        let filledGalon = 0, filledFlash = 0;

        for (let h = 1; h <= MAX_HOUR; h++) {
            const gEl = document.querySelector(`.penimbangan-galon-input[data-hour="${h}"]`);
            const fEl = document.querySelector(`.penimbangan-flash-input[data-hour="${h}"]`);
            const sub = document.querySelector(`.penimbangan-subtotal-value[data-hour="${h}"]`);

            const g = parseFloat(gEl?.value) || 0;
            const f = parseFloat(fEl?.value) || 0;
            if (sub) sub.textContent = (g + f).toFixed(2) + ' kg';
            totalGalon += g;
            totalFlash += f;
            if (g > 0) filledGalon++;
            if (f > 0) filledFlash++;
        }

        const grand     = totalGalon + totalFlash;
        // Rata-rata dibagi jumlah kolom yang terisi, bukan MAX_HOUR
        const rataGalon = filledGalon > 0 ? totalGalon / filledGalon : 0;
        const rataFlash = filledFlash > 0 ? totalFlash / filledFlash : 0;
        const q = (sel) => document.querySelector(sel);
        if (q('.penimbangan-total-galon')) q('.penimbangan-total-galon').textContent = totalGalon.toFixed(2) + ' kg';
        if (q('.penimbangan-rata-galon'))  q('.penimbangan-rata-galon').textContent  = rataGalon.toFixed(2)  + ' kg';
        if (q('.penimbangan-total-flash')) q('.penimbangan-total-flash').textContent = totalFlash.toFixed(2) + ' kg';
        if (q('.penimbangan-rata-flash'))  q('.penimbangan-rata-flash').textContent  = rataFlash.toFixed(2)  + ' kg';
        if (q('.penimbangan-grand-total')) q('.penimbangan-grand-total').textContent = grand.toFixed(2) + ' kg';
        if (q('.penimbangan-total-tonase')) {
            // Rumus: rata² flash × (total bagus + total reject) / 1000
            const totalBagus  = parseFloat(q('.grand_total_bagus_hidden')?.value)  || 0;
            const totalReject = parseFloat(q('.grand_total_reject_hidden')?.value) || 0;
            const totalProd   = totalBagus + totalReject;
            q('.penimbangan-total-tonase').textContent = ((rataFlash * totalProd) / 1000).toFixed(2) + ' kg';
        }

        updateCollapsedSummary(totalGalon, totalFlash, grand);
    }

    function updateCollapsedSummary(g, f, grand) {
        const q = (sel) => document.querySelector(sel);
        if (g === undefined) {
            g = 0; f = 0;
            for (let h = 1; h <= MAX_HOUR; h++) {
                g += parseFloat(document.querySelector(`.penimbangan-galon-input[data-hour="${h}"]`)?.value) || 0;
                f += parseFloat(document.querySelector(`.penimbangan-flash-input[data-hour="${h}"]`)?.value) || 0;
            }
            grand = g + f;
        }
        if (q('.cs-galon')) q('.cs-galon').textContent = g.toFixed(2);
        if (q('.cs-flash')) q('.cs-flash').textContent = f.toFixed(2);
        if (q('.cs-grand')) q('.cs-grand').textContent = grand.toFixed(2);
    }

    // ── Hover effect ──────────────────────────────────────────────
    function attachHover(card) {
        card.addEventListener('mouseenter', () => {
            card.style.boxShadow = '0 6px 16px rgba(37,99,235,.15)';
            card.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.boxShadow = '';
            card.style.transform = '';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.penimbangan-galon-input, .penimbangan-flash-input')
            .forEach(inp => {
                inp.addEventListener('input',  updateCalc);
                inp.addEventListener('change', updateCalc);
            });
        document.querySelectorAll('.penimbangan-hour-card').forEach(attachHover);
        updateCalc();
    });
})();
</script>
