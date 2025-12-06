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
                    <button id="showChartBtn" class="btn btn-primary float-right" data-toggle="modal" data-target="#chartModal">
                        <i class="fa fa-chart-bar"></i> Tampilkan Grafik
                    </button>
                    <a href="<?= base_url('admin/penjualan/piutang'); ?>" class="btn btn-warning">
                        <i class="fa fa-list"></i> Daftar Piutang
                    </a>
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
                <div class="col-md-4">
                    <button id="resetFilterButton" class="btn btn-danger w-100">Reset Filter</button>
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
                            <th>Total Modal</th>
                            <th>Total Jual</th>
                            <th>Total Untung</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Grand Total:</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('pages/admin/penjualan/modal') ?>

<script>
$(document).ready(function() {
    var table = $('#penjualanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/penjualan/datatables'); ?>',
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
            { data: 'total_harga_modal' },
            { data: 'total_harga_jual' },
            { data: 'total_untung' },
            { data: 'action', orderable: false }
        ],
        order: [[1, 'desc']], // Order by date descending
        footerCallback: function(row, data, start, end, display) {
    var api = this.api();
    
    // Calculate totals for visible data
    api.columns([3,4,5,6]).every(function(index) {
        var sum = 0;
        var data = this.data();
        
        for(var i = 0; i < data.length; i++) {
            var val = data[i];
            // Remove any currency symbol, dots, and commas, then convert remaining string to number
            val = val.toString().replace(/[Rp\.\s]/g, '').replace(/,00/g, '').replace(/,/g, '.');
            sum += parseFloat(val) || 0;
        }
        
        // Format sum based on column type
        if(index === 3) {
            // For total_barang_keluar: Add thousand separators only
            sum = Math.round(sum).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        } else {
            // For monetary columns: Add Rp and thousand separators
            sum = 'Rp ' + Math.round(sum).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ',00';
        }
        
        $(this.footer()).html(sum);
    });
}
    });

    // Filter button click event
    $('#filterButton').on('click', function() {
        table.draw();
    });

        // Reset filter button click event
    $('#resetFilterButton').on('click', function() {
        // Clear the date inputs
        $('#startDate').val('');
        $('#endDate').val('');
        // Redraw the table
        table.draw();
    });
});
</script>

<?= $this->endSection() ?>