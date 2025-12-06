<!-- Add/Insert Supplier modal -->
<div class="modal fade" id="ModalTambahSupplier" tabindex="-1" role="dialog" aria-labelledby="ModalTambahSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahSupplierLabel">Tambah Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierTambahForm">
                    <div class="form-group">
                        <label for="nama_supplier">Nama</label>
                        <input type="text" name="nama_supplier" class="form-control" id="nama_supplier" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" name="alamat" class="form-control" id="alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="no_telp">Nomor Telepon</label>
                        <input type="text" name="no_telp" id="no_telp" class="form-control" required>
                    </div>
                    <div id="responseMessageTambah" class="mt-3"></div> <!-- For displaying response messages -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier modal -->
<div class="modal fade" id="ModalEditSupplier" tabindex="-1" role="dialog" aria-labelledby="ModalEditSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditSupplierLabel">Edit Supplier</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span> <!-- Bootstrap 5 no longer requires an <i> tag -->
                </button>
            </div>
            <div class="modal-body">
                <form id="supplierEditForm">
                    <input type="hidden" name="id_supplier" id="edit_id_supplier">
                    <div class="form-group">
                        <label for="edit_nama_supplier">Nama</label>
                        <input type="text" name="nama_supplier" class="form-control" id="edit_nama_supplier" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_alamat">Alamat</label>
                        <input type="text" name="alamat" class="form-control" id="edit_alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_no_telp">Nomor Telepon</label>
                        <input type="text" name="no_telp" class="form-control" id="edit_no_telp" required>
                    </div>
                    <div id="responseMessageEdit" class="mt-3"></div> <!-- For displaying response messages -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="ModalDeleteSupplier" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteSupplierLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDeleteSupplierLabel">Delete Confirmation</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this item?</p>
                <div id="responseMessageDelete" class="mt-3"></div> <!-- For displaying response messages -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>


<!-- Success Modal -->
<div class="modal fade" id="ModalSuccess" tabindex="-1" role="dialog" aria-labelledby="ModalSuccessLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalSuccessLabel">Success</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="successMessage"></p> <!-- Success message will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Failure Modal -->
<div class="modal fade" id="ModalFailure" tabindex="-1" role="dialog" aria-labelledby="ModalFailureLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalFailureLabel">Error</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p> <!-- Error message will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<script>
$(document).ready(function() {
    // Function to show toast
    function showToast(icon, title) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Toast.fire({
            icon: icon,
            title: title
        })
    }

    // Function to show alert, then reload, and set toast message
    function showAlertThenReload(alertOptions, toastOptions) {
        Swal.fire(alertOptions).then((result) => {
            if (result.isConfirmed) {
                $('.modal').modal('hide'); // Close all Bootstrap modals
                // Store toast message in sessionStorage
                sessionStorage.setItem('toastMessage', JSON.stringify(toastOptions));
                location.reload(); // Reload the page
            }
        });
    }

    // Check for toast message on page load
    var storedToastMessage = sessionStorage.getItem('toastMessage');
    if (storedToastMessage) {
        var toastOptions = JSON.parse(storedToastMessage);
        showToast(toastOptions.icon, toastOptions.title);
        sessionStorage.removeItem('toastMessage'); // Clear the message
    }

    $('#supplierTambahForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/supplier/save'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalTambahSupplier').modal('hide');
                $('#supplierTambahForm')[0].reset();
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Supplier berhasil ditambahkan',
                    },
                    {
                        icon: 'success',
                        title: 'Supplier berhasil ditambahkan'
                    }
                );
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Error: " + error,
                });
            }
        });
    });

    // Global delete function
    window.deleteSupplier = function(id) {
        // Set the supplier ID in a data attribute or global variable
        $('#confirmDeleteButton').data('supplier-id', id);
        $('#ModalDeleteSupplier').modal('show');
    };

    // Move the delete logic outside of a function
    $('#confirmDeleteButton').on('click', function() {
        // Retrieve the supplier ID
        var id = $(this).data('supplier-id');
        
        $.ajax({
            url: '<?= base_url('admin/supplier/delete/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#ModalDeleteSupplier').modal('hide');
                
                if (response.status === 'success') {
                    showAlertThenReload(
                        {
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Supplier berhasil dihapus',
                        },
                        {
                            icon: 'success',
                            title: 'Supplier berhasil dihapus'
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal menghapus supplier',
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menghapus supplier';
                
                try {
                    let response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch(e) {
                    console.error('Error parsing response:', e);
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });

    window.editSupplier = function(id) {
    $.ajax({
        url: '<?= base_url('admin/supplier/get/'); ?>' + id,
        type: 'GET',
        dataType: 'json',  // Explicitly specify JSON
        success: function(response) {
            // Check if response is an object and has the expected properties
            console.log('Response:', response);

            // Handle both direct object and response with status
            let supplier = response.status ? response.data : response;

            $('#edit_id_supplier').val(supplier.id_supplier);
            $('#edit_nama_supplier').val(supplier.nama_supplier);
            $('#edit_alamat').val(supplier.alamat);
            $('#edit_no_telp').val(supplier.no_telp);
            
            // Show the edit modal
            $('#ModalEditSupplier').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error details:', xhr.responseText);

            let errorMessage = 'Gagal mengambil data supplier';
            
            // Try to parse error message from response
            try {
                let response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
            } catch(e) {
                // If parsing fails, use default error message
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage,
            });
        }
    });
};

        // Edit Form Submission
        $('#supplierEditForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/supplier/update'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalEditSupplier').modal('hide');
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Supplier berhasil diupdate',
                    },
                    {
                        icon: 'success',
                        title: 'Supplier berhasil diupdate'
                    }
                );
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON ? 
                    xhr.responseJSON.message : 
                    'Gagal mengupdate supplier';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });
});

</script>