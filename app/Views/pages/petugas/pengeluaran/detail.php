<?= $this->extend('layout/app') ?> <!-- Extends the template you shared -->
<?= $this->section('content') ?> <!-- Begin content section -->

<div class="col-12">
    <a href="#" onclick="window.location.href='<?= base_url('petugas/pengeluaran'); ?>'; return false;" class="btn btn-primary">
        <i class="fa fa-solid fa-arrow-left"></i> Kembali
    </a>
    <div class="card">
    <div class="card-header">
            <h4 class="card-title">Riwayat Biaya Harian - <?= date('d F Y', strtotime($tgl_pengeluaran)); ?></h4>
        </div>
        <div class="card-content mb-4 px-4">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Biaya</th>
                            <th>Keterangan</th>
                            <th>Aksi</th> <!-- Added a new header for actions -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php $nomor = 1; foreach ($detail_pengeluaran as $detail): ?>
                        <tr>
                            <td><?= $nomor++; ?></td>
                            <td><?= number_format($detail['jumlah_biaya'], 0, ',', '.'); ?></td>
                            <td><?= $detail['keterangan']; ?></td>
                            <td>
                            <a href="#" data-id="<?= $detail['id_detail_pengeluaran']; ?>" class="btn btn-info text-white edit-btn"><i class="fa fa-pen"></i> Edit</a> |    <a href="#" data-id="<?= $detail['id_detail_pengeluaran']; ?>" class="btn btn-danger text-white delete-btn"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include your modal here -->
<?= view('pages/petugas/pengeluaran/modal') ?> <!-- Modal view for edit functionality -->

<?= $this->endSection() ?> <!-- End content section -->
