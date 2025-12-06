<?= $this->extend('layout/app') ?> <!-- Extends the template you shared -->
<?= $this->section('content') ?> <!-- Begin content section -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tabel List Barang</h4>
        </div>
        <div class="card-content mb-4 px-4">
            <!-- Button to trigger the modal -->
            <div class="table-responsive">
                <br>
                <table class="table table-bordered table-striped table-hover mb-0 text-center" id="barangTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Satuan Dasar</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                        <tbody>
                            <?php $nomor = 1; foreach ($barang as $row): ?>
                            <tr>
                                <td><?= $nomor++; ?></td>
                                <td><?= $row['jenis_barang']; ?></td>
                                <td><?= $row['satuan_dasar']; ?></td>
                                <!-- <td>
                                    <a href="#" data-id="<?= $row['id_tipe']; ?>" class="btn btn-danger delete-btn">Delete Barang</a>
                                </td> -->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                </table>
                <br>
                <div class="d-flex justify-content-between mb-3">
                    <!-- Entries Info on the left -->
                    <div id="entriesInfo" class="text-left"></div>
                    <!-- Pagination on the right -->
                    <?php if ($pager) : ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-primary">
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
</div>
<script>
    $(document).ready(function() {
                        // Update the entries info based on the current table data
    function updateEntriesInfo() {
        var totalEntries = <?= count($barang); ?>; // Total number of entries
        var startEntry = 1; // Starting entry number
        var endEntry = totalEntries; // Ending entry number
    $('#entriesInfo').text('Menampilkan ' + startEntry + ' to ' + endEntry + ' of ' + totalEntries + ' entries');
    }
        updateEntriesInfo(); // Call the function to set the initial text
    });
</script>


<!-- <?= view('pages/petugas/barang/modal') ?> This will include the modal from modal_pengeluaran.php -->

<?= $this->endSection() ?> <!-- End content section -->