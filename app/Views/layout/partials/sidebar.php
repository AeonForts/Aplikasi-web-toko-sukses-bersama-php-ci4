<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= base_url('/') ?>">Sukses Bersama</a>
                </div>
                <div class="form-check form-switch fs-6">
                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                    <label class="form-check-label"></label>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">



                <?php
                // Get the user's role (peran) from the session
                $userRole = session()->get('peran');

                // Display menu based on user role
                if ($userRole === 'Admin') { ?>
                    <!-- Admin specific menu items -->
                        <!-- Common Menu for All Users -->
                    <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-link">
                        <i class="bi bi-grid-fill"></i>
                        <span>Home</span>
                    </a>
                    <li class="sidebar-item">
                        <a href="<?= base_url('admin/summary') ?>" class="sidebar-link">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                            <span>Admin Summary</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="<?= base_url('admin/pembelian') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Pembelian Barang</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?= base_url('admin/penjualan') ?>" class="sidebar-link">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                            <span>Penjualan Barang</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?= base_url('admin/pengeluaran') ?>" class="sidebar-link">
                            <i class="bi bi-cash"></i>
                            <span>Pengeluaran / Biaya Harian</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?= base_url('admin/invoice') ?>" class="sidebar-link">
                            <i class="bi bi-cash"></i>
                            <span>Invoice</span>
                        </a>
                    </li>
                    <!-- <li class="sidebar-item">
                        <a href="<?= base_url('admin/users') ?>" class="sidebar-link">
                            <i class="bi bi-person"></i>
                            <span>User List</span>
                        </a>
                    </li> -->

                    <li class="sidebar-item has-sub">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-person"></i>
                            <span>User</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item">
                                <a href="<?= base_url('admin/customer') ?>" class="submenu-link">
                                    <!-- <i class="bi bi-cash"></i> -->
                                    <span>Customer</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="<?= base_url('admin/supplier') ?>" class="submenu-link">
                                    <!-- <i class="bi bi-cash"></i> -->
                                    <span>Supplier</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="<?= base_url('admin/users') ?>" class="submenu-link">
                                    <!-- <i class="bi bi-cash"></i> -->
                                    <span>Users</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item has-sub">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Barang</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item">
                                <a href="<?= base_url('admin/barang') ?>" class="submenu-link">
                                    <i class="bi bi-cash"></i>
                                    <span>Unit Barang</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="<?= base_url('admin/barang/list-barang') ?>" class="submenu-link">
                                    <i class="bi bi-cash"></i>
                                    <span>Jenis Barang</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="<?= base_url('admin/barang/list-stock-barang') ?>" class="submenu-link">
                                    <i class="bi bi-cash"></i>
                                    <span>Stock Barang</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php } elseif ($userRole === 'Owner') { ?>
                    <!-- Owner specific menu items -->
                    <li class="sidebar-item">
                        <a href="<?= base_url('owner/stock_telur') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Owner Stock Telur</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?= base_url('owner/summary') ?>" class="sidebar-link">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                            <span>Owner Summary</span>
                        </a>
                    </li>
                <?php } elseif ($userRole === 'Petugas') { ?>
                    <!-- Petugas specific menu items -->
                   <!-- Petugas specific menu items -->
                   <a href="<?= base_url('petugas/dashboard') ?>" class="sidebar-link">
                    <i class="bi bi-grid-fill"></i>
                    <span>Home</span>
                    </a>
                    <!-- <li class="sidebar-item">
                        <a href="<?= base_url('petugas/summary') ?>" class="sidebar-link">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                            <span>petugas Summary</span>
                        </a>
                    </li> -->

                    <!-- <li class="sidebar-item">
                        <a href="<?= base_url('petugas/pembelian') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Pembelian Barang</span>
                        </a>
                    </li> -->
                    <li class="sidebar-item">
                        <a href="<?= base_url('petugas/penjualan') ?>" class="sidebar-link">
                            <i class="bi bi-file-earmark-medical-fill"></i>
                            <span>Penjualan Barang</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?= base_url('petugas/pengeluaran') ?>" class="sidebar-link">
                            <i class="bi bi-cash"></i>
                            <span>Pengeluaran / Biaya Harian</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="<?= base_url('petugas/invoice') ?>" class="sidebar-link">
                            <i class="bi bi-cash"></i>
                            <span>Invoice</span>
                        </a>
                    </li>
                    <!-- <li class="sidebar-item">
                        <a href="<?= base_url('petugas/users') ?>" class="sidebar-link">
                            <i class="bi bi-person"></i>
                            <span>User List</span>
                        </a>
                    </li> -->
                    <li class="sidebar-item has-sub">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Barang</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item">
                                <a href="<?= base_url('petugas/barang') ?>" class="submenu-link">
                                    <i class="bi bi-cash"></i>
                                    <span>Unit Barang</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="<?= base_url('petugas/barang/list-barang') ?>" class="submenu-link">
                                    <i class="bi bi-cash"></i>
                                    <span>Jenis Barang</span>
                                </a>
                            </li>
                            <li class="submenu-item">
                                <a href="<?= base_url('petugas/barang/list-stock-barang') ?>" class="submenu-link">
                                    <i class="bi bi-cash"></i>
                                    <span>Stock Barang</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

            </ul>
        </div>
    </div>
</div>
