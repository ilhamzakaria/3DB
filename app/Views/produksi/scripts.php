<script>
let activeRejectStorage = null;
let jamRowCount = 1000;
let dtRowCount = 1000;

function previewSig(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            input.parentElement.querySelector('img').src = e.target.result;
            input.parentElement.classList.add('border-primary');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Helper functions for dynamic rows
    window.addJamRow = function(tableBody, jam, val = '') {
        const rowId = jamRowCount++;
        const html = `
            <tr data-jam="${jam}">
                <td class="px-3" width="100">
                    <input type="text" name="jam_data[${rowId}][rentang_jam]" value="${jam}" class="form-control-plaintext text-center font-weight-bold" readonly style="width: 80px;">
                </td>
                <td><input type="number" name="jam_data[${rowId}][hasil_produksi]" value="${val}" class="form-control form-control-sm jam-hasil text-center" required placeholder="0"></td>
                <td class="text-center" width="50">
                    <button type="button" class="btn btn-link btn-sm text-danger remove-jam" title="Hapus" style="text-decoration: none; font-size: 1.2rem;">×</button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', html);
    };

    window.addDtRow = function(tableBody, data = {}) {
        const rowId = dtRowCount++;
        const html = `
            <tr>
                <td class="px-3"><input type="text" name="downtimes[${rowId}][alasan]" value="${data.alasan_downtime || ''}" class="form-control form-control-sm"></td>
                <td><input type="datetime-local" name="downtimes[${rowId}][mulai]" value="${data.waktu_mulai ? data.waktu_mulai.replace(' ', 'T').substring(0,16) : ''}" class="form-control form-control-sm dt-mulai"></td>
                <td><input type="datetime-local" name="downtimes[${rowId}][selesai]" value="${data.waktu_selesai ? data.waktu_selesai.replace(' ', 'T').substring(0,16) : ''}" class="form-control form-control-sm dt-selesai"></td>
                <td><input type="number" name="downtimes[${rowId}][durasi]" value="${data.durasi_menit || ''}" class="form-control form-control-sm dt-durasi"></td>
                <td class="text-center"><button type="button" class="btn btn-link btn-sm text-danger remove-dt">×</button></td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', html);
    };

    window.updateTotals = function(container) {
        if (!container) return;
        let totalHasil = 0;
        container.querySelectorAll('.jam-hasil').forEach(inp => totalHasil += (parseInt(inp.value) || 0));
        
        const isi = parseInt(container.querySelector('.isi').value) || 0;
        const totalPcs = totalHasil * isi;
        
        const display = container.querySelector('.grand_total_hasil_display');
        if (display) display.innerText = totalHasil;
        
        const totalReject = parseInt(container.querySelector('.grand_total_reject').value) || 0;
        const gBagus = container.querySelector('.grand_total_bagus');
        if (gBagus) gBagus.value = totalPcs - totalReject;
    };

    // Global event listeners using delegation
    document.addEventListener('click', function(e) {
        // Add Jam Row
        if (e.target.closest('.btn_add_jam_row')) {
            const btn = e.target.closest('.btn_add_jam_row');
            const container = btn.closest('.modal-body');
            const select = container.querySelector('.select_jam');
            const tableBody = container.querySelector('.table_jam_form tbody');
            const jam = select.value;
            if (!jam) return;
            if (tableBody.querySelector(`tr[data-jam="${jam}"]`)) { alert('Jam ini sudah ada'); return; }
            addJamRow(tableBody, jam);
            updateTotals(container);
        }

        // Add Downtime Row
        if (e.target.closest('.btn_add_dt')) {
            const tableBody = e.target.closest('.modal-body').querySelector('.table_downtime_form tbody');
            addDtRow(tableBody);
        }

        // Remove Row
        if (e.target.classList.contains('remove-jam')) {
            const container = e.target.closest('.modal-body');
            e.target.closest('tr').remove();
            if (container) updateTotals(container);
        }
        if (e.target.classList.contains('remove-dt')) { e.target.closest('tr').remove(); }

        // Reject Modal Trigger
        if (e.target.closest('.btn_open_reject_modal')) {
            const btn = e.target.closest('.btn_open_reject_modal');
            activeRejectStorage = document.getElementById(btn.dataset.targetStorage);
            document.querySelectorAll('.input-reject-detail').forEach(inp => inp.value = '');
            if (activeRejectStorage) {
                activeRejectStorage.querySelectorAll('input').forEach(inp => {
                    const modalInp = document.querySelector(`.input-reject-detail[data-type="${inp.dataset.type}"]`);
                    if (modalInp) modalInp.value = inp.value;
                });
            }
        }

        // Edit Laporan (AJAX Fragment)
        if (e.target.closest('.btn_edit_laporan')) {
            const btn = e.target.closest('.btn_edit_laporan');
            const id = btn.dataset.id;
            const container = document.getElementById('editModalContent');
            if (!container) return;
            container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Memuat formulir...</p></div>';
            fetch('<?= base_url('produksi/get_edit_form') ?>/' + id)
                .then(r => r.text())
                .then(html => {
                    container.innerHTML = html;
                    // Trigger total calculation
                    updateTotals(container.querySelector('.modal-body'));
                });
        }

        // View Detail (AJAX Fragment)
        if (e.target.closest('.btn_view_detail')) {
            const btn = e.target.closest('.btn_view_detail');
            const id = btn.dataset.id;
            const container = document.getElementById('detailModalContent');
            if (!container) return;
            container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Memuat data...</p></div>';
            fetch('<?= base_url('produksi/get_detail_modal') ?>/' + id)
                .then(r => r.text())
                .then(html => {
                    container.innerHTML = html;
                });
        }

        // Delete Confirm
        if (e.target.closest('.btn_confirm_delete')) {
            const id = e.target.closest('.btn_confirm_delete').dataset.id;
            const finalDeleteBtn = document.getElementById('btn_final_delete');
            if (finalDeleteBtn) finalDeleteBtn.href = '<?= base_url('produksi/delete') ?>/' + id;
        }
    });

    // Change events
    document.addEventListener('change', function(e) {
        // SPK Baru Switch
        if (e.target.classList.contains('check_spk_baru')) {
            const container = e.target.closest('.modal-body');
            const selectWrap = container.querySelector('.spk_select_wrapper');
            const inputWrap = container.querySelector('.spk_input_wrapper');
            const select = container.querySelector('.nomor_spk');
            const input = container.querySelector('.nomor_spk_baru');
            if (e.target.checked) {
                selectWrap.style.display = 'none'; inputWrap.style.display = 'block';
                select.removeAttribute('name'); input.setAttribute('name', 'nomor_spk');
                input.setAttribute('required', true); select.removeAttribute('required');
            } else {
                selectWrap.style.display = 'block'; inputWrap.style.display = 'none';
                input.removeAttribute('name'); select.setAttribute('name', 'nomor_spk');
                select.setAttribute('required', true); input.removeAttribute('required');
            }
        }

        // SPK Select Autofill
        if (e.target.classList.contains('nomor_spk')) {
            const sel = e.target;
            const opt = sel.options[sel.selectedIndex];
            if (!opt.value) return;
            const container = sel.closest('.modal-body');
            container.querySelector('.nama_mesin').value = opt.dataset.mesin || '';
            container.querySelector('.nama_produksi').value = opt.dataset.produk || '';
            fetch('<?= base_url('produksi/get_last_data') ?>/' + encodeURIComponent(opt.value))
                .then(r => r.json())
                .then(data => {
                    const d = data.last || data.legacy;
                    if (d) {
                        container.querySelector('.nomor_mesin').value = d.nomor_mesin || '';
                        container.querySelector('.batch_number').value = d.batch_number || '';
                        container.querySelector('.grup').value = d.grup || '';
                        container.querySelector('.packing').value = d.packing || '';
                        container.querySelector('.cycle_time').value = d.cycle_time || '';
                        container.querySelector('.target').value = d.target || '';
                        container.querySelector('.isi').value = d.isi || '';
                        container.querySelector('.operator').value = d.operator || '';
                        container.querySelector('.merek_kode').value = d.merek_kode || '';
                        container.querySelector('.m_pemakaian').value = d.m_pemakaian || d.pemakaian || '';
                    }
                });
        }
    });

    // Input events
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jam-hasil') || e.target.classList.contains('isi')) { 
            updateTotals(e.target.closest('.modal-body')); 
        }

        // Downtime Duration Calculation
        if (e.target.classList.contains('dt-mulai') || e.target.classList.contains('dt-selesai')) {
            const row = e.target.closest('tr');
            const mulai = row.querySelector('.dt-mulai').value;
            const selesai = row.querySelector('.dt-selesai').value;
            const durasiInput = row.querySelector('.dt-durasi');
            
            if (mulai && selesai) {
                const start = new Date(mulai);
                const end = new Date(selesai);
                const diffMs = end - start;
                const diffMins = Math.round(diffMs / 60000);
                durasiInput.value = isNaN(diffMins) ? '' : diffMins;
            }
        }
    });

    // Save Reject from Reject Modal
    const btnSaveReject = document.getElementById('btn_save_reject_generic');
    if (btnSaveReject) {
        btnSaveReject.addEventListener('click', function() {
            if (!activeRejectStorage) return;
            activeRejectStorage.innerHTML = '';
            let total = 0;
            document.querySelectorAll('.input-reject-detail').forEach(inp => {
                const val = parseInt(inp.value) || 0;
                if (val > 0) {
                    total += val;
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden'; hidden.name = `rejects[${inp.dataset.type}]`;
                    hidden.value = val; hidden.dataset.type = inp.dataset.type;
                    activeRejectStorage.appendChild(hidden);
                }
            });
            const container = activeRejectStorage.closest('.modal-body');
            if (container) {
                const rejectInput = container.querySelector('.grand_total_reject');
                if (rejectInput) rejectInput.value = total;
                updateTotals(container);
            }
        });
    }

    // Recycle Bin Loading
    const btnTrash = document.getElementById('btn_open_trash');
    if (btnTrash) {
        btnTrash.addEventListener('click', function() {
            const tbody = document.querySelector('#table_trash tbody');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';
            fetch('<?= base_url('produksi/get_trash') ?>')
                .then(r => r.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) { tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Tempat sampah kosong.</td></tr>'; return; }
                    data.forEach(p => {
                        const html = `<tr><td class="px-3 py-3"><div class="font-weight-bold">${p.nomor_spk}</div><div class="text-xs text-muted">${p.nama_mesin} - ${p.nama_produksi}</div></td><td class="py-3 small">${p.deleted_at}</td><td class="text-center py-3"><div class="d-flex justify-content-center gap-1"><a href="<?= base_url('produksi/restore') ?>/${p.id}" class="btn btn-xs btn-success px-2 py-1" style="font-size: 10px;"><i class="fas fa-undo mr-1"></i> Pulihkan</a><a href="<?= base_url('produksi/delete_permanent') ?>/${p.id}" class="btn btn-xs btn-outline-danger px-2 py-1" style="font-size: 10px;" onclick="return confirm('Hapus permanen?')"><i class="fas fa-times"></i></a></div></td></tr>`;
                        tbody.insertAdjacentHTML('beforeend', html);
                    });
                });
        });
    }
});
</script>
