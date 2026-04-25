<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?php
$dashboardMetrics = $dashboard_metrics ?? [];
$materialModules = $material_modules ?? [];
$machineModules = $machine_modules ?? [];
$recentActivities = $recent_activities ?? [];

$formatNumber = static function ($value): string {
    if ($value === null || $value === '') {
        return '-';
    }

    $numeric = (float) $value;
    $isInteger = abs($numeric - round($numeric)) < 0.00001;

    return number_format($numeric, $isInteger ? 0 : 2, ',', '.');
};

$formatTimestamp = static function ($timestamp): string {
    $timestamp = (int) $timestamp;
    return $timestamp > 0 ? date('d M Y H:i', $timestamp) : '-';
};

$summaryCards = [
    [
        'label' => 'Total Modul',
        'value' => ($dashboardMetrics['modules_with_data'] ?? 0) . ' / ' . ($dashboardMetrics['module_total'] ?? 0),
        'note' => 'Modul yang sudah memiliki data',
        'icon' => 'fas fa-layer-group',
        'accent' => 'primary',
    ],
    [
        'label' => 'Total Record',
        'value' => $formatNumber($dashboardMetrics['total_records'] ?? 0),
        'note' => 'Akumulasi seluruh tabel gudang',
        'icon' => 'fas fa-database',
        'accent' => 'success',
    ],
    [
        'label' => 'Total Qty / Stock',
        'value' => $formatNumber($dashboardMetrics['total_quantity'] ?? 0),
        'note' => 'Penjumlahan jumlah dan stock yang tersedia',
        'icon' => 'fas fa-box-open',
        'accent' => 'warning',
    ],
    [
        'label' => 'Update Terakhir',
        'value' => $formatTimestamp($dashboardMetrics['latest_timestamp'] ?? 0),
        'note' => 'Sumber terbaru: ' . ($dashboardMetrics['latest_module'] ?? '-'),
        'icon' => 'fas fa-clock',
        'accent' => 'info',
    ],
];

$quickLinks = array_merge($materialModules, $machineModules);
?>

<style>
    .gudang-dashboard {
        --gudang-surface: linear-gradient(180deg, #f7f9ff 0%, #eef4ff 100%);
        --gudang-ink: #183153;
        --gudang-muted: #6c7a92;
        --gudang-border: rgba(24, 49, 83, 0.1);
        --gudang-shadow: 0 18px 40px rgba(31, 45, 76, 0.08);
    }

    .gudang-dashboard .hero-card,
    .gudang-dashboard .summary-card,
    .gudang-dashboard .module-card,
    .gudang-dashboard .activity-card {
        border: 1px solid var(--gudang-border);
        border-radius: 20px;
        box-shadow: var(--gudang-shadow);
        background: #fff;
    }

    .gudang-dashboard .hero-card {
        padding: 28px;
        background: linear-gradient(135deg, #173b72 0%, #2957a4 48%, #3c78cf 100%);
        color: #fff;
        overflow: hidden;
        position: relative;
    }

    .gudang-dashboard .hero-card::after {
        content: "";
        position: absolute;
        inset: auto -60px -80px auto;
        width: 220px;
        height: 220px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.09);
    }

    .gudang-dashboard .hero-title {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .gudang-dashboard .hero-text {
        max-width: 700px;
        color: rgba(255, 255, 255, 0.84);
        margin-bottom: 0;
    }

    .gudang-dashboard .quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .gudang-dashboard .quick-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .gudang-dashboard .quick-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.18);
        text-decoration: none;
    }

    .gudang-dashboard .summary-card {
        height: 100%;
        padding: 18px 20px;
    }

    .gudang-dashboard .summary-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: var(--gudang-muted);
        font-size: 0.88rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .gudang-dashboard .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
    }

    .gudang-dashboard .summary-value {
        margin-top: 16px;
        font-size: 1.8rem;
        line-height: 1.1;
        font-weight: 700;
        color: var(--gudang-ink);
    }

    .gudang-dashboard .summary-note {
        margin-top: 8px;
        margin-bottom: 0;
        color: var(--gudang-muted);
        font-size: 0.92rem;
    }

    .gudang-dashboard .section-head {
        display: flex;
        flex-wrap: wrap;
        align-items: end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .gudang-dashboard .section-title {
        margin-bottom: 0;
        color: var(--gudang-ink);
        font-size: 1.3rem;
        font-weight: 700;
    }

    .gudang-dashboard .section-subtitle {
        margin-bottom: 0;
        color: var(--gudang-muted);
    }

    .gudang-dashboard .module-card {
        height: 100%;
        padding: 20px;
        background: var(--gudang-surface);
    }

    .gudang-dashboard .module-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .gudang-dashboard .module-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .gudang-dashboard .module-title {
        margin-bottom: 4px;
        color: var(--gudang-ink);
        font-size: 1.05rem;
        font-weight: 700;
    }

    .gudang-dashboard .module-meta {
        color: var(--gudang-muted);
        font-size: 0.9rem;
    }

    .gudang-dashboard .module-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .gudang-dashboard .metric-box {
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(24, 49, 83, 0.08);
    }

    .gudang-dashboard .metric-box small {
        display: block;
        color: var(--gudang-muted);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
    }

    .gudang-dashboard .metric-box strong {
        display: block;
        color: var(--gudang-ink);
        font-size: 1.05rem;
    }

    .gudang-dashboard .module-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: auto;
    }

    .gudang-dashboard .module-link {
        font-weight: 600;
        text-decoration: none;
    }

    .gudang-dashboard .module-empty {
        color: var(--gudang-muted);
        font-size: 0.92rem;
        margin-bottom: 0;
    }

    .gudang-dashboard .activity-card {
        padding: 22px;
    }

    .gudang-dashboard .activity-table {
        margin-bottom: 0;
    }

    .gudang-dashboard .activity-table thead th {
        border-top: 0;
        color: var(--gudang-muted);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .gudang-dashboard .activity-table td {
        vertical-align: middle;
    }

    .gudang-dashboard .badge-soft {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        background: #edf3ff;
        color: #2756a7;
    }

    @media (max-width: 767.98px) {
        .gudang-dashboard .hero-card {
            padding: 22px;
        }

        .gudang-dashboard .hero-title {
            font-size: 1.6rem;
        }

        .gudang-dashboard .module-metrics {
            grid-template-columns: 1fr;
        }

        .gudang-dashboard .module-footer {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="container-fluid gudang-dashboard">
    <div class="hero-card mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start">
            <div class="pr-lg-4">
                <div class="hero-title">Dashboard Gudang</div>
                <!-- <p class="hero-text mt-2">
                    Halaman ini merangkum seluruh data gudang dan monitoring mesin dalam satu tempat:
                    total record, total quantity atau stock, update terbaru, dan akses cepat ke setiap modul detail.
                </p> -->
            </div>
        </div>

        <?php if (!empty($quickLinks)) : ?>
            <div class="quick-links">
                <?php foreach ($quickLinks as $module) : ?>
                    <a class="quick-link" href="<?= base_url($module['route']) ?>">
                        <i class="<?= esc($module['icon']) ?>"></i>
                        <span><?= esc($module['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php foreach ($summaryCards as $card) : ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span><?= esc($card['label']) ?></span>
                        <span class="summary-icon bg-<?= esc($card['accent']) ?>">
                            <i class="<?= esc($card['icon']) ?>"></i>
                        </span>
                    </div>
                    <div class="summary-value"><?= esc($card['value']) ?></div>
                    <p class="summary-note"><?= esc($card['note']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section-head mt-2">
        <div>
            <h2 class="section-title">Ringkasan Material Gudang</h2>
            <p class="section-subtitle">Bahan baku, packaging, masterbatch, reject, stiker, dan item gudang lainnya.</p>
        </div>
    </div>

    <div class="row">
        <?php foreach ($materialModules as $module) : ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="module-card d-flex flex-column">
                    <div class="module-top">
                        <div>
                            <div class="module-title"><?= esc($module['label']) ?></div>
                            <div class="module-meta">
                                <?= !empty($module['available']) ? 'Tabel aktif' : 'Tabel belum tersedia' ?>
                            </div>
                        </div>
                        <div class="module-icon bg-<?= esc($module['accent']) ?>">
                            <i class="<?= esc($module['icon']) ?>"></i>
                        </div>
                    </div>

                    <div class="module-metrics">
                        <div class="metric-box">
                            <small>Total Record</small>
                            <strong><?= esc($formatNumber($module['record_count'] ?? 0)) ?></strong>
                        </div>
                        <div class="metric-box">
                            <small>Total Qty</small>
                            <strong>
                                <?= !empty($module['has_quantity']) ? esc($formatNumber($module['quantity_total'] ?? 0)) : '-' ?>
                            </strong>
                        </div>
                        <div class="metric-box">
                            <small>Item Terakhir</small>
                            <strong><?= esc($module['latest_item'] ?? '-') ?></strong>
                        </div>
                        <div class="metric-box">
                            <small>Update Terakhir</small>
                            <strong><?= esc($formatTimestamp($module['latest_timestamp'] ?? 0)) ?></strong>
                        </div>
                    </div>

                    <div class="module-footer">
                        <p class="module-empty mb-0">
                            SPK: <?= esc($module['latest_spk'] ?? '-') ?> | Shift: <?= esc($module['latest_shift'] ?? '-') ?>
                        </p>
                        <a class="module-link text-<?= esc($module['accent']) ?>" href="<?= base_url($module['route']) ?>">
                            Buka detail <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section-head mt-2">
        <div>
            <h2 class="section-title">Monitoring Mesin</h2>
            <p class="section-subtitle">Pantauan stok hasil mesin Becum, Powerjet, CCM, dan IPS.</p>
        </div>
    </div>

    <div class="row">
        <?php foreach ($machineModules as $module) : ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="module-card d-flex flex-column">
                    <div class="module-top">
                        <div>
                            <div class="module-title"><?= esc($module['label']) ?></div>
                            <div class="module-meta">
                                <?= !empty($module['available']) ? 'Monitoring aktif' : 'Tabel belum tersedia' ?>
                            </div>
                        </div>
                        <div class="module-icon bg-<?= esc($module['accent']) ?>">
                            <i class="<?= esc($module['icon']) ?>"></i>
                        </div>
                    </div>

                    <div class="module-metrics">
                        <div class="metric-box">
                            <small>Total Record</small>
                            <strong><?= esc($formatNumber($module['record_count'] ?? 0)) ?></strong>
                        </div>
                        <div class="metric-box">
                            <small>Total Stock</small>
                            <strong>
                                <?= !empty($module['has_quantity']) ? esc($formatNumber($module['quantity_total'] ?? 0)) : '-' ?>
                            </strong>
                        </div>
                        <div class="metric-box">
                            <small>Produk Terakhir</small>
                            <strong><?= esc($module['latest_item'] ?? '-') ?></strong>
                        </div>
                        <div class="metric-box">
                            <small>Update Terakhir</small>
                            <strong><?= esc($formatTimestamp($module['latest_timestamp'] ?? 0)) ?></strong>
                        </div>
                    </div>

                    <div class="module-footer">
                        <p class="module-empty mb-0">
                            SPK: <?= esc($module['latest_spk'] ?? '-') ?> | Shift: <?= esc($module['latest_shift'] ?? '-') ?>
                        </p>
                        <a class="module-link text-<?= esc($module['accent']) ?>" href="<?= base_url($module['route']) ?>">
                            Buka detail <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="section-head mt-2">
        <div>
            <h2 class="section-title">Aktivitas Terbaru</h2>
            <p class="section-subtitle">Snapshot update terbaru dari seluruh modul gudang.</p>
        </div>
    </div>

    <div class="activity-card mb-4">
        <?php if (!empty($recentActivities)) : ?>
            <div class="table-responsive">
                <table class="table activity-table">
                    <thead>
                        <tr>
                            <th>Modul</th>
                            <th>Item</th>
                            <th>Qty / Stock</th>
                            <th>SPK</th>
                            <th>Shift</th>
                            <th>Waktu</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivities as $activity) : ?>
                            <tr>
                                <td><span class="badge-soft"><?= esc($activity['module_label']) ?></span></td>
                                <td><?= esc($activity['item_label'] ?? '-') ?></td>
                                <td><?= $activity['quantity'] !== null && $activity['quantity'] !== '' ? esc($formatNumber($activity['quantity'])) : '-' ?></td>
                                <td><?= esc($activity['spk'] ?? '-') ?></td>
                                <td><?= esc($activity['shift'] ?? '-') ?></td>
                                <td><?= esc($formatTimestamp($activity['timestamp'] ?? 0)) ?></td>
                                <td class="text-right">
                                    <a href="<?= base_url($activity['route']) ?>" class="btn btn-sm btn-outline-primary">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="text-center py-4 text-muted">
                Belum ada aktivitas yang bisa ditampilkan dari modul gudang.
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>