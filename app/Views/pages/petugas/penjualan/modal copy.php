    <!-- Add/Edit Penjualan Modal -->
    <div class="modal fade" id="ModalTambahPenjualan" tabindex="-1" role="dialog" aria-labelledby="ModalTambahPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document"> <!-- Increased modal size -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalTambahPenjualanLabel">Tambah/Edit Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="penjualanForm">
                        <div class="row">
                            <div class="col-md-6"> <!-- Left side for input fields -->
                                <input type="hidden" id="id_penjualan" name="id_penjualan">
                                <div class="form-group">
                                    <label for="nama_customer">Nama Customer</label>
                                    <input type="text" name="nama_customer" class="form-control" id="nama_customer" value="eceran"required>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-7">
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
                                    <div class="col-md-3">
                                        <label for="satuan_dasar">Satuan</label>
                                        <input type="text" name="satuan_dasar" id="satuan_dasar" class="form-control" placeholder="Satuan Barang" readonly>
                                    </div>

                                </div>

                                <div class="form-group row">
                                <div class="col-md-7">
                                    <label for="tipe_unit">Jenis Unit</label>
                                    <select name="tipe_unit" id="tipe_unit" class="form-control" required>
                                        <option>Pilih unit..</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                        <label for="standar_jumlah_barang">Jumlah</label>
                                        <input type="text" name="standar_jumlah_barang" id="standar_jumlah_barang" class="form-control" placeholder="Satuan Barang" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="harga_jual">Harga Jual Barang</label>
                                    <input type="text" name="harga_jual" class="form-control" id="harga_jual" required>
                                </div>
                                <!-- <div class="form-group">
                                    <label>Input Method</label>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-primary active">
                                            <input type="radio" name="input_method" id="input_uang" value="uang" checked> Uang
                                        </label>
                                        <label class="btn btn-outline-primary">
                                            <input type="radio" name="input_method" id="input_barang_keluar" value="barang_keluar"> Barang Keluar
                                        </label>
                                    </div>
                                </div> -->
                                <hr>
                                <div class="form-group">
                                    <label for="jumlah">Uang</label>
                                    <input type="number" id="jumlah" class="form-control" name="jumlah" value="0.00" />
                                </div>
                                <div class="form-group">
                                    <label for="jumlah_keluar">Barang Keluar</label>
                                    <input type="number" id="jumlah_keluar" class="form-control" name="jumlah_keluar" readonly />
                                </div>
                                <div class="form-group">
                                    <label for="id_method">Metode Pembayaran</label>
                                    <select name="id_method" id="id_method" class="form-control" required>
                                        <option value="3">Cash</option>
                                        <option value="1">Piutang</option>
                                        <option value="2">Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button type="button" id="addToCart" class="btn btn-primary">Add to Cart</button>
                                </div>
                            </div>
                            <div class="col-md-6"> <!-- Right side for cart -->
                                <h5>Cart</h5>
                                <table class="table" id="cartTable">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Cart items will be appended here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" id="insertAll" class="btn btn-success">Insert All</button>
                        </div>
                    </form>
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
                    <p id="successMessage"></p>
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
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="DeleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="DeleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="DeleteConfirmationModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this penjualan?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="chartModal" tabindex="-1" role="dialog" aria-labelledby="chartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="chartModalLabel">Grafik Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="chartTypeSelector">Pilih Tipe Chart</label>
                        <select id="chartTypeSelector" class="form-control">
                            <option value="total_barang_keluar">Total Barang Keluar</option>
                            <option value="total_harga_modal">Total Modal</option>
                            <option value="total_harga_jual">Total Penjualan</option>
                            <option value="total_untung">Total Keuntungan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tipeBarangSelector">Pilih Tipe Barang</label>
                        <select id="tipeBarangSelector" class="form-control">
                            <!-- Options will be populated here -->
                        </select>
                    </div>
                    <canvas id="penjualanChartModal"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Receipt Options Modal -->
<!-- Print Receipt Options Modal -->
<div class="modal fade" id="printReceiptModal" tabindex="-1" role="dialog" aria-labelledby="printReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printReceiptModalLabel">Cetak Struk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <h4>Pilih Opsi Cetak Struk</h4>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" id="cancelPrintBtn">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-warning" id="notPrintReceiptBtn">
                    <i class="fas fa-print"></i> Tidak Cetak Struk
                </button>
                <button type="button" class="btn btn-primary" id="printReceiptBtn">
                    <i class="fas fa-print"></i> Cetak Struk
                </button>
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
        });

        Toast.fire({
            icon: icon,
            title: title
        });
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

    // // Handle form submission for adding penjualan
    // $('#penjualanForm').on('submit', function(event) {
    //     $.ajax({
    //         url: '<?= base_url('admin/penjualan/save'); ?>',
    //         type: 'POST',
    //         data: $(this).serialize() + '&save=true',
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.status === 'success') {
    //                 $('#ModalTambahPenjualan').modal('hide');
    //                 $('#penjualanForm')[0].reset();
    //                 showAlertThenReload(
    //                     {
    //                         icon: 'success',
    //                         title: 'Berhasil!',
    //                         text: response.message || 'Penjualan berhasil ditambahkan',
    //                     },
    //                     {
    //                         icon: 'success',
    //                         title: 'Penjualan berhasil ditambahkan'
    //                     }
    //                 );
    //             } else {
    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Error!',
    //                     text: response.message || 'Terjadi kesalahan saat menyimpan data.',
    //                 });
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             let errorMessage = "Terjadi kesalahan: " + error;
    //             if (xhr.responseJSON && xhr.responseJSON.message) {
    //                 errorMessage = xhr.responseJSON.message;
    //             }
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Error!',
    //                 text: errorMessage,
    //             });
    //         }
    //     });
    // });

    $('#id_tipe').on('change', function() {
        var selectedId = $(this).val();
        if (selectedId) {
            $.ajax({
                url: '<?= base_url('admin/penjualan/getTipeBarang') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var selectedItem = data.find(item => item.id == selectedId);
                    if (selectedItem) {
                        // Populate satuan_dasar
                        $('#satuan_dasar').val(selectedItem.satuan_dasar);
                        
                        // Populate tipe_unit
                        if (selectedItem.units.length > 0) {
                            var unitOptions = selectedItem.units.map(unit => {
                                return `<option value="${unit.id_unit}">${unit.tipe_unit}</option>`;
                            }).join('');
                            
                            $('#tipe_unit').html(unitOptions);
                            
                            // Automatically update jumlah_keluar and harga_jual 
                            // based on the first unit initially
                            // $('#jumlah_keluar').val(selectedItem.units[0].standar_jumlah_barang);
                            $('#harga_jual').val(selectedItem.units[0].standar_harga_jual);
                            $('#standar_jumlah_barang').val(selectedItem.units[0].standar_jumlah_barang);
                        } else {
                            $('#jumlah_keluar').val('');
                            $('#harga_jual').val('');
                            $('#standar_jumlah_barang').val('');
                            $('#tipe_unit').html(''); // Clear the options if no units are found
                        }
                    } else {
                        $('#satuan_dasar').val('');
                        $('#harga_jual').val('');
                        $('#jumlah_keluar').val('');
                        $('#standar_jumlah_barang').val('');
                        $('#tipe_unit').html(''); // Clear the options if no item is found
                    }
                },
                error: function() {
                    console.error('Error fetching tipe barang data');
                }
            });
        } else {
            $('#satuan_dasar').val('');
            $('#harga_jual').val('');
            $('#jumlah_keluar').val('');
            $('#standar_jumlah_barang').val('');
            $('#tipe_unit').html(''); // Clear the options if no type is selected
        }
    });

    // Add an event listener for tipe_unit change
    $('#tipe_unit').on('change', function() {
        var selectedUnitId = $(this).val();
        var selectedTipeId = $('#id_tipe').val();

        // Find the selected item and unit
        $.ajax({
            url: '<?= base_url('admin/penjualan/getTipeBarang') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var selectedItem = data.find(item => item.id == selectedTipeId);
                if (selectedItem) {
                    var selectedUnit = selectedItem.units.find(unit => unit.id_unit == selectedUnitId);
                    
                    if (selectedUnit) {
                        // Update jumlah_keluar and harga_jual based on the selected unit
                        // $('#jumlah_keluar').val(selectedUnit.standar_jumlah_barang);
                        $('#standar_jumlah_barang').val(selectedUnit.standar_jumlah_barang);
                        $('#harga_jual').val(selectedUnit.standar_harga_jual);
                    }
                }
            },
            error: function() {
                console.error('Error fetching tipe barang data');
            }
        });
    });

    // Calculate Barang Keluar
    function calculateBarangKeluar() {
        var uang = parseFloat($('#jumlah').val()) || 0; // Get Uang
        var hargaJual = parseFloat($('#harga_jual').val()) || 0; // Get Harga Jual
        var standar_jumlah_barang  = parseFloat($('#standar_jumlah_barang').val()) || 0; // Get Standar J
        var barangKeluar = hargaJual > 0 ? (uang / hargaJual * standar_jumlah_barang) : 0; // Calculate Barang Keluar
        $('#jumlah_keluar').val(barangKeluar.toFixed(2)); // Display Barang Keluar
    }

    // Event listeners for calculating Barang Keluar
    $('#jumlah, #harga_jual').on('input', calculateBarangKeluar); // Trigger calculation on input change

    // Add item to cart
    $('#addToCart').on('click', function() {
        var itemName = $('#id_tipe option:selected').text(); // Get item name
        var idTipe = $('#id_tipe').val(); // Get id_tipe
        var idUnit = $('#tipe_unit').val(); // Get id_unit
        var unitName = $('#tipe_unit option:selected').text(); // Get unit name
        var quantity = $('#jumlah_keluar').val(); // Get calculated quantity
        var price = $('#harga_jual').val(); // Get item price

        if (itemName && idTipe && quantity && price && idUnit) {
            var cartRow = `<tr data-id-tipe="${idTipe}" data-id-unit="${idUnit}">
                <td>${itemName} (${unitName})</td>
                <td>${quantity}</td>
                <td>${price}</td>
                <td><button type="button" class="btn btn-danger removeItem">Remove</button></td>
            </tr>`;
            $('#cartTable tbody').append(cartRow);
            clearInputFields();
        } else {
            alert('Please fill in all fields before adding to cart.');
        }
    });


    // Clear form fields (but not 'jumlah')
    function clearInputFields() {
        $('#id_tipe').val('');
        $('#satuan_dasar').val('');
        $('#harga_jual').val('');
        $('#jumlah_keluar').val('');
        $('#keterangan').val('');
    }

    // Remove item from cart
    $('#cartTable').on('click', '.removeItem', function() {
        $(this).closest('tr').remove();
    });

    // // Insert all items from cart
    // $('#insertAll').on('click', function() {
    //     var cartData = [];
    //     $('#cartTable tbody tr').each(function() {
    //     var item = {
    //         id_tipe: $(this).data('id-tipe'), // Get id_tipe from the data attribute
    //         id_unit: $(this).data('id-unit'), // Get id_unit from the data attribute
    //         name: $(this).find('td:eq(0)').text(), // Get item name
    //         quantity: $(this).find('td:eq(1)').text(), // Get quantity
    //         price: $(this).find('td:eq(2)').text() // Get price
    //     };
    //     cartData.push(item);
    // });

    //     if (cartData.length === 0) {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Cart is Empty',
    //             text: 'Please add items to the cart before inserting.',
    //         });
    //         return;
    //     }

    //     // Get id_method and jumlah
    //     var idMethod = $('#id_method').val();
    //     var jumlah = $('#jumlah').val(); // Use value from 'jumlah' field

    //     // Check if 'jumlah' is empty before sending
    //     if (!jumlah || jumlah == "0.00") {
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Invalid Amount',
    //             text: 'Please enter a valid Uang value.',
    //         });
    //         return;
    //     }

    //     // SweetAlert confirmation dialog
    //     Swal.fire({
    //         title: 'Are you sure?',
    //         text: "Do you want to insert all items from the cart?",
    //         icon: 'warning',
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: 'Yes, insert all!',
    //         cancelButtonText: 'No, cancel!'
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             // If confirmed, send data to server
    //             $.ajax({
    //                 url: '<?= base_url('admin/penjualan/save'); ?>',
    //                 type: 'POST',
    //                 data: { 
    //                     cart: cartData,
    //                     id_method: idMethod,
    //                     jumlah: jumlah // Send correct jumlah
    //                 },
    //                 success: function(response) {
    //                     if (response.status === 'success') {
    //                         Swal.fire({
    //                             icon: 'success',
    //                             title: 'Success',
    //                             text: 'All items inserted successfully!',
    //                         }).then(() => {
    //                             $('#ModalTambahPenjualan').modal('hide');
    //                             $('#cartTable tbody').empty(); // Clear cart
    //                             $('#penjualanForm')[0].reset(); // Reset form
    //                         });
    //                     } else {
    //                         Swal.fire({
    //                             icon: 'error',
    //                             title: 'Error',
    //                             text: 'Error inserting items: ' + response.message,
    //                         });
    //                     }
    //                 },
    //                 error: function() {
    //                     Swal.fire({
    //                         icon: 'error',
    //                         title: 'Error',
    //                         text: 'An error occurred while inserting items.',
    //                     });
    //                 }
    //             });
    //         }
    //     });
    // });

$('#insertAll').on('click', function() {
    // Collect cart items
    var cartData = [];
    $('#cartTable tbody tr').each(function() {
        var item = {
            id_tipe: $(this).data('id-tipe'), 
            id_unit: $(this).data('id-unit'), 
            name: $(this).find('td:eq(0)').text(), 
            quantity: $(this).find('td:eq(1)').text(), 
            price: $(this).find('td:eq(2)').text() 
        };
        cartData.push(item);
    });

    // Validate cart is not empty
    if (cartData.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cart is Empty',
            text: 'Please add items to the cart before inserting.',
        });
        return;
    }

    // Get additional form data
    var idMethod = $('#id_method').val();
    var jumlah = $('#jumlah').val();
    var namaCustomer = $('#nama_customer').val();

    // Validate jumlah (payment amount)
    if (!jumlah || jumlah == "0.00") {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Amount',
            text: 'Please enter a valid Uang value.',
        });
        return;
    }

    // Validate customer name
    if (!namaCustomer) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Customer Name',
            text: 'Please enter a customer name.',
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Transaksi',
        text: "Apakah Anda yakin ingin melanjutkan transaksi?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Memproses Transaksi...',
                text: 'Mohon tunggu sebentar',
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX request
            $.ajax({
                url: '<?= base_url('admin/penjualan/save'); ?>',
                type: 'POST',
                dataType: 'json',
                data: { 
                    cart: cartData,
                    id_method: idMethod,
                    jumlah: jumlah,
                    nama_customer: namaCustomer
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Store response for later use
                        window.lastTransactionResponse = response;
                        
                        // Close loading and show print options modal
                        Swal.close();
                        $('#printReceiptModal').modal('show');
                    } else {
                        // Show error
                        Swal.fire({
                            icon: 'error',
                            title: 'Transaksi Gagal',
                            text: response.message || 'Terjadi kesalahan saat memproses transaksi'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Show network error
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Jaringan',
                        text: 'Tidak dapat menyelesaikan transaksi. Silakan periksa koneksi internet Anda.'
                    });
                    console.error('AJAX Error:', status, error);
                }
            });
        }
    });
});

// Print Receipt Handler
// Print Receipt Handler
$('#printReceiptBtn').on('click', function() {
    if (window.lastTransactionResponse) {
        var response = window.lastTransactionResponse;
        
        // Create a new window to print the receipt
        var printWindow = window.open('', '_blank');
        printWindow.document.write(response.receiptHtml);
        printWindow.document.close();
        
        try {
            printWindow.print();
            printWindow.close();
        } catch (error) {
            console.error('Print error:', error);
        }

        // Close print modal
        $('#printReceiptModal').modal('hide');
        
        // Reset form
        $('#ModalTambahPenjualan').modal('hide');
        $('#cartTable tbody').empty();
        $('#penjualanForm')[0].reset();

        // Show success with print confirmation and manual reload
        Swal.fire({
            icon: 'success',
            title: 'Transaksi Berhasil!',
            text: 'Struk telah dicetak.',
            showConfirmButton: true,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    }
});

// Not Print Receipt Handler
$('#notPrintReceiptBtn').on('click', function() {
    // Close print modal
    $('#printReceiptModal').modal('hide');
    $('#ModalTambahPenjualan').modal('hide');
    
    // Reset form
    $('#cartTable tbody').empty();
    $('#penjualanForm')[0].reset();

    // Show success without print, wait for user confirmation before reload
    Swal.fire({
        icon: 'success',
        title: 'Transaksi Berhasil!',
        text: 'Transaksi selesai tanpa mencetak struk.',
        showConfirmButton: true,
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            location.reload();
        }
    });
});

// Cancel Print Handler
$('#cancelPrintBtn').on('click', function() {
    // Just close the modal, no additional action needed
    $('#printReceiptModal').modal('hide');
});
        // Also modify the form submission handler to include nama_customer
        $('#penjualanForm').on('submit', function(event) {
            $.ajax({
                url: '<?= base_url('admin/penjualan/save'); ?>',
                type: 'POST',
                data: $(this).serialize() + '&save=true',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#ModalTambahPenjualan').modal('hide');
                        $('#penjualanForm')[0].reset();
                        showAlertThenReload(
                            {
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Penjualan berhasil ditambahkan',
                            },
                            {
                                icon: 'success',
                                title: 'Penjualan berhasil ditambahkan'
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




        function confirmPayment(id_pembelian) {
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
        }





    // Chart Initialization and Data Fetching
    function initializePenjualanChart(chartType = 'total_barang_keluar', idTipe = '') {
        $.ajax({
            url: '<?= base_url('admin/penjualan/chart-data'); ?>',
            method: 'GET',
            data: {
                chart_type: chartType,
                id_tipe: idTipe,
                year: new Date().getFullYear() // Current year by default
            },
            dataType: 'json',
            success: function(data) {
                renderPenjualanChart(data, chartType);
            },
            error: function(xhr, status, error) {
                console.error('Chart Data Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load chart data'
                });
            }
        });
    }

    function renderPenjualanChart(data, chartType) {
    // Destroy existing chart if it exists
    if (window.penjualanChart instanceof Chart) {
        window.penjualanChart.destroy();
    }

    const ctx = document.getElementById('penjualanChartModal').getContext('2d');
    
    // Indonesian month names
    const indonesianMonths = [
        'Januari', 'Februari', 'Maret', 'April', 
        'Mei', 'Juni', 'Juli', 'Agustus', 
        'September', 'Oktober', 'November', 'Desember'
    ];

    // Define chart labels and colors based on type
    const chartConfig = {
        'total_barang_keluar': {
            label: 'Barang Keluar',
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)'
        },
        'total_harga_modal': {
            label: 'Total Modal',
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
            borderColor: 'rgba(255, 99, 132, 1)'
        },
        'total_harga_jual': {
            label: 'Total Penjualan',
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)'
        },
        'total_untung': {
            label: 'Total Keuntungan',
            backgroundColor: 'rgba(255, 206, 86, 0.6)',
            borderColor: 'rgba(255, 206, 86, 1)'
        }
    };

    const config = chartConfig[chartType] || chartConfig['total_barang_keluar'];

    window.penjualanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => {
                const monthName = indonesianMonths[parseInt(item.month) - 1];
                return `${monthName}`;
            }),
            datasets: [{
                label: config.label,
                data: data.map(item => item.total),
                backgroundColor: config.backgroundColor,
                borderColor: config.borderColor,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: `Grafik ${config.label} Tahun ${new Date().getFullYear()}`,
                    font: {
                        size: 16
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: config.label
                    }
                }
            }
        }
    });
}

    // Populate Tipe Barang Dropdown
    function populateTipeBarangDropdown() {
        $.ajax({
            url: '<?= base_url('admin/barang/getTipeBarang'); ?>',
            method: 'GET',
            dataType: 'json',
            success: function(tipeBarangList) {
                const $tipeBarangSelector = $('#tipeBarangSelector');
                $tipeBarangSelector.empty();
                $tipeBarangSelector.append('<option value="">Semua Barang</option>');
                
                tipeBarangList.forEach(function(tipe) {
                    $tipeBarangSelector.append(
                        `<option value="${tipe.id}">${tipe.jenis_barang}</option>`
                    );
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat daftar barang'
                });
            }
        });
    }

    // Open the chart modal
    $('#showChartBtn').on('click', function() {
        const selectedChartType = $('#chartTypeSelector').val();
        const selectedIdTipe = $('#tipeBarangSelector').val();
        
        // Populate Tipe Barang Dropdown
        populateTipeBarangDropdown();

        // Initialize chart
        initializePenjualanChart(selectedChartType, selectedIdTipe);
        $('#chartModal').modal('show');
    });

    // Chart Type Selector Change
    $('#chartTypeSelector').on('change', function() {
        const selectedChartType = $(this).val();
        const selectedIdTipe = $('#tipeBarangSelector').val();
        initializePenjualanChart(selectedChartType, selectedIdTipe);
    });

    // Tipe Barang Selector Change
    $('#tipeBarangSelector').on('change', function() {
        const selectedIdTipe = $(this).val();
        const selectedChartType = $('#chartTypeSelector').val();
        initializePenjualanChart(selectedChartType, selectedIdTipe);
    });

    $('#chartModal').on('hidden.bs.modal', function () {
    // Remove any lingering backdrop
    $('.modal-backdrop').remove();
    });

});
</script>