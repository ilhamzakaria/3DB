<!-- Add Data Modal -->
<div class="modal fade" id="addDataModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Laporan Produksi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formAddLaporan" action="<?= base_url('produksi/simpan') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?= view('produksi/partials/form_fields') ?>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="formAddLaporan" class="btn btn-primary px-5 shadow-sm">Simpan Laporan</button>
            </div>
        </div>
    </div>
</div>
