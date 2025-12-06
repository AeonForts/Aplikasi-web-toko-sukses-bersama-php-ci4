<?php

namespace App\Controllers\Petugas;

use App\Controllers\BaseController;
use App\Models\PengeluaranModel;  
use App\Models\DetailPengeluaranModel;  
use App\Models\BarangModel;
use App\Models\CashPaymentModel;  
use CodeIgniter\HTTP\ResponseInterface;

class PengeluaranController extends BaseController
{
    protected $pengeluaranModel;

    public function __construct()
    {
        $this->pengeluaranModel = new PengeluaranModel();
    }
    public function list()
    {
        return view('pages/petugas/pengeluaran/list');
    }
    
    public function getDatatables()
    {
        $request = service('request');
        $pengeluaranModel = new PengeluaranModel();
    
        // Get parameters for DataTables
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        
        // Get start and end date filters if provided
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
    
        // Base query
        $query = $pengeluaranModel->select('id_pengeluaran, tgl_pengeluaran, total_biaya')
            ->orderBy('tgl_pengeluaran', 'DESC');
    
        // Apply date filter if dates are provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('tgl_pengeluaran >=', $startDate)
                  ->where('tgl_pengeluaran <=', $endDate);
        }
    
        // Apply search if search value exists
        if (!empty($searchValue)) {
            $query->groupStart()
                  ->like('tgl_pengeluaran', $searchValue)
                  ->orLike('total_biaya', $searchValue)
                  ->groupEnd();
        }
    
        // Get total records
        $totalRecords = $pengeluaranModel->countAllResults(false);
    
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
                    'tgl_pengeluaran' => $item['tgl_pengeluaran'],
                    'total_biaya' => number_format($item['total_biaya'], 0, ',', '.'),
                    'action' => '<a href="' . base_url('petugas/pengeluaran/detail/' . $item['id_pengeluaran']) . '" class="btn btn-info text-white">Detail</a>'
                ];
            }, $data)
        ];
    
        return $this->response->setJSON($response);
    }
        
    public function detail($id)
    {
        $pengeluaranModel = new PengeluaranModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
        
        // Fetch the pengeluaran record to get the date
        $pengeluaran = $pengeluaranModel->find($id);
        
        $data['tgl_pengeluaran'] = $pengeluaran['tgl_pengeluaran']; // Add this line
        $data['detail_pengeluaran'] = $detailPengeluaranModel->getDetailByPengeluaranId($id);
        
        return view('pages/petugas/pengeluaran/detail', $data);
    }

    public function getChartData()
    {
        $filter = $this->request->getGet('filter');
        $year = date('Y');
        $currentMonth = date('m');
    
        try {
            // If filter is for weekly data
            if ($filter === 'monthly') {
                // Prepare weekly data structure
                $weeklyData = [
                    ['week' => 1, 'total' => 0],
                    ['week' => 2, 'total' => 0],
                    ['week' => 3, 'total' => 0],
                    ['week' => 4, 'total' => 0]
                ];
    
                // Query to get weekly totals for current month
                $results = $this->pengeluaranModel
                    ->select("CEIL(DAY(tgl_pengeluaran) / 7) as week, SUM(total_biaya) as total")
                    ->where('YEAR(tgl_pengeluaran)', $year)
                    ->where('MONTH(tgl_pengeluaran)', $currentMonth)
                    ->groupBy('week')
                    ->findAll();
    
                // Update weekly data with actual results
                foreach ($results as $result) {
                    $weekIndex = $result['week'] - 1;
                    $weeklyData[$weekIndex]['total'] = $result['total'];
                }
    
                return $this->response->setJSON($weeklyData);
            }
    
            // Default: Yearly monthly chart data
            // Prepare an array with all months initialized to zero
            $chartData = array_map(function($month) {
                return [
                    'month' => $month,
                    'total' => 0
                ];
            }, range(1, 12));
    
            // Get total expenses for each month
            $monthlyResults = $this->pengeluaranModel
                ->select('MONTH(tgl_pengeluaran) as month, SUM(total_biaya) as total')
                ->where('YEAR(tgl_pengeluaran)', $year)
                ->groupBy('MONTH(tgl_pengeluaran)')
                ->findAll();
    
            // Update the chartData with actual results
            foreach ($monthlyResults as $result) {
                $index = array_search($result['month'], array_column($chartData, 'month'));
                if ($index !== false) {
                    $chartData[$index]['total'] = $result['total'];
                }
            }
    
            return $this->response->setJSON($chartData);
        } 
        catch (\Exception $e) {
            // Log the error
            log_message('error', 'Chart Data Error: ' . $e->getMessage());
            
            // Return error response
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    private function sanitizeNumberInput($input)
    {
        // Replace comma with dot for decimal
        $cleanInput = str_replace(',', '.', $input);
        
        // Remove dots that are used as thousand separators
        $cleanInput = str_replace('.', '', $cleanInput);
        
        // Convert to float
        return floatval($cleanInput);
    }


    public function save()
    {
        $jumlah_biaya = $this->request->getPost('jumlah_biaya');
        $keterangan = $this->request->getPost('keterangan');
        $tgl_pengeluaran = $this->request->getPost('tgl_pengeluaran') ?? date('Y-m-d');
    
        $sanitizedJumlahBiaya = $this->sanitizeNumberInput($jumlah_biaya);
        log_message('debug', 'Sanitized Amount: ' . $sanitizedJumlahBiaya);
        $pengeluaranModel = new PengeluaranModel();
        $detailPengeluaranModel = new DetailPengeluaranModel();
    
        try {
            // Check if there's already an entry for the selected date
            $existingEntry = $pengeluaranModel->where('tgl_pengeluaran', $tgl_pengeluaran)->first();
    
            if ($existingEntry) {
                $last_id = $existingEntry['id_pengeluaran'];
            } else {
                // Insert into tbl_pengeluaran with the selected date
                $pengeluaranModel->insert(['tgl_pengeluaran' => $tgl_pengeluaran]);
                $last_id = $pengeluaranModel->insertID();
            }
    
            // Insert detail with the corresponding date's id
            $detailPengeluaranModel->insert([
                'id_pengeluaran' => $last_id,
                'jumlah_biaya' => $sanitizedJumlahBiaya,
                'keterangan' => $keterangan
            ]);
    
            return $this->response->setJSON(['message' => 'Data berhasil disimpan!']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function edit()
    {
        $id = $this->request->getPost('id_detail_pengeluaran');
        $detailPengeluaranModel = new DetailPengeluaranModel();
    
        $data = $detailPengeluaranModel->find($id);
    
        return $this->response->setJSON($data);
    }
    
    public function update()
    {

        $sanitizedJumlahBiaya = $this->sanitizeNumberInput($jumlah_biaya);

        $id = $this->request->getPost('id_detail_pengeluaran');
        $jumlah_biaya = $this->request->getPost('jumlah_biaya');
        $keterangan = $this->request->getPost('keterangan');
    
        $detailPengeluaranModel = new DetailPengeluaranModel();
    
        try {
            $detailPengeluaranModel->update($id, [
                'jumlah_biaya' => $sanitizedJumlahBiaya,
                'keterangan' => $keterangan
            ]);
    
            return $this->response->setJSON(['message' => 'Data berhasil diperbarui!']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    
    public function delete()
    {
        $id_detail_pengeluaran = $this->request->getPost('id_detail_pengeluaran');
    
        $detailPengeluaranModel = new DetailPengeluaranModel();
        $pengeluaranModel = new PengeluaranModel();
    
        // First, check if the detail exists
        $detailPengeluaran = $detailPengeluaranModel->find($id_detail_pengeluaran);
        
        if (!$detailPengeluaran) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Detail pengeluaran tidak ditemukan.'
            ]);
        }
    
        try {
            $id_pengeluaran = $detailPengeluaran['id_pengeluaran'];
    
            // Delete the specific detail record
            $detailPengeluaranModel->delete($id_detail_pengeluaran);
    
            // Check remaining details for this pengeluaran
            $remainingDetails = $detailPengeluaranModel
                ->where('id_pengeluaran', $id_pengeluaran)
                ->countAllResults();
    
            // If no details remain, remove the pengeluaran record
            if ($remainingDetails === 0) {
                $pengeluaranModel->delete($id_pengeluaran);
            }
    
            // Return success response
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil dihapus!'
            ]);
    
        } catch (\Exception $e) {
            // Return error response
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }
    
    
        
    
}