<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Riwayat Penjualan</h1>
        </div>
        <div class="card-content mb-4 px-4">
            <div class="row mb-3">
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahPenjualan">
                        <i class="fa fa-plus"></i> Tambah Penjualan
                    </button>
                    <!-- <button id="showChartBtn" class="btn btn-primary float-right" data-toggle="modal" data-target="#chartModal">
                        <i class="fa fa-chart-bar"></i> Tampilkan Grafik
                    </button> -->
                </div>
            </div>

            <!-- Date filter inputs -->
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <label for="startDate">Tanggal Mulai:</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="endDate">Tanggal Selesai:</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-4">
                    <button id="filterButton" class="btn btn-secondary w-100">Filter</button>
                </div>
            </div>

            <!-- Table -->
            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover text-center" id="penjualanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Total Barang Keluar</th>
                            <!-- <th>Total Modal</th> -->
                            <!-- <th>Total Jual</th> -->
                            <!-- <th>Total Untung</th> -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('pages/petugas/penjualan/modal') ?>


<script>
$(document).ready(function() {
    var table = $('#penjualanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('petugas/penjualan/datatables'); ?>',
            type: 'POST',
            data: function(d) {
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
            }
        },
        columns: [
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'tgl_penjualan' },
            { data: 'jenis_barang' },
            { data: 'total_barang_keluar' },
            // { data: 'total_harga_modal' },
            // { data: 'total_harga_jual' },
            // { data: 'total_untung' },
            { data: 'action', orderable: false }
        ],
        order: [[1, 'desc']] // Order by date descending
    });

    // Filter button click event
    $('#filterButton').on('click', function() {
        table.draw();
    });
});
</script>


<?= $this->endSection() ?>