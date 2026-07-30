<?php
$currentMachine = $current_machine ?? '';
$machines = [
    'mesin_becum'     => ['label' => 'Becum',      'icon' => 'fas fa-industry',       'url' => 'ppic/mesin_becum'],
    'mesin_powerjet1' => ['label' => 'Powerjet 1', 'icon' => 'fas fa-bolt',           'url' => 'ppic/mesin_powerjet1'],
    'mesin_powerjet2' => ['label' => 'Powerjet 2', 'icon' => 'fas fa-bolt',           'url' => 'ppic/mesin_powerjet2'],
    'mesin_ccm1'      => ['label' => 'CCM 1',      'icon' => 'fas fa-cogs',           'url' => 'ppic/mesin_ccm1'],
    'mesin_ccm2'      => ['label' => 'CCM 2',      'icon' => 'fas fa-cogs',           'url' => 'ppic/mesin_ccm2'],
    'mesin_ips1'      => ['label' => 'IPS 1',      'icon' => 'fas fa-tachometer-alt', 'url' => 'ppic/mesin_ips1'],
    'mesin_ips2'      => ['label' => 'IPS 2',      'icon' => 'fas fa-tachometer-alt', 'url' => 'ppic/mesin_ips2'],
    'mesin_ips3'      => ['label' => 'IPS 3',      'icon' => 'fas fa-tachometer-alt', 'url' => 'ppic/mesin_ips3'],
    'mesin_ips4'      => ['label' => 'IPS 4',      'icon' => 'fas fa-tachometer-alt', 'url' => 'ppic/mesin_ips4'],
];

$finishGoodSlugs = ['finish_good_galon', 'finish_good_cap_galon', 'finish_good_freeform', 'finish_good_screw_cap'];
$isFinishGoodActive = in_array($currentMachine, $finishGoodSlugs, true);
?>

<style>
.fg-dropdown-wrap { position: relative; display: inline-block; }
.fg-dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1050;
    min-width: 170px;
    background: #fff;
    border: 1px solid #d1d3e2;
    border-radius: .35rem;
    box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
    padding: .25rem 0;
    margin-top: 2px;
}
.fg-dropdown-menu.show { display: block; }
.fg-dropdown-menu a {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .45rem 1rem;
    color: #3a3b45;
    font-size: .82rem;
    text-decoration: none;
    white-space: nowrap;
}
.fg-dropdown-menu a:hover { background: #f8f9fc; color: #4e73df; }
.fg-dropdown-menu a.active { background: #eaecf4; color: #4e73df; font-weight: 600; }
.machine-btn-group { display: flex; flex-wrap: wrap; align-items: center; gap: .35rem; }
</style>

<div class="machine-btn-group mb-3">
    <span class="small text-muted font-weight-bold d-flex align-items-center mr-1">
        <i class="fas fa-cog mr-1"></i> LOG MESIN:
    </span>

    <?php foreach ($machines as $slug => $machine) : ?>
        <a href="<?= base_url($machine['url']) ?>"
           class="machine-link-btn btn btn-sm <?= $currentMachine === $slug ? 'btn-primary' : 'btn-outline-primary' ?>">
            <i class="<?= esc($machine['icon']) ?>"></i> <?= esc($machine['label']) ?>
        </a>
    <?php endforeach; ?>

    <!-- Finish Goods dropdown -->
    <div class="fg-dropdown-wrap">
        <button type="button" id="fgDropdownBtn"
            class="btn btn-sm <?= $isFinishGoodActive ? 'btn-success' : 'btn-outline-success' ?>"
            onclick="toggleFgDropdown(event)">
            <i class="fas fa-box-open"></i> Finish Goods
            <i class="fas fa-caret-down ml-1"></i>
        </button>
        <div class="fg-dropdown-menu" id="fgDropdownMenu">
            <a href="<?= base_url('ppic/finish_good/galon') ?>"
               class="<?= $currentMachine === 'finish_good_galon' ? 'active' : '' ?>">
                <i class="fas fa-tint text-primary"></i> Galon
            </a>
            <a href="<?= base_url('ppic/finish_good/cap_galon') ?>"
               class="<?= $currentMachine === 'finish_good_cap_galon' ? 'active' : '' ?>">
                <i class="fas fa-circle text-warning"></i> Cap Galon
            </a>
            <a href="<?= base_url('ppic/finish_good/freeform') ?>"
               class="<?= $currentMachine === 'finish_good_freeform' ? 'active' : '' ?>">
                <i class="fas fa-shapes text-info"></i> Freeform
            </a>
            <a href="<?= base_url('ppic/finish_good/screw_cap') ?>"
               class="<?= $currentMachine === 'finish_good_screw_cap' ? 'active' : '' ?>">
                <i class="fas fa-bottle-water text-secondary"></i> Screw Cap
            </a>
        </div>
    </div>
</div>

<script>
function toggleFgDropdown(event) {
    event.stopPropagation();
    const menu = document.getElementById('fgDropdownMenu');
    menu.classList.toggle('show');
}
// Close dropdown when clicking outside
document.addEventListener('click', function() {
    const menu = document.getElementById('fgDropdownMenu');
    if (menu) menu.classList.remove('show');
});

if (typeof window.ppicAjaxInitialized === 'undefined') {
    window.ppicAjaxInitialized = true;
    window.loadAjaxPage = function(url, pushState = true) {
        window.location.href = url;
    };
}
</script>
