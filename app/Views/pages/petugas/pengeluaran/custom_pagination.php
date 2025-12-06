<!-- app/Views/pager/custom_pagination.php -->

<?php if ($pager->hasPages()): ?>
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-primary justify-content-center">
            
            <!-- Previous Page Link -->
            <?php if ($pager->hasPrevious()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getPreviousPage() ?>">Previous</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
            <?php endif; ?>

            <!-- Page Number Links -->
            <?php foreach ($pager->links() as $link): ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
                </li>
            <?php endforeach; ?>

            <!-- Next Page Link -->
            <?php if ($pager->hasNext()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getNextPage() ?>">Next</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                </li>
            <?php endif; ?>
            
        </ul>
    </nav>
<?php endif; ?>
