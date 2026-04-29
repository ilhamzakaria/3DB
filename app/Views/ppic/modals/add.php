<div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-primary" id="addDataModalLabel">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Data PPIC
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="<?= base_url('ppic/tambahData') ?>">
                <?= csrf_field() ?>

                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #f0f7ff;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary mr-3 fa-lg"></i>
                            <span class="small text-primary font-weight-bold">Tip: Gunakan Master Data SPK untuk mengisi formulir secara otomatis.</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="filter-section-title">Master Data SPK</label>
                            <select class="form-select form-select-lg border-primary shadow-sm" id="masterNoSpkSelect" style="border-radius: 12px; font-size: 1rem;">
                                <option value="">-- Pilih No. SPK untuk autofill --</option>
                                <?php foreach (($spk_master ?? []) as $master): ?>
                                    <option value="<?= esc($master['no_spk']) ?>">
                                        <?= esc($master['no_spk']) ?> - <?= esc($master['nama_produk']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Jam Operasional</label>
                            <select class="form-select" name="jam" id="jamSelect" required>
                                <option value="">-- Pilih Jam --</option>
                                <?php
                                $listJam = ["07-08", "08-09", "09-10", "10-11", "11-12", "12-13", "13-14", "14-15", "15-16", "16-17", "17-18", "18-19", "19-20", "20-21", "21-22", "22-23", "23-00", "00-01", "01-02", "03-04", "04-05", "05-06", "06-07"];
                                foreach ($listJam as $j) :
                                ?>
                                    <option value="<?= $j ?>"><?= $j ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">No. SPK</label>
                            <input type="text" class="form-control" name="no_spk" id="add_no_spk" placeholder="Input atau pilih SPK...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Shift</label>
                            <select class="form-select" name="shif" id="shiftInput">
                                <option value="">-- otomatis --</option>
                                <option value="1">Shift 1 (07-14)</option>
                                <option value="2">Shift 2 (14-22)</option>
                                <option value="3">Shift 3 (22-06)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="filter-section-title">Nama Mesin</label>
                            <input type="text" class="form-control" name="nama_mesin" id="add_nama_mesin" placeholder="...">
                        </div>

                        <div class="col-md-6">
                            <label class="filter-section-title">Nama Produk</label>
                            <input type="text" class="form-control" name="nama_produk" id="add_nama_produk" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Grade</label>
                            <input type="text" class="form-control" name="grade" id="add_grade" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Warna</label>
                            <input type="text" class="form-control" name="warna" id="add_warna" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Nomor Mesin</label>
                            <input type="text" class="form-control" name="nomor_mesin" id="add_nomor_mesin" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Operator</label>
                            <input type="text" class="form-control" name="operator" id="add_operator" placeholder="...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Target</label>
                            <input type="text" class="form-control" name="targett" id="add_targett" placeholder="0">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Revisi (R1)</label>
                            <input type="text" class="form-control" name="revisi_1" id="add_revisi_1" placeholder="Input revisi jika ada...">
                        </div>

                        <div class="col-md-4">
                            <label class="filter-section-title">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button class="btn btn-light px-4" type="button" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button class="btn btn-primary px-4 shadow-sm" type="submit" style="border-radius: 10px;">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
