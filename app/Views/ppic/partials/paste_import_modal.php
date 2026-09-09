<?php
/**
 * Paste from Excel Modal Partial
 * Copy sel dari Excel → Ctrl+V ke textarea → preview → simpan
 *
 * Required vars:
 *   $type       : galon | cap_galon | freeform | screw_cap
 *   $type_label : label untuk judul modal
 */
$type      = $type      ?? 'galon';
$typeLabel = $type_label ?? 'Galon';

// Konfigurasi kolom per tipe
// keys: nama kolom DB, required: wajib tidak kosong, numeric: harus angka
$colConfigs = [
    'galon' => [
        ['key' => 'nomor',       'label' => 'Nomor',       'required' => false, 'numeric' => false],
        ['key' => 'nama_produk', 'label' => 'Nama Produk', 'required' => true,  'numeric' => false],
        ['key' => 'grade',       'label' => 'Grade',       'required' => false, 'numeric' => false],
        ['key' => 'berat',       'label' => 'Berat',       'required' => false, 'numeric' => true],
        ['key' => 'wh_oro',      'label' => 'WH ORO',      'required' => false, 'numeric' => true],
        ['key' => 'wh_bam',      'label' => 'WH BAM',      'required' => false, 'numeric' => true],
        ['key' => 'tanggal',     'label' => 'Tanggal',     'required' => false, 'numeric' => false],
    ],
    'cap_galon' => [
        ['key' => 'nomor',       'label' => 'Nomor',       'required' => false, 'numeric' => false],
        ['key' => 'nama_produk', 'label' => 'Nama Produk', 'required' => true,  'numeric' => false],
        ['key' => 'warna',       'label' => 'Warna',       'required' => false, 'numeric' => false],
        ['key' => 'berat',       'label' => 'Berat',       'required' => false, 'numeric' => true],
        ['key' => 'jumlah',      'label' => 'Jumlah',      'required' => false, 'numeric' => true],
        ['key' => 'packing',     'label' => 'Packing',     'required' => false, 'numeric' => false],
        ['key' => 'tanggal',     'label' => 'Tanggal',     'required' => false, 'numeric' => false],
    ],
    'freeform' => [
        ['key' => 'nomor',       'label' => 'Nomor',       'required' => false, 'numeric' => false],
        ['key' => 'nama_produk', 'label' => 'Nama Produk', 'required' => true,  'numeric' => false],
        ['key' => 'warna',       'label' => 'Warna',       'required' => false, 'numeric' => false],
        ['key' => 'berat',       'label' => 'Berat',       'required' => false, 'numeric' => true],
        ['key' => 'jumlah',      'label' => 'Jumlah',      'required' => false, 'numeric' => true],
        ['key' => 'packing',     'label' => 'Packing',     'required' => false, 'numeric' => false],
        ['key' => 'tanggal',     'label' => 'Tanggal',     'required' => false, 'numeric' => false],
    ],
    'screw_cap' => [
        ['key' => 'nomor',       'label' => 'Nomor',       'required' => false, 'numeric' => false],
        ['key' => 'nama_produk', 'label' => 'Nama Produk', 'required' => true,  'numeric' => false],
        ['key' => 'warna',       'label' => 'Warna',       'required' => false, 'numeric' => false],
        ['key' => 'berat',       'label' => 'Berat',       'required' => false, 'numeric' => true],
        ['key' => 'jumlah',      'label' => 'Jumlah',      'required' => false, 'numeric' => true],
        ['key' => 'keterangan',  'label' => 'Keterangan',  'required' => false, 'numeric' => false],
        ['key' => 'tanggal',     'label' => 'Tanggal',     'required' => false, 'numeric' => false],
    ],
];

$cols = $colConfigs[$type] ?? $colConfigs['galon'];
?>

<!-- ===== MODAL PASTE DARI EXCEL ===== -->
<div class="modal fade" id="pasteImportModal" tabindex="-1" role="dialog" aria-labelledby="pasteImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-paste mr-2"></i>Paste dari Excel &mdash; <?= esc($typeLabel) ?>
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <!-- STEP 1: Textarea paste -->
                <div id="piStep1">
                    <!-- Petunjuk kolom -->
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">
                            <i class="fas fa-info-circle text-primary mr-1"></i>
                            Urutan kolom yang diharapkan (sesuaikan urutan kolom di Excel):
                        </small>
                        <div>
                            <?php foreach ($cols as $i => $col): ?>
                                <span class="badge <?= $col['required'] ? 'badge-primary' : 'badge-secondary' ?> mr-1 mb-1" style="font-size:.8rem">
                                    <?= ($i + 1) ?> <?= esc($col['label']) ?>
                                    <?= $col['required'] ? '' : '<span style="opacity:.7;font-size:.75em">opsional</span>' ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">
                            Badge <span class="badge badge-primary">biru</span> = wajib &nbsp;|&nbsp;
                            <span class="badge badge-secondary">abu</span> = opsional, boleh kosong.
                            Kolom opsional di kanan boleh tidak di-copy.
                        </small>
                    </div>

                    <!-- Checkbox skip header -->
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="piSkipHeader" checked>
                        <label class="form-check-label small" for="piSkipHeader">
                            Baris pertama adalah header (otomatis dilewati)
                        </label>
                    </div>

                    <!-- Textarea -->
                    <textarea
                        id="piTextarea"
                        class="form-control"
                        rows="10"
                        placeholder="Paste data dari Excel di sini (Ctrl+V)..."
                        style="font-size:.82rem;resize:vertical;white-space:pre;font-family:monospace"></textarea>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted" id="piPasteHint">
                            <i class="fas fa-keyboard mr-1"></i>Ctrl+V setelah klik di dalam kotak di atas
                        </small>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" id="piBtnReset">
                                <i class="fas fa-times mr-1"></i>Reset
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="piBtnPreview">
                                <i class="fas fa-table mr-1"></i>Preview
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Tabel preview -->
                <div id="piStep2" style="display:none">
                    <!-- Summary -->
                    <div class="d-flex align-items-center mb-3" id="piSummary"></div>

                    <!-- Tabel -->
                    <div class="table-responsive" style="max-height:400px;overflow-y:auto">
                        <table class="table table-sm table-bordered" id="piPreviewTable">
                            <thead class="thead-light">
                                <tr id="piPreviewHead"></tr>
                            </thead>
                            <tbody id="piPreviewBody"></tbody>
                        </table>
                    </div>
                </div>

            </div><!-- /.modal-body -->

            <div class="modal-footer">
                <!-- Footer step 1 -->
                <div id="piFooter1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
                <!-- Footer step 2 -->
                <div id="piFooter2" style="display:none">
                    <button type="button" class="btn btn-outline-secondary mr-2" id="piBtnBackEdit">
                        <i class="fas fa-arrow-left mr-1"></i>Edit Lagi
                    </button>
                    <button type="button" class="btn btn-success" id="piBtnSave" disabled>
                        <i class="fas fa-save mr-1"></i>Simpan <span id="piSaveCount">0</span> Baris
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- ===== END MODAL ===== -->

<script>
(function () {
    'use strict';

    /* ─── konfigurasi kolom dari PHP ─── */
    var COLS       = <?= json_encode(array_values($cols), JSON_UNESCAPED_UNICODE) ?>;
    var COMMIT_URL = '<?= base_url('ppic/finish_good/' . $type . '/import-commit') ?>';

    /* ─── state ─── */
    var _validRows = [];

    /* ─── tunggu DOM ready ─── */
    function init() {

    /* ─── elemen ─── */
    var modalEl     = document.getElementById('pasteImportModal');
    var textarea    = document.getElementById('piTextarea');
    var cbSkip      = document.getElementById('piSkipHeader');
    var step1       = document.getElementById('piStep1');
    var step2       = document.getElementById('piStep2');
    var footer1     = document.getElementById('piFooter1');
    var footer2     = document.getElementById('piFooter2');
    var btnPreview  = document.getElementById('piBtnPreview');
    var btnReset    = document.getElementById('piBtnReset');
    var btnBackEdit = document.getElementById('piBtnBackEdit');
    var btnSave     = document.getElementById('piBtnSave');
    var summary     = document.getElementById('piSummary');
    var previewHead = document.getElementById('piPreviewHead');
    var previewBody = document.getElementById('piPreviewBody');
    var saveCount   = document.getElementById('piSaveCount');
    var pasteHint   = document.getElementById('piPasteHint');

    if (!modalEl) return; // partial belum di DOM, skip

    /* ─── helper show/hide ─── */
    function show(el) { if (el) el.style.display = ''; }
    function hide(el) { if (el) el.style.display = 'none'; }

    /* ─── reset ke step 1 ─── */
    function resetAll() {
        if (textarea) textarea.value = '';
        _validRows = [];
        show(step1); hide(step2);
        show(footer1); hide(footer2);
        if (previewHead) previewHead.innerHTML = '';
        if (previewBody) previewBody.innerHTML = '';
        if (summary)     summary.innerHTML     = '';
    }

    /* ─── reset on modal close ─── */
    if (modalEl && window.jQuery) {
        jQuery(modalEl).on('hidden.bs.modal', resetAll);
    }

    /* ─── parse tanggal ─── */
    function parseDate(s) {
        if (!s) return null;
        s = s.trim();
        // YYYY-MM-DD
        if (/^\d{4}-\d{1,2}-\d{1,2}$/.test(s)) {
            var d = new Date(s);
            return isNaN(d) ? null : s.replace(/(\d{4})-(\d{1,2})-(\d{1,2})/, function(_, y, m, d2) {
                return y + '-' + m.padStart(2,'0') + '-' + d2.padStart(2,'0');
            });
        }
        // DD/MM/YYYY atau DD-MM-YYYY
        var m2 = s.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
        if (m2) return m2[3] + '-' + m2[2].padStart(2,'0') + '-' + m2[1].padStart(2,'0');
        return null;
    }

    /* ─── detect apakah baris adalah header ─── */
    function looksLikeHeader(cells) {
        var joined = cells.join(' ').toLowerCase();
        return /nama|produk|nomor|grade|warna|berat|jumlah|tanggal|packing|keterangan|wh_oro|wh_bam/.test(joined);
    }

    /* ─── parse paste text ─── */
    function parsePaste(rawText, skipHeader) {
        var lines = rawText.replace(/\r\n/g, '\n').replace(/\r/g, '\n').trim().split('\n');
        if (!lines.length) return { valid: [], errors: [] };

        // split tiap baris by tab
        var rows = lines.map(function(l) {
            return l.split('\t').map(function(c) { return c.trim(); });
        });

        // skip baris pertama jika header
        var startIdx = 0;
        if (skipHeader && rows.length > 0) {
            startIdx = 1;
        } else if (rows.length > 0 && looksLikeHeader(rows[0])) {
            // auto-detect: meski checkbox tidak dicentang
            startIdx = 1;
        }

        var valid  = [];
        var errors = [];

        for (var i = startIdx; i < rows.length; i++) {
            var cells  = rows[i];
            var rowNum = i + 1; // nomor baris di Excel (dengan header dihitung)
            var rowData = {};
            var rowErrors = [];

            COLS.forEach(function(col, colIdx) {
                var val = (cells[colIdx] !== undefined) ? cells[colIdx] : '';

                // Tanggal: parse dan konversi
                if (col.key === 'tanggal') {
                    if (val === '') {
                        rowData[col.key] = new Date().toISOString().slice(0, 10); // default hari ini
                    } else {
                        var parsed = parseDate(val);
                        if (!parsed) {
                            rowErrors.push({ col: col.key, msg: 'Format tanggal tidak valid ("' + val + '")' });
                            rowData[col.key] = new Date().toISOString().slice(0, 10);
                        } else {
                            rowData[col.key] = parsed;
                        }
                    }
                    return;
                }

                // Numeric
                if (col.numeric) {
                    if (val === '') {
                        rowData[col.key] = '0';
                    } else {
                        // ganti koma desimal → titik
                        var num = val.replace(',', '.');
                        if (isNaN(parseFloat(num))) {
                            rowErrors.push({ col: col.key, msg: col.label + ' harus angka ("' + val + '")' });
                            rowData[col.key] = '0';
                        } else {
                            rowData[col.key] = num;
                        }
                    }
                    return;
                }

                // Required
                if (col.required && val === '') {
                    rowErrors.push({ col: col.key, msg: col.label + ' tidak boleh kosong' });
                }

                rowData[col.key] = val;
            });

            // tambahkan bagian kosong (kolom tambahan yg tidak ada di paste)
            if (rowData['bagian'] === undefined) rowData['bagian'] = '';

            var isValid = rowErrors.filter(function(e) {
                return COLS.find(function(c) { return c.key === e.col && c.required; });
            }).length === 0;

            if (isValid && rowErrors.length > 0) {
                // Ada warning tapi bukan required field — tetap valid
                rowData._warnings = rowErrors.map(function(e) { return e.msg; });
                rowData._invalid_cols = [];
            } else if (!isValid) {
                rowData._errors = rowErrors.map(function(e) { return e.msg; });
                rowData._invalid_cols = rowErrors.map(function(e) { return e.col; });
                errors.push({ row: rowNum, msgs: rowErrors.map(function(e) { return e.msg; }) });
            } else {
                rowData._invalid_cols = [];
            }

            valid.push(rowData);
        }

        return { valid: valid, errors: errors };
    }

    /* ─── build tabel preview ─── */
    function buildPreview(parsed) {
        // header
        var headHtml = '<th>#</th>';
        COLS.forEach(function(c) { headHtml += '<th>' + c.label + '</th>'; });
        previewHead.innerHTML = headHtml;

        // body
        previewBody.innerHTML = '';
        parsed.valid.forEach(function(row, idx) {
            var hasError = row._errors && row._errors.length;
            var tr = document.createElement('tr');
            if (hasError) tr.style.background = '#fff5f5';

            var td0 = document.createElement('td');
            td0.textContent = idx + 1;
            td0.style.color = '#888';
            td0.style.fontSize = '.8rem';
            tr.appendChild(td0);

            COLS.forEach(function(c) {
                var td = document.createElement('td');
                var isInvalidCol = row._invalid_cols && row._invalid_cols.indexOf(c.key) !== -1;
                td.textContent = row[c.key] !== undefined ? row[c.key] : '';
                td.style.fontSize = '.82rem';
                if (isInvalidCol) {
                    td.style.background = '#ffc';
                    td.style.color      = '#c00';
                    td.title = (row._errors || []).join('; ');
                }
                tr.appendChild(td);
            });

            // tooltip error di baris
            if (hasError) {
                tr.title = row._errors.join(' | ');
            }

            previewBody.appendChild(tr);
        });
    }

    /* ─── tombol preview ─── */
    if (btnPreview) {
        btnPreview.addEventListener('click', function() {
            var raw = textarea ? textarea.value : '';
            if (!raw.trim()) { alert('Paste data terlebih dahulu.'); return; }

            var skipHeader = cbSkip ? cbSkip.checked : true;
            var parsed = parsePaste(raw, skipHeader);

            // hitung valid (tanpa error required)
            var validOnly = parsed.valid.filter(function(r) {
                return !r._errors || r._errors.length === 0;
            });

            _validRows = validOnly.map(function(r) {
                // buang key internal
                var clean = {};
                COLS.forEach(function(c) { clean[c.key] = r[c.key] !== undefined ? r[c.key] : ''; });
                clean.bagian = r.bagian || '';
                return clean;
            });

            // summary
            var totalRows  = parsed.valid.length;
            var errorCount = parsed.errors.length;
            var validCount = _validRows.length;
            summary.innerHTML =
                '<span class="badge badge-success mr-2" style="font-size:.9rem">&#10003; ' + validCount + ' baris valid</span>' +
                (errorCount > 0 ? '<span class="badge badge-danger mr-2" style="font-size:.9rem">&#10007; ' + errorCount + ' baris error (dilewati)</span>' : '') +
                '<small class="text-muted">dari ' + totalRows + ' baris total</small>';

            // bangun tabel (semua baris, error disorot)
            buildPreview(parsed);

            // update tombol simpan
            if (saveCount) saveCount.textContent = validCount;
            if (btnSave)   btnSave.disabled = (validCount === 0);

            // switch ke step 2
            hide(step1);   show(step2);
            hide(footer1); show(footer2);
        });
    }

    /* ─── tombol reset ─── */
    if (btnReset) {
        btnReset.addEventListener('click', function() {
            if (textarea) textarea.value = '';
            if (pasteHint) pasteHint.textContent = '';
        });
    }

    /* ─── tombol back edit ─── */
    if (btnBackEdit) {
        btnBackEdit.addEventListener('click', function() {
            show(step1);   hide(step2);
            show(footer1); hide(footer2);
        });
    }

    /* ─── tombol simpan ─── */
    if (btnSave) {
        btnSave.addEventListener('click', function() {
            if (!_validRows.length) { alert('Tidak ada data valid.'); return; }
            if (!confirm('Simpan ' + _validRows.length + ' baris data?')) return;

            btnSave.disabled = true;
            btnSave.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';

            fetch(COMMIT_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rows: _validRows })
            })
            .then(function(r) {
                return r.json().then(function(d) { return { ok: r.ok, data: d }; });
            })
            .then(function(res) {
                if (!res.ok || !res.data.success) {
                    throw new Error(res.data.error || res.data.message || 'Gagal menyimpan.');
                }
                // tutup modal lalu reload
                if (window.jQuery) {
                    jQuery(modalEl).modal('hide');
                    jQuery(modalEl).on('hidden.bs.modal', function() { window.location.reload(); });
                } else {
                    window.location.reload();
                }
            })
            .catch(function(err) {
                alert('Error: ' + err.message);
                btnSave.disabled = false;
                btnSave.innerHTML = '<i class="fas fa-save mr-1"></i>Simpan <span id="piSaveCount">' + _validRows.length + '</span> Baris';
            });
        });
    }

    /* ─── update hint saat textarea dapat fokus ─── */
    if (textarea && pasteHint) {
        textarea.addEventListener('focus', function() {
            pasteHint.innerHTML = '<i class="fas fa-keyboard mr-1 text-success"></i><strong>Ctrl+V</strong> sekarang untuk paste data Excel';
        });
        textarea.addEventListener('paste', function() {
            setTimeout(function() {
                var lines = (textarea.value || '').trim().split('\n').length;
                pasteHint.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i>' + lines + ' baris terdeteksi — klik <strong>Preview</strong>';
            }, 50);
        });
        textarea.addEventListener('blur', function() {
            if (!textarea.value.trim()) {
                pasteHint.innerHTML = '<i class="fas fa-keyboard mr-1"></i>Ctrl+V setelah klik di dalam kotak di atas';
            }
        });
    }

    } // end init()

    // Jalankan setelah DOM ready
    if (window.jQuery) {
        jQuery(document).ready(function() {
            console.log('[PasteImportModal] Initializing...');
            init();
            
            // Manual initialization untuk tombol yang membuka modal
            // Ini memastikan modal bisa dibuka meskipun ada konflik Bootstrap
            jQuery('[data-bs-target="#pasteImportModal"]').off('click.pasteImportModal').on('click.pasteImportModal', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('[PasteImportModal] Button clicked, trying to open modal...');
                
                var $modal = jQuery('#pasteImportModal');
                if ($modal.length === 0) {
                    console.error('[PasteImportModal] Modal element not found in DOM');
                    alert('Error: Modal tidak ditemukan. Silakan refresh halaman.');
                    return;
                }
                
                // Cek apakah jQuery modal plugin tersedia
                if (typeof $modal.modal === 'function') {
                    try {
                        console.log('[PasteImportModal] Opening modal via jQuery...');
                        $modal.modal('show');
                    } catch (err) {
                        console.error('[PasteImportModal] Error opening modal:', err);
                        // Fallback: manual show
                        $modal.addClass('show').css('display', 'block');
                        jQuery('body').addClass('modal-open').append('<div class="modal-backdrop fade show"></div>');
                    }
                } else {
                    console.error('[PasteImportModal] jQuery modal plugin not available');
                    alert('Error: Bootstrap modal tidak tersedia. Silakan refresh halaman.');
                }
            });
            
            console.log('[PasteImportModal] Initialization complete');
        });
    } else {
        document.addEventListener('DOMContentLoaded', init);
    }

}());
</script>
