<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Bulek Detail Table</h1>
        </div>
        
        <div class="card-body">
            <!-- Filters for Detail Table -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Jenis Barang:</label>
                    <select id="filter-jenis-barang" class="form-control">
                        <!-- <option value="">Semua Jenis</option> -->
                        <!-- Populate with dynamic options from your data -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Start Date:</label>
                    <input type="date" id="start-date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>End Date:</label>
                    <input type="date" id="end-date" class="form-control">
                </div>
                <div class="col-md-3 align-self-end">
                    <button id="filter-btn" class="btn btn-primary mr-2">Filter</button>
                    <button id="reset-btn" class="btn btn-secondary">Reset</button>
                </div>
            </div>

            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#ModalSetorBulek">Setor Uang</button>
            
            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover" id="bulekDetailTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Penjualan</th>
                            <th>Jenis Barang</th>
                            <th>Total Harga Modal</th>
                            <th>Total Harga Jual</th>
                            <th>Total Untung</th>
                            <th>Tanggal Setor</th>
                            <th>Jumlah Setor</th>
                            <th>Keterangan</th>
                            <th>Sisa Untung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>



            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover" id="bulekBiayaTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Penjualan</th>
                            <th>Jenis Barang</th>
                            <th>Total Harga Modal</th>
                            <th>Total Harga Jual</th>
                            <th>Total Untung</th>
                            <th>Tanggal Setor</th>
                            <th>Jumlah Setor</th>
                            <th>Sisa Untung</th>
                            <th>Total Biaya Pengeluaran</th>
                            <th>Sisa Untung Setelah Biaya</th>
                        </tr>
                    </thead>
                </table>
            </div>


        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Bulek Summary Table</h1>
        </div>
        
        <div class="card-body">
            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover" id="bulekTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Barang</th>
                            <th>Total Untung</th>
                            <th>Total Setor</th>
                            <th>Total Sisa</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

</div>

<?= view('pages/admin/bulek/modal') ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulek Detail Table with Filtering

    $.ajax({
        url: '<?= base_url('admin/bulek/getTipeBarang'); ?>', // Ensure this route exists
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            // Populate dropdown with fetched options
            var $dropdown = $('#filter-jenis-barang');
            $dropdown.append('<option value="">Semua Jenis</option>');
            
            response.forEach(function(item) {
                $dropdown.append(
                    `<option value="${item.id}">${item.jenis_barang}</option>`
                );
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching jenis barang:', error);
        }
    });

        // Shared filter function
        function getSharedFilterData() {
        return {
            start_date: $('#start-date').val(),
            end_date: $('#end-date').val(),
            jenis_barang: $('#filter-jenis-barang').val()
        };
    }

    var bulekDetailTable = $('#bulekDetailTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url('admin/bulek/datatableDetail'); ?>',
            type: 'POST',
            data: function(d) {
                // Get filter values
                var startDate = $('#start-date').val();
                var endDate = $('#end-date').val();
                var jenisBarang = $('#filter-jenis-barang').val();
                
                // Debug log
                console.log('Filter values being sent:', {
                    start_date: startDate,
                    end_date: endDate,
                    jenis_barang: jenisBarang
                });
                
                // Return data object
                return {
                    ...d,
                    start_date: startDate,
                    end_date: endDate,
                    jenis_barang: jenisBarang
                };
            },
            dataSrc: function(response) {
                // Debug log
                console.log('Server response:', response);
                
                if (response.recordsFiltered === 0) {
                    console.log('No records found with current filters');
                }
                
                return response.data;
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
            { data: 'total_harga_modal' },
            { data: 'total_harga_jual' },
            { data: 'total_untung' },
            { data: 'tgl_setor' },
            { data: 'jumlah_setor' },
            { data: 'keterangan' },
            { data: 'sisa_profit' },
            { 
                data: 'id_penjualan',
                render: function (data, type, row) {
                    const isDisabled = !row.id_bulek;
                    
                    return `
                        <div class="btn-group" role="group">
                            <button 
                                class="btn btn-sm btn-primary ${isDisabled ? 'disabled' : ''}" 
                                ${isDisabled ? 'data-toggle="tooltip" title="Belum bisa diproses"' : ''}
                                onclick="${isDisabled ? 'return false;' : `editBulek(${data})`}"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button 
                                class="btn btn-sm btn-danger ${isDisabled ? 'disabled' : ''}" 
                                ${isDisabled ? 'data-toggle="tooltip" title="Belum bisa dihapus"' : ''}
                                onclick="${isDisabled ? 'return false;' : `deleteBulek(${data})`}"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[1, 'desc']]
    });


        // Biaya Table DataTable
    var bulekBiayaTable = $('#bulekBiayaTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false,
        ajax: {
            url: '<?= base_url('admin/bulek/datatableWithBiaya'); ?>',
            type: 'POST',
            data: function(d) {
                var startDate = $('#start-date').val();
                var endDate = $('#end-date').val();
                var jenisBarang = $('#filter-jenis-barang').val();
                
                return {
                    ...d,
                    
                    start_date: startDate,
                    end_date: endDate,
                    jenis_barang: jenisBarang
                };
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
            { data: 'total_harga_modal' },
            { data: 'total_harga_jual' },
            { data: 'total_untung' },
            { data: 'tgl_setor' },
            { data: 'jumlah_setor' },
            { data: 'sisa_profit' },
            { data: 'total_biaya' },
            { data: 'sisa_profit_after_biaya' }
        ],
        order: [[1, 'desc']]
    });

    // Enhanced filter button with validation
    $('#filter-btn').on('click', function() {
        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();
        
        if (startDate && endDate && startDate > endDate) {
            alert('Start date cannot be later than end date');
            return;
        }
        
        // Reload both tables with same filters
        bulekDetailTable.ajax.reload();

    });

    // Reset Button Click Event
    $('#reset-btn').on('click', function() {
        $('#start-date').val('');
        $('#end-date').val('');
        $('#filter-jenis-barang').val('');
        
        // Reload both tables
        bulekDetailTable.ajax.reload();

    });

    // Initialize tooltips after DataTable render
    $('#bulekDetailTable').on('draw.dt', function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Bulek Summary Table (No Filtering)
    var bulekTable = $('#bulekTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        paging: false,
        searching: false,
        ajax: {
            url: '<?= base_url('admin/bulek/datatables'); ?>',
            type: 'POST',
            error: function (xhr, error, thrown) {
                alert('Error loading data');
                console.error('DataTables error:', xhr, error, thrown);
            }
        },
        columns: [
            { 
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                orderable: false 
            },
            { data: 'jenis_barang', name: 'jenis_barang' },
            { 
                data: 'total_keseluruhan',
                render: function(data, type, row) {
                    return 'Rp. ' + parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            },
            { 
                data: 'total_disetor',
                render: function(data, type, row) {
                    return 'Rp. ' + parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            },
            { 
                data: 'total_sisa_profit',
                render: function(data, type, row) {
                    if (row.id_tipe == 7) {
                        return 'Rp. ' + parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' <i class="fas fa-exclamation-circle text-warning" title="Total sisa profit di potong oleh total biaya"></i>';
                    } else {
                        return 'Rp. ' + parseFloat(data).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }
            }
        ],
        order: [[1, 'asc']],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            zeroRecords: 'Tidak ada data',
            emptyTable: 'Tidak ada data tersedia',
            infoEmpty: 'Tidak ada data yang ditampilkan',
            infoFiltered: '(difilter dari _MAX_ total data)'
        }
    });


});
</script>
<?= $this->endSection() ?>