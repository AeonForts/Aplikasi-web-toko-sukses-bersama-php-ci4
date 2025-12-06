<?= $this->extend('layout/app') ?> <!-- Extends the template you shared -->
<?= $this->section('content') ?> <!-- Begin content section -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Riwayat Pembayaran</h4>
        </div>
        <div class="card-content mb-4 px-4">
            <!-- Button to trigger the modal -->
            <div class="table-responsive">
                <br>
                <table class="table table-bordered table-striped table-hover mb-0 text-center" id="barangTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Jumlah</th>
                            <th>Metode Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1; foreach ($payment as $row): ?>
                        <tr>
                            <td><?= $nomor++; ?></td>
                            <td><?= $row['tgl']; ?></td>
                            <td><?= $row['customer'] ?></td>
                            <td><?= $row['jumlah']; ?></td>
                            <td><?= $row['metode_pembayaran']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <!-- Updated Pagination -->
                <?php if ($pager) : ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-primary justify-content-center">
                        <?php if ($pager->getCurrentPage() > 1) : ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Previous">
                                    <span aria-hidden="true">Prev</span>
                                </a>
                            </li>
                        <?php endif ?>
                        <?php 
                        $totalPages = $pager->getPageCount();
                        $currentPage = $pager->getCurrentPage();
                        $visiblePages = 5; // Adjust this number to show more or fewer page numbers

                        $start = max(1, $currentPage - floor($visiblePages / 2));
                        $end = min($start + $visiblePages - 1, $totalPages);

                        for ($i = $start; $i <= $end; $i++) : 
                        ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $pager->getPageURI($i) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor ?>

                        <?php if ($pager->getCurrentPage() < $pager->getPageCount()) : ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Next">
                                    <span aria-hidden="true">Next</span>
                                </a>
                            </li>
                        <?php endif ?>
                    </ul>
                </nav>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<script>

</script>


<?= view('pages/admin/barang/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<?= $this->endSection() ?> <!-- End content section -->