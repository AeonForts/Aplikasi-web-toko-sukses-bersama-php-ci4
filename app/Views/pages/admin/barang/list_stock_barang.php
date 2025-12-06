<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Stock Barang</h1>
        </div>
        <div class="card-content mb-4 px-4">
            <!-- Date filter inputs -->
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <label for="startDate">Start Date:</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="endDate">End Date:</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-4">
                    <button id="filterButton" class="btn btn-secondary w-100">Filter</button>
                </div>
            </div>

            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover text-center" id="stockTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Barang</th>
                            <th>Satuan</th>
                            <!-- <th>Stok Awal</th> -->
                            <th>Stok Masuk</th>
                            <th>Stok Keluar</th>
                            <th>Stok Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




<script>
$(document).ready(function() {
    var table = $('#stockTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/barang/datatables'); ?>',
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
            { 
                data: 'tgl_stock', 
                render: function(data, type, row) {
                    return type === 'sort' ? row.tgl_stock_sort : data;
                }
            },
            { data: 'jenis_barang' },
            { data: 'satuan_dasar' },
            // { data: 'total_stock' },
            { data: 'total_pembelian' },
            { data: 'total_penjualan' },
            { data: 'sisa_stok' }
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