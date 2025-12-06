<?= $this->extend('layout/app') ?> <!-- Extends the template you shared -->
<?= $this->section('content') ?> <!-- Begin content section -->

<a href="#" onclick="window.location.href='<?= base_url('admin/pembelian'); ?>'; return false;" class="btn btn-primary">
        <i class="fa fa-solid fa-arrow-left"></i> Kembali
    </a>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">Detail Pembelian</h4>
    </div>

    <div class="card-content mb-4 px-4">
        <h4>Detail Pembelian: <?= date('d/m/Y', strtotime($pembelian['tgl_masuk'])) ?> (ID #<?= $pembelian['id_pembelian'] ?>)</h4>
        <hr>
        <div class="detail-section">
    <p><strong>Status</strong>: 
        <span class="<?php 
            switch($pembelian['status']) {
                case 0:
                    echo 'text-danger';
                    break;
                case 1:
                    echo 'text-warning';
                    break;
                case 2:
                    echo 'text-success';
                    break;
                default:
                    echo 'text-muted';
            }
        ?>">
        <?php 
            switch($pembelian['status']) {
                case 0:
                    echo 'Belum Lunas';
                    break;
                case 1:
                    echo 'Menunggu Konfirmasi';
                    break;
                case 2:
                    echo 'Lunas';
                    break;
                default:
                    echo 'Status Tidak Dikenal';
            }
        ?>
    </span></p>
    <p><strong>Nama Supplier</strong>: <?= $pembelian['nama_supplier'] ?></p>
    <p><strong>Tanggal Pembelian</strong>: <?= date('m/d/Y', strtotime($pembelian['tgl_masuk'])) ?></p>
    <p><strong>Nama Barang</strong>: <?= $pembelian['jenis_barang'] ?></p>
    <p><strong>Jumlah Barang</strong>: <?= number_format($pembelian['barang_masuk'], 2) ?></p>
    <p><strong>Satuan Barang</strong>: <?= isset($view_meity['satuan_dasar']) ? $view_meity['satuan_dasar'] : '' ?></p>
    <p>
    <strong>Harga Modal Barang</strong>: 
    <?= 'Rp ' . number_format($pembelian['harga_modal_barang'], 2, ',', '.') ?>
    <i class="fa fa-exclamation-circle" data-toggle="tooltip" title="<?= 'Nilai Asli Tanpa Pembulatan: Rp ' . number_format($pembelian['harga_modal_barang'], 8, ',', '.') . ' (Max 8 decimal places)' ?>"></i>
</p>    <hr>
    <p><strong>Bayar Meity</strong>: <?= 'Rp ' . number_format($pembelian['total_meity'], 2, ',', '.') ?></p>
    <p><strong>Uang Terkumpul</strong>: <?= 'Rp ' . number_format($pembelian['terkumpul'], 2, ',', '.') ?></p>
    <p><strong>Hutang</strong>: <?= 'Rp -' . number_format(abs($view_meity['hutang'] ?? 0), 2, ',', '.') ?></p>
    <p><strong>Total Cash</strong>: <?= 'Rp ' . number_format(isset($view_meity['total_cash']) ? $view_meity['total_cash'] : 0, 2, ',', '.') ?></p>
    <p><strong>Total Sisa dari Meity Sebelumnya</strong>: <?= 'Rp ' . number_format(isset($view_meity['current_sisa']) ? $view_meity['current_sisa'] : 0, 2, ',', '.') ?></p>
    <p><strong>Total Transfer</strong>: <?= 'Rp ' . number_format(isset($view_meity['current_transfer']) ? $view_meity['current_transfer'] : 0, 2, ',', '.') ?></p>
    <p><strong>Total Piutang</strong>: <?= 'Rp ' . number_format(isset($view_meity['current_piutang']) ? $view_meity['current_piutang'] : 0, 2, ',', '.') ?></p>
    <p><strong>Keterangan</strong>: <?= $pembelian['keterangan'] ?></p>
    <hr>
    <button type="button" class="btn btn-success mark-as-cash-trigger" 
        data-id_pembelian="<?= $pembelian['id_pembelian'] ?>" 
        data-toggle="modal" 
        data-target="#ModalMarkAsCash">
    Mark as Cash
</button>
    <button type="button" class="btn btn-primary edit-btn" 
                data-id="<?= $pembelian['id_pembelian'] ?>" 
                data-toggle="modal" 
                data-target="#editPembelianModal">
                <i class="fa fa-edit"></i> Edit Pembelian
            </button>
    <button type="button" class="btn btn-danger delete-btn" 
                data-id="<?= $pembelian['id_pembelian'] ?>" 
                data-toggle="modal" 
                data-target="#deletePembelianModal">
                <i class="fa fa-trash"></i> Hapus Pembelian
            </button>
</div>

        </div>
    </div>
</div>
<!-- Include your modal here -->
<?= view('pages/admin/pembelian/modal') ?> <!-- Modal view for edit functionality -->

<script>
    
</script>
<?= $this->endSection() ?> <!-- End content section -->
