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
                                <input type="text" name="nama_customer" class="form-control" id="nama_customer" value="eceran" autocomplete="off" required>
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
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="jumlah_untuk_barang">Uang Untuk Barang</label>
                                    <input type="text" id="jumlah_untuk_barang" class="form-control" name="jumlah_untuk_barang" placeholder="..." required/>
                                </div>
                                <div class="col-md-6">
                                    <label for="jumlah">Uang Dari Pembeli</label>
                                    <input type="text" id="jumlah" class="form-control" name="jumlah" placeholder="..." required/>
                                </div>
                            </div>
                            <!-- <div class="form-group">
                                <label for="jumlah">Uang</label>
                                <input type="text" id="jumlah" class="form-control" name="jumlah" value="0.00" />
                            </div> -->
                            <!-- <div class="form-group">
                                <label for="uang_keluar">Uang Diberikan</label>
                                <input type="text" id="uang_keluar" class="form-control" name="jumlah" value="0.00" />
                            </div> -->
                            <div class="form-group">
                                <label>Quantity</label>
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        id="jumlah_keluar" 
                                        class="form-control" 
                                        readonly 
                                        step="0.01"
                                    >
                                    <!-- Button will be dynamically added by JS -->
                                </div>
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
                                <label for="tgl_penjualan">Tanggal Transaksi</label>
                                <input type="date" id="tgl_penjualan" class="form-control" value="<?= date('Y-m-d') ?>">
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
                                        <th>Metode</th>
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

<!-- Edit Penjualan Modal -->
<div class="modal fade" id="ModalEditPenjualanDetail" tabindex="-1" role="dialog" aria-labelledby="ModalEditPenjualanDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document"> <!-- Increased modal size -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditPenjualanDetailLabel">Edit Detail Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="penjualanDetailEditForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="form-group">
                                <label for="edit_id_method">Metode Pembayaran</label>
                                <select name="id_method" id="edit_id_method" class="form-control" required>
                                    <option value="3">Cash</option>
                                    <option value="1">Piutang</option>
                                    <option value="2">Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" id="updatePenjualanDetail" class="btn btn-success">Update</button>
                        </div>
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
                                
                                // Automatically update harga_jual based on the first unit initially
                                $('#harga_jual').val(formatFlexibleDecimal(selectedItem.units[0].standar_harga_jual));
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
                            // Update harga_jual based on the selected unit
                            $('#standar_jumlah_barang').val(selectedUnit.standar_jumlah_barang);
                            $('#harga_jual').val(formatFlexibleDecimal(selectedUnit.standar_harga_jual));
                        }
                    }
                },
                error: function() {
                    console.error('Error fetching tipe barang data');
                }
            });
        });

        function calculateBarangQuantity() {
            try {
                // Get the raw value from jumlah_untuk_barang and parse it
                const uangJumlahBarang = parseFloat($('#jumlah_untuk_barang').val().replace(/[^0-9,-]/g, '').replace(',', '.') || '0');
                
                // Get harga_jual directly from the input (assumed to be raw number)
                const hargaJualRaw = $('#harga_jual').val(); // Get the raw value
                const hargaJual = parseFloat(hargaJualRaw) || 0; // Directly parse as float

                const standarJumlah = parseFloat($('#standar_jumlah_barang').val() || '1'); // Default to 1 if empty
                const $jumlahKeluar = $('#jumlah_keluar');

                // Validate inputs
                if (uangJumlahBarang <= 0 || hargaJual <= 0) {
                    $jumlahKeluar.val('0.00'); // Reset if invalid
                    return;
                }

                // Calculate barang quantity based on uang jumlah barang
                const barangKeluar = (uangJumlahBarang / hargaJual) * standarJumlah;

                // Set the calculated quantity, ensuring two decimal places
                $jumlahKeluar.val(barangKeluar.toFixed(6));

            } catch (error) {
                console.error('Calculation Error:', error);
                $('#jumlah_keluar').val('0.00'); // Reset on error
            }
        }

        // Initialize Cleave.js for currency formatting
        const cleaveJumlahUntukBarang = new Cleave('#jumlah_untuk_barang', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            prefix: 'Rp ', // Optional: Add prefix for currency
            numeralDecimalMark: ',', // Use comma as decimal mark
            numeralDecimalScale: 12, // Allow up to 13 decimal places
            numeralIntegerScale: 20,
            delimiter: '.', // Use dot as thousands separator
        });

        const cleaveJumlah = new Cleave('#jumlah', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            prefix: 'Rp ', // Optional: Add prefix for currency
            numeralDecimalMark: ',', // Use comma as decimal mark
            numeralDecimalScale: 12, // Allow up to 13 decimal places
            numeralIntegerScale: 20,
            delimiter: '.', // Use dot as thousands separator
        });

        function formatFlexibleDecimal(value) {
            // Convert to number to remove leading zeros
            let num = parseFloat(value);
            
            // Return the number as a string without trailing zeros
            return num.toString();
        }

        $(document).ready(function() {

            // Add this new block of code
            $('#jumlah_untuk_barang').on('input', function() {
                // Get the value from jumlah_untuk_barang
                var uangJumlahBarang = $(this).val();

                // Set jumlah to the same value
                $('#jumlah').val(uangJumlahBarang);
            });

            // Add input listener for jumlah_untuk_barang
            $('#jumlah_untuk_barang, #harga_jual, #standar_jumlah_barang')
                .on('input', calculateBarangQuantity);

            // Add input validation for manual mode
            $('#jumlah_keluar').on('input', function() {
                // Only allow numeric input when in manual mode
                if ($(this).hasClass('manual-input')) {
                    $(this).val($(this).val().replace(/[^0-9.]/g, ''));
                }
            });
        });

        function sanitizeCurrencyInput(value) {
            // Remove 'Rp ', '.', and any other non-numeric characters
            let sanitized = value
                .replace('Rp ', '')
                .replace(/\./g, '')  // Remove all dots
                .replace(',', '.')   // Replace comma with dot
                .trim();
            
            // Parse as float and ensure reasonable precision
            return parseFloat(parseFloat(sanitized).toFixed(2));
        }

        $('#addToCart').on('click', function() {
            var itemName = $('#id_tipe option:selected').text();
            var idTipe = $('#id_tipe').val();
            var idUnit = $('#tipe_unit').val();
            var unitName = $('#tipe_unit option:selected').text();
            var quantity = $('#jumlah_keluar').val();
            var price = $('#harga_jual').val();
            var standarJumlah = $('#standar_jumlah_barang').val();
            var idMethod = $('#id_method').val();
            
            // Use the actual input amount
            var jumlah = $('#jumlah').val();
            var isManualInput = $('#jumlah_keluar').hasClass('manual-input');

            if (itemName && idTipe && quantity && price && idUnit && idMethod && jumlah) {
                var cartRow = `<tr 
                    data-id-tipe="${idTipe}" 
                    data-id-unit="${idUnit}"
                    data-id-method="${idMethod}"
                    data-standar-jumlah="${standarJumlah}"
                    data-jumlah="${jumlah}"
                    data-is-manual-input="${isManualInput}">
                    <td>${itemName} (${unitName})</td>
                    <td>${quantity}</td>
                    <td>${price}</td>
                    <td>${$('#id_method option:selected').text()}</td>
                    <td><button type="button" class="btn btn-danger removeItem">Remove</button></td>
                </tr>`;
                
                $('#cartTable tbody').append(cartRow);
                clearInputFields();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Information',
                    text: 'Please fill in all fields before adding to cart.'
                });
            }
        });

        // Optional: Add some CSS for manual input mode
        $('head').append(`
            <style>
                .manual-input {
                    background-color: #fff3cd !important;
                    border-color: #ffeeba !important;
                    color: #856404 !important;
                }
            </style>
        `);

        // Clear form fields (but not 'jumlah')
        function clearInputFields() {
            $('#id_tipe').val('');
            $('#satuan_dasar').val('');
            $('#harga_jual').val('');
            $('#jumlah_keluar').val('');
            $('#jumlah_untuk_barang').val('');
            $('#jumlah').val('');
            $('#keterangan').val('');
            $('#tipe_unit').val('');
            $('#standar_jumlah_barang').val('');

            $('#id_method').val('3');
        }

        // Remove item from cart
        $('#cartTable').on('click', '.removeItem', function() {
            $(this).closest('tr').remove();
        });

        $('#insertAll').on('click', function() {
            var cartData = [];
            var tglPenjualan = $('#tgl_penjualan').val(); // Get the selected date
            console.log("Selected Date:", tglPenjualan); // Log the selected date

            $('#cartTable tbody tr').each(function() {
                var $row = $(this);
                
                var item = {
                    id_tipe: $row.data('id-tipe'),
                    id_unit: $row.data('id-unit'),
                    name: $row.find('td:eq(0)').text().trim(),
                    quantity: parseFloat($row.find('td:eq(1)').text()) || 0,
                    price: parseFloat($row.find('td:eq(2)').text()) || 0,
                    standar_jumlah_barang: parseFloat($row.data('standar-jumlah')) || 1,
                    tgl_penjualan: tglPenjualan, // Include the date here
                    
                    nama_customer: $('#nama_customer').val(),
                    id_method: $row.data('id-method'), // Get method from row data attribute
                    jumlah: sanitizeCurrencyInput($row.data('jumlah') || '0')
                };

                if (item.quantity > 0 && item.price > 0) {
                    cartData.push(item);
                }
            });

            // Rest of the existing submission logic remains the same
            if (cartData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cart is Empty',
                    text: 'Please add items to the cart before inserting.',
                });
                return;
            }

            // Debug logging
            console.log('Cart Data:', cartData);

            // Confirmation and AJAX submission logic
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
                    $.ajax({
                        url: '<?= base_url('admin/penjualan/save'); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: { 
                            cart: cartData,
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Store the response globally and in sessionStorage
                                window.lastTransactionResponse = response;
                                sessionStorage.setItem('lastTransactionResponse', JSON.stringify(response));
                                
                                $('#printReceiptModal').modal('show');
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Transaksi Gagal',
                                    text: response.message || 'Terjadi kesalahan saat memproses transaksi'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Jaringan',
                                text: 'Tidak dapat menyelesaikan transaksi.'
                            });
                        }
                    });
                }
            });
        });

        //
                // Make sure to wrap your code in a document ready function or place it at the end of the body
        $(document).ready(function() {
            // Define the editDetailPenjualan function in the global scope
            window.editDetailPenjualan = function(idDetailPenjualan) {
                // Show loading indicator
                Swal.fire({
                    title: 'Loading...',
                    text: 'Getting penjualan data',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // AJAX request to get the data using CI4's CSRF protection
                $.ajax({
                    url: '<?= site_url('admin/penjualan/edit') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_detail_penjualan: idDetailPenjualan,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>' // Include CSRF token for CI4
                    },
                    success: function(response) {
                        // Close loading indicator
                        Swal.close();
                        
                        if (response.status === 'success') {
                            // Get the detail data
                            const detail = response.data.detail_penjualan;
                            
                            // Set the current method value in the dropdown
                            $('#edit_id_method').val(detail.id_method);
                            
                            // Store the ID in a hidden input for later use when updating
                            $('#penjualanDetailEditForm').append('<input type="hidden" name="id_detail_penjualan" value="' + idDetailPenjualan + '">');
                            
                            // Show the modal - already handled by data-target attribute
                            // $('#ModalEditPenjualanDetail').modal('show');
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to load data',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Close loading indicator
                        Swal.close();
                        
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to connect to the server. Please try again.',
                        });
                        
                        console.error('AJAX Error:', error);
                    }
                });
            };
            
            // Handle form submission when the update button is clicked
            $('#updatePenjualanDetail').on('click', function() {
                // Get form data
                const idDetailPenjualan = $('input[name="id_detail_penjualan"]').val();
                const idMethod = $('#edit_id_method').val();
                
                // Validate form data
                if (!idMethod) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please select a payment method',
                    });
                    return;
                }
                
                // Show loading indicator
                Swal.fire({
                    title: 'Processing...',
                    text: 'Updating payment method',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // AJAX request to update the data
                $.ajax({
                    url: '<?= site_url('penjualan/updatePenjualanDetail') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id_detail_penjualan: idDetailPenjualan,
                        id_method: idMethod,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>' // Include CSRF token for CI4
                    },
                    success: function(response) {
                        // Close loading indicator
                        Swal.close();
                        
                        if (response.status === 'success') {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Payment method updated successfully',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Hide the modal
                                $('#ModalEditPenjualanDetail').modal('hide');
                                
                                // Refresh the page or update the table
                                location.reload();
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to update payment method',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Close loading indicator
                        Swal.close();
                        
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to connect to the server. Please try again.',
                        });
                        
                        console.error('AJAX Error:', error);
                    }
                });
            });
        });

        $.ajax({
            url: '<?= base_url('admin/penjualan/getCustomer') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Store customers globally
                window.customers = data;

                $('#nama_customer').on('input', function() {
                    var input = $(this).val().toLowerCase();
                    var inputWidth = $(this).outerWidth();
                    var resultsContainer = $('#customer-results');

                    // Create results container if it doesn't exist
                    if (resultsContainer.length === 0) {
                        $(this).after('<div id="customer-results" class="list-group position-absolute"></div>');
                        resultsContainer = $('#customer-results');
                    }

                    // Set width to match input
                    resultsContainer.width(inputWidth);

                    // Clear previous results
                    resultsContainer.empty();

                    // Filter and show results
                    if (input.length >= 2) {
                        var matchingCustomers = window.customers.filter(function(customer) {
                            return customer.text.toLowerCase().includes(input);
                        });

                        // Populate results
                        matchingCustomers.forEach(function(customer) {
                            resultsContainer.append(
                                `<a href="#" class="list-group-item list-group-item-action customer-option" 
                                data-customer="${customer.text}">
                                    ${customer.text}
                                </a>`
                            );
                        });

                        // Show results container
                        resultsContainer.show();
                    } else {
                        // Hide results if input is too short
                        resultsContainer.hide();
                    }
                });

                // Handle customer selection
                $(document).on('click', '.customer-option', function(e) {
                    e.preventDefault();
                    var selectedCustomer = $(this).data('customer');
                    $('#nama_customer').val(selectedCustomer);
                    $('#customer-results').hide();
                });

                // Hide results when clicking outside
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#nama_customer, #customer-results').length) {
                        $('#customer-results').hide();
                    }
                });
            },
            error: function() {
                console.error('Error fetching customer data');
            }
        });

        // Print Receipt Handler
        $('#printReceiptBtn').on('click', function() {
            // Prevent multiple clicks
            $(this).prop('disabled', true);

            console.log('Print Receipt Clicked');
            console.log('Last Transaction Response:', window.lastTransactionResponse);

            // If lastTransactionResponse is not set, try to get it from the previous AJAX response
            if (!window.lastTransactionResponse) {
                // Check if the last AJAX response is stored somewhere
                var lastSaveResponse = sessionStorage.getItem('lastTransactionResponse');
                
                if (lastSaveResponse) {
                    try {
                        window.lastTransactionResponse = JSON.parse(lastSaveResponse);
                        console.log('Retrieved from sessionStorage:', window.lastTransactionResponse);
                    } catch (e) {
                        console.error('Error parsing stored response:', e);
                    }
                }
            }

            if (window.lastTransactionResponse && window.lastTransactionResponse.invoiceId) {
                var invoiceId = window.lastTransactionResponse.invoiceId;
                console.log('Attempting to print invoice:', invoiceId);

                // Close all modals first to prevent recursion
                $('.modal').modal('hide');

                $.ajax({
                    url: '<?= base_url('admin/penjualan/print-invoice/') ?>' + invoiceId,
                    type: 'GET',
                    dataType: 'json',
                    timeout: 10000, // 10 second timeout
                    success: function(response) {
                        console.log('Print Invoice Response:', response);

                        if (response.status === 'success') {
                            function printInvoice(html) {
                                // Create a new window with more specific features
                                var printWindow = window.open('', '_blank', 'width=420,height=600');
                                
                                if (printWindow) {
                                    // Write the HTML content directly
                                    printWindow.document.open();
                                    printWindow.document.write(html);
                                    printWindow.document.close();

                                    printWindow.onload = function() {
                                        setTimeout(() => {
                                            try {
                                                printWindow.focus(); // Ensure window is focused
                                                printWindow.print();
                                                printWindow.close();
                                            } catch (printErr) {
                                                console.error('Print error:', printErr);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Terjadi kesalahan saat mencetak',
                                                    text: 'Silakan coba lagi.'
                                                });
                                            }
                                        }, 300);
                                    };
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Membuka Jendela Cetak',
                                        text: 'Pastikan popup tidak diblokir'
                                    });
                                }
                            }
                            // Directly print the invoice
                            printInvoice(response.invoiceHtml);

                            Swal.fire({
                                icon: 'success',
                                title: 'Transaksi Berhasil!',
                                text: 'Invoice telah dicetak',
                                showConfirmButton: true,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Reset form and clear cart
                                    $('#cartTable tbody').empty();
                                    $('#penjualanForm')[0].reset();
                                    $('#ModalTambahPenjualan').modal('hide');
                                }
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Gagal mengambil invoice'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            status: status,
                            error: error,
                            responseText: xhr.responseText
                        });

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Mengambil Invoice',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengambil data invoice'
                        });
                    },
                    complete: function() {
                        // Re-enable the button
                        $('#printReceiptBtn').prop('disabled', false);
                    }
                });

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Tidak ada transaksi yang ditemukan. Silakan ulangi transaksi.'
                });

                // Re-enable the button
                $(this).prop('disabled', false);
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