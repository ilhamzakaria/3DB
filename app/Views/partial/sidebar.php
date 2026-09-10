<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">

        </div>
        <div class="sidebar-brand-text mx-3">PT.OROPLASTINDO</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <?php
    $uri = service('uri');
    $role = session()->get('role');
    ?>


    <!-- Admin (Manager & Admin) -->
    <?php if (in_array($role, ['manager', 'admin'])): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'admin') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin') ?>">
                <span>Admin</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- PPIC -->
    <?php if (in_array($role, ['manager', 'admin', 'ppic', 'operator', 'leader_plant1', 'leader_plant2'])): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'ppic') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('ppic') ?>">
                <i class="fas fa-fw fa-tasks"></i>
                <span>PPIC</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Produksi -->
    <?php if (in_array($role, ['manager', 'admin', 'produksi', 'ppic', 'operator', 'operator_produksi', 'operator_produksi1', 'operator_produksi2'])): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'produksi') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('produksi') ?>">
                <i class="fas fa-fw fa-industry"></i>
                <span>Produksi</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Gudang (Status Feature) -->
    <?php if (in_array($role, ['manager', 'admin', 'gudang'])): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'gudang') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('gudang') ?>">
                <i class="fas fa-fw fa-warehouse"></i>
                <span>Gudang</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Warehouse 2 -->
    <?php if (in_array($role, ['manager', 'admin', 'gudang', 'operator', 'leader_plant1', 'leader_plant2'])): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'warehouse2') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('warehouse2') ?>">
                <i class="fas fa-fw fa-cube"></i>
                <span>Warehouse 2</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Operator (Semua Role) -->
    <!-- <?php if (in_array($role, ['manager', 'admin', 'operator'])): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'operator') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('operator') ?>">
                <span>Operator</span>
            </a>
        </li>
    <?php endif; ?> -->

    <!-- Logout -->


    <!-- Divider (garis)-->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>