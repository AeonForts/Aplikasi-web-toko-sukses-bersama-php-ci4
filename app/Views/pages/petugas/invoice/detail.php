<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<div class="col-12">
    <a href="<?= base_url('petugas/invoice'); ?>" class="btn btn-primary mb-3">
        <i class="fa fa-solid fa-arrow-left"></i> Kembali ke Daftar Invoice
    </a>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Detail Invoice #<?= $invoice['id_invoice']; ?></h4>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informasi Invoice</h5>
                    <table class="table">
                        <tr>
                            <th>Nomor Invoice</th>
                            <td><?= $invoice['id_invoice']; ?></td>
                        </tr>
                        <tr>
                            <th>Nama Customer</th>
                            <td><?= $invoice['customer_name']; ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal Invoice</th>
                            <td><?= date('d M Y H:i', strtotime($invoice['invoice_date'])); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Ringkasan Pembayaran</h5>
                    <table class="table">
                        <tr>
                            <th>Total Tagihan</th>
                            <td>Rp. <?= number_format($invoice['total_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <th>Jumlah Dibayar</th>
                            <td>Rp. <?= number_format($invoice['payment_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <th>Kembalian</th>
                            <td>Rp. <?= number_format($invoice['change_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <?php if ($invoice['payment_amount'] >= $invoice['total_amount']): ?>
                                    <span class="badge bg-success">Lunas</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Belum Lunas</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">Detail Barang</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Barang</th>
                                    <th>Harga Satuan</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoice_items as $index => $item): ?>
                                    <tr>
                                        <td><?= $index + 1; ?></td>
                                        <td><?= $item['jenis_barang']; ?></td>
                                        <td>Rp. <?= number_format($item['price'], 2); ?></td>
                                        <td><?= $item['quantity']; ?></td>
                                        <td>Rp. <?= number_format($item['total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>