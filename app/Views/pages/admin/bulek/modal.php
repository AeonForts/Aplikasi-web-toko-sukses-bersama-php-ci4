<!-- Add/Insert Bulek modal -->
<div class="modal fade" id="ModalSetorBulek" tabindex="-1" role="dialog" aria-labelledby="ModalSetorBulekLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalSetorBulekLabel">Setor Bulek</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulekSetorForm">
                    <div class="form-group">
                        <label for="tgl_setor">Tanggal Setor</label>
                        <input type="date" name="tgl_setor" class="form-control" id="tgl_setor" value="<?= date('Y-m-d') ?>"  required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_setor">Jumlah Setor</label>
                        <input type="number" name="jumlah_setor" class="form-control" id="jumlah_setor" required>
                    </div>
                    <div class="form-group">
                        <label for="id_tipe">Jenis Barang</label>
                        <select name="id_tipe" id="id_tipe" class="form-control" required>
                            <option value="">Pilih Jenis Barang</option>
                            <!-- Dynamically populated options -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" required></textarea>
                    </div>
                    <div id="responseMessageSetor" class="mt-3"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalEditBulek" tabindex="-1" role="dialog" aria-labelledby="ModalEditBulekLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditBulekLabel">Edit Bulek</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bulekEditForm">
                    <input type="hidden" name="id_bulek" id="edit_id_bulek">
                    <!-- <div class="form-group">
                        <label for="tgl_setor">Tanggal Setor</label>
                        <input type="date" name="tgl_setor" class="form-control" id="edit_tgl_setor" required>
                    </div> -->
                    <div class="form-group">
                        <label for="jumlah_setor">Jumlah Setor</label>
                        <input type="number" name="jumlah_setor" class="form-control" id="edit_jumlah_setor" required>
                    </div>
                    <div class="form-group">
                        <label for="id_tipe">Jenis Barang</label>
                        <select name="id_tipe" id="edit_id_tipe" class="form-control" required>
                            <option value="">Pilih Jenis Barang</option>
                            <!-- Dynamically populated options -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" required></textarea>
                    </div>
                    <div id="responseMessageEdit" class="mt-3"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="ModalDeleteBulek" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteBulekLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDeleteBulekLabel">Delete Confirmation</h5>
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

    function populateTipeBarang() {
        $.ajax({
            url: '<?= base_url('admin/bulek/getTipeBarang'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let $select = $('#id_tipe, #edit_id_tipe');
                $select.empty().append('<option value="">Pilih Jenis Barang</option>');
                
                response.forEach(function(item) {
                    $select.append(`<option value="${item.id}">${item.jenis_barang}</option>`);
                });
            },
            error: function(xhr) {
                console.error('Error loading tipe barang:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat jenis barang'
                });
            }
        });
    }

    // Add this to your script
    window.editBulek = function(id) {
        console.log('Editing with ID:', id);
        
        $.ajax({
            url: '<?= base_url('admin/bulek/edit/'); ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Retrieved response:', response);
                
                // Populate the edit form
                $('#edit_id_bulek').val(response.id_bulek);
                // $('#edit_tgl_setor').val(response.tgl_setor);
                $('#edit_jumlah_setor').val(response.jumlah_setor);
                $('#edit_id_tipe').val(response.id_tipe);
                $('#edit_keterangan').val(response.keterangan);
                
                // Show the modal
                $('#ModalEditBulek').modal('show');
            },
            error: function(xhr) {
                console.error('Error details:', xhr);
                
                let errorMessage = 'Failed to load data';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch(e) {
                    console.error('Error parsing error response:', e);
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            }
        });
    };

    // Call on page load
    populateTipeBarang();

    // Setor Bulek Form Submission
    $('#bulekSetorForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/bulek/setor'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#ModalSetorBulek').modal('hide');
                    showAlertThenReload(
                        {
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data Bulek berhasil disimpan',
                        },
                        {
                            icon: 'success',
                            title: 'Data Bulek berhasil disimpan'
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal menyimpan data'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON ? 
                    xhr.responseJSON.message : 
                    ' Gagal menyimpan data';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });


        function editBulek(id) {
        $.ajax({
            url: '<?= base_url('admin/bulek/edit/'); ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Populate modal fields with retrieved data
                $('#edit_id_bulek').val(response.id_bulek);
                $('#edit_tgl_setor').val(response.tgl_setor);
                $('#edit_jumlah_setor').val(response.jumlah_setor);
                $('#edit_id_tipe').val(response.id_tipe);
                $('#edit_keterangan').val(response.keterangan);

                // Show the modal
                $('#ModalEditBulek').modal('show');
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal mengambil data untuk diedit'
                });
            }
        });
    }
    // Edit Bulek Form Submission
    $('#bulekEditForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/bulek/update'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.status === 'success') {
                    $('#ModalEditBulek').modal('hide');
                    showAlertThenReload(
                        {
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data Bulek berhasil diupdate',
                        },
                        {
                            icon: 'success',
                            title: 'Data Bulek berhasil diupdate'
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal mengupdate data'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON ? 
                    xhr.responseJSON.message : 
                    'Gagal mengupdate data';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });

    // Global delete function
    window.deleteBulek = function(id) {
        $('#confirmDeleteButton').data('bulek-id', id);
        $('#ModalDeleteBulek').modal('show');
    };

    // Confirm delete action
    $('#confirmDeleteButton').on('click', function() {
        var id = $(this).data('bulek-id');
        
        $.ajax({
            url: '<?= base_url('admin/bulek/delete/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#ModalDeleteBulek').modal('hide');
                
                if (response.status === 'success') {
                    showAlertThenReload(
                        {
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data Bulek berhasil dihapus',
                        },
                        {
                            icon: 'success',
                            title: 'Data Bulek berhasil dihapus'
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Gagal menghapus data'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menghapus data';
                
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
});

</script>