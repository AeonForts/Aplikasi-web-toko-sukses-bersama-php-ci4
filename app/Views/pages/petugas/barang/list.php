<?= $this->extend('layout/app') ?> <!-- Extends the template you shared -->
<?= $this->section('content') ?> <!-- Begin content section -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tabel List Barang Unit</h4>
        </div>
        <div class="card-content mb-4 px-4">
        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahBarang">
                <i class="fa fa-plus"></i> Tambah Barang Baru
         </button>
         <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahUnitBarang">
                <i class="fa fa-plus"></i> Tambah Tipe Unit Barang
         </button> -->
            <!-- Button to trigger the modal -->
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
                                    <!-- <a href="#" data-id="<?= $row['id_tipe']; ?>" data-unit-id="<?= $row['id_unit']; ?>" class="btn btn-danger delete-btn">Delete Unit</a> -->
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



<?= view('pages/petugas/barang/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<?= $this->endSection() ?> <!-- End content section -->