<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    
    <div class="row">
    <div id="standar-harga-jual-status" class="alert" style="display:none;"></div>

        <!-- Left Side: Line Chart with Filters -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Financial Summary</h3>
                    <div class="card-tools">
                        <select id="jenisBarangFilter" class="form-control">
                            <option value="">All Types</option>
                            <?php if (!empty($jenisBarangOptions)): ?>
                                <?php foreach($jenisBarangOptions as $jenis): ?>
                                    <option value="<?= $jenis ?>"><?= $jenis ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <select id="chartTypeFilter" class="form-control">
                            <option value="margine_bersih">Net Margin</option>
                            <option value="margine">Margin</option>
                            <option value="biaya">Expenses</option>
                        </select>
                        <select id="summaryPeriodFilter" class="form-control">
                            <option value="yearly">Yearly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="financialLineChart" width="800" height="400"></canvas>
                </div>
            </div>
            <!-- Add this in the container-fluid div -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daily Sales</h3>
                <div class="card-tools">
                    <select id="tipeBarangFilter" class="form-control">
                        <option value="">All Types</option>
                    </select>
                    <input type="date" id="startDateFilter" class="form-control">
                    <input type="date" id="endDateFilter" class="form-control">
                </div>
            </div>
            <div class="card-body">
                <canvas id="dailyBarChart" width="800" height="400"></canvas>
            </div>
        </div>
    </div>
</div>
        </div>

        

        <!-- Right Side: Daily Pie Chart -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today's Summary</h3>
                </div>
                <div class="card-body">
                    <canvas id="dailyPieChart" width="400" height="400"></canvas>
                    <div id="todaySummaryDetails" class="mt-3">
                        <p>Cash: <span id="cashAmount">0</span></p>
                        <p>Transfer: <span id="transferAmount">0</span></p>
                        <p>Piutang: <span id="piutangAmount">0</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fetch and render the financial line chart
        function fetchFinancialChartData() {
            const jenisBarang = $('#jenisBarangFilter').val();
            const chartType = $('#chartTypeFilter').val();
            const summaryPeriod = $('#summaryPeriodFilter').val();

            $.post('<?= base_url('admin/dashboard/getChartData') ?>', {
                jenis_barang: jenisBarang,
                chart_type: chartType,
                summary_period: summaryPeriod }, function(data) {
                const ctx = document.getElementById('financialLineChart').getContext('2d');
                
                // Destroy existing chart if it exists
                if (window.financialLineChart instanceof Chart) {
                    window.financialLineChart.destroy();
                }

                window.financialLineChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Financial Summary',
                            data: data.values,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }, 'json');
        }

        // Fetch and render the daily pie chart
        function fetchDailyPieChartData() {
            $.get('<?= base_url('admin/dashboard/getDailyPieChartData') ?>', function(data) {
                const ctx = document.getElementById('dailyPieChart').getContext('2d');
                
                // Destroy existing chart if it exists
                if (window.dailyPieChart instanceof Chart) {
                    window.dailyPieChart.destroy();
                }

                window.dailyPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.values,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                        }]
                    },
                    options: {
                        responsive: true
                    }
                });

                // Update today's summary details
                $('#cashAmount').text(data.values[0]);
                $('#transferAmount').text(data.values[1]);
                $('#piutangAmount').text(data.values[2]);
            }, 'json');
        }

        // Event listeners for filters
        $('#jenisBarangFilter, #chartTypeFilter, #summaryPeriodFilter').change(function() {
            fetchFinancialChartData();
        });

        // Initial data fetch
        fetchFinancialChartData();
        fetchDailyPieChartData();
    });


        $(document).ready(function() {
        // Populate tipe barang options
        function loadTipeBarangOptions() {
            $.get('<?= base_url('admin/dashboard/getTipeBarangOptions') ?>', function(data) {
                data.forEach(function(item) {
                    $('#tipeBarangFilter').append(
                        `<option value="${item.id_tipe}">${item.id_tipe}</option>`
                    );
                });
            }, 'json');
        }

        // Fetch and render the daily bar chart
        function fetchDailyBarChartData() {
            const idTipe = $('#tipeBarangFilter').val();
            const startDate = $('#startDateFilter').val();
            const endDate = $('#endDateFilter').val();

            $.get('<?= base_url('admin/dashboard/getDailyBarChartData') ?>', {
                id_tipe: idTipe,
                start_date: startDate,
                end_date: endDate
            }, function(data) {
                const ctx = document.getElementById('dailyBarChart').getContext('2d');
                
                // Destroy existing chart if it exists
                if (window.dailyBarChart instanceof Chart) {
                    window.dailyBarChart.destroy();
                }

                window.dailyBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Quantity Sold',
                                data: data.quantities,
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Total Revenue',
                                data: data.revenues,
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }, 'json');
        }

        // Event listeners for filters
        $('#tipeBarangFilter, #startDateFilter, #endDateFilter').change(function() {
            fetchDailyBarChartData();
        });

        // Initial data fetch
        loadTipeBarangOptions();
        
        // Set default date range
        const today = new Date();
        const sevenDaysAgo = new Date(today);
        sevenDaysAgo.setDate(today.getDate() - 7);
        
        $('#startDateFilter').val(sevenDaysAgo.toISOString().split('T')[0]);
        $('#endDateFilter').val(today.toISOString().split('T')[0]);
        
        fetchDailyBarChartData();
    });

    $(document).ready(function() {
        // Add this to your existing script
        function checkStandarHargaJualStatus() {
            $.get('<?= base_url('admin/dashboard/checkStandarHargaJualStatus') ?>', function(data) {
                const statusElement = $('#standar-harga-jual-status');
                
                if (data.is_updated) {
                    statusElement.removeClass('alert-warning alert-danger').addClass('alert-success');
                    statusElement.html('✅ Harga Jual is updated today (' + data.last_update_date + ')');
                } else {
                    statusElement.removeClass('alert-success alert-danger').addClass('alert-warning');
                    statusElement.html('⚠️ Harga Jual was last updated on ' + data.last_update_date + 
                                       ' (' + data.days_since_update + ' days ago)');
                }
                
                statusElement.show();
            });
        }

        // Call the function when the page loads
        checkStandarHargaJualStatus();
    });
</script>

<?= $this->endSection() ?>


