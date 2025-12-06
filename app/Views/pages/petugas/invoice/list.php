<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Riwayat Invoice</h1>
        </div>
        <div class="card-content mb-4 px-4">


            <!-- Date filter inputs -->
            <div class="row align-items-end mb-3">
                <div class="col-md-4">
                    <label for="startDate">Tanggal Mulai:</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="endDate">Tanggal Selesai:</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-4">
                    <button id="filterButton" class="btn btn-secondary w-100">Filter</button>
                </div>
            </div>

            <!-- Table -->
            <div class='table-responsive'>
                <table class="table table-bordered table-striped table-hover text-center" id="invoiceTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Customer</th>
                            <th>Tanggal Invoice</th>
                            <th>Total Keseluruhan</th>
                            <th>Jumlah Bayar</th>
                            <th>Kembalian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#invoiceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('petugas/invoice/datatables'); ?>',
            type: 'POST',
            data: function(d) {
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
            }
        },
        columns: [
            { 
                data: null, 
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'customer_name' },
            { data: 'invoice_date' },
            { 
                data: 'total_amount',
                render: function(data) {
                    return 'Rp. ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { 
                data: 'payment_amount',
                render: function(data) {
                    return 'Rp. ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { 
                data: 'change_amount',
                render: function(data) {
                    return 'Rp. ' + Number(data).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { 
                data: 'id_invoice', 
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <a href="<?= base_url('petugas/invoice/detail/') ?>${data}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> Detail
                            </a>
                            <button onclick="printInvoice(${data})" class="btn btn-success btn-sm">
                                <i class="fa fa-print"></i> Print
                            </button>                         
                        </div>

                    `;
                },
                orderable: false 
            }
        ],
        order: [[2, 'desc']] // Order by date descending
    });

    // Filter button click event
    $('#filterButton').on('click', function() {
        table.draw();
    });


    
});

function printInvoice(invoiceId) {
    $.ajax({
        url: '<?= base_url('petugas/invoice/print/') ?>' + invoiceId, // Corrected route
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                let printWindow = window.open('', '_blank', 'width=420,height=600');
                
                printWindow.document.open();
                printWindow.document.write(response.invoiceHtml);
                printWindow.document.close();

                printWindow.onload = function () {
                    setTimeout(() => {
                        printWindow.print();
                        printWindow.close();
                    }, 300);
                };
            }
        }
    });
}


function deleteInvoice(invoiceId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the invoice and all its associated items!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('petugas/invoice/delete/') ?>' + invoiceId,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    Swal.fire(
                        'Deleted!', 
                        response.message, 
                        'success'
                    );
                    // Refresh datatable or redirect
                    $('#invoiceTable').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Error!',
                        xhr.responseJSON.message || 'Something went wrong',
                        'error'
                    );
                }
            });
        }
    });
}
</script>

<?= $this->endSection() ?>