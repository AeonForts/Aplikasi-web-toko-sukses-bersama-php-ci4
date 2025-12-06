<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{PenjualanModel, DetailPembelianModel, DetailPenjualanModel, ViewDetailPenjualanModel,UnitBarangModel, BarangModel, CustomerModel,PaymentModel, PaymentMethodModel,StockBarangModel,MeityModel};
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class PenjualanController extends BaseController
{
    protected $db;
    protected $meityModel;
    protected $penjualanModel;
    protected $customerModel;
    protected $detailPembelianModel;
    protected $detailPenjualanModel;
    protected $viewDetailPenjualanModel;
    protected $paymentModel;
    protected $paymentMethodModel;
    protected $barangModel;
    protected $unitBarangModel;
    protected $stockBarangModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->penjualanModel = new PenjualanModel();
        $this->customerModel = new CustomerModel();
        $this->detailPembelianModel = new DetailPembelianModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->viewDetailPenjualanModel = new ViewDetailPenjualanModel();
        $this->paymentModel = new PaymentModel();
        $this->paymentMethodModel = new PaymentMethodModel();
        $this->barangModel = new BarangModel();
        $this->unitBarangModel = new UnitBarangModel();
        $this->stockBarangModel = new StockBarangModel();
        $this->meityModel = new MeityModel();
    }

    public function list()
    {
        $data = [
            'penjualan' => $this->penjualanModel->paginate(10),
            'pager' => $this->penjualanModel->pager,
            'tipeBarangList' => $this->barangModel->findAll()
        ];

        return view('pages/admin/penjualan/list', $data);
    }

    public function detail($id)
    {
        $data['vw_detail_penjualan'] = $this->viewDetailPenjualanModel->getDetailByPenjualanId($id);
        return view('pages/admin/penjualan/detail', $data);
    }

    public function save()
    {
        $this->db->transStart();
        
        try {

            $nama_customer = $this->request->getPost('nama_customer');
    
            // Backend validation
            if (empty($nama_customer)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Nama Customer is required.'
                ]);
            }
            // Check if cart data is provided
            if ($this->request->getPost('cart')) {
                $cartItems = $this->request->getPost('cart');
                foreach ($cartItems as $item) {
                    // Ensure all required keys exist with default values
                    $input = [
                        'nama_customer' => $this->request->getPost('nama_customer'),
                        'id_tipe' => $item['id_tipe'] ?? null, 
                        'id_unit' => $item['id_unit'] ?? null,
                        'jumlah_keluar' => $item['quantity'] ?? 0,
                        'harga_jual' => $item['price'] ?? 0,
                        'jumlah' => $this->request->getPost('jumlah') ?? 0,
                        'id_method' => $this->request->getPost('id_method') ?? null
                    ];

                    // Validate input before processing
                    if (empty($input['id_tipe']) || empty($input['id_unit']) || empty($input['nama_customer'])) {
                        throw new Exception("Missing product type or unit information");
                    }

                    $customer = $this->getOrCreateCustomer($input['nama_customer']);
                    $pembelian = $this->getLatestPembelian($input['id_tipe']);
                    
                    $totals = $this->calculateTotals($input['jumlah_keluar'], $input['harga_jual'], $pembelian['harga_modal_barang']);
                    $id_penjualan = $this->updateOrCreatePenjualan($input['id_tipe'], $input['jumlah_keluar'], $totals);
                    
                    // Prepare stock input
                    $stockInput = [
                        'id_tipe' => $input['id_tipe'],
                        'barang_masuk' => 0,
                        'barang_keluar' => $input['jumlah_keluar']
                    ];
                    
                    $this->insertDetailPenjualan($id_penjualan, $customer['id_customer'], $input, $pembelian['id_pembelian']);
                    $this->updateOrCreateStock($stockInput);

                    // Update terkumpul based on total_harga_modal using LIFO
                    $this->meityModel->updateLatestTerkumpul($totals['total_harga_modal']);

                    // $receiptData = $this->generateReceiptData($cartItems, $totals, $this->request->getPost('nama_customer'));
                }
            } else {
                // Existing save logic for single item
                $input = $this->validateInput();
                $customer = $this->getOrCreateCustomer($input['nama_customer']);
                $pembelian = $this->getLatestPembelian($input['id_tipe']);
                
                $totals = $this->calculateTotals($input['jumlah_keluar'], $input['harga_jual'], $pembelian['harga_modal_barang']);
                $id_penjualan = $this->updateOrCreatePenjualan($input['id_tipe'], $input['jumlah_keluar'], $totals);
                
                // Prepare stock input
                $stockInput = [
                    'id_tipe' => $input['id_tipe'],
                    'barang_masuk' => 0,
                    'barang_keluar' => $input['jumlah_keluar']
                ];
                
                $this->insertDetailPenjualan($id_penjualan, $customer['id_customer'], $input, $pembelian['id_pembelian']);
                $this->updateOrCreateStock($stockInput);

                // Update terkumpul based on total_harga_modal using LIFO
                $this->meityModel->updateLatestTerkumpul($totals['total_harga_modal']);

                // $receiptData = $this->generateReceiptData($cartItems, $totals, $this->request->getPost('nama_customer'));
            }

            $this->db->transComplete();
            // $receiptHtml = $this->loadReceiptHtml($receiptData);
            // return $this->response->setJSON([
            //     'status' => 'success'
            // //     'receiptHtml' => $receiptHtml // Pass the receipt HTML in the response
            // ]);
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data saved successfully.'
            ]);
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    
    public function getTipeBarang()
    {
        $barangModel = new BarangModel();
        $unitBarangModel = new UnitBarangModel();
        
        $data = $barangModel->findAll();
        $options = [];
        
        foreach ($data as $item) {
            // Get units for this type
            $units = $unitBarangModel->getUnitsByTipeId($item['id_tipe']);
            
            $unitOptions = [];
            foreach ($units as $unit) {
                $unitOptions[] = [
                    'id_unit' => $unit['id_unit'],
                    'tipe_unit' => $unit['tipe_unit'],
                    'standar_jumlah_barang' => $unit['standar_jumlah_barang'],
                    'standar_harga_jual' => $unit['standar_harga_jual']
                ];
            }
            
            $options[] = [
                'id' => (int) $item['id_tipe'],
                'jenis_barang' => $item['jenis_barang'],
                'satuan_dasar' => $item['satuan_dasar'],
                'units' => $unitOptions
            ];
        }
        
        return $this->response->setJSON($options);
    }

    private function validateInput()
    {
        $input = $this->request->getPost([
            'nama_customer', 'id_tipe', 'jumlah_keluar', 'harga_jual', 'id_method', 'jumlah','id_unit' // Added 'jumlah'
        ]);
    
        if (empty($input['nama_customer']) || empty($input['id_tipe']) || empty($input['jumlah_keluar']) || empty($input['id_method']) || empty($input['jumlah'] || empty($input['id_unit']))) { // Check for 'jumlah'
            throw new Exception("Customer name, product type, quantity, payment method, and amount are required.");
        }
    
        return $input;
    }

    // private function getOrCreateCustomer($nama_customer) 

    // {
    //     // Check if customer already exists
    //     $existingCustomer = $this->customerModel
    //         ->where('nama_customer', $nama_customer)
    //         ->first();
            
    //     if ($existingCustomer) {
    //         return $existingCustomer['id_customer']; // Return existing customer's ID
    //     }
    //     // Insert new customer if not found
    //     $this->customerModel->insert(['nama_customer' => $nama_customer]);
    //     return $this->customerModel->insertID(); // Return new customer's ID

    // }
    private function getOrCreateCustomer($nama_customer)
    {
        $customer = $this->customerModel->where('nama_customer', $nama_customer)->first();
        if (!$customer) {
            $this->customerModel->insert(['nama_customer' => $nama_customer]);
            $customer = ['id_customer' => $this->customerModel->getInsertID()];
        }
        return $customer;
    }

    private function getLatestPembelian($id_tipe)
    {
        // Debugging: Log the id_tipe being processed
        log_message('debug', 'Processing id_tipe: ' . $id_tipe);

        $pembelian = $this->detailPembelianModel
            ->join('tbl_pembelian tp', 'tbl_detail_pembelian.id_pembelian = tp.id_pembelian')
            ->where('id_tipe', $id_tipe)
            ->orderBy('tp.tgl_masuk', 'DESC')
            ->first();

        // Check if the query returned false
        if ($pembelian === false) {
            throw new Exception("Query failed: No stock available for this item type or invalid query.");
        }

        if (!$pembelian) {
            throw new Exception("No stock available for this item type.");
        }

        return $pembelian;
    }

    private function calculateTotals($jumlah_keluar, $harga_jual, $harga_modal_barang)
    {
        $total_harga_jual = $harga_jual * $jumlah_keluar;
        $total_harga_modal = $harga_modal_barang * $jumlah_keluar;
        return [
            'total_harga_jual' => $total_harga_jual,
            'total_harga_modal' => $total_harga_modal,
            'total_untung' => $total_harga_jual - $total_harga_modal
        ];
    }

    private function updateOrCreatePenjualan($id_tipe, $jumlah_keluar, $totals, $id_unit = null)
    {
        // First, check if there's an existing record for this id_tipe
        $existingRecord = $this->penjualanModel->where('id_tipe', $id_tipe)
            ->orderBy('tgl_penjualan', 'DESC')
            ->first();
    
        // Calculate cumulative jumlah_barang_keluar
        $total_barang_keluar = $existingRecord 
            ? $existingRecord['total_barang_keluar'] + $jumlah_keluar 
            : $jumlah_keluar;
    
        $penjualanData = [
            'tgl_penjualan' => date('Y-m-d'),
            'id_tipe' => $id_tipe,
            'total_harga_jual' => $totals['total_harga_jual'],
            'total_harga_modal' => $totals['total_harga_modal'],
            'total_untung' => $totals['total_untung'],
            'total_barang_keluar' => $total_barang_keluar, // Now cumulative
        ];
    
        // Insert the record and get the ID
        $id_penjualan = $this->penjualanModel->insert($penjualanData);
    
        // Check if the insertion was successful
        if (!$id_penjualan) {
            throw new Exception("Failed to create penjualan record.");
        }
    
        return $this->penjualanModel->getInsertID();
    }



    private function insertDetailPenjualan($id_penjualan, $id_customer, $input, $id_pembelian)
    {
        // Insert payment record
        $id_payment = $this->insertPayment($input['jumlah'], $input['id_method'], $id_customer, $id_penjualan); 
    
        $this->detailPenjualanModel->insert([
            'id_customer' => $id_customer,
            'id_penjualan' => $id_penjualan,
            'jumlah_keluar' => $input['jumlah_keluar'],
            'harga_jual' => $input['harga_jual'],
            'id_pembelian' => $id_pembelian,
            'id_tipe' => $input['id_tipe'],
            'id_unit' => $input['id_unit'], 
            'id_payment' => $id_payment
        ]);
    }

    private function calculateStock($input)
    {
        // Check if input has required fields
        if (!isset($input['id_tipe']) || !isset($input['barang_masuk']) || !isset($input['barang_keluar'])) {
            throw new \Exception('Missing required input for stock calculation');
        }
    
        // Retrieve the latest stock for this product type
        $latestStock = $this->stockBarangModel
            ->where('id_tipe', $input['id_tipe'])
            ->orderBy('tgl_stock', 'DESC')
            ->first();
    
        // Calculate current stock
        $currentStock = $latestStock 
            ? $latestStock['stock_barang'] + $input['barang_masuk'] - $input['barang_keluar']
            : $input['barang_masuk'] - $input['barang_keluar'];
    
        return [
            'id_tipe' => $input['id_tipe'],
            'current_stock' => $currentStock,
            'barang_masuk' => $input['barang_masuk'],
            'barang_keluar' => $input['barang_keluar']
        ];
    }
    
    private function updateOrCreateStock($input)
    {
        // Validate input
        if (!isset($input['id_tipe'])) {
            throw new \Exception('Missing id_tipe for stock creation.');
        }
    
        // Calculate stock
        $stockData = $this->calculateStock($input);
    
        try {
            // Check if a stock record for this type and date already exists
            $existingStock = $this->stockBarangModel
                ->where('id_tipe', $input['id_tipe'])
                ->where('tgl_stock', date('Y-m-d'))
                ->first();
    
            if ($existingStock) {
                // Update existing stock record
                $this->stockBarangModel->update($existingStock['id_stock'], [
                    'stock_barang' => $stockData['current_stock'],
                    'barang_masuk' => $existingStock['barang_masuk'] + $stockData['barang_masuk'],
                    'barang_keluar' => $existingStock['barang_keluar'] + $stockData['barang_keluar']
                ]);
    
                return $existingStock['id_stock'];
            } else {
                // Insert new stock record
                return $this->stockBarangModel->insert([
                    'id_tipe' => $input['id_tipe'],
                    'tgl_stock' => date('Y-m-d'),
                    'stock_barang' => $stockData['current_stock'],
                    'barang_masuk' => $stockData['barang_masuk'],
                    'barang_keluar' => $stockData['barang_keluar']
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to create/update stock: ' . $e->getMessage());
            throw $e;
        }
    }

    private function insertPayment($jumlah, $id_method, $id_customer, $id_penjualan)
    {
        $this->paymentModel->insert([
            'id_customer' => $id_customer, // Now this variable is available
            'id_method' => $id_method,
            'tgl' => date('Y-m-d'),
            'jumlah' => $jumlah
        ]);
        return $this->paymentModel->getInsertID();
    }

    public function updateLatestTerkumpul($totalHargaModal)
    {
        // Fetch the latest record
        $latestRecord = $this->getLatestMeity();

        if (!$latestRecord) {
            // If no record exists, you can optionally insert a new one
            return $this->insert([
                'terkumpul' => $totalHargaModal,
                'status' => 'active', // Adjust based on your logic
            ]);
        }

        $idMeity = $latestRecord['id_meity'];
        $currentTerkumpul = $latestRecord['terkumpul'] ?? 0;

        // Calculate the new cumulative value
        $newTerkumpul = $currentTerkumpul + $totalHargaModal;

        // Update the record
        return $this->update($idMeity, ['terkumpul' => $newTerkumpul]);
    }

    private function successResponse()
    {
        return $this->response->setJSON([
            'status' => 'success',  // Ensure 'status' is present
            'message' => 'Transaction successfully recorded.'
        ]);
    }
    

    private function errorResponse($message)
    {
        return $this->response->setJSON([
            'status' => 'error',  // Ensure 'status' is present
            'message' => $message
        ]);
    }
    
}
