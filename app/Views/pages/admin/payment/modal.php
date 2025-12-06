<!-- Add/Insert pengeluaran modal -->
<div class="modal fade" id="ModalTambahBarang" tabindex="-1" role="dialog" aria-labelledby="ModalTambahBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahBarangLabel">Tambah Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="barangTambahForm">
                    <div class="form-group">
                        <label for="jenis_barang">Nama Barang</label>
                        <input type="text" name="jenis_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="satuan_barang">Satuan Barang</label>
                        <input type="text" name="satuan_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="standar_harga_jual">Harga Jual</label>
                        <input type="number" name="standar_harga_jual" class="form-control" required>
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

<!-- Edit barang modal -->
<!-- Edit pengeluaran modal -->
<div class="modal fade" id="ModalEditBarang" tabindex="-1" role="dialog" aria-labelledby="ModalEditBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditBarangLabel">Edit Barang</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span> <!-- Bootstrap 5 no longer requires an <i> tag -->
                </button>
            </div>
            <div class="modal-body">
                <form id="barangEditForm">
                    <input type="hidden" name="id_tipe" id="edit_id_tipe">
                    <div class="form-group">
                        <label for="edit_jenis_barang">barang</label>
                        <input type="text" name="jenis_barang" class="form-control" id="edit_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_satuan_barang">Satuan Barang</label>
                        <input type="text" name="satuan_barang" class="form-control" id="edit_satuan_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_standar_harga_jual">Harga Jual</label>
                        <input type="number" name="standar_harga_jual" class="form-control" id="edit_standar_harga_jual" required>
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
<div class="modal fade" id="ModalDeleteBarang" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDeleteBarangLabel">Delete Confirmation</h5>
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

    $('#barangTambahForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/barang/save'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalTambahBarang').modal('hide');
                $('#barangTambahForm')[0].reset();
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Barang berhasil ditambahkan',
                    },
                    {
                        icon: 'success',
                        title: 'Barang berhasil ditambahkan'
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

    // Trigger the Edit modal
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');

        // Fetch existing data for the selected record
        $.ajax({
            url: '<?= base_url('admin/barang/edit'); ?>',
            type: 'POST',
            data: { id_tipe: id },
            success: function(response) {
                $('#edit_id_tipe').val(response.id_tipe);
                $('#edit_barang').val(response.jenis_barang);
                $('#edit_satuan_barang').val(response.satuan_barang);
                $('#edit_standar_harga_jual').val(response.standar_harga_jual);
                $('#ModalEditBarang').modal('show'); // Show the modal
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Error: " + error,
                });
            }
        });
    });

    // Handle form submission for Edit
    $('#barangEditForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/barang/update'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalEditBarang').modal('hide');
                $('#barangEditForm')[0].reset();
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Barang berhasil diperbarui',
                    },
                    {
                        icon: 'success',
                        title: 'Barang berhasil diperbarui'
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


    // Delete confirmation
    $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/barang/delete'); ?>',
                    type: 'POST',
                    data: { id_tipe: id },
                    success: function(response) {
                        showAlertThenReload(
                            {
                                icon: 'success',
                                title: 'Terhapus!',
                                text: 'Barang berhasil dihapus',
                            },
                            {
                                icon: 'success',
                                title: 'Barang berhasil dihapus'
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
            }
        });
    });

    // Handle modal close for Success Modal and trigger page reload
    $('#ModalSuccess').on('hidden.bs.modal', function() {
        location.reload(); // Reload the page when the success modal is closed
    });
});
</script>

