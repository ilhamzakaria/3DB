<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <form action="<?= base_url('plant_produksi/simpan') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- 1. Master Data -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Master Data (Nomor SPK)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <label>Nomor SPK</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="check_spk_baru">
                                <label class="form-check-label small" for="check_spk_baru">Baru</label>
                            </div>
                        </div>
                        <div id="spk_select_wrapper">
                            <select name="nomor_spk" id="nomor_spk" class="form-control select2" required>
                                <option value="">-- Pilih SPK --</option>
                                <?php foreach ($spk_list as $spk): ?>
                                    <option value="<?= $spk['no_spk'] ?>" data-mesin="<?= $spk['nama_mesin'] ?>" data-produk="<?= $spk['nama_produk'] ?>">
                                        <?= $spk['no_spk'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="spk_input_wrapper" style="display:none;">
                            <input type="text" id="nomor_spk_baru" class="form-control" placeholder="Input SPK Baru">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Nama Mesin</label>
                        <input type="text" name="nama_mesin" id="nama_mesin" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Nama Produksi</label>
                        <input type="text" name="nama_produksi" id="nama_produksi" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" id="batch_number" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label>Shift</label>
                        <select name="shift" id="shift" class="form-control">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Grup</label>
                        <input type="text" name="grup" id="grup" class="form-control">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Nomor Mesin</label>
                        <input type="text" name="nomor_mesin" id="nomor_mesin" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Packing</label>
                        <input type="text" name="packing" id="packing" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label>Cycle Time</label>
                        <input type="text" name="cycle_time" id="cycle_time" class="form-control">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Target</label>
                        <input type="number" name="target" id="target" class="form-control">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label>Operator</label>
                        <input type="text" name="operator" id="operator" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Isi Data Per Jam -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Produksi Per Jam</h6>
                <div class="d-flex align-items-center">
                    <select id="select_jam" class="form-control form-control-sm mr-2" style="width: 150px;">
                        <option value="">-- Pilih Jam --</option>
                        <?php 
                        $hours = [
                            '06-07','07-08','08-09','09-10','10-11','11-12','12-13','13-14',
                            '14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22',
                            '22-23','23-00','00-01','01-02','02-03','03-04','04-05','05-06'
                        ];
                        foreach ($hours as $h): ?>
                            <option value="<?= $h ?>"><?= $h ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm" id="btn_add_jam_row">
                        <i class="fas fa-plus"></i> Tambah Jam
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table_jam">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="30%">Jam</th>
                                <th>Hasil Produksi (pcs)</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows added dynamically -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right">Total Hasil Produksi:</th>
                                <th id="grand_total_hasil" colspan="2">0</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2b. Detail Kualitas & Reject (Total Shift) -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Detail Kualitas & Reject (Total Shift)</h6>
                <button type="button" class="btn btn-danger btn-sm" id="btn_open_reject_total">
                    <i class="fas fa-exclamation-triangle"></i> Input Reject
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Total Bagus (pcs)</label>
                        <input type="number" name="grand_total_bagus" id="grand_total_bagus" class="form-control form-control-lg border-primary" readonly>
                        <small class="text-muted">Total Hasil - Reject</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Total Reject (pcs)</label>
                        <input type="number" name="grand_total_reject" id="grand_total_reject" class="form-control form-control-lg border-danger" readonly>
                        <div id="reject_storage_total"></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Sisa PO</label>
                        <input type="number" name="sisa_po" id="sisa_po" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Hold</label>
                        <input type="number" name="hold" id="hold" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3 mb-3">
                        <label>Gumpalan</label>
                        <input type="number" name="gumpalan" id="gumpalan" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Isi</label>
                        <input type="number" name="isi" id="isi" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Material, Colorant & Packaging Section (Unified) -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-boxes mr-2"></i>Komposisi Material, Colorant & Packaging</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Primary Material -->
                    <div class="col-lg-5 border-right">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-2">Primary Material</label>
                        <div class="row">
                            <div class="col-md-7 mb-2">
                                <label class="text-xs">Merek Kode</label>
                                <input type="text" name="materials[0][merek_kode]" id="merek_kode" class="form-control form-control-sm border-left-primary">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="text-xs">Pemakaian (kg)</label>
                                <input type="number" step="0.01" name="materials[0][pemakaian]" id="m_pemakaian" class="form-control form-control-sm border-left-primary">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3"><label class="text-xs">Lot A</label><input type="text" name="materials[0][lot_a]" id="m_lot_a" class="form-control form-control-sm"></div>
                            <div class="col-3"><label class="text-xs">Lot B</label><input type="text" name="materials[0][lot_b]" id="m_lot_b" class="form-control form-control-sm"></div>
                            <div class="col-3"><label class="text-xs">Lot C</label><input type="text" name="materials[0][lot_c]" id="m_lot_c" class="form-control form-control-sm"></div>
                            <div class="col-3"><label class="text-xs">Lot D</label><input type="text" name="materials[0][lot_d]" id="m_lot_d" class="form-control form-control-sm"></div>
                        </div>
                    </div>

                    <!-- Colorant Detail -->
                    <div class="col-lg-4 border-right">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-2">Colorant Detail</label>
                        <div class="mb-2">
                            <label class="text-xs">Color Code</label>
                            <input type="text" name="colorants[0][code]" class="form-control form-control-sm border-left-info">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="text-xs">Lot No.</label>
                                <input type="text" name="colorants[0][nomor_lot]" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="text-xs">Pakai (kg)</label>
                                <input type="number" step="0.01" name="colorants[0][pemakaian]" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Packaging -->
                    <div class="col-lg-3">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-2">Packaging</label>
                        <div class="mb-2">
                            <label class="text-xs">Box / Nicktainer (pcs)</label>
                            <input type="number" name="box_karung_nicktainer" class="form-control form-control-sm border-left-warning">
                        </div>
                        <div>
                            <label class="text-xs">Plastik (pcs)</label>
                            <input type="number" name="plastik" class="form-control form-control-sm border-left-warning">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Down Time Section -->
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-stopwatch mr-2"></i>Downtime & Machine Stops</h6>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" id="btn_add_dt">
                    <i class="fas fa-plus mr-1"></i> Tambah Downtime
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="table_downtime">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-top-0 px-4">Alasan / Penyebab</th>
                                <th class="border-top-0" width="200">Waktu Mulai</th>
                                <th class="border-top-0" width="200">Waktu Selesai</th>
                                <th class="border-top-0 text-center" width="120">Durasi (Mnt)</th>
                                <th class="border-top-0 text-center" width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Downtime rows -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. Catatan & Signature Section -->
        <div class="card shadow mb-4 overflow-hidden">
            <div class="row no-gutters">
                <div class="col-md-5 bg-light p-4 border-right">
                    <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-comment-alt mr-2"></i>Catatan Produksi</h6>
                    <textarea name="catatan" class="form-control border-0 shadow-sm" rows="10" placeholder="Tambahkan instruksi khusus, masalah teknis, atau informasi penting lainnya di sini..."></textarea>
                </div>
                <div class="col-md-7 p-4">
                    <h6 class="font-weight-bold text-primary mb-4 text-center"><i class="fas fa-signature mr-2"></i>Verifikasi & Pengesahan</h6>
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm position-relative">
                                <div class="upload-loader" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 10; align-items: center; justify-content: center; border-radius: 8px;">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="text-xs text-primary mt-1 font-weight-bold">UPLOADING...</div>
                                    </div>
                                </div>
                                <img id="preview_ttd_1" src="<?= base_url('assets/img/upload_placeholder.png') ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_shift_1" class="d-none sig-input" data-preview="preview_ttd_1" accept="image/*">
                                <button type="button" class="btn btn-outline-primary btn-xs btn-block btn-upload-sig">Upload</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small">Operator Shift 1</div>
                        </div>
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm position-relative">
                                <div class="upload-loader" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 10; align-items: center; justify-content: center; border-radius: 8px;">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="text-xs text-primary mt-1 font-weight-bold">UPLOADING...</div>
                                    </div>
                                </div>
                                <img id="preview_ttd_2" src="<?= base_url('assets/img/upload_placeholder.png') ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_shift_2" class="d-none sig-input" data-preview="preview_ttd_2" accept="image/*">
                                <button type="button" class="btn btn-outline-primary btn-xs btn-block btn-upload-sig">Upload</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small">Operator Shift 2</div>
                        </div>
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm position-relative">
                                <div class="upload-loader" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 10; align-items: center; justify-content: center; border-radius: 8px;">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="text-xs text-primary mt-1 font-weight-bold">UPLOADING...</div>
                                    </div>
                                </div>
                                <img id="preview_ttd_3" src="<?= base_url('assets/img/upload_placeholder.png') ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_shift_3" class="d-none sig-input" data-preview="preview_ttd_3" accept="image/*">
                                <button type="button" class="btn btn-outline-primary btn-xs btn-block btn-upload-sig">Upload</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small">Operator Shift 3</div>
                        </div>
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm border-primary position-relative">
                                <div class="upload-loader" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 10; align-items: center; justify-content: center; border-radius: 8px;">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="text-xs text-primary mt-1 font-weight-bold">UPLOADING...</div>
                                    </div>
                                </div>
                                <img id="preview_ttd_spv" src="<?= base_url('assets/img/upload_placeholder.png') ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_spv" class="d-none sig-input" data-preview="preview_ttd_spv" accept="image/*">
                                <button type="button" class="btn btn-primary btn-xs btn-block btn-upload-sig">Upload</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small text-primary text-uppercase small">Supervisor</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 py-4 text-right">
                <a href="<?= base_url('plant_produksi') ?>" class="btn btn-light px-4 mr-2">Batal</a>
                <button type="submit" id="btn_simpan" class="btn btn-primary btn-lg px-5 shadow">
                    <i class="fas fa-save mr-2"></i> <span id="btn_text">Simpan Laporan Produksi</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jenis Reject - Jam <span id="reject_jam_title"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php 
                $rejectTypes = [
                    "Start Up", "Karantina", "Trial", "Camera", "Button Putih", "Oval", 
                    "Flashing", "Short Shoot", "Kotor", "Beda Warna", "Sampling QC", 
                    "Kontaminasi", "Black Spot", "Gosong", "Struktur Tidak Standar", 
                    "Inject Point Tidak Standar", "Bolong", "Bubble", "Berair", "Neck Panjang"
                ];
                foreach ($rejectTypes as $type): ?>
                <div class="row mb-2">
                    <div class="col-md-7"><label><?= $type ?></label></div>
                    <div class="col-md-5">
                        <input type="number" class="form-control form-control-sm input-reject-detail" data-type="<?= $type ?>">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_reject">Selesai</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shiftSelect = document.getElementById('shift');
    const tableJamBody = document.querySelector('#table_jam tbody');
    const nomorSpkSelect = document.getElementById('nomor_spk');
    const checkSpkBaru = document.getElementById('check_spk_baru');
    const spkSelectWrapper = document.getElementById('spk_select_wrapper');
    const spkInputWrapper = document.getElementById('spk_input_wrapper');
    const nomorSpkBaru = document.getElementById('nomor_spk_baru');

    // Toggle SPK Baru
    checkSpkBaru.addEventListener('change', function() {
        if (this.checked) {
            spkSelectWrapper.style.display = 'none';
            spkInputWrapper.style.display = 'block';
            nomorSpkSelect.removeAttribute('name');
            nomorSpkBaru.setAttribute('name', 'nomor_spk');
            nomorSpkBaru.setAttribute('required', true);
            nomorSpkSelect.removeAttribute('required');
        } else {
            spkSelectWrapper.style.display = 'block';
            spkInputWrapper.style.display = 'none';
            nomorSpkBaru.removeAttribute('name');
            nomorSpkSelect.setAttribute('name', 'nomor_spk');
            nomorSpkSelect.setAttribute('required', true);
            nomorSpkBaru.removeAttribute('required');
        }
    });
    
    function fetchSpkData(spk) {
        if (!spk) return;
        console.log('Fetching data for SPK:', spk);
        fetch('<?= base_url('plant_produksi/get_last_data') ?>/' + encodeURIComponent(spk))
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                const setVal = (id, val) => {
                    const el = document.getElementById(id);
                    if (el) el.value = val || '';
                };

                // Helper to populate Reject Storage
                const populateRejects = (rejects) => {
                    const storage = document.getElementById('reject_storage_total');
                    storage.innerHTML = '';
                    let total = 0;
                    if (rejects) {
                        rejects.forEach(r => {
                            const hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = `rejects[${r.jenis_reject}]`;
                            hiddenInput.value = r.jumlah;
                            hiddenInput.dataset.type = r.jenis_reject;
                            storage.appendChild(hiddenInput);
                            total += parseInt(r.jumlah) || 0;
                        });
                    }
                    document.getElementById('grand_total_reject').value = total;
                };

                // Clear previous dynamic data
                document.querySelector('#table_downtime tbody').innerHTML = '';
                dtCount = 0;

                // Priority 1: Data from Previous Production (New Module)
                if (data.last) {
                    setVal('nama_mesin', data.last.nama_mesin);
                    setVal('nama_produksi', data.last.nama_produksi);
                    setVal('nomor_mesin', data.last.nomor_mesin);
                    setVal('shift', data.last.shift);
                    setVal('target', data.last.target);
                    setVal('operator', data.last.operator);
                    setVal('batch_number', data.last.batch_number);
                    setVal('grup', data.last.grup);
                    setVal('packing', data.last.packing);
                    setVal('cycle_time', data.last.cycle_time);
                    setVal('isi', data.last.isi);
                    setVal('sisa_po', data.last.sisa_po);
                    setVal('hold', data.last.hold);
                    setVal('gumpalan', data.last.gumpalan);
                    document.querySelector('textarea[name="catatan"]').value = data.last.catatan || '';

                    populateRejects(data.rejects);

                    if (data.materials && data.materials[0]) {
                        const m = data.materials[0];
                        setVal('merek_kode', m.merek_kode);
                        setVal('m_pemakaian', m.pemakaian);
                        setVal('m_lot_a', m.lot_a);
                        setVal('m_lot_b', m.lot_b);
                        setVal('m_lot_c', m.lot_c);
                        setVal('m_lot_d', m.lot_d);
                    }
                    if (data.colorants && data.colorants[0]) {
                        const c = data.colorants[0];
                        document.getElementsByName('colorants[0][code]')[0].value = c.code || '';
                        document.getElementsByName('colorants[0][nomor_lot]')[0].value = c.nomor_lot || '';
                        document.getElementsByName('colorants[0][pemakaian]')[0].value = c.pemakaian || '';
                    }
                    if (data.packaging) {
                        document.getElementsByName('box_karung_nicktainer')[0].value = data.packaging.box_karung_nicktainer || 0;
                        document.getElementsByName('plastik')[0].value = data.packaging.plastik || 0;
                    }
                    if (data.downtimes) {
                        data.downtimes.forEach(dt => addDowntimeRow(dt));
                    }
                    updateGrandTotal();
                }
                // Priority 2: Fallback to Legacy
                else if (data.legacy) {
                    setVal('nama_mesin', data.legacy.nama_mesin);
                    setVal('nama_produksi', data.legacy.nama_produk); 
                    setVal('nomor_mesin', data.legacy.nomor_mesin);
                    setVal('shift', data.legacy.shif); 
                    setVal('target', data.legacy.target);
                    setVal('operator', data.legacy.operator);
                    setVal('batch_number', data.legacy.batch_number);
                    setVal('grup', data.legacy.grup);
                    setVal('packing', data.legacy.packing);
                    setVal('cycle_time', data.legacy.cycle_time);
                    setVal('isi', data.legacy.isi);
                    
                    const legacyRejects = [];
                    const types = ["Start Up", "Karantina", "Trial", "Camera", "Button Putih", "Oval", "Flashing", "Short Shoot", "Kotor", "Beda Warna", "Sampling QC", "Kontaminasi", "Black Spot", "Gosong", "Struktur Tidak Standar", "Inject Point Tidak Standar", "Bolong", "Bubble", "Berair", "Neck Panjang"];
                    types.forEach(t => {
                        const col = t.toLowerCase().replace(/ /g, '_');
                        if (data.legacy[col] && data.legacy[col] > 0) {
                            legacyRejects.push({ jenis_reject: t, jumlah: data.legacy[col] });
                        }
                    });
                    populateRejects(legacyRejects);

                    setVal('merek_kode', data.legacy.merek_kode);
                    setVal('m_pemakaian', data.legacy.m_pemakaian);
                    setVal('m_lot_a', data.legacy.m_no_lot1);
                    setVal('m_lot_b', data.legacy.m_no_lot2);
                    setVal('m_lot_c', data.legacy.m_no_lot3);
                    setVal('m_lot_d', data.legacy.m_no_lot4);

                    document.getElementsByName('colorants[0][code]')[0].value = data.legacy.c_kode || '';
                    document.getElementsByName('colorants[0][nomor_lot]')[0].value = data.legacy.c_no_lot || '';
                    document.getElementsByName('colorants[0][pemakaian]')[0].value = data.legacy.c_pemakaian || '';

                    document.getElementsByName('box_karung_nicktainer')[0].value = data.legacy.box || 0;
                    document.getElementsByName('plastik')[0].value = data.legacy.plastik || 0;

                    updateGrandTotal();
                }
            })
            .catch(error => console.error('Error fetching SPK data:', error));
    }

    // Event Listeners for SPK Selection
    nomorSpkSelect.addEventListener('change', function() {
        console.log('SPK Selected:', this.value);
        fetchSpkData(this.value);
    });

    nomorSpkBaru.addEventListener('input', function() {
        // Only fetch if more than 3 chars to avoid too many requests
        if (this.value.length >= 3) {
            fetchSpkData(this.value);
        }
    });

    // Dynamic Hourly Rows
    let jamRowCount = 0;
    const selectJam = document.getElementById('select_jam');
    const btnAddJamRow = document.getElementById('btn_add_jam_row');

    btnAddJamRow.addEventListener('click', function() {
        const jam = selectJam.value;
        if (!jam) {
            alert('Pilih jam terlebih dahulu');
            return;
        }

        // Check if jam already exists
        let exists = false;
        document.querySelectorAll('input[name*="[rentang_jam]"]').forEach(input => {
            if (input.value === jam) exists = true;
        });

        if (exists) {
            alert('Jam ini sudah ditambahkan');
            return;
        }

        const row = `
            <tr>
                <td>
                    <input type="text" name="jam_data[${jamRowCount}][rentang_jam]" value="${jam}" class="form-control-plaintext text-center" readonly>
                </td>
                <td><input type="number" name="jam_data[${jamRowCount}][hasil_produksi]" class="form-control jam-hasil" required></td>
                <td class="text-center">
                    <button type="button" class="btn btn-link btn-sm text-danger remove-row">×</button>
                </td>
            </tr>
        `;
        tableJamBody.insertAdjacentHTML('beforeend', row);
        jamRowCount++;
        updateGrandTotal();
    });

    tableJamBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
            updateGrandTotal();
        }
    });

    // Reject Modal Handling (Total Shift)
    document.getElementById('btn_open_reject_total').addEventListener('click', function() {
        document.getElementById('reject_jam_title').innerText = "Total Shift";
        
        // Clear inputs in modal
        document.querySelectorAll('.input-reject-detail').forEach(input => input.value = '');
        
        // Load existing from total storage
        const storage = document.getElementById('reject_storage_total');
        storage.querySelectorAll('input').forEach(input => {
            const type = input.dataset.type;
            const match = document.querySelector(`.input-reject-detail[data-type="${type}"]`);
            if (match) match.value = input.value;
        });

        $('#modalReject').modal('show');
    });

    document.getElementById('btn_save_reject').addEventListener('click', function() {
        const storage = document.getElementById('reject_storage_total');
        storage.innerHTML = '';
        let total = 0;
        document.querySelectorAll('.input-reject-detail').forEach(input => {
            const val = parseInt(input.value) || 0;
            if (val > 0) {
                total += val;
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `rejects[${input.dataset.type}]`; // Shift level rejects
                hiddenInput.value = val;
                hiddenInput.dataset.type = input.dataset.type;
                storage.appendChild(hiddenInput);
            }
        });
        document.getElementById('grand_total_reject').value = total;
        $('#modalReject').modal('hide');
        updateGrandTotal();
    });

    function updateGrandTotal() {
        let totalHasil = 0;
        document.querySelectorAll('.jam-hasil').forEach(i => totalHasil += (parseInt(i.value) || 0));
        document.getElementById('grand_total_hasil').innerText = totalHasil;
        
        let totalReject = parseInt(document.getElementById('grand_total_reject').value) || 0;
        document.getElementById('grand_total_bagus').value = totalHasil - totalReject;
    }

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('jam-hasil')) {
            updateGrandTotal();
        }
    });

    // Dynamic Row Helpers
    let dtCount = 0;
    function addDowntimeRow(data = {}) {
        const html = `
            <tr>
                <td><input type="text" name="downtimes[${dtCount}][alasan]" value="${data.alasan_downtime || ''}" class="form-control"></td>
                <td><input type="datetime-local" name="downtimes[${dtCount}][mulai]" value="${data.waktu_mulai ? data.waktu_mulai.replace(' ', 'T').substring(0,16) : ''}" class="form-control dt-mulai"></td>
                <td><input type="datetime-local" name="downtimes[${dtCount}][selesai]" value="${data.waktu_selesai ? data.waktu_selesai.replace(' ', 'T').substring(0,16) : ''}" class="form-control dt-selesai"></td>
                <td><input type="number" name="downtimes[${dtCount}][durasi]" value="${data.durasi_menit || ''}" class="form-control dt-durasi"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
            </tr>
        `;
        document.querySelector('#table_downtime tbody').insertAdjacentHTML('beforeend', html);
        dtCount++;
    }

    document.getElementById('btn_add_dt').addEventListener('click', () => addDowntimeRow());

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            if (e.target.closest('tr')) e.target.closest('tr').remove();
        }
    });

    // Signature Upload Preview Handling
    document.querySelectorAll('.btn-upload-sig').forEach(btn => {
        btn.addEventListener('click', function() {
            this.parentElement.querySelector('.sig-input').click();
        });
    });

    document.querySelectorAll('.sig-input').forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.dataset.preview;
            const previewImg = document.getElementById(previewId);
            const box = this.closest('.signature-upload-box');
            const loader = box.querySelector('.upload-loader');
            
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Validasi tipe file
                if (!file.type.startsWith('image/')) {
                    alert('Error: File harus berupa gambar!');
                    this.value = '';
                    return;
                }

                // Tampilkan loading
                loader.style.display = 'flex';
                
                const reader = new FileReader();
                reader.onload = (e) => {
                    setTimeout(() => { // Simulasi sedikit biar kerasa loading-nya
                        previewImg.src = e.target.result;
                        loader.style.display = 'none';
                        // Tambahkan border hijau tanda sukses
                        box.classList.add('border-success');
                        setTimeout(() => box.classList.remove('border-success'), 2000);
                    }, 500);
                };
                reader.onerror = () => {
                    alert('Gagal membaca file gambar.');
                    loader.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    });
    // Form Submission Loading
    const form = document.querySelector('form');
    const btnSimpan = document.getElementById('btn_simpan');
    const btnText = document.getElementById('btn_text');

    form.addEventListener('submit', function() {
        btnSimpan.disabled = true;
        btnSimpan.classList.add('btn-secondary');
        btnText.innerText = 'Sedang Menyimpan...';
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Memproses Laporan...';
    });
});
</script>

<?= $this->endSection() ?>
