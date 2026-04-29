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
        border-radius: 12px;
        padding: 8px 16px !important;
        margin: 0 2px;
        font-weight: 600;
        font-size: 14px;
        color: #4a5568 !important;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .topbar-menu-link:hover {
        /* background-color: rgba(67, 97, 238, 0.05); */
        color: #4361ee !important;
    }

    .topbar-menu-link.active {
        color: #4361ee !important;
        /* background-color: rgba(67, 97, 238, 0.08); */
    }

    .profile-pill {
        display: flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        border-radius: 50px;
        background: transparent;
        transition: all 0.2s ease;
        text-decoration: none !important;
    }

    .profile-pill:hover {
        background: rgba(0,0,0,0.05);
    }
</style>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm border-bottom" style="height: 70px;">
            <div class="container-fluid px-4 px-md-5">
                <!-- LEFT SIDE -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= uri_string() == 'home' ? 'active' : '' ?>" href="<?= base_url('home') ?>">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= uri_string() == 'ppic' ? 'active' : '' ?>" href="<?= base_url('ppic') ?>">
                            PPIC
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= uri_string() == 'produksi' ? 'active' : '' ?>" href="<?= base_url('produksi') ?>">
                            Produksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= (uri_string() == 'gudang' || str_starts_with(uri_string(), 'gudang/')) ? 'active' : '' ?>" href="<?= base_url('gudang') ?>">
                            Gudang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link position-relative <?= uri_string() == 'pesan' ? 'active' : '' ?>" href="<?= base_url('pesan') ?>">
                            Pesan
                            <span id="chatBadge" class="badge badge-danger badge-pill position-absolute" style="display:none; font-size:10px; top: 0; right: 0;">0</span>
                        </a>
                    </li>
                    <?php if (session('role') == 'produksi' || session('role') == 'gudang'): ?>
                        <li class="nav-item">
                            <a class="nav-link topbar-menu-link position-relative <?= uri_string() == 'notifikasi' ? 'active' : '' ?>" href="<?= base_url('notifikasi') ?>">
                                Notifikasi
                                <span id="notifBadge" class="badge badge-danger badge-pill position-absolute" style="display:none; font-size:10px; top: 0; right: 0;">0</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <!-- RIGHT SIDE -->
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle profile-pill" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" data-bs-toggle="dropdown">
                            <img class="img-profile rounded-circle"
                                src="<?= base_url('assets/img/profile.svg') ?>" 
                                style="width: 36px; height: 36px; object-fit: cover; border: 2px solid #f8f9fc; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-right: 12px;">
                            <span class="text-gray-800 small font-weight-bold mr-2 d-none d-lg-inline">
                                Halo, <?= session('username'); ?>
                            </span>
                            <i class="fas fa-chevron-down text-gray-400" style="font-size: 0.7rem;"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-end shadow border-0 animated--grow-in" style="border-radius: 15px; margin-top: 10px;">
                            <div class="dropdown-header text-xs text-uppercase font-weight-bold text-muted">User Menu</div>
                            <a class="dropdown-item py-2" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item py-2 text-danger" href="<?= base_url('logout') ?>">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger opacity-50"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
