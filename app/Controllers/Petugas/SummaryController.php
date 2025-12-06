<?php

namespace App\Controllers\Admin;

use App\Models\ViewSummaryModel;
use App\Models\BarangModel; // Make sure to import the BarangModel
use App\Models\ViewSummaryMargineBarangModel;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SummaryController extends BaseController
{
    protected $viewSummaryModel;
    protected $viewSummaryMargineBarangModel;
    protected $barangModel;

    public function __construct()
    {
        $this->viewSummaryModel = new ViewSummaryModel();
        $this->viewSummaryMargineBarangModel = new ViewSummaryMargineBarangModel();
        $this->barangModel = new BarangModel();
    }
    public function list()
    {
        return view('pages/admin/summary/list');
    }
    public function getDatatables()
    {
        $request = service('request');

        // Similar to previous implementation, but add summary period logic
        $summaryPeriod = $request->getPost('summary_period');
    
        // Modify query based on summary period
        if ($summaryPeriod === 'yearly') {
            // Yearly summary logic
            $startDate = date('Y-m-d', strtotime('-11 months'));
            $endDate = date('Y-m-d');
        } else {
            // Monthly summary logic
            $startDate = date('Y-m-d', strtotime('-4 weeks'));
            $endDate = date('Y-m-d');
        }
    
        // Get parameters for DataTables
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        
        // Get start and end date filters if provided
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        
        // Correctly get jenis_barang filter
        $jenisBarang = $request->getPost('jenis_barang');
    
        // Base query using $this->viewSummaryModel
        $query = $this->viewSummaryModel->orderBy('tgl_penjualan', 'DESC');
    
        // Apply date filter if dates are provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('tgl_penjualan >=', $startDate)
                  ->where('tgl_penjualan <=', $endDate);
        }
    
        // Apply jenis_barang filter if provided
        if (!empty($jenisBarang)) {
            $query->where('jenis_barang', $jenisBarang);
        }
    
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
                    
                    // Use 1 instead of 1000 to avoid over-scaling
                    'margine' => number_format($item['margine'], 2, ',', '.'),
                    'biaya' => number_format($item['biaya'], 2, ',', '.'),
                    'margine_bersih' => number_format($item['margine_bersih'], 2, ',', '.'),
                    
                    'jumlah_transaksi' => $item['jumlah_transaksi'],
                    
                    // Keep these as numbers without "Rp"
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
    
        // Determine date range
        $summaryPeriod = $request->getPost('summary_period');
        if ($summaryPeriod === 'yearly') {
            $startDate = date('Y-m-d', strtotime('-11 months'));
            $endDate = date('Y-m-d');
        } else {
            $startDate = date('Y-m-d', strtotime('-4 weeks'));
            $endDate = date('Y-m-d');
        }
    
        // Get jenis_barang filter
        $jenisBarang = $request->getPost('jenis_barang');
    
        // Build query for cumulative data
        $query = $this->viewSummaryModel;
    
        // Apply date filter
        $query->where('tgl_penjualan >=', $startDate)
              ->where('tgl_penjualan <=', $endDate)
              ->orderBy('tgl_penjualan', 'DESC');  // Add descending order
    
        // Apply jenis_barang filter if provided
        if (!empty($jenisBarang)) {
            $query->where('jenis_barang', $jenisBarang);
        }
    
        // Retrieve all records
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
    
        $response = [
            'data' => $formattedData,
            'recordsTotal' => count($formattedData),
            'recordsFiltered' => count($formattedData)
        ];
    
        return $this->response->setJSON($response);
    }

    public function getPerBarangDatatables()
    {
        $request = service('request');

        // Build query for per barang data
        $query = $this->viewSummaryMargineBarangModel;

        // Get jenis_barang filter
        $jenisBarang = $request->getPost('jenis_barang');

        // Apply jenis_barang filter if provided
        if (!empty($jenisBarang)) {
            $query->where('jenis_barang', $jenisBarang);
        }

        // Order by date in descending order
        $query->orderBy('tgl_penjualan', 'DESC');

        // Retrieve all records
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

// Add this route in your routes file
// $routes->post('admin/summary/cumulative-datatables', 'Admin\SummaryController::getCumulativeDatatables');
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

    public function getChartData()
    {
        $request = service('request');
    
        // Get filter parameters
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        $jenisBarang = $request->getPost('jenis_barang');
        $chartType = $request->getPost('chart_type');
        $summaryPeriod = $request->getPost('summary_period'); // 'yearly' or 'monthly'
    
        // Determine date range based on summary period
        if ($summaryPeriod === 'yearly') {
            // Yearly summary (last 12 months)
            $startDate = date('Y-m-d', strtotime('-11 months', strtotime(date('Y-m-01'))));
            $endDate = date('Y-m-d');
            
            $query = $this->viewSummaryModel
                ->select("DATE_FORMAT(tgl_penjualan, '%Y-%m') as period, SUM($chartType) as total")
                ->where('tgl_penjualan >=', $startDate)
                ->where('tgl_penjualan <=', $endDate)
                ->groupBy("DATE_FORMAT(tgl_penjualan, '%Y-%m')");
        } else {
            // Monthly summary (last 4 weeks)
            $startDate = date('Y-m-d', strtotime('-4 weeks'));
            $endDate = date('Y-m-d');
            
            $query = $this->viewSummaryModel
                ->select("DATE_FORMAT(tgl_penjualan, '%Y-%m-%d') as period, SUM($chartType) as total")
                ->where('tgl_penjualan >=', $startDate)
                ->where('tgl_penjualan <=', $endDate)
                ->groupBy("DATE_FORMAT(tgl_penjualan, '%Y-%m-%d')");
        }
    
        // Apply additional filters
        if (!empty($jenisBarang)) {
            $query->where('jenis_barang', $jenisBarang);
        }
    
        $data = $query->findAll();
    
        // Prepare labels and values
        $labels = [];
        $values = [];
    
        foreach ($data as $item) {
            $labels[] = $item['period'];
            $values[] = (float)$item['total'];
        }
    
        return $this->response->setJSON([
            'labels' => $labels,
            'values' => $values,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
}
