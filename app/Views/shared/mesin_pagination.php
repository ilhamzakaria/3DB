<?php if (!empty($pager) && !empty($pager_group)) : ?>
    <div class="px-3 py-3 border-top d-flex flex-wrap justify-content-between align-items-center">
        <div class="small text-muted mb-2 mb-md-0">
            Menampilkan <?= count($data_list ?? []) ?> data
        </div>
        <div class="pagination-modern">
            <?= $pager->links($pager_group, 'bootstrap_pagination') ?>
        </div>
    </div>
<?php endif; ?>
