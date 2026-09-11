<script>
    // ===== DIAGNOSTIC LOGGING =====
    // Capture semua console.log ke array untuk debug
    const diagnosticLogs = [];
    const originalLog = console.log;
    const originalError = console.error;

    console.log = function(...args) {
        diagnosticLogs.push({
            type: 'log',
            message: args.join(' '),
            timestamp: new Date().toISOString()
        });
        originalLog.apply(console, args);
    };

    console.error = function(...args) {
        diagnosticLogs.push({
            type: 'error',
            message: args.join(' '),
            timestamp: new Date().toISOString()
        });
        originalError.apply(console, args);
    };

    // Expose diagnostics globally
    window.getDiagnostics = function() {
        return {
            logs: diagnosticLogs,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            environment: 'server/hosting (assumed from context)'
        };
    };

    console.log('[DIAGNOSTIC] Scripts loaded at', new Date().toISOString());
</script>

<script>
    let activeRejectStorage = null;
    let jamRowCount = 1000;
    let dtRowCount = 1000;
    let spkAutofillLock = false;
    let ttdAutoFillInProgress = false; // Flag untuk track TTD auto-fill status

    // Flag: apakah mesin ini menggunakan reject inline per jam (Becum, Powerjet 1, Powerjet 2)
    // Nilai di-set oleh form_fields.php via window.IS_JAM_REJECT_MESIN sebelum scripts.php dieksekusi
    const IS_JAM_REJECT_MESIN = window.IS_JAM_REJECT_MESIN === true;

    /**
     * Parse persentase komposisi dari PPIC revisi field
     * Contoh input: "(TBT Pristine : 293 kg) V : 30,84 %, RS Ikrama : 40 % , OT+ : 29 %, Trans Blue : 0,16 %"
     * Output: 100.00
     */
    function parseKomposisiPersentase(revisiText) {
        return 0;
    }

    function displayKomposisi(container, komposisiArr) {
        console.log('[KOMPOSISI] displayKomposisi called with:', {
            container,
            komposisiArr
        });

        const wrapper = (container && container.querySelector('#ppic_komposisi_wrapper')) ||
            document.querySelector('#ppic_komposisi_wrapper');
        const list = (container && container.querySelector('#ppic_komposisi_list')) ||
            document.querySelector('#ppic_komposisi_list');
        const empty = (container && container.querySelector('#ppic_komposisi_empty')) ||
            document.querySelector('#ppic_komposisi_empty');

        console.log('[KOMPOSISI] Elements found:', {
            wrapper: !!wrapper,
            list: !!list,
            empty: !!empty
        });

        if (!wrapper || !list || !empty) {
            console.warn('[KOMPOSISI] Missing elements - wrapper:', !!wrapper, 'list:', !!list, 'empty:', !!empty);
            return;
        }

        if (!komposisiArr || komposisiArr.length === 0) {
            console.log('[KOMPOSISI] No data, showing empty state');
            wrapper.style.display = 'none';
            empty.style.display = 'block';
            list.innerHTML = '';
            return;
        }

        // Generate badges untuk setiap komposisi
        console.log('[KOMPOSISI] Generating badges for:', komposisiArr);
        list.innerHTML = komposisiArr.map(nama =>
            `<span class="badge fw-normal" style="background: rgba(13,110,253,.1); color: #0a58ca; border: 1px solid rgba(13,110,253,.25); font-size: 0.7rem; border-radius: 12px;">${escHtml(String(nama))}</span>`
        ).join('');

        wrapper.style.display = 'block';
        empty.style.display = 'none';

        console.log('[KOMPOSISI] Displayed:', komposisiArr.length, 'items successfully');
    }

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

    function updateRejectInlineTotals(root = document) {
        const rejectTbody = root.querySelector('#reject_tbody');
        const rejectTotalSpan = root.querySelector('#reject_total');
        if (!rejectTbody || !rejectTotalSpan) return;

        let total = 0;
        rejectTbody.querySelectorAll('.reject-jumlah').forEach(input => {
            total += parseInt(input.value) || 0;
        });

        rejectTotalSpan.textContent = total.toLocaleString('id-ID');

        // Update display field
        const grandTotalRejectDisplay = root.querySelector('.grand_total_reject');
        if (grandTotalRejectDisplay) grandTotalRejectDisplay.value = total;
        // Update hidden submit field
        const grandTotalRejectHidden = root.querySelector('.grand_total_reject_hidden');
        if (grandTotalRejectHidden) grandTotalRejectHidden.value = total;

        // Trigger updateTotals to recalculate Total Bagus and Sisa PO
        updateTotals(root);
    }

    /**
     * syncJamRejectToRejectSection — Kumpulkan semua reject dari kolom jam,
     * gabungkan per jenis, update Total Reject, simpan ke form.dataset untuk submit.
     * Hanya aktif jika IS_JAM_REJECT_MESIN = true.
     */
    function syncJamRejectToRejectSection(container) {
        if (!IS_JAM_REJECT_MESIN) return;

        // Kumpulkan semua reject dari semua baris jam
        const rejectMap = {}; // { 'START UP': 10, 'CAMERA': 8 }
        container.querySelectorAll('.jam-reject-cell').forEach(cell => {
            cell.querySelectorAll('.jam-reject-row').forEach(row => {
                const jenis  = row.querySelector('.jam-reject-type')?.value  || '';
                const jumlah = parseInt(row.querySelector('.jam-reject-jumlah')?.value) || 0;
                if (jenis && jumlah > 0) {
                    rejectMap[jenis] = (rejectMap[jenis] || 0) + jumlah;
                }
            });
        });

        // Hitung total reject
        const totalReject = Object.values(rejectMap).reduce((a, b) => a + b, 0);

        // Update display Total Reject
        const rejectDisplay = container.querySelector('.grand_total_reject');
        const rejectHidden  = container.querySelector('.grand_total_reject_hidden');
        if (rejectDisplay) rejectDisplay.value = totalReject;
        if (rejectHidden)  rejectHidden.value  = totalReject;

        // Update badge di card Detail Jenis Reject
        const rejectTotalBadge = container.querySelector('#reject_total');
        if (rejectTotalBadge) rejectTotalBadge.textContent = totalReject.toLocaleString('id-ID');

        // Simpan rejectMap ke dataset form untuk dipakai saat submit
        const form = container.querySelector('form') || container.closest('form');
        if (form) form.dataset.jamRejectMap = JSON.stringify(rejectMap);

        // Trigger updateTotals agar Sisa PO ikut dihitung ulang
        updateTotals(container);
    }

    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Reject types passed from PHP (app/Controllers/Produksi.php passes $data['reject_types'])
    const _rejectTypesData = <?= isset($reject_types) ? json_encode($reject_types, JSON_UNESCAPED_UNICODE) : '[]' ?>;

    function getRejectTypesFromData() {
        return _rejectTypesData.map(type => type.nama_reject || type);
    }

    async function addRejectInlineRow(root = document) {
        const rejectTbody = root.querySelector('#reject_tbody');
        if (!rejectTbody) return;

        const types = getRejectTypesFromData();
        const rowCount = rejectTbody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.className = 'reject-row border-bottom';
        newRow.style.transition = 'background-color 0.2s';

        let optionsHtml = '<option value="">-- Pilih Jenis Reject --</option>';
        types.forEach(typeName => {
            optionsHtml += `<option value="${escHtml(typeName)}">${escHtml(typeName)}</option>`;
        });

        newRow.innerHTML = `
            <td class="px-4 py-3">
                <select name="rejects[${rowCount}][jenis_reject]"
                    class="form-select form-select-sm reject-type"
                    style="border-radius: 6px; border: 1px solid #dee2e6; font-size: 0.875rem;"
                    required>
                    ${optionsHtml}
                </select>
            </td>
            <td class="px-4 py-3">
                <input type="number"
                    name="rejects[${rowCount}][jumlah]"
                    class="form-control form-control-sm text-center reject-jumlah"
                    placeholder="0"
                    min="0"
                    max="99999"
                    value="0"
                    style="border-radius: 6px; border: 1px solid #dee2e6; font-weight: 600; font-size: 0.9rem; padding: 0.5rem;"
                    required>
            </td>
            <td class="text-center px-4 py-3">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-reject" title="Hapus" style="border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                </button>
            </td>
        `;

        rejectTbody.appendChild(newRow);
        newRow.querySelector('.btn-remove-reject').addEventListener('click', function(e) {
            e.preventDefault();
            newRow.remove();
            updateRejectInlineTotals(root);
        });
        newRow.querySelector('.reject-jumlah').addEventListener('input', function() {
            updateRejectInlineTotals(root);
        });
        newRow.querySelector('.reject-type').focus();
        updateRejectInlineTotals(root);
    }

    function initializeRejectSection(root = document) {
        const rejectTable = root.querySelector('#table_reject_inline');
        if (!rejectTable) return;

        // Pre-fetch reject types
        getRejectTypes();

        const addRejectBtn = root.querySelector('#btn_add_reject_row_inline');
        if (addRejectBtn && !addRejectBtn.dataset.rejectBound) {
            addRejectBtn.dataset.rejectBound = '1';
            addRejectBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addRejectInlineRow(root);
            });
        }

        rejectTable.querySelectorAll('.btn-remove-reject').forEach(btn => {
            if (!btn.dataset.rejectRemoveBound) {
                btn.dataset.rejectRemoveBound = '1';
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.closest('tr').remove();
                    updateRejectInlineTotals(root);
                });
            }
        });

        rejectTable.querySelectorAll('.reject-jumlah').forEach(input => {
            if (!input.dataset.rejectQtyBound) {
                input.dataset.rejectQtyBound = '1';
                input.addEventListener('input', function() {
                    updateRejectInlineTotals(root);
                });
            }
        });

        if (!document.getElementById('reject_types_list')) {
            // Reject types list removed - now using dropdown select instead
        }

        updateRejectInlineTotals(root);
        updateBagusTotal(root);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Function untuk menghitung total komposisi persen
        window.updateKomposisiTotal = function(container = document) {
            console.log('[updateKomposisiTotal] Called with container:', container);
            const persenInputs = container.querySelectorAll('.komposisi-persen-input');
            const totalDisplay = container.querySelector('.komposisi-total-persen');
            const warningContainer = container.querySelector('.komposisi-warning');
            const warningText = container.querySelector('.komposisi-warning-text');
            
            console.log('[updateKomposisiTotal] Persen inputs found:', persenInputs.length, 'Total display:', totalDisplay);
            
            if (!totalDisplay) {
                console.warn('[updateKomposisiTotal] No totalDisplay element found!');
                return;
            }
            
            let total = 0;
            persenInputs.forEach((input, idx) => {
                const value = parseFloat(input.value) || 0;
                console.log(`[updateKomposisiTotal] Input ${idx}:`, value);
                total += value;
            });
            
            console.log('[updateKomposisiTotal] Total calculated:', total.toFixed(2));
            
            // Update display
            totalDisplay.textContent = total.toFixed(2) + ' %';
            
            // Change color berdasarkan total
            if (total === 100) {
                totalDisplay.className = 'komposisi-total-persen badge bg-success';
                if (warningContainer) warningContainer.style.display = 'none';
                console.log('[updateKomposisiTotal] Status: VALID (100%)');
            } else if (total < 100) {
                totalDisplay.className = 'komposisi-total-persen badge bg-warning';
                if (warningContainer && warningText) {
                    warningText.textContent = `Total persen hanya ${total.toFixed(2)}%, harus 100% untuk valid.`;
                    warningContainer.style.display = 'block';
                }
                console.log('[updateKomposisiTotal] Status: INCOMPLETE (<100%)');
            } else {
                totalDisplay.className = 'komposisi-total-persen badge bg-danger';
                if (warningContainer && warningText) {
                    warningText.textContent = `Total persen ${total.toFixed(2)}%, melebihi 100%!`;
                    warningContainer.style.display = 'block';
                }
                console.log('[updateKomposisiTotal] Status: OVER (>100%)');
            }
        };

        // Init komposisi event listeners
        function initKomposisiListeners(container = document) {
            console.log('[initKomposisiListeners] Starting with container:', container);
            const persenInputs = container.querySelectorAll('.komposisi-persen-input');
            console.log('[initKomposisiListeners] Found persen inputs:', persenInputs.length);
            
            persenInputs.forEach(input => {
                if (!input.dataset.komposisiBound) {
                    input.dataset.komposisiBound = '1';
                    input.addEventListener('input', function() {
                        console.log('[komposisi input] Value changed:', this.value);
                        window.updateKomposisiTotal(container);
                    });
                    console.log('[initKomposisiListeners] Listener attached to input');
                }
            });
            // Initial calculation
            console.log('[initKomposisiListeners] Calling updateKomposisiTotal');
            window.updateKomposisiTotal(container);
        }

        // Expose for modal initialization
        window.initKomposisiListeners = initKomposisiListeners;

        // GLOBAL EVENT DELEGATION - untuk catch semua input persentase komposisi
        // Fallback jika event listener direct tidak berfungsi
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('komposisi-persen-input')) {
                console.log('[global-delegation] Komposisi persen input changed:', e.target.value);
                const form = e.target.closest('form') || e.target.closest('.modal-body') || document;
                window.updateKomposisiTotal(form);
            }
        }, true);
        window.addJamRow = function(tableBody, jam, val = '') {
            const rowId = jamRowCount++;

            // Build reject options dari data PHP
            let rejectOptions = '<option value="">-- Jenis --</option>';
            getRejectTypesFromData().forEach(name => {
                rejectOptions += `<option value="${escHtml(name)}">${escHtml(name)}</option>`;
            });

            // Kolom reject — hanya jika mesin Becum/Powerjet
            const rejectColHtml = IS_JAM_REJECT_MESIN ? `
                <td class="px-2 py-2 jam-reject-cell" data-jam="${jam}">
                    <div class="jam-reject-list">
                        <div class="jam-reject-row d-flex gap-1 mb-1 align-items-center">
                            <select class="form-select form-select-sm jam-reject-type flex-grow-1"
                                style="font-size: 0.75rem; min-width: 110px;">
                                ${rejectOptions}
                            </select>
                            <input type="number" min="0"
                                class="form-control form-control-sm jam-reject-jumlah"
                                placeholder="0" style="width: 60px; font-size: 0.75rem;">
                            <button type="button"
                                class="btn btn-sm btn-outline-danger btn-remove-jam-reject"
                                style="padding: 0.2rem 0.4rem; font-size: 0.7rem;">✕</button>
                        </div>
                    </div>
                    <button type="button"
                        class="btn btn-link btn-sm btn-add-jam-reject p-0 mt-1"
                        style="font-size: 0.72rem; color: #6c757d;">
                        <i class="fas fa-plus me-1"></i>Reject
                    </button>
                </td>` : '';

            const html = `
            <tr data-jam="${jam}">
                <td class="px-4 py-3">
                    <span class="badge bg-primary-light text-primary fw-600" style="font-size: 0.85rem;">${jam}</span>
                    <input type="hidden" name="jam_data[${rowId}][rentang_jam]" value="${jam}">
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="jam_data[${rowId}][hasil_produksi]" value="${val}" class="form-control form-control-sm jam-hasil" required placeholder="0" style="border-radius: 6px; border: 1px solid #dee2e6;">
                </td>
                ${rejectColHtml}
                <td class="text-center px-4 py-3">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-jam" title="Hapus" style="border-radius: 50%; width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                    </button>
                </td>
            </tr>
        `;
            tableBody.insertAdjacentHTML('beforeend', html);
        };

        window.addDtRow = function(container, data = {}) {
            const rowId = dtRowCount++;
            const reasons = {
                'DT1':  'Improvement',
                'DT2':  'PLN Off',
                'DT3':  'Change Product',
                'DT4':  'Planned Maintenance',
                'DT5':  'Trial Produk',
                'DT6':  'Trial mould dan emboss',
                'DT7':  'Bencana Alam',
                'DT8':  'Hari libur dan istirahat',
                'DT10': 'Sanitasi',
                'DT11': 'Warm up',
                'DT12': 'Stop msn atas permint khusus',
                'DT13': 'Quality Inspection',
                'DT14': 'Kerusakan Mesin Utama',
                'DT15': 'Kerusakan Utility',
                'DT16': 'Kerusakan Tools',
                'DT17': 'Alarm karena Error',
                'DT18': 'Alarm karena kesalahan proses',
                'DT20': 'Salah memasukan material',
                'DT21': 'Material tidak tersedia',
                'DT22': 'Man power tidak mencukupi',
                'DT23': 'Tidak ada tmpt Menyimpan produk',
                'DT24': 'Product Tidak Standar',
                'DT25': 'Material tidak standar',
                'DT26': 'Coloran tidak standar',
                'DT27': 'No Order',
                'DT28': 'Cleaning Mold',
                'DT29': 'Cleaning Tooling',
                'DT30': 'Cleaning Mesin dan Tools',
                'DT31': 'Kegagalan Design Produk',
                'DT32': 'Ketiadaan kelengkapan kerja',
            };

            let reasonOptions = '<option value="">-- Pilih Alasan --</option>';
            for (const [code, desc] of Object.entries(reasons)) {
                const val = `${code} - ${desc}`;
                reasonOptions += `<option value="${val}" ${data.alasan_downtime === val ? 'selected' : ''}>${val}</option>`;
            }

            const cardNum = container.querySelectorAll('.dt-card').length + 1;

            const html = `
            <div class="dt-card">
                <div class="dt-card-header">
                    <span class="dt-card-title">
                        <i class="fas fa-exclamation-circle me-2"></i>Downtime #${cardNum}
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-dt dt-remove-btn">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <div class="dt-card-body">
                    <div class="dt-field">
                        <label class="dt-label">Alasan</label>
                        <select name="downtimes[${rowId}][alasan]" class="form-select form-select-sm">
                            ${reasonOptions}
                        </select>
                    </div>
                    <div class="dt-field">
                        <label class="dt-label">Problem</label>
                        <textarea name="downtimes[${rowId}][problem]"
                            class="form-control form-control-sm"
                            rows="2"
                            placeholder="Deskripsi problem...">${data.problem || ''}</textarea>
                    </div>
                    <div class="dt-field">
                        <label class="dt-label">Action Plant</label>
                        <textarea name="downtimes[${rowId}][action_plant]"
                            class="form-control form-control-sm"
                            rows="2"
                            placeholder="Tindakan perbaikan...">${data.action_plant || ''}</textarea>
                    </div>
                    <div class="dt-field-row">
                        <div class="dt-field">
                            <label class="dt-label">Mulai</label>
                            <input type="datetime-local"
                                name="downtimes[${rowId}][mulai]"
                                value="${data.waktu_mulai ? data.waktu_mulai.replace(' ', 'T').substring(0, 16) : ''}"
                                class="form-control form-control-sm dt-mulai">
                        </div>
                        <div class="dt-field">
                            <label class="dt-label">Selesai</label>
                            <input type="datetime-local"
                                name="downtimes[${rowId}][selesai]"
                                value="${data.waktu_selesai ? data.waktu_selesai.replace(' ', 'T').substring(0, 16) : ''}"
                                class="form-control form-control-sm dt-selesai">
                        </div>
                    </div>
                    <div class="dt-field" style="max-width: 200px;">
                        <label class="dt-label">Durasi (menit)</label>
                        <input type="number"
                            name="downtimes[${rowId}][durasi]"
                            value="${data.durasi_menit || ''}"
                            class="form-control form-control-sm dt-durasi"
                            placeholder="0">
                    </div>
                </div>
            </div>`;

            // Sembunyikan empty state jika ada
            const emptyState = container.querySelector('#dt_empty_state');
            if (emptyState) emptyState.style.display = 'none';

            container.insertAdjacentHTML('beforeend', html);
        };

        window.addRejectRow = function(tableBody, type = '', qty = '') {
            const rejectTypes = [
                'start up', 'karantina', 'trial', 'camera', 'bottom putih', 'oval', 'flashing', 'short shoot',
                'kotor', 'beda warna', 'sampling qc', 'kontaminasi', 'black spot', 'gosong',
                'struktur tidak standard', 'inject poin tidak standard', 'bolong', 'bubble', 'berair', 'neck panjang'
            ];

            let typeOptions = '<option value="">-- Pilih Jenis --</option>';
            rejectTypes.forEach(t => {
                typeOptions += `<option value="${t.toUpperCase()}" ${type.toUpperCase() === t.toUpperCase() ? 'selected' : ''}>${t.toUpperCase()}</option>`;
            });

            const html = `
            <tr>
                <td>
                    <select class="form-select form-select-sm input-reject-type">
                        ${typeOptions}
                    </select>
                </td>
                <td><input type="number" class="form-control form-control-sm input-reject-qty" value="${qty}" placeholder="0"></td>
                <td class="text-center"><button type="button" class="btn btn-link btn-sm text-danger remove-reject-row">×</button></td>
            </tr>
        `;
            tableBody.insertAdjacentHTML('beforeend', html);
        };

        /**
         * Auto-fill batch_number dari nomor_mesin + tanggal + shift
         * Format: {shift}{nomor_mesin}/{DD.MM.YYYY}
         * Contoh: 1B01/08.09.2026
         */
        window.autofillBatchNumber = function(container) {
            if (!container) return;
            const nomorMesin = container.querySelector('.nomor_mesin_hidden')?.value || '';
            const tanggal    = container.querySelector('.tanggal_hidden')?.value || '';   // format YYYY-MM-DD
            if (!nomorMesin || !tanggal) return;

            // Baca shift (prefix) — strip "extra_" jika ada, ambil nomor belakangnya saja
            const shiftRaw = container.querySelector('.shift_hidden')?.value || '';
            const shiftVal = shiftRaw.startsWith('extra_') ? shiftRaw.replace('extra_', '') : shiftRaw;

            // Konversi YYYY-MM-DD → DD.MM.YYYY (pakai titik)
            const parts = tanggal.split('-');
            if (parts.length !== 3) return;
            const [y, m, d] = parts;
            const formatted  = `${d}.${m}.${y}`;
            const prefix     = shiftVal ? `${shiftVal}` : '';
            const batchValue = `${prefix}${nomorMesin}/${formatted}`;

            const batchInput = container.querySelector('.batch_number');
            if (batchInput) {
                batchInput.value = batchValue;
                console.log('[autofillBatchNumber] batch_number set to:', batchValue);
            }
        };

        /**
         * Kalkulasi KG per komposisi (hanya Becum)
         * - Hitung total kg dari 4 input
         * - Tampil warning jika total kg ≠ pemakaian, atau konfirmasi hijau jika sesuai
         * - TIDAK menyentuh kolom persen sama sekali — keduanya independen
         */
        window.updateKomposisiKg = function(container) {
            if (!container) return;
            const kgInputs = container.querySelectorAll('.komposisi-kg-input');
            if (!kgInputs.length) return; // bukan Becum, skip

            // 1. Hitung total kg
            let totalKg = 0;
            kgInputs.forEach(inp => { totalKg += parseFloat(inp.value) || 0; });

            // 2. Update display total kg
            const totalKgDisplay = container.querySelector('.komposisi-total-kg');
            if (totalKgDisplay) totalKgDisplay.textContent = totalKg.toFixed(3) + ' kg';

            // 3. Validasi: bandingkan total kg vs pemakaian — tampil warning atau konfirmasi
            const pemakaian = parseFloat(container.querySelector('.m_pemakaian')?.value) || 0;
            const warningEl = container.querySelector('.komposisi-kg-warning');
            if (!warningEl) return;

            if (pemakaian > 0 && totalKg > 0) {
                const diff = Math.abs(totalKg - pemakaian);
                if (diff > 0.001) {
                    // Tidak sesuai — kuning/merah
                    warningEl.style.cssText = 'display:block; font-size:0.72rem; border-radius:4px; padding:0.25rem 0.5rem; margin-top:2px; color:#856404; background:#fff3cd; border:1px solid #ffc107;';
                    warningEl.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Total KG (${totalKg.toFixed(3)}) belum sama dengan Pemakaian (${pemakaian} kg). Harus 100%.`;
                } else {
                    // Sesuai — hijau
                    warningEl.style.cssText = 'display:block; font-size:0.72rem; border-radius:4px; padding:0.25rem 0.5rem; margin-top:2px; color:#155724; background:#d4edda; border:1px solid #c3e6cb;';
                    warningEl.innerHTML = `<i class="fas fa-check-circle me-1"></i> Total KG sesuai dengan Pemakaian (${pemakaian} kg).`;
                }
            } else {
                warningEl.style.display = 'none';
                warningEl.innerHTML = '';
            }
        };

        /**
         * Update label nama di samping input KG saat nama komposisi berubah
         * Sinkronisasi real-time antara input .komposisi_nama_X dan label di KG section
         */
        window.syncKomposisiKgLabels = function(container) {
            if (!container) return;
            for (let i = 1; i <= 4; i++) {
                const namaInput = container.querySelector(`.komposisi_nama_${i}`);
                const kgLabel   = container.querySelector(`.komposisi-kg-label-${i}`);
                if (namaInput && kgLabel) {
                    const val = namaInput.value.trim();
                    kgLabel.textContent = val || `Nama ${i}`;
                }
            }
        };

        window.updateTotals = function(container) {
            if (!container) return;
            let totalHasil = 0;
            container.querySelectorAll('.jam-hasil').forEach(inp => totalHasil += (parseInt(inp.value) || 0));

            const isi = parseInt(container.querySelector('.isi')?.value) || 0;
            const totalPcs = totalHasil * isi;

            // Update display span total hasil
            const display = container.querySelector('.grand_total_hasil_display');
            if (display) display.innerText = totalHasil;

            // Total Bagus = Total hasil produksi (tidak dikurangi reject)
            const totalBagus = Math.max(0, totalPcs);

            // Update display + hidden untuk grand_total_bagus
            const gBagusDisplay = container.querySelector('.grand_total_bagus');
            if (gBagusDisplay) gBagusDisplay.value = totalBagus;
            const gBagusHidden = container.querySelector('.grand_total_bagus_hidden');
            if (gBagusHidden) gBagusHidden.value = totalBagus;

            // Calculate Sisa PO: total_spk - (kumulatif DB + total bagus form aktif ini) = sisa_po
            const totalSpkInput = container.querySelector('.total_spk');
            const sisaPoInput = container.querySelector('.sisa_po');
            const sisaPoHidden = container.querySelector('.sisa_po_hidden');
            if (totalSpkInput && sisaPoInput) {
                const totalSpk = parseInt(totalSpkInput.value) || 0;
                const kumulatifEl = container.querySelector('.total_bagus_kumulatif_hidden');
                const kumulatif = parseInt(kumulatifEl?.value) || 0;
                const sisaPo = Math.max(0, totalSpk - kumulatif - totalBagus);
                sisaPoInput.value = sisaPo;
                if (sisaPoHidden) sisaPoHidden.value = sisaPo;
                console.log('Auto-calculated Sisa PO:', sisaPo,
                    '(Total SPK:', totalSpk,
                    '- Kumulatif DB:', kumulatif,
                    '- Bagus form aktif:', totalBagus, ')');
            }
        };

        function normalizeMachineName(machineName) {
            if (!machineName) return '';
            const norm = machineName.toUpperCase().replace(/[\s-_]/g, '');
            if (norm.includes('BECUM')) return 'Becum';
            if (norm.includes('CCM1') || norm.includes('CCM01')) return 'CCM 1';
            if (norm.includes('CCM2') || norm.includes('CCM02')) return 'CCM 2';
            if (norm.includes('CCM')) return 'CCM 1'; // fallback generic CCM
            if (norm.includes('POWERJET1') || norm.includes('POWERJET01')) return 'Powerjet 1';
            if (norm.includes('POWERJET2') || norm.includes('POWERJET02')) return 'Powerjet 2';
            if (norm.includes('POWERJET')) return 'Powerjet 1'; // fallback
            if (norm.includes('IPS1') || norm.includes('IPS01')) return 'IPS 1';
            if (norm.includes('IPS2') || norm.includes('IPS02')) return 'IPS 2';
            if (norm.includes('IPS3') || norm.includes('IPS03')) return 'IPS 3';
            if (norm.includes('IPS4') || norm.includes('IPS04')) return 'IPS 4';
            return machineName;
        }

        function showProduksiLoading(container, message) {
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

        function hideProduksiLoading(container) {
            const overlay = container.querySelector('.spk-autofill-overlay');
            if (overlay) overlay.style.display = 'none';
        }

        function fillProduksiFromPpic(container, data, spk) {
            console.log('fillProduksiFromPpic called with:', {
                spk,
                data,
                container
            });
            spkAutofillLock = true;

            const nomorSpkSelect = container.querySelector('.nomor_spk');
            console.log('nomorSpkSelect found:', !!nomorSpkSelect);

            if (nomorSpkSelect) {
                let found = false;
                for (let i = 0; i < nomorSpkSelect.options.length; i++) {
                    if (nomorSpkSelect.options[i].value === spk) {
                        nomorSpkSelect.selectedIndex = i;
                        found = true;
                        break;
                    }
                }

                if (!found) {
                    const opt = document.createElement('option');
                    opt.value = spk;
                    opt.textContent = spk;
                    opt.dataset.mesin = data.nama_mesin || '';
                    opt.dataset.produk = data.nama_produk || '';
                    opt.dataset.brand = data.brand || '';
                    opt.dataset.grade = data.grade || '';
                    opt.dataset.warna = data.warna || '';
                    opt.dataset.nomorMesin = data.nomor_mesin || '';
                    opt.dataset.operator = data.operator || '';
                    opt.dataset.targett = data.targett || '';
                    opt.dataset.shif = data.shif || '';
                    nomorSpkSelect.appendChild(opt);
                    nomorSpkSelect.value = spk;
                }

                const selectedOpt = nomorSpkSelect.options[nomorSpkSelect.selectedIndex];
                if (selectedOpt) {
                    selectedOpt.dataset.mesin = data.nama_mesin || '';
                    selectedOpt.dataset.produk = data.nama_produk || '';
                    selectedOpt.dataset.brand = data.brand || '';
                    selectedOpt.dataset.grade = data.grade || '';
                    selectedOpt.dataset.warna = data.warna || '';
                    selectedOpt.dataset.nomorMesin = data.nomor_mesin || '';
                    selectedOpt.dataset.operator = data.operator || '';
                    selectedOpt.dataset.targett = data.targett || '';
                    selectedOpt.dataset.shif = data.shif || '';
                    selectedOpt.dataset.packing = data.packing || '';
                }

                if (typeof jQuery !== 'undefined' && jQuery(nomorSpkSelect).hasClass('select2-hidden-accessible')) {
                    jQuery(nomorSpkSelect).val(spk).trigger('change.select2');
                }
            }

            const checkSpkBaru = container.querySelector('.check_spk_baru');
            const spkSelectWrap = container.querySelector('.spk_select_wrapper');
            const spkInputWrap = container.querySelector('.spk_input_wrapper');
            const spkInput = container.querySelector('.nomor_spk_baru');
            if (checkSpkBaru && spkSelectWrap && spkInputWrap) {
                checkSpkBaru.checked = false;
                spkSelectWrap.style.display = 'block';
                spkInputWrap.style.display = 'none';
                if (nomorSpkSelect) {
                    nomorSpkSelect.setAttribute('name', 'nomor_spk');
                    nomorSpkSelect.setAttribute('required', true);
                }
                if (spkInput) {
                    spkInput.removeAttribute('name');
                    spkInput.removeAttribute('required');
                }
            }

            const source = data.ppic || data.last || data.legacy || data.machine_data || data;
            console.log('Source data for autofill:', source);

            const namaMesinEl = container.querySelector('.nama_mesin');
            if (namaMesinEl) {
                const mesinValue = source.nama_mesin || '';
                console.log('Setting nama_mesin to:', mesinValue);
                namaMesinEl.value = normalizeMachineName(mesinValue);
            } else {
                console.log('nama_mesin element not found');
            }

            // Set Nama Produk - visible disabled field + hidden input
            const namaProdukEl = container.querySelector('.nama_produk');
            const namaProdukHidden = container.querySelector('.nama_produk_hidden');
            if (namaProdukEl && namaProdukHidden) {
                const produkValue = source.nama_produk || source.produk_galon || source.produk_cap_galon || source.produk_preform || '';
                console.log('Setting nama_produk to:', produkValue);
                namaProdukEl.value = produkValue;
                namaProdukHidden.value = produkValue;
            }

            // Set Brand - visible disabled field + hidden input
            const brandEl = container.querySelector('.brand');
            const brandHidden = container.querySelector('.brand_hidden');
            if (brandEl && brandHidden) {
                const brandValue = source.brand || '';
                console.log('Setting brand to:', brandValue);
                brandEl.value = brandValue;
                brandHidden.value = brandValue;
            }

            // Set Warna - visible disabled field + hidden input
            const warnaEl = container.querySelector('.warna');
            const warnaHidden = container.querySelector('.warna_hidden');
            if (warnaEl && warnaHidden) {
                const warnaValue = source.warna || '';
                console.log('Setting warna to:', warnaValue);
                warnaEl.value = warnaValue;
                warnaHidden.value = warnaValue;
            }

            // Set Grade - visible disabled field + hidden input
            const gradeEl = container.querySelector('.grade');
            const gradeHidden = container.querySelector('.grade_hidden');
            if (gradeEl && gradeHidden) {
                const gradeValue = source.grade || '';
                console.log('Setting grade to:', gradeValue);
                gradeEl.value = gradeValue;
                gradeHidden.value = gradeValue;
            }

            // Set Nomor Mesin - visible disabled field + hidden input
            const nomorMesinEl = container.querySelector('.nomor_mesin');
            const nomorMesinHidden = container.querySelector('.nomor_mesin_hidden');
            if (nomorMesinEl && nomorMesinHidden) {
                console.log('Setting nomor_mesin to:', source.nomor_mesin);
                nomorMesinEl.value = source.nomor_mesin || '';
                nomorMesinHidden.value = source.nomor_mesin || '';
            }

            // DO NOT overwrite operator - keep the initial username value
            // Only update if SPK has specific operator data
            const operatorEl = container.querySelector('.operator');
            const operatorHidden = container.querySelector('.operator_hidden');
            // Operator TIDAK di-override dari SPK data — selalu pakai username session
            if (operatorEl && operatorHidden) {
                console.log('Keeping operator from session:', operatorEl.value);
                // Keep existing value (sudah di-set dari session di PHP)
            }

            // Set Target - visible disabled field + hidden input
            const targetEl = container.querySelector('.target');
            const targetHidden = container.querySelector('.target_hidden');
            if (targetEl && targetHidden) {
                console.log('Setting target to:', source.targett || source.target);
                const targetValue = source.targett || source.target || '';
                targetEl.value = targetValue;
                targetHidden.value = targetValue;
            }

            // Set Total SPK - visible disabled field + hidden input
            const totalSpkEl = container.querySelector('.total_spk');
            const totalSpkHidden = container.querySelector('.total_spk_hidden');
            if (totalSpkEl && totalSpkHidden) {
                console.log('Setting total_spk to:', source.total_spk);
                const rawTotalSpk = String(source.total_spk || '');
                // Strip non-numeric characters: hapus titik ribuan, koma, spasi, huruf (mis: "5.000 pcs" → "5000")
                const cleanTotalSpk = rawTotalSpk
                    .replace(/\./g, '') // hapus titik ribuan
                    .replace(/,/g, '') // hapus koma
                    .replace(/[^0-9]/g, ''); // hapus semua non-angka (termasuk "pcs", spasi, dll)
                const totalSpkValue = cleanTotalSpk !== '' ? cleanTotalSpk : '';
                totalSpkEl.value = totalSpkValue;
                totalSpkHidden.value = totalSpkValue;
                console.log('✓ total_spk synced:', {
                    raw: rawTotalSpk,
                    clean: totalSpkValue,
                    visible: totalSpkEl.value,
                    hidden: totalSpkHidden.value
                });
            } else {
                console.warn('⚠ total_spk elements not found - visible:', !!totalSpkEl, 'hidden:', !!totalSpkHidden);
            }

            // Set Komposisi dari revisi PPIC - textarea field
            const komposisiEl = container.querySelector('.komposisi');
            if (komposisiEl) {
                const komposisiValue = source.revisi ?? source.komposisi ?? '';
                komposisiEl.value = komposisiValue;
                komposisiEl.textContent = komposisiValue; // For textarea content
                console.log('✓ komposisi synced from PPIC:', komposisiValue);
            }

            // Set Shift - visible disabled field + hidden input
            // GUARD: jangan override shift jika ini mode edit (shift sudah set dari DB)
            const shiftEl = container.querySelector('.shift');
            const shiftHidden = container.querySelector('.shift_hidden');
            if (shiftEl && shiftHidden) {
                const isEditMode = shiftEl.dataset.isEdit === '1';
                const shiftValue = source.shif || source.shift || '';
                if (shiftValue && !isEditMode) {
                    console.log('Setting shift to:', shiftValue);
                    shiftEl.value = shiftValue;
                    shiftHidden.value = shiftValue;
                    // TRIGGER CHANGE EVENT untuk auto-fill TTD
                    shiftEl.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                } else if (isEditMode) {
                    console.log('Edit mode: skipping shift override, keeping DB value:', shiftEl.value);
                }
            }

            // Set Packing - visible disabled field + hidden input
            const packingEl = container.querySelector('.packing');
            const packingHidden = container.querySelector('.packing_hidden');
            if (packingEl && packingHidden) {
                const packingValue = source.packing || '';
                if (packingValue) {
                    console.log('Setting packing to:', packingValue);
                    packingEl.value = packingValue;
                    packingHidden.value = packingValue;
                } else {
                    console.log('No packing value found in source data');
                }
            }

            // Set Tanggal - visible disabled field + hidden input
            const tanggalHidden = container.querySelector('.tanggal_hidden');
            const tanggalDisplay = container.querySelector('input[name="tanggal"][disabled]');
            if (tanggalHidden) {
                // Gunakan local date, bukan UTC
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const localDate = `${year}-${month}-${day}`;
                console.log('Setting tanggal to:', localDate);
                tanggalHidden.value = localDate;
                if (tanggalDisplay) {
                    tanggalDisplay.value = localDate;
                }
            }

            spkAutofillLock = false;
            console.log('fillProduksiFromPpic complete');
            // Panggil updateTotals agar sisa_po langsung dihitung setelah semua field terisi
            updateTotals(container);
            // Auto-fill batch_number dari nomor_mesin + tanggal
            autofillBatchNumber(container);
        }

        function fillFromSpkOption(select, container) {
            console.log('fillFromSpkOption called');
            console.log('select element:', select);
            console.log('container element:', container);

            if (!select || !select.options) {
                console.log('Select element or options not available');
                return;
            }

            const opt = select.options[select.selectedIndex];
            console.log('Selected option:', opt);

            if (!opt || !opt.value) {
                console.log('No option or option value');
                return;
            }

            // Get all data attributes - handle both camelCase (dataset) and kebab-case (getAttribute)
            const mesin = opt.dataset.mesin || opt.getAttribute('data-mesin') || '';
            const produk = opt.dataset.produk || opt.getAttribute('data-produk') || '';
            const brand = opt.dataset.brand || opt.getAttribute('data-brand') || '';
            const grade = opt.dataset.grade || opt.getAttribute('data-grade') || '';
            const warna = opt.dataset.warna || opt.getAttribute('data-warna') || '';
            const nomorMesin = opt.dataset.nomorMesin || opt.getAttribute('data-nomor-mesin') || '';
            const operator = opt.dataset.operator || opt.getAttribute('data-operator') || '';
            const targett = opt.dataset.targett || opt.getAttribute('data-targett') || '';
            const shif = opt.dataset.shif || opt.getAttribute('data-shif') || '';
            const packing = opt.dataset.packing || opt.getAttribute('data-packing') || '';
            let totalSpk = opt.dataset.totalSpk || opt.getAttribute('data-total-spk') || '';
            const revisi = opt.dataset.revisi || opt.getAttribute('data-revisi') || '';

            console.log('Extracted autofill data:', {
                mesin,
                produk,
                brand,
                grade,
                warna,
                nomorMesin,
                operator,
                targett,
                shif,
                packing,
                totalSpk
            });

            // FALLBACK: If total_spk is empty from dropdown, fetch from PPIC via AJAX
            if (!totalSpk || totalSpk === '') {
                console.log('total_spk empty from dropdown, fetching from PPIC...');
                showProduksiLoading(container, 'Memuat Total SPK dari PPIC...');

                fetch('<?= base_url('produksi/getPpicSpkDetail') ?>?spk=' + encodeURIComponent(opt.value))
                    .then(r => r.json())
                    .then(data => {
                        console.log('PPIC SPK Detail response:', data);
                        hideProduksiLoading(container);

                        if (data.ppic && data.ppic.total_spk) {
                            totalSpk = data.ppic.total_spk;
                            console.log('Got total_spk from PPIC API:', totalSpk);
                        }

                        // Set total_bagus_kumulatif_hidden SEBELUM fillProduksiFromPpic
                        // agar updateTotals() langsung bisa hitung sisa_po dengan benar
                        const kumulatifEl = container.querySelector('.total_bagus_kumulatif_hidden');
                        if (kumulatifEl) {
                            kumulatifEl.value = data.total_bagus_kumulatif ?? 0;
                            console.log('Set total_bagus_kumulatif_hidden:', kumulatifEl.value);
                        }

                        // Display komposisi dari PPIC
                        console.log('Komposisi data received:', data.komposisi);
                        displayKomposisi(container, data.komposisi || []);

                        // Now fill the form with all data including total_spk from API
                        fillProduksiFromPpic(container, {
                            nama_mesin: mesin || data.ppic?.nama_mesin,
                            nama_produk: produk || data.ppic?.nama_produk,
                            brand: brand || data.ppic?.brand,
                            grade: grade || data.ppic?.grade,
                            warna: warna || data.ppic?.warna,
                            nomor_mesin: nomorMesin || data.ppic?.nomor_mesin,
                            operator: operator || data.ppic?.operator,
                            targett: targett || data.ppic?.targett,
                            shif: shif || data.ppic?.shif,
                            packing: packing || data.ppic?.packing,
                            total_spk: totalSpk,
                            revisi: data.ppic?.revisi || data.revisi || revisi
                        }, opt.value);
                    })
                    .catch(err => {
                        console.error('Error fetching PPIC SPK detail:', err);
                        hideProduksiLoading(container);

                        // Still fill with available data even if API fails
                        fillProduksiFromPpic(container, {
                            nama_mesin: mesin,
                            nama_produk: produk,
                            brand: brand,
                            grade: grade,
                            warna: warna,
                            nomor_mesin: nomorMesin,
                            operator: operator,
                            targett: targett,
                            shif: shif,
                            packing: packing,
                            total_spk: totalSpk,
                            revisi: revisi
                        }, opt.value);
                    });

                return; // Exit early, will continue in async callback
            }

            // If total_spk is available from dropdown, fill immediately
            fillProduksiFromPpic(container, {
                nama_mesin: mesin,
                nama_produk: produk,
                brand: brand,
                grade: grade,
                warna: warna,
                nomor_mesin: nomorMesin,
                operator: operator,
                targett: targett,
                shif: shif,
                packing: packing,
                total_spk: totalSpk,
                revisi: revisi
            }, opt.value);
        }

        /**
         * Alternative method to get data when Select2 wraps the select
         */
        function fillFromSpkOptionAlt(value, container) {
            // Find the underlying select and get the option
            const select = container.querySelector('.nomor_spk');
            if (!select) return;

            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].value === value) {
                    const opt = select.options[i];
                    const mesin = opt.dataset.mesin || opt.getAttribute('data-mesin') || '';
                    const produk = opt.dataset.produk || opt.getAttribute('data-produk') || '';
                    const brand = opt.dataset.brand || opt.getAttribute('data-brand') || '';
                    const grade = opt.dataset.grade || opt.getAttribute('data-grade') || '';
                    const warna = opt.dataset.warna || opt.getAttribute('data-warna') || '';
                    const nomorMesin = opt.dataset.nomorMesin || opt.getAttribute('data-nomor-mesin') || '';
                    const operator = opt.dataset.operator || opt.getAttribute('data-operator') || '';
                    const targett = opt.dataset.targett || opt.getAttribute('data-targett') || '';
                    const shif = opt.dataset.shif || opt.getAttribute('data-shif') || '';
                    const packing = opt.dataset.packing || opt.getAttribute('data-packing') || '';
                    let totalSpk = opt.dataset.totalSpk || opt.getAttribute('data-total-spk') || '';
                    const revisi = opt.dataset.revisi || opt.getAttribute('data-revisi') || '';

                    console.log('Alt autofill data:', {
                        mesin,
                        produk,
                        brand,
                        grade,
                        warna,
                        nomorMesin,
                        operator,
                        targett,
                        shif,
                        packing,
                        totalSpk
                    });

                    // FALLBACK: If total_spk is empty from dropdown, fetch from PPIC via AJAX
                    if (!totalSpk || totalSpk === '') {
                        console.log('Alt method: total_spk empty, fetching from PPIC...');
                        showProduksiLoading(container, 'Memuat Total SPK dari PPIC...');

                        fetch('<?= base_url('produksi/getPpicSpkDetail') ?>?spk=' + encodeURIComponent(value))
                            .then(r => r.json())
                            .then(data => {
                                console.log('PPIC SPK Detail response (alt):', data);
                                hideProduksiLoading(container);

                                if (data.ppic && data.ppic.total_spk) {
                                    totalSpk = data.ppic.total_spk;
                                    console.log('Got total_spk from PPIC API (alt):', totalSpk);
                                }

                                // Set total_bagus_kumulatif_hidden SEBELUM fillProduksiFromPpic
                                // agar updateTotals() langsung bisa hitung sisa_po dengan benar
                                const kumulatifEl = container.querySelector('.total_bagus_kumulatif_hidden');
                                if (kumulatifEl) {
                                    kumulatifEl.value = data.total_bagus_kumulatif ?? 0;
                                    console.log('Set total_bagus_kumulatif_hidden (alt):', kumulatifEl.value);
                                }

                                // Display komposisi dari PPIC (alt method)
                                console.log('Komposisi data received (alt):', data.komposisi);
                                displayKomposisi(container, data.komposisi || []);

                                // Now fill the form with all data including total_spk from API
                                fillProduksiFromPpic(container, {
                                    nama_mesin: mesin || data.ppic?.nama_mesin,
                                    nama_produk: produk || data.ppic?.nama_produk,
                                    brand: brand || data.ppic?.brand,
                                    grade: grade || data.ppic?.grade,
                                    warna: warna || data.ppic?.warna,
                                    nomor_mesin: nomorMesin || data.ppic?.nomor_mesin,
                                    operator: operator || data.ppic?.operator,
                                    targett: targett || data.ppic?.targett,
                                    shif: shif || data.ppic?.shif,
                                    packing: packing || data.ppic?.packing,
                                    total_spk: totalSpk,
                                    revisi: data.ppic?.revisi || data.revisi || revisi
                                }, value);
                            })
                            .catch(err => {
                                console.error('Error fetching PPIC SPK detail (alt):', err);
                                hideProduksiLoading(container);

                                // Still fill with available data even if API fails
                                fillProduksiFromPpic(container, {
                                    nama_mesin: mesin,
                                    nama_produk: produk,
                                    brand: brand,
                                    grade: grade,
                                    warna: warna,
                                    nomor_mesin: nomorMesin,
                                    operator: operator,
                                    targett: targett,
                                    shif: shif,
                                    packing: packing,
                                    total_spk: totalSpk,
                                    revisi: revisi
                                }, value);
                            });

                        return; // Exit early, will continue in async callback
                    }

                    // If total_spk is available from dropdown, fill immediately
                    fillProduksiFromPpic(container, {
                        nama_mesin: mesin,
                        nama_produk: produk,
                        brand: brand,
                        grade: grade,
                        warna: warna,
                        nomor_mesin: nomorMesin,
                        operator: operator,
                        targett: targett,
                        shif: shif,
                        packing: packing,
                        total_spk: totalSpk,
                        revisi: revisi
                    }, value);
                    break;
                }
            }
        }

        function applyProduksiLastData(container, data) {
            const d = data.last || data.legacy;
            if (!d) return;

            if (d.nomor_mesin) {
                const el = container.querySelector('.nomor_mesin');
                if (el) el.value = d.nomor_mesin;
            }
            if (d.batch_number) {
                const el = container.querySelector('.batch_number');
                if (el) el.value = d.batch_number;
            }
            if (d.grup) {
                const el = container.querySelector('.grup');
                if (el) el.value = d.grup;
            }
            if (d.packing) {
                const el = container.querySelector('.packing');
                const elHidden = container.querySelector('.packing_hidden');
                if (el) el.value = d.packing;
                if (elHidden) elHidden.value = d.packing;
                console.log('Set packing from last data:', d.packing);
            }
            if (d.cycle_time) {
                const el = container.querySelector('.cycle_time');
                if (el) el.value = d.cycle_time;
            }
            if (d.target) {
                const el = container.querySelector('.target');
                const elHidden = container.querySelector('.target_hidden');
                if (el) el.value = d.target;
                if (elHidden) elHidden.value = d.target;
            }
            if (d.total_spk) {
                const el = container.querySelector('.total_spk');
                const elHidden = container.querySelector('.total_spk_hidden');
                if (el) el.value = d.total_spk;
                if (elHidden) elHidden.value = d.total_spk;
                console.log('Set total_spk from last data:', d.total_spk);
            }
            if (d.isi) {
                const el = container.querySelector('.isi');
                if (el) el.value = d.isi;
            }
            // Operator TIDAK di-override dari last data — selalu pakai username session
            // if (d.operator) { ... } — dihapus intentionally
            if (d.shift || d.shif) {
                const el = container.querySelector('.shift');
                const elHidden = container.querySelector('.shift_hidden');
                if (el) el.value = d.shift || d.shif;
                if (elHidden) elHidden.value = d.shift || d.shif;
            }
            if (d.merek_kode) {
                const el = container.querySelector('.merek_kode');
                if (el) el.value = d.merek_kode;
            }
            if (d.m_pemakaian || d.pemakaian) {
                const el = container.querySelector('.m_pemakaian');
                if (el) el.value = d.m_pemakaian || d.pemakaian;
            }
        }

        function loadProduksiSpkData(container, spk) {
            if (!spk || !container) return;

            const select = container.querySelector('.nomor_spk');
            if (!select) return;

            // Try to fetch last data from server (optional enhancement)
            // For now, just do basic autofill which already happened in fillFromSpkOption

            showProduksiLoading(container, 'Memuat data SPK...');

            fetch('<?= base_url('produksi/get_last_data') ?>/' + encodeURIComponent(spk))
                .then(r => r.json())
                .then(data => {
                    hideProduksiLoading(container);

                    if (data.last || data.legacy) {
                        applyProduksiLastData(container, data);
                    }
                })
                .catch(() => {
                    hideProduksiLoading(container);
                    // Silently fail - autofill from option data already worked
                });
        }

        window.loadProduksiSpkData = loadProduksiSpkData;

        window.loadDynamicShifts = function(container, mesinName, nomorMesin, noSpk) {
            if (!container || !mesinName || !nomorMesin) return;
            noSpk = noSpk || '';

            const shiftSelect = container.querySelector('#shift_select') || container.querySelector('.shift-selector');
            if (!shiftSelect) {
                console.log('Shift select not found');
                return;
            }

            // Extract mesin slug from name (e.g., "Becum" -> "becum", "IPS 1" -> "ips1")
            const mesinSlug = (mesinName || '')
                .toLowerCase()
                .replace(/\s+/g, '')
                .replace(/[^\w]/g, '');

            console.log('loadDynamicShifts - mesinName:', mesinName, 'slug:', mesinSlug, 'nomorMesin:', nomorMesin);

            const apiUrl = '<?= base_url('produksi/get-shifts') ?>' +
                '?mesin=' + encodeURIComponent(mesinSlug) +
                '&nomor_mesin=' + encodeURIComponent(nomorMesin) +
                (noSpk ? '&no_spk=' + encodeURIComponent(noSpk) : '');

            console.log('Fetching shifts from:', apiUrl);

            fetch(apiUrl)
                .then(r => r.json())
                .then(data => {
                    console.log('Shifts response:', data);

                    if (!data.shifts || Object.keys(data.shifts).length === 0) {
                        console.log('No shifts returned, keeping defaults');
                        return;
                    }

                    // Save current selected value
                    const currentValue = shiftSelect.value;

                    // Clear all options except the placeholder
                    const options = Array.from(shiftSelect.options);
                    options.forEach((opt, idx) => {
                        if (idx > 0) opt.remove();
                    });

                    // Add shifts from response
                    Object.entries(data.shifts).forEach(([key, label]) => {
                        const option = document.createElement('option');
                        option.value = key;
                        option.textContent = label;
                        // Store original label in data attribute for reference
                        option.dataset.label = label;
                        shiftSelect.appendChild(option);
                    });

                    // Restore or keep first shift
                    if (currentValue && shiftSelect.querySelector(`option[value="${currentValue}"]`)) {
                        shiftSelect.value = currentValue;
                    }

                    // Sync shift_hidden with the current value after options loaded
                    const shiftHiddenInContainer = container.querySelector ? container.querySelector('.shift_hidden') : document.querySelector('.shift_hidden');
                    if (shiftHiddenInContainer && shiftSelect.value) {
                        shiftHiddenInContainer.value = shiftSelect.value;
                        console.log('loadDynamicShifts: synced shift_hidden to:', shiftSelect.value);
                    }

                    console.log('Shift dropdown updated with', Object.keys(data.shifts).length, 'shifts');
                })
                .catch(err => {
                    console.error('Error loading shifts:', err);
                    // Keep defaults on error
                });
        };

        window.loadTargetByShift = function(container, mesinName, nomorMesin, shift, noSpk) {
            if (!container || !mesinName || !nomorMesin || !shift) return;
            noSpk = noSpk || '';

            const targetInput = container.querySelector('.target');
            const targetHidden = container.querySelector('.target_hidden');
            if (!targetInput && !targetHidden) {
                console.log('Target inputs not found');
                return;
            }

            const mesinSlug = (mesinName || '')
                .toLowerCase()
                .replace(/\s+/g, '')
                .replace(/[^\w]/g, '');

            console.log('loadTargetByShift - mesinName:', mesinName, 'slug:', mesinSlug, 'shift:', shift, 'shift type:', typeof shift);

            // For extra shifts, get the label from the option data-attribute
            let shiftParam = shift;
            if (shift.startsWith('extra_')) {
                const shiftSelector = container.querySelector('.shift-selector');
                if (shiftSelector) {
                    const selectedOption = shiftSelector.options[shiftSelector.selectedIndex];
                    const label = selectedOption?.dataset?.label || selectedOption?.textContent || shift;
                    // Gunakan format "extra:" + rawurlencode(label) agar aman dari semua karakter spesial
                    // encodeURIComponent handles (, ), spasi, dan karakter lainnya
                    shiftParam = 'extra:' + encodeURIComponent(label.trim());
                    console.log('Extra shift detected - label:', label, 'param:', shiftParam);
                }
            }

            const apiUrl = '<?= base_url('produksi/get-target-by-shift') ?>' +
                '?mesin=' + encodeURIComponent(mesinSlug) +
                '&nomor_mesin=' + encodeURIComponent(nomorMesin) +
                '&shift=' + encodeURIComponent(shiftParam) +
                (noSpk ? '&no_spk=' + encodeURIComponent(noSpk) : '');

            console.log('Fetching target from:', apiUrl);

            fetch(apiUrl)
                .then(r => r.json())
                .then(data => {
                    console.log('Target response:', data);

                    if (data.target === null || data.target === undefined) {
                        console.log('No target found for this shift');
                        console.log('Debug info:', data.debug || '');
                        return;
                    }

                    // Set target in both visible and hidden inputs
                    if (targetInput) {
                        targetInput.value = data.target;
                    }
                    if (targetHidden) {
                        targetHidden.value = data.target;
                    }

                    console.log('Target auto-filled to:', data.target);
                })
                .catch(err => {
                    console.error('Error loading target:', err);
                });
        };

        window.openProduksiAddWithSpk = function(spk) {
            const addModalEl = document.getElementById('addDataModal');
            if (!addModalEl || !spk) return;

            const container = addModalEl.querySelector('#formAddLaporan');
            if (!container) return;

            const modal = bootstrap.Modal.getOrCreateInstance ?
                bootstrap.Modal.getOrCreateInstance(addModalEl) :
                new bootstrap.Modal(addModalEl);

            const onShown = function() {
                addModalEl.removeEventListener('shown.bs.modal', onShown);
                loadProduksiSpkData(container, spk);
            };
            addModalEl.addEventListener('shown.bs.modal', onShown);
            modal.show();
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
                if (tableBody.querySelector(`tr[data-jam="${jam}"]`)) {
                    alert('Jam ini sudah ada');
                    return;
                }
                addJamRow(tableBody, jam);
                updateTotals(container);
            }

            // Add Downtime Row
            if (e.target.closest('.btn_add_dt')) {
                const container = e.target.closest('.modal-body').querySelector('#dt_list_container');
                addDtRow(container);
            }

            // Remove Row
            if (e.target.classList.contains('remove-jam')) {
                const container = e.target.closest('.modal-body');
                e.target.closest('tr').remove();
                if (container) updateTotals(container);
            }
            if (e.target.closest('.remove-dt')) {
                e.target.closest('.dt-card').remove();
            }

            // SPK Badge Click -> Open Add Modal with PPIC Data
            if (e.target.closest('.btn-spk-produksi-autofill') || e.target.closest('.btn-spk-autofill')) {
                const btn = e.target.closest('.btn-spk-produksi-autofill') || e.target.closest('.btn-spk-autofill');
                const spk = (btn.dataset.spk || '').trim();
                if (!spk) return;
                openProduksiAddWithSpk(spk);
            }

            // Reject Modal Trigger
            if (e.target.closest('.btn_open_reject_modal')) {
                const btn = e.target.closest('.btn_open_reject_modal');
                activeRejectStorage = document.getElementById(btn.dataset.targetStorage);

                const tableBody = document.querySelector('#table_reject_modal tbody');
                tableBody.innerHTML = '';

                if (activeRejectStorage) {
                    const inputs = activeRejectStorage.querySelectorAll('input');
                    if (inputs.length > 0) {
                        inputs.forEach(inp => {
                            addRejectRow(tableBody, inp.dataset.type, inp.value);
                        });
                    } else {
                        addRejectRow(tableBody);
                    }
                } else {
                    addRejectRow(tableBody);
                }
            }

            // Add Reject Row
            if (e.target.id === 'btn_add_reject_row' || e.target.closest('#btn_add_reject_row')) {
                const tableBody = document.querySelector('#table_reject_modal tbody');
                addRejectRow(tableBody);
            }

            if (e.target.closest('.btn-add-reject-row-inline')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-add-reject-row-inline');
                const scopedRoot = btn.closest('.modal-body') || document;
                addRejectInlineRow(scopedRoot);
            }

            if (e.target.closest('.btn-remove-reject')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-remove-reject');
                btn.closest('tr').remove();
                updateRejectInlineTotals(btn.closest('.modal-body') || document);
            }

            // Remove Reject Row
            if (e.target.classList.contains('remove-reject-row')) {
                e.target.closest('tr').remove();
            }

            // Tambah sub-baris reject di dalam baris jam
            if (e.target.closest('.btn-add-jam-reject')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-add-jam-reject');
                const rejectList = btn.previousElementSibling; // .jam-reject-list
                let rejectOptions = '<option value="">-- Jenis --</option>';
                getRejectTypesFromData().forEach(name => {
                    rejectOptions += `<option value="${escHtml(name)}">${escHtml(name)}</option>`;
                });
                const newRow = document.createElement('div');
                newRow.className = 'jam-reject-row d-flex gap-1 mb-1 align-items-center';
                newRow.innerHTML = `
                    <select class="form-select form-select-sm jam-reject-type flex-grow-1"
                        style="font-size: 0.75rem; min-width: 110px;">
                        ${rejectOptions}
                    </select>
                    <input type="number" min="0"
                        class="form-control form-control-sm jam-reject-jumlah"
                        placeholder="0" style="width: 60px; font-size: 0.75rem;">
                    <button type="button"
                        class="btn btn-sm btn-outline-danger btn-remove-jam-reject"
                        style="padding: 0.2rem 0.4rem; font-size: 0.7rem;">✕</button>`;
                rejectList.appendChild(newRow);
                const container = btn.closest('.modal-body') || document;
                syncJamRejectToRejectSection(container);
            }

            // Hapus sub-baris reject
            if (e.target.closest('.btn-remove-jam-reject')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-remove-jam-reject');
                const row = btn.closest('.jam-reject-row');
                const container = btn.closest('.modal-body') || document;
                row.remove();
                syncJamRejectToRejectSection(container);
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

                        // Cari modal-body di dalam container (AJAX inject modal-header + modal-body + modal-footer)
                        const modalBody = container.querySelector('.modal-body') || container;

                        // Hitung ulang total jam & bagus
                        if (typeof updateTotals === 'function') updateTotals(modalBody);

                        // Init reject section: pasang event listeners + hitung total reject
                        if (typeof initializeRejectSection === 'function') initializeRejectSection(modalBody);

                        // Hitung ulang total reject (memanggil updateBagusTotal juga)
                        if (typeof updateRejectInlineTotals === 'function') updateRejectInlineTotals(modalBody);

                        // RESTORE EDIT DATA - SPK, Shift, TTD
                        setTimeout(() => {
                            console.log('=== POST-AJAX EDIT FORM INIT ===');

                            // Initialize komposisi listeners AFTER form loaded via AJAX
                            if (typeof initKomposisiListeners === 'function') {
                                console.log('[EDIT] Initializing komposisi listeners');
                                initKomposisiListeners(container);
                            }

                            // Restore SPK selection and populate related fields
                            const spkSelect = container.querySelector('.nomor_spk');
                            if (spkSelect && spkSelect.value) {
                                console.log('Restoring SPK:', spkSelect.value);
                                const changeEvent = new Event('change', {
                                    bubbles: true
                                });
                                spkSelect.dispatchEvent(changeEvent);
                            }

                            // Fetch total_bagus_kumulatif untuk mode edit (exclude row ini sendiri)
                            // agar sisa_po tidak double-count produksi shift yang sedang diedit
                            if (spkSelect && spkSelect.value) {
                                const editId = btn.dataset.id;
                                fetch('<?= base_url('produksi/getPpicSpkDetail') ?>?spk=' +
                                        encodeURIComponent(spkSelect.value) +
                                        '&exclude_id=' + encodeURIComponent(editId))
                                    .then(r => r.json())
                                    .then(data => {
                                        const kumulatifEl = container.querySelector('.total_bagus_kumulatif_hidden');
                                        if (kumulatifEl && data.total_bagus_kumulatif !== undefined) {
                                            kumulatifEl.value = data.total_bagus_kumulatif;
                                            console.log('[EDIT] total_bagus_kumulatif set:', data.total_bagus_kumulatif);
                                            // Recalculate sisa_po setelah kumulatif terisi
                                            const modalBody = container.querySelector('.modal-body') || container;
                                            updateTotals(modalBody);
                                        }
                                    })
                                    .catch(err => console.warn('[EDIT] Gagal fetch kumulatif:', err));
                            }

                            // Load dynamic shifts based on machine (this will include extra shifts)
                            const mesinHiddenField = container.querySelector('input[name="nama_mesin"]');
                            const nomorMesinField = container.querySelector('input[name="nomor_mesin"]');
                            if (mesinHiddenField && nomorMesinField) {
                                const mesinName = mesinHiddenField.value;
                                const nomorMesin = nomorMesinField.value;
                                if (mesinName && nomorMesin) {
                                    console.log('Loading dynamic shifts from AJAX - mesin:', mesinName, 'nomor_mesin:', nomorMesin);
                                    loadDynamicShifts(container, mesinName, nomorMesin);
                                }
                            }

                            // Restore Shift selection explicitly
                            const shiftSelector = container.querySelector('.shift-selector');
                            if (shiftSelector) {
                                const savedShiftValue = shiftSelector.dataset.savedShift || shiftSelector.value;
                                console.log('Shift selector current value:', savedShiftValue, 'type:', typeof savedShiftValue);
                                console.log('All shift options:', Array.from(shiftSelector.options).map(o => ({
                                    value: o.value,
                                    text: o.text,
                                    selected: o.selected
                                })));

                                // Make sure the value is properly set (with delay to allow API to load shifts first)
                                setTimeout(() => {
                                    if (savedShiftValue && String(savedShiftValue).trim() !== '') {
                                        shiftSelector.value = String(savedShiftValue).trim();
                                        console.log('After explicit set - Shift value:', shiftSelector.value, 'Selected:', shiftSelector.options[shiftSelector.selectedIndex]?.text);
                                    }
                                }, 1000); // Wait longer for API to complete and render options
                            }

                            // Restore TTD display
                            const ttdHiddenInput = container.querySelector('#auto-user-ttd');
                            if (ttdHiddenInput && ttdHiddenInput.value) {
                                console.log('Restoring saved TTD:', ttdHiddenInput.value);
                                const ttdPath = ttdHiddenInput.value;
                                const preview = container.querySelector('#auto-ttd-preview');
                                const placeholder = container.querySelector('#auto-ttd-placeholder');

                                if (preview && ttdPath) {
                                    let imageUrl;
                                    if (ttdPath.startsWith('uploads/')) {
                                        imageUrl = '<?= base_url() ?>' + ttdPath;
                                    } else {
                                        imageUrl = '<?= base_url('uploads/ttd/') ?>' + ttdPath;
                                    }

                                    console.log('Loading TTD from:', imageUrl);
                                    preview.src = imageUrl;
                                    preview.style.display = 'block';
                                    if (placeholder) placeholder.style.display = 'none';

                                    preview.onerror = function() {
                                        console.error('Failed to load TTD:', imageUrl);
                                        this.style.display = 'none';
                                        if (placeholder) {
                                            placeholder.innerHTML = '<i class="fas fa-exclamation-circle text-warning" style="font-size: 1.5rem; margin-bottom: 5px; display: block;"></i><small class="text-warning">TTD tidak ditemukan</small>';
                                            placeholder.style.display = 'block';
                                        }
                                    };
                                }
                            }

                            // Trigger TTD auto-fill for shift if no saved TTD
                            if (shiftSelector && shiftSelector.value && !ttdHiddenInput?.value) {
                                console.log('Triggering TTD auto-fill for shift:', shiftSelector.value);
                                const changeEvent = new Event('change', {
                                    bubbles: true
                                });
                                shiftSelector.dispatchEvent(changeEvent);
                            }
                        }, 100); // Small delay to ensure all elements are rendered
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
                        
                        // Initialize komposisi listeners AFTER form loaded via AJAX
                        if (typeof initKomposisiListeners === 'function') {
                            setTimeout(() => {
                                initKomposisiListeners(container);
                            }, 100);
                        }
                    });
            }

            // Delete Confirm
            if (e.target.closest('.btn_confirm_delete')) {
                const id = e.target.closest('.btn_confirm_delete').dataset.id;
                const finalDeleteBtn = document.getElementById('btn_final_delete');
                if (finalDeleteBtn) finalDeleteBtn.href = '<?= base_url('produksi/delete') ?>/' + id;
            }
        });

        // Change events - PRIMARY HANDLER
        document.addEventListener('change', function(e) {
            // Sync visible tanggal_input to hidden tanggal_hidden field
            if (e.target.classList.contains('tanggal_input')) {
                const container = e.target.closest('form') || e.target.closest('.modal-body');
                const tanggalHidden = container?.querySelector('.tanggal_hidden');
                if (tanggalHidden) {
                    tanggalHidden.value = e.target.value;
                    console.log('Tanggal updated to:', e.target.value);
                    autofillBatchNumber(container);
                }
            }

            // SPK Select Autofill - Use option data attributes directly
            if (e.target.classList.contains('nomor_spk')) {
                if (spkAutofillLock) {
                    console.log('SPK autofill locked, skipping');
                    return;
                }
                const spk = (e.target.value || '').trim();
                console.log('Change event - SPK:', spk);
                if (!spk) return;
                const container = e.target.closest('form') || e.target.closest('.modal-body');
                if (!container) return;

                // Try to fill from the option's data attributes (direct autofill)
                console.log('Calling fillFromSpkOption...');
                fillFromSpkOption(e.target, container);

                // Also try alternative method in case Select2 wrapped the select
                console.log('Calling fillFromSpkOptionAlt...');
                fillFromSpkOptionAlt(spk, container);

                // Then, optionally fetch additional data from server for last data
                console.log('Calling loadProduksiSpkData...');
                loadProduksiSpkData(container, spk);

                // Load dynamic shifts from PPIC mesin data
                const nomorMesinInput = container.querySelector('.nomor_mesin');
                const selectedOpt = e.target.options[e.target.selectedIndex];
                const mesinName = selectedOpt?.dataset.mesin || nomorMesinInput?.value || '';
                const nomorMesin = selectedOpt?.dataset.nomorMesin || '';

                if (mesinName && nomorMesin) {
                    console.log('Loading shifts for mesin:', mesinName, 'nomor_mesin:', nomorMesin);
                    const spkVal = container.querySelector('.nomor_spk')?.value || '';
                    loadDynamicShifts(container, mesinName, nomorMesin, spkVal);
                }
            }

            // AUTO-FILL TTD & TARGET BERDASARKAN SHIFT
            if (e.target.classList.contains('shift-selector')) {
                const shift = e.target.value;

                console.log('=== SHIFT CHANGE EVENT ===');
                console.log('Shift value:', shift);

                if (!shift) {
                    console.log('Empty shift - resetting TTD display');
                    ttdAutoFillInProgress = false;
                    const preview = document.querySelector('#auto-ttd-preview');
                    const placeholder = document.querySelector('#auto-ttd-placeholder');
                    const hiddenInput = document.querySelector('#auto-user-ttd');

                    if (preview) preview.style.display = 'none';
                    if (placeholder) {
                        placeholder.innerHTML = '<i class="fas fa-pen-fancy" style="font-size: 1.5rem; margin-bottom: 5px; display: block;"></i><small>Pilih shift untuk melihat tanda tangan</small>';
                        placeholder.style.display = 'block';
                    }
                    if (hiddenInput) hiddenInput.value = '';
                    return;
                }

                // Get mesin info from form for target auto-fill
                const formContext = e.target.closest('form') || e.target.closest('.modal-body') || document;
                const mesinHiddenField = formContext.querySelector('input[name="nama_mesin"]');
                const nomorMesinField = formContext.querySelector('input[name="nomor_mesin"]');

                if (mesinHiddenField && nomorMesinField) {
                    const mesinName = mesinHiddenField.value;
                    const nomorMesin = nomorMesinField.value;

                    if (mesinName && nomorMesin) {
                        console.log('Calling loadTargetByShift - mesin:', mesinName, 'nomor:', nomorMesin, 'shift:', shift);
                        const spkVal = formContext.querySelector('.nomor_spk')?.value || '';
                        loadTargetByShift(formContext, mesinName, nomorMesin, shift, spkVal);
                    } else {
                        console.log('Mesin info not available yet for target auto-fill');
                    }
                } else {
                    console.log('Mesin fields not found in form');
                }

                // Mark TTD auto-fill as in progress
                ttdAutoFillInProgress = true;
                console.log('TTD auto-fill started...');

                // Extract API shift param - for extra shifts, strip 'extra_' prefix
                // for standard shifts (1,2,3) use as-is
                let apiShiftParam = shift;
                if (shift.startsWith('extra_')) {
                    // Extra shifts: use the label from data attribute
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const label = selectedOption?.dataset?.label || shift;
                    console.log('Extra shift detected, using label:', label);
                    apiShiftParam = label;
                } else {
                    // Standard shifts 1, 2, 3
                    console.log('Standard shift selected:', apiShiftParam);
                }

                // Fetch user TTD dari API - kirim shift sebagai query string agar aman dari karakter spesial
                const apiUrl = '<?= base_url('produksi/get-user-ttd') ?>?shift=' + encodeURIComponent(apiShiftParam);
                console.log('Fetching TTD from:', apiUrl);

                // Show loading state
                const placeholder = document.querySelector('#auto-ttd-placeholder');
                if (placeholder) {
                    placeholder.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div><small class="d-block mt-2">Mengambil tanda tangan...</small>';
                    placeholder.style.display = 'block';
                }

                fetch(apiUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin' // Include cookies/session
                    })
                    .then(async r => {
                        console.log('TTD Response status:', r.status, r.statusText);

                        let data;
                        try {
                            data = await r.json();
                        } catch (e) {
                            data = {
                                message: r.statusText
                            };
                        }

                        // Return response object along with data
                        return {
                            ok: r.ok,
                            status: r.status,
                            statusText: r.statusText,
                            data: data
                        };
                    })
                    .then(response => {
                        console.log('Response object:', response);

                        if (!response.ok) {
                            const errorMsg = response.data?.message || response.statusText || 'Unknown error';
                            console.error(`API Error HTTP ${response.status}: ${errorMsg}`);
                            ttdAutoFillInProgress = false;
                            throw new Error(`HTTP ${response.status}: ${errorMsg}`);
                        }

                        const data = response.data;
                        console.log('TTD fetch response:', data);

                        if (!data.success) {
                            console.log('API returned success=false:', data.message);
                            ttdAutoFillInProgress = false;
                            const preview = document.querySelector('#auto-ttd-preview');
                            const placeholder = document.querySelector('#auto-ttd-placeholder');

                            if (preview) preview.style.display = 'none';
                            if (placeholder) {
                                placeholder.innerHTML = '<i class="fas fa-exclamation-triangle text-warning" style="font-size: 1.5rem; margin-bottom: 5px; display: block;"></i><small class="text-warning">' + (data.message || 'TTD belum di-upload') + '<br><a href="<?= base_url('profile') ?>" target="_blank" class="text-decoration-underline">Upload di Profile</a></small>';
                                placeholder.style.display = 'block';
                            }

                            const hiddenInput = document.querySelector('#auto-user-ttd');
                            if (hiddenInput) hiddenInput.value = '';
                            return;
                        }

                        // Success - display TTD
                        const ttdPath = data.tanda_tangan;
                        const preview = document.querySelector('#auto-ttd-preview');
                        const placeholder = document.querySelector('#auto-ttd-placeholder');
                        const hiddenInput = document.querySelector('#auto-user-ttd');

                        console.log('TTD path:', ttdPath);

                        if (preview && ttdPath) {
                            // Handle both absolute path (uploads/ttd/...) dan relative path (ttd_...)
                            let imageUrl;
                            if (ttdPath.startsWith('uploads/')) {
                                // Already has uploads/ prefix
                                imageUrl = '<?= base_url() ?>' + ttdPath;
                            } else {
                                // Just filename, need to add uploads/ttd/
                                imageUrl = '<?= base_url('uploads/ttd/') ?>' + ttdPath;
                            }

                            console.log('Full image URL:', imageUrl);
                            preview.src = imageUrl;
                            preview.style.display = 'block';

                            // Add error handling untuk image load
                            preview.onerror = function() {
                                console.error('Failed to load TTD image:', imageUrl);
                                this.style.display = 'none';
                                if (placeholder) {
                                    placeholder.innerHTML = '<i class="fas fa-exclamation-circle text-danger" style="font-size: 1.5rem; margin-bottom: 5px; display: block;"></i><small class="text-danger">File TTD tidak ditemukan di: ' + imageUrl + '</small>';
                                    placeholder.style.display = 'block';
                                }
                            };

                            // Add success handler
                            preview.onload = function() {
                                console.log('TTD image loaded successfully');
                                if (placeholder) placeholder.style.display = 'none';
                                ttdAutoFillInProgress = false;
                            };
                        }

                        if (placeholder) {
                            placeholder.style.display = 'none';
                        }

                        if (hiddenInput) {
                            hiddenInput.value = ttdPath;
                            console.log('Updated hidden TTD input to:', ttdPath);
                            ttdAutoFillInProgress = false;
                        }

                        console.log('TTD auto-filled successfully');
                    })
                    .catch(err => {
                        console.error('Fetch error:', err.message);
                        ttdAutoFillInProgress = false;
                        const preview = document.querySelector('#auto-ttd-preview');
                        const placeholder = document.querySelector('#auto-ttd-placeholder');

                        if (preview) preview.style.display = 'none';
                        if (placeholder) {
                            placeholder.innerHTML = '<i class="fas fa-exclamation-circle text-danger" style="font-size: 1.5rem; margin-bottom: 5px; display: block;"></i><small class="text-danger">' + err.message + '</small>';
                            placeholder.style.display = 'block';
                        }

                        const hiddenInput = document.querySelector('#auto-user-ttd');
                        if (hiddenInput) hiddenInput.value = '';
                    });

                // Update shift_hidden dengan nilai shift untuk database
                // Simpan VALUE dari option (bukan label)
                // Untuk extra shifts: "extra_1", "extra_2", dll
                // Untuk standard shifts: "1", "2", "3"
                const shiftForm = e.target.closest('form') || e.target.closest('.modal-body');
                const shiftHidden = shiftForm ? shiftForm.querySelector('.shift_hidden') : document.querySelector('.shift_hidden');
                if (shiftHidden) {
                    const valueToStore = String(shift).trim();
                    shiftHidden.value = valueToStore;
                    console.log('Updated shift_hidden to:', valueToStore, 'form context:', shiftForm?.id || 'document');
                }

                // Re-generate batch number dengan shift prefix baru
                const batchContainer = shiftForm || e.target.closest('.modal-body') || document;
                window.autofillBatchNumber(batchContainer);
            }

            // Komposisi Nama dropdowns berubah — sync label di KG section (Becum)
            if (e.target.classList.contains('komposisi_nama_1') ||
                e.target.classList.contains('komposisi_nama_2') ||
                e.target.classList.contains('komposisi_nama_3') ||
                e.target.classList.contains('komposisi_nama_4')) {
                const container = e.target.closest('form') || e.target.closest('.modal-body') || document;
                window.syncKomposisiKgLabels(container);
            }
        }, true); // Use capture phase to ensure we fire first before Select2

        // Input events
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('jam-hasil') || e.target.classList.contains('isi')) {
                updateTotals(e.target.closest('.modal-body'));
            }

            // Reject inline per jam — update total reject real-time (Becum/Powerjet)
            if (e.target.classList.contains('jam-reject-jumlah') ||
                e.target.classList.contains('jam-reject-type')) {
                const container = e.target.closest('.modal-body') || document;
                syncJamRejectToRejectSection(container);
                updateTotals(container);
            }

            // Total SPK input - trigger sisa_po calculation via updateTotals
            if (e.target.classList.contains('total_spk')) {
                const root = e.target.closest('.modal-body') || e.target.closest('form') || document;
                updateTotals(root);
            }

            // Reject jumlah input — update total reject secara real-time
            if (e.target.classList.contains('reject-jumlah')) {
                const root = e.target.closest('.modal-body') || e.target.closest('form') || document;
                updateRejectInlineTotals(root);
            }

            // Meter awal/akhir — kalkulasi pemakaian = akhir - awal
            if (e.target.classList.contains('meter_awal') ||
                e.target.classList.contains('meter_akhir')) {
                const container = e.target.closest('form') || e.target.closest('.modal-body') || document;
                const awal    = parseFloat(container.querySelector('.meter_awal')?.value)  || 0;
                const akhir   = parseFloat(container.querySelector('.meter_akhir')?.value) || 0;
                const hasil   = akhir - awal;
                const pemakaianDisplay = container.querySelector('.meter_pemakaian');
                const pemakaianHidden  = container.querySelector('.meter_pemakaian_hidden');
                if (pemakaianDisplay) pemakaianDisplay.value = hasil.toFixed(2);
                if (pemakaianHidden)  pemakaianHidden.value  = hasil.toFixed(2);
            }

            // KG komposisi input — kalkulasi otomatis (hanya Becum)
            if (e.target.classList.contains('komposisi-kg-input') ||
                e.target.classList.contains('m_pemakaian')) {
                const container = e.target.closest('form') || e.target.closest('.modal-body') || document;
                window.updateKomposisiKg(container);
            }

            // Nama komposisi berubah — sync label di KG section (handle both input dan select change)
            if (e.target.classList.contains('komposisi_nama_1') ||
                e.target.classList.contains('komposisi_nama_2') ||
                e.target.classList.contains('komposisi_nama_3') ||
                e.target.classList.contains('komposisi_nama_4')) {
                const container = e.target.closest('form') || e.target.closest('.modal-body') || document;
                window.syncKomposisiKgLabels(container);
            }

            // Downtime Duration Calculation
            if (e.target.classList.contains('dt-mulai') || e.target.classList.contains('dt-selesai')) {
                const card = e.target.closest('.dt-card');
                const mulai = card.querySelector('.dt-mulai').value;
                const selesai = card.querySelector('.dt-selesai').value;
                const durasiInput = card.querySelector('.dt-durasi');

                if (mulai && selesai) {
                    const start = new Date(mulai);
                    const end = new Date(selesai);
                    const diffMs = end - start;
                    const diffMins = Math.round(diffMs / 60000);
                    durasiInput.value = isNaN(diffMins) ? '' : diffMins;
                }
            }
        });

        // Disable Enter key submission in produksi forms - USE CAPTURE PHASE
        document.addEventListener('keydown', function(e) {
            // Check if Enter was pressed
            if (e.key === 'Enter' || e.keyCode === 13) {
                const form = e.target.closest('#formAddLaporan') || e.target.closest('#formEditLaporan');
                if (form) {
                    // Allow Enter only in textarea
                    if (e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Enter key blocked in produksi form');
                        return false;
                    }
                }
            }
        }, true); // TRUE = capture phase (fires before any other handlers)

        // Save Reject from Reject Modal
        const btnSaveReject = document.getElementById('btn_save_reject_generic');
        if (btnSaveReject) {
            btnSaveReject.addEventListener('click', function() {
                if (!activeRejectStorage) return;
                activeRejectStorage.innerHTML = '';
                let total = 0;
                document.querySelectorAll('#table_reject_modal tbody tr').forEach(row => {
                    const type = row.querySelector('.input-reject-type').value;
                    const qty = parseInt(row.querySelector('.input-reject-qty').value) || 0;

                    if (type && qty > 0) {
                        total += qty;
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `rejects[${type}]`;
                        hidden.value = qty;
                        hidden.dataset.type = type;
                        activeRejectStorage.appendChild(hidden);
                    }
                });
                const container = activeRejectStorage.closest('.modal-body');
                if (container) {
                    const rejectInput = container.querySelector('.grand_total_reject');
                    if (rejectInput) rejectInput.value = total;
                    updateTotals(container);
                }

                // Manually close the reject modal to ensure it doesn't affect the parent modal
                const rejectModalEl = document.getElementById('modalReject');
                const modalInstance = bootstrap.Modal.getInstance(rejectModalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        }

        // Recycle Bin Loading
        const btnTrash = document.getElementById('btn_open_trash');
        if (btnTrash) {
            btnTrash.addEventListener('click', function() {
                const tbody = document.querySelector('#table_trash tbody');
                if (!tbody) return;
                tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</div></td>';
                fetch('<?= base_url('produksi/get_trash') ?>')
                    .then(r => r.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        if (data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Tempat sampah kosong.</td></tr>';
                            return;
                        }
                        data.forEach(p => {
                            const html = `<tr><td class="px-3 py-3"><div class="font-weight-bold">${p.nomor_spk}</div><div class="text-xs text-muted">${p.nama_mesin} - ${p.nama_produk}</div></td><td class="py-3 small">${p.deleted_at}</td><td class="text-center py-3"><div class="d-flex justify-content-center gap-1"><a href="<?= base_url('produksi/restore') ?>/${p.id}" class="btn btn-xs btn-success px-2 py-1" style="font-size: 10px;"><i class="fas fa-undo mr-1"></i> Pulihkan</a><a href="<?= base_url('produksi/delete_permanent') ?>/${p.id}" class="btn btn-xs btn-outline-danger px-2 py-1" style="font-size: 10px;" onclick="return confirm('Hapus permanen?')"><i class="fas fa-times"></i></a></div></td></tr>`;
                            tbody.insertAdjacentHTML('beforeend', html);
                        });
                    });
            });
        }

        // Before form submission, sync grand_total_bagus hidden field
        const formAddLaporan = document.getElementById('formAddLaporan');
        if (formAddLaporan) {
            formAddLaporan.addEventListener('submit', function() {
                // Sync display field values ke hidden inputs sebelum submit
                const rejectDisplay = formAddLaporan.querySelector('.grand_total_reject');
                const rejectHidden = formAddLaporan.querySelector('.grand_total_reject_hidden');
                if (rejectDisplay && rejectHidden) rejectHidden.value = rejectDisplay.value;

                const bagusDisplay = formAddLaporan.querySelector('.grand_total_bagus');
                const bagusHidden = formAddLaporan.querySelector('.grand_total_bagus_hidden');
                if (bagusDisplay && bagusHidden) bagusHidden.value = bagusDisplay.value;
            });
        }

        // Initialize reject section when modals are shown
        const addModalEl = document.getElementById('addDataModal');
        if (addModalEl) {
            addModalEl.addEventListener('shown.bs.modal', function(e) {
                const container = addModalEl.querySelector('.modal-body');
                if (container && typeof initializeRejectSection === 'function') {
                    initializeRejectSection(container);
                }

                // Initialize komposisi listeners dengan setTimeout untuk memastikan DOM siap
                if (container && typeof initKomposisiListeners === 'function') {
                    setTimeout(() => {
                        console.log('[addDataModal shown] Initializing komposisi listeners after 50ms');
                        initKomposisiListeners(container);
                    }, 50);
                }

                // Trigger sisa_po calculation on modal show via updateTotals
                if (container && typeof updateTotals === 'function') {
                    updateTotals(container);
                }

                // Auto-load TTD on modal open — no shift required
                const preview = addModalEl.querySelector('#auto-ttd-preview');
                const placeholder = addModalEl.querySelector('#auto-ttd-placeholder');
                const ttdInput = addModalEl.querySelector('#auto-user-ttd');

                // Skip if already loaded
                if (ttdInput && ttdInput.value && preview && preview.style.display !== 'none') return;

                if (placeholder) {
                    placeholder.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"></div><small class="d-block mt-2">Mengambil tanda tangan...</small>';
                    placeholder.style.display = 'block';
                }

                fetch('<?= base_url('produksi/get-user-ttd') ?>', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success && data.tanda_tangan) {
                            let imageUrl = data.tanda_tangan.startsWith('uploads/') ?
                                '<?= base_url() ?>' + data.tanda_tangan :
                                '<?= base_url('uploads/ttd/') ?>' + data.tanda_tangan;

                            if (preview) {
                                preview.src = imageUrl;
                                preview.style.display = 'block';
                            }
                            if (placeholder) placeholder.style.display = 'none';
                            if (ttdInput) ttdInput.value = data.tanda_tangan;
                        } else {
                            if (preview) preview.style.display = 'none';
                            if (placeholder) {
                                placeholder.innerHTML = '<i class="fas fa-exclamation-triangle text-warning" style="font-size:1.5rem;display:block;margin-bottom:5px;"></i>' +
                                    '<small class="text-warning">' + (data.message || 'TTD belum di-upload') + '<br>' +
                                    '<a href="<?= base_url('profile') ?>" target="_blank" class="text-decoration-underline">Upload di Profile</a></small>';
                                placeholder.style.display = 'block';
                            }
                            if (ttdInput) ttdInput.value = '';
                        }
                    })
                    .catch(() => {
                        if (placeholder) {
                            placeholder.innerHTML = '<i class="fas fa-times-circle text-danger" style="font-size:1.5rem;display:block;margin-bottom:5px;"></i><small class="text-danger">Gagal memuat tanda tangan</small>';
                            placeholder.style.display = 'block';
                        }
                    });
            });
        }

        const editModalEl = document.getElementById('modalEdit');
        if (editModalEl) {
            editModalEl.addEventListener('shown.bs.modal', function(e) {
                const container = editModalEl.querySelector('.modal-body');
                if (container && typeof initializeRejectSection === 'function') {
                    initializeRejectSection(container);
                }

                // Initialize komposisi listeners dengan setTimeout untuk memastikan DOM siap
                if (container && typeof initKomposisiListeners === 'function') {
                    setTimeout(() => {
                        console.log('[editModal shown] Initializing komposisi listeners after 50ms');
                        initKomposisiListeners(container);
                    }, 50);
                }
            });
        }

        // Handle edit form submission — sync hidden inputs BEFORE form submit
        document.addEventListener('submit', function(e) {
            if (e.target && (e.target.id === 'formEditLaporan' || e.target.id === 'formAddLaporan')) {
                // Sync shift value from visible select to hidden input
                const shiftSelector = e.target.querySelector('.shift-selector');
                const shiftHidden = e.target.querySelector('.shift_hidden');
                if (shiftSelector && shiftHidden) {
                    shiftHidden.value = shiftSelector.value;
                    console.log('Synced shift from selector to hidden:', shiftHidden.value);
                }

                // Sync nama_produk
                const namaProdukEl = e.target.querySelector('.nama_produk_hidden');
                const namaProdukDisplay = e.target.querySelector('.nama_produk');
                if (namaProdukEl && namaProdukDisplay) namaProdukEl.value = namaProdukDisplay.value;

                // Sync nomor_mesin
                const nomorMesinEl = e.target.querySelector('.nomor_mesin_hidden');
                const nomorMesinDisplay = e.target.querySelector('.nomor_mesin');
                if (nomorMesinEl && nomorMesinDisplay) nomorMesinEl.value = nomorMesinDisplay.value;

                // Sync brand
                const brandEl = e.target.querySelector('.brand_hidden');
                const brandDisplay = e.target.querySelector('.brand');
                if (brandEl && brandDisplay) brandEl.value = brandDisplay.value;

                // Sync warna
                const warnaEl = e.target.querySelector('.warna_hidden');
                const warnaDisplay = e.target.querySelector('.warna');
                if (warnaEl && warnaDisplay) warnaEl.value = warnaDisplay.value;

                // Sync grade
                const gradeEl = e.target.querySelector('.grade_hidden');
                const gradeDisplay = e.target.querySelector('.grade');
                if (gradeEl && gradeDisplay) gradeEl.value = gradeDisplay.value;

                // Sync packing
                const packingEl = e.target.querySelector('.packing_hidden');
                const packingDisplay = e.target.querySelector('.packing');
                if (packingEl && packingDisplay) packingEl.value = packingDisplay.value;

                // Sync target
                const targetEl = e.target.querySelector('.target_hidden');
                const targetDisplay = e.target.querySelector('.target');
                if (targetEl && targetDisplay) targetEl.value = targetDisplay.value;

                // Sync total_spk
                const totalSpkEl = e.target.querySelector('.total_spk_hidden');
                const totalSpkDisplay = e.target.querySelector('.total_spk');
                if (totalSpkEl && totalSpkDisplay) totalSpkEl.value = totalSpkDisplay.value;

                // Sync sisa_po
                const sisaPoEl = e.target.querySelector('.sisa_po_hidden');
                const sisaPoDisplay = e.target.querySelector('.sisa_po');
                if (sisaPoEl && sisaPoDisplay) sisaPoEl.value = sisaPoDisplay.value;

                // Sync operator
                const operatorEl = e.target.querySelector('.operator_hidden');
                const operatorDisplay = e.target.querySelector('.operator');
                if (operatorEl && operatorDisplay) operatorEl.value = operatorDisplay.value;

                // Sync grand_total_bagus
                const rejectDisplay = e.target.querySelector('.grand_total_reject');
                const rejectHidden = e.target.querySelector('.grand_total_reject_hidden');
                if (rejectDisplay && rejectHidden) rejectHidden.value = rejectDisplay.value;

                const bagusDisplay = e.target.querySelector('.grand_total_bagus');
                const bagusHidden = e.target.querySelector('.grand_total_bagus_hidden');
                if (bagusDisplay && bagusHidden) bagusHidden.value = bagusDisplay.value;

                // Inject hidden inputs dari jam-reject (Becum/Powerjet)
                if (IS_JAM_REJECT_MESIN) {
                    // Hapus hidden reject lama agar tidak duplikat
                    e.target.querySelectorAll('input[name^="rejects["]').forEach(el => el.remove());

                    // Baca rejectMap dari dataset form (diisi oleh syncJamRejectToRejectSection)
                    const rejectMap = JSON.parse(e.target.dataset.jamRejectMap || '{}');
                    let idx = 0;
                    for (const [jenis, jumlah] of Object.entries(rejectMap)) {
                        const inputJenis = document.createElement('input');
                        inputJenis.type  = 'hidden';
                        inputJenis.name  = `rejects[${idx}][jenis_reject]`;
                        inputJenis.value = jenis;
                        e.target.appendChild(inputJenis);

                        const inputJumlah = document.createElement('input');
                        inputJumlah.type  = 'hidden';
                        inputJumlah.name  = `rejects[${idx}][jumlah]`;
                        inputJumlah.value = jumlah;
                        e.target.appendChild(inputJumlah);

                        idx++;
                    }
                    console.log('Jam-reject injected:', rejectMap, 'total entries:', idx);
                }

                console.log('All hidden fields synced before submission');
            }
        });

        const urlParams = new URLSearchParams(window.location.search);
        const pendingSpk = (urlParams.get('spk') || '').trim();
        if (urlParams.has('open_add') || pendingSpk) {
            if (pendingSpk) {
                openProduksiAddWithSpk(pendingSpk);
            } else {
                const addModalEl = document.getElementById('addDataModal');
                if (addModalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(addModalEl);
                    modal.show();
                }
            }

            const cleanParams = new URLSearchParams(window.location.search);
            cleanParams.delete('open_add');
            cleanParams.delete('spk');
            const query = cleanParams.toString();
            const cleanUrl = window.location.pathname + (query ? '?' + query : '');
            window.history.replaceState({}, document.title, cleanUrl);
        }

        // ============================================
        // TTD AUTO-FILL INITIALIZATION
        // ============================================
        console.log('=== Produksi Scripts Loaded ===');

        // ============================================
        // SPK DROPDOWN AUTO-FILL ON CHANGE
        // ============================================
        // Handle SPK dropdown change to auto-fill form fields
        // Gunakan event delegation agar bekerja meski Select2 di-init belakangan

        function handleSpkChange(selectEl) {
            if (!selectEl || !selectEl.value) return;

            // Find nearest form container
            const container = selectEl.closest('form') ||
                selectEl.closest('[role="dialog"]')?.querySelector('form') ||
                document;

            console.log('[SPK-AUTOFILL] SPK changed to:', selectEl.value, '| container:', container?.id || container?.tagName);

            try {
                fillFromSpkOption(selectEl, container);
            } catch (err) {
                console.error('[SPK-AUTOFILL] fillFromSpkOption error:', err);
                // Fallback
                if (typeof fillFromSpkOptionAlt === 'function') {
                    fillFromSpkOptionAlt(selectEl.value, container);
                }
            }
        }

        // 1. Attach to all existing .nomor_spk selects (native change)
        document.querySelectorAll('.nomor_spk').forEach(select => {
            if (!select.dataset.autofillBound) {
                select.dataset.autofillBound = '1';
                select.addEventListener('change', function() {
                    handleSpkChange(this);
                });
            }
        });

        // 2. Attach Select2 event setelah delay (Select2 init biasanya selesai 300-500ms setelah DOM)
        setTimeout(function() {
            if (typeof jQuery === 'undefined') return;

            jQuery('.nomor_spk').each(function() {
                const $sel = jQuery(this);
                if ($sel.hasClass('select2-hidden-accessible') && !this.dataset.select2AutofillBound) {
                    this.dataset.select2AutofillBound = '1';
                    $sel.on('select2:select', function(e) {
                        console.log('[SPK-AUTOFILL-SELECT2] select2:select fired for:', e.params?.data?.id);
                        handleSpkChange(this);
                    });
                    console.log('[SPK-AUTOFILL] Select2 bound to .nomor_spk');
                }
            });
        }, 600);

        // Debug: Log shift selector elements
        const shiftSelectors = document.querySelectorAll('.shift-selector');
        console.log('Found shift selectors:', shiftSelectors.length);
        shiftSelectors.forEach((selector, i) => {
            console.log(`Shift selector ${i}:`, selector, 'Value:', selector.value, 'Classes:', Array.from(selector.classList));
        });

        // Debug: Log TTD elements
        const ttdPreview = document.querySelector('#auto-ttd-preview');
        const ttdPlaceholder = document.querySelector('#auto-ttd-placeholder');
        const ttdHidden = document.querySelector('#auto-user-ttd');
        console.log('TTD Elements:', {
            preview: !!ttdPreview,
            placeholder: !!ttdPlaceholder,
            hidden: !!ttdHidden
        });

        // Initialize TTD auto-fill for edit mode AND restore SPK data
        const isEditMode = document.querySelector('[data-is-edit="1"]');
        if (isEditMode) {
            console.log('Edit mode detected, restoring edit data...');

            // First, restore SPK selection and trigger autofill to populate related fields
            const spkSelect = document.querySelector('.nomor_spk');
            if (spkSelect && spkSelect.value) {
                console.log('Found saved SPK value:', spkSelect.value);
                // Trigger change to populate all related fields
                setTimeout(() => {
                    const changeEvent = new Event('change', {
                        bubbles: true
                    });
                    spkSelect.dispatchEvent(changeEvent);
                }, 300);
            }

            // Load dynamic shifts based on machine (this will include extra shifts)
            setTimeout(() => {
                const mesinHiddenField = document.querySelector('input[name="nama_mesin"]');
                const nomorMesinField = document.querySelector('input[name="nomor_mesin"]');
                if (mesinHiddenField && nomorMesinField) {
                    const mesinName = mesinHiddenField.value;
                    const nomorMesin = nomorMesinField.value;
                    if (mesinName && nomorMesin) {
                        console.log('Loading dynamic shifts for edit mode - mesin:', mesinName, 'nomor_mesin:', nomorMesin);
                        loadDynamicShifts(document, mesinName, nomorMesin);
                    }
                }
            }, 500);

            // Restore Shift selection explicitly and ensure it's set
            // WITH DELAY to allow API to load extra shifts first
            const shiftSelector = document.querySelector('.shift-selector');
            if (shiftSelector) {
                const savedShiftValue = shiftSelector.dataset.savedShift || shiftSelector.value;
                console.log('Shift selector - saved shift from data-attr:', shiftSelector.dataset.savedShift, 'current value:', shiftSelector.value, 'type:', typeof savedShiftValue);

                // Delay to wait for loadDynamicShifts to complete
                setTimeout(() => {
                    console.log('Shift options after API load:', Array.from(shiftSelector.options).map(o => ({
                        value: o.value,
                        text: o.text
                    })));

                    if (savedShiftValue && String(savedShiftValue).trim() !== '') {
                        // Ensure value is string for proper comparison
                        shiftSelector.value = String(savedShiftValue).trim();
                        console.log('After explicit set - Shift value:', shiftSelector.value, 'Selected option:', shiftSelector.options[shiftSelector.selectedIndex]?.text);
                    }
                }, 1200); // Wait for loadDynamicShifts (500ms) + buffer
            }

            const ttdHiddenInput = document.querySelector('#auto-user-ttd');

            // First, check if there's a saved TTD value from DB
            if (ttdHiddenInput && ttdHiddenInput.value) {
                console.log('Found saved TTD value:', ttdHiddenInput.value);
                const ttdPath = ttdHiddenInput.value;

                // Display the saved TTD image
                const preview = document.querySelector('#auto-ttd-preview');
                const placeholder = document.querySelector('#auto-ttd-placeholder');

                if (preview && ttdPath) {
                    let imageUrl;
                    if (ttdPath.startsWith('uploads/')) {
                        imageUrl = '<?= base_url() ?>' + ttdPath;
                    } else {
                        imageUrl = '<?= base_url('uploads/ttd/') ?>' + ttdPath;
                    }

                    console.log('Loading saved TTD from:', imageUrl);
                    preview.src = imageUrl;
                    preview.style.display = 'block';

                    preview.onerror = function() {
                        console.error('Failed to load saved TTD:', imageUrl);
                        this.style.display = 'none';
                        if (placeholder) {
                            placeholder.innerHTML = '<i class="fas fa-exclamation-circle text-warning" style="font-size: 1.5rem; margin-bottom: 5px; display: block;"></i><small class="text-warning">TTD belum tersedia</small>';
                            placeholder.style.display = 'block';
                        }
                    };

                    preview.onload = function() {
                        console.log('Saved TTD loaded successfully');
                        if (placeholder) placeholder.style.display = 'none';
                    };
                }
            }

            // Then, trigger TTD auto-fill for shift selection (only if no saved TTD exists)
            if (shiftSelector && shiftSelector.value && !ttdHiddenInput?.value) {
                console.log('Found existing shift value:', shiftSelector.value, 'triggering TTD auto-fill...');
                // Trigger TTD auto-fill for existing shift value
                setTimeout(() => {
                    const changeEvent = new Event('change', {
                        bubbles: true
                    });
                    shiftSelector.dispatchEvent(changeEvent);
                }, 1000); // Delay untuk memastikan semua element sudah ready
            }
        }
    });

    // Form submission handler - prevent submit if TTD auto-fill still in progress
    document.addEventListener('submit', function(e) {
        // Check if TTD auto-fill is still in progress for produksi forms
        if ((e.target.id === 'formAddLaporan' || e.target.id === 'formEditLaporan') && ttdAutoFillInProgress) {
            console.warn('TTD auto-fill still in progress, preventing submit');
            e.preventDefault();
            alert('Tanda tangan sedang dimuat. Silakan tunggu sebentar...');
            return false;
        }

        // Sync shift and all hidden fields BEFORE submission
        if (e.target && (e.target.id === 'formEditLaporan' || e.target.id === 'formAddLaporan')) {
            // Sync shift value from visible select to hidden input
            const shiftSelector = e.target.querySelector('.shift-selector');
            const shiftHidden = e.target.querySelector('.shift_hidden');
            if (shiftSelector && shiftHidden) {
                shiftHidden.value = shiftSelector.value;
                console.log('Pre-submit: Synced shift from selector to hidden:', shiftHidden.value);
            }

            // Sync nama_produk
            const namaProdukEl = e.target.querySelector('.nama_produk_hidden');
            const namaProdukDisplay = e.target.querySelector('.nama_produk');
            if (namaProdukEl && namaProdukDisplay) namaProdukEl.value = namaProdukDisplay.value;

            // Sync nomor_mesin
            const nomorMesinEl = e.target.querySelector('.nomor_mesin_hidden');
            const nomorMesinDisplay = e.target.querySelector('.nomor_mesin');
            if (nomorMesinEl && nomorMesinDisplay) nomorMesinEl.value = nomorMesinDisplay.value;

            // Sync brand
            const brandEl = e.target.querySelector('.brand_hidden');
            const brandDisplay = e.target.querySelector('.brand');
            if (brandEl && brandDisplay) brandEl.value = brandDisplay.value;

            // Sync warna
            const warnaEl = e.target.querySelector('.warna_hidden');
            const warnaDisplay = e.target.querySelector('.warna');
            if (warnaEl && warnaDisplay) warnaEl.value = warnaDisplay.value;

            // Sync grade
            const gradeEl = e.target.querySelector('.grade_hidden');
            const gradeDisplay = e.target.querySelector('.grade');
            if (gradeEl && gradeDisplay) gradeEl.value = gradeDisplay.value;

            // Sync packing
            const packingEl = e.target.querySelector('.packing_hidden');
            const packingDisplay = e.target.querySelector('.packing');
            if (packingEl && packingDisplay) packingEl.value = packingDisplay.value;

            // Sync target
            const targetEl = e.target.querySelector('.target_hidden');
            const targetDisplay = e.target.querySelector('.target');
            if (targetEl && targetDisplay) targetEl.value = targetDisplay.value;

            // Sync operator
            const operatorEl = e.target.querySelector('.operator_hidden');
            const operatorDisplay = e.target.querySelector('.operator');
            if (operatorEl && operatorDisplay) operatorEl.value = operatorDisplay.value;

            // Sync tanggal
            const tanggalEl = e.target.querySelector('.tanggal_hidden');
            const tanggalDisplay = e.target.querySelector('input[name="tanggal"][disabled]');
            if (tanggalEl && tanggalDisplay) tanggalEl.value = tanggalDisplay.value;

            // Sync grand_total_bagus
            const rejectDisplay = e.target.querySelector('.grand_total_reject');
            const rejectHidden = e.target.querySelector('.grand_total_reject_hidden');
            if (rejectDisplay && rejectHidden) rejectHidden.value = rejectDisplay.value;

            const bagusDisplay = e.target.querySelector('.grand_total_bagus');
            const bagusHidden = e.target.querySelector('.grand_total_bagus_hidden');
            if (bagusDisplay && bagusHidden) bagusHidden.value = bagusDisplay.value;

            // Inject hidden inputs dari jam-reject (Becum/Powerjet)
            if (IS_JAM_REJECT_MESIN) {
                // Hapus hidden reject lama agar tidak duplikat
                e.target.querySelectorAll('input[name^="rejects["]').forEach(el => el.remove());

                // Baca rejectMap dari dataset form (diisi oleh syncJamRejectToRejectSection)
                const rejectMap = JSON.parse(e.target.dataset.jamRejectMap || '{}');
                let idx = 0;
                for (const [jenis, jumlah] of Object.entries(rejectMap)) {
                    const inputJenis = document.createElement('input');
                    inputJenis.type  = 'hidden';
                    inputJenis.name  = `rejects[${idx}][jenis_reject]`;
                    inputJenis.value = jenis;
                    e.target.appendChild(inputJenis);

                    const inputJumlah = document.createElement('input');
                    inputJumlah.type  = 'hidden';
                    inputJumlah.name  = `rejects[${idx}][jumlah]`;
                    inputJumlah.value = jumlah;
                    e.target.appendChild(inputJumlah);

                    idx++;
                }
                console.log('Jam-reject injected:', rejectMap, 'total entries:', idx);
            }

            console.log('All hidden fields synced before submission');
        }
    });

    // ============================================
    // TB IN/OUT COLLAPSE ICON ANIMATION
    // ============================================
    document.addEventListener('shown.bs.collapse', function(e) {
        if (e.target.id === 'tbInCollapse') {
            const icon = document.querySelector('.toggle-icon-tb-in');
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
        if (e.target.id === 'tbOutCollapse') {
            const icon = document.querySelector('.toggle-icon-tb-out');
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
    });

    document.addEventListener('hidden.bs.collapse', function(e) {
        if (e.target.id === 'tbInCollapse') {
            const icon = document.querySelector('.toggle-icon-tb-in');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
        if (e.target.id === 'tbOutCollapse') {
            const icon = document.querySelector('.toggle-icon-tb-out');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    });
</script>