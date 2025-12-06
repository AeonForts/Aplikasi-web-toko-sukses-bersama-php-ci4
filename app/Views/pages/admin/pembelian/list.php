<?= $this->extend('layout/app') ?>
<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Riwayat Transaksi Pembelian</h4>
        </div>
        <div class="card-content mb-4 px-4">
            <!-- Button to trigger the modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahPembelian">
                <i class="fa fa-plus"></i> Tambah Pembelian
            </button>
            <button class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#PembelianChartModal">
                <i class="fa fa-chart-bar"></i> Lihat Grafik
            </button>

            <!-- Date filter inputs -->
            <div class="date-filter mb-3 mt-3">
                <div class="row">
                    <div class="col-md-4">
                        <label for="startDate">Start Date:</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="endDate">End Date:</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <!-- Status filter input -->
                    <div class="col-md-4">
                        <label for="statusFilter">Status:</label>
                        <select id="statusFilter" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="0">Belum Lunas</option>
                            <option value="1">Menunggu Konfirmasi</option>
                            <option value="2">Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button id="filterButton" class="btn btn-secondary">Filter</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0 text-center" id="pembelianTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pembelian</th>
                            <th>Bayar Meity</th>
                            <th>Status</th>
                            <th>Terkumpul</th>
                            <th>Hutang</th>
                            <th>Jenis Barang</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('pages/admin/pembelian/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<script>
$(document).ready(function() {
    // Initialize DataTables
    var table = $('#pembelianTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/pembelian/getDatatables') ?>',
            type: 'POST',
            data: function(d) {
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
                d.status = $('#statusFilter').val();

            }
        },
        columns: [
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { 
                data: 'tgl_masuk',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            { 
                data: 'total_meity',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    if (data == 0) {
                        return '<span class="badge text-bg-danger">Belum Lunas</span>';
                    } else if (data == 1) {
                        return '<span class="badge text-bg-warning">Menunggu Konfirmasi</span>';
                    } else {
                        return '<span class="badge text-bg-success">Lunas</span>';
                    }
                }
            },
            { 
                data: 'terkumpul',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            { 
                data: 'hutang',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            { 
                data: 'jenis_barang',
                render: function(data) {
                    return data ? data : 'N/A';
                }
            },
            { 
                data: 'id_pembelian',
                render: function(data, type, row) {
                    let detailUrl = '<?= base_url('admin/pembelian/detail/') ?>' + data;
                    let actionHtml = `<a href="${detailUrl}" class="btn btn-info text-white">Detail</a>`;
                    
                    if (row.status == 1) {
                        actionHtml += ` <button onclick="confirmPayment(${data})" class="btn btn-success">Confirm Payment</button>`;
                    }
                    
                    return actionHtml;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

    // Filter button click event
    $('#filterButton').on('click', function() {
        table.draw();
    });

    // Confirm payment function 
    window.confirmPayment = function(id) {
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: "Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/pembelian/confirmPayment') ?>',
                    type: 'POST',
                    data: { id_pembelian: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pembayaran berhasil dikonfirmasi'
                            });
                            // Refresh the table
                            table.draw();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Gagal mengkonfirmasi pembayaran'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mengkonfirmasi pembayaran'
                        });
                    }
                });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>


