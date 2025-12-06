<!-- Modal for Date Selection -->
<div class="modal fade" id="exportDateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Tanggal Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Tanggal Mulai</label>
                        <input type="date" id="startDate" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Tanggal Selesai</label>
                        <input type="date" id="endDate" class="form-control" required>
                    </div>
                </div>
                <hr>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Tanggal Mulai Meity</label>
                        <input type="date" id="startDateMeity" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Tanggal Selesai Meity</label>
                        <input type="date" id="endDateMeity" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmExportBtn">Export</button>
            </div>
        </div>
    </div>
</div>

<script>
function showExportDateModal(event) {
    event.preventDefault();
    
    // Open the modal
    const exportModal = new bootstrap.Modal(document.getElementById('exportDateModal'));
    exportModal.show();

    // Handle export confirmation
    document.getElementById('confirmExportBtn').onclick = function() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const startDateMeity = document.getElementById('startDateMeity').value;
        const endDateMeity = document.getElementById('endDateMeity').value;

        // Validate dates
        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Harap pilih kedua tanggal!'
            });
            return;
        }

        // Construct export URL with date parameters
        const baseUrl = '<?= base_url('admin/summary/export') ?>';
        const exportUrl = `${baseUrl}?start_date=${startDate}&end_date=${endDate}&start_date_meity=${startDateMeity}&end_date_meity=${endDateMeity}`;

        // Close modal
        exportModal.hide();

        // Confirmation dialog
        Swal.fire({
            title: 'Export Laporan',
            text: `Ekspor laporan dari ${startDate} sampai ${endDate}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ekspor',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                const overlay = document.getElementById('overlay');
                const spinner = document.getElementById('spinner');
                
                overlay.style.display = 'block';
                spinner.style.display = 'block';

                // Trigger download
                window.location.href = exportUrl;

                // Hide loading and show success
                setTimeout(() => {
                    overlay.style.display = 'none';
                    spinner.style.display = 'none';

                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Laporan telah diekspor',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }, 2000);
            }
        });
    };
}

// Attach the event listener to your export button
document.getElementById('exportButton').addEventListener('click', showExportDateModal);
</script>