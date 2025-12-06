<!-- Add/Insert pengeluaran modal -->
<div class="modal fade" id="ModalTambahPengeluaran" tabindex="-1" role="dialog" aria-labelledby="ModalTambahPengeluaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalTambahPengeluaranLabel">Tambah Pengeluaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="pengeluaranTambahForm">
                    <div class="form-group">
                        <label for="tgl_pengeluaran">Tanggal Pengeluaran</label>
                        <input type="date" name="tgl_pengeluaran" class="form-control" id="tgl_pengeluaran" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label for="jumlah_biaya">Biaya / Pengeluaran</label>
                        <input type="text" name="jumlah_biaya" class="form-control" id="jumlah_biaya" required>
                    </div>
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
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

<!-- Edit pengeluaran modal -->
<!-- Edit pengeluaran modal -->
<div class="modal fade" id="ModalEditPengeluaran" tabindex="-1" role="dialog" aria-labelledby="ModalEditPengeluaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalEditPengeluaranLabel">Edit Pengeluaran</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span>&times;</span> <!-- Bootstrap 5 no longer requires an <i> tag -->
                </button>
            </div>
            <div class="modal-body">
                <form id="pengeluaranEditForm">
                    <input type="hidden" name="id_detail_pengeluaran" id="edit_id_detail_pengeluaran">
                    <div class="form-group">
                        <label for="edit_jumlah_biaya">Biaya / Pengeluaran</label>
                        <input type="text" name="jumlah_biaya" class="form-control" id="edit_jumlah_biaya" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_keterangan">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" id="edit_keterangan" required>
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
<div class="modal fade" id="ModalDeletePengeluaran" tabindex="-1" role="dialog" aria-labelledby="ModalDeletePengeluaranLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDeletePengeluaranLabel">Delete Confirmation</h5>
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

<!-- Chart Modal -->
<div class="modal fade" id="ChartModal" tabindex="-1" role="dialog" aria-labelledby="ChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ChartModalLabel">Grafik Pengeluaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center mb-3">
                    <div class="btn-group" role="group" aria-label="Chart Filters">
                        <button id="monthlyFilter" class="btn btn-primary">Bulanan</button>
                        <button id="yearlyFilter" class="btn btn-secondary">Tahunan</button>
                    </div>
                </div>
                <canvas id="pengeluaranChart" style="width: 100%; height: 300px;"></canvas>
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

    $('#pengeluaranTambahForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('petugas/pengeluaran/save'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalTambahPengeluaran').modal('hide');
                $('#pengeluaranTambahForm')[0].reset();
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pengeluaran berhasil ditambahkan',
                    },
                    {
                        icon: 'success',
                        title: 'Pengeluaran berhasil ditambahkan'
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
            url: '<?= base_url('petugas/pengeluaran/edit'); ?>',
            type: 'POST',
            data: { id_detail_pengeluaran: id },
            success: function(response) {
                $('#edit_id_detail_pengeluaran').val(response.id_detail_pengeluaran);
                $('#edit_jumlah_biaya').val(response.jumlah_biaya);
                $('#edit_keterangan').val(response.keterangan);
                $('#ModalEditPengeluaran').modal('show'); // Show the modal
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
    $('#pengeluaranEditForm').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url('petugas/pengeluaran/update'); ?>',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#ModalEditPengeluaran').modal('hide');
                $('#pengeluaranEditForm')[0].reset();
                showAlertThenReload(
                    {
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pengeluaran berhasil diperbarui',
                    },
                    {
                        icon: 'success',
                        title: 'Pengeluaran berhasil diperbarui'
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
                url: '<?= base_url('petugas/pengeluaran/delete'); ?>',
                type: 'POST',
                data: { id_detail_pengeluaran: id },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '<?= base_url('petugas/pengeluaran'); ?>';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "Error: " + error
                    });
                }
            });
        }
    });
});




});

// Global variables
let pengeluaranChart;
const months = [
    'Januari', 'Februari', 'Maret', 'April', 
    'Mei', 'Juni', 'Juli', 'Agustus', 
    'September', 'Oktober', 'November', 'Desember'
];

let currentFilter = 'yearly'; // Default to yearly

function fetchChartData() {
    $.ajax({
        url: '<?= base_url('petugas/pengeluaran/chart-data'); ?>',
        method: 'GET',
        data: { filter: currentFilter },
        success: function(response) {
            const labels = [];
            const data = [];

            if (currentFilter === 'yearly') {
                // Yearly (monthly breakdown) data processing
                response.forEach(item => {
                    labels.push(months[item.month - 1]);
                    data.push(item.total);
                });
            } else {
                // Monthly (weekly) data processing
                response.forEach(item => {
                    labels.push(`Minggu ${item.week}`);
                    data.push(item.total);
                });
            }

            const ctx = document.getElementById('pengeluaranChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (pengeluaranChart) {
                pengeluaranChart.destroy();
            }

            // Create new chart
            pengeluaranChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: currentFilter === 'yearly' 
                            ? 'Total Pengeluaran per Bulan (Rp)' 
                            : 'Total Pengeluaran per Minggu (Rp)',
                        data: data,
                        backgroundColor: currentFilter === 'yearly' 
                            ? 'rgba(54, 162, 235, 0.6)' // Blue for yearly
                            : 'rgba(75, 192, 192, 0.6)', // Teal for monthly
                        borderColor: currentFilter === 'yearly' 
                            ? 'rgba(54, 162, 235, 1)' 
                            : 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: currentFilter === 'yearly' 
                                ? `Grafik Pengeluaran Bulanan ${new Date().getFullYear()}` 
                                : `Grafik Pengeluaran Mingguan ${months[new Date().getMonth()]}`
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Biaya (Rp)'
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

            // Update filter button styles
            updateFilterButtonStyles();
        },
        error: function(xhr, status, error) {
            console.error("Error fetching chart data:", error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: 'Tidak dapat mengambil data grafik. Silakan coba lagi.'
            });
        }
    });
}

function updateFilterButtonStyles() {
    if (currentFilter === 'yearly') {
        $('#monthlyFilter').removeClass('btn-primary').addClass('btn-secondary');
        $('#yearlyFilter').removeClass('btn-secondary').addClass('btn-primary');
    } else {
        $('#yearlyFilter').removeClass('btn-primary').addClass('btn-secondary');
        $('#monthlyFilter').removeClass('btn-secondary').addClass('btn-primary');
    }
}

// Document ready function
$(document).ready(function() {
    // Initial chart load
    fetchChartData();

    // Filter buttons event listeners
    $('#monthlyFilter').on('click', function() {
        currentFilter = 'monthly';
        fetchChartData();
    });

    $('#yearlyFilter').on('click', function() {
        currentFilter = 'yearly';
        fetchChartData();
    });
});

$(document).ready(function() {
    // Initialize Cleave for jumlah_biaya
    new Cleave('#jumlah_biaya', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });

    // Similar for edit modal
    new Cleave('#edit_jumlah_biaya', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.'
    });
});
</script>