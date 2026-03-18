<style>
    #chatBadge {
        min-width: 18px;
        height: 18px;
        line-height: 18px;
        padding: 0 5px;
    }

    .active {
        /* background-color: #4e73df; */
        color: white !important;
        border-radius: 5px;
        padding: 5px 10px;
        margin: 0 5px;
        font-weight: bold;
        font-size: 14px;
        text-decoration: none;
        /* text-transform: uppercase; */
        letter-spacing: 1px;
    }

    .active a:hover {
        color: black !important;
    }

    .active a:active {
        color: black !important;
    }

    .active a:focus {
        color: black !important;
    }

    .active a:visited {
        color: black !important;
    }
</style>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- LEFT SIDE -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link font-weight-bold text-dark ml-5 <?= uri_string() == 'home' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= base_url('home') ?>">
                            Home
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link font-weight-bold text-dark<?= uri_string() == 'admin' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= base_url('admin') ?>">
                            Admin
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link font-weight-bold text-primary <?= uri_string() == 'ppic' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= base_url('ppic') ?>">
                            PPIC
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link font-weight-bold text-primary <?= uri_string() == 'produksi' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= base_url('produksi') ?>">
                            Produksi
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link font-weight-bold text-primary <?= uri_string() == 'gudang' ? 'active' : '' ?>">
                        <a class="nav-link" href="<?= base_url('gudang') ?>">
                            Gudang
                        </a>
                    </span>
                </li>
                <li class="nav-item">
                    <span class="nav-link font-weight-bold text-primary <?= uri_string() == 'chat' ? 'active' : '' ?>">
                        <a class="nav-link position-relative" href="<?= base_url('chat') ?>">
                            Pesan
                            <span id="chatBadge" class="badge badge-danger badge-pill position-absolute" style="display:none; top:-mm; right:-13px; font-size:10px;">0</span>
                        </a>
                    </span>
                </li>
                <?php if (session('role') == 'produksi' || session('role') == 'gudang'): ?>
                    <li class="nav-item">
                        <span class="nav-link font-weight-bold text-primary <?= uri_string() == 'notifikasi' ? 'active' : '' ?>">
                            <a href="/notifikasi" class="nav-link position-relative" href="<?= base_url('notifikasi') ?>">
                                Notifikasi
                                <span id="notifBadge" class="badge badge-danger badge-pill position-absolute" style="display:none; top:-mm; right:-13px; font-size:10px;">0</span>
                            </a>
                        </span>
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