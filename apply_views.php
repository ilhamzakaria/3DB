<?php
/**
 * Replaces ONLY the <!-- Modals --> block and the <tbody> rows in 8 mesin views.
 * The card-header, thead, filter sidebar, etc. are left untouched.
 */

// ── Shared modal blocks ───────────────────────────────────────────────────────

function modalCapGalon(string $title, string $slug, string $produkField): string
{
    $addAction = "<?= base_url((\$is_ppic_context ?? false) ? 'ppic2/{$slug}/tambahData' : 'gudang/{$slug}/tambahData') ?>";
    return <<<HTML
<!-- Modals -->
<div class="modal fade" id="addDataModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="fas fa-plus mr-2"></i>Tambah Data {$title}</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
    <form action="{$addAction}" method="post"><?= csrf_field() ?>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Tanggal *</label><input type="date" class="form-control" name="tanggal" value="<?= date('Y-m-d') ?>" required></div></div>
          <div class="col-md-3"><div class="form-group"><label>No. SPK</label><input type="number" class="form-control" name="no_spk"></div></div>
          <div class="col-md-3"><div class="form-group"><label>No Mesin</label><input type="text" class="form-control" name="nomor_mesin" placeholder="cth: I.01"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Shift</label><select class="form-control" name="shif"><option value="">--</option><option>1</option><option>2</option><option>3</option></select></div></div>
        </div>
        <div class="row">
          <div class="col-md-4"><div class="form-group"><label>Nama Produk (Cap Galon)</label><input type="text" class="form-control" name="{$produkField}"></div></div>
          <div class="col-md-4"><div class="form-group"><label>Brand</label><input type="text" class="form-control" name="brand"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat"></div></div>
        </div>
        <hr class="my-2"><h6 class="text-info font-weight-bold mb-1">Waktu &amp; Jumlah Target Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Target</label><input type="datetime-local" class="form-control" name="jam"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Target</label><input type="datetime-local" class="form-control" name="waktu_akhir_target"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 1</label><input type="number" class="form-control" name="target_shift1" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 2</label><input type="number" class="form-control" name="target_shift2" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 3</label><input type="number" class="form-control" name="target_shift3" placeholder="pcs"></div></div>
        </div>
        <hr class="my-2"><h6 class="text-success font-weight-bold mb-1">Waktu &amp; Jumlah Realisasi Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Realisasi</label><input type="datetime-local" class="form-control" name="waktu_awal_realisasi"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Realisasi</label><input type="datetime-local" class="form-control" name="waktu_akhir_realisasi"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 1</label><input type="number" class="form-control" name="realisasi_shift1" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 2</label><input type="number" class="form-control" name="realisasi_shift2" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 3</label><input type="number" class="form-control" name="realisasi_shift3" placeholder="pcs"></div></div>
        </div>
        <hr class="my-2">
        <div class="row">
          <div class="col-md-4"><div class="form-group"><label>Komposisi</label><textarea class="form-control" name="revisi" rows="2" placeholder="cth: LDPE:45%, HDPE:30%"></textarea></div></div>
          <div class="col-md-4"><div class="form-group"><label>Total SPK</label><input type="text" class="form-control" name="total_spk" placeholder="pcs"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Packing</label><input type="text" class="form-control" name="packing" placeholder="cth: 5-5"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Keterangan</label><input type="text" class="form-control" name="keterangan"></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Simpan</button></div>
    </form>
  </div></div>
</div>

<div class="modal fade" id="editDataModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
    <div class="modal-header bg-warning"><h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Edit Data {$title}</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
    <form id="formEdit" method="post"><?= csrf_field() ?><input type="hidden" name="id" id="edit_id">
      <div class="modal-body">
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Tanggal *</label><input type="date" class="form-control" name="tanggal" id="edit_tanggal" required></div></div>
          <div class="col-md-3"><div class="form-group"><label>No. SPK</label><input type="number" class="form-control" name="no_spk" id="edit_spk"></div></div>
          <div class="col-md-3"><div class="form-group"><label>No Mesin</label><input type="text" class="form-control" name="nomor_mesin" id="edit_nomor_mesin"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Shift</label><select class="form-control" name="shif" id="edit_shif"><option value="">--</option><option>1</option><option>2</option><option>3</option></select></div></div>
        </div>
        <div class="row">
          <div class="col-md-4"><div class="form-group"><label>Nama Produk (Cap Galon)</label><input type="text" class="form-control" name="{$produkField}" id="edit_produk"></div></div>
          <div class="col-md-4"><div class="form-group"><label>Brand</label><input type="text" class="form-control" name="brand" id="edit_brand"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Warna</label><input type="text" class="form-control" name="warna" id="edit_warna"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Berat (gr)</label><input type="text" class="form-control" name="berat" id="edit_berat"></div></div>
        </div>
        <hr class="my-2"><h6 class="text-info font-weight-bold mb-1">Waktu &amp; Jumlah Target Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Target</label><input type="datetime-local" class="form-control" name="jam" id="edit_jam"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Target</label><input type="datetime-local" class="form-control" name="waktu_akhir_target" id="edit_waktu_akhir_target"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 1</label><input type="number" class="form-control" name="target_shift1" id="edit_target_shift1"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 2</label><input type="number" class="form-control" name="target_shift2" id="edit_target_shift2"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Target Shift 3</label><input type="number" class="form-control" name="target_shift3" id="edit_target_shift3"></div></div>
        </div>
        <hr class="my-2"><h6 class="text-success font-weight-bold mb-1">Waktu &amp; Jumlah Realisasi Produksi</h6>
        <div class="row">
          <div class="col-md-3"><div class="form-group"><label>Waktu Awal Realisasi</label><input type="datetime-local" class="form-control" name="waktu_awal_realisasi" id="edit_waktu_awal_real"></div></div>
          <div class="col-md-3"><div class="form-group"><label>Waktu Akhir Realisasi</label><input type="datetime-local" class="form-control" name="waktu_akhir_realisasi" id="edit_waktu_akhir_real"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 1</label><input type="number" class="form-control" name="realisasi_shift1" id="edit_real1"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 2</label><input type="number" class="form-control" name="realisasi_shift2" id="edit_real2"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Realisasi Shift 3</label><input type="number" class="form-control" name="realisasi_shift3" id="edit_real3"></div></div>
        </div>
        <hr class="my-2">
        <div class="row">
          <div class="col-md-4"><div class="form-group"><label>Komposisi</label><textarea class="form-control" name="revisi" id="edit_revisi" rows="2"></textarea></div></div>
          <div class="col-md-4"><div class="form-group"><label>Total SPK</label><input type="text" class="form-control" name="total_spk" id="edit_total_spk"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Packing</label><input type="text" class="form-control" name="packing" id="edit_packing"></div></div>
          <div class="col-md-2"><div class="form-group"><label>Keterangan</label><input type="text" class="form-control" name="keterangan" id="edit_keterangan"></div></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning"><i class="fas fa-save mr-1"></i>Update</button></div>
    </form>
  </div></div>
</div>

<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm"><div class="modal-content">
    <form id="formDelete" method="post"><?= csrf_field() ?><input type="hidden" name="id" id="delete_id">
      <div class="modal-body text-center"><p id="delete_message"></p></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger btn-sm">Hapus</button></div>
    </form>
  </div></div>
</div>

<script>
    const toDatetimeLocal = v => v ? v.replace(' ', 'T').substring(0, 16) : '';
    function setEditModal(btn) {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_tanggal').value = btn.dataset.tanggal;
        document.getElementById('edit_spk').value = btn.dataset.spk;
        document.getElementById('edit_nomor_mesin').value = btn.dataset.nomor_mesin || '';
        document.getElementById('edit_shif').value = btn.dataset.shif;
        document.getElementById('edit_produk').value = btn.dataset.produk;
        document.getElementById('edit_brand').value = btn.dataset.brand;
        document.getElementById('edit_warna').value = btn.dataset.warna;
        document.getElementById('edit_berat').value = btn.dataset.berat || '';
        document.getElementById('edit_jam').value = toDatetimeLocal(btn.dataset.jam);
        document.getElementById('edit_waktu_akhir_target').value = toDatetimeLocal(btn.dataset.waktu_akhir_target);
        document.getElementById('edit_target_shift1').value = btn.dataset.target_shift1 || '';
        document.getElementById('edit_target_shift2').value = btn.dataset.target_shift2 || '';
        document.getElementById('edit_target_shift3').value = btn.dataset.target_shift3 || '';
        document.getElementById('edit_waktu_awal_real').value = toDatetimeLocal(btn.dataset.waktu_awal_realisasi);
        document.getElementById('edit_waktu_akhir_real').value = toDatetimeLocal(btn.dataset.waktu_akhir_realisasi);
        document.getElementById('edit_real1').value = btn.dataset.realisasi_shift1 || '';
        document.getElementById('edit_real2').value = btn.dataset.realisasi_shift2 || '';
        document.getElementById('edit_real3').value = btn.dataset.realisasi_shift3 || '';
        document.getElementById('edit_revisi').value = btn.dataset.revisi || '';
        document.getElementById('edit_total_spk').value = btn.dataset.total_spk || '';
        document.getElementById('edit_packing').value = btn.dataset.packing || '';
        document.getElementById('edit_keterangan').value = btn.dataset.keterangan || '';
        document.getElementById('formEdit').action = btn.dataset.action;
    }
    function setDeleteModal(btn) {
        document.getElementById('delete_id').value = btn.dataset.id;
        document.getElementById('delete_message').textContent = 'Hapus data "' + btn.dataset.nama + '"?';
        document.getElementById('formDelete').action = btn.dataset.action;
    }
    function toggleFilter(show) {
        if (show) { $('.filter-sidebar').addClass('active'); $('.filter-overlay').addClass('active'); }
        else { $('.filter-sidebar').removeClass('active'); $('.filter-overlay').removeClass('active'); }
    }
</script>
<?= view('shared/spk_produksi_autofill') ?>
<?= \$this->endSection() ?>
HTML;
}
