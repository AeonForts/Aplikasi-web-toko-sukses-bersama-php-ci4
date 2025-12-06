<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


/**
 * Homepage
 */
// Set the login view as the main landing page
$routes->get('/', 'AuthController::login'); // Main landing page

// Define other routes
$routes->get('/login', 'AuthController::login');
$routes->post('/auth/dologin', 'AuthController::doLogin');
$routes->get('/logout', 'AuthController::logout');


//  Admin

// User dashboard routes
$routes->get('/admin/dashboard', 'ViewController::admin'); // Admin dashboard
$routes->get('/owner/dashboard', 'ViewController::owner'); // Owner dashboard
$routes->get('/petugas/dashboard', 'ViewController::petugas'); // Petugas dashboard


//Owner





//Petugas
//Petugas
$routes->group('petugas', function($routes) {
    $routes->get('dashboard', 'Petugas\DashboardController::list');
    $routes->post('dashboard/getChartData', 'Petugas\DashboardController::getChartData');
    $routes->get('dashboard/getDailyPieChartData', 'Petugas\DashboardController::getDailyPieChartData');
    $routes->get('dashboard/getTipeBarang', 'Petugas\DashboardController::getTipeBarang');
    $routes->get('dashboard/getDailyBarChartData', 'Petugas\DashboardController::getDailyBarChartData');
    $routes->get('dashboard/getTipeBarangOptions', 'Petugas\DashboardController::getTipeBarangOptions');
    $routes->get('dashboard/checkStandarHargaJualStatus', 'Petugas\DashboardController::checkStandarHargaJualStatus');

    $routes->get('pengeluaran', 'Petugas\PengeluaranController::list');  
    $routes->get('pengeluaran/detail/(:num)', 'Petugas\PengeluaranController::detail/$1');  // Access pengeluaran detail
    $routes->post('pengeluaran/filter', 'Petugas\PengeluaranController::filter');
    $routes->post('pengeluaran/save', 'Petugas\PengeluaranController::save');
    $routes->post('pengeluaran/edit', 'Petugas\PengeluaranController::edit');
    $routes->post('pengeluaran/update', 'Petugas\PengeluaranController::update');
    $routes->post('pengeluaran/delete', 'Petugas\PengeluaranController::delete');
    $routes->add('pengeluaran/datatables', 'Petugas\PengeluaranController::getDatatables');
    $routes->get('pengeluaran/chart-data', 'Petugas\PengeluaranController::getChartData');

    //Penjualan
    $routes->get('penjualan', 'Petugas\PenjualanController::list');  
    $routes->get('penjualan/getTipeBarang', 'Petugas\PenjualanController::getTipeBarang');  
    $routes->get('penjualan/detail/(:num)', 'Petugas\PenjualanController::detail/$1');  
    $routes->post('penjualan/save', 'Petugas\PenjualanController::save'); // For saving the form data
    $routes->add('penjualan/datatables', 'Petugas\PenjualanController::getDatatables');
    $routes->get('penjualan/print-invoice/(:num)', 'Petugas\PenjualanController::printInvoice/$1');
    $routes->get('penjualan/chart-data', 'Petugas\PenjualanController::getChartData');
    $routes->get('penjualan/filtered-chart-data', 'Petugas\PenjualanController::getFilteredChartData');
    $routes->post('penjualan/mark-lunas', 'Petugas\PenjualanController::markLunas');
    $routes->post('penjualan/delete-detail', 'Petugas\PenjualanController::deleteDetailPenjualan');
    $routes->get('penjualan/getTipeBarangDetails/(:num)', 'Petugas\PenjualanController::getTipeBarangDetails/$1');

    $routes->get('barang/list-stock-barang', 'Petugas\BarangController::listStock');
    $routes->get('barang', 'Petugas\BarangController::showList');
    $routes->get('barang/list-barang', 'Petugas\BarangController::showListBarang');
    
    $routes->post('barang/save', 'Petugas\BarangController::save');
    $routes->get('barang/getTipeBarang', 'Petugas\BarangController::getTipeBarang');  
    $routes->post('barang/edit', 'Petugas\BarangController::edit');
    $routes->post('barang/update', 'Petugas\BarangController::update');
    $routes->post('barang/delete', 'Petugas\BarangController::delete');
    $routes->add('barang/datatables', 'Petugas\BarangController::getDatatables');
    $routes->add('barang/get-datatables', 'Petugas\BarangController::getBarangDatatables');
    $routes->add('barang/get-datatables-barang', 'Petugas\BarangController::getBarangListDatatables');
    $routes->add('barang/getBarangDatatable', 'Petugas\BarangController::getBarangDatatable');


    $routes->get('summary', 'Petugas\SummaryController::list');
    $routes->post('summary/datatables', 'Petugas\SummaryController::getDatatables');
    $routes->post('summary/cumulative-datatables', 'Petugas\SummaryController::getCumulativeDatatables');
    $routes->post('summary/per-barang-datatables', 'Petugas\SummaryController::getPerBarangDatatables');

    $routes->get('summary/tipe-barang', 'Petugas\SummaryController::getTipeBarang');
    $routes->post('summary/chart-data', 'Petugas\SummaryController::getChartData');
    
    // In app/Config/Routes.php
    $routes->get('invoice', 'Petugas\InvoiceController::list');
    $routes->post('invoice/datatables', 'Petugas\InvoiceController::getDatatables');
    $routes->get('invoice/detail/(:num)', 'Petugas\InvoiceController::detail/$1');
    $routes->get('invoice/print/(:num)', 'Petugas\InvoiceController::printInvoice/$1');
    $routes->delete('invoice/delete/(:num)', 'Petugas\InvoiceController::delete/$1');

});



// $routes->get('admin', 'ViewController::admin');
$routes->group('admin', function($routes) {
    //Pengeluaran Routes Admin
    $routes->get('dashboard', 'Admin\DashboardController::list');
    $routes->post('dashboard/getChartData', 'Admin\DashboardController::getChartData');
    $routes->get('dashboard/getDailyPieChartData', 'Admin\DashboardController::getDailyPieChartData');
    $routes->get('dashboard/getTipeBarang', 'Admin\DashboardController::getTipeBarang');
    $routes->get('dashboard/getDailyBarChartData', 'Admin\DashboardController::getDailyBarChartData');
    $routes->get('dashboard/getTipeBarangOptions', 'Admin\DashboardController::getTipeBarangOptions');
    $routes->get('dashboard/checkStandarHargaJualStatus', 'Admin\DashboardController::checkStandarHargaJualStatus');
    


    $routes->get('pengeluaran', 'Admin\PengeluaranController::list');  
    $routes->get('pengeluaran/detail/(:num)', 'Admin\PengeluaranController::detail/$1');  // Access pengeluaran detail
    $routes->post('pengeluaran/filter', 'Admin\PengeluaranController::filter');
    $routes->post('pengeluaran/save', 'Admin\PengeluaranController::save');
    $routes->post('pengeluaran/edit', 'Admin\PengeluaranController::edit');
    $routes->post('pengeluaran/update', 'Admin\PengeluaranController::update');
    $routes->post('pengeluaran/delete', 'Admin\PengeluaranController::delete');
    $routes->add('pengeluaran/datatables', 'Admin\PengeluaranController::getDatatables');
    $routes->get('pengeluaran/chart-data', 'Admin\PengeluaranController::getChartData');


    $routes->get('pembelian', 'Admin\PembelianController::list');  
    $routes->get('pembelian/getTipeBarang', 'Admin\PembelianController::getTipeBarang');  
    $routes->get('pembelian/detail/(:num)', 'Admin\PembelianController::detail/$1');  
    $routes->post('pembelian/confirmCash', 'Admin\PembelianController::confirmCash');
    // $routes->add('admin/pembelian/confirm-payment/(:num)', 'PembelianController::confirmPayment/$1', ['as' => 'confirm_payment']);
    $routes->post('pembelian/save', 'Admin\PembelianController::save'); // For saving the form data
    $routes->post('pembelian/delete/(:num)', 'Admin\PembelianController::delete/$1');
    // Edit routes
    $routes->get('pembelian/edit/(:num)', 'Admin\PembelianController::edit/$1');
    $routes->post('pembelian/update', 'Admin\PembelianController::update');
    $routes->post('pembelian/confirmPayment', 'Admin\PembelianController::confirmPayment');
    $routes->get('pembelian/get-tipe-barang', 'Admin\PembelianController::getTipeBarang');
    $routes->get('pembelian/getSupplier', 'Admin\PembelianController::getSupplier');
    $routes->get('pembelian/chart-data', 'Admin\PembelianController::getChartData');
    $routes->post('pembelian/getDatatables', 'Admin\PembelianController::getDatatables');

    //Penjualan
    $routes->get('penjualan', 'Admin\PenjualanController::list');  
    $routes->get('penjualan/getTipeBarang', 'Admin\PenjualanController::getTipeBarang');  
    $routes->get('penjualan/detail/(:num)', 'Admin\PenjualanController::detail/$1');  
    $routes->post('penjualan/save', 'Admin\PenjualanController::save'); // For saving the form data
    $routes->add('penjualan/datatables', 'Admin\PenjualanController::getDatatables');
    $routes->get('penjualan/print-invoice/(:num)', 'Admin\PenjualanController::printInvoice/$1');
    $routes->get('penjualan/chart-data', 'Admin\PenjualanController::getChartData');
    $routes->get('penjualan/filtered-chart-data', 'Admin\PenjualanController::getFilteredChartData');
    $routes->post('penjualan/mark-lunas', 'Admin\PenjualanController::markLunas');
    $routes->get('penjualan/getCustomer', 'Admin\PenjualanController::getCustomer');
    $routes->post('penjualan/delete-detail', 'Admin\PenjualanController::deleteDetailPenjualan');
    $routes->get('penjualan/getTipeBarangDetails/(:num)', 'Admin\PenjualanController::getTipeBarangDetails/$1');
    $routes->add('penjualan/piutang', 'Admin\PenjualanController::piutangList');
    $routes->post('penjualan/edit', 'Admin\PenjualanController::editDetailPenjualan');
    $routes->post('penjualan/update', 'Admin\PenjualanController::updateDetailPenjualan');


    $routes->get('barang/list-stock-barang', 'Admin\BarangController::listStock');
    $routes->get('barang', 'Admin\BarangController::showList');
    $routes->get('barang/list-barang', 'Admin\BarangController::showListBarang');
    
    $routes->post('barang/save', 'Admin\BarangController::save');
    $routes->get('barang/getTipeBarang', 'Admin\BarangController::getTipeBarang');  
    $routes->post('barang/edit', 'Admin\BarangController::edit');
    $routes->post('barang/update', 'Admin\BarangController::update');
    $routes->post('barang/delete', 'Admin\BarangController::delete');
    $routes->add('barang/datatables', 'Admin\BarangController::getDatatables');
    $routes->add('barang/get-datatables', 'Admin\BarangController::getBarangDatatables');
    $routes->add('barang/get-datatables-barang', 'Admin\BarangController::getBarangListDatatables');
    $routes->add('barang/getBarangDatatable', 'Admin\BarangController::getBarangDatatable');


    $routes->get('summary', 'Admin\SummaryController::list');
    $routes->post('summary/datatables', 'Admin\SummaryController::getDatatables');
    $routes->post('summary/cumulative-datatables', 'Admin\SummaryController::getCumulativeDatatables');
    $routes->post('summary/per-barang-datatables', 'Admin\SummaryController::getPerBarangDatatables');
    $routes->post('summary/penjualan-chart-data', 'Admin\SummaryController::getPenjualanChartData');

    $routes->post('summary/summary-chart-data', 'Admin\SummaryController::getSummaryChartData');
    $routes->get('summary/tipe-barang', 'Admin\SummaryController::getTipeBarangOptions');
    $routes->get('summary/tipe-barang', 'Admin\SummaryController::getTipeBarang');
    $routes->post('summary/chart-data', 'Admin\SummaryController::getChartData');
    $routes->get('summary/export', 'Admin\SummaryController::exportAllReports');
    
    // In app/Config/Routes.php
    $routes->get('invoice', 'Admin\InvoiceController::list');
    $routes->post('invoice/datatables', 'Admin\InvoiceController::getDatatables');
    $routes->get('invoice/detail/(:num)', 'Admin\InvoiceController::detail/$1');
    $routes->get('invoice/print/(:num)', 'Admin\InvoiceController::printInvoice/$1');
    $routes->delete('invoice/delete/(:num)', 'Admin\InvoiceController::delete/$1');


    $routes->get('users', 'Admin\UserController::list');
    $routes->post('users/save', 'Admin\UserController::save');
    $routes->post('users/edit', 'Admin\UserController::edit');
    $routes->post('users/update', 'Admin\UserController::update');
    $routes->post('users/delete', 'Admin\UserController::delete');

    // In Config/Routes.php
    $routes->get('customer', 'Admin\CustomerController::list');
    $routes->get('customer/get/(:num)', 'Admin\CustomerController::get/$1');
    $routes->post('customer/datatables', 'Admin\CustomerController::datatables');
    $routes->post('customer/save', 'Admin\CustomerController::save');
    $routes->post('customer/edit', 'Admin\CustomerController::edit');
    $routes->post('customer/update', 'Admin\CustomerController::update');
    $routes->post('customer/delete/(:num)', 'Admin\CustomerController::delete/$1');


    $routes->get('supplier', 'Admin\SupplierController::list');
    $routes->post('supplier/datatables', 'Admin\SupplierController::datatables');
    $routes->get('supplier/get/(:num)', 'Admin\SupplierController::get/$1');
    $routes->post('supplier/save', 'Admin\SupplierController::save');
    $routes->post('supplier/edit', 'Admin\SupplierController::edit');
    $routes->post('supplier/update', 'Admin\SupplierController::update');
    $routes->post('supplier/delete/(:num)', 'Admin\SupplierController::delete/$1');

    $routes->get('bulek', 'Admin\BulekController::list');
    $routes->post('bulek/datatables', 'Admin\BulekController::datatables');
    $routes->post('bulek/datatableDetail', 'Admin\BulekController::datatableDetail');
    $routes->post('bulek/datatableWithBiaya', 'Admin\BulekController::datatableWithBiaya');
    $routes->get('bulek/getTipeBarang', 'Admin\BulekController::getTipeBarang');  
    $routes->post('bulek/setor', 'Admin\BulekController::setor');
    $routes->get('bulek/edit/(:num)', 'Admin\BulekController::edit/$1');
    $routes->post('bulek/update', 'Admin\BulekController::update');
    $routes->post('bulek/delete/(:num)', 'Admin\BulekController::delete/$1');
});


// $routes->get('admin', 'PengeluaranController::admin')
// $routes->get('home/index', 'Home::index');
// $routes->get('home/stock_telur', 'Home::stock_telur');
// $routes->get('home/summary', 'Home::summary');
// $routes->get('home/pengeluaran', 'Home::pengeluaran');


