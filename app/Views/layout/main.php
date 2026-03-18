<?= $this->include('partial/header') ?>

<?php if (uri_string() != 'ppic' && uri_string() != 'operator' && uri_string() != 'admin' && uri_string() != 'produksi' && uri_string() != 'gudang' && uri_string() != 'chat' && uri_string() != 'home' && uri_string() != 'notifikasi' && uri_string() != 'mesin1' && uri_string() != 'mesin2'): ?>
    <?= $this->include('partial/sidebar') ?>
<?php endif; ?>

<?= $this->include('partial/topbar') ?>

<div class="container-fluid">
    <?= $this->renderSection('content') ?>
</div>

<?= $this->include('partial/footer') ?>