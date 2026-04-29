</div> <!-- content -->

<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Ilham Zakaria <?= date('Y') ?></span>
        </div>
    </div>
</footer>

</div> <!-- content-wrapper -->
</div> <!-- wrapper -->

<!-- Bootstrap JS & jQuery -->
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<?php if (isset($modern_layout) && $modern_layout): ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php else: ?>
    <script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/sb-admin-2.min.js') ?>"></script>
<?php endif; ?>

<?php if (session('logged_in') && in_array(session('role'), ['ppic','produksi','gudang','admin','manager'])): ?>
<script>
(function(){
    var badge = document.getElementById('chatBadge');
    var notifBadge = document.getElementById('notifBadge');
    var isChatPage = window.location.pathname.indexOf('/pesan') === 0;
    var isNotifPage = window.location.pathname.indexOf('/notifikasi') === 0;
    var lastCount = -1;
    var lastNotifCount = -1;

    function playNotifSound() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 800;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.2);
        } catch (e) {}
    }

    function showNotify(title, body) {
        if ('Notification' in window && Notification.permission === 'granted' && document.hidden) {
            new Notification(title || 'Pesan Baru', { body: body || 'Ada pesan baru', icon: '<?= base_url("assets/img/profile.svg") ?>' });
        }
    }

    function pollUnread() {
        if (isChatPage || !badge) return;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= base_url("pesan/getUnreadCount") ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            try {
                var r = JSON.parse(xhr.responseText);
                if (r.success && typeof r.count === 'number') {
                    var c = r.count;
                    if (c > 0) {
                        badge.textContent = c > 99 ? '99+' : c;
                        badge.style.display = 'inline-block';
                        if (lastCount >= 0 && c > lastCount) {
                            playNotifSound();
                            showNotify('Pesan', 'Ada ' + c + ' pesan baru');
                        }
                    } else {
                        badge.style.display = 'none';
                    }
                    lastCount = c;
                }
            } catch (e) {}
        };
        xhr.send();
    }

    function pollNotifUnread() {
        if (isNotifPage || !notifBadge) return;
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= base_url("notifikasi/count") ?>', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            try {
                var r = JSON.parse(xhr.responseText);
                var c = (r && typeof r.total === 'number') ? r.total : 0;
                if (c > 0) {
                    notifBadge.textContent = c > 99 ? '99+' : c;
                    notifBadge.style.display = 'inline-block';
                    if (lastNotifCount >= 0 && c > lastNotifCount) {
                        playNotifSound();
                        showNotify('Notifikasi', 'Ada ' + c + ' notifikasi baru');
                    }
                } else {
                    notifBadge.style.display = 'none';
                }
                lastNotifCount = c;
            } catch (e) {}
        };
        xhr.send();
    }

    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    pollUnread();
    pollNotifUnread();
    setInterval(function() {
        pollUnread();
        pollNotifUnread();
    }, 60000);
})();
</script>
<?php endif; ?>
</body>

</html>