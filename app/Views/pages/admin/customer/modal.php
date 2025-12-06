<!-- Add/Insert Customer modal -->
<div class="modal fade" id="ModalTambahCustomer" tabindex="-1" role="dialog" aria-labelledby="ModalTambahCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahCustomerLabel">Tambah Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="customerTambahForm">
                    <div class="form-group">
                        <label for="nama_customer">Nama</label>
                        <input type="text" name="nama_customer" class="form-control" id="nama_customer" required>
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

<!-- Edit Customer modal -->
<div class="modal fade" id="ModalEditCustomer" tabindex="-1" role="dialog" aria-labelledby="ModalEditCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditCustomerLabel">Edit Customer</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span> <!-- Bootstrap 5 no longer requires an <i> tag -->
                </button>
            </div>
            <div class="modal-body">
                <form id="customerEditForm">
                    <input type="hidden" name="id_customer" id="edit_id_customer">
                    <div class="form-group">
                        <label for="edit_nama_customer">Nama</label>
                        <input type="text" name="nama_customer" class="form-control" id="edit_nama_customer" required>
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
<div class="modal fade" id="ModalDeleteCustomer" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteCustomerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDeleteCustomerLabel">Delete Confirmation</h5>
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

    $('#customerTambahForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/customer/save'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalTambahCustomer').modal('hide');
                $('#customerTambahForm')[0].reset();
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Customer berhasil ditambahkan',
                    },
                    {
                        icon: 'success',
                        title: 'Customer berhasil ditambahkan'
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
    window.deleteCustomer = function(id) {
        // Set the customer ID in a data attribute or global variable
        $('#confirmDeleteButton').data('customer-id', id);
        $('#ModalDeleteCustomer').modal('show');
    };

    // Move the delete logic outside of a function
    $('#confirmDeleteButton').on('click', function() {
        // Retrieve the customer ID
        var id = $(this).data('customer-id');
        
        $.ajax({
            url: '<?= base_url('admin/customer/delete/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#ModalDeleteCustomer').modal('hide');
                
                if (response.status === 'success') {
                    showAlertThenReload(
                        {
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Customer berhasil dihapus',
                        },
                        {
                            icon: 'success',
                            title: 'Customer berhasil dihapus'
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal menghapus customer',
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menghapus customer';
                
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

    window.editCustomer = function(id) {
    $.ajax({
        url: '<?= base_url('admin/customer/get/'); ?>' + id,
        type: 'GET',
        dataType: 'json',  // Explicitly specify JSON
        success: function(response) {
            // Check if response is an object and has the expected properties
            console.log('Response:', response);

            // Handle both direct object and response with status
            let customer = response.status ? response.data : response;

            $('#edit_id_customer').val(customer.id_customer);
            $('#edit_nama_customer').val(customer.nama_customer);
            $('#edit_alamat').val(customer.alamat);
            $('#edit_no_telp').val(customer.no_telp);
            
            // Show the edit modal
            $('#ModalEditCustomer').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error details:', xhr.responseText);

            let errorMessage = 'Gagal mengambil data customer';
            
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
        $('#customerEditForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/customer/update'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalEditCustomer').modal('hide');
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Customer berhasil diupdate',
                    },
                    {
                        icon: 'success',
                        title: 'Customer berhasil diupdate'
                    }
                );
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON ? 
                    xhr.responseJSON.message : 
                    'Gagal mengupdate customer';
                
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