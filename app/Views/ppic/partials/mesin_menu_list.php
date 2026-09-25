<?php
/**
 * Structured mesin menu with organized list layout
 * Displays latest data per machine in real-time from backend
 * 
 * Replaces machine_links.php with enhanced UX for catatan.md requirements:
 * - Menu Layout: Clean structured list format
 * - Menu Titles: Prominent display per machine
 * - Data Display: Latest data dynamically shown
 * - Data Source: Real-time backend integration
 * - User Experience: Responsive and user-friendly
 * - Code Maintainability: Clean, modular structure
 */

$currentMachine = $current_machine ?? '';
$machines = [
    'mesin_becum'     => ['label' => 'Bekum',      'icon' => 'fas fa-industry',       'color' => 'primary'],
    'mesin_powerjet1' => ['label' => 'Powerjet 1', 'icon' => 'fas fa-bolt',           'color' => 'warning'],
    'mesin_powerjet2' => ['label' => 'Powerjet 2', 'icon' => 'fas fa-bolt',           'color' => 'warning'],
    'mesin_ccm1'      => ['label' => 'CCM 1',      'icon' => 'fas fa-cogs',           'color' => 'info'],
    'mesin_ccm2'      => ['label' => 'CCM 2',      'icon' => 'fas fa-cogs',           'color' => 'info'],
    'mesin_ips1'      => ['label' => 'IPS 1',      'icon' => 'fas fa-tachometer-alt', 'color' => 'success'],
    'mesin_ips2'      => ['label' => 'IPS 2',      'icon' => 'fas fa-tachometer-alt', 'color' => 'success'],
    'mesin_ips3'      => ['label' => 'IPS 3',      'icon' => 'fas fa-tachometer-alt', 'color' => 'success'],
    'mesin_ips4'      => ['label' => 'IPS 4',      'icon' => 'fas fa-tachometer-alt', 'color' => 'success'],
];

$finishGoodSlugs = ['finish_good_galon', 'finish_good_cap_galon', 'finish_good_freeform', 'finish_good_screw_cap'];
$isFinishGoodActive = in_array($currentMachine, $finishGoodSlugs, true);
?>

<style>
/**
 * Mesin Menu List Styles
 * Responsive grid layout with data cards
 */

.mesin-menu-container {
    padding: 1.25rem;
    background: #f8f9fc;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.mesin-menu-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    font-weight: 600;
    font-size: 0.95rem;
    color: #3a3b45;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.mesin-menu-header i {
    font-size: 1.1rem;
    color: #4e73df;
}

/**
 * Mesin Grid - Responsive layout
 */
.mesin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .mesin-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
    }
}

@media (max-width: 480px) {
    .mesin-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
}

/**
 * Mesin Card - Individual machine data display
 */
.mesin-card {
    position: relative;
    background: #fff;
    border: 1px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    user-select: none;
}

.mesin-card:hover {
    background: #f8f9ff;
    border-color: #4e73df;
    border-width: 2px;
    box-shadow: 0 0.5rem 1.5rem rgba(78, 115, 223, 0.25);
    transform: translateY(-3px);
    padding: calc(1rem - 1px) 1rem 1rem 1rem;
}

.mesin-card.active {
    background: linear-gradient(135deg, #f5f7ff 0%, #eaecf4 100%);
    border-color: #4e73df;
    border-width: 2px;
}

.mesin-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.875rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f0f1f6;
}

.mesin-card-icon {
    width: 42px;
    height: 42px;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    background: #f0f1f6;
    color: #4e73df;
    transition: all 0.25s ease;
}

.mesin-card:hover .mesin-card-icon {
    background: #4e73df;
    color: #fff;
    transform: scale(1.1);
}

.mesin-card.active .mesin-card-icon {
    background: #4e73df;
    color: #fff;
}

.mesin-card-title-group h5 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #3a3b45;
    line-height: 1.3;
    transition: color 0.25s ease;
}

.mesin-card:hover .mesin-card-title-group h5 {
    color: #4e73df;
}

.mesin-card-title-group small {
    font-size: 0.75rem;
    color: #858796;
    display: block;
    margin-top: 0.25rem;
    transition: color 0.25s ease;
}

.mesin-card:hover .mesin-card-title-group small {
    color: #4e73df;
}

/**
 * Mesin Card Data Rows
 */
.mesin-data-rows {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
}

.mesin-data-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    gap: 0.5rem;
}

.mesin-data-row-label {
    color: #858796;
    font-weight: 500;
    flex-shrink: 0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.mesin-data-row-value {
    color: #3a3b45;
    font-weight: 600;
    text-align: right;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: color 0.25s ease;
}

.mesin-card:hover .mesin-data-row-value {
    color: #4e73df;
}

.mesin-data-row-value.status-idle {
    color: #858796;
}

.mesin-data-row-value.status-active {
    color: #1cc88a;
    font-weight: 700;
}

.mesin-data-row-value.status-production {
    color: #4e73df;
    font-weight: 700;
}

/**
 * No Data State
 */
.mesin-card.no-data {
    background: #f8f9fc;
    border-color: #e3e6f0;
    opacity: 0.7;
    cursor: default;
}

.mesin-card.no-data:hover {
    border-color: #e3e6f0;
    box-shadow: none;
    transform: none;
}

.no-data-indicator {
    font-size: 0.75rem;
    color: #858796;
    font-style: italic;
    margin-top: 0.5rem;
}

/**
 * Finish Goods Dropdown
 */
.finish-goods-section {
    padding-top: 1rem;
    border-top: 2px solid #e3e6f0;
}

.finish-goods-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #3a3b45;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.finish-goods-label i {
    color: #1cc88a;
}

.finish-goods-menu {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.finish-goods-btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
    border-radius: 0.3rem;
    transition: all 0.2s ease;
}

.finish-goods-btn:hover {
    transform: translateY(-1px);
}

/**
 * Real-time Loading State
 */
.mesin-card.loading {
    pointer-events: none;
    opacity: 0.6;
}

.mesin-card.loading .mesin-data-rows::after {
    content: '';
    display: block;
    height: 4px;
    background: linear-gradient(90deg, #e3e6f0 0%, #4e73df 50%, #e3e6f0 100%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 2px;
    margin-top: 0.5rem;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/**
 * Responsive utilities
 */
@media (max-width: 768px) {
    .mesin-menu-container {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .mesin-card {
        padding: 0.875rem;
    }

    .mesin-card-header {
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
    }

    .mesin-card-icon {
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }

    .mesin-card-title-group h5 {
        font-size: 0.9rem;
    }

    .mesin-data-row {
        font-size: 0.75rem;
    }
}
</style>

<div class="mesin-menu-container">
    <!-- Header -->
    <div class="mesin-menu-header">
        <i class="fas fa-cog"></i>
        <span>Selamat Datang di PPIC</span>
    </div>

    <!-- Mesin Grid -->
    <div class="mesin-grid" id="mesinGrid">
        <?php foreach ($machines as $slug => $machine) : ?>
            <a href="<?= base_url('ppic/' . $slug) ?>" 
               class="mesin-card <?= $currentMachine === $slug ? 'active' : '' ?>" 
               data-mesin="<?= esc($slug) ?>">
                
                <div class="mesin-card-header">
                    <div class="mesin-card-icon">
                        <i class="<?= esc($machine['icon']) ?>"></i>
                    </div>
                    <div class="mesin-card-title-group">
                        <h5><?= esc($machine['label']) ?></h5>
                        <small>Mesin Produksi</small>
                    </div>
                </div>

                <div class="mesin-data-rows" data-mesin-content="<?= esc($slug) ?>">
                    <!-- Data will be populated by JavaScript -->
                    <div class="mesin-data-row">
                        <span class="mesin-data-row-label">SPK</span>
                        <span class="mesin-data-row-value">-</span>
                    </div>
                    <div class="mesin-data-row">
                        <span class="mesin-data-row-label">Produk</span>
                        <span class="mesin-data-row-value">Loading...</span>
                    </div>
                    <div class="mesin-data-row">
                        <span class="mesin-data-row-label">Tanggal</span>
                        <span class="mesin-data-row-value">-</span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Finish Goods Section -->
    <div class="finish-goods-section">
        <div class="finish-goods-label">
            <i class="fas fa-box-open"></i>
            <span>Produk Jadi</span>
        </div>
        <div class="finish-goods-menu">
            <a href="<?= base_url('ppic/finish_good/galon') ?>" 
               class="btn btn-sm finish-goods-btn <?= ($currentMachine === 'finish_good_galon') ? 'btn-success' : 'btn-outline-success' ?>">
                <i class="fas fa-tint"></i> Galon
            </a>
            <a href="<?= base_url('ppic/finish_good/cap_galon') ?>" 
               class="btn btn-sm finish-goods-btn <?= ($currentMachine === 'finish_good_cap_galon') ? 'btn-success' : 'btn-outline-success' ?>">
                <i class="fas fa-circle"></i> Cap Galon
            </a>
            <a href="<?= base_url('ppic/finish_good/freeform') ?>" 
               class="btn btn-sm finish-goods-btn <?= ($currentMachine === 'finish_good_freeform') ? 'btn-success' : 'btn-outline-success' ?>">
                <i class="fas fa-shapes"></i> Freeform
            </a>
            <a href="<?= base_url('ppic/finish_good/screw_cap') ?>" 
               class="btn btn-sm finish-goods-btn <?= ($currentMachine === 'finish_good_screw_cap') ? 'btn-success' : 'btn-outline-success' ?>">
                <i class="fas fa-bottle-water"></i> Screw Cap
            </a>
        </div>
    </div>
</div>

<script>
/**
 * Real-time Mesin Data Loading
 * Fetches latest data per machine and updates display cards
 */
(function() {
    'use strict';

    const MESIN_DATA_API = '<?= base_url('api/mesin-data/latest') ?>';
    const REFRESH_INTERVAL = 30000; // 30 seconds
    let refreshTimer = null;

    /**
     * Format date and time for display
     */
    function formatDate(dateStr) {
        if (!dateStr || dateStr === '-') return '-';
        try {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
        } catch (e) {
            return dateStr;
        }
    }

    /**
     * Determine status badge class
     */
    function getStatusClass(status) {
        if (status === 'idle' || status === 'unknown' || !status) return 'status-idle';
        if (status === 'production' || status === 'menggiling' || status === 'active') return 'status-production';
        return 'status-active';
    }

    /**
     * Update a single mesin card with data
     */
    function updateMesinCard(slug, data) {
        const card = document.querySelector(`[data-mesin="${slug}"]`);
        if (!card) return;

        const container = card.querySelector(`[data-mesin-content="${slug}"]`);
        if (!container) return;

        // Remove loading state
        card.classList.remove('loading');

        // Update card content based on data availability
        if (data && data.product !== 'No data') {
            container.innerHTML = `
                <div class="mesin-data-row">
                    <span class="mesin-data-row-label">SPK</span>
                    <span class="mesin-data-row-value" title="${data.spk || '-'}">${data.spk || '-'}</span>
                </div>
                <div class="mesin-data-row">
                    <span class="mesin-data-row-label">Produk</span>
                    <span class="mesin-data-row-value" title="${data.product || 'N/A'}">${data.product || 'N/A'}</span>
                </div>
                <div class="mesin-data-row">
                    <span class="mesin-data-row-label">Tanggal</span>
                    <span class="mesin-data-row-value">${formatDate(data.date)}</span>
                </div>
                <div class="mesin-data-row">
                    <span class="mesin-data-row-label">Grade</span>
                    <span class="mesin-data-row-value">${data.grade || '-'}</span>
                </div>
            `;
            card.classList.remove('no-data');
        } else {
            // No data state
            container.innerHTML = `
                <div class="mesin-data-row">
                    <span class="mesin-data-row-label">Status</span>
                    <span class="mesin-data-row-value status-idle">Belum ada data</span>
                </div>
                <div class="no-data-indicator">
                    <i class="fas fa-info-circle mr-1"></i> Tunggu input dari produksi
                </div>
            `;
            card.classList.add('no-data');
        }
    }

    /**
     * Load mesin data from API
     */
    function loadMesinData() {
        fetch(MESIN_DATA_API)
            .then(response => {
                if (!response.ok) throw new Error('API error: ' + response.status);
                return response.json();
            })
            .then(json => {
                if (json.status === 'success' && json.data) {
                    Object.entries(json.data).forEach(([slug, data]) => {
                        updateMesinCard(slug, data);
                    });
                }
            })
            .catch(err => {
                console.warn('Failed to load mesin data:', err);
                // Cards will show default content, no error display needed
            });
    }

    /**
     * Start auto-refresh timer
     */
    function startAutoRefresh() {
        if (refreshTimer) clearInterval(refreshTimer);
        refreshTimer = setInterval(loadMesinData, REFRESH_INTERVAL);
    }

    /**
     * Stop auto-refresh timer
     */
    function stopAutoRefresh() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
            refreshTimer = null;
        }
    }

    /**
     * Initialize on page load
     */
    function init() {
        // Load initial data
        loadMesinData();
        
        // Start auto-refresh
        startAutoRefresh();

        // Stop refresh when page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                loadMesinData();
                startAutoRefresh();
            }
        });

        // Manual refresh button (optional)
        window.refreshMesinData = loadMesinData;
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Stop refresh on page unload
    window.addEventListener('beforeunload', stopAutoRefresh);
})();
</script>
