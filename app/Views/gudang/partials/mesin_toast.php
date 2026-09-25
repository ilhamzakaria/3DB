<script>
/* ── Mesin Toast Progress (shared partial) ─────────────────────── */
(function () {
    'use strict';

    /* ── Engine ── */
    window.showMesinToast = function (message, type) {
        type = type || 'info';
        var cfg = {
            success : { bg: '#198754', icon: 'fas fa-check-circle' },
            error   : { bg: '#dc3545', icon: 'fas fa-exclamation-circle' },
            info    : { bg: '#0d6efd', icon: 'fas fa-info-circle' },
            warning : { bg: '#e6a817', icon: 'fas fa-exclamation-triangle' }
        }[type] || { bg: '#0d6efd', icon: 'fas fa-info-circle' };

        var container = document.getElementById('mesinToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'mesinToastContainer';
            container.style.cssText = [
                'position:fixed', 'top:20px', 'right:20px', 'z-index:99999',
                'display:flex', 'flex-direction:column', 'gap:10px',
                'pointer-events:none'
            ].join(';');
            document.body.appendChild(container);
        }

        var barId = 'mbar_' + Date.now();
        var toast = document.createElement('div');
        toast.style.cssText = [
            'background:' + cfg.bg,
            'color:#fff',
            'padding:14px 18px',
            'border-radius:10px',
            'box-shadow:0 6px 20px rgba(0,0,0,.2)',
            'display:flex',
            'align-items:center',
            'gap:12px',
            'min-width:280px',
            'max-width:360px',
            'transform:translateX(420px)',
            'transition:transform .3s cubic-bezier(.25,.8,.25,1)',
            'font-size:14px',
            'font-family:inherit'
        ].join(';');

        toast.innerHTML =
            '<i class="' + cfg.icon + '" style="font-size:17px;flex-shrink:0;"></i>' +
            '<div style="flex-grow:1">' +
                '<div style="font-weight:600;margin-bottom:5px;">' + message + '</div>' +
                '<div style="height:3px;background:rgba(255,255,255,.25);border-radius:2px;overflow:hidden;">' +
                    '<div id="' + barId + '" style="height:100%;background:rgba(255,255,255,.75);width:100%;transition:width 2.8s linear;"></div>' +
                '</div>' +
            '</div>';

        container.appendChild(toast);

        /* Slide in */
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                toast.style.transform = 'translateX(0)';
                var bar = document.getElementById(barId);
                if (bar) requestAnimationFrame(function () { bar.style.width = '0%'; });
            });
        });

        /* Slide out & remove */
        setTimeout(function () {
            toast.style.transform   = 'translateX(420px)';
            toast.style.opacity     = '0';
            toast.style.transition += ',opacity .3s';
            setTimeout(function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 380);
        }, 3200);
    };

    /* ── Bind helper ── */
    function bindForm(formId, btnId, msg) {
        var form = document.getElementById(formId);
        var btn  = document.getElementById(btnId);
        if (!form || !btn || form.__toastBound) return;
        form.__toastBound = true;
        form.addEventListener('submit', function () {
            window.showMesinToast(msg, 'info');
            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';
        });
    }

    function init() {
        bindForm('formTambahMesin', 'btnTambahMesin', 'Menyimpan data...');
        bindForm('formEditMesin',   'btnEditMesin',   'Menyimpan perubahan...');
    }

    /* Bootstrap 5 */
    document.addEventListener('show.bs.modal', function (e) {
        var id = e.target && e.target.id;
        if (id === 'addDataModal' || id === 'editDataModal') setTimeout(init, 60);
    });
    /* Bootstrap 4 (jQuery) */
    if (window.jQuery) {
        jQuery(document).on('show.bs.modal', '#addDataModal, #editDataModal', function () {
            setTimeout(init, 60);
        });
    }
    /* Direct init */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
