<?php

namespace App\Controllers\Admin;

use App\Models\ViewSummaryModel;
use App\Models\{ViewSummaryMargineBarangModel,ViewMeityModel,ViewDetailPenjualanModel,PenjualanModel,ViewStockModel,BarangModel};
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SummaryController extends BaseController
{
    protected $viewSummaryModel;
    protected $viewSummaryMargineBarangModel;
    protected $barangModel;
    protected $viewMeityModel;
    protected $viewDetailPenjualanModel;
    protected $penjualanModel;
    protected $viewStockModel;


    public function __construct()
    {
        $this->viewSummaryModel = new ViewSummaryModel();
        $this->viewSummaryMargineBarangModel = new ViewSummaryMargineBarangModel();
        $this->barangModel = new BarangModel();
        $this->viewMeityModel = new ViewMeityModel();
        $this->viewDetailPenjualanModel = new ViewDetailPenjualanModel();
        $this->penjualanModel = new PenjualanModel();
        $this->viewStockModel = new ViewStockModel();

    }

    public function list()
    {
        return view('pages/admin/summary/list');
    }

    public function getSummaryChartData()
    {
        $request = service('request');
    
        // Get date range from request
        $startDate = $request->getPost('start_date') ?? date('Y-m-d', strtotime('-1 week'));
        $endDate = $request->getPost('end_date') ?? date('Y-m-d');
    
        // Base query without additional summing
        $results = $this->viewSummaryModel
            ->where('tgl_penjualan >=', $startDate)
            ->where('tgl_penjualan <=', $endDate)
            ->orderBy('tgl_penjualan', 'ASC')
            ->findAll();
    
        // Prepare data for chart
        $labels = [];
        $totalMargine = [];
        $totalBiaya = [];
        $totalMargineBersih = [];
        $totalJumlahTransaksi = [];
    
        foreach ($results as $row) {
            $labels[] = date('d-m-Y', strtotime($row['tgl_penjualan']));
            $totalMargine[] = floatval($row['margine']);
            $totalBiaya[] = floatval($row['biaya']);
            $totalMargineBersih[] = floatval($row['margine_bersih']);
            $totalJumlahTransaksi[] = floatval($row['jumlah_transaksi']);
        }
    
        $response = [
            'labels' => $labels,
            'total_margine' => $totalMargine,
            'total_biaya' => $totalBiaya,
            'total_margine_bersih' => $totalMargineBersih,
            'total_jumlah_transaksi' => $totalJumlahTransaksi
        ];
    
        return $this->response->setJSON($response);
    }

    public function getPenjualanChartData()
    {
        $request = service('request');
    
        // Get date range from request
        $startDate = $request->getPost('start_date') ?? date('Y-m-d', strtotime('-1 week'));
        $endDate = $request->getPost('end_date') ?? date('Y-m-d');
        $tipeBarang = $request->getPost('tipe_barang') ?? 'all';
    
        // Base query with more detailed selection
        $query = $this->penjualanModel
            ->select('
                tgl_penjualan, 
                id_tipe, 
                SUM(total_harga_jual) as total_harga_jual, 
                SUM(total_harga_modal) as total_harga_modal,
                SUM(total_untung) as total_untung,
                SUM(total_barang_keluar) as total_barang_keluar
            ')
            ->where('tgl_penjualan >=', $startDate)
            ->where('tgl_penjualan <=', $endDate);
    
        // Add Tipe Barang filter if not 'all'
        if ($tipeBarang !== 'all') {
            $query->where('id_tipe', $tipeBarang);
        }
    
        $results = $query
            ->groupBy('tgl_penjualan')
            ->orderBy('tgl_penjualan', 'ASC')
            ->findAll();
    
        // Prepare data for chart
        $labels = [];
        $totalHargaJual = [];
        $totalUntung = [];
        $totalHargaModal = [];
        $totalBarangKeluar = [];
    
        foreach ($results as $row) {
            $labels[] = date('d-m-Y', strtotime($row['tgl_penjualan']));
            $totalHargaJual[] = floatval($row['total_harga_jual']);
            $totalUntung[] = floatval($row['total_untung']);
            $totalHargaModal[] = floatval($row['total_harga_modal']);
            $totalBarangKeluar[] = floatval($row['total_barang_keluar']);
        }
    
        $response = [
            'labels' => $labels,
            'total_harga_jual' => $totalHargaJual,
            'total_untung' => $totalUntung,
            'total_harga_modal' => $totalHargaModal,
            'total_barang_keluar' => $totalBarangKeluar
        ];
    
        return $this->response->setJSON($response);
    }
    
    // Add a method to get Tipe Barang options
    public function getTipeBarangOptions()
    {
        // Assuming you have a BarangModel or similar
        $barangModel = new BarangModel();
        
        $tipeBarangList = $barangModel
            ->select('id_tipe, jenis_barang')
            ->distinct()
            ->findAll();
    
        // Add 'All' option
        $options = [
            [
                'id_tipe' => 'all',
                'jenis_barang' => 'Semua Barang'
            ]
        ];
    
        // Merge with existing tipe barang
        $options = array_merge($options, $tipeBarangList);
    
        return $this->response->setJSON($options);
    }
    
    public function getTipeBarang() 
    {
        try {
            // Use the correct model to fetch unique jenis_barang
            // Correct the SQL query to use DISTINCT properly
            $barangList = $this->barangModel->select('jenis_barang')->distinct()->findAll();
            
            $options = [];
            
            foreach ($barangList as $item) {
                $options[] = [
                    'jenis_barang' => $item['jenis_barang']
                ];
            }
            
            return $this->response->setJSON($options);
        } catch (\Exception $e) {
            log_message('error', 'Tipe Barang Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    
    public function getDatatables()
    {
        $request = service('request');

        // Get parameters for DataTables
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];

        // Base query using $this->viewSummaryModel
        $query = $this->viewSummaryModel->orderBy('tgl_penjualan', 'DESC');

        // Apply search if search value exists
        if (!empty($searchValue)) {
            $query->groupStart()
                  ->like('tgl_penjualan', $searchValue)
                  ->orLike('jenis_barang', $searchValue)
                  ->orLike('margine', $searchValue)
                  ->orLike('biaya', $searchValue)
                  ->orLike('margine_bersih', $searchValue)
                  ->groupEnd();
        }

        // Get total records
        $totalRecords = $this->viewSummaryModel->countAllResults(false);

        // Get filtered records
        $filteredRecords = $query->countAllResults(false);

        // Get data for current page
        $data = $query->findAll($length, $start);

        // Prepare response for DataTables
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => array_map(function($item) {
                return [
                    'no' => '', // This will be populated by DataTables
                    'tgl_penjualan' => $item['tgl_penjualan'],
                    'jenis_barang' => $item['jenis_barang'] ?? 'N/A',
                    'margine' => number_format($item['margine'], 2, ',', '.'),
                    'biaya' => number_format($item['biaya'], 2, ',', '.'),
                    'margine_bersih' => number_format($item['margine_bersih'], 2, ',', '.'),
                    'jumlah_transaksi' => $item['jumlah_transaksi'],
                    'jumlah_cash' => $item['jumlah_cash'],
                    'jumlah_transfer' => $item['jumlah_transfer'],
                    'jumlah_piutang' => $item['jumlah_piutang']
                ];
            }, $data)
        ];

        return $this->response->setJSON($response);
    }

    public function getCumulativeDatatables()
    {
        $request = service('request');
    
        // Get date range from request
        $startDate = $request->getPost('start_date') ?? date('Y-m-d', strtotime('-1 month'));
        $endDate = $request->getPost('end_date') ?? date('Y-m-d');
    
        // Retrieve records within the date range
        $query = $this->viewSummaryModel
            ->where('tgl_penjualan >=', $startDate)
            ->where('tgl_penjualan <=', $endDate)
            ->orderBy('tgl_penjualan', 'DESC');
    
        // Get the filtered results
        $results = $query->findAll();
    
        // Format the data
        $formattedData = array_map(function($row) {
            return [
                'tgl_penjualan' => date('Y-m-d', strtotime($row['tgl_penjualan'])),
                'total_margine' => number_format($row['margine'], 2, ',', '.'),
                'total_biaya' => number_format($row['biaya'], 2, ',', '.'),
                'total_margine_bersih' => number_format($row['margine_bersih'], 2, ',', '.'),
                'total_transaksi' => $row['jumlah_transaksi'],
                'total_cash' => $row['jumlah_cash'],
                'total_transfer' => $row['jumlah_transfer'],
                'total_piutang' => $row['jumlah_piutang']
            ];
        }, $results);
    
        // Calculate grand totals based on filtered results
        $grandTotals = [
            'total_margine' => number_format(
                array_sum(array_column($results, 'margine')), 
                2, ',', '.'
            ),
            'total_biaya' => number_format(
                array_sum(array_column($results, 'biaya')), 
                2, ',', '.'
            ),
            'total_margine_bersih' => number_format(
                array_sum(array_column($results, 'margine_bersih')), 
                2, ',', '.'
            ),
            'total_transaksi' => array_sum(array_column($results, 'jumlah_transaksi')),
            'total_cash' => array_sum(array_column($results, 'jumlah_cash')),
            'total_transfer' => array_sum(array_column($results, 'jumlah_transfer')),
            'total_piutang' => array_sum(array_column($results, 'jumlah_piutang'))
        ];
    
        $response = [
            'data' => $formattedData,
            'grandTotals' => $grandTotals,
            'recordsTotal' => count($formattedData),
            'recordsFiltered' => count($formattedData),
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    
        return $this->response->setJSON($response);
    }

    public function getPerBarangDatatables()
    {
        $request = service('request');
    
        // Get date range
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
    
        // Build query for per barang data
        $query = $this->viewSummaryMargineBarangModel
            ->where('tgl_penjualan >=', $startDate)
            ->where('tgl_penjualan <=', $endDate)
            ->orderBy('tgl_penjualan', 'DESC');
    
        // Retrieve filtered records
        $results = $query->findAll();
    
        // Format the data
        $formattedData = array_map(function($row) {
            return [
                'tgl_penjualan' => date('Y-m-d', strtotime($row['tgl_penjualan'])),
                'jenis_barang' => $row['jenis_barang'],
                'margine' => number_format($row['margine'], 2, ',', '.'),
                'jumlah_transaksi' => $row['jumlah_transaksi'],
                'jumlah_cash' => $row['jumlah_cash'],
                'jumlah_transfer' => $row['jumlah_transfer'],
                'jumlah_piutang' => $row['jumlah_piutang']
            ];
        }, $results);
    
        $response = [
            'data' => $formattedData,
            'recordsTotal' => count($formattedData),
            'recordsFiltered' => count($formattedData)
        ];
    
        return $this->response->setJSON($response);
    }

    public function exportAllReports()
    {

        if ($this->request->isAJAX()) {
            // Return a JSON response for AJAX
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Persiapan ekspor...'
            ]);
        }


            // Get date parameters
    $startDate = $this->request->getGet('start_date');
    $endDate = $this->request->getGet('end_date');
    $startDateMeity = $this->request->getGet('start_date_meity');
    $endDateMeity = $this->request->getGet('end_date_meity');

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
    


 // Sheet 1: Detailed Meity Overview
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Meity Overview');

    // Filter Meity data by date if provided
    $meityQuery = $this->viewMeityModel;
    if ($startDateMeity && $endDateMeity) {
        $meityQuery = $meityQuery->where('tgl_masuk >=', $startDateMeity)
                                 ->where('tgl_masuk <=', $endDateMeity);
    }
    $meityData = $meityQuery->findAll();

    // Set comprehensive headers
    $sheet1->setCellValue('A1', 'ID Pembelian');
    $sheet1->setCellValue('B1', 'Tanggal Masuk');
    $sheet1->setCellValue('C1', 'Barang Masuk');
    $sheet1->setCellValue('D1', 'Harga Modal Barang');
    $sheet1->setCellValue('E1', 'Total Meity');
    $sheet1->setCellValue('F1', 'Terkumpul');
    $sheet1->setCellValue('G1', 'Hutang');
    $sheet1->setCellValue('H1', 'Jenis Barang');
    $sheet1->setCellValue('I1', 'Satuan Dasar');
    $sheet1->setCellValue('J1', 'Sudah Setor');
    $sheet1->setCellValue('K1', 'Keterangan');
    $sheet1->setCellValue('L1', 'Status');
    $sheet1->setCellValue('M1', 'Current Piutang');
    $sheet1->setCellValue('N1', 'Current Sisa');
    $sheet1->setCellValue('O1', 'Current Transfer');
    // $sheet1->setCellValue('P1', 'Is Cash');
    $sheet1->setCellValue('P1', 'Total Cash');
    // $sheet1->setCellValue('R1', 'Jumlah Cash');

    // Write Meity data
    foreach ($meityData as $rowIndex => $item) {
        $row = $rowIndex + 2;
        $sheet1->setCellValue('A' . $row, $item['id_pembelian']);
        $sheet1->setCellValue('B' . $row, date('d-m-Y', strtotime($item['tgl_masuk'])));
        $sheet1->setCellValue('C' . $row, $item['barang_masuk']);
        $sheet1->setCellValue('D' . $row, $item['harga_modal_barang']);
        $sheet1->setCellValue('E' . $row, $item['total_meity']);
        $sheet1->setCellValue('F' . $row, $item['terkumpul']);
        $sheet1->setCellValue('G' . $row, $item['hutang']);
        $sheet1->setCellValue('H' . $row, $item['jenis_barang']);
        $sheet1->setCellValue('I' . $row, $item['satuan_dasar']);
        $sheet1->setCellValue('J' . $row, $item['sudah_setor']);
        
        $statusMap = [
            0 => 'Belum Lunas',
            1 => 'Menunggu Konfirmasi',
            2 => 'Lunas'
        ];
        $sheet1->setCellValue('L' . $row, $statusMap[$item['status']] ?? 'Unknown');
        
        $sheet1->setCellValue('M' . $row, $item['current_piutang']);
        $sheet1->setCellValue('N' . $row, $item['current_sisa']);
        $sheet1->setCellValue('O' . $row, $item['current_transfer']);
        $sheet1->setCellValue('P' . $row, $item['total_cash']);
    }
    

    // Sheet 2: Penjualan Overview
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('Penjualan Overview');

    // Fetch Penjualan data
    $penjualanModel = new PenjualanModel();
    $barangModel = new BarangModel();

        // Get all barang types mapped by their ID
        $barangTypes = $barangModel->findAll();
        $barangTypeMap = [];
        foreach ($barangTypes as $barang) {
            $barangTypeMap[$barang['id_tipe']] = $barang['jenis_barang'];
        }

    $penjualanModel = new PenjualanModel();
    $penjualanQuery = $penjualanModel;
    if ($startDate && $endDate) {
        $penjualanQuery = $penjualanQuery->where('tgl_penjualan >=', $startDate)
                                         ->where('tgl_penjualan <=', $endDate);
    }
    $penjualanData = $penjualanQuery->findAll();

    // Set headers for Penjualan sheet
    $sheet2->setCellValue('A1', 'ID Penjualan');
    $sheet2->setCellValue('B1', 'Jenis Barang');
    $sheet2->setCellValue('C1', 'Tanggal Penjualan');
    $sheet2->setCellValue('D1', 'Total Barang Keluar');
    $sheet2->setCellValue('E1', 'Total Harga Jual');
    $sheet2->setCellValue('F1', 'Total Harga Modal');
    $sheet2->setCellValue('G1', 'Total Untung');

    // Write Penjualan data
    foreach ($penjualanData as $rowIndex => $item) {
        $row = $rowIndex + 2;
        $sheet2->setCellValue('A' . $row, $item['id_penjualan']);
        
        // Replace ID with Jenis Barang name, with fallback
        $jenisBarang = $barangTypeMap[$item['id_tipe']] ?? 'Unknown';
        $sheet2->setCellValue('B' . $row, $jenisBarang);
        
        $sheet2->setCellValue('C' . $row, date('d-m-Y', strtotime($item['tgl_penjualan'])));
        $sheet2->setCellValue('D' . $row, $item['total_barang_keluar']);
        $sheet2->setCellValue('E' . $row, $item['total_harga_jual']);
        $sheet2->setCellValue('F' . $row, $item['total_harga_modal']);
        $sheet2->setCellValue('G' . $row, $item['total_untung']);
    }
    
    // Sheet 3: Detail Penjualan with Tanggal Penjualan
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('Detail Penjualan');

    // Fetch Detail Penjualan data with tanggal_penjualan
    $viewDetailPenjualanModel = new ViewDetailPenjualanModel();
    
    $detailPenjualanQuery = \Config\Database::connect()
        ->table('vw_detail_penjualan vdp')
        ->select('vdp.*, tp.tgl_penjualan')
        ->join('tbl_penjualan tp', 'tp.id_penjualan = vdp.id_penjualan');

    if ($startDate && $endDate) {
        $detailPenjualanQuery->where("tp.tgl_penjualan >=", $startDate)
                             ->where("tp.tgl_penjualan <=", $endDate);
    }

    $detailPenjualanData = $detailPenjualanQuery->get()->getResultArray();

    // Set headers for Detail Penjualan sheet
    $sheet3->setCellValue('A1', 'Tanggal Penjualan');
    $sheet3->setCellValue('B1', 'ID Penjualan');
    $sheet3->setCellValue('C1', 'Nama Customer');
    $sheet3->setCellValue('D1', 'Jumlah Keluar');
    $sheet3->setCellValue('E1', 'Harga Modal');
    $sheet3->setCellValue('F1', 'Total Harga Modal');
    $sheet3->setCellValue('G1', 'Harga Jual');
    $sheet3->setCellValue('H1', 'Total Harga Jual');
    $sheet3->setCellValue('I1', 'Untung');
    $sheet3->setCellValue('J1', 'Uang Masuk');
    $sheet3->setCellValue('K1', 'Metode Pembayaran');
    $sheet3->setCellValue('L1', 'Jenis Barang');
    $sheet3->setCellValue('M1', 'Tipe Unit');
    $sheet3->setCellValue('N1', 'Status');

    // Write Detail Penjualan data
    foreach ($detailPenjualanData as $rowIndex => $item) {
        $row = $rowIndex + 2;
        $sheet3->setCellValue('A' . $row, date('d-m-Y', strtotime($item['tgl_penjualan'])));
        $sheet3->setCellValue('B' . $row, $item['id_penjualan']);
        $sheet3->setCellValue('C' . $row, $item['nama_customer']);
        $sheet3->setCellValue('D' . $row, $item['jumlah_keluar']);
        $sheet3->setCellValue('E' . $row, $item['harga_modal_barang']);
        $sheet3->setCellValue('F' . $row, $item['total_harga_modal']);
        $sheet3->setCellValue('G' . $row, $item['harga_jual']);
        $sheet3->setCellValue('H' . $row, $item['total_harga_jual']);
        $sheet3->setCellValue('I' . $row, $item['untung_telur']);
        $sheet3->setCellValue('J' . $row, $item['jumlah']);
        $sheet3->setCellValue('K' . $row, $item['nama_method']);
        $sheet3->setCellValue('L' . $row, $item['jenis_barang']);
        $sheet3->setCellValue('M' . $row, $item['tipe_unit']);

        $statusMap = [
            '0' => 'Belum Lunas',
            '1' => 'Lunas'
        ];
        $sheet3->setCellValue('N' . $row, $statusMap[$item['status']] ?? 'Unknown');

    }
    
        // Sheet 4: Summary Reports
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Summary Reports');
    
    // Filter Summary data
    $viewSummaryModel = new ViewSummaryModel();
    $summaryQuery = $viewSummaryModel;
    if ($startDate && $endDate) {
        $summaryQuery = $summaryQuery->where('tgl_penjualan >=', $startDate)
                                     ->where('tgl_penjualan <=', $endDate);
    }
    $summaryData = $summaryQuery->findAll();
    
        // Set headers for Summary sheet
        $sheet4->setCellValue('A1', 'Tanggal Penjualan');
        $sheet4->setCellValue('B1', 'Margine');
        $sheet4->setCellValue('C1', 'Biaya');
        $sheet4->setCellValue('D1', 'Margine Bersih');
        $sheet4->setCellValue('E1', 'Jumlah Transaksi');
        $sheet4->setCellValue('F1', 'Jumlah Cash');
        $sheet4->setCellValue('G1', 'Jumlah Transfer');
        $sheet4->setCellValue('H1', 'Jumlah Piutang');
    
        // Write Summary data
        foreach ($summaryData as $rowIndex => $item) {
            $row = $rowIndex + 2;
            $sheet4->setCellValue('A' . $row, date('d-m-Y', strtotime($item['tgl_penjualan'])));
            $sheet4->setCellValue('B' . $row, $item['margine']);
            $sheet4->setCellValue('C' . $row, $item['biaya']);
            $sheet4->setCellValue('D' . $row, $item['margine_bersih']);
            $sheet4->setCellValue('E' . $row, $item['jumlah_transaksi']);
            $sheet4->setCellValue('F' . $row, $item['jumlah_cash']);
            $sheet4->setCellValue('G' . $row, $item['jumlah_transfer']);
            $sheet4->setCellValue('H' . $row, $item['jumlah_piutang']);
        }
    
        // Sheet 5: Stock Overview
        // Sheet 5: Stock Overview
        $sheet5 = $spreadsheet->createSheet();
        $sheet5->setTitle('Stock Overview');

        // Filter Stock data
        $stockQuery = $this->viewStockModel
            ->select('view_stock_with_sisa_per_unit.*, tbl_tipe_barang.jenis_barang, tbl_tipe_barang.satuan_dasar')
            ->join('tbl_tipe_barang', 'view_stock_with_sisa_per_unit.id_tipe = tbl_tipe_barang.id_tipe')
            ->orderBy('tgl_stock', 'DESC');

        if ($startDate && $endDate) {
            $stockQuery->where('tgl_stock >=', $startDate)
                    ->where('tgl_stock <=', $endDate);
        }

        $stockData = $stockQuery->findAll();

        // Set headers for Stock sheet
        $sheet5->setCellValue('A1', 'No');
        $sheet5->setCellValue('B1', 'Tanggal Stock');
        $sheet5->setCellValue('C1', 'Jenis Barang');
        $sheet5->setCellValue('D1', 'Satuan Dasar');
        $sheet5->setCellValue('F1', 'Total Pembelian');
        $sheet5->setCellValue('G1', 'Total Penjualan');
        $sheet5->setCellValue('H1', 'Sisa Stok');

        // Write Stock data
        foreach ($stockData as $rowIndex => $item) {
            $row = $rowIndex + 2;
            
            $sheet5->setCellValue('A' . $row, $rowIndex + 1);
            $sheet5->setCellValue('B' . $row, date('d-m-Y', strtotime($item['tgl_stock'])));
            $sheet5->setCellValue('C' . $row, $item['jenis_barang']);
            $sheet5->setCellValue('D' . $row, $item['satuan_dasar']);
            
            // Format numbers similar to DataTables method
            $sheet5->setCellValue('F' . $row, number_format($item['total_pembelian'], 2, ',', '.'));
            $sheet5->setCellValue('G' . $row, number_format($item['total_penjualan'], 2, ',', '.'));
            $sheet5->setCellValue('H' . $row, number_format($item['sisa_stok'], 2, ',', '.'));
        }

        // Optional: Set active sheet to first sheet when opened
        $spreadsheet->setActiveSheetIndex(0);
    
        // Generate file
        // Generate file
        $writer = new Xlsx($spreadsheet);

        // Get current date in desired format
        $exportDate = date('Y-m-d_H-i-s');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="summary_export_' . $exportDate . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit();
    }

}
