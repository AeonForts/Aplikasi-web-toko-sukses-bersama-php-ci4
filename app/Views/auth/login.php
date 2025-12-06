<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/app-dark.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/compiled/css/iconly.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/fontawesome/css/all.min.css') ?>" />
    <script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
    <link rel="stylesheet" href="admin/assets/css/bootstrap.css">
    <style>
        body {
            height: 100vh; Make the body take up the full height
            margin: 0; /* Remove default margin */
            overflow-y:hidden;
        }

        .container {
            height: 100%; /* Make the container take up the full height */
            display: flex;
            justify-content: center; /* Horizontal Centering */
            align-items: center; /* Vertical Centering */
            flex-direction: column; /* Make the container a column */
            margin-top: 50px; 
        }

        .panel {
            max-width: 300px; /* Set a max width for the panel */
            margin: 20px; /* Add some margin around the panel */
            border: 5px solid #bbb; /* Add a border around the panel */
            border-radius: 10px; /* Add a rounded corner to the border */
            padding: 10px; /* Add some padding inside the panel */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add a subtle shadow effect */
        }
    </style>
</head>
<body>
<div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><strong>Login</strong></h3>
            </div>
            <div class="panel-body">
                <form action="<?= base_url('auth/dologin') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-primary" type="submit" name="login">Login</button>
                </form>  
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger mt-2">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/static/js/components/dark.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js') ?>"></script>
    <script src="<?= base_url('assets/compiled/js/app.js') ?>"></script>
    <script src="<?= base_url('assets/extensions/apexcharts/apexcharts.min.js') ?>"></script>
    <script src="<?= base_url('assets/static/js/pages/dashboard.js') ?>"></script>
    <script src="<?= base_url('assets/static/js/initTheme.js') ?>"></script>
</body>
</html>
