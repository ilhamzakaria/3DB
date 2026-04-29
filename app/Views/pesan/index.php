<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<?= view('pesan/partials/styles') ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4 border-0" style="border-radius: 15px;">
        <div class="card-header py-3 bg-white border-bottom" style="border-radius: 15px 15px 0 0;">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-comments me-2"></i> Chat - PPIC, Produksi, Gudang, Admin, Manager
            </h6>
        </div>
        <div class="card-body">
            <div class="chat-container">
                <div class="chat-messages" id="chatBox">
                    <div class="text-muted text-center py-4" id="loadingMsg">Memuat pesan...</div>
                </div>
                <form class="chat-form" id="chatForm" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <label class="file-label" for="fileInput">
                        <i class="fas fa-paperclip fw-bold"></i> File
                    </label>
                    <input type="file" id="fileInput" class="file-input" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                    <span class="file-name" id="fileName"></span>
                    <input type="text" id="msgInput" placeholder="Ketik pesan..." autocomplete="off" maxlength="500">
                    <button type="submit" id="btnSend">
                        <i class="fas fa-paper-plane me-1"></i> Kirim
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('pesan/scripts') ?>

<?= $this->endSection() ?>
