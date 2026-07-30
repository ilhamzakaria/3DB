<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0" style="gap:2px">
        <?php if ($pager->hasPrevious()): ?>
            <li class="page-item">
                <a class="page-link rounded" href="<?= $pager->getFirst() ?>" title="Halaman pertama" style="padding:4px 9px">
                    <i class="fas fa-angle-double-left" style="font-size:.7rem"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link rounded" href="<?= $pager->getPrevious() ?>" title="Sebelumnya" style="padding:4px 9px">
                    <i class="fas fa-angle-left" style="font-size:.7rem"></i>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link): ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link rounded" href="<?= $link['uri'] ?>" style="padding:4px 9px;min-width:32px;text-align:center;font-size:.8rem;<?= $link['active'] ? 'font-weight:600' : '' ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()): ?>
            <li class="page-item">
                <a class="page-link rounded" href="<?= $pager->getNext() ?>" title="Berikutnya" style="padding:4px 9px">
                    <i class="fas fa-angle-right" style="font-size:.7rem"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link rounded" href="<?= $pager->getLast() ?>" title="Halaman terakhir" style="padding:4px 9px">
                    <i class="fas fa-angle-double-right" style="font-size:.7rem"></i>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>
