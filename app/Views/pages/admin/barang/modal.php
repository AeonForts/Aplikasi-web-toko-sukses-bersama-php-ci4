<!-- Add/Insert pengeluaran modal for Barang -->
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
                <form id="barangTambahForm" method="post">
                    <input type="hidden" name="form_type" value="barang">
                    <div class="form-group">
                        <label for="jenis_barang">Nama Barang</label>
                        <input type="text" name="jenis_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="satuan_dasar">Satuan Barang</label>
                        <input type="text" name="satuan_dasar" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tipe_unit">Jenis Unit</label>
                        <input type="text" name="tipe_unit" class="form-control" placeholder="Masukan jenis unit baru..." required>
                    </div>
                    <div class="form-group">
                        <label for="standar_jumlah_barang">Jumlah Per unit</label>
                        <input type="text" name="standar_jumlah_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="standar_harga_jual">Harga Jual</label>
                        <input type="text" name="standar_harga_jual" class="form-control" required>
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

<!-- Add/Insert pengeluaran modal for Unit Barang -->
<div class="modal fade" id="ModalTambahUnitBarang" tabindex="-1" role="dialog" aria-labelledby="ModalTambahUnitBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahUnitBarangLabel">Tambah Unit Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="unitBarangTambahForm" method="post">
                    <input type="hidden" name="form_type" value="unit">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="id_tipe">Tipe Barang</label>
                            <select name="id_tipe" id="id_tipe" class="form-control" required>
                                <option value="">Pilih Tipe Barang</option>
                                <?php foreach ($tipeBarangList as $tipe) : ?>
                                    <option value="<?= $tipe['id_tipe']; ?>" data-satuan="<?= $tipe['satuan_dasar']; ?>">
                                        <?= $tipe['jenis_barang']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="satuan_dasar">Satuan Barang</label>
                            <input type="text" name="satuan_dasar" id="satuan_dasar" class="form-control" placeholder="Satuan Barang" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tipe_unit">Jenis Unit</label>
                        <input type="text" name="tipe_unit" class="form-control" placeholder="Masukan jenis unit baru..." required>
                    </div>
                    <div class="form-group">
                        <label for="standar_jumlah_barang">Jumlah Per unit</label>
                        <input type="text" name="standar_jumlah_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="standar_harga_jual">Harga Jual</label>
                        <input type="text" name="standar_harga_jual" class="form-control" required>
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

<!-- Edit Barang Modal -->
<div class="modal fade" id="ModalEditBarang" tabindex="-1" role="dialog" aria-labelledby="ModalEditBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditBarangLabel">Edit Barang</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="barangEditForm">
                    <input type="hidden" name="id_tipe" id="edit_id_tipe">
                    <input type="hidden" name="id_unit" id="edit_id_unit">
                    <div class="form-group">
                        <label for="edit_barang">Nama Barang</label>
                        <input type="text" name="jenis_barang" id="edit_barang" class="form-control" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_satuan_dasar">Satuan Barang</label>
                        <input type="text" name="satuan_dasar" id="edit_satuan_dasar" class="form-control" required readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_tipe_unit">Unit</label>
                        <input type="text" name="tipe_unit" id="edit_tipe_unit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_standar_jumlah_barang">Jumlah Per unit</label>
                        <input type="text" name="standar_jumlah_barang" id="edit_standar_jumlah_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_standar_harga_jual">Harga Jual</label>
                        <input type="text" name="standar_harga_jual" id="edit_standar_harga_jual" class="form-control" required>
                    </div>
                    <div id="responseMessageEdit" class="mt-3"></div>
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


<!-- Modal for raw stock -->
<!-- Add/Insert stock modal for Barang -->
<div class="modal fade" id="ModalTambahStock" tabindex="-1" role="dialog" aria-labelledby="ModalTambahStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahStockLabel">Tambah Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockTambahForm" method="post">
                    <div class="form-group">
                        <label for="id_tipe">Jenis Barang</label>
                        <select class="form-control" name="id_tipe" id="id_tipe" required>
                            <!-- Option will be filled on jquery -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tgl_stock">Tanggal</label>
                        <input type="text" name="tgl_stock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_barang">Stock Sisa</label>
                        <input type="text" name="stock_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="barang_masuk">Barang Masuk</label>
                        <input type="text" name="barang_masuk" class="form-control" placeholder="Masukan jenis unit baru..." required>
                    </div>
                    <div class="form-group">
                        <label for="barang_keluar">Barang Keluar</label>
                        <input type="text" name="barang_keluar" class="form-control" required>
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

<!-- Edit stock Modal -->
<div class="modal fade" id="ModalEditStock" tabindex="-1" role="dialog" aria-labelledby="ModalEditStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditStockLabel">Edit Barang</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="stockEditForm">
                    <div class="form-group">
                        <label for="id_tipe">Jenis Barang</label>
                        <select class="form-control" name="id_tipe" id="id_tipe" required>
                            <!-- Option will be filled on jquery -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tgl_stock">Tanggal</label>
                        <input type="text" name="tgl_stock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_barang">Stock Sisa</label>
                        <input type="text" name="stock_barang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="barang_masuk">Barang Masuk</label>
                        <input type="text" name="barang_masuk" class="form-control" placeholder="Masukan jenis unit baru..." required>
                    </div>
                    <div class="form-group">
                        <label for="barang_keluar">Barang Keluar</label>
                        <input type="text" name="barang_keluar" class="form-control" required>
                    </div>
                    <div id="responseMessageEdit" class="mt-3"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Delete stock Modal -->
<div class="modal fade" id="ModalDeleteStock" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteStockLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDeleteStockLabel">Delete Confirmation</h5>
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

        function formatFlexibleDecimal(value) {
        // Convert to string first to preserve exact value
        let strValue = value.toString();
        
        // Remove unnecessary trailing zeros after the decimal point
        strValue = strValue.replace(/\.?0+$/, '');
        
        return strValue;
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


    //Insert barang
    $('#barangTambahForm').on('submit', function(event) {
        event.preventDefault();

        $('#barangTambahForm').find('input[type="text"]').each(function() {
            $(this).val($(this).val().toLowerCase());
        });

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

    $('#unitBarangTambahForm').on('submit', function(event) {
        event.preventDefault();
        
        // Get the selected barang name and other details
        var barangName = $('#id_tipe option:selected').text().trim();
        var satuan = $('#id_tipe option:selected').data('satuan');
        
        // Create a FormData object to ensure all fields are sent correctly
        var formData = new FormData(this);
        
        // Ensure these fields are set
        formData.set('jenis_barang', barangName);
        formData.set('satuan_dasar', satuan);
        
        $.ajax({
            url: '<?= base_url('admin/barang/save'); ?>',
            type: 'POST',
            data: formData,
            processData: false,  // Important for FormData
            contentType: false,  // Important for FormData
            success: function(response) {
                if (response.error) {
                    // Handle specific error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.error,
                    });
                } else {
                    $('#ModalTambahUnitBarang').modal('hide');
                    $('#unitBarangTambahForm')[0].reset();
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
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Error: " + xhr.responseText,
                });
            }
        });
    });

    // Ensure satuan is populated when selecting tipe barang
    $('#id_tipe').on('change', function() {
        var satuan = $(this).find('option:selected').data('satuan');
        $('#satuan_dasar').val(satuan);
    });

    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var id_unit = $(this).data('unit-id');  // Changed from 'id_unit' to 'unit-id' to match HTML
        
        // Add debug logging
        console.log('Sending data:', { id_tipe: id, id_unit: id_unit });
        
        $.ajax({
            url: '<?= base_url('admin/barang/edit'); ?>',
            type: 'POST',
            data: { 
                id_tipe: id,
                id_unit: id_unit
            },
            success: function(response) {
                console.log('Raw response:', response);  // Debug log
                
                // Check if response needs to be parsed
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                
                $('#edit_id_tipe').val(response.id_tipe);
                $('#edit_id_unit').val(response.id_unit);
                $('#edit_barang').val(response.jenis_barang);
                $('#edit_satuan_dasar').val(response.satuan_dasar);
                $('#edit_tipe_unit').val(response.tipe_unit);
                $('#edit_standar_jumlah_barang').val(formatFlexibleDecimal(response.standar_jumlah_barang));
                $('#edit_standar_harga_jual').val(formatFlexibleDecimal(response.standar_harga_jual));
                
                $('#ModalEditBarang').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error details:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "Error: " + error,
                });
            }
        });
    });
    // Handle Tipe Barang change
    $('#id_tipe').on('change', function() {
        var selectedId = $(this).val();
        if (selectedId) {
            $.ajax({
                url: '<?= base_url('admin/barang/getTipeBarang') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var selectedItem = data.find(item => item.id == selectedId);
                    if (selectedItem) {
                        $('#satuan_dasar').val(selectedItem.satuan_dasar);
                    } else {
                        $('#satuan_dasar').val('');
                    }
                },
                error: function() {
                    console.error('Error fetching tipe barang data');
                }
            });
        } else {
            $('#satuan_dasar').val('');
        }
    });




    // Handle form submission for Edit
    $('#barangEditForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        $.ajax({
            url: '<?= base_url('admin/barang/update'); ?>',
            type: 'POST',
            data: $(this).serialize(), // Serialize the form data
            success: function(response) {
                $('#ModalEditBarang').modal('hide'); // Hide the modal
                $('#barangEditForm')[0].reset(); // Reset the form
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


    // Delete confirmation for Barang (main item)
    $('.delete-btn').on('click', function() {
        var id_tipe = $(this).data('id');
        var id_unit = $(this).data('unit-id'); // Add this line to capture unit ID if it exists

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
                // Prepare data object
                var deleteData = { id_tipe: id_tipe };
                
                // Add id_unit to data if it exists
                if (id_unit) {
                    deleteData.id_unit = id_unit;
                }

                $.ajax({
                    url: '<?= base_url('admin/barang/delete'); ?>', // Ensure this route can handle both scenarios
                    type: 'POST',
                    data: deleteData,
                    success: function(response) {
                        // Check the response for success
                        if (response.message) {
                            showAlertThenReload(
                                {
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message,
                                },
                                {
                                    icon: 'success',
                                    title: response.message
                                }
                            );
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: "Unexpected response",
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Parse error response
                        var errorMessage = xhr.responseJSON ? 
                            (xhr.responseJSON.error || "Unknown error") : 
                            "Error: " + error;

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

    // handle stock modal related
    // Handle form submission for Tambah Stock
    $('#formTambahStock').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        $.ajax({
            url: '<?= base_url('admin/stock/save'); ?>',
            type: 'POST',
            data: $(this).serialize(), // Serialize the form data
            success: function(response) {
                $('#ModalTambahStock').modal('hide'); // Hide the modal
                $('#formTambahStock')[0].reset(); // Reset the form
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Stock berhasil ditambahkan',
                    },
                    {
                        icon: 'success',
                        title: 'Stock berhasil ditambahkan'
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

    // Handle form submission for Edit Stock
    $('#formEditStock').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission
        $.ajax({
            url: '<?= base_url('admin/stock/update'); ?>',
            type: 'POST',
            data: $(this).serialize(), // Serialize the form data
            success: function(response) {
                $('#ModalEditStock').modal('hide'); // Hide the modal
                $('#formEditStock')[0].reset(); // Reset the form
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Stock berhasil diperbarui',
                    },
                    {
                        icon: 'success',
                        title: 'Stock berhasil diperbarui'
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

    // Delete confirmation for Stock
    $('#formDeleteStock').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission




    });

    // Handle modal close for Success Modal and trigger page reload
    $('#ModalSuccess').on('hidden.bs.modal', function() {
        location.reload(); // Reload the page when the success modal is closed
    });



    
    

});
</script>

