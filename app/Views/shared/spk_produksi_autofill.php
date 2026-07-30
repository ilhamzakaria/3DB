<script>
(function() {
    let pendingSpk = null;
    let spkListCache = null;

    function setFieldValue(container, selector, value) {
        if (value === null || value === undefined || value === '') {
            return;
        }

        const field = container.querySelector(selector);
        if (!field) {
            return;
        }

        field.value = value;

        if (typeof jQuery !== 'undefined' && field.tagName === 'SELECT' && jQuery(field).hasClass('select2-hidden-accessible')) {
            jQuery(field).val(value).trigger('change');
        }
    }

    function fillGudangSpkFields(container, data, spk) {
        const d = data.last || data.legacy || data.machine_data;
        if (!d) {
            return;
        }

        const nomorSpk = spk || data.nomor_spk || d.nomor_spk || d.no_spk || '';
        setFieldValue(container, 'select[name="no_spk"].nomor_spk_produksi, input[name="no_spk"]', nomorSpk);

        const namaProduk = d.nama_produk || d.produk_galon || d.produk_cap_galon || d.produk_preform || '';
        ['produk', 'produk_galon', 'produk_preform', 'produk_cap_galon'].forEach(function(name) {
            setFieldValue(container, 'input[name="' + name + '"], select[name="' + name + '"]', namaProduk);
        });

        // Auto-fill nama_produk field in Produksi form
        if (namaProduk) {
            setFieldValue(container, '.nama_produk', namaProduk);
            setFieldValue(container, '.nama_produk_hidden', namaProduk);
        }

        const shiftValue = d.shift || d.shif || '';
        setFieldValue(container, 'input[name="shif"], input[name="shift"], select[name="shif"], select[name="shift"]', shiftValue);
        setFieldValue(container, 'input[name="operator"]', d.operator || '');

        // Auto-fill packing field in Produksi form
        const packing = d.packing || '';
        if (packing) {
            setFieldValue(container, '.packing', packing);
            setFieldValue(container, '.packing_hidden', packing);
        }

        // Auto-fill tanggal field in Produksi form
        const tanggal = d.tanggal || d.created_at || '';
        if (tanggal) {
            const dateOnly = tanggal.split(' ')[0]; // Extract YYYY-MM-DD
            setFieldValue(container, '.tanggal_hidden', dateOnly);
            // Also update the display field if it exists
            const dateDisplay = container.querySelector('input[name="tanggal"][disabled]');
            if (dateDisplay) {
                dateDisplay.value = dateOnly;
            }
        }

        // If data came from machine table, also populate additional fields
        if (data.machine_data) {
            setFieldValue(container, 'input[name="brand"], select[name="brand"]', data.machine_data.brand || '');
            setFieldValue(container, 'input[name="warna"], select[name="warna"]', data.machine_data.warna || '');
            setFieldValue(container, 'input[name="stock"]', data.machine_data.stock || '');
            
            if (data.machine_data.grade) {
                setFieldValue(container, 'input[name="grade"], select[name="grade"]', data.machine_data.grade);
            }
            if (data.machine_data.berat) {
                setFieldValue(container, 'input[name="berat"]', data.machine_data.berat);
            }
            if (data.machine_data.nomor_lot) {
                setFieldValue(container, 'input[name="nomor_lot"]', data.machine_data.nomor_lot);
            }
            if (data.machine_data.packaging) {
                setFieldValue(container, 'input[name="packaging"], select[name="packaging"]', data.machine_data.packaging);
            }
        }

        if (typeof initSelect2ForModal === 'function') {
            initSelect2ForModal('#addDataModal');
        }
    }

    function showLoadingOverlay(container, message) {
        let overlay = container.querySelector('.spk-autofill-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'spk-autofill-overlay';
            overlay.style.cssText = 'position:absolute;inset:0;background:rgba(255,255,255,0.9);z-index:20;display:flex;align-items:center;justify-content:center;';
            container.style.position = 'relative';
            container.appendChild(overlay);
        }

        overlay.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><p class="mt-2 mb-0">' + message + '</p></div>';
        overlay.style.display = 'flex';
    }

    function hideLoadingOverlay(container) {
        const overlay = container.querySelector('.spk-autofill-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    function showAddModal(modalEl) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getOrCreateInstance
                ? bootstrap.Modal.getOrCreateInstance(modalEl)
                : new bootstrap.Modal(modalEl);
            modal.show();
            return;
        }

        if (typeof jQuery !== 'undefined') {
            jQuery(modalEl).modal('show');
        }
    }

    function populateSpkSelect(select, list) {
        const currentValue = select.value;
        select.innerHTML = '<option value="">-- Pilih SPK --</option>';

        list.forEach(function(item) {
            const opt = document.createElement('option');
            opt.value = item.no_spk;
            opt.textContent = item.no_spk;
            opt.dataset.produk = item.nama_produk || '';
            opt.dataset.shift = item.shift || '';
            opt.dataset.operator = item.operator || '';
            select.appendChild(opt);
        });

        if (currentValue) {
            select.value = currentValue;
        }
    }

    function ensureGudangSpkSelect(modalEl) {
        const container = modalEl.querySelector('.modal-body');
        if (!container) {
            return null;
        }

        let select = container.querySelector('select.nomor_spk_produksi[name="no_spk"]');
        if (select) {
            return select;
        }

        const input = container.querySelector('input[name="no_spk"]');
        if (!input) {
            return null;
        }

        select = document.createElement('select');
        select.name = 'no_spk';
        select.className = input.className + ' nomor_spk_produksi';
        if (input.id) {
            select.id = input.id;
        }
        if (input.required) {
            select.required = true;
        }

        select.innerHTML = '<option value="">-- Pilih SPK --</option>';
        input.parentNode.replaceChild(select, input);

        return select;
    }

    function loadSpkList() {
        if (spkListCache) {
            return Promise.resolve(spkListCache);
        }

        return fetch('<?= base_url('produksi/get_produksi_spk_list') ?>')
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                spkListCache = Array.isArray(data) ? data : [];
                return spkListCache;
            });
    }

    function initGudangSpkSelect(modalEl) {
        const select = ensureGudangSpkSelect(modalEl);
        if (!select || select.dataset.spkLoaded === '1') {
            return Promise.resolve(select);
        }

        return loadSpkList()
            .then(function(list) {
                populateSpkSelect(select, list);
                select.dataset.spkLoaded = '1';
                return select;
            })
            .catch(function() {
                return select;
            });
    }

    function loadGudangSpkData(modalEl, spk) {
        const container = modalEl.querySelector('.modal-body');
        if (!container || !spk) {
            return;
        }

        showLoadingOverlay(container, 'Memuat data SPK dari modul produksi...');

        fetch('<?= base_url('produksi/get_last_data') ?>/' + encodeURIComponent(spk))
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                hideLoadingOverlay(container);

                if (!data.last && !data.legacy && !data.machine_data) {
                    alert('Data SPK tidak ditemukan di modul produksi.');
                    return;
                }

                fillGudangSpkFields(container, data, spk);
            })
            .catch(function() {
                hideLoadingOverlay(container);
                alert('Gagal memuat data SPK.');
            });
    }

    function handleAddModalShown(modalEl) {
        initGudangSpkSelect(modalEl).then(function(select) {
            if (!select || !pendingSpk) {
                return;
            }

            const spk = pendingSpk;
            pendingSpk = null;

            let found = false;
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === spk) {
                    select.selectedIndex = i;
                    found = true;
                    break;
                }
            }

            if (!found) {
                const opt = document.createElement('option');
                opt.value = spk;
                opt.textContent = spk;
                select.appendChild(opt);
                select.value = spk;
            }

            loadGudangSpkData(modalEl, spk);
        });
    }

    document.addEventListener('shown.bs.modal', function(e) {
        if (e.target && e.target.id === 'addDataModal') {
            handleAddModalShown(e.target);
        }
    });

    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('shown.bs.modal', '#addDataModal', function() {
            handleAddModalShown(this);
        });
    }

    document.addEventListener('change', function(e) {
        if (!e.target.matches('#addDataModal select.nomor_spk_produksi[name="no_spk"]')) {
            return;
        }

        const spk = (e.target.value || '').trim();
        if (!spk) {
            return;
        }

        const modalEl = document.getElementById('addDataModal');
        if (!modalEl) {
            return;
        }

        loadGudangSpkData(modalEl, spk);
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-spk-produksi-autofill');
        if (!btn) {
            return;
        }

        const spk = (btn.dataset.spk || '').trim();
        if (!spk) {
            return;
        }

        const addModalEl = document.getElementById('addDataModal');
        if (!addModalEl) {
            return;
        }

        pendingSpk = spk;
        showAddModal(addModalEl);
    });
})();
</script>
