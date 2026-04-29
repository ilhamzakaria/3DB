<?= $this->include('partial/header') ?>

<?php if (uri_string() != 'ppic' && uri_string() != 'operator' && uri_string() != 'admin' && uri_string() != 'produksi' && uri_string() != 'gudang' && uri_string() != 'pesan' && uri_string() != 'home' && uri_string() != 'notifikasi' && uri_string() != 'mesin1' && uri_string() != 'mesin2' && uri_string() != 'mesin3' && !str_starts_with(uri_string(), 'gudang/') && !str_starts_with(uri_string(), 'produksi')): ?>
    <?= $this->include('partial/sidebar') ?>
<?php endif; ?>

<?= $this->include('partial/topbar') ?>

<div class="container-fluid">
    <?= $this->renderSection('content') ?>
</div>

<?= $this->include('partial/footer') ?>
<?= $this->renderSection('vendor_scripts') ?>
<?= $this->renderSection('scripts') ?>
