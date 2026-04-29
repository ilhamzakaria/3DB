<!-- Edit Data Modal Fragment -->
<div class="modal-header">
    <input type="hidden" name="id" value="<?= $header['id'] ?>">
    <h5 class="modal-title font-weight-bold text-primary">
        <i class="fas fa-edit mr-2"></i> Edit Laporan Produksi
    </h5>
</div>
<div class="modal-body p-4">
    <form id="formEditLaporan" action="<?= base_url('produksi/update/' . $header['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <?= view('produksi/partials/form_fields', ['header' => $header, 'jams' => $jams, 'rejects' => $rejects, 'materials' => $materials, 'colorants' => $colorants, 'packaging' => $packaging, 'downtimes' => $downtimes]) ?>
    </form>
</div>
<div class="modal-footer border-top-0 pt-0 pb-4 px-4">
    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
    <button type="submit" form="formEditLaporan" class="btn btn-primary px-5 shadow-sm">Update Laporan</button>
</div>
