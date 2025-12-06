<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{ViewSummaryModel,BarangModel,PenjualanModel,DetailPenjualanModel,UnitBarangModel};
use App\Models\PengeluaranModel;

class DashboardController extends BaseController
{
    protected $viewSummaryModel;
    protected $barangModel;
    protected $pengeluaranModel;
    protected $penjualanModel;
    protected $detailPenjualanModel;
    protected $unitBarangModel;

    public function __construct()
    {
        $this->viewSummaryModel = new ViewSummaryModel();
        $this->barangModel = new BarangModel();
        $this->pengeluaranModel = new PengeluaranModel();
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->unitBarangModel = new UnitBarangModel();
    }

    public function list()
    {
        // Get unique jenis_barang for dropdown
        $barangList = $this->barangModel->select('jenis_barang')->distinct()->findAll();
        $jenisBarangOptions = array_column($barangList, 'jenis_barang');

        // Get today's summary data for pie chart
        $todayDate = date('Y-m-d');
        $todaySummary = $this->viewSummaryModel
            ->where('tgl_penjualan', $todayDate)
            ->first();

        // Get today's expenses
        $todayExpenses = $this->pengeluaranModel
            ->where('tgl_pengeluaran', $todayDate)
            ->first();

        $data = [
            'jenisBarangOptions' => $jenisBarangOptions,
            'todaySummary' => $todaySummary ?? [], // Provide an empty array if no data
            'todayExpenses' => $todayExpenses ?? [] // Provide an empty array if no data
        ];

        return view('pages/admin/dashboard', $data);
    }

    public function checkStandarHargaJualStatus()
    {
        $today = date('Y-m-d');
        
        // Use the UnitBarangModel you've already defined
        $unitBarangModel = new UnitBarangModel();
        
        // Query to find the latest record
        $latestRecord = $unitBarangModel
            ->orderBy('tanggal', 'DESC')
            ->first();
    
        $status = [
            'is_updated' => false,
            'last_update_date' => null,
            'days_since_update' => null
        ];
    
        if ($latestRecord) {
            $lastUpdateDate = $latestRecord['tanggal'];
            
            // Calculate days since last update
            $dateDiff = date_diff(date_create($lastUpdateDate), date_create($today));
            $daysSinceUpdate = $dateDiff->days;
    
            $status = [
                'is_updated' => $lastUpdateDate == $today,
                'last_update_date' => $lastUpdateDate,
                'days_since_update' => $daysSinceUpdate
            ];
        }
    
        return $this->response->setJSON($status);
    }

    public function getChartData()
    {
        $request = service('request');
    
        // Get filter parameters
        $startDate = $request->getPost('start_date') ?? date('Y-m-d', strtotime('-11 months'));
        $endDate = $request->getPost('end_date') ?? date('Y-m-d');
        $jenisBarang = $request->getPost('jenis_barang');
        $idTipe = $request->getPost('id_tipe'); // Add this line
        $chartType = $request->getPost('chart_type') ?? 'margine_bersih';
        $summaryPeriod = $request->getPost('summary_period') ?? 'yearly';
    
        // Determine date range and grouping based on summary period
        $query = $this->viewSummaryModel
            ->select("DATE_FORMAT(tgl_penjualan, '%Y-%m') as period, SUM($chartType) as total")
            ->where('tgl_penjualan >=', $startDate)
            ->where('tgl_penjualan <=', $endDate);
    
        // Apply jenis_barang filter if provided
        if (!empty($jenisBarang)) {
            $query->where('jenis_barang', $jenisBarang);
        }
    
        // Apply id_tipe filter if provided
        if (!empty($idTipe)) {
            $query->where('id_tipe', $idTipe);
        }
    
        // Group by period
        $query->groupBy("DATE_FORMAT(tgl_penjualan, '%Y-%m')");
    
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

    public function getDailyPieChartData()
    {
        $todayDate = date('Y-m-d');

        $dailyData = $this->viewSummaryModel
            ->where('tgl_penjualan', $todayDate)
            ->first();

        if (!$dailyData) {
            return $this->response->setJSON([
                'labels' => ['Cash', 'Transfer', 'Piutang'],
                'values' => [0, 0, 0]
            ]);
        }

        $labels = ['Cash', 'Transfer', 'Piutang'];
        $values = [
            $dailyData['jumlah_cash'] ?? 0,
            $dailyData['jumlah_transfer'] ?? 0,
            $dailyData['jumlah_piutang'] ?? 0
        ];

        return $this->response->setJSON([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function getDailyBarChartData()
{
    $request = service('request');

    // Get filter parameters
    $startDate = $request->getGet('start_date') ?? date('Y-m-d', strtotime('-7 days'));
    $endDate = $request->getGet('end_date') ?? date('Y-m-d');
    $idTipe = $request->getGet('id_tipe');

    // Build the query
    $query = $this->detailPenjualanModel
        ->select("DATE(tgl_penjualan) as sale_date, 
                  SUM(jumlah_keluar) as total_quantity, 
                  SUM(harga_jual * jumlah_keluar) as total_revenue")
        ->join('tbl_penjualan', 'tbl_penjualan.id_penjualan = tbl_detail_penjualan.id_penjualan')
        ->where('tgl_penjualan >=', $startDate)
        ->where('tgl_penjualan <=', $endDate);

    // Apply id_tipe filter if provided
    if (!empty($idTipe)) {
        $query->where('tbl_detail_penjualan.id_tipe', $idTipe);
    }

    // Group by date
    $dailyData = $query->groupBy('sale_date')
                       ->orderBy('sale_date')
                       ->findAll();

    // Prepare labels and values
    $labels = [];
    $quantities = [];
    $revenues = [];

    foreach ($dailyData as $item) {
        $labels[] = $item['sale_date'];
        $quantities[] = (float)$item['total_quantity'];
        $revenues[] = (float)$item['total_revenue'];
    }

    return $this->response->setJSON([
        'labels' => $labels,
        'quantities' => $quantities,
        'revenues' => $revenues,
        'start_date' => $startDate,
        'end_date' => $endDate
    ]);
}

// Method to get tipe barang options
public function getTipeBarangOptions()
{
    // Fetch unique id_tipe from tbl_tipe_barang using BarangModel
    $tipeBarangOptions = $this->barangModel
        ->select('id_tipe')
        ->distinct()
        ->findAll();

    return $this->response->setJSON($tipeBarangOptions);
}

    
}