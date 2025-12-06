<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tabel List Barang Unit</h4>
        </div>
        <div class="card-content mb-4 px-4">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahBarang">
                <i class="fa fa-plus"></i> Tambah Barang Baru
            </button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahUnitBarang">
                <i class="fa fa-plus"></i> Tambah Tipe Unit Barang
            </button>
            
            <!-- Add search input -->
            <div class="mt-3 mb-3">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari barang...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <br>
                <table class="table table-bordered table-striped table-hover mb-0 text-center" id="barangTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Satuan Dasar</th>
                            <th>Unit</th>
                            <th>Standar Jumlah Barang</th>
                            <th>Standar Harga</th>
                            <th>Tanggal Terakhir Diubah</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1; foreach ($barang as $row): ?>
                        <tr>
                            <td><?= $nomor++; ?></td>
                            <td><?= $row['jenis_barang']; ?></td>
                            <td><?= $row['satuan_dasar']; ?></td>
                            <td><?= $row['tipe_unit']; ?></td>
                            <td><?= $row['standar_jumlah_barang']; ?></td>
                            <td><?= number_format($row['standar_harga_jual'], 2); ?></td>
                            <td><?= $row['tanggal']; ?></td>
                            <td>
                                <a href="#" data-id="<?= $row['id_tipe']; ?>" data-unit-id="<?= $row['id_unit']; ?>" class="btn btn-info text-white edit-btn">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                                <a href="#" data-id="<?= $row['id_tipe']; ?>" data-unit-id="<?= $row['id_unit']; ?>" class="btn btn-danger delete-btn">Delete Unit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
</div>

<?= view('pages/admin/barang/modal') ?>

<!-- Add this script section at the bottom of your view -->
<script>
$(document).ready(function() {
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#barangTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
        
        // Update row numbers for visible rows
        var visibleIndex = 1;
        $("#barangTable tbody tr:visible").each(function() {
            $(this).find("td:first").text(visibleIndex++);
        });
    });
});
</script>

<?= $this->endSection() ?>