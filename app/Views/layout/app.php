<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Toko Sukses Bersama</title>

    <!-- CSS files -->
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app-dark.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/iconly.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/extensions/select2/dist/css/select2.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>" />
    <link href="https://printjs-4de6.kxcdn.com/print.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>" />
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/select2/dist/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') ?>"></script>
    <script src="<?= base_url('assets/static/js/pages/datatables.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/chart.js/chart.umd.js') ?>" type="module"></script>
    <script>
    console.log('Chart.js loaded:', typeof Chart !== 'undefined');
</script>

</head>
<body>
    <!-- Overlay for darkening the screen -->
    <div id="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9998;"></div>
    
    <!-- Spinner HTML -->
    <div id="spinner" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div id="app">
        <?= $this->include('layout/partials/sidebar') ?>  <!-- Sidebar inclusion -->

        <div id="main">
            <?= $this->include('layout/partials/navbar') ?>  <!-- Navbar inclusion -->

            <div class="page-content">
                <section class="row">
                    <?= $this->renderSection('content') ?> <!-- Dynamic content based on role -->
                </section>
            </div>

            <footer class="footer fixed">
                <div class="footer clearfix mb-0 text-muted">
                    <div class="float-start">
                        <p>2024 &copy; Unbin</p>
                    </div>
                    <div class="float-end">
                        <p>Crafted with <span class="text-danger"><i class="bi bi-heart-fill icon-mid"></i></span> by <a href="https://saugi.me">Saugi</a></p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
    <!-- JS files -->
    <script src="<?= base_url('assets/extensions/select2/dist/js/select2.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/static/js/components/dark.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/cleave.js-1.6.0/dist/cleave.min.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/apexcharts/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('assets/static/js/pages/dashboard.js') ?>"></script>
    <script src="<?= base_url('assets/static/js/initTheme.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/sweetalert2/sweetalert2.min.js') ?>"></script>
    <script>
        // Show spinner and overlay on page load
        window.addEventListener('beforeunload', function() {
            document.getElementById('spinner').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        });
    </script>
</body>
</html>
