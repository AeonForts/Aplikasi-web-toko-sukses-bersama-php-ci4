<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <a href="<?= base_url('admin/summary/export') ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Summary
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Summary Chart</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Tanggal Mulai (Summary Chart)</label>
                            <input type="date" id="summary_chart_start_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Tanggal Akhir (Summary Chart)</label>
                            <input type="date" id="summary_chart_end_date" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button id="summarychartFilterButton" class="btn btn-primary">Filter Summary Chart</button>
                        </div>
                    </div>
                    <div id="summaryChart"></div>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Penjualan Chart</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" id="chart_start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Tanggal Akhir</label>
                            <input type="date" id="chart_end_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Tipe Barang</label>
                            <select id="chart_tipe_barang" class="form-control">
                                <!-- Options will be populated dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="chartFilterButton" class="btn btn-primary">Filter Chart</button>
                        </div>
                    </div>
                    <div id="penjualanChart"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Summary Filter</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Tanggal Mulai</label>
                            <input type="date" id="start_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Tanggal Akhir</label>
                            <input type="date" id="end_date" class="form-control">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button id="filterButton" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cumulative Summary</h3>
                </div>
                <div class="card-body">
                    <table id="cumulativeSummaryTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Total Margine</th>
                                <th>Total Biaya</th>
                                <th>Total Margine Bersih</th>
                                <th>Total Transaksi</th>
                                <th>Total Cash</th>
                                <th>Total Transfer</th>
                                <th>Total Piutang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Grand Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Per Barang Summary</h3>
                </div>
                <div class="card-body">
                    <table id="summaryPerBarangTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal Penjualan</th>
                                <th>Jenis Barang</th>
                                <th>Margine</th>
                                <th>Jumlah Transaksi</th>
                                <th>Jumlah Cash</th>
                                <th>Jumlah Transfer</th>
                                <th>Jumlah Piutang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('pages/admin/summary/modal') ?> <!-- This will include the modal from modal_pengeluaran.php -->

<script>
$(document).ready(function() {
    // Set default dates
    const today = new Date();
    const oneMonthAgo = new Date(today);
    oneMonthAgo.setMonth(today.getMonth() - 1);

    $('#start_date').val(oneMonthAgo.toISOString().split('T')[0]);
    $('#end_date').val(today.toISOString().split('T')[0]);

    const cumulativeTable = $('#cumulativeSummaryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/summary/cumulative-datatables'); ?>',
            type: 'POST',
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.draw = d.draw || 1;
                d.start = d.start || 0;
                d.length = d.length || 10;
            }
        },
        columns: [
            { data: 'tgl_penjualan', title: 'Tanggal' },
            { 
                data: 'total_margine', 
                title: 'Total Margine',
                render: function(data) {
                    return 'Rp ' + data.replace(/[.,]00$/, '');
                }
            },
            { 
                data: 'total_biaya', 
                title: 'Total Biaya',
                render: function(data) {
                    return 'Rp ' + data.replace(/[.,]00$/, '');
                }
            },
            { 
                data: 'total_margine_bersih', 
                title: 'Margine Bersih',
                render: function(data) {
                    return 'Rp ' + data.replace(/[.,]00$/, '');
                }
            },
            { data: 'total_transaksi', title: 'Total Transaksi' },
            { data: 'total_cash', title: 'Total Cash' },
            { data: 'total_transfer', title: 'Total Transfer' },
            { data: 'total_piutang', title: 'Total Piutang' }
        ],
        paging: true,
        pageLength: 10,
        searching: false,
        info: true,
        order: [[0, 'desc']],
        footerCallback: function(row, data, start, end, display) {
            var api = this.api();
            
            if (api.ajax.json() && api.ajax.json().grandTotals) {
                var grandTotals = api.ajax.json().grandTotals;
                
                $(api.column(1).footer()).html('Rp ' + grandTotals.total_margine);
                $(api.column(2).footer()).html('Rp ' + grandTotals.total_biaya);
                $(api.column(3).footer()).html('Rp ' + grandTotals.total_margine_bersih);
                $(api.column(4).footer()).html(grandTotals.total_transaksi);
                $(api.column(5).footer()).html(grandTotals.total_cash);
                $(api.column(6).footer()).html(grandTotals.total_transfer);
                $(api.column(7).footer()).html(grandTotals.total_piutang);
            }
        }
    });

    const perBarangTable = $('#summaryPerBarangTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/summary/per-barang-datatables'); ?>',
            type: 'POST',
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.draw = d.draw || 1;
                d.start = d.start || 0;
                d.length = d.length || 10;
            }
        },
        columns: [
            { data: 'tgl_penjualan', title: 'Tanggal Penjualan' },
            { data: 'jenis_barang', title: 'Jenis Barang' },
            { 
                data: 'margine', 
                title: 'Margine',
                render: function(data) {
                    return 'Rp ' + data.replace(/[.,]00$/, '');
                }
            },
            { data: 'jumlah_transaksi', title: 'Jumlah Transaksi' },
            { data: 'jumlah_cash', title: 'Jumlah Cash' },
            { data: 'jumlah_transfer', title: 'Jumlah Transfer' },
            { data: 'jumlah_piutang', title: 'Jumlah Piutang' }
        ],
        paging: true,
        pageLength: 10,
        searching: false,
        info: true,
        order: [[0, 'desc']]
    });

    // Filter button click event
    $('#filterButton').on('click', function() {
        cumulativeTable.draw();
        perBarangTable.draw();
    });

    // Load Tipe Barang options
    function loadTipeBarangOptions() {
        $.ajax({
            url: '<?= base_url('admin/summary/tipe-barang') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const $select = $('#chart_tipe_barang');
                $select.empty();
                
                response.forEach(function(item) {
                    $select.append(
                        $('<option>', {
                            value: item.id_tipe,
                            text: item.jenis_barang
                        })
                    );
                });

                // Set "Semua Barang" as default selected
                $select.val('all');

                // Trigger initial chart load after options are populated
                loadPenjualanChart();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching Tipe Barang:', error);
            }
        });
    }


    // Set default dates
    const chartToday = new Date();
    const chartOneWeekAgo = new Date(chartToday);
    chartOneWeekAgo.setDate(chartToday.getDate() - 7);

    $('#chart_start_date').val(chartOneWeekAgo.toISOString().split('T')[0]);
    $('#chart_end_date').val(chartToday.toISOString().split('T')[0]);

    // Penjualan Chart Initialization
    let penjualanChart = null;

    // Function to load Penjualan Chart
    function loadPenjualanChart() {
    const startDate = $('#chart_start_date').val();
    const endDate = $('#chart_end_date').val();
    const tipeBarang = $('#chart_tipe_barang').val();

        $.ajax({
            url: '<?= base_url('admin/summary/penjualan-chart-data') ?>',
            method: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate,
                tipe_barang: tipeBarang
            },
            dataType: 'json',
            success: function(response) {
                // Destroy existing chart if it exists
                if (penjualanChart) {
                    penjualanChart.destroy();
                }

                // ApexCharts configuration
                const options = {
                    series: [
                        {
                            name: 'Total Harga Jual',
                            type: 'column',
                            data: response.total_harga_jual
                        },
                        {
                            name: 'Total Untung',
                            type: 'line',
                            data: response.total_untung
                        },
                        {
                            name: 'Total Harga Modal',
                            type: 'column',
                            data: response.total_harga_modal
                        },
                        {
                            name: 'Total Barang Keluar',
                            type: 'line',
                            data: response.total_barang_keluar.map(val => Number(val).toFixed(2))  // Apply decimal limitation here
                        }
                    ],
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: {
                            show: true
                        }
                    },
                    stroke: {
                        width: [0, 4, 0, 4]
                    },
                    title: {
                        text: `Penjualan Overview ${tipeBarang === 'all' ? '(Semua Barang)' : ''}`
                    },
                    dataLabels: {
                        enabled: true,
                        enabledOnSeries: [1, 3]
                    },
                    labels: response.labels,
                    xaxis: {
                        type: 'category',
                        title: {
                            text: 'Tanggal'
                        }
                    },
                    yaxis: [
                        {
                            title: {
                                text: 'Total Harga Jual',
                            },
                            labels: {
                                formatter: function (value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        },
                        {
                            opposite: true,
                            title: {
                                text: 'Total Untung'
                            },
                            labels: {
                                formatter: function (value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    ],
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                                const seriesName = w.globals.seriesNames[seriesIndex];
                                return seriesName + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    legend: {
                        show: true,
                        position: 'bottom'
                    }
                };

                // Create new chart
                penjualanChart = new ApexCharts(document.querySelector("#penjualanChart"), options);
                penjualanChart.render();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching chart data:', error);
            }
        });
    }

    // Initial chart load
    loadTipeBarangOptions();


    // Chart filter button click event
    $('#chartFilterButton').on('click', function() {
        loadPenjualanChart();
    });


    // Summary Chart Initialization
    let summaryChart = null;

    // Set default dates for Summary Chart
    const summaryChartToday = new Date();
    const summaryChartOneWeekAgo = new Date(summaryChartToday);
    summaryChartOneWeekAgo.setDate(summaryChartToday.getDate() - 7);

    $('#summary_chart_start_date').val(summaryChartOneWeekAgo.toISOString().split('T')[0]);
    $('#summary_chart_end_date').val(summaryChartToday.toISOString().split('T')[0]);

    // Function to load Summary Chart
    function loadSummaryChart() {
    const startDate = $('#summary_chart_start_date').val();
    const endDate = $('#summary_chart_end_date').val();

    $.ajax({
        url: '<?= base_url('admin/summary/summary-chart-data') ?>',
        method: 'POST',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(response) {
            // Destroy existing chart if it exists
            if (summaryChart) {
                summaryChart.destroy();
            }

            // ApexCharts configuration
            const options = {
                series: [
                    {
                        name: 'Margine',
                        type: 'column',
                        data: response.total_margine.map(Number)
                    },
                    {
                        name: 'Biaya',
                        type: 'column',
                        data: response.total_biaya.map(Number)
                    },
                    {
                        name: 'Margine Bersih',
                        type: 'line',
                        data: response.total_margine_bersih.map(Number)
                    }
                ],
                chart: {
                    height: 350,
                    type: 'line',
                    stacked: false,
                    toolbar: {
                        show: true
                    }
                },
                stroke: {
                    width: [0, 0, 4]
                },
                plotOptions: {
                    bar: {
                        columnWidth: '50%'
                    }
                },
                xaxis: {
                    categories: response.labels,
                    title: {
                        text: 'Tanggal'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Nilai (Rp)'
                    },
                    labels: {
                        formatter: function (value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                            const seriesName = w.globals.seriesNames[seriesIndex];
                            return seriesName + ': Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom'
                },
                dataLabels: {
                    enabled: false
                }
            };

            // Create new chart
            try {
                summaryChart = new ApexCharts(document.querySelector("#summaryChart"), options);
                summaryChart.render();
            } catch (error) {
                console.error('Chart Rendering Error:', error);
                $('#summaryChart').html(`<p>Chart Rendering Error: ${error.message}</p>`);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching chart data:', {
                status,
                error,
                responseText: xhr.responseText
            });
            $('#summaryChart').html('<p>Error loading chart: ' + error + '</p>');
        }
    });
}

    // Initial chart load
    loadSummaryChart();

    // Chart filter button click event
    $('#summarychartFilterButton').on('click', function() {
        loadSummaryChart();
    });





});

</script>

<?= $this->endSection() ?>