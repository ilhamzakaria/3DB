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

    <!-- Dashboard (Manager Only) -->
    <?php if ($role == 'manager'): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'dashboard') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('dashboard') ?>">
                <span>Dashboard</span>
            </a>
        </li>
    <?php endif; ?>

    <!-- Admin (Manager & Admin) -->
    <?php if (in_array($role, ['manager', 'admin'])): ?>
        <li class="nav-item <?= ($uri->getSegment(1) == 'admin') ? 'active' : '' ?>">
            <a class="nav-link" href="<?= base_url('admin') ?>">
                <span>Admin</span>
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