<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{
    BarangModel,
    PembelianModel,
    DetailPembelianModel,
    MeityModel,
    ViewMeityModel,
    SupplierModel,
    SisaTerkumpulModel,
    StockBarangModel
};
use App\Services\StockService;
use CodeIgniter\Exceptions\PageNotFoundException;

class PembelianController extends BaseController
{
    private $viewMeityModel;
    private $pembelianModel;
    private $detailPembelianModel;
    private $meityModel;
    private $supplierModel;
    private $barangModel;
    private $stockBarangModel;
    private $sisaTerkumpulModel;

    //Service
    private $stockService;

    public function __construct() 
    {
        $this->viewMeityModel = new ViewMeityModel();
        $this->pembelianModel = new PembelianModel();
        $this->detailPembelianModel = new DetailPembelianModel();
        $this->meityModel = new MeityModel();
        $this->supplierModel = new SupplierModel();
        $this->barangModel = new BarangModel();
        $this->stockBarangModel = new StockBarangModel();
        $this->sisaTerkumpulModel = new  SisaTerkumpulModel();

        //Service
        $this->stockService = new StockService();
    }

    public function list() 
    {
        return view('pages/admin/pembelian/list', [
            'pembelian' => $this->viewMeityModel->paginate(10),
            'pager' => $this->viewMeityModel->pager
        ]);
    }

    public function detail($id) 
    {
        $pembelian = $this->pembelianModel->getPembelianDetail($id);
        
        if (empty($pembelian)) {
            throw new PageNotFoundException("Pembelian with ID $id not found.");
        }
        
        return view('pages/admin/pembelian/detail', [
            'pembelian' => $pembelian,
            'id_pembelian' => $id,  // Explicitly pass the ID
            'tipe_barang' => $this->pembelianModel->getTipeBarang(),
            'view_meity' => $this->viewMeityModel->getMeityByPembelian($id)
        ]);
    }

    public function getDatatables()
    {
        $request = service('request');
        $viewMeityModel = new ViewMeityModel(); // Use the ViewMeityModel
    
        // Get parameters for DataTables
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        
        // Get start and end date filters if provided
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
    
        // Base query
        $query = $viewMeityModel
            ->orderBy('tgl_masuk', 'DESC');
    
        // Apply date filter if dates are provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('tgl_masuk >=', $startDate)
                ->where('tgl_masuk <=', $endDate);
        }
    
        // Apply search if search value exists
        if (!empty($searchValue)) {
            $query->groupStart()
                ->like('tgl_masuk', $searchValue)
                ->orLike('jenis_barang', $searchValue)
                ->orLike('total_meity', $searchValue)
                ->orLike('status', $searchValue)
                ->orLike('terkumpul', $searchValue)
                ->orLike('hutang', $searchValue)
                ->groupEnd();
        }
    
        // Get total records
        $totalRecords = $viewMeityModel->countAllResults(false);
    
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
                    'id_pembelian' => $item['id_pembelian'],
                    'tgl_masuk' => date('d-m-Y', strtotime($item['tgl_masuk'])),
                    'total_meity' => number_format($item['total_meity'], 2, ',', '.'),
                    'status' => $item['status'],
                    'terkumpul' => number_format($item['terkumpul'], 2, ',', '.'),
                    'hutang' => number_format($item['hutang'], 2, ',', '.'),
                    'jenis_barang' => $item['jenis_barang'] ?? 'N/A',
                ];
            }, $data)
        ];
    
        return $this->response->setJSON($response);
    }

    public function getChartData()
    {
        try {
            $viewMeityModel = new ViewMeityModel();
            
            // Get parameters
            $jenis_barang = $this->request->getGet('jenis_barang');
            $year = $this->request->getGet('year') ?? date('Y');
    
            // Start base query
            $query = $viewMeityModel->select(
                'MONTH(tgl_masuk) as month, 
                 COALESCE(SUM(terkumpul), 0) as total_terkumpul,
                 COALESCE(SUM(total_meity), 0) as total_meity'
            )
            ->where('YEAR(tgl_masuk)', $year);
    
            // Add jenis_barang filter if provided
            if ($jenis_barang !== null && $jenis_barang !== '') {
                $query->where('jenis_barang', $jenis_barang);
            }
    
            $chartData = $query->groupBy('MONTH(tgl_masuk)')
                               ->orderBy('month')
                               ->findAll();
    
            return $this->response->setJSON($chartData);
        } catch (\Exception $e) {
            log_message('error', 'Chart Data Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    public function getTipeBarang() 
    {
        try {
            // Ensure you're using the correct model
            $barangList = $this->barangModel->findAll();
            
            $options = [];
            
            foreach ($barangList as $item) {
                $options[] = [
                    'id' => (int) $item['id_tipe'],
                    'jenis_barang' => $item['jenis_barang'],
                    'satuan_dasar' => $item['satuan_dasar']
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

    public function save() 
    {
        $input = $this->request->getPost();
        
        if (isset($input['harga_modal_barang'])) {

            $input['harga_modal_barang'] = str_replace(',', '.', $input['harga_modal_barang']);
            
        }

        if (!$this->validateInput($input)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid input data'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $data = $this->processPembelian($input);
            
            $db->transComplete();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data inserted successfully',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getSupplier()
    {
        try{
            $supplier = $this->supplierModel->findAll();

            $options = [];

            foreach ($supplier as $item) {
                $options[] = [
                    'id' => (int) $item['id_supplier'],
                    'nama_supplier' => $item['nama_supplier']
                ];
            }

            return $this->response->setJSON($options);
        } catch (\Exception $e) {
            log_message('error', 'Supplier Error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function edit($id_pembelian)
    {
        try {
            // Use the getPembelianDetail method from PembelianModel
            $pembelian = $this->pembelianModel->getPembelianDetail($id_pembelian);
            $viewmeity = $this->viewMeityModel->getMeityByPembelian($id_pembelian);
    
            if (!$pembelian) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Pembelian not found'
                ])->setStatusCode(404);
            }
    
            // Prepare response with all necessary details
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id_pembelian' => $pembelian['id_pembelian'],
                    'tgl_masuk' => $pembelian['tgl_masuk'],
                    'id_supplier' => $pembelian['id_supplier'],
                    'id_tipe' => $pembelian['id_tipe'],
                    'barang_masuk' => $pembelian['barang_masuk'],
                    'harga_modal_barang' => $pembelian['harga_modal_barang'],
                    'total_meity' => $pembelian['total_meity'],

                    'terkumpul' => $pembelian['terkumpul'],
                    'current_piutang' => $pembelian['current_piutang'],
                    'current_transfer' => $pembelian['current_transfer'],
                    'current_sisa' => $pembelian['current_sisa'],
                    'sudah_setor' => $pembelian['sudah_setor'],
                    'status' => $pembelian['status'],

                    'total_cash' => $viewmeity['total_cash'],
                    'hutang' => $viewmeity['hutang'],
                ]
            ]);
    
        } catch (\Exception $e) {
            log_message('error', 'Edit Pembelian Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    public function update()
    {
        $input = $this->request->getPost();
        
        $db = \Config\Database::connect();
        $db->transStart();
    
        try {
            // Validate required fields
            $requiredFields = [
                'id_pembelian', 
                'id_tipe',
                'id_supplier', 
                'tgl_masuk', 
                'barang_masuk', 
                'harga_modal_barang', 
                'total_meity',
                'terkumpul',
                'current_piutang',
                'current_transfer',
                'current_sisa',
                'status'
            ];
    
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || $input[$field] === '') {
                    throw new \Exception("Field {$field} is required");
                }
            }
    
            // Retrieve the original pembelian record
            $originalPembelian = $this->pembelianModel->find($input['id_pembelian']);
            $originalDetailPembelian = $this->detailPembelianModel
                ->where('id_pembelian', $input['id_pembelian'])
                ->first();
    
            // Calculate stock difference
            $barangMasukDiff = $input['barang_masuk'] - $originalDetailPembelian['barang_masuk'];
    
            // Update stock with the difference for the specific date
            $this->stockService->updateStockWithSpecificDate([
                'id_tipe' => $input['id_tipe'],
                'tgl_stock' => $input['tgl_masuk'],
                'barang_masuk' => $barangMasukDiff,
                'barang_keluar' => 0
            ]);
    
            // Update Meity
            $this->meityModel->update($input['id_pembelian'], [
                'terkumpul' => $input['terkumpul'],
                'current_piutang' => $input['current_piutang'],
                'current_transfer' => $input['current_transfer'],
                'current_sisa' => $input['current_sisa'],
                'sudah_setor' => $input['sudah_setor'],
                'status' => $input['status']
            ]);
    
            // Update Pembelian
            $this->pembelianModel->update($input['id_pembelian'], [
                'tgl_masuk' => $input['tgl_masuk'],
                'id_supplier' => $input['id_supplier'],
                'total_meity' => $input['total_meity']
            ]);
    
            // Update Detail Pembelian
            $this->detailPembelianModel
                ->where('id_pembelian', $input['id_pembelian'])
                ->update(null, [
                    'barang_masuk' => $input['barang_masuk'],
                    'harga_modal_barang' => $input['harga_modal_barang'],
                    'id_tipe' => $input['id_tipe'],
                ]);
            
            $db->transComplete();
    
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pembelian updated successfully',
                'data' => $input
            ]);
    
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Update Pembelian Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    
    public function delete($id = null) 
    {

        // Validate input
        if (!$id || !is_numeric($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid customer ID'
            ])->setStatusCode(400);
        }

        try {
            $existingTransactions = $this->detailPenjualanModel
                ->where('id_pembelian' , $id)
                ->countAllResults();

            if ($existingTransactions > 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Tidak dapat menghapus Meity. Terdapat {$existingTransactions} transaksi yang terkait."
                ])->setStatusCode(400);
            }

            $result = $this->MeityModel->delete($id);
            
            //restores stock
            $this->stockService->updateOrCreateStock([
                'id_tipe' => $input['id_tipe'],
                'barang_masuk' => $input['barang_masuk'],
                'barang_keluar' => 0
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Record deleted successfully'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function validateInput($input) 
    {
        $requiredFields = [
            'nama_supplier',
            'tgl_masuk',
            'id_tipe',
            'barang_masuk',
            'harga_modal_barang'
        ];

        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                return false;
            }
        }

        return true;
    }

    private function processPembelian($input) 
    {
        $id_supplier = $this->getOrCreateSupplier($input['nama_supplier']);
        $total_meity = $this->calculateTotalMeity($input);
        
        $id_pembelian = $this->createPembelian($id_supplier, $total_meity, $input);
        
        if (!$id_pembelian) {
            throw new \Exception('Failed to create Pembelian.');
        }
    
        // Get the `id_detail_pembelian` and pass it to `createStock`.
        $id_detail_pembelian = $this->createDetailPembelian($id_pembelian, $input);

        $this->createStock($input); // Pass $input with `id_detail_pembelian` set.
        $this->createMeity($id_pembelian, $input);
    
        return [
            'id_pembelian' => $id_pembelian,
            'id_supplier' => $id_supplier,
            'total_meity' => $total_meity
        ];
    }
    
    private function calculateTotalMeity($input) 
    {
        return $input['barang_masuk'] * $input['harga_modal_barang'];
    }

    private function getOrCreateSupplier($nama_supplier) 
    {
        $existingSupplier = $this->supplierModel
            ->where('nama_supplier', $nama_supplier)
            ->first();
        
        if ($existingSupplier) {
            return $existingSupplier['id_supplier'];
        }
        
        $this->supplierModel->insert(['nama_supplier' => $nama_supplier]);
        return $this->supplierModel->insertID();
    }

    private function createPembelian($id_supplier, $total_meity, $input) 
    {
        $this->pembelianModel->insert([
            'tgl_masuk' => $input['tgl_masuk'],
            'id_supplier' => $id_supplier,
            'total_meity' => $total_meity
        ]);
        
        return $this->pembelianModel->insertID();
    }

    private function createDetailPembelian($id_pembelian, $input) 
    {
        $this->detailPembelianModel->insert([
            'id_pembelian' => $id_pembelian,
            'barang_masuk' => $input['barang_masuk'],
            'harga_modal_barang' => $input['harga_modal_barang'],
            'id_tipe' => (int) $input['id_tipe']
        ]);
    
        // Capture and return the last insert ID.
        return $this->detailPembelianModel->insertID();

    }
    
    private function createMeity($id_pembelian, $input) 
    {
        $total_meity = $this->calculateTotalMeity($input);
        
        // Check for existing sisa terkumpul for this type
        $existingSisaTerkumpul = $this->sisaTerkumpulModel
            ->where('id_tipe', $input['id_tipe'])
            ->first();
        
        $terkumpul = $input['terkumpul'] ?? 0;
        $current_sisa = 0; // Initialize current_sisa
        
        if ($existingSisaTerkumpul) {
            $terkumpul += $existingSisaTerkumpul['sisa_terkumpul'];
            
            $this->sisaTerkumpulModel->delete($existingSisaTerkumpul['id_sisa_terkumpul']);
        }
        
        // If terkumpul exceeds total_meity, calculate current_sisa
        if ($terkumpul > $total_meity) {
            $current_sisa = $terkumpul - $total_meity;
            
            // Store excess in sisa_terkumpul
            $this->sisaTerkumpulModel->insert([
                'id_tipe' => (int) $input['id_tipe'],
                'sisa_terkumpul' => $current_sisa,
                'tgl_sisa_terkumpul' => date('Y-m-d')
            ]);
            
            // Set terkumpul to exactly match total_meity
            $terkumpul = $total_meity;
        }
    
        // Calculate the status based on the final terkumpul value
        $status = $this->calculateStatus($total_meity, $terkumpul);
        
        // Prepare data for insertion, explicitly including current_sisa
        $this->meityModel->insert([
            'id_pembelian' => $id_pembelian,
            'id_tipe' => (int) $input['id_tipe'],
            'current_sisa' => $current_sisa, // Explicitly set current_sisa
            'terkumpul' => $terkumpul,
            'sudah_setor' => null,
            'keterangan' => $input['keterangan'] ?? '',
            'status' => $status
        ]);
    }


    public function updateTerkumpul($id_pembelian, $new_terkumpul)
    {
        $meity = $this->meityModel->where('id_pembelian', $id_pembelian)->first();
        if (!$meity) {
            throw new \Exception('Record not found');
        }

        $total_meity = $meity['total_meity'];
        $id_tipe = $meity['id_tipe'];

        // First, check for existing sisa terkumpul
        $sisaTerkumpulAmount = $this->sisaTerkumpulModel->useSisaTerkumpul($id_tipe, $new_terkumpul);
        
        // Adjust new_terkumpul after using sisa terkumpul
        $new_terkumpul += $sisaTerkumpulAmount;

        // If new_terkumpul is still more than total_meity
        if ($new_terkumpul > $total_meity) {
            $sisa = $new_terkumpul - $total_meity;
            
            // Store excess in sisa_terkumpul
            $this->sisaTerkumpulModel->insert([
                'id_tipe' => $id_tipe,
                'sisa_terkumpul' => $sisa,
                'tgl_sisa_terkumpul' => date('Y-m-d')
            ]);
            
            // Set new_terkumpul to exactly match total_meity
            $new_terkumpul = $total_meity;
        }

        $new_status = $this->calculateStatus($total_meity, $new_terkumpul);

        $this->meityModel->update($meity['id_meity'], [
            'terkumpul' => $new_terkumpul,
            'status' => $new_status
        ]);
    }

    private function getStatusText($status) 
    {
        switch ($status) {
            case 0:
                return 'Belum Lunas';
            case 1:
                return 'Menunggu Konfirmasi';
            case 2:
                return 'Lunas';
            default:
                return 'Unknown Status';
        }
    }

    // In PembelianController
    public function confirmPayment()
    {
        $id_pembelian = $this->request->getPost('id_pembelian');

        try {
            // Find the Meity record
            $meityRecord = $this->meityModel
                ->where('id_pembelian', $id_pembelian)
                ->first();

            if (!$meityRecord) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Meity Record tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Directly fetch Pembelian record to get total_meity
            $pembelianModel = new PembelianModel(); // Make sure to import/use this model
            $pembelianRecord = $pembelianModel->find($id_pembelian);

            if (!$pembelianRecord) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pembelian Record tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Debug logging
            log_message('debug', 'Confirm Payment Details: ' . json_encode([
                'meityRecord' => $meityRecord,
                'pembelianRecord' => $pembelianRecord
            ]));

            // Validate that it's ready for confirmation
            // Use total_meity from pembelian record
            if ($meityRecord['terkumpul'] < $pembelianRecord['total_meity']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pembayaran belum mencapai jumlah yang dibutuhkan. Terkumpul: ' . 
                                $meityRecord['terkumpul'] . 
                                ', Total Meity: ' . $pembelianRecord['total_meity']
                ])->setStatusCode(400);
            }

            // Only allow confirmation if currently in "Menunggu Konfirmasi" status
            if ($meityRecord['status'] != 1) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Pembayaran tidak dapat dikonfirmasi pada status saat ini'
                ])->setStatusCode(400);
            }

            // Update to Lunas
            $updated = $this->meityModel->update($meityRecord['id_meity'], [
                'status' => 2,  // Lunas
                'sudah_setor' => date('Y-m-d'),
                'current_piutang' => 0,
                'current_transfer' => 0,
                'current_sisa' => 0
            ]);

            if ($updated) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pembayaran berhasil dikonfirmasi'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengkonfirmasi pembayaran'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
    private function calculateStatus($total_meity, $terkumpul)
    {
        if ($terkumpul == 0) {
            return 0; // Belum Lunas
        } elseif ($terkumpul >= $total_meity) {
            return 1; // Menunggu Konfirmasi (automatically triggered)
        } elseif ($terkumpul > 0 && $terkumpul < $total_meity) {
            return 0; // Belum Lunas (partial payment)
        }
        
        return 0; // Default
    }


    private function createStock($input) 
    {
        try {
            $stockService = new \App\Services\StockService();
            
            // Prepare input for stock service
            $stockInput = [
                'id_tipe' => $input['id_tipe'],
                'barang_masuk' => $input['barang_masuk'],
                'barang_keluar' => 0  // No barang keluar during purchase
            ];
            
            // Use the stock service to update or create stock
            $stockService->updateOrCreateStock($stockInput);
        } catch (\Exception $e) {
            log_message('error', 'Failed to create stock: ' . $e->getMessage());
            throw $e;
        }
    }
    
    public function confirmCash()
    {
        try {
            $id_pembelian = $this->request->getPost('id_pembelian');
    
            $meityModel = new MeityModel();
            
            // Find the record by id_pembelian
            $record = $meityModel->where('id_pembelian', $id_pembelian)->first();
    
            if (!$record) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Record not found.'
                ]);
            }
    
            $data = [
                'is_cash' => 1,
                'current_piutang' => 0,
                'current_transfer' => 0
            ];
    
            $result = $meityModel->update($record['id_meity'], $data);
    
            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Payment marked as cash successfully.'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to mark payment as cash.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function deleteRelatedRecords($id_pembelian) 
    {
        $this->detailPembelianModel->where('id_pembelian', $id_pembelian)->delete();
        $this->meityModel->where('id_pembelian', $id_pembelian)->delete();
        $this->pembelianModel->delete($id_pembelian);
    }
}