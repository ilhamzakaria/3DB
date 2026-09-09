<?php

/**
 * Import Excel Modal Partial
 * Works with both Bootstrap 4 (jQuery required) and Bootstrap 5
 */
$type      = $type      ?? 'galon';
$typeLabel = $type_label ?? 'Galon';
$modalId   = 'importExcelModal';
?>

<!-- Modal Import Excel -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-excel mr-2"></i> Import Excel &mdash; Finish Good <?= esc($typeLabel) ?>
                </h5>
                <!-- close button: works for both BS4 and BS5 -->
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- STEP 1: Upload -->
                <div id="fgImportStep1">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Kolom header Excel yang diperlukan:</strong>
                        <?php if ($type === 'galon'): ?>
                            <code>BAGIAN, NOMOR, NAMA_PRODUK, GRADE, BERAT, WH_ORO, WH_BAM, TANGGAL</code>
                        <?php elseif ($type === 'screw_cap'): ?>
                            <code>BAGIAN, NOMOR, NAMA_PRODUK, WARNA, BERAT, JUMLAH, KETERANGAN, TANGGAL</code>
                        <?php else: ?>
                            <code>BAGIAN, NOMOR, NAMA_PRODUK, WARNA, BERAT, JUMLAH, PACKING, TANGGAL</code>
                        <?php endif; ?>
                        <br><small>Format tanggal: <code>YYYY-MM-DD</code> atau <code>DD/MM/YYYY</code>. Kolom BAGIAN boleh kosong.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold">File Excel (.xlsx / .xls)</label>
                        <input type="file" class="form-control" id="fgExcelFile" accept=".xlsx,.xls">
                    </div>

                    <div>
                        <a href="<?= base_url('ppic/finish_good/' . $type . '/template') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download mr-1"></i> Download Template
                        </a>
                    </div>

                    <div id="fgUploadProgress" class="mt-3 d-none">
                        <div class="progress" style="height:22px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated w-100">
                                <i class="fas fa-spinner fa-spin mr-1"></i> Membaca file...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Preview -->
                <div id="fgImportStep2" style="display:none">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="h4 text-success mb-0" id="fgValidCount">0</div>
                                <small class="text-muted">Baris Valid</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="h4 text-danger mb-0" id="fgErrorCount">0</div>
                                <small class="text-muted">Baris Error</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <div class="h4 text-info mb-0" id="fgTotalCount">0</div>
                                <small class="text-muted">Total Baris</small>
                            </div>
                        </div>
                    </div>

                    <div id="fgErrorBox" class="alert alert-warning d-none">
                        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Baris dengan error (akan dilewati):</strong>
                        <ul id="fgErrorList" class="mb-0 mt-2" style="max-height:150px;overflow-y:auto;font-size:.85rem"></ul>
                    </div>

                    <div>
                        <strong>Preview data valid (maks 10 baris):</strong>
                        <div class="table-responsive mt-2" style="max-height:250px;overflow-y:auto">
                            <table class="table table-sm table-bordered" id="fgPreviewTable">
                                <thead class="thead-light">
                                    <tr id="fgPreviewHead"></tr>
                                </thead>
                                <tbody id="fgPreviewBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <!-- Step 1 footer -->
                <div id="fgFooter1">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="fgBtnPreview">
                        <i class="fas fa-eye mr-1"></i> Preview Data
                    </button>
                </div>
                <!-- Step 2 footer -->
                <div id="fgFooter2" style="display:none">
                    <button type="button" class="btn btn-secondary" id="fgBtnBack">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </button>
                    <button type="button" class="btn btn-success" id="fgBtnCommit" disabled>
                        <i class="fas fa-check mr-1"></i> Konfirmasi Import
                        (<span id="fgCommitCount">0</span> baris)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        'use strict';
        var _type = <?= json_encode($type) ?>;
        var _validRows = [];

        /* ── helpers ── */
        function show(el) {
            if (el) el.style.display = '';
        }

        function hide(el) {
            if (el) el.style.display = 'none';
        }

        function closeModal() {
            var el = document.getElementById('importExcelModal');
            if (!el) return;
            // Bootstrap 5
            if (window.bootstrap && bootstrap.Modal) {
                var inst = bootstrap.Modal.getInstance(el);
                if (inst) {
                    inst.hide();
                    return;
                }
            }
            // Bootstrap 4 via jQuery
            if (window.$ && $(el).modal) {
                $(el).modal('hide');
            }
        }

        function resetToStep1() {
            show(document.getElementById('fgImportStep1'));
            hide(document.getElementById('fgImportStep2'));
            show(document.getElementById('fgFooter1'));
            hide(document.getElementById('fgFooter2'));
            hide(document.getElementById('fgUploadProgress'));
            var fi = document.getElementById('fgExcelFile');
            if (fi) fi.value = '';
            _validRows = [];
        }

        /* ── reset on close ── */
        var modalEl = document.getElementById('importExcelModal');
        if (modalEl) {
            var importTrigger = document.getElementById('btnImportExcel');
            if (importTrigger) {
                importTrigger.addEventListener('click', function() {
                    if (!window.bootstrap || !bootstrap.Modal) {
                        alert('Komponen modal belum siap. Silakan refresh halaman.');
                        return;
                    }
                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
                        backdrop: true,
                        keyboard: true
                    });
                    modal.show();
                });
            }
            // BS5
            modalEl.addEventListener('hidden.bs.modal', resetToStep1);
            // BS4 via jQuery
            if (window.$) $(modalEl).on('hidden.bs.modal', resetToStep1);
        }

        /* ── preview button ── */
        var btnPreview = document.getElementById('fgBtnPreview');
        if (btnPreview) {
            btnPreview.addEventListener('click', function() {
                var fi = document.getElementById('fgExcelFile');
                if (!fi || !fi.files.length) {
                    alert('Pilih file Excel terlebih dahulu.');
                    return;
                }

                var formData = new FormData();
                formData.append('excel_file', fi.files[0]);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                show(document.getElementById('fgUploadProgress'));
                btnPreview.disabled = true;

                fetch('<?= base_url('ppic/finish_good/' . $type . '/import-preview') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(function(r) {
                        return r.json().then(function(d) {
                            return {
                                ok: r.ok,
                                data: d
                            };
                        });
                    })
                    .then(function(res) {
                        hide(document.getElementById('fgUploadProgress'));
                        btnPreview.disabled = false;

                        if (!res.ok) {
                            alert(res.data.error || 'Gagal memproses file.');
                            return;
                        }

                        var d = res.data;
                        _validRows = d.valid || [];

                        document.getElementById('fgValidCount').textContent = d.valid_count || 0;
                        document.getElementById('fgErrorCount').textContent = d.error_count || 0;
                        document.getElementById('fgTotalCount').textContent = d.total || 0;
                        document.getElementById('fgCommitCount').textContent = d.valid_count || 0;

                        /* errors */
                        var errBox = document.getElementById('fgErrorBox');
                        var errList = document.getElementById('fgErrorList');
                        if (d.errors && d.errors.length) {
                            errList.innerHTML = '';
                            d.errors.forEach(function(e) {
                                var li = document.createElement('li');
                                li.textContent = 'Baris ' + e.row + ': ' + e.message;
                                errList.appendChild(li);
                            });
                            show(errBox);
                        } else {
                            hide(errBox);
                        }

                        /* preview table */
                        if (_validRows.length) {
                            var keys = Object.keys(_validRows[0]);
                            document.getElementById('fgPreviewHead').innerHTML =
                                keys.map(function(k) {
                                    return '<th>' + k.toUpperCase() + '</th>';
                                }).join('');

                            var tbody = document.getElementById('fgPreviewBody');
                            tbody.innerHTML = '';
                            _validRows.slice(0, 10).forEach(function(row) {
                                var tr = document.createElement('tr');
                                tr.innerHTML = keys.map(function(k) {
                                    return '<td>' + (row[k] || '') + '</td>';
                                }).join('');
                                tbody.appendChild(tr);
                            });
                        }

                        document.getElementById('fgBtnCommit').disabled = (_validRows.length === 0);

                        hide(document.getElementById('fgImportStep1'));
                        show(document.getElementById('fgImportStep2'));
                        hide(document.getElementById('fgFooter1'));
                        show(document.getElementById('fgFooter2'));
                    })
                    .catch(function(err) {
                        hide(document.getElementById('fgUploadProgress'));
                        btnPreview.disabled = false;
                        alert('Error: ' + err.message);
                    });
            });
        }

        /* ── back button ── */
        var btnBack = document.getElementById('fgBtnBack');
        if (btnBack) {
            btnBack.addEventListener('click', function() {
                show(document.getElementById('fgImportStep1'));
                hide(document.getElementById('fgImportStep2'));
                show(document.getElementById('fgFooter1'));
                hide(document.getElementById('fgFooter2'));
            });
        }

        /* ── commit button ── */
        var btnCommit = document.getElementById('fgBtnCommit');
        if (btnCommit) {
            btnCommit.addEventListener('click', function() {
                if (!_validRows.length) {
                    alert('Tidak ada data valid.');
                    return;
                }
                if (!confirm('Import ' + _validRows.length + ' baris data?')) return;

                btnCommit.disabled = true;
                btnCommit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

                fetch('<?= base_url('ppic/finish_good/' . $type . '/import-commit') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                        },
                        body: JSON.stringify({
                            rows: _validRows
                        })
                    })
                    .then(function(r) {
                        return r.json().then(function(d) {
                            return {
                                ok: r.ok,
                                data: d
                            };
                        });
                    })
                    .then(function(res) {
                        if (!res.ok) {
                            throw new Error(res.data.error || 'Gagal menyimpan data.');
                        }
                        closeModal();
                        setTimeout(function() {
                            window.location.reload();
                        }, 400);
                    })
                    .catch(function(err) {
                        alert('Error: ' + err.message);
                        btnCommit.disabled = false;
                        btnCommit.innerHTML = '<i class="fas fa-check mr-1"></i> Konfirmasi Import (<span id="fgCommitCount">' + _validRows.length + '</span> baris)';
                    });
            });
        }
    }());
</script>