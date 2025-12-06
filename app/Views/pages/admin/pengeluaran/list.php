<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Riwayat Biaya Kebutuhan</h4>
        </div>
        <div class="card-content mb-4 px-4">
            <!-- Button to trigger the modal -->
            <div class="d-flex justify-content mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahPengeluaran">
                    <i class="fa fa-plus"></i> Tambah Pengeluaran
                </button>
            
            </div>
            <div class="d-flex justify-content mb-3">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ChartModal">
                    <i class="fa fa-chart-bar"></i> Lihat Grafik Pengeluaran
                </button>
            </div>
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

            <!-- Table -->
            <div class="table-responsive-sm">
                <table class="table table-bordered table-striped table-hover text-center" id="pengeluaranTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Total Biaya</th>
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


<?= view('pages/admin/pengeluaran/modal') ?>


<script>
$(document).ready(function() {
    var table = $('#pengeluaranTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/pengeluaran/datatables'); ?>',
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
            { data: 'tgl_pengeluaran' },
            { data: 'total_biaya' },
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