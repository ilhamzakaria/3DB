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
        color: #4361ee !important;
    }

    .topbar-menu-link.active {
        color: #4361ee !important;
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

    /* ── TASK 4: Mobile sidebar CSS ─────────────────────────── */

    /* Hamburger button — mobile only */
    .mobile-hamburger {
        display: none;
        background: none;
        border: none;
        font-size: 1.3rem;
        color: #4a5568;
        padding: 0.4rem 0.6rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.2s;
        line-height: 1;
    }

    .mobile-hamburger:hover { background: #f4f7fe; }

    /* Overlay gelap di belakang drawer */
    .mobile-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1040;
    }

    .mobile-overlay.active { display: block; }

    /* Drawer sidebar */
    .mobile-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 78vw;
        max-width: 300px;
        height: 100vh;
        background: #fff;
        z-index: 1045;
        transform: translateX(-100%);
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.12);
        overflow: hidden;
    }

    .mobile-sidebar.open { transform: translateX(0); }

    /* Header drawer */
    .mobile-sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #f0f2f5;
        background: #4361ee;
        color: #fff;
        flex-shrink: 0;
    }

    .mobile-sidebar-header span { font-size: 0.95rem; letter-spacing: 0.02em; }

    .mobile-sidebar-close {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.1rem;
        cursor: pointer;
        padding: 0.25rem 0.4rem;
        border-radius: 6px;
        opacity: 0.8;
    }

    .mobile-sidebar-close:hover { opacity: 1; background: rgba(255, 255, 255, 0.15); }

    /* Nav links */
    .mobile-sidebar-nav {
        flex: 1;
        overflow-y: auto;
        padding: 0.75rem 0;
    }

    .mobile-nav-link {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.85rem 1.25rem;
        color: #4a5568;
        text-decoration: none !important;
        font-size: 0.95rem;
        font-weight: 500;
        transition: background 0.15s, color 0.15s;
        position: relative;
    }

    .mobile-nav-link i {
        width: 20px;
        text-align: center;
        color: #a3aed1;
        font-size: 1rem;
        flex-shrink: 0;
        transition: color 0.15s;
    }

    .mobile-nav-link:hover { background: #f4f7fe; color: #4361ee; }
    .mobile-nav-link:hover i { color: #4361ee; }

    .mobile-nav-link.active {
        background: rgba(67, 97, 238, 0.08);
        color: #4361ee;
        font-weight: 600;
        border-right: 3px solid #4361ee;
    }

    .mobile-nav-link.active i { color: #4361ee; }

    .mobile-nav-link.nav-danger { color: #ef476f !important; }
    .mobile-nav-link.nav-danger i { color: #ef476f !important; }

    /* Badge di dalam nav link */
    .mobile-badge {
        margin-left: auto;
        background: #ef476f;
        color: #fff;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.15rem 0.45rem;
        min-width: 20px;
        text-align: center;
    }

    /* Divider */
    .mobile-nav-divider {
        height: 1px;
        background: #f0f2f5;
        margin: 0.5rem 1rem;
    }

    /* Footer drawer */
    .mobile-sidebar-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid #f0f2f5;
        flex-shrink: 0;
        font-size: 0.82rem;
        color: #a3aed1;
    }

    @media (max-width: 767.98px) {
        /* Tampilkan hamburger button */
        .mobile-hamburger { display: inline-flex; align-items: center; justify-content: center; }

        /* Sembunyikan menu horizontal kiri */
        nav.navbar .navbar-nav:first-of-type { display: none !important; }

        /* Navbar lebih compact di mobile */
        nav.navbar { height: 56px !important; }

        nav.navbar .container-fluid { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }

        /* Teks "Halo, username" selalu tersembunyi di mobile */
        .profile-pill span { display: none !important; }
    }

    /* Sembunyikan drawer sepenuhnya di desktop */
    @media (min-width: 768px) {
        .mobile-overlay,
        .mobile-sidebar { display: none !important; }
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm border-bottom" style="height: 70px;">
            <div class="container-fluid px-4 px-md-5">

                <!-- TASK 3: Hamburger button — mobile only, sebelum menu kiri -->
                <button id="openMobileSidebar" class="mobile-hamburger" aria-label="Buka menu">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- LEFT SIDE: menu horizontal (hidden di mobile via CSS) -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= uri_string() == 'home' ? 'active' : '' ?>" href="<?= base_url('home') ?>">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= (uri_string() == 'ppic' || str_starts_with(uri_string(), 'ppic/')) ? 'active' : '' ?>" href="<?= base_url('ppic') ?>">
                            PPIC
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= uri_string() == 'produksi' ? 'active' : '' ?>" href="<?= base_url('produksi') ?>">
                            Produksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link topbar-menu-link <?= (uri_string() == 'warehouse2' || str_starts_with(uri_string(), 'warehouse2/')) ? 'active' : '' ?>" href="<?= base_url('warehouse2') ?>">
                            Warehouse
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
                            <a class="dropdown-item py-2" href="<?= base_url('profile') ?>">
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

        <!-- TASK 2: Mobile Drawer — hidden di desktop via CSS -->
        <div id="mobileSidebarOverlay" class="mobile-overlay"></div>

        <div id="mobileSidebar" class="mobile-sidebar">
            <div class="mobile-sidebar-header">
                <span class="fw-bold">PT. OROPLASTINDO</span>
                <button id="closeMobileSidebar" class="mobile-sidebar-close" aria-label="Tutup menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="mobile-sidebar-nav">
                <a href="<?= base_url('home') ?>"
                   class="mobile-nav-link <?= uri_string() == 'home' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Home
                </a>

                <a href="<?= base_url('ppic') ?>"
                   class="mobile-nav-link <?= (uri_string() == 'ppic' || str_starts_with(uri_string(), 'ppic/')) ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i> PPIC
                </a>

                <a href="<?= base_url('produksi') ?>"
                   class="mobile-nav-link <?= (uri_string() == 'produksi' || str_starts_with(uri_string(), 'produksi/')) ? 'active' : '' ?>">
                    <i class="fas fa-industry"></i> Produksi
                </a>

                <a href="<?= base_url('warehouse2') ?>"
                   class="mobile-nav-link <?= (uri_string() == 'warehouse2' || str_starts_with(uri_string(), 'warehouse2/')) ? 'active' : '' ?>">
                    <i class="fas fa-cube"></i> Warehouse
                </a>

                <a href="<?= base_url('pesan') ?>"
                   class="mobile-nav-link <?= uri_string() == 'pesan' ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> Pesan
                    <span id="mobileChatBadge" class="mobile-badge" style="display:none;"></span>
                </a>

                <?php if (in_array(session('role'), ['produksi', 'gudang'])): ?>
                <a href="<?= base_url('notifikasi') ?>"
                   class="mobile-nav-link <?= uri_string() == 'notifikasi' ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i> Notifikasi
                    <span id="mobileNotifBadge" class="mobile-badge" style="display:none;"></span>
                </a>
                <?php endif; ?>

                <div class="mobile-nav-divider"></div>

                <a href="<?= base_url('profile') ?>" class="mobile-nav-link">
                    <i class="fas fa-user"></i> Profile
                </a>

                <a href="<?= base_url('logout') ?>" class="mobile-nav-link nav-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>

            <div class="mobile-sidebar-footer">
                Halo, <strong><?= esc(session('username')) ?></strong>
                <div class="text-muted" style="font-size:0.75rem; margin-top:2px;"><?= esc(session('role')) ?></div>
            </div>
        </div>

        <!-- TASK 5: JavaScript buka/tutup drawer -->
        <script>
        (function () {
            var openBtn  = document.getElementById('openMobileSidebar');
            var closeBtn = document.getElementById('closeMobileSidebar');
            var overlay  = document.getElementById('mobileSidebarOverlay');
            var sidebar  = document.getElementById('mobileSidebar');

            function openDrawer() {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (openBtn)  openBtn.addEventListener('click', openDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            if (overlay)  overlay.addEventListener('click', closeDrawer);

            // Tutup drawer saat swipe kiri (touch gesture)
            var touchStartX = 0;
            if (sidebar) {
                sidebar.addEventListener('touchstart', function (e) {
                    touchStartX = e.touches[0].clientX;
                }, { passive: true });

                sidebar.addEventListener('touchend', function (e) {
                    var dx = e.changedTouches[0].clientX - touchStartX;
                    if (dx < -60) closeDrawer(); // swipe kiri > 60px = tutup
                }, { passive: true });
            }
        })();
        </script>

