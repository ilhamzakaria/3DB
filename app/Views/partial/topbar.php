<style>
    #chatBadge {
        min-width: 18px;
        height: 18px;
        line-height: 18px;
        padding: 0 5px;
    }

    .topbar-menu-link {
        display: flex;
        align-items: center;
        border-radius: 5px;
        padding: 6px 10px !important;
        margin: 0 4px;
        font-weight: bold;
        font-size: 14px;
        letter-spacing: 0.3px;
        color: #5a5c69 !important;
        text-decoration: none !important;
    }

    .topbar-menu-link:hover,
    .topbar-menu-link:focus {
        color: #2e3340 !important;
        text-decoration: none !important;
    }

    .topbar-menu-link.active {
        color: #2f59d9 !important;
    }

    .topbar-menu-link .badge {
        top: 2px !important;
        right: -8px !important;
    }
</style>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- LEFT SIDE -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link topbar-menu-link ml-5 <?= uri_string() == 'home' ? 'active' : '' ?>" href="<?= base_url('home') ?>">
                        Home
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link topbar-menu-link <?= uri_string() == 'admin' ? 'active' : '' ?>" href="<?= base_url('admin') ?>">
                        Admin
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link topbar-menu-link <?= uri_string() == 'ppic' ? 'active' : '' ?>" href="<?= base_url('ppic') ?>">
                        PPIC
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link topbar-menu-link <?= uri_string() == 'plant_produksi' ? 'active' : '' ?>" href="<?= base_url('plant_produksi') ?>">
                        Produksi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link topbar-menu-link <?= (uri_string() == 'gudang' || str_starts_with(uri_string(), 'gudang/')) ? 'active' : '' ?>" href="<?= base_url('gudang') ?>">
                        Gudang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link topbar-menu-link position-relative <?= uri_string() == 'chat' ? 'active' : '' ?>" href="<?= base_url('chat') ?>">
                        Pesan
                        <span id="chatBadge" class="badge badge-danger badge-pill position-absolute" style="display:none; font-size:10px;">0</span>
                    </a>
                </li>
                <?php if (session('role') == 'produksi' || session('role') == 'gudang'): ?>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link position-relative <?= uri_string() == 'notifikasi' ? 'active' : '' ?>" href="<?= base_url('notifikasi') ?>">
                            Notifikasi
                            <span id="notifBadge" class="badge badge-danger badge-pill position-absolute" style="display:none; font-size:10px;">0</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- RIGHT SIDE -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown">



                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            Halo! <?= session('username'); ?>
                            <small class="text-primary">
                                (<?= ucfirst(session('role')); ?>)
                            </small>
                        </span>

                        <img class="img-profile rounded-circle"
                            src="<?= base_url('assets/img/profile.svg') ?>">
                    </a>

                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>

        </nav>
