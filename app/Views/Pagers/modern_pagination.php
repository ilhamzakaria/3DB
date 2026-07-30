<?php
/**
 * Modern Pagination Template with improved UI/UX
 * Features:
 * - Better visual hierarchy
 * - Page info display
 * - Responsive design
 * - Better navigation buttons
 * - Hover effects and transitions
 */
$pager->setSurroundCount(2);
$pageCount = $pager->getPageCount();
$currentPage = $pager->getCurrentPage();
$total = $pager->getTotal();
$perPage = $pager->getPerPage();
$firstItem = ($currentPage - 1) * $perPage + 1;
$lastItem = min($currentPage * $perPage, $total);
?>

<style>
.pagination-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem 0;
    flex-wrap: wrap;
    gap: 1rem;
}

.pagination-info {
    font-size: 0.9rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-info strong {
    color: #333;
    font-weight: 600;
}

.pagination-wrapper {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.pagination {
    margin: 0;
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.page-item {
    list-style: none;
}

.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0.5rem 0.75rem;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    background-color: #fff;
    color: #0056b3;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.page-link:hover {
    background-color: #e9ecef;
    border-color: #0056b3;
    color: #0056b3;
    transform: translateY(-1px);
}

.page-item.active .page-link {
    background-color: #0056b3;
    border-color: #0056b3;
    color: #fff;
    cursor: default;
    box-shadow: 0 2px 4px rgba(0, 86, 179, 0.3);
}

.page-item.disabled .page-link {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #999;
    cursor: not-allowed;
    opacity: 0.6;
}

.page-item.disabled .page-link:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    transform: none;
}

.pagination-separator {
    padding: 0 0.5rem;
    color: #999;
}

/* Responsive design */
@media (max-width: 576px) {
    .pagination-container {
        flex-direction: column;
        align-items: stretch;
        padding: 1rem 0;
    }

    .pagination-info {
        justify-content: center;
    }


    .pagination-wrapper {
        justify-content: center;
    }

    .page-link {
        min-width: 36px;
        height: 36px;
        font-size: 0.85rem;
    }
}

/* Loading state */
.pagination-loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Smooth animations */
.page-link {
    -webkit-user-select: none;
    user-select: none;
}

.page-link:active {
    transform: translateY(0);
}
</style>

<div class="pagination-container">
    <!-- Page Info -->
    <div class="pagination-info">
        <?php if ($total > 0) : ?>
            <span>Menampilkan <strong><?= $firstItem ?></strong> sampai <strong><?= $lastItem ?></strong> dari <strong><?= number_format($total) ?></strong> data</span>
        <?php else : ?>
            <span>Tidak ada data</span>
        <?php endif; ?>
    </div>

    <!-- Pagination Controls -->
    <?php if ($pageCount > 1) : ?>
        <div class="pagination-wrapper">
            <ul class="pagination">
                <!-- First Page Button -->
                <?php if ($pager->hasPrevious()) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $pager->getFirst() ?>" title="Halaman pertama">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-angle-double-left"></i>
                        </span>
                    </li>
                <?php endif; ?>

                <!-- Previous Page Button -->
                <?php if ($pager->hasPrevious()) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $pager->getPrevious() ?>" title="Halaman sebelumnya">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-angle-left"></i>
                        </span>
                    </li>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php foreach ($pager->links() as $link) : ?>
                    <?php if ($link['active']) : ?>
                        <li class="page-item active">
                            <span class="page-link"><?= $link['title'] ?></span>
                        </li>
                    <?php else : ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Next Page Button -->
                <?php if ($pager->hasNext()) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $pager->getNext() ?>" title="Halaman berikutnya">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </li>
                <?php endif; ?>

                <!-- Last Page Button -->
                <?php if ($pager->hasNext()) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?= $pager->getLast() ?>" title="Halaman terakhir">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                <?php else : ?>
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-angle-double-right"></i>
                        </span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
