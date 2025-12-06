<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{InvoiceModel,InvoiceItemModel,PenjualanModel, DetailPembelianModel, DetailPenjualanModel, ViewDetailPenjualanModel,UnitBarangModel, BarangModel, CustomerModel,PaymentModel, PaymentMethodModel,StockBarangModel,MeityModel};
use App\Services\{StockService,CustomerService,PaymentService,PenjualanService,TipeBarangService};
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
    protected $invoiceModel;
    protected $invoiceItemModel;

    #Service (Modular)
    protected $stockService;
    protected $customerService;
    protected $paymentService;
    protected $penjualanService;
    protected $tipeBarangService;

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
        $this->invoiceModel = new InvoiceModel();
        $this->invoiceItemModel = new InvoiceItemModel();

        #Service Modular
        $this->stockService = new StockService();
        $this->customerService = new CustomerService();
        $this->paymentService = new PaymentService();
        $this->penjualanService = new PenjualanService();
        $this->tipeBarangService = new TipeBarangService();
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

    public function getDatatables()
    {
        $request = service('request');
        $penjualanModel = new PenjualanModel();

        // Get parameters for DataTables
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        
        // Get start and end date filters if provided
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');

        // Base query with join to get barang details if needed
        $query = $penjualanModel
            ->select('tbl_penjualan.*, tb.jenis_barang')
            ->join('tbl_tipe_barang tb', 'tbl_penjualan.id_tipe = tb.id_tipe', 'left')
            ->orderBy('tgl_penjualan', 'DESC');

        // Apply date filter if dates are provided
        if (!empty($startDate) && !empty($endDate)) {
            $query->where('tgl_penjualan >=', $startDate)
                ->where('tgl_penjualan <=', $endDate);
        }

        // Apply search if search value exists
        if (!empty($searchValue)) {
            $query->groupStart()
                ->like('tgl_penjualan', $searchValue)
                ->orLike('tb.jenis_barang', $searchValue)
                ->orLike('total_barang_keluar', $searchValue)
                ->orLike('total_harga_jual', $searchValue)
                ->groupEnd();
        }

        // Get total records
        $totalRecords = $penjualanModel->countAllResults(false);

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
                    'tgl_penjualan' => date('d-m-Y', strtotime($item['tgl_penjualan'])),
                    'jenis_barang' => $item['jenis_barang'] ?? 'N/A',
                    'total_barang_keluar' => number_format($item['total_barang_keluar'], 2, ',', '.'),
                    'total_harga_modal' => 'Rp. ' . number_format($item['total_harga_modal'], 2, ',', '.'),
                    'total_harga_jual' => 'Rp. ' . number_format($item['total_harga_jual'], 2, ',', '.'),
                    'total_untung' => 'Rp. ' . number_format($item['total_untung'], 2, ',', '.'),
                    'action' => '<a href="' . base_url('admin/penjualan/detail/' . $item['id_penjualan']) . '" class="btn btn-info text-white">Detail</a>'
                ];
            }, $data)
        ];

        return $this->response->setJSON($response);
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
            // Check if cart data is provided 
            if ($this->request->getPost('cart')) { 
                $cartItems = $this->request->getPost('cart'); 
                $total = 0; 
                $processedItems = []; 
    
                // Enhanced calculation to match frontend logic
                foreach ($cartItems as $item) { 
                    // Get additional details for precise calculation
                    $hargaJual = floatval($item['price']);
                    $standarJumlah = floatval($item['standar_jumlah_barang'] ?? 1);
                    $uang = floatval($this->request->getPost('jumlah'));
    
                    // Calculate maximum purchasable units
                    $maxUnits = floor($uang / $hargaJual);
                    
                    // Calculate precise quantity and total
                    $quantity = $maxUnits * $standarJumlah;
                    $itemTotal = $maxUnits * $hargaJual;
    
                    $processedItems[] = [ 
                        'name' => $item['name'], 
                        'quantity' => $quantity, 
                        'price' => $hargaJual, 
                        'total' => $itemTotal, 
                        'id_tipe' => $item['id_tipe'], 
                        'id_unit' => $item['id_unit'],
                        'standar_jumlah' => $standarJumlah
                    ]; 
                    $total += $itemTotal; 
                } 
    
                // Precise change calculation
                $paymentAmount = floatval($this->request->getPost('jumlah'));
                $changeAmount = max(0, $paymentAmount - $total);
    
                // Prepare invoice data with precise calculations
                $invoiceData = [ 
                    'customer_name' => $this->request->getPost('nama_customer') ?? 'Eceran', 
                    'total_amount' => $total, 
                    'payment_amount' => $paymentAmount, 
                    'change_amount' => $changeAmount, 
                    'payment_method' => $this->request->getPost('id_method'), 
                    'invoice_date' => date('Y-m-d H:i:s') 
                ]; 
    
                // Insert invoice 
                $invoiceModel = new InvoiceModel(); 
                $invoiceId = $invoiceModel->insert($invoiceData); 
    
                // Process each cart item 
                foreach ($processedItems as $item) { 
                    // Process sale with precise calculations
                    $input = [ 
                        'nama_customer' => $invoiceData['customer_name'], 
                        'id_tipe' => $item['id_tipe'], 
                        'id_unit' => $item['id_unit'], 
                        'jumlah_keluar' => $item['quantity'], 
                        'harga_jual' => $item['price'], 
                        'jumlah' => $invoiceData['payment_amount'], 
                        'id_method' => $this->request->getPost('id_method'),
                        'standar_jumlah' => $item['standar_jumlah']
                    ]; 
    
                    $customer = $this->customerService->getOrCreateCustomer($input['nama_customer']); 
                    $pembelian = $this->penjualanService->getLatestPembelian($input['id_tipe']); 
    
                    $totals = $this->penjualanService->calculateTotals(
                        $input['jumlah_keluar'], 
                        $input['harga_jual'], 
                        $pembelian['harga_modal_barang']
                    ); 
    
                    // Determine payment status 
                    $status = $input['id_method'] == 1 ? 0 : 1; // 0 for Piutang, 1 for Lunas 
    
                    // Always create or update penjualan record based on status 
                    $id_penjualan = $this->penjualanService->updateOrCreatePenjualan( 
                        $input['id_tipe'], 
                        $input['jumlah_keluar'], 
                        $totals, 
                        $status 
                    ); 
    
                    // Insert detail penjualan with the appropriate status 
                    $this->penjualanService->insertDetailPenjualan( 
                        $id_penjualan, 
                        $customer['id_customer'], 
                        $input, 
                        $pembelian['id_pembelian'] 
                    ); 
    
                    // Reduce stock 
                    $stockInput = [ 
                        'id_tipe' => $input['id_tipe'], 
                        'barang_masuk' => 0, 
                        'barang_keluar' => $input['jumlah_keluar'] 
                    ]; 
    
                    $this->stockService->updateOrCreateStock($stockInput); 
    
                    // Only update terkumpul for lunas transactions 
                    if ($status === 1) { 
                        $this->penjualanService->updateLatestTerkumpul( 
                            $pembelian['id_pembelian'], 
                            $totals['total_harga_modal'], 
                            $input['id_tipe'] 
                        ); 
                    } 
    
                    // Insert invoice items 
                    $invoiceItemModel = new InvoiceItemModel(); 
                    $invoiceItemModel->insert([ 
                        'id_invoice' => $invoiceId, 
                        'id_tipe' => $item['id_tipe'], 
                        'id_unit' => $item['id_unit'], 
                        'quantity' => $item['quantity'], 
                        'price' => $item['price'], 
                        'total' => $item['total'],
                        'id_method' => $input['id_method']
                    ]); 
                } 
    
                $this->db->transComplete(); 
    
                // Prepare receipt data 
                $receiptData = [ 
                    'invoiceId' => $invoiceId, 
                    'storeName' => 'Toko Sukses Bersama', 
                    'address' => 'Jl. Bantarkemang No. 5c', 
                    'phone' => '0895346228087', 
                    'date' => date('d/m/Y H:i:s'), 
                    'customerName' => $invoiceData['customer_name'], 
                    'items' => $processedItems, 
                    'total' => $total, 
                    'payment' => $invoiceData['payment_amount'], 
                    'change' => $invoiceData['change_amount'], 
                    'paymentMethod' => $this->getPaymentMethodName($this->request->getPost('id_method')) 
                ]; 
    
                return $this->response->setJSON([ 
                    'status' => 'success', 
                    'invoiceId' => $invoiceId, 
                    'receiptData' => $receiptData 
                ]); 
            } 
    
            // If no cart items 
            return $this->response->setJSON([ 
                'status' => 'error', 
                'message' => 'No items in cart' 
            ]); 
    
        } catch (Exception $e) { 
            $this->db->transRollback(); 
            return $this->response->setJSON([ 
                'status' => 'error', 
                'message' => $e->getMessage() 
            ]); 
        } 
    } 
    
    // Helper method to get payment method name 
    private function getPaymentMethodName($methodId) 
    { 
        switch ($methodId) { 
            case 1: 
                return 'Piutang'; 
            case 2: 
                return 'Transfer'; 
            default: 
                return 'Cash'; 
        } 
    }

    public function printInvoice($invoiceId)
    {
        try {
            $invoiceModel = new InvoiceModel();
            $invoiceItemModel = new InvoiceItemModel();
    
            // Fetch invoice details with comprehensive error handling
            $invoice = $invoiceModel->find($invoiceId);
            if (!$invoice) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invoice not found'
                ])->setStatusCode(404);
            }
    
            // Fetch invoice items with more detailed information
            $invoiceItems = $invoiceItemModel
                ->select('tbl_invoice_items.*, tb.jenis_barang, ub.tipe_unit')
                ->join('tbl_tipe_barang tb', 'tbl_invoice_items.id_tipe = tb.id_tipe')
                ->join('tbl_unit_barang ub', 'tbl_invoice_items.id_unit = ub.id_unit')
                ->where('id_invoice', $invoiceId)
                ->findAll();
    
            // Prepare receipt data with more comprehensive information
            $receiptData = [
                'invoice' => $invoice,
                'items' => $invoiceItems,
                'storeName' => 'Toko Sukses Bersama',
                'storeAddress' => 'Jl. Bantarkemang No. 5c',
                'storePhone' => '0895346228087',
                'totalAmount' => array_sum(array_column($invoiceItems, 'total'))
            ];
    
            // Load view for receipt
            $receiptHtml = view('pages/admin/penjualan/invoice_receipt', $receiptData);
    
            return $this->response->setJSON([
                'status' => 'success',
                'invoiceHtml' => $receiptHtml
            ]);
    
        } catch (Exception $e) {
            log_message('error', 'Print Invoice Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses invoice'
            ])->setStatusCode(500);
        }
    }

    public function getChartData()
    {
        try {
            // Use appropriate model for Penjualan
            $penjualanModel = new PenjualanModel();
            
            // Get parameters
            $id_tipe = $this->request->getGet('id_tipe');
            $chart_type = $this->request->getGet('chart_type') ?? 'total_barang_keluar';
            $year = $this->request->getGet('year') ?? date('Y');
    
            // Start base query
            $query = $penjualanModel->select(
                'MONTH(tgl_penjualan) as month, 
                 COALESCE(SUM(' . $chart_type . '), 0) as total'
            )
            ->where('YEAR(tgl_penjualan)', $year);
    
            // Add id_tipe filter if provided
            if ($id_tipe !== null && $id_tipe !== '') {
                $query->where('id_tipe', $id_tipe);
            }
    
            $chartData = $query->groupBy('MONTH(tgl_penjualan)')
                               ->orderBy('month')
                               ->findAll();
    
            // Ensure all months are represented
            $completeChartData = $this->fillMissingMonths($chartData);
    
            return $this->response->setJSON($completeChartData);
        } catch (\Exception $e) {
            log_message('error', 'Chart Data Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    
    
    // Helper method to ensure all months are represented
    private function fillMissingMonths($data)
    {
        $completeData = [];
        
        // Initialize all months with zero
        for ($month = 1; $month <= 12; $month++) {
            $completeData[] = [
                'month' => $month,
                'total' => 0
            ];
        }
    
        // Replace with actual data where available
        foreach ($data as $item) {
            $index = $item['month'] - 1;
            $completeData[$index]['total'] = $item['total'];
        }
    
        return $completeData;
    }

    // public function save()
    // {
    //     $this->db->transStart();
        
    //     try {

    //         $nama_customer = $this->request->getPost('nama_customer');
    
    //         // Backend validation
    //         if (empty($nama_customer)) {
    //             return $this->response->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'Nama Customer is required.'
    //             ]);
    //         }
    //         // Check if cart data is provided
    //         if ($this->request->getPost('cart')) {
    //             $cartItems = $this->request->getPost('cart');
    //             foreach ($cartItems as $item) {
    //                 // Ensure all required keys exist with default values
    //                 $input = [
    //                     'nama_customer' => $this->request->getPost('nama_customer'),
    //                     'id_tipe' => $item['id_tipe'] ?? null, 
    //                     'id_unit' => $item['id_unit'] ?? null,
    //                     'jumlah_keluar' => $item['quantity'] ?? 0,
    //                     'harga_jual' => $item['price'] ?? 0,
    //                     'jumlah' => $this->request->getPost('jumlah') ?? 0,
    //                     'id_method' => $this->request->getPost('id_method') ?? null
    //                 ];

    //                 // Validate input before processing
    //                 if (empty($input['id_tipe']) || empty($input['id_unit']) || empty($input['nama_customer'])) {
    //                     throw new Exception("Missing product type or unit information");
    //                 }

    //                 $customer = $this->customerService->getOrCreateCustomer($input['nama_customer']);
    //                 $pembelian = $this->penjualanService->getLatestPembelian($input['id_tipe']);
                    
    //                 $totals = $this->penjualanService->calculateTotals($input['jumlah_keluar'], $input['harga_jual'], $pembelian['harga_modal_barang']);
    //                 $id_penjualan = $this->penjualanService->updateOrCreatePenjualan($input['id_tipe'], $input['jumlah_keluar'], $totals);
                    
    //                 // Prepare stock input
    //                 $stockInput = [
    //                     'id_tipe' => $input['id_tipe'],
    //                     'barang_masuk' => 0,
    //                     'barang_keluar' => $input['jumlah_keluar']
    //                 ];
                    
    //                 $this->penjualanService->insertDetailPenjualan($id_penjualan, $customer['id_customer'], $input, $pembelian['id_pembelian']);
    //                 $this->stockService->updateOrCreateStock($stockInput);

    //                 // Update terkumpul based on total_harga_modal using LIFO
    //                 // $this->penjualanService->updateLatestTerkumpul($totals['total_harga_modal']);
    //                 $this->penjualanService->updateLatestTerkumpul(
    //                     $pembelian['id_pembelian'],  // Pass the id_pembelian from the latest purchase
    //                     $totals['total_harga_modal'], 
    //                     $input['id_tipe']
    //                 );
    //                 // $receiptData = $this->generateReceiptData($cartItems, $totals, $this->request->getPost('nama_customer'));
    //             }
    //         } else {
    //             // Existing save logic for single item
    //             $input = $this->validateInput();
    //             $customer = $this->customerService->getOrCreateCustomer($input['nama_customer']);
    //             $pembelian = $this->penjualanService->getLatestPembelian($input['id_tipe']);
                
    //             $totals = $this->penjualanService->calculateTotals($input['jumlah_keluar'], $input['harga_jual'], $pembelian['harga_modal_barang']);
    //             $id_penjualan = $this->penjualanService->updateOrCreatePenjualan($input['id_tipe'], $input['jumlah_keluar'], $totals);
                
    //             // Prepare stock input
    //             $stockInput = [
    //                 'id_tipe' => $input['id_tipe'],
    //                 'barang_masuk' => 0,
    //                 'barang_keluar' => $input['jumlah_keluar']
    //             ];
                
    //             $this->penjualanService->insertDetailPenjualan($id_penjualan, $customer['id_customer'], $input, $pembelian['id_pembelian']);
    //             $this->stockService->updateOrCreateStock($stockInput);
                
    //             // Update terkumpul based on total_harga_modal using LIFO
    //             // $this->penjualanService->updateLatestTerkumpul($totals['total_harga_modal']);
    //             $this->penjualanService->updateLatestTerkumpul(
    //                 $pembelian['id_pembelian'],  // Pass the id_pembelian from the latest purchase
    //                 $totals['total_harga_modal'], 
    //                 $input['id_tipe']
    //             );
    //             // $receiptData = $this->generateReceiptData($cartItems, $totals, $this->request->getPost('nama_customer'));
    //         }

    //         $this->db->transComplete();
    //         // $receiptHtml = $this->loadReceiptHtml($receiptData);
    //         // return $this->response->setJSON([
    //         //     'status' => 'success'
    //         // //     'receiptHtml' => $receiptHtml // Pass the receipt HTML in the response
    //         // ]);
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Data saved successfully.'
    //         ]);
    //     } catch (Exception $e) {
    //         $this->db->transRollback();
    //         return $this->errorResponse($e->getMessage());
    //     }
    // }

    // public function markLunas()
    // {
    //     $this->db->transStart();
    
    //     try {
    //         $idDetailPenjualan = $this->request->getPost('id_detail_penjualan');
    
    //         // Fetch the detail penjualan with a join to get all necessary details
    //         $detailPenjualan = $this->detailPenjualanModel
    //             ->select('tbl_detail_penjualan.*, tdp.harga_modal_barang')
    //             ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->first();
    
    //         if (!$detailPenjualan) {
    //             throw new Exception("Detail penjualan not found");
    //         }
    
    //         // Calculate total harga modal and total harga jual
    //         $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
    //         $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
    
    //         // Find the latest penjualan record for this type
    //         $latestPenjualan = $this->penjualanModel
    //             ->where('id_tipe', $detailPenjualan['id_tipe'])
    //             ->orderBy('id_penjualan', 'DESC')
    //             ->first();
    
    //         if (!$latestPenjualan) {
    //             throw new Exception("No Penjualan record found");
    //         }
    
    //         // Update the latest penjualan record's totals
    //         $updatedTotals = [
    //             'total_harga_jual' => $latestPenjualan['total_harga_jual'] + $totalHargaJual,
    //             'total_harga_modal' => $latestPenjualan['total_harga_modal'] + $totalHargaModal,
    //             'total_untung' => $latestPenjualan['total_untung'] + ($totalHargaJual - $totalHargaModal),
    //             'total_barang_keluar' => $latestPenjualan['total_barang_keluar'] + $detailPenjualan['jumlah_keluar']
    //         ];
    
    //         // Log the SQL query for debugging
    //         log_message('info', 'Updating penjualan with ID: ' . $latestPenjualan['id_penjualan']);
    //         log_message('info', 'Updated totals: ' . json_encode($updatedTotals));
    
    //         // Ensure you are using the correct primary key for your table
    //         $this->penjualanModel->update($latestPenjualan['id_penjualan'], $updatedTotals);
    
    //         // Log the update for penjualan
    //         log_message('info', 'Updated penjualan totals: ' . json_encode($updatedTotals));
    
    //         // Mark the detail penjualan as Lunas
    //         $this->detailPenjualanModel
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->set(['status' => 1, 'id_penjualan' => $latestPenjualan['id_penjualan']])
    //             ->update();
    
    //         // Log the detail penjualan update
    //         log_message('info', 'Marked detail penjualan as Lunas: id_detail_penjualan = ' . $idDetailPenjualan);
    
    //         $this->db->transComplete();
    
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Payment marked as Lunas successfully.',
    //             'debug' => [
    //                 'totalHargaModal' => $totalHargaModal,
    //                 'totalHargaJual' => $totalHargaJual,
    //                 'jumlahKeluar' => $detailPenjualan['jumlah_keluar'],
    //                 'hargaModal' => $detailPenjualan['harga_modal_barang'],
    //                 'latestPenjualanId' => $latestPenjualan['id_penjualan']
    //             ]
    //         ]);
    
    //     } catch (Exception $e) {
    //         $this->db->transRollback();
    //         log_message('error', 'Mark Lunas Error: ' . $e->getMessage());
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }


    public function markLunas()
    {
        $this->db->transStart();
    
        try {
            $idDetailPenjualan = $this->request->getPost('id_detail_penjualan');
    
            // Fetch the detail penjualan with a join to get all necessary details
            $detailPenjualan = $this->detailPenjualanModel
                ->select('tbl_detail_penjualan.*, tdp.harga_modal_barang')
                ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
                ->where('id_detail_penjualan', $idDetailPenjualan)
                ->first();
    
            if (!$detailPenjualan) {
                throw new Exception("Detail penjualan not found");
            }
    
            // Calculate total harga modal and total harga jual
            $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
            $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
            $totalUntung = $totalHargaJual - $totalHargaModal;
    
            // Find the latest penjualan record for this type
            $latestPenjualan = $this->penjualanModel
                ->where('id_tipe', $detailPenjualan['id_tipe'])
                ->orderBy('id_penjualan', 'DESC')
                ->first();
    
            if (!$latestPenjualan) {
                // If no existing record, create a new one
                $newPenjualanData = [
                    'id_tipe' => $detailPenjualan['id_tipe'],
                    'tgl_penjualan' => date('Y-m-d'),
                    'total_barang_keluar' => $detailPenjualan['jumlah_keluar'],
                    'total_harga_jual' => $totalHargaJual,
                    'total_harga_modal' => $totalHargaModal,
                    'total_untung' => $totalUntung
                ];
    
                $newPenjualanId = $this->penjualanModel->insert($newPenjualanData);
                
                log_message('info', 'Created new penjualan record: ' . json_encode($newPenjualanData));
            } else {
                // Update existing record
                $updatedTotals = [
                    'total_harga_jual' => $latestPenjualan['total_harga_jual'] + $totalHargaJual,
                    'total_harga_modal' => $latestPenjualan['total_harga_modal'] + $totalHargaModal,
                    'total_untung' => $latestPenjualan['total_untung'] + $totalUntung,
                    'total_barang_keluar' => $latestPenjualan['total_barang_keluar'] + $detailPenjualan['jumlah_keluar']
                ];
    
                $this->penjualanModel
                    ->where('id_penjualan', $latestPenjualan['id_penjualan'])
                    ->set($updatedTotals)
                    ->update();
    
                log_message('info', 'Updated existing penjualan record: ' . json_encode($updatedTotals));
            }
    
            // Get the latest meity record for this type
            $latestMeityRecord = $this->meityModel
                ->where('id_tipe', $detailPenjualan['id_tipe'])
                ->orderBy('id_meity', 'DESC')
                ->first();
    
            if (!$latestMeityRecord) {
                throw new Exception("No Meity record found");
            }
    
            // Update the latest meity record's terkumpul
            $this->meityModel->update($latestMeityRecord['id_meity'], [
                'terkumpul' => $latestMeityRecord['terkumpul'] + $totalHargaModal
            ]);
    
            log_message('info', 'Updated Meity terkumpul: ' . 
                ($latestMeityRecord['terkumpul'] + $totalHargaModal));
    
            // Mark the detail penjualan as Lunas
            $this->detailPenjualanModel
                ->where('id_detail_penjualan', $idDetailPenjualan)
                ->set([
                    'status' => 1, 
                    'id_penjualan' => $latestPenjualan['id_penjualan'] ?? $newPenjualanId
                ])
                ->update();
    
            log_message('info', 'Marked detail penjualan as Lunas: id_detail_penjualan = ' . $idDetailPenjualan);
    
            $this->db->transComplete();
    
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payment marked as Lunas successfully.',
                'debug' => [
                    'totalHargaModal' => $totalHargaModal,
                    'totalHargaJual' => $totalHargaJual,
                    'totalUntung' => $totalUntung,
                    'jumlahKeluar' => $detailPenjualan['jumlah_keluar'],
                    'previousTerkumpul' => $latestMeityRecord['terkumpul'],
                    'newTerkumpul' => $latestMeityRecord['terkumpul'] + $totalHargaModal
                ]
            ]);
    
        } catch (Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Mark Lunas Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
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

    public function deleteDetailPenjualan()
    {
        $this->db->transStart();
    
        try {
            $idDetailPenjualan = $this->request->getPost('id_detail_penjualan');
    
            // Validate input
            if (empty($idDetailPenjualan)) {
                throw new Exception("Invalid detail penjualan ID");
            }
    
            // Manually query the detail penjualan instead of using find()
            $detailPenjualan = $this->db->table('tbl_detail_penjualan')
                ->select('tbl_detail_penjualan.*, tdp.harga_modal_barang')
                ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
                ->where('id_detail_penjualan', $idDetailPenjualan)
                ->get()
                ->getRowArray();
    
            // Debug logging
            log_message('error', 'Detail Penjualan Query Result: ' . json_encode($detailPenjualan));
    
            // Additional null check
            if (empty($detailPenjualan)) {
                throw new Exception("No detail penjualan found for ID: " . $idDetailPenjualan);
            }
    
            // Ensure required fields exist
            if (!isset($detailPenjualan['jumlah_keluar']) || !isset($detailPenjualan['harga_modal_barang']) || !isset($detailPenjualan['harga_jual'])) {
                throw new Exception("Missing required fields in detail penjualan");
            }
    
            // Calculate total harga modal to subtract
            $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
            $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
    
            // Find the latest meity record for this type
            $latestMeityRecord = $this->db->table('tbl_meity')
                ->where('id_tipe', $detailPenjualan['id_tipe'])
                ->orderBy('id_meity', 'DESC')
                ->get()
                ->getRowArray();
    
            // Debug logging for meity record
            log_message('error', 'Latest Meity Record: ' . json_encode($latestMeityRecord));
    
            if ($latestMeityRecord) {
                // Subtract from terkumpul
                $newTerkumpul = max(0, $latestMeityRecord['terkumpul'] - $totalHargaModal);
                
                // Update meity record
                $this->db->table('tbl_meity')
                    ->where('id_meity', $latestMeityRecord['id_meity'])
                    ->update(['terkumpul' => $newTerkumpul]);
            }
    
            // Find penjualan record
            $penjualan = $this->db->table('tbl_penjualan')
                ->where('id_penjualan', $detailPenjualan['id_penjualan'])
                ->get()
                ->getRowArray();
    
            if ($penjualan) {
                $updatedTotalHargaJual = max(0, $penjualan['total_harga_jual'] - $totalHargaJual);
                $updatedTotalHargaModal = max(0, $penjualan['total_harga_modal'] - $totalHargaModal);
                $updatedTotalUntung = $updatedTotalHargaJual - $updatedTotalHargaModal;
                $updatedTotalBarangKeluar = max(0, $penjualan['total_barang_keluar'] - $detailPenjualan['jumlah_keluar']);
    
                // Update penjualan record
                $this->db->table('tbl_penjualan')
                    ->where('id_penjualan', $penjualan['id_penjualan'])
                    ->update([
                        'total_harga_jual' => $updatedTotalHargaJual,
                        'total_harga_modal' => $updatedTotalHargaModal,
                        'total_untung' => $updatedTotalUntung,
                        'total_barang_keluar' => $updatedTotalBarangKeluar
                    ]);
            }
    
            // Delete associated payment
            if (!empty($detailPenjualan['id_payment'])) {
                $this->db->table('tbl_payment')
                    ->where('id_payment', $detailPenjualan['id_payment'])
                    ->delete();
            }
    
            // Delete the detail penjualan
            $this->db->table('tbl_detail_penjualan')
                ->where('id_detail_penjualan', $idDetailPenjualan)
                ->delete();
    
            $this->db->transComplete();
    
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Detail penjualan deleted successfully.',
                'debug' => [
                    'totalHargaModal' => $totalHargaModal,
                    'totalHargaJual' => $totalHargaJual,
                    'jumlahKeluar' => $detailPenjualan['jumlah_keluar']
                ]
            ]);
    
        } catch (Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Delete Detail Penjualan Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
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


    
public function save() 
{
    // Start the transaction
    $this->db->transStart();

    try {
        // Check if cart data is provided
        if ($this->request->getPost('cart')) {
            $cartItems = $this->request->getPost('cart');
            $total = 0;
            $processedItems = [];
            
            // Cumulative payment amount
            $cumulativePaymentAmount = floatval($this->request->getPost('jumlah') ?? 0);

            foreach ($cartItems as $item) {
                // Fetch inputs
                $hargaJual = floatval($item['price']);
                $standarJumlah = floatval($item['standar_jumlah_barang'] ?? 1);
                
                // Debug: Check incoming data
                log_message('debug', 'Item: ' . print_r($item, true));
                log_message('debug', 'Uang: ' . $cumulativePaymentAmount . ', Harga Jual: ' . $hargaJual . ', Standar Jumlah: ' . $standarJumlah);

                // Use the manually provided quantity if available
                $customQuantity = isset($item['quantity']) ? floatval($item['quantity']) : null;

                // Prioritize custom quantity, fallback to calculation if not set
                if ($customQuantity !== null) {
                    $quantity = $customQuantity;
                } else {
                    // Calculate quantity based on total payment amount and item price
                    $quantity = round(($cumulativePaymentAmount / $hargaJual) * $standarJumlah, 2);
                }

                // Debug: Check calculated quantity
                log_message('debug', 'Quantity (Custom or Calculated): ' . $quantity);

                // Calculate item total
                $itemTotal = $quantity * $hargaJual;

                // Prepare processed items for invoice
                $processedItems[] = [
                    'name' => $item['name'],
                    'quantity' => $quantity,
                    'price' => $hargaJual,
                    'total' => $itemTotal,
                    'id_tipe' => $item['id_tipe'],
                    'id_unit' => $item['id_unit'],
                    'standar_jumlah' => $standarJumlah
                ];

                // Add item total to overall total
                $total += $itemTotal;
            }

            // Debug: Total after processing all items
            log_message('debug', 'Total After Items: ' . $total);

            // Precise change calculation using cumulative payment amount
            $paymentAmount = $cumulativePaymentAmount;
            $changeAmount = max(0, $paymentAmount - $total);

            // Prepare invoice data with precise calculations
            $invoiceData = [
                'customer_name' => $this->request->getPost('nama_customer') ?? 'Eceran',
                'total_amount' => $total,
                'payment_amount' => $paymentAmount,
                'change_amount' => $changeAmount,
                'payment_method' => $this->request->getPost('id_method'),
                'invoice_date' => date('Y-m-d H:i:s')
            ];

            // Insert invoice
            $invoiceModel = new InvoiceModel();
            $invoiceId = $invoiceModel->insert($invoiceData);

            // Debug: Invoice Data and ID
            log_message('debug', 'Invoice Data: ' . print_r($invoiceData, true));
            log_message('debug', 'Invoice ID: ' . $invoiceId);

            // Process each cart item for sale and inventory
            foreach ($processedItems as $item) {
                // Process sale with precise calculations
                $input = [
                    'nama_customer' => $invoiceData['customer_name'],
                    'id_tipe' => $item['id_tipe'],
                    'id_unit' => $item['id_unit'],
                    'jumlah_keluar' => $item['quantity'],
                    'harga_jual' => $item['price'],
                    'jumlah' => $invoiceData['payment_amount'],
                    'id_method' => $this->request->getPost('id_method'),
                    'standar_jumlah' => $item['standar_jumlah']
                ];

                // Debug: Check item details before processing
                log_message('debug', 'Sale Input: ' . print_r($input, true));

                // Get or create customer
                $customer = $this->customerService->getOrCreateCustomer($input['nama_customer']);
                $pembelian = $this->penjualanService->getLatestPembelian($input['id_tipe']);

                // Calculate totals for penjualan
                $totals = $this->penjualanService->calculateTotals(
                    $input['jumlah_keluar'],
                    $input['harga_jual'],
                    $pembelian['harga_modal_barang']
                );

                // Determine payment status (0 for Piutang, 1 for Lunas)
                $status = $input['id_method'] == 1 ? 0 : 1;

                // Update or create penjualan record
                $id_penjualan = $this->penjualanService->updateOrCreatePenjualan(
                    $input['id_tipe'],
                    $input['jumlah_keluar'],
                    $totals,
                    $status
                );

                // Insert detail penjualan with the appropriate status
                $this->penjualanService->insertDetailPenjualan(
                    $id_penjualan,
                    $customer['id_customer'],
                    $input,
                    $pembelian['id_pembelian']
                );

                // Update stock after sale
                $stockInput = [
                    'id_tipe' => $input['id_tipe'],
                    'barang_masuk' => 0,
                    'barang_keluar' => $input['jumlah_keluar']
                ];
                $this->stockService->updateOrCreateStock($stockInput);

                // Only update terkumpul for 'lunas' transactions
                if ($status === 1) {
                    $this->penjualanService->updateLatestTerkumpul(
                        $pembelian['id_pembelian'],
                        $totals['total_harga_modal'],
                        $input['id_tipe']
                    );
                }

                // Insert invoice item details
                $invoiceItemModel = new InvoiceItemModel();
                $invoiceItemModel->insert([
                    'id_invoice' => $invoiceId,
                    'id_tipe' => $item['id_tipe'],
                    'id_unit' => $item['id_unit'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                    'id_method' => $input['id_method']
                ]);
            }

            // Complete the transaction
            $this->db->transComplete();

            // Prepare receipt data for the response
            $receiptData = [
                'invoiceId' => $invoiceId,
                'storeName' => 'Toko Sukses Bersama',
                'address' => 'Jl. Bantarkemang No. 5c',
                'phone' => '0895346228087',
                'date' => date('d/m/Y H:i:s'),
                'customerName' => $invoiceData['customer_name'],
                'items' => $processedItems,
                'total' => $total,
                'payment' => $invoiceData['payment_amount'],
                'change' => $invoiceData['change_amount'],
                'paymentMethod' => $this->getPaymentMethodName($this->request->getPost('id_method'))
            ];

            // Return the success response with receipt data
            return $this->response->setJSON([
                'status' => 'success',
                'invoiceId' => $invoiceId,
                'receiptData' => $receiptData
            ]);
        }

        // If no cart items
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No items in cart'
        ]);

    } catch (Exception $e) {
        // Rollback the transaction on error
        $this->db->transRollback();

        // Return error response
        return $this->response->setJSON([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}