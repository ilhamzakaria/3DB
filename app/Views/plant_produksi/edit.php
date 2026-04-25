<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <form action="<?= base_url('plant_produksi/update/' . $header['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <!-- 1. Master Data -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Master Data (Nomor SPK: <?= $header['nomor_spk'] ?>)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Nomor SPK</label>
                        <input type="text" name="nomor_spk" value="<?= $header['nomor_spk'] ?>" class="form-control" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Nama Mesin</label>
                        <input type="text" name="nama_mesin" id="nama_mesin" value="<?= $header['nama_mesin'] ?>" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Nama Produksi</label>
                        <input type="text" name="nama_produksi" id="nama_produksi" value="<?= $header['nama_produksi'] ?>" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" id="batch_number" value="<?= $header['batch_number'] ?>" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label>Shift</label>
                        <select name="shift" id="shift" class="form-control">
                            <option value="1" <?= $header['shift'] == '1' ? 'selected' : '' ?>>1</option>
                            <option value="2" <?= $header['shift'] == '2' ? 'selected' : '' ?>>2</option>
                            <option value="3" <?= $header['shift'] == '3' ? 'selected' : '' ?>>3</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Grup</label>
                        <input type="text" name="grup" id="grup" value="<?= $header['grup'] ?>" class="form-control">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Nomor Mesin</label>
                        <input type="text" name="nomor_mesin" id="nomor_mesin" value="<?= $header['nomor_mesin'] ?>" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Packing</label>
                        <input type="text" name="packing" id="packing" value="<?= $header['packing'] ?>" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $header['tanggal'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label>Cycle Time</label>
                        <input type="text" name="cycle_time" id="cycle_time" value="<?= $header['cycle_time'] ?>" class="form-control">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Target</label>
                        <input type="number" name="target" id="target" value="<?= $header['target'] ?>" class="form-control">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label>Operator</label>
                        <input type="text" name="operator" id="operator" value="<?= $header['operator'] ?>" class="form-control">
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
                            <?php foreach ($jams as $i => $jam): ?>
                            <tr>
                                <td>
                                    <input type="text" name="jam_data[<?= $i ?>][rentang_jam]" value="<?= $jam['rentang_jam'] ?>" class="form-control-plaintext text-center" readonly>
                                </td>
                                <td><input type="number" name="jam_data[<?= $i ?>][hasil_produksi]" value="<?= $jam['hasil_produksi'] ?>" class="form-control jam-hasil" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link btn-sm text-danger remove-row">×</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
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
                    <i class="fas fa-exclamation-triangle"></i> Edit Reject
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Total Bagus (pcs)</label>
                        <input type="number" name="grand_total_bagus" id="grand_total_bagus" value="<?= $header['grand_total_bagus'] ?>" class="form-control form-control-lg border-primary" readonly>
                        <small class="text-muted">Total Hasil - Reject</small>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Total Reject (pcs)</label>
                        <input type="number" name="grand_total_reject" id="grand_total_reject" value="<?= $header['grand_total_reject'] ?>" class="form-control form-control-lg border-danger" readonly>
                        <div id="reject_storage_total">
                            <?php foreach ($rejects as $reject): ?>
                                <input type="hidden" name="rejects[<?= $reject['jenis_reject'] ?>]" value="<?= $reject['jumlah'] ?>" data-type="<?= $reject['jenis_reject'] ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Sisa PO</label>
                        <input type="number" name="sisa_po" id="sisa_po" value="<?= $header['sisa_po'] ?>" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Hold</label>
                        <input type="number" name="hold" id="hold" value="<?= $header['hold'] ?>" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3 mb-3">
                        <label>Gumpalan</label>
                        <input type="number" name="gumpalan" id="gumpalan" value="<?= $header['gumpalan'] ?>" class="form-control">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Isi</label>
                        <input type="number" name="isi" id="isi" value="<?= $header['isi'] ?>" class="form-control">
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
                                <input type="text" name="materials[0][merek_kode]" id="merek_kode" value="<?= $materials[0]['merek_kode'] ?? '' ?>" class="form-control form-control-sm border-left-primary">
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="text-xs">Pemakaian (kg)</label>
                                <input type="number" step="0.01" name="materials[0][pemakaian]" id="m_pemakaian" value="<?= $materials[0]['pemakaian'] ?? '' ?>" class="form-control form-control-sm border-left-primary">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3"><label class="text-xs">Lot A</label><input type="text" name="materials[0][lot_a]" id="m_lot_a" value="<?= $materials[0]['lot_a'] ?? '' ?>" class="form-control form-control-sm"></div>
                            <div class="col-3"><label class="text-xs">Lot B</label><input type="text" name="materials[0][lot_b]" id="m_lot_b" value="<?= $materials[0]['lot_b'] ?? '' ?>" class="form-control form-control-sm"></div>
                            <div class="col-3"><label class="text-xs">Lot C</label><input type="text" name="materials[0][lot_c]" id="m_lot_c" value="<?= $materials[0]['lot_c'] ?? '' ?>" class="form-control form-control-sm"></div>
                            <div class="col-3"><label class="text-xs">Lot D</label><input type="text" name="materials[0][lot_d]" id="m_lot_d" value="<?= $materials[0]['lot_d'] ?? '' ?>" class="form-control form-control-sm"></div>
                        </div>
                    </div>

                    <!-- Colorant Detail -->
                    <div class="col-lg-4 border-right">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-2">Colorant Detail</label>
                        <div class="mb-2">
                            <label class="text-xs">Color Code</label>
                            <input type="text" name="colorants[0][code]" value="<?= $colorants[0]['code'] ?? '' ?>" class="form-control form-control-sm border-left-info">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="text-xs">Lot No.</label>
                                <input type="text" name="colorants[0][nomor_lot]" value="<?= $colorants[0]['nomor_lot'] ?? '' ?>" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="text-xs">Pakai (kg)</label>
                                <input type="number" step="0.01" name="colorants[0][pemakaian]" value="<?= $colorants[0]['pemakaian'] ?? '' ?>" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Packaging -->
                    <div class="col-lg-3">
                        <label class="text-xs font-weight-bold text-uppercase text-muted mb-2">Packaging</label>
                        <div class="mb-2">
                            <label class="text-xs">Box / Nicktainer (pcs)</label>
                            <input type="number" name="box_karung_nicktainer" value="<?= $packaging['box_karung_nicktainer'] ?? 0 ?>" class="form-control form-control-sm border-left-warning">
                        </div>
                        <div>
                            <label class="text-xs">Plastik (pcs)</label>
                            <input type="number" name="plastik" value="<?= $packaging['plastik'] ?? 0 ?>" class="form-control form-control-sm border-left-warning">
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
                            <?php foreach ($downtimes as $i => $dt): ?>
                            <tr>
                                <td><input type="text" name="downtimes[<?= $i ?>][alasan]" value="<?= $dt['alasan_downtime'] ?>" class="form-control"></td>
                                <td><input type="datetime-local" name="downtimes[<?= $i ?>][mulai]" value="<?= date('Y-m-d\TH:i', strtotime($dt['waktu_mulai'])) ?>" class="form-control dt-mulai"></td>
                                <td><input type="datetime-local" name="downtimes[<?= $i ?>][selesai]" value="<?= date('Y-m-d\TH:i', strtotime($dt['waktu_selesai'])) ?>" class="form-control dt-selesai"></td>
                                <td><input type="number" name="downtimes[<?= $i ?>][durasi]" value="<?= $dt['durasi_menit'] ?>" class="form-control dt-durasi"></td>
                                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
                            </tr>
                            <?php endforeach; ?>
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
                    <textarea name="catatan" class="form-control border-0 shadow-sm" rows="10" placeholder="Tambahkan instruksi khusus, masalah teknis, atau informasi penting lainnya di sini..."><?= $header['catatan'] ?></textarea>
                </div>
                <div class="col-md-7 p-4">
                    <h6 class="font-weight-bold text-primary mb-4 text-center"><i class="fas fa-signature mr-2"></i>Verifikasi & Pengesahan</h6>
                    <div class="row text-center">
                        <?php 
                        $ttd1 = $header['ttd_shift_1'] ? base_url('uploads/ttd/' . $header['ttd_shift_1']) : base_url('assets/img/upload_placeholder.png');
                        $ttd2 = $header['ttd_shift_2'] ? base_url('uploads/ttd/' . $header['ttd_shift_2']) : base_url('assets/img/upload_placeholder.png');
                        $ttd3 = $header['ttd_shift_3'] ? base_url('uploads/ttd/' . $header['ttd_shift_3']) : base_url('assets/img/upload_placeholder.png');
                        $ttdspv = $header['ttd_spv'] ? base_url('uploads/ttd/' . $header['ttd_spv']) : base_url('assets/img/upload_placeholder.png');
                        ?>
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm">
                                <img id="preview_ttd_1" src="<?= $ttd1 ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_shift_1" class="d-none sig-input" data-preview="preview_ttd_1">
                                <button type="button" class="btn btn-outline-primary btn-xs btn-block btn-upload-sig">Change</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small">Operator Shift 1</div>
                        </div>
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm">
                                <img id="preview_ttd_2" src="<?= $ttd2 ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_shift_2" class="d-none sig-input" data-preview="preview_ttd_2">
                                <button type="button" class="btn btn-outline-primary btn-xs btn-block btn-upload-sig">Change</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small">Operator Shift 2</div>
                        </div>
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm">
                                <img id="preview_ttd_3" src="<?= $ttd3 ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_shift_3" class="d-none sig-input" data-preview="preview_ttd_3">
                                <button type="button" class="btn btn-outline-primary btn-xs btn-block btn-upload-sig">Change</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small">Operator Shift 3</div>
                        </div>
                        <div class="col-3">
                            <div class="signature-upload-box mb-3 border rounded p-2 bg-white shadow-sm border-primary">
                                <img id="preview_ttd_spv" src="<?= $ttdspv ?>" class="img-fluid mb-2" style="max-height: 80px; object-fit: contain;">
                                <input type="file" name="ttd_spv" class="d-none sig-input" data-preview="preview_ttd_spv">
                                <button type="button" class="btn btn-primary btn-xs btn-block btn-upload-sig">Change</button>
                            </div>
                            <div class="font-weight-bold border-top pt-2 mx-2 small text-primary text-uppercase small">Supervisor</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 py-4 text-right">
                <a href="<?= base_url('plant_produksi') ?>" class="btn btn-light px-4 mr-2">Batal</a>
                <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                    <i class="fas fa-save mr-2"></i> Update Laporan Produksi
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
                <h5 class="modal-title">Jenis Reject - Total Shift</h5>
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
    const tableJamBody = document.querySelector('#table_jam tbody');
    
    // Dynamic Hourly Rows
    let jamRowCount = <?= count($jams) ?>;
    const selectJam = document.getElementById('select_jam');
    const btnAddJamRow = document.getElementById('btn_add_jam_row');

    btnAddJamRow.addEventListener('click', function() {
        const jam = selectJam.value;
        if (!jam) {
            alert('Pilih jam terlebih dahulu');
            return;
        }

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
        document.querySelectorAll('.input-reject-detail').forEach(input => input.value = '');
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
                hiddenInput.name = `rejects[${input.dataset.type}]`;
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
    let dtCount = <?= count($downtimes) ?>;
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
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => previewImg.src = e.target.result;
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    updateGrandTotal();
});
</script>

<?= $this->endSection() ?>
