<!-- Add/Insert Pembelian Modal -->
<div class="modal fade" id="ModalTambahPembelian" tabindex="-1" role="dialog" aria-labelledby="ModalTambahPembelianLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahPembelianLabel">Tambah Pembelian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="pembelianTambahForm">
                    <div class="form-group">
                        <label for="nama_supplier">Nama Supplier</label>
                        <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" placeholder="Nama Supplier" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="id_tipe">Tipe Barang</label>
                                <select name="id_tipe" id="id_tipe" class="form-control" required>
                                    <option value="">Pilih Tipe Barang</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="satuan_dasar">Satuan</label>
                                <input type="text" id="satuan_dasar" name="satuan_dasar" class="form-control" readonly disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="barang_masuk">Barang Masuk</label>
                        <input type="text" name="barang_masuk" class="form-control" id="barang_masuk" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_modal_barang">Harga Modal Barang</label>
                        <input type="text" name="harga_modal_barang" class="form-control" id="harga_modal_barang" required>
                    </div>
         
                    <div class="form-group">
                        <label for="tgl_masuk">Tanggal</label>
                        <input type="date" name="tgl_masuk" class="form-control" id="tgl_masuk" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <!-- <div class="btn-group btn-group-toggle w-100">
                        <button type="button" class="btn btn-outline-primary" id="toggle-piutang">Piutang</button>
                        <button type="button" class="btn btn-outline-primary" id="toggle-trf">Transfer</button>
                    </div>

                    <div id="piutang-input" class="form-group" style="display: none;">
                        <label for="piutang">Piutang</label>
                        <input type="text" id="piutang" name="piutang" step="0.01" min="0" placeholder="0.00" class="form-control">
                    </div>

                    <div id="trf-input" class="form-group" style="display: none;">
                        <label for="trf">Transfer</label>
                        <input type="text" id="trf" name="trf" class="form-control">
                    </div> -->


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

<!-- Update Modal -->
<div class="modal fade" id="editPembelianModal" tabindex="-1" role="dialog" aria-labelledby="editPembelianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPembelianModalLabel">Edit Pembelian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="pembelianEditForm">
                    <div class="row">
                        <!-- Left Column: Pembelian -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">Detail Pembelian</div>
                                <div class="card-body">
                                    <input type="hidden" id="edit_id_pembelian" name="id_pembelian">
                                    
                                    <div class="form-group">
                                        <label for="edit_tgl_masuk">Tanggal Masuk</label>
                                        <input type="date" class="form-control" id="edit_tgl_masuk" name="tgl_masuk" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_id_supplier">Supplier</label>
                                        <select class="form-control" id="edit_id_supplier" name="id_supplier" required>
                                            <!-- Populate with supplier options -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_id_tipe">Jenis Barang</label>
                                        <select class="form-control" id="edit_id_tipe" name="id_tipe" required>
                                            <!-- Populate with barang options -->
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_barang_masuk">Jumlah Barang Masuk</label>
                                        <input type="text" class="form-control" id="edit_barang_masuk" name="barang_masuk" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_harga_modal_barang">Harga Modal</label>
                                        <input type="text" class="form-control" id="edit_harga_modal_barang" name="harga_modal_barang" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="edit_total_meity">Total Meity</label>
                                        <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text h-100 d-flex align-items-center" data-toggle="tooltip" title="Total Meity secara automatis terhitung">
                                                            <i class="fas fa-exclamation-circle fa-lg"></i>
                                                        </span>
                                                </div>
                                            <input type="text" class="form-control" id="edit_total_meity" name="total_meity" required readonly>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Meity Details -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">Detail Meity</div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="edit_terkumpul">Uang Terkumpul</label>
                                        <input type="text" class="form-control" id="edit_terkumpul" name="terkumpul" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_hutang">Hutang</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text h-100 d-flex align-items-center" data-toggle="tooltip" title="Hutang secara Automatis Terhitung">
                                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="edit_hutang" name="hutang" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_current_piutang">Jumlah Piutang</label>
                                        <input type="text" class="form-control" id="edit_current_piutang" name="current_piutang" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_current_transfer">Jumlah Transfer</label>
                                        <input type="text" class="form-control" id="edit_current_transfer" name="current_transfer" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_current_sisa">Jumlah Sisa</label>
                                        <input type="text" class="form-control" id="edit_current_sisa" name="current_sisa" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_total_cash">Total Cash</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text h-100 d-flex align-items-center" data-toggle="tooltip" title="Total Cash secara Automatis Terhitung">
                                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="edit_total_cash" name="total_cash" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_sudah_setor">Tangal Setor</label>
                                        <input type="date" class="form-control" id="edit_sudah_setor" name="sudah_setor" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit_status">Status</label>
                                        <select class="form-control" id="edit_status" name="status" required>
                                            <option value="0">Belum Lunas</option>
                                            <option value="1">Menunggu Konfirmasi</option>
                                            <option value="2">Lunas</option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitEditPembelian">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Cash Modal -->
<div class="modal fade" id="ModalMarkAsCash" tabindex="-1" role="dialog" aria-labelledby="ModalMarkAsCashLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalMarkAsCashLabel">Confirm Mark as Cash</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this payment as cash? This action cannot be undone.</p>
                <div id="responseMessageMarkAsCash" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="confirmMarkAsCash" data-id-pembelian="">Yes, Mark as Cash</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<!-- Delete Pembelian Modal -->
<div class="modal fade" id="deletePembelianModal" tabindex="-1" role="dialog" aria-labelledby="deletePembelianModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePembelianModalLabel">Hapus Pembelian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus pembelian ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeletePembelian">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Lunas Modal -->
<div class="modal fade" id="ModalMarkAsLunas" tabindex="-1" role="dialog" aria-labelledby="ModalMarkAsLunasLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalMarkAsLunasLabel">Konfirmasi Pelunasan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <strong>Perhatian!</strong> Anda akan mengkonfirmasi pelunasan pembayaran ini.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Total Meity:</strong> 
                        <span id="lunasModalTotalMeity"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Terkumpul:</strong> 
                        <span id="lunasModalTerkumpul"></span>
                    </div>
                </div>
                <input type="hidden" id="lunasModalIdPembelian">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmMarkAsLunas">
                    <i class="fas fa-check-circle"></i> Konfirmasi Pelunasan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pembelian Chart Modal -->
<div class="modal fade" id="PembelianChartModal" tabindex="-1" role="dialog" aria-labelledby="PembelianChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="PembelianChartModalLabel">
                    Grafik Terkumpul vs Total Meity Pembelian
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <select id="tipeBarangFilter" class="form-control">
                            <option value="">Semua Tipe Barang</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                </div>
                <canvas id="pembelianChart" style="width: 100%; height: 400px;"></canvas>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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

    // Handle Tooltips
    $('[data-toggle="tooltip"]').tooltip();




    // Load Tipe Barang options
    loadTipeBarang();
    loadSupplier();
    $('#edit_barang_masuk, #edit_harga_modal_barang').on('input', function() {
    calculateTotalMeity();
    });

    // Handle form submission for adding pembelian
    $('#pembelianTambahForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('admin/pembelian/save'); ?>',
            type: 'POST',
            data: $(this).serialize() + '&save=true',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#ModalTambahPembelian').modal('hide');
                    $('#pembelianTambahForm')[0].reset();
                    showAlertThenReload(
                        {
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Pembelian berhasil ditambahkan',
                        },
                        {
                            icon: 'success',
                            title: 'Pembelian berhasil ditambahkan'
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Terjadi kesalahan saat menyimpan data.',
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = "Terjadi kesalahan: " + error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });

    // Modify the existing code to include the calculation function
    function calculateTotalMeity() {
        var barang_masuk = $('#edit_barang_masuk').val() || 0;
        var harga_modal_barang = $('#edit_harga_modal_barang').val() || 0;
        
        // Ensure inputs are converted to numbers
        barang_masuk = parseFloat(barang_masuk);
        harga_modal_barang = parseFloat(harga_modal_barang);
        
        // Calculate total and handle potential NaN
        var total_meity = isNaN(barang_masuk * harga_modal_barang) 
            ? 0 
            : (barang_masuk * harga_modal_barang).toFixed(2);
        
        $('#edit_total_meity').val(total_meity);
    }

    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');

        $.ajax({
            url: '<?= base_url('admin/pembelian/edit/'); ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;
                    
                    // Populate modal fields
                    $('#edit_id_pembelian').val(data.id_pembelian);
                    $('#edit_tgl_masuk').val(data.tgl_masuk);
                    $('#edit_id_supplier').val(data.id_supplier);
                    $('#edit_id_tipe').val(data.id_tipe);
                    $('#edit_jenis_barang').val(data.jenis_barang);
                    $('#edit_barang_masuk').val(data.barang_masuk);
                    $('#edit_harga_modal_barang').val(data.harga_modal_barang);
                    calculateTotalMeity(); // Call the calculation function
                    $('#edit_terkumpul').val(data.terkumpul);
                    $('#edit_hutang').val(data.hutang);
                    $('#edit_current_piutang').val(data.current_piutang);
                    $('#edit_current_transfer').val(data.current_transfer);
                    $('#edit_current_sisa').val(data.current_sisa);
                    $('#edit_total_cash').val(data.total_cash);
                    $('#edit_sudah_setor').val(data.sudah_setor);
                    $('#edit_status').val(data.status);


                    $('#editPembelianModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch pembelian details'
                });
            }
        });
    });

    $('#submitEditPembelian').on('click', function() {
        var id_pembelian = $('#edit_id_pembelian').val();
        var tgl_masuk = $('#edit_tgl_masuk').val();
        var id_supplier = $('#edit_id_supplier').val();
        var id_tipe = $('#edit_id_tipe').val();
        var barang_masuk = $('#edit_barang_masuk').val();
        var harga_modal_barang = $('#edit_harga_modal_barang').val();
        var total_meity = $('#edit_total_meity').val();
        var terkumpul = $('#edit_terkumpul').val();
        var current_piutang = $('#edit_current_piutang').val();
        var current_transfer = $('#edit_current_transfer').val();
        var current_sisa = $('#edit_current_sisa').val();
        var sudah_setor = $('#edit_sudah_setor').val();
        var status = $('#edit_status').val();

        $.ajax({
            url: '<?= base_url('admin/pembelian/update'); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                id_pembelian: id_pembelian,
                tgl_masuk: tgl_masuk,
                id_supplier: id_supplier,
                id_tipe: id_tipe,
                barang_masuk: barang_masuk,
                harga_modal_barang: harga_modal_barang,
                total_meity: total_meity,
                terkumpul: terkumpul,
                current_piutang: current_piutang,
                current_transfer: current_transfer,
                current_sisa: current_sisa,
                sudah_setor: sudah_setor,
                status: status
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update pembelian'
                });
            }
        });
    });

    // Handle Tipe Barang change
    $('#id_tipe').on('change', function() {
        var selectedId = $(this).val();
        if (selectedId) {
            $.ajax({
                url: '<?= base_url('admin/pembelian/getTipeBarang') ?>',
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

    // Populate the select options on page load
    $.ajax({
        url: '<?= base_url('admin/pembelian/getTipeBarang') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var select = $('#id_tipe');
            var editSelect = $('#edit_id_tipe');
            select.empty();
            select.append('<option value="">Pilih Tipe Barang</option>');
            $.each(data, function(index, item) {
                select.append('<option value="' + item.id + '">' + item.jenis_barang + '</option>');
            });
        },
        error: function() {
            console.error('Error fetching tipe barang data');
        }
    });

    // Update button click
    $('#submitUpdate').click(function() {
        var formData = $('#updateForm').serialize();
        $.ajax({
            url: '<?= base_url('admin/pembelian/updateTerkumpul'); ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    $('#updateModal').modal('hide');
                    showAlertThenReload(
                        {
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Record berhasil diperbarui',
                        },
                        {
                            icon: 'success',
                            title: 'Record berhasil diperbarui'
                        }
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "An error occurred: " + error,
                });
            }
        });
    });

    $('.mark-as-cash-trigger').on('click', function() {
    var idPembelian = $(this).data('id_pembelian');
    $('#confirmMarkAsCash').data('id_pembelian', idPembelian);
    });

    $('#confirmMarkAsCash').on('click', function() {
    var selectedId = $(this).data('id_pembelian');
    
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to mark this payment as cash? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Mark as Cash!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/pembelian/confirmCash') ?>',
                    method: 'POST',
                    data: {
                        id_pembelian: selectedId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Full response:', response); // Add full response logging
                        
                        if (response.status === 'success') {
                            $('#ModalMarkAsCash').modal('hide');
                            
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload(); // Force page reload
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.log('Response Text:', xhr.responseText);
                        
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred: ' + error,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    function loadSupplier() {
        $.ajax({
            url: '<?= base_url('admin/pembelian/getSupplier'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.log('Error: ' + response.error);
                    return;
                }

                // Populate both dropdowns
                const $idSupplier = $('#id_supplier');
                const $editIdSupplier = $('#edit_id_supplier');
                
                // Clear existing options
                $idSupplier.empty();
                $editIdSupplier.empty();

                // Add default option
                $idSupplier.append('<option value="">Pilih Supplier</option>');
                $editIdSupplier.append('<option value="">Pilih Supplier</option>');

                // Populate options
                if (Array.isArray(response)) {
                    response.forEach(item => {
                        const optionHtml = `<option value="${item.id}">${item.nama_supplier}</option>`;
                        $idSupplier.append(optionHtml);
                        $editIdSupplier.append(optionHtml);
                    });
                } else {
                    Object.entries(response).forEach(([key, value]) => {
                        const optionHtml = `<option value="${key}">${value}</option>`;
                        $idSupplier.append(optionHtml);
                        $editIdSupplier.append(optionHtml);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat supplier'
                });
            }
        });
    }

    function loadTipeBarang() {
        $.ajax({
            url: '<?= base_url('admin/pembelian/getTipeBarang'); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.log('Error: ' + response.error);
                    return;
                }

                // Populate both dropdowns
                const $idTipe = $('#id_tipe');
                const $editIdTipe = $('#edit_id_tipe');
                
                // Clear existing options
                $idTipe.empty();
                $editIdTipe.empty();

                // Add default option
                $idTipe.append('<option value="">Pilih Tipe Barang</option>');
                $editIdTipe.append('<option value="">Pilih Tipe Barang</option>');

                // Populate options
                if (Array.isArray(response)) {
                    response.forEach(item => {
                        const optionHtml = `<option value="${item.id}">${item.jenis_barang}</option>`;
                        $idTipe.append(optionHtml);
                        $editIdTipe.append(optionHtml);
                    });
                } else {
                    Object.entries(response).forEach(([key, value]) => {
                        const optionHtml = `<option value="${key}">${value}</option>`;
                        $idTipe.append(optionHtml);
                        $editIdTipe.append(optionHtml);
                    });
                }

                // Attach change event for satuan_dasar
                $idTipe.on('change', function() {
                    const selectedId = $(this).val();
                    if (selectedId) {
                        const selectedItem = response.find(item => item.id == selectedId);
                        $('#satuan_dasar').val(selectedItem ? selectedItem.satuan_dasar : '');
                    } else {
                        $('#satuan_dasar').val('');
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat tipe barang'
                });
            }
        });
    }

    $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        $('#confirmDeletePembelian').data('id', id); // Pass the ID to the confirm button
    });

    // Event listener for confirm delete button
    $('#confirmDeletePembelian').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '<?= base_url('admin/pembelian/delete/'); ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                    }).then(() => {
                        location.reload(); // Reload the page after deletion
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan: ' + error,
                });
            }
        });
    });


    window.confirmPayment = function(id_pembelian) {
        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: "Apakah Anda yakin ingin mengkonfirmasi pembayaran ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/pembelian/confirmPayment') ?>',
                    type: 'POST',
                    data: { id_pembelian: id_pembelian },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pembayaran berhasil dikonfirmasi',
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Terjadi kesalahan saat mengkonfirmasi pembayaran.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr, status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: "Terjadi kesalahan: " + error,
                        });
                    }
                });
            }
        });
    };


    const months = [
        'Januari', 'Februari', 'Maret', 'April', 
        'Mei', 'Juni', 'Juli', 'Agustus', 
        'September', 'Oktober', 'November', 'Desember'
    ];

    // Function to populate Tipe Barang Dropdown
    function populateTipeBarangDropdown() {
        $.ajax({
            url: '<?= base_url('admin/pembelian/get-tipe-barang'); ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const select = $('#tipeBarangFilter');
                select.empty();
                select.append('<option value="">Semua Tipe Barang</option>');
                
                // Ensure response is an array
                if (Array.isArray(response)) {
                    response.forEach(item => {
                        select.append(`
                            <option value="${item.jenis_barang}">
                                ${item.jenis_barang}
                            </option>
                        `);
                    });
                } else {
                    console.error('Invalid response format for tipe barang', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching tipe barang:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat daftar tipe barang'
                });
            }
        });
    }

    // Function to fetch and render chart data
    function fetchPembelianChartData(jenis_barang = null) {
        $.ajax({
            url: '<?= base_url('admin/pembelian/chart-data'); ?>',
            method: 'GET',
            dataType: 'json',
            data: { 
                jenis_barang: jenis_barang,
                year: new Date().getFullYear()
            },
            success: function(response) {
                // Validate response
                if (!response || !Array.isArray(response)) {
                    renderEmptyChart();
                    return;
                }

                // Prepare data arrays with zeros for all months
                const terkumpulData = new Array(12).fill(0);
                const totalMeityData = new Array(12).fill(0);

                // Populate data
                response.forEach(item => {
                    const monthIndex = parseInt(item.month) - 1;
                    terkumpulData[monthIndex] = parseFloat(item.total_terkumpul) || 0;
                    totalMeityData[monthIndex] = parseFloat(item.total_meity) || 0;
                });

                // Render Chart
                renderChart(terkumpulData, totalMeityData, jenis_barang);
            },
            error: function(xhr, status, error) {
                console.error('Chart data fetch error:', error);
                
                // Use SweetAlert for error notification
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Tidak dapat mengambil data grafik. Silakan coba lagi.'
                });

                // Render empty chart as fallback
                renderEmptyChart();
            }
        });
    }

    // Function to render chart (previous implementation remains the same)
    function renderChart(terkumpulData, totalMeityData, jenis_barang = null) {
        const ctx = document.getElementById('pembelianChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (window.pembelianChart instanceof Chart) {
            window.pembelianChart.destroy();
        }

        window.pembelianChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Total Terkumpul (Rp)',
                        data: terkumpulData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Meity (Rp)',
                        data: totalMeityData,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: jenis_barang 
                            ? `Grafik Pembelian ${jenis_barang} Tahun ${new Date().getFullYear()}`
                            : `Grafik Pembelian Semua Tipe Barang Tahun ${new Date().getFullYear()}`
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total (Rp)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Event listener for dropdown change
    $('#tipeBarangFilter').on('change', function() {
        const selectedJenisBarang = $(this).val();
        fetchPembelianChartData(selectedJenisBarang);
    });

    // Initial population of the dropdown and chart data
    populateTipeBarangDropdown();
    fetchPembelianChartData();
});





</script>