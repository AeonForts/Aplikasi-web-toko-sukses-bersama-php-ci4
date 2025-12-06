<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>

<div class="col-12">
    <a href="#" onclick="window.location.href='<?= base_url('admin/penjualan'); ?>'; return false;" class="btn btn-primary mb-3">
        <i class="fa fa-solid fa-arrow-left"></i> Kembali
    </a>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Daftar Piutang (Berdasarkan Tanggal Pembelian)</h4>
        </div>
        <div class="card-content mb-4 px-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0" id="piutangTable">
                    <thead>
                        <tr>
                            <th>Tanggal Piutang Masuk</th>
                            <th>Umur Piutang</th>
                            <th>Customer</th>
                            <th>Barang</th>
                            <th>Jenis Unit</th>
                            <th>Jumlah Keluar</th>
                            <th>Harga Jual</th>
                            <th>Terikat pada Meity Tanggal</th>
                            <th>Total Harga Modal</th>
                            <th>Total Harga Jual</th>
                            <th>Jenis Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vw_detail_penjualan as $row) : ?>
                            <tr class="<?= $row['days_overdue'] > 30 ? 'table-danger' : ($row['days_overdue'] > 15 ? 'table-warning' : '') ?>">

                                <td><?= date('d-m-Y', strtotime($row['tgl_penjualan'])); ?></td>
                                <td>
                                    <?php 
                                    $daysOverdue = $row['days_overdue'];
                                    if ($daysOverdue == 0) {
                                        echo '<span class="badge badge-info">Hari Ini</span>';
                                    } elseif ($daysOverdue <= 15) {
                                        echo '<span class="badge badge-primary">' . $daysOverdue . ' hari</span>';
                                    } elseif ($daysOverdue <= 30) {
                                        echo '<span class="badge badge-warning">' . $daysOverdue . ' hari</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">' . $daysOverdue . ' hari</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= $row['nama_customer']; ?></td>
                                <td><?= $row['jenis_barang']; ?></td>
                                <td><?= $row['tipe_unit']; ?></td>
                                <td><?= $row['jumlah_keluar']; ?></td>
                                <td><?= $row['harga_jual']; ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_masuk'])); ?></td>
                                <td>Rp. <?= number_format($row['total_harga_modal'], 0, ',', '.'); ?></td>
                                <td>Rp. <?= number_format($row['total_harga_jual'], 0, ',', '.'); ?></td>
                                <td><?= $row['nama_method']; ?></td>
                                
                                <td>
                                    <span class="badge badge-danger">Belum Lunas</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button 
                                            class="btn btn-success btn-sm mark-lunas" 
                                            data-id-detail-penjualan="<?= $row['id_detail_penjualan']; ?>"
                                        >
                                            Lunas
                                        </button>
                                        <button 
                                            class="btn btn-danger btn-sm delete-detail" 
                                            data-id-detail-penjualan="<?= $row['id_detail_penjualan']; ?>"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#piutangTable').DataTable({
        "order": [[0, "asc"]], // Order by date ascending
        "pageLength": 25, // Show 25 entries by default
        "columns": [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            { "orderable": false } // Disable sorting for action column
        ]
    });

    // Existing mark-lunas and delete-detail scripts remain the same
});

$(document).ready(function() {
    $('.mark-lunas').on('click', function() {
        var idDetailPenjualan = $(this).data('id-detail-penjualan');

        Swal.fire({
            title: 'Mark as Lunas',
            text: "Are you sure you want to mark this payment as Lunas?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, mark as Lunas!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/penjualan/mark-lunas'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_detail_penjualan: idDetailPenjualan
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(
                                'Lunas!',
                                'Payment has been marked as Lunas.',
                                'success'
                            ).then(() => {
                                location.reload(); // Reload to reflect changes
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message || 'Failed to mark payment as Lunas.',
                                'error'
                            );
                        }
                    },
                    error: function(xhr) {
                        // Improved error handling
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message 
                            ? xhr.responseJSON.message 
                            : 'An unexpected error occurred.';
                        Swal.fire(
                            'Error!',
                            errorMessage,
                            'error'
                        );
                    }
                });
            }
        });
    });
});

function deleteDetailPenjualan(idDetailPenjualan) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('admin/penjualan/delete-detail'); ?>', 
                type: 'POST',
                dataType: 'json',
                data: {
                    id_detail_penjualan: idDetailPenjualan
                },
                success: function(response) {
                    console.log('Success response:', response);
                    if (response.status === 'success') {
                        Swal.fire(
                            'Deleted!',
                            'The sale detail has been deleted.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Failed to delete sale detail.',
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Full error details:', {
                        status: status,
                 error: error,
                        responseText: xhr.responseText
                    });
                    
                    // Try to parse the response JSON if possible
                    try {
                        var errorResponse = JSON.parse(xhr.responseText);
                        Swal.fire(
                            'Error!',
                            errorResponse.message || 'An unexpected error occurred',
                            'error'
                        );
                    } catch (e) {
                        Swal.fire(
                            'Error!',
                            'An unexpected error occurred: ' + xhr.responseText,
                            'error'
                        );
                    }
                }
            });
        }
    });
}
// Add event listener for dynamically added buttons
$(document).ready(function() {
    $(document).on('click', '.delete-detail', function() {
        var idDetailPenjualan = $(this).data('id-detail-penjualan');
        deleteDetailPenjualan(idDetailPenjualan);
    });
});
</script>

<?= $this->endSection() ?>