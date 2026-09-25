<?php
/**
 * Produksi — Machine navigation buttons (matching PPIC layout)
 * Horizontal button group showing all 9 mesin as clickable tabs
 */

$currentMachine = $filter_mesin ?? '';
$machines = [
    'becum'     => ['label' => 'Bekum',      'icon' => 'fas fa-industry',       'url' => 'produksi/mesin_becum'],
    'powerjet1' => ['label' => 'Powerjet 1', 'icon' => 'fas fa-bolt',           'url' => 'produksi/mesin_powerjet1'],
    'powerjet2' => ['label' => 'Powerjet 2', 'icon' => 'fas fa-bolt',           'url' => 'produksi/mesin_powerjet2'],
    'ccm1'      => ['label' => 'CCM 1',      'icon' => 'fas fa-cogs',           'url' => 'produksi/mesin_ccm1'],
    'ccm2'      => ['label' => 'CCM 2',      'icon' => 'fas fa-cogs',           'url' => 'produksi/mesin_ccm2'],
    'ips1'      => ['label' => 'IPS 1',      'icon' => 'fas fa-tachometer-alt', 'url' => 'produksi/mesin_ips1'],
    'ips2'      => ['label' => 'IPS 2',      'icon' => 'fas fa-tachometer-alt', 'url' => 'produksi/mesin_ips2'],
    'ips3'      => ['label' => 'IPS 3',      'icon' => 'fas fa-tachometer-alt', 'url' => 'produksi/mesin_ips3'],
    'ips4'      => ['label' => 'IPS 4',      'icon' => 'fas fa-tachometer-alt', 'url' => 'produksi/mesin_ips4'],
];
?>

<style>
.machine-btn-group { 
    display: flex; 
    flex-wrap: wrap; 
    align-items: center; 
    gap: .35rem;
    margin-bottom: 1rem;
}

.machine-link-btn {
    font-size: .82rem;
    padding: .45rem .75rem;
    white-space: nowrap;
    border-radius: .35rem;
}

.machine-link-btn i {
    margin-right: .3rem;
}

.machine-link-btn.btn-primary {
    background-color: #4361ee;
    border-color: #4361ee;
    color: #fff;
}

.machine-link-btn.btn-primary:hover {
    background-color: #364dc9;
    border-color: #364dc9;
}

.machine-link-btn.btn-outline-primary {
    border-color: #4361ee;
    color: #4361ee;
}

.machine-link-btn.btn-outline-primary:hover {
    background-color: #f5f7fe;
    border-color: #4361ee;
    color: #4361ee;
}
</style>

<div class="machine-links-wrapper">
<div class="machine-btn-group">
    <span class="small text-muted font-weight-bold d-flex align-items-center mr-1">
        <i class="fas fa-cog mr-1"></i> LOG MESIN:
    </span>

    <?php foreach ($machines as $slug => $machine): ?>
        <?php
        // Sembunyikan mesin yang bukan milik plant role ini
        $currentRole  = session('role');
        $allowedPlant = \App\Controllers\Produksi::getPlantMesin($currentRole);
        if ($allowedPlant !== null && !in_array($slug, $allowedPlant, true)) continue;
        ?>
        <a href="<?= base_url($machine['url']) ?>"
           class="machine-link-btn btn btn-sm <?= $currentMachine === $slug ? 'btn-primary' : 'btn-outline-primary' ?>">
            <i class="<?= esc($machine['icon']) ?>"></i> <?= esc($machine['label']) ?>
        </a>
    <?php endforeach; ?>
</div>
</div>
