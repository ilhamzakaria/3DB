<?php
/**
 * Produksi — LOG MESIN navigation cards
 * Clicking a card filters the table to that machine (?mesin=slug).
 * Active card is highlighted based on $filter_mesin passed from controller.
 */

$activeMesin = $filter_mesin ?? '';

$machines = [
    'becum'     => ['label' => 'Becum',      'icon' => 'fas fa-industry',       'color' => '#4e73df'],
    'powerjet1' => ['label' => 'Powerjet 1', 'icon' => 'fas fa-bolt',           'color' => '#f6c23e'],
    'powerjet2' => ['label' => 'Powerjet 2', 'icon' => 'fas fa-bolt',           'color' => '#f6c23e'],
    'ccm1'      => ['label' => 'CCM 1',      'icon' => 'fas fa-cogs',           'color' => '#36b9cc'],
    'ccm2'      => ['label' => 'CCM 2',      'icon' => 'fas fa-cogs',           'color' => '#36b9cc'],
    'ips1'      => ['label' => 'IPS 1',      'icon' => 'fas fa-tachometer-alt', 'color' => '#1cc88a'],
    'ips2'      => ['label' => 'IPS 2',      'icon' => 'fas fa-tachometer-alt', 'color' => '#1cc88a'],
    'ips3'      => ['label' => 'IPS 3',      'icon' => 'fas fa-tachometer-alt', 'color' => '#1cc88a'],
    'ips4'      => ['label' => 'IPS 4',      'icon' => 'fas fa-tachometer-alt', 'color' => '#1cc88a'],
];

// Whether the cards should link to dedicated mesin routes (/produksi/mesin_{slug})
// or fall back to ?mesin= query params (only used on the main /produksi index).
// When $base_url_mesin is passed (from mesin.php view) we use route-based links.
$useRoutes = !empty($base_url_mesin);

// Params that survive across nav (strip mesin/page — not needed on route-based links)
$currentParams = array_filter([
    'q'      => $q ?? '',
    'filter' => $filter ?? '',
    'shift'  => $filter_shift ?? '',
    'tanggal'=> $filter_tanggal ?? '',
]);
?>

<style>
/* ── Produksi Mesin Menu ─────────────────────────────────────── */
.pm-wrap {
    padding: 1.25rem 1.25rem 1rem;
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.pm-section-label {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .75rem;
    font-weight: 700;
    color: #858796;
    text-transform: uppercase;
    letter-spacing: .7px;
    margin-bottom: .875rem;
}
.pm-section-label i { color: #4e73df; font-size: .85rem; }

/* Grid */
.pm-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: .6rem;
    margin-bottom: 1rem;
}
@media (max-width: 1199px) { .pm-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px)  { .pm-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { .pm-grid { grid-template-columns: 1fr; } }

/* Card — base */
.pm-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1.5px solid #e3e6f0;
    border-radius: .5rem;
    padding: .8rem .9rem;
    text-decoration: none;
    color: inherit;
    transition: border-color .18s, box-shadow .18s, transform .18s, background .18s;
    cursor: pointer;
    user-select: none;
    position: relative;
    overflow: hidden;
}
.pm-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--pm-color, #4e73df);
    opacity: 0;
    transition: opacity .18s;
}
.pm-card:hover {
    border-color: var(--pm-color, #4e73df);
    box-shadow: 0 4px 16px rgba(0,0,0,.09);
    transform: translateY(-2px);
    text-decoration: none;
    color: inherit;
}
.pm-card:hover::before { opacity: 1; }

/* Active state */
.pm-card.pm-active {
    border-color: var(--pm-color, #4e73df);
    background: #fff;
    box-shadow: 0 4px 18px rgba(0,0,0,.1);
}
.pm-card.pm-active::before { opacity: 1; }

/* Card header row */
.pm-card-head {
    display: flex;
    align-items: center;
    gap: .55rem;
    margin-bottom: .6rem;
    padding-bottom: .55rem;
    border-bottom: 1px solid #f0f1f7;
}
.pm-card-icon {
    width: 34px;
    height: 34px;
    border-radius: .35rem;
    background: #eaecf4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    color: var(--pm-color, #4e73df);
    transition: background .18s, color .18s;
}
.pm-card:hover .pm-card-icon,
.pm-card.pm-active .pm-card-icon {
    background: var(--pm-color, #4e73df);
    color: #fff;
}
.pm-card-name {
    font-size: .85rem;
    font-weight: 700;
    color: #3a3b45;
    margin: 0;
    line-height: 1.2;
}
.pm-card-sub {
    font-size: .7rem;
    color: #adb5bd;
    display: block;
}

/* Data rows */
.pm-rows { display: flex; flex-direction: column; gap: .3rem; }
.pm-row  {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    font-size: .75rem;
    gap: .3rem;
}
.pm-lbl {
    color: #adb5bd;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
    flex-shrink: 0;
}
.pm-val {
    color: #3a3b45;
    font-weight: 700;
    text-align: right;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.pm-val.pm-idle { color: #ced4da; font-style: italic; font-weight: 400; }

/* Active label pill (top-right of card) */
.pm-active-pill {
    position: absolute;
    top: .45rem; right: .55rem;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    background: var(--pm-color, #4e73df);
    color: #fff;
    border-radius: 999px;
    padding: .15rem .5rem;
}

/* Loading shimmer */
.pm-card.pm-loading .pm-rows::after {
    content: '';
    display: block;
    height: 3px;
    margin-top: .35rem;
    border-radius: 2px;
    background: linear-gradient(90deg, #e9ecef 0%, #c5cbe6 50%, #e9ecef 100%);
    background-size: 200% 100%;
    animation: pmShimmer 1.3s infinite;
}
@keyframes pmShimmer {
    0%   { background-position: -200% 0; }
    100% { background-position:  200% 0; }
}

/* Produk Jadi row */
.pm-footer {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .5rem;
    padding-top: .875rem;
    border-top: 1px solid #e9ecef;
}
.pm-footer-label {
    font-size: .75rem;
    font-weight: 700;
    color: #858796;
    text-transform: uppercase;
    letter-spacing: .5px;
    display: flex;
    align-items: center;
    gap: .35rem;
    flex-shrink: 0;
    margin-right: .25rem;
}
.pm-footer-label i { color: #1cc88a; }
</style>

<div class="pm-wrap">

    <!-- Section title -->
    <div class="pm-section-label">
        <i class="fas fa-cog"></i>
        <span>LOG MESIN</span>
        <?php if ($activeMesin): ?>
            <span class="ms-1 text-primary" style="font-size:.7rem;">
                — menampilkan <strong><?= esc($mesin_labels[$activeMesin] ?? $activeMesin) ?></strong>
                &nbsp;<a href="<?= base_url('produksi' . ($currentParams ? '?' . http_build_query($currentParams) : '')) ?>"
                         class="text-danger" style="text-decoration:none;" title="Hapus filter mesin">
                    <i class="fas fa-times-circle"></i>
                </a>
            </span>
        <?php endif; ?>
    </div>

    <!-- Mesin cards -->
    <div class="pm-grid" id="pmGrid">
        <?php foreach ($machines as $slug => $m):
            $isActive = ($activeMesin === $slug);

            // Route-based link (/produksi/mesin_{slug}) when on a mesin sub-page;
            // query-param link (?mesin=slug) when on the main /produksi index.
            if ($useRoutes) {
                $href = base_url('produksi/mesin_' . $slug)
                      . ($currentParams ? '?' . http_build_query($currentParams) : '');
            } else {
                $cardParams = array_merge($currentParams, ['mesin' => $slug]);
                $href = base_url('produksi?' . http_build_query($cardParams));
            }
        ?>
        <a href="<?= $href ?>"
           class="pm-card pm-loading <?= $isActive ? 'pm-active' : '' ?>"
           data-mesin="mesin_<?= esc($slug) ?>"
           style="--pm-color: <?= esc($m['color']) ?>;">

            <?php if ($isActive): ?>
                <span class="pm-active-pill">Aktif</span>
            <?php endif; ?>

            <div class="pm-card-head">
                <div class="pm-card-icon">
                    <i class="<?= esc($m['icon']) ?>"></i>
                </div>
                <div>
                    <p class="pm-card-name"><?= esc($m['label']) ?></p>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
(function () {
    'use strict';

    var API   = '<?= base_url('api/mesin-data/latest') ?>';
    var TICK  = 30000;
    var timer = null;

    function fmt(s) {
        if (!s || s === '-') return '-';
        try { return new Date(s).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }); }
        catch (e) { return s; }
    }

    function renderCard(slug, d) {
        // slug from API is e.g. "mesin_becum"; data-content uses same format
        var box = document.querySelector('[data-content="' + slug + '"]');
        if (!box) return;
        var card = box.closest('.pm-card');
        if (card) card.classList.remove('pm-loading');

        if (d && d.product && d.product !== 'No data') {
            box.innerHTML =
                row('SPK',     escHtml(d.spk     || '-')) +
                row('Produk',  escHtml(d.product || 'N/A')) +
                row('Tanggal', fmt(d.date)) +
                row('Grade',   escHtml(d.grade   || '-'));
        } else {
            box.innerHTML =
                '<div class="pm-row"><span class="pm-lbl">Status</span>' +
                '<span class="pm-val pm-idle">Belum ada data</span></div>';
        }
    }

    function row(lbl, val) {
        return '<div class="pm-row">' +
            '<span class="pm-lbl">' + lbl + '</span>' +
            '<span class="pm-val" title="' + val + '">' + val + '</span>' +
        '</div>';
    }

    function escHtml(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function load() {
        fetch(API)
            .then(function (r) { return r.ok ? r.json() : Promise.reject(r.status); })
            .then(function (json) {
                if (json.status === 'success' && json.data) {
                    Object.keys(json.data).forEach(function (slug) {
                        renderCard(slug, json.data[slug]);
                    });
                }
            })
            .catch(function () {
                document.querySelectorAll('.pm-card.pm-loading').forEach(function (c) {
                    c.classList.remove('pm-loading');
                });
            });
    }

    function start() { stop(); timer = setInterval(load, TICK); }
    function stop()  { if (timer) { clearInterval(timer); timer = null; } }

    var btn = document.getElementById('btnPmRefresh');
    if (btn) btn.addEventListener('click', function () {
        var ic = this.querySelector('i');
        if (ic) ic.classList.add('fa-spin');
        load();
        setTimeout(function () { if (ic) ic.classList.remove('fa-spin'); }, 900);
    });

    document.addEventListener('visibilitychange', function () {
        document.hidden ? stop() : (load(), start());
    });
    window.addEventListener('beforeunload', stop);

    load();
    start();
}());
</script>
