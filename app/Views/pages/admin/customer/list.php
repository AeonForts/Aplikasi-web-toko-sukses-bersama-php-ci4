<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Daftar Customer</h1>
        </div>
        
        <div class="card-body">
            <button class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahCustomer">Tambah Customer</button>
            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover" id="customerTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama Customer</th>
                            <th>Alamat</th>
                            <th>No Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('pages/admin/customer/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    var customerTable = $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url('admin/customer/datatables'); ?>',
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
            { data: 'id_customer', name: 'id_customer' },
            { data: 'nama_customer', name: 'nama_customer' },
            { 
                data: 'alamat', 
                name: 'alamat',
                defaultContent: '-' 
            },
            { 
                data: 'no_telp', 
                name: 'no_telp',
                defaultContent: '-' 
            },
            { 
                data: 'id_customer',
                render: function (data, type, row) {
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary" onclick="editCustomer(${data})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${data})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                    `;
                },
                orderable: false,
                searchable: false
            }
        ],
        order: [[1, 'asc']],
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            zeroRecords: 'Tidak ada data customer',
            emptyTable: 'Tidak ada data tersedia',
            infoEmpty: 'Tidak ada data yang ditampilkan',
            infoFiltered: '(difilter dari _MAX_ total data)'
        }
    });

    // Global functions for edit and delete
    window.editCustomer = function(id) {
        // Implementation for editing customer
        console.log('Edit customer:', id);
        $('#ModalEditCustomer').modal('show');
    };

    window.deleteCustomer = function(id) {
        // Implementation for deleting customer
        console.log('Delete customer:', id);
        $('#ModalDeleteCustomer').modal('show');
    };
});
</script>
<?= $this->endSection() ?>