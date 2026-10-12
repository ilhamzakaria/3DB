<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Produksi Realtime</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ── Reset / Base ─────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            overflow: hidden; /* no scroll on body, inner grid scrolls */
        }

        /* ── Fullscreen wrapper ───────────────────────────────── */
        .realtime-shell {
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 100vw;
            padding: .6rem .85rem .5rem;
            gap: .6rem;
            overflow: hidden;
        }

        /* ── Top bar ──────────────────────────────────────────── */
        .realtime-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
            flex-shrink: 0;
        }

        .realtime-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: .45rem;
        }

        .realtime-meta {
            display: flex;
            align-items: center;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .last-update-badge {
            font-size: .7rem;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: .22rem .7rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .interval-badge {
            font-size: .7rem;
            color: #0369a1;
            background: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 20px;
            padding: .22rem .7rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ── Scrollable content area ──────────────────────────── */
        .realtime-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            /* hide scrollbar but keep scroll */
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .realtime-content::-webkit-scrollbar { width: 5px; }
        .realtime-content::-webkit-scrollbar-track { background: transparent; }
        .realtime-content::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        /* ── Card Grid ────────────────────────────────────────── */
        .realtime-grid {
            display: grid;
            gap: .75rem;
        }

        .realtime-grid-4 { grid-template-columns: repeat(4, 1fr); }
        .realtime-grid-5 { grid-template-columns: repeat(5, 1fr); }

        @media (max-width: 1200px) {
            .realtime-grid-4 { grid-template-columns: repeat(2, 1fr); }
            .realtime-grid-5 { grid-template-columns: repeat(3, 1fr); }
        }
        @media (max-width: 768px) {
            .realtime-grid-4,
            .realtime-grid-5 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .realtime-grid-4,
            .realtime-grid-5 { grid-template-columns: 1fr; }
        }

        /* ── Machine Card ─────────────────────────────────────── */
        .mesin-card {
            background: #fff;
            border-radius: 14px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            overflow: hidden;
            transition: box-shadow .2s, border-color .2s;
        }

        .mesin-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }

        .mesin-card.running { border-color: #86efac; }
        .mesin-card.idle    { border-color: #e2e8f0; }
        .mesin-card.problem { border-color: #fca5a5; }

        /* card header bar */
        .card-head {
            padding: .45rem .75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }

        .mesin-card.running .card-head { background: linear-gradient(135deg,#ecfdf5 0%,#d1fae5 100%); }
        .mesin-card.idle    .card-head { background: #f8fafc; }
        .mesin-card.problem .card-head { background: linear-gradient(135deg,#fef2f2 0%,#fee2e2 100%); }

        .mesin-name {
            font-size: .85rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .gear-icon { font-size: 1.2rem; color: #94a3b8; transition: color .3s; }
        .mesin-card.running .gear-icon { color: #16a34a; animation: gear-spin 1.5s linear infinite; }
        .mesin-card.problem .gear-icon { color: #dc2626; animation: gear-spin 0.8s linear infinite; }

        @keyframes gear-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* status badge */
        .status-badge {
            font-size: .66rem;
            font-weight: 700;
            border-radius: 20px;
            padding: .2rem .6rem;
            display: flex;
            align-items: center;
            gap: .3rem;
            white-space: nowrap;
        }

        .status-badge.running { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .status-badge.idle    { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }
        .status-badge.problem { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* pulse dot */
        .pulse-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #22c55e;
            display: inline-block;
            animation: pulse-ring 1.4s ease-out infinite;
        }

        .pulse-dot.problem { background: #dc2626; animation: pulse-ring-warning 1.4s ease-out infinite; }

        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(34,197,94,.5); }
            70%  { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
            100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
        }

        @keyframes pulse-ring-warning {
            0%   { box-shadow: 0 0 0 0 rgba(220,38,38,.5); }
            70%  { box-shadow: 0 0 0 5px rgba(220,38,38,0); }
            100% { box-shadow: 0 0 0 0 rgba(220,38,38,0); }
        }

        @keyframes pulse-warning {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* card body rows */
        .card-body-rows { padding: .35rem .75rem .5rem; }

        .data-row {
            display: flex;
            align-items: baseline;
            padding: .13rem 0;
            border-bottom: 1px solid #f1f5f9;
            gap: 0;
        }

        .data-row:last-child { border-bottom: none; }

        .data-label {
            font-size: .8rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
            flex-shrink: 0;
            font-weight: 700;
        }

        .data-label::after {
            content: ' :\00a0';
            color: #94a3b8;
            font-weight: 400;
        }

        .data-value {
            font-size: .95rem;
            font-weight: 600;
            color: #1e293b;
            word-break: break-word;
        }

        .data-value.highlight { color: #0ea5e9; }
        .data-value.success   { color: #16a34a; }
        .data-value.danger    { color: #dc2626; }
        .data-value.muted     { color: #94a3b8; font-weight: 400; }

        /* produk & hasil bagus lebih besar */
        [id^="produk-"], [id^="bagus-"] { font-size: 1.1rem; }

        /* selesai spk badge */
        .spk-done { font-size: .63rem; background: #dcfce7; color: #15803d; border-radius: 6px; padding: .1rem .4rem; margin-left: .3rem; }

        /* skeleton shimmer */
        .shimmer {
            background: linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer-move 1.2s infinite;
            border-radius: 4px;
            display: inline-block;
            height: .8rem;
            width: 60%;
        }

        @keyframes shimmer-move {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* hourly breakdown */
        .hourly-section {
            margin-top: .55rem;
            border-top: 1px solid #f1f5f9;
            padding-top: .45rem;
        }

        .hourly-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
            padding: .12rem 0;
            font-size: .7rem;
            font-weight: 700;
            color: #0369a1;
        }

        .hourly-toggle:hover { color: #0284c7; }

        .hourly-toggle i {
            font-size: .63rem;
            transition: transform .2s;
        }

        .hourly-toggle.collapsed i { transform: rotate(-90deg); }

        .hourly-list {
            margin-top: .4rem;
            max-height: 300px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: .3rem;
        }

        .hourly-list.hidden { display: none; }

        .jam-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-left: 3px solid #0ea5e9;
            padding: .3rem .4rem;
            border-radius: 4px;
            font-size: .68rem;
        }

        .jam-label { font-weight: 600; color: #0369a1; min-width: 70px; }
        .jam-value { font-weight: 700; color: #16a34a; text-align: right; }

        /* error toast */
        .realtime-toast {
            position: fixed;
            bottom: 1.2rem;
            right: 1.2rem;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
            border-radius: 10px;
            padding: .6rem 1rem;
            font-size: .78rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,.12);
            z-index: 9999;
            opacity: 0;
            transform: translateY(12px);
            transition: opacity .3s, transform .3s;
            pointer-events: none;
        }

        .realtime-toast.show { opacity: 1; transform: translateY(0); }

        /* ── Downtime Section ──────────────────────────────── */
        .downtime-section {
            margin-top: .5rem;
            padding: .4rem;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left: 3px solid #dc2626;
            border-radius: 4px;
            display: none;
        }

        .downtime-section.active { display: block; }

        .downtime-label {
            font-size: .65rem;
            color: #b91c1c;
            text-transform: uppercase;
            letter-spacing: .3px;
            font-weight: 700;
            margin-bottom: .2rem;
        }

        .downtime-marquee {
            font-size: .78rem;
            color: #b91c1c;
            font-weight: 600;
            overflow: hidden;
            white-space: nowrap;
            background: #fff5f5;
            padding: .25rem .35rem;
            border-radius: 3px;
            border: 1px solid #fca5a5;
        }

        .downtime-text {
            display: inline-block;
            padding-left: 100%;
            animation: marquee 10s linear infinite;
        }

        @keyframes marquee {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }

        .downtime-text:hover {
            animation-play-state: paused;
        }
    </style>
</head>
<body>

<div class="realtime-shell">

    <!-- Top bar -->
    <div class="realtime-topbar">
        <h1 class="realtime-title">
            <i class="fas fa-bolt text-warning"></i> Monitor Produksi Realtime
        </h1>
        <div class="realtime-meta">
            <div class="interval-badge">
                <i class="fas fa-sync-alt"></i> Refresh tiap <span id="countdown">30</span>s
            </div>
            <div class="last-update-badge">
                <i class="fas fa-clock"></i> Update: <span id="lastUpdate">—</span>
            </div>
        </div>
    </div>

    <!-- Scrollable card area -->
    <div class="realtime-content">

        <!-- Row 1: 4 card -->
        <div class="realtime-grid realtime-grid-4">
            <?php $i = 0; foreach ($mesinLabels as $slug => $label): if ($i >= 4) break; $i++; ?>
            <div class="mesin-card idle" id="card-<?= $slug ?>">
                <div class="card-head">
                    <div class="mesin-name">
                        <i class="fas fa-cog gear-icon" id="gear-<?= $slug ?>"></i>
                        <?= esc($label) ?>
                    </div>
                    <span class="status-badge idle" id="badge-<?= $slug ?>">
                        <i class="fas fa-minus-circle"></i> Idle
                    </span>
                </div>
                <div class="card-body-rows" id="body-<?= $slug ?>">
                    <div class="data-row"><span class="data-label">SPK</span><span class="data-value highlight shimmer" id="spk-<?= $slug ?>"></span></div>
                    <div class="data-row"><span class="data-label">Produk</span><span class="data-value shimmer" id="produk-<?= $slug ?>"></span></div>
                    <div class="data-row"><span class="data-label">Brand</span><span class="data-value shimmer" id="brand-<?= $slug ?>"></span></div>
                    <div class="data-row" id="grade-row-<?= $slug ?>" style="display:none;"><span class="data-label">Grade</span><span class="data-value shimmer" id="grade-<?= $slug ?>"></span></div>
                    <div class="data-row" id="warna-row-<?= $slug ?>" style="display:none;"><span class="data-label">Warna</span><span class="data-value shimmer" id="warna-<?= $slug ?>"></span></div>
                    <div class="data-row"><span class="data-label">Hasil Bagus</span><span class="data-value success shimmer" id="bagus-<?= $slug ?>"></span></div>
                    <!-- <div class="data-row"><span class="data-label">Total Reject</span><span class="data-value danger shimmer" id="reject-<?= $slug ?>"></span></div> -->
                    <div class="data-row"><span class="data-label">Sisa SPK</span><span class="data-value shimmer" id="sisapo-<?= $slug ?>"></span></div>
                    <!-- <div class="data-row"><span class="data-label">Total SPK</span><span class="data-value shimmer" id="totalspk-<?= $slug ?>"></span></div> -->
                    <div class="data-row"><span class="data-label">Grup & Shift</span><span class="data-value shimmer" id="operator-<?= $slug ?>"></span></div>
                    <div class="downtime-section" id="downtime-section-<?= $slug ?>">
                        <div class="downtime-label">⚠ Downtime Aktif</div>
                        <div class="downtime-marquee">
                            <span class="downtime-text" id="downtime-text-<?= $slug ?>"></span>
                        </div>
                    </div>
                    <div class="hourly-section" id="hourly-section-<?= $slug ?>">
                        <div class="hourly-toggle collapsed" data-slug="<?= $slug ?>" onclick="toggleHourly('<?= $slug ?>')">
                            <span>📊 Jam Per Jam</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="hourly-list hidden" id="hourly-list-<?= $slug ?>"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Row 2: 5 card -->
        <div class="realtime-grid realtime-grid-5">
            <?php $i = 0; foreach ($mesinLabels as $slug => $label): if ($i++ < 4) continue; ?>
            <div class="mesin-card idle" id="card-<?= $slug ?>">
                <div class="card-head">
                    <div class="mesin-name">
                        <i class="fas fa-cog gear-icon" id="gear-<?= $slug ?>"></i>
                        <?= esc($label) ?>
                    </div>
                    <span class="status-badge idle" id="badge-<?= $slug ?>">
                        <i class="fas fa-minus-circle"></i> Idle
                    </span>
                </div>
                <div class="card-body-rows" id="body-<?= $slug ?>">
                    <div class="data-row"><span class="data-label">SPK</span><span class="data-value highlight shimmer" id="spk-<?= $slug ?>"></span></div>
                    <div class="data-row"><span class="data-label">Produk</span><span class="data-value shimmer" id="produk-<?= $slug ?>"></span></div>
                    <div class="data-row"><span class="data-label">Brand</span><span class="data-value shimmer" id="brand-<?= $slug ?>"></span></div>
                    <div class="data-row" id="grade-row-<?= $slug ?>" style="display:none;"><span class="data-label">Grade</span><span class="data-value shimmer" id="grade-<?= $slug ?>"></span></div>
                    <div class="data-row" id="warna-row-<?= $slug ?>" style="display:none;"><span class="data-label">Warna</span><span class="data-value shimmer" id="warna-<?= $slug ?>"></span></div>
                    <div class="data-row"><span class="data-label">Hasil Bagus</span><span class="data-value success shimmer" id="bagus-<?= $slug ?>"></span></div>
                    <!-- <div class="data-row"><span class="data-label">Total Reject</span><span class="data-value danger shimmer" id="reject-<?= $slug ?>"></span></div> -->
                    <div class="data-row"><span class="data-label">Sisa SPK</span><span class="data-value shimmer" id="sisapo-<?= $slug ?>"></span></div>
                    <!-- <div class="data-row"><span class="data-label">Total SPK</span><span class="data-value shimmer" id="totalspk-<?= $slug ?>"></span></div> -->
                    <div class="data-row"><span class="data-label">Grup & Shift</span><span class="data-value shimmer" id="operator-<?= $slug ?>"></span></div>
                    <div class="downtime-section" id="downtime-section-<?= $slug ?>">
                        <div class="downtime-label">⚠ Downtime Aktif</div>
                        <div class="downtime-marquee">
                            <span class="downtime-text" id="downtime-text-<?= $slug ?>"></span>
                        </div>
                    </div>
                    <div class="hourly-section" id="hourly-section-<?= $slug ?>">
                        <div class="hourly-toggle collapsed" data-slug="<?= $slug ?>" onclick="toggleHourly('<?= $slug ?>')">
                            <span>📊 Jam Per Jam</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="hourly-list hidden" id="hourly-list-<?= $slug ?>"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div><!-- /.realtime-content -->

</div><!-- /.realtime-shell -->

<!-- Error Toast -->
<div class="realtime-toast" id="errorToast">
    <i class="fas fa-exclamation-triangle"></i>
    <span id="errorMsg">Gagal memperbarui data</span>
</div>

<script>
(function () {
    'use strict';

    const API_URL      = '<?= base_url('api/realtime/produksi-live') ?>';
    const INTERVAL_SEC = 30;

    let countdown     = INTERVAL_SEC;
    let intervalTimer = null;
    let toastTimer    = null;
    const prevValues  = {};

    // ── Helpers ──────────────────────────────────────────────────
    function fmt(n) { return Number(n).toLocaleString('id-ID'); }

    function shiftLabel(s) {
        if (!s || s === '-' || s === '0') return '-';
        if (String(s).startsWith('extra_')) return 'Extra ' + s.replace('extra_', '');
        return 'Shift ' + s;
    }

    function el(id) { return document.getElementById(id); }

    // ── Toast ─────────────────────────────────────────────────────
    function showToast(msg) {
        const toast = el('errorToast');
        el('errorMsg').textContent = msg || 'Gagal memperbarui data';
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 4000);
    }

    // ── Shimmer helpers ──────────────────────────────────────────
    function clearShimmer(id, value, extraClass) {
        const e = el(id);
        if (!e) return;
        e.classList.remove('shimmer');
        if (extraClass) { e.className = 'data-value ' + extraClass; }
        const prev = prevValues[id];
        if (prev !== undefined && prev !== value) {
            e.style.transition = 'color .1s';
            e.style.color = '#f59e0b';
            setTimeout(() => { e.style.color = ''; e.style.transition = ''; }, 600);
        }
        prevValues[id] = value;
        e.textContent = value;
    }

    // ── Update a single card ─────────────────────────────────────
    function updateCard(d) {
        const slug    = d.slug;
        const running = d.status === 'running';
        const problem = d.status === 'problem';

        const card = el('card-' + slug);
        if (!card) return;
        card.className = 'mesin-card ' + d.status;

        const gear = el('gear-' + slug);
        if (gear) {
            gear.style.color = problem ? '#dc2626' : running ? '#16a34a' : '#94a3b8';
        }

        const badge = el('badge-' + slug);
        if (badge) {
            badge.className = 'status-badge ' + d.status;
            if (running) {
                badge.innerHTML = '<span class="pulse-dot"></span> Running';
            } else if (problem) {
                badge.innerHTML = '<span class="pulse-dot problem"></span> Problem';
            } else {
                badge.innerHTML = '<i class="fas fa-minus-circle"></i> Idle';
            }
        }

        if (running || problem) {
            const spkText = d.nomor_spk + (d.selesai_spk ? ' <span class="spk-done">✓ Selesai</span>' : '');
            const spkEl   = el('spk-' + slug);
            if (spkEl) {
                spkEl.classList.remove('shimmer');
                spkEl.className = 'data-value highlight';
                spkEl.innerHTML = spkText;
            }

            clearShimmer('produk-'   + slug, d.nama_produk || '-', '');
            clearShimmer('brand-'    + slug, d.brand || '-', 'muted');
            
            // Show Grade for Becum, Warna for others
            if (slug === 'becum') {
                el('grade-row-' + slug).style.display = 'flex';
                el('warna-row-' + slug).style.display = 'none';
                clearShimmer('grade-' + slug, d.grade || '-', 'muted');
            } else {
                el('grade-row-' + slug).style.display = 'none';
                el('warna-row-' + slug).style.display = 'flex';
                clearShimmer('warna-' + slug, d.warna || '-', 'muted');
            }
            clearShimmer('operator-' + slug, (d.grup || '-') + ' · ' + shiftLabel(d.shift), 'muted');
            
            // Show/hide downtime section
            const downtimeSection = el('downtime-section-' + slug);
            const downtimeText = el('downtime-text-' + slug);
            if (problem && d.alasan_downtime) {
                if (downtimeSection) {
                    downtimeSection.classList.add('active');
                    if (downtimeText) downtimeText.textContent = d.alasan_downtime;
                }
            } else {
                if (downtimeSection) downtimeSection.classList.remove('active');
            }
            clearShimmer('bagus-'    + slug, fmt(d.grand_total_bagus) + ' pcs', 'success');
            clearShimmer('reject-'   + slug, fmt(d.grand_total_reject) + ' pcs', 'danger');
            clearShimmer('sisapo-'   + slug, fmt(d.sisa_po) + ' pcs', '');
            clearShimmer('totalspk-' + slug, fmt(d.total_spk) + ' pcs', '');

            if (problem && d.alasan_downtime) {
                const body = el('body-' + slug);
                if (body && !body.querySelector('[data-downtime-row]')) {
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = '<div class="data-row" data-downtime-row style="background:#fef2f2;padding:.25rem .35rem;border-radius:4px;border-left:3px solid #dc2626;margin:.3rem 0;">' +
                        '<span class="data-label">Alasan</span>' +
                        '<span class="data-value" style="color:#b91c1c;">' + escapeHtml(d.alasan_downtime) + '</span></div>';
                    const opRow = body.querySelector('#operator-' + slug)?.parentElement;
                    if (opRow) opRow.after(tempDiv.firstElementChild);
                }
            }

            updateHourlyData(slug, d.jams || []);
        } else {
            ['spk','produk','brand','grade','warna','operator','bagus','reject','sisapo','totalspk'].forEach(function(key) {
                const e = el(key + '-' + slug);
                if (e) { e.classList.remove('shimmer'); e.textContent = '—'; e.className = 'data-value muted'; }
            });
            const downEl = el('body-' + slug)?.querySelector('[data-downtime-row]');
            if (downEl) downEl.remove();
            const hourlyList = el('hourly-list-' + slug);
            if (hourlyList) { hourlyList.textContent = ''; hourlyList.classList.add('hidden'); }
        }
    }

    // ── Hourly section ────────────────────────────────────────────
    function updateHourlyData(slug, jams) {
        const hourlyList = el('hourly-list-' + slug);
        if (!hourlyList) return;
        hourlyList.textContent = '';
        if (!jams || jams.length === 0) {
            hourlyList.innerHTML = '<div style="text-align:center;color:#94a3b8;font-size:.65rem;padding:.3rem 0;">Belum ada data per jam</div>';
            return;
        }
        jams.forEach(function(jam) {
            const row = document.createElement('div');
            row.className = 'jam-row';
            row.innerHTML = '<span class="jam-label">' + escapeHtml(jam.rentang_jam) + '</span>' +
                            '<span class="jam-value">' + fmt(jam.hasil) + ' pcs</span>';
            hourlyList.appendChild(row);
        });
    }

    window.toggleHourly = function(slug) {
        document.querySelector('[data-slug="' + slug + '"]')?.classList.toggle('collapsed');
        el('hourly-list-' + slug)?.classList.toggle('hidden');
    };

    function escapeHtml(txt) {
        if (!txt) return '';
        const map = { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' };
        return String(txt).replace(/[&<>"']/g, c => map[c]);
    }

    // ── Fetch ─────────────────────────────────────────────────────
    function fetchData() {
        fetch(API_URL)
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(json => {
                if (!json.success) throw new Error('API error');
                json.data.forEach(updateCard);
                const upd = el('lastUpdate');
                if (upd) upd.textContent = json.updated_at || new Date().toLocaleTimeString('id-ID');
            })
            .catch(err => {
                console.warn('[Realtime] fetch error:', err);
                showToast('Gagal memperbarui data — mencoba lagi dalam ' + countdown + 's');
            });
    }

    // ── Countdown ─────────────────────────────────────────────────
    function startCountdown() {
        countdown = INTERVAL_SEC;
        clearInterval(intervalTimer);
        intervalTimer = setInterval(function () {
            countdown--;
            const cdEl = el('countdown');
            if (cdEl) cdEl.textContent = countdown;
            if (countdown <= 0) { countdown = INTERVAL_SEC; fetchData(); }
        }, 1000);
    }

    fetchData();
    startCountdown();

})();
</script>

</body>
</html>
