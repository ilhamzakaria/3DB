
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ppicSpkMaster = <?= json_encode($spk_master ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const ppicSpkMasterMap = Object.fromEntries(
            ppicSpkMaster.map((item) => [String(item.no_spk || '').trim(), item])
        );

        function applyPpicMasterData(noSpk) {
            const key = String(noSpk || '').trim();
            const master = ppicSpkMasterMap[key];
            if (!master) return;

            const fields = {
                'add_no_spk': master.no_spk,
                'add_nama_mesin': master.nama_mesin,
                'add_nama_produk': master.nama_produk,
                'add_grade': master.grade,
                'add_warna': master.warna,
                'add_nomor_mesin': master.nomor_mesin,
                'add_operator': master.operator,
                'add_targett': master.targett
            };

            for (const [id, value] of Object.entries(fields)) {
                const el = document.getElementById(id);
                if (el) el.value = value || '';
            }
        }

        // Master Data Select Handler
        const masterSelect = document.getElementById('masterNoSpkSelect');
        if (masterSelect) {
            masterSelect.addEventListener('change', function() {
                applyPpicMasterData(this.value);
            });
        }

        // Shift Auto-calculation
        const jamSelect = document.getElementById('jamSelect');
        if (jamSelect) {
            jamSelect.addEventListener('change', function() {
                const jam = this.value;
                const shiftInput = document.getElementById('shiftInput');
                if (!shiftInput) return;

                const s1 = ["07-08", "08-09", "09-10", "10-11", "11-12", "12-13", "13-14"];
                const s2 = ["14-15", "15-16", "16-17", "17-18", "18-19", "19-20", "20-21", "21-22"];
                const s3 = ["22-23", "23-00", "00-01", "01-02", "02-03", "03-04", "04-05", "05-06", "06-07"];

                if (s1.includes(jam)) shiftInput.value = "1";
                else if (s2.includes(jam)) shiftInput.value = "2";
                else if (s3.includes(jam)) shiftInput.value = "3";
                else shiftInput.value = "";
            });
        }

        // Edit Modal Handler
        const modalEdit = document.getElementById('modalEdit');
        if (modalEdit) {
            modalEdit.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const id = btn.getAttribute('data-id');
                const fields = ['jam', 'no_spk', 'nama_mesin', 'nama_produk', 'grade', 'warna', 'nomor_mesin', 'shif', 'operator', 'targett', 'revisi', 'tanggal'];

                fields.forEach(field => {
                    const el = document.getElementById('edit_' + field);
                    if (el) el.value = btn.getAttribute('data-' + field) || '';
                });

                const editIdField = document.getElementById('edit_id');
                if (editIdField) editIdField.value = id;
                const formEdit = document.getElementById('formEdit');
                if (formEdit) formEdit.setAttribute('action', "<?= base_url('ppic/update/') ?>" + id);
            });
        }

        // Delete Modal Handler
        const modalDelete = document.getElementById('modalDelete');
        if (modalDelete) {
            modalDelete.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                const spk = btn.getAttribute('data-spk');

                const deleteSpkName = document.getElementById('deleteSpkName');
                if (deleteSpkName) deleteSpkName.textContent = spk;
                const formDelete = document.getElementById('formDelete');
                if (formDelete) formDelete.setAttribute('action', "<?= base_url('ppic/delete/') ?>" + id);
            });
        }
        // Recycle Bin Loading
        const btnOpenTrash = document.getElementById('btn_open_trash');
        if (btnOpenTrash) {
            btnOpenTrash.addEventListener('click', function() {
                const tbody = document.querySelector('#table_trash tbody');
                if (!tbody) return;
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';
                fetch('<?= base_url('ppic/get_trash') ?>')
                    .then(r => r.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Tempat sampah kosong.</td></tr>';
                            return;
                        }
                        data.forEach(p => {
                            const html = `
                                <tr style="font-size: 13px;">
                                    <td class="px-3 py-2">
                                        <div class="font-weight-bold" style="margin-bottom: -2px;">${p.no_spk}</div>
                                        <div class="text-muted" style="font-size: 11px;">${p.nama_mesin} - ${p.nama_produk}</div>
                                    </td>
                                    <td class="py-2 text-center text-muted">${p.deleted_at}</td>
                                    <td class="text-end py-2 px-3">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="<?= base_url('ppic/restore') ?>/${p.id}" class="btn btn-success btn-xs px-2" style="font-size: 10px; padding: 2px 8px;">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                            <a href="<?= base_url('ppic/delete_permanent') ?>/${p.id}" class="btn btn-outline-danger btn-xs px-2" style="font-size: 10px; padding: 2px 8px;" onclick="return confirm('Hapus permanen?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>`;
                            tbody.insertAdjacentHTML('beforeend', html);
                        });
                    });
            });
        }
    });
</script>
