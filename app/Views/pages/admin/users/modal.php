
<div class="modal fade" id="ModalTambahUser" tabindex="-1" role="dialog" aria-labelledby="ModalTambahUser Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahUser Label">Tambah Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userTambahForm" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Telepon</label>
                        <input type="text" name="telepon" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="peran">Peran</label>
                        <select name="peran" class="form-control" required>
                            <option value="">Pilih Peran</option>
                            <option value="Admin">Admin</option>
                            <option value="Owner">Owner</option>
                            <option value="Petugas">Petugas</option>
                        </select>
                    </div>
                    <div id="responseMessageTambah" class="mt-3"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="ModalEditUser" tabindex="-1" role="dialog" aria-labelledby="ModalEditUser Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditUser Label">Edit Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userEditForm">
                    <input type="hidden" name="id_user" id="edit_id_user">
                    <div class="form-group">
                        <label for="edit_username">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama">Nama</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_telepon">Telepon</label>
                        <input type="text" name="telepon" id="edit_telepon" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">New Password (optional)</label>
                        <input type="password" name="password" id="edit_password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit_peran">Peran</label>
                        <select name="peran" id="edit_peran" class="form-control" required>
                            <option value="Admin">Admin</option>
                            <option value="Owner">Owner</option>
                            <option value="Petugas">Petugas</option>
                        </select>
                    </div>
                    <div id="responseMessageEdit" class="mt-3"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="ModalDeleteUser" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteUser Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDeleteUser Label">Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user?</p>
                <div id="responseMessageDelete" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
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

    // Insert user
    $('#userTambahForm').on('submit', function(event) {
        event.preventDefault();

        $.ajax({
            url: '<?= base_url('admin/users/save'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Close modal first
                $('#ModalTambahUser').modal('hide');
                
                // Show success sweet alert
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Reload page after alert
                    location.reload();
                });
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON.error || "Error occurred";
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });

    // Edit user
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');

        $.ajax({
            url: '<?= base_url('admin/users/edit'); ?>',
            type: 'POST',
            data: { id_user: id },
            success: function(response) {
                $('#edit_id_user').val(response.id_user);
                $('#edit_username').val(response.username);
                $('#edit_nama').val(response.nama);
                $('#edit_email').val(response.email);
                $('#edit_telepon').val(response.telepon);
                $('#edit_peran').val(response.peran);
                
                // Show the modal
                $('#ModalEditUser').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Failed to fetch user data.",
                });
            }
        });
    });

    // Update user
    $('#userEditForm').on('submit', function(event) {
        event.preventDefault();

        $.ajax({
            url: '<?= base_url('admin/users/update'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Close modal
                $('#ModalEditUser').modal('hide');
                
                // Remove any lingering modal backdrops
                $('.modal-backdrop').remove();
                
                // Show success sweet alert
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // Reload page after alert
                    location.reload();
                });
            },
            error: function(xhr) {
                const errorMessage = xhr.responseJSON.error || "Error occurred";
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });

    // Delete user
    $('.delete-btn').on('click', function(){
        const id = $(this).data('id');

        // Use SweetAlert for confirmation
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak dapat mengembalikan data yang dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/users/delete'); ?>',
                    type: 'POST',
                    data: { id_user: id },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON.error || "Error occurred";
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                });
            }
        });
    });
});
</script>