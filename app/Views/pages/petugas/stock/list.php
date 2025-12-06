<?= $this->extend('layout/app') ?> <!-- Extends the template you shared -->
<?= $this->section('content') ?> <!-- Begin content section -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Stock Barang</h1>
        </div>
        <div class="card-content mb-4 px-4">
        <?= anchor('pembelian/tambah', '<i class="fa fa-solid fa-plus"></i> Setor', ['class' => 'btn btn-success']) ?>
            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Total Barang keluar</th>
                            <th>Total Modal</th>
                            <th>Total Jual</th>
                            <th>Total Untung</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1; ?>
                        <?php foreach ($penjualan as $row) { ?>
                            <tr>
                                <td><?= $nomor; ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_penjualan'])); ?></td>
                                <td><?= number_format($row['total_barang_keluar'], 2,',','.'); ?></td>
                                <td>Rp. <?= number_format($row['total_harga_modal'], 2,',','.'); ?></td>
                                <td>Rp. <?= number_format($row['total_harga_jual'], 2,',','.'); ?></td>
                                <td>Rp. <?= number_format($row['total_untung'], 2,',','.'); ?></td>
                                <td><a href="<?= base_url('petugas/penjualan/detail/' . $row['id_penjualan']); ?>" class="btn btn-info text-white">Detail</a></td>
                                </tr>
                            <?php $nomor++;?>
                        <?php }?>
                    </tbody>
                </table>
                <?= $pager->links(); ?>

            </div>
        </div>
    </div>
</div>

<?= view('pages/petugas/penjualan/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<?= $this->endSection() ?> <!-- End content section -->
