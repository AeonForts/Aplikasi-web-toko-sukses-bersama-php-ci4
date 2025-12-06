<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Daftar Supplier</h1>
        </div>
        
        <div class="card-body">
            <button class="btn btn-primary" data-toggle="modal" data-target="#ModalTambahSupplier">Tambah Supplier</button>
            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover" id="supplierTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama Supplier</th>
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

<?= view('pages/admin/supplier/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    var supplierTable = $('#supplierTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url('admin/supplier/datatables'); ?>',
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
            { data: 'id_supplier', name: 'id_supplier' },
            { data: 'nama_supplier', name: 'nama_supplier' },
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
                data: 'id_supplier',
                render: function (data, type, row) {
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary" onclick="editSupplier(${data})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSupplier(${data})">
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
            zeroRecords: 'Tidak ada data supplier',
            emptyTable: 'Tidak ada data tersedia',
            infoEmpty: 'Tidak ada data yang ditampilkan',
            infoFiltered: '(difilter dari _MAX_ total data)'
        }
    });

    // Global functions for edit and delete
    window.editSupplier = function(id) {
        // Implementation for editing supplier
        console.log('Edit supplier:', id);
        $('#ModalEditSupplier').modal('show');
    };

    window.deleteSupplier = function(id) {
        // Implementation for deleting supplier
        console.log('Delete supplier:', id);
        $('#ModalDeleteSupplier').modal('show');
    };
});
</script>
<?= $this->endSection() ?>