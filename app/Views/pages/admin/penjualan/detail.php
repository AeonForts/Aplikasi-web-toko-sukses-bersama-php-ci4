<?= $this->extend('layout/app') ?> <!-- Extends the template you shared -->
<?= $this->section('content') ?> <!-- Begin content section -->

<div class="col-12">
    <a href="#" onclick="window.location.href='<?= base_url('admin/penjualan'); ?>'; return false;" class="btn btn-primary">
        <i class="fa fa-solid fa-arrow-left"></i> Kembali
    </a>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Riwayat Penjualan </h4>
        </div>
        <div class="card-content mb-4 px-4">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Barang</th>
                            <th>Jenis Unit</th>
                            <th>Jumlah Keluar</th>
                            <th>Harga Modal</th>
                            <th>Total Harga Modal</th>
                            <th>Harga Jual</th>
                            <th>Total Harga Jual</th>
                            <th>Untung</th>
                            <th>Jumlah Uang</th>
                            <th>Jenis Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vw_detail_penjualan as $row) : ?>
                            <tr>
                                <td><?= $row['nama_customer']; ?></td>
                                <td><?= $row['jenis_barang']; ?></td>
                                <td><?= $row['tipe_unit']; ?></td>
                                <td><?= $row['jumlah_keluar']; ?></td>
                                <td><?= number_format($row['harga_modal_barang'], 0); ?></td>
                                <td><?= number_format($row['total_harga_modal'], 0); ?></td>
                                <td><?= number_format($row['harga_jual'], 0); ?></td>
                                <td><?= number_format($row['total_harga_jual'], 0); ?></td>
                                <td><?= number_format($row['untung_telur'], 0); ?></td>
                                <td><?= number_format($row['jumlah'], 0); ?></td>
                                <td><?= $row['nama_method']; ?></td>
                                <td>
                                    <?php if ($row['status'] == 0): ?>
                                        <span class="text-danger">Belum Lunas</span>
                                    <?php else: ?>
                                        <span class="text-success">Lunas</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <?php if ($row['status'] == 0): ?>
                                <button 
                                    class="btn btn-success btn-sm mark-lunas" 
                                    data-id-detail-penjualan="<?= $row['id_detail_penjualan']; ?>"
                                >
                                    Mark as Lunas
                                </button>
                            <?php endif; ?>
                                    <button 
                                        class="btn btn-danger btn-sm delete-detail" 
                                        data-id-detail-penjualan="<?= $row['id_detail_penjualan']; ?>"
                                    >
                                        Delete
                                    </button>
                                    <button 
                                        class="btn btn-warning btn-sm edit-detail" 
                                        data-toggle="modal" data-target="#ModalEditPenjualanDetail" 
                                        onclick="window.editDetailPenjualan(<?= $row['id_detail_penjualan']; ?>)"
                                    >
                                        Edit
                                    </button>

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

<!-- Include your modal here -->
<?= view('pages/admin/penjualan/modal') ?> <!-- Modal view for edit functionality -->

<?= $this->endSection() ?> <!-- End content section -->
