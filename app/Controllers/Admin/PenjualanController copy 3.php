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

    public function piutangList()
    {
        $viewDetailPenjualanModel = new ViewDetailPenjualanModel();
        
        // Fetch unpaid (status 0) transactions with both purchase and sale dates
        $data['vw_detail_penjualan'] = $viewDetailPenjualanModel
            ->select('vw_detail_penjualan.*, 
                      tp.tgl_masuk, 
                      tbl_penjualan.tgl_penjualan,
                      DATEDIFF(CURDATE(), tbl_penjualan.tgl_penjualan) as days_overdue')  // Changed this line
            ->join('tbl_detail_penjualan tdp', 'vw_detail_penjualan.id_detail_penjualan = tdp.id_detail_penjualan', 'left')
            ->join('tbl_pembelian tp', 'tdp.id_pembelian = tp.id_pembelian', 'left')
            ->join('tbl_penjualan', 'vw_detail_penjualan.id_penjualan = tbl_penjualan.id_penjualan', 'left')
            ->where('vw_detail_penjualan.status', 0)
            ->orderBy('tbl_penjualan.tgl_penjualan', 'ASC')  // Optional: change order by to match new calculation
            ->findAll();
        
        return view('pages/admin/penjualan/piutang_list', $data);
    }

    private function sanitizeCurrencyInput($input) {
        $sanitizedInput = $input;
    
        $fieldsToSanitize = ['jumlah_untuk_barang', 'jumlah', 'bayar', 'kembalian'];
        
        foreach ($fieldsToSanitize as $field) {
            if (isset($input[$field])) {
                // Remove 'Rp ', thousands separator, and replace comma with dot
                $cleanValue = str_replace(['Rp ', '.', ' '], '', $input[$field]);
                $cleanValue = str_replace(',', '.', $cleanValue);
                
                // Ensure numeric value with 2 decimal places
                $sanitizedInput[$field] = floatval($cleanValue);
            }
        }
    
        return $sanitizedInput;
    }

    public function save() 
    {
        function roundToNearestFiveHundred($value) {
            // If the remainder is less than 250, round down
            // If the remainder is 250 or more, round up
            $remainder = $value % 500;
            
            if ($remainder < 250) {
                // Round down to nearest 500
                return floor($value / 500) * 500;
            } else {
                // Round up to nearest 500
                return ceil($value / 500) * 500;
            }
        }
        // Start the database transaction
        $this->db->transStart();
    
        try {
            // Check if cart data is provided
            $sanitizedInput = $this->sanitizeCurrencyInput($this->request->getPost());
            log_message('info', 'Sanitized Input: ' . json_encode($sanitizedInput));

            if ($this->request->getPost('cart')) {
                $cartItems = $this->request->getPost('cart');
                $total = 0;
                $cumulativePaymentAmount = 0;
                $processedItems = [];
                
                foreach ($cartItems as $item) {
                    // Default fallback values
                    $namaCustomer = $item['nama_customer'] ?? 'Eceran';
                    $idMethod = $item['id_method'] ?? 3; // Default to Cash
    
                    // Accumulate payment amount
                    $itemPaymentAmount = floatval($item['jumlah'] ?? 0);
                    $cumulativePaymentAmount += $itemPaymentAmount;
    
                    // Fetch and validate inputs
                    $hargaJual = floatval($item['price']);
                    $standarJumlah = floatval($item['standar_jumlah_barang'] ?? 1);
                    $quantity = floatval($item['quantity']);

                    try {
                        // Ensure $idTipe is correctly passed
                        $idTipe = $item['id_tipe'] ?? null;
                        $quantity = floatval($item['quantity']);
                    
                        $this->stockService->validateStock($idTipe, $quantity);
                    } catch (Exception $e) {
                        // If stock validation fails, throw an exception with item details
                        throw new Exception(sprintf(
                            "Stock Error for %s: %s", 
                            $item['name'] ?? 'Unknown Product', 
                            $e->getMessage()
                        ));
                    }
                
                    log_message('info', "Received date: " . $item['tgl_penjualan']);
                    $tgl_penjualan = $item['tgl_penjualan'];

                    // Calculate item total
                    // Replace the item total calculation with:
                    $itemTotal = roundToNearestFiveHundred($quantity * $hargaJual);

                    // Prepare processed items for invoice
                    $processedItems[] = [
                        'name' => $item['name'],
                        'quantity' => $quantity,
                        'price' => $hargaJual,
                        'total' => $itemTotal,
                        'id_tipe' => $item['id_tipe'],
                        'id_unit' => $item['id_unit'],
                        'standar_jumlah' => $standarJumlah,
                        'nama_customer' => $namaCustomer,
                        'id_method' => $idMethod,
                        'jumlah' => $itemPaymentAmount
                    ];
    
                    // Add item total to overall total
                    $total += $itemTotal;
                }
                // Optional: Round the overall total as well
                $total = roundToNearestFiveHundred($total);
                // Calculate change (kembalian)
                $changeAmount = max(0, $cumulativePaymentAmount - $total);
    
                // Prepare invoice data
                $invoiceData = [
                    'customer_name' => $processedItems[0]['nama_customer'] ?? 'Eceran',
                    'total_amount' => $total,
                    'payment_amount' => $cumulativePaymentAmount,
                    'change_amount' => $changeAmount,
                    'payment_method' => $processedItems[0]['id_method'] ?? 3,
                    'invoice_date' => date('Y-m-d H:i:s')
                ];
    
                // Insert invoice
                $invoiceModel = new InvoiceModel();
                $invoiceId = $invoiceModel->insert($invoiceData);
    
                // Process each cart item
                foreach ($processedItems as $item) {
                    // Prepare input for sale processing
                    $input = [
                        'nama_customer' => $item['nama_customer'],
                        'id_tipe' => $item['id_tipe'],
                        'id_unit' => $item['id_unit'],
                        'jumlah_keluar' => $item['quantity'],
                        'harga_jual' => $item['price'],
                        'jumlah' => $item['jumlah'],
                        'id_method' => $item['id_method'],
                        'standar_jumlah' => $item['standar_jumlah']
                    ];
    
                    // Get or create customer
                    $customer = $this->customerService->getOrCreateCustomer($input['nama_customer']);
                    
                    // Get latest pembelian for this item type
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
                        $status,
                        $tgl_penjualan // Pass the date
                    );
    
                    // Insert detail penjualan
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


                        $this->penjualanService->updateLatestTerkumpul(
                            $pembelian['id_pembelian'],
                            $totals['total_harga_modal'],
                            $input['id_tipe'],
                            $input['id_method']  // Pass the payment method
                        );

    
                    // Insert invoice item details
                    $invoiceItemModel = new InvoiceItemModel();
                    $invoiceItemModel->insert([
                        'id_invoice' => $invoiceId,
                        'id_tipe' => $item['id_tipe'],
                        'id_unit' => $item['id_unit'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => roundToNearestFiveHundred($item['quantity'] * $item['price']),
                        'id_method' => $input['id_method']
                    ]);
                }
    
                // Complete the transaction
                $this->db->transComplete();
    
                // Prepare receipt data
                $receiptData = [
                    'invoiceId' => $invoiceId,
                    'storeName' => 'Toko Sukses Bersama <br> (Agen Telur Orange) ',
'storeAddress' => 'Jl. Rambutan Raya No. 42<br>RT.06/RW.06 Baranangsiang<br>Kec. Bogor Timur, Kota Bogor<br>Jawa Barat 16143',                    'phone' => '082175671616',
                    'date' => date('d/m/Y H:i:s'),
                    'customerName' => $invoiceData['customer_name'],
                    'items' => $processedItems,
                    'total' => $total,
                    'payment' => $cumulativePaymentAmount,
                    'change' => $changeAmount,
                    'paymentMethod' => $invoiceData['payment_method']
                ];
    
                // Return success response
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
    
            // Log the error
            log_message('error', 'Sale Transaction Error: ' . $e->getMessage());
    
            // Return error response
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function printInvoice($invoiceId)
    {
        try {
            $invoiceModel = new InvoiceModel();
            $invoiceItemModel = new InvoiceItemModel();
            $unitBarangModel = new UnitBarangModel();
    
            // Fetch invoice details
            $invoice = $invoiceModel->find($invoiceId);
            if (!$invoice) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invoice not found'
                ])->setStatusCode(404);
            }
    
            // Debugging: Check if invoice is retrieved
            log_message('info', 'Invoice retrieved: ' . json_encode($invoice));
    
            // Fetch invoice items with more detailed information
            $invoiceItems = $invoiceItemModel
                ->select('tbl_invoice_items.*, 
                          tb.jenis_barang, 
                          tb.satuan_dasar, 
                          ub.tipe_unit, 
                          ub.standar_jumlah_barang')
                ->join('tbl_tipe_barang tb', 'tbl_invoice_items.id_tipe = tb.id_tipe')
                ->join('tbl_unit_barang ub', 'tbl_invoice_items.id_unit = ub.id_unit')
                ->where('id_invoice', $invoiceId)
                ->findAll();
    
            // Debugging: Check if invoice items are retrieved
            log_message('info', 'Invoice items retrieved: ' . json_encode($invoiceItems));
    
            // Get the payment method from the first invoice item
            $paymentMethodId = !empty($invoiceItems) ? $invoiceItems[0]['id_method'] : null;
    
            // Prepare receipt data
            $receiptData = [
                'invoice' => [
                    'id_invoice' => $invoice['id_invoice'],
                    'invoice_date' => $invoice['invoice_date'] ?? date('Y-m-d H:i:s'),
                    'customer_name' => $invoice['customer_name'] ?? 'Umum',
                    'total_amount' => $invoice['total_amount'],
                    'payment_amount' => $invoice['payment_amount'],
                    'change_amount' => $invoice['change_amount'],
                    'payment_method' => $this->getPaymentMethodName($paymentMethodId)
                ],
                'items' => $invoiceItems, // Use the retrieved items directly
                'storeName' => 'Toko Sukses Bersama <br> (Agen Telur Orange) ',
                'storeAddress' => 'Jl. Rambutan Raya No. 42<br>RT.06/RW.06 Baranangsiang<br>Kec. Bogor Timur, Kota Bogor<br>Jawa Barat 16143', 
                'storePhone' => '082175671616'
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

    
    // Helper method to get payment method name 
    private function getPaymentMethodName($methodId = null) 
    { 
        if ($methodId === null) {
            return 'Tidak Diketahui';
        }
    
        switch ($methodId) { 
            case 1: 
                return 'Piutang'; 
            case 2: 
                return 'Transfer'; 
            case 3: 
                return 'Cash'; 
            default:
                return 'Metode Lain';
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

    public function markLunas()
    {
        $this->db->transStart();
    
        try {
            $idDetailPenjualan = $this->request->getPost('id_detail_penjualan');
    
            // Fetch the detail penjualan with comprehensive details
            $detailPenjualan = $this->detailPenjualanModel
                ->select('tbl_detail_penjualan.*, 
                          tdp.harga_modal_barang, 
                          tp.id_method')
                ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
                ->join('tbl_payment tp', 'tbl_detail_penjualan.id_payment = tp.id_payment', 'left')
                ->where('id_detail_penjualan', $idDetailPenjualan)
                ->first();
            
            if (!$detailPenjualan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Detail penjualan not found.'
                ]);
            }
    
            // Find id_meity directly from tbl_meity using id_pembelian
            $meityRecord = $this->meityModel
                ->where('id_pembelian', $detailPenjualan['id_pembelian'])
                ->where('id_tipe', $detailPenjualan['id_tipe'])
                ->first();
    
            $idMeity = $meityRecord['id_meity'] ?? null;
    
            if (!$idMeity) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No Meity record found for this purchase.'
                ]);
            }
    
            // Calculate total amounts
            $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
            $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
            $totalUntung = $totalHargaJual - $totalHargaModal;
    
            // Find or create Penjualan record for today
            $latestPenjualan = $this->penjualanModel
                ->where('id_tipe', $detailPenjualan['id_tipe'])
                ->where('tgl_penjualan', date('Y-m-d'))
                ->orderBy('id_penjualan', 'DESC')
                ->first();
    
            if ($latestPenjualan) {
                // Update existing penjualan for today
                $penjualanData = [
                    'total_harga_jual' => $latestPenjualan['total_harga_jual'] + $totalHargaJual,
                    'total_harga_modal' => $latestPenjualan['total_harga_modal'] + $totalHargaModal,
                    'total_untung' => $latestPenjualan['total_untung'] + $totalUntung,
                    'total_barang_keluar' => $latestPenjualan['total_barang_keluar'] + $detailPenjualan['jumlah_keluar']
                ];
    
                $this->penjualanModel
                    ->where('id_penjualan', $latestPenjualan['id_penjualan'])
                    ->set($penjualanData)
                    ->update();
    
                $penjualanId = $latestPenjualan['id_penjualan'];
            } else {
                // Create new penjualan record for today
                $penjualanData = [
                    'tgl_penjualan' => date('Y-m-d'),
                    'id_tipe' => $detailPenjualan['id_tipe'],
                    'total_harga_jual' => $totalHargaJual,
                    'total_harga_modal' => $totalHargaModal,
                    'total_untung' => $totalUntung,
                    'total_barang_keluar' => $detailPenjualan['jumlah_keluar']
                ];
    
                $penjualanId = $this->penjualanModel->insert($penjualanData);
            }
    
            // Subtract piutang for specific meity
            $newCurrentPiutang = max(0, $meityRecord['current_piutang'] - $totalHargaModal);
            
            // Explicitly specify only the columns to update
            $updateData = [
                'current_piutang' => $newCurrentPiutang
            ];
    
            $this->meityModel
                ->where('id_meity', $idMeity)
                ->set($updateData)
                ->update();
    
            // Mark detail penjualan as Lunas
            $this->detailPenjualanModel
                ->where('id_detail_penjualan', $idDetailPenjualan)
                ->set([
                    'status' => 1, 
                    'id_penjualan' => $penjualanId,
                ])
                ->update();
    
            $this->db->transComplete();
    
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Payment marked as Lunas successfully.',
                'details' => [
                    'totalHargaModal' => $totalHargaModal,
                    'idMeity' => $idMeity,
                    'idPembelian' => $detailPenjualan['id_pembelian'],
                    'newCurrentPiutang' => $newCurrentPiutang,
                ]
            ]);
    
        } catch (Exception $e) {
            $this->db->transRollback();
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    // public function markLunas()
    // {
    //     $this->db->transStart();
    
    //     try {
    //         $idDetailPenjualan = $this->request->getPost('id_detail_penjualan');
    
    //         // Fetch the detail penjualan with comprehensive details
    //         $detailPenjualan = $this->detailPenjualanModel
    //             ->select('tbl_detail_penjualan.*, 
    //                       tdp.harga_modal_barang, 
    //                       tp.id_method')
    //             ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
    //             ->join('tbl_payment tp', 'tbl_detail_penjualan.id_payment = tp.id_payment', 'left')
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->first();
            
    //         if (!$detailPenjualan) {
    //             return $this->response->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'Detail penjualan not found.'
    //             ]);
    //         }
    
    //         // Find id_meity directly from tbl_meity using id_pembelian
    //         $meityRecord = $this->meityModel
    //         ->where('id_pembelian', $detailPenjualan['id_pembelian'])
    //         ->where('id_tipe', $detailPenjualan['id_tipe'])
    //         ->first();

    //         $idMeity = $meityRecord['id_meity'] ?? null;
    
    //         if (!$idMeity) {
    //             return $this->response->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'No Meity record found for this purchase.'
    //             ]);
    //         }
    
    //         // Calculate total amounts
    //         $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
    //         $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
    //         $totalUntung = $totalHargaJual - $totalHargaModal;
    
    //         // Find or create Penjualan record for today
    //         $latestPenjualan = $this->penjualanModel
    //             ->where('id_tipe', $detailPenjualan['id_tipe'])
    //             ->where('tgl_penjualan', date('Y-m-d'))
    //             ->orderBy('id_penjualan', 'DESC')
    //             ->first();
    
    //         if ($latestPenjualan) {
    //             // Update existing penjualan for today
    //             $penjualanData = [
    //                 'total_harga_jual' => $latestPenjualan['total_harga_jual'] + $totalHargaJual,
    //                 'total_harga_modal' => $latestPenjualan['total_harga_modal'] + $totalHargaModal,
    //                 'total_untung' => $latestPenjualan['total_untung'] + $totalUntung,
    //                 'total_barang_keluar' => $latestPenjualan['total_barang_keluar'] + $detailPenjualan['jumlah_keluar']
    //             ];
    
    //             $this->penjualanModel
    //                 ->where('id_penjualan', $latestPenjualan['id_penjualan'])
    //                 ->set($penjualanData)
    //                 ->update();
    
    //             $penjualanId = $latestPenjualan['id_penjualan'];
    //         } else {
    //             // Create new penjualan record for today
    //             $penjualanData = [
    //                 'tgl_penjualan' => date('Y-m-d'),
    //                 'id_tipe' => $detailPenjualan['id_tipe'],
    //                 'total_harga_jual' => $totalHargaJual,
    //                 'total_harga_modal' => $totalHargaModal,
    //                 'total_untung' => $totalUntung,
    //                 'total_barang_keluar' => $detailPenjualan['jumlah_keluar']
    //             ];
    
    //             $penjualanId = $this->penjualanModel->insert($penjualanData);
    //         }
    
    //         // Subtract piutang for specific meity
    //         $newCurrentPiutang = max(0, $meityRecord['current_piutang'] - $totalHargaModal);
    //         log_message('debug', 'Current Piutang before update: ' . $meityRecord['current_piutang']);
    //         log_message('debug', 'Total Harga Jual: ' . $totalHargaModal);
    //         log_message('debug', 'New Current Piutang: ' . $newCurrentPiutang);
    //         $this->meityModel
    //             ->where('id_meity', $idMeity)
    //             ->set([
    //                 'current_piutang' => $newCurrentPiutang,
    //                 // 'sudah_setor' => $newCurrentPiutang == 0 ? 1 : 0, // Optional: Mark as fully paid
    //             ])
    //             ->update();
    //             log_message('debug', 'Meity record updated for ID: ' . $idMeity);

    //         // Mark detail penjualan as Lunas
    //         $this->detailPenjualanModel
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->set([
    //                 'status' => 1, 
    //                 'id_penjualan' => $penjualanId,
    //                 // 'lunas_date' => date('Y-m-d') // Optional: track Lunas date
    //             ])
    //             ->update();
    
    //         $this->db->transComplete();
    
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Payment marked as Lunas successfully.',
    //             'details' => [
    //                 'totalHargaJual' => $totalHargaJual,
    //                 'idMeity' => $idMeity,
    //                 'idPembelian' => $detailPenjualan['id_pembelian'],
    //                 'newCurrentPiutang' => $newCurrentPiutang,
    //                 // 'lunasTgl' => date('Y-m-d')
    //             ]
    //         ]);
    
    //     } catch (Exception $e) {
    //         $this->db->transRollback();
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //             // 'trace' => $e->getTraceAsString() // Optional: for debugging
    //         ]);
    //     }
    // }

    // public function markLunas()
    // {
    //     $this->db->transStart();
    
    //     try {
    //         $idDetailPenjualan = $this->request->getPost('id_detail_penjualan');
    
    //         // Fetch the detail penjualan with a join to get all necessary details
    //         $detailPenjualan = $this->detailPenjualanModel
    //             ->select('tbl_detail_penjualan.*, tdp.harga_modal_barang, tp.id_method')
    //             ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
    //             ->join('tbl_payment tp', 'tbl_detail_penjualan.id_payment = tp.id_payment', 'left')
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->first();
    
    //         // Check if already marked as Lunas
    //         if ($detailPenjualan['status'] == 1) {
    //             return $this->response->setJSON([
    //                 'status' => 'error',
    //                 'message' => 'This transaction is already marked as Lunas.'
    //             ]);
    //         }
    
    //         // Calculate total harga modal and total harga jual
    //         $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
    //         $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
    //         $totalUntung = $totalHargaJual - $totalHargaModal;
    
    //         // Find the latest penjualan record for this type
    //         $latestPenjualan = $this->penjualanModel
    //             ->where('id_tipe', $detailPenjualan['id_tipe'])
    //             ->orderBy('id_penjualan', 'DESC')
    //             ->first();
    
    //         if ($latestPenjualan) {
    //             // Cumulative update for all totals
    //             $penjualanData = [
    //                 'total_harga_jual' => $latestPenjualan['total_harga_jual'] + $totalHargaJual,
    //                 'total_harga_modal' => $latestPenjualan['total_harga_modal'] + $totalHargaModal,
    //                 'total_untung' => $latestPenjualan['total_untung'] + $totalUntung,
    //                 'total_barang_keluar' => $latestPenjualan['total_barang_keluar'] + $detailPenjualan['jumlah_keluar']
    //             ];
    
    //             // Update existing penjualan record
    //             $this->penjualanModel
    //                 ->where('id_penjualan', $latestPenjualan['id_penjualan'])
    //                 ->set($penjualanData)
    //                 ->update();
    
    //             $penjualanId = $latestPenjualan['id_penjualan'];
    //         } else {
    //             // Create new penjualan record with full totals
    //             $penjualanData = [
    //                 'tgl_penjualan' => date('Y-m-d'),
    //                 'id_tipe' => $detailPenjualan['id_tipe'],
    //                 'total_harga_jual' => $totalHargaJual,
    //                 'total_harga_modal' => $totalHargaModal,
    //                 'total_untung' => $totalUntung,
    //                 'total_barang_keluar' => $detailPenjualan['jumlah_keluar']
    //             ];
    
    //             $penjualanId = $this->penjualanModel->insert($penjualanData);
    //         }
    
    //         // Use the existing updateLatestTerkumpul method
    //         // $this->penjualanService->updateLatestTerkumpul(
    //         //     $detailPenjualan['id_pembelian'],
    //         //     $totalHargaModal,
    //         //     $detailPenjualan['id_tipe'],
    //         //     $detailPenjualan['id_method']
    //         // );
    
    //         // Mark the detail penjualan as Lunas
    //         $this->detailPenjualanModel
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->set([
    //                 'status' => 1, 
    //                 'id_penjualan' => $penjualanId
    //             ])
    //             ->update();
    
    //         $this->db->transComplete();
    
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Payment marked as Lunas successfully.',
    //             'debug' => [
    //                 'totalHargaModal' => $totalHargaModal,
    //                 'totalHargaJual' => $totalHargaJual,
    //                 'totalUntung' => $totalUntung,
    //                 'jumlahKeluar' => $detailPenjualan['jumlah_keluar']
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
    
            // Fetch detail penjualan with payment method
            $detailPenjualan = $this->db->table('tbl_detail_penjualan')
                ->select('tbl_detail_penjualan.*, tdp.harga_modal_barang, tp.id_method')
                ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
                ->join('tbl_payment tp', 'tbl_detail_penjualan.id_payment = tp.id_payment', 'left')
                ->where('id_detail_penjualan', $idDetailPenjualan)
                ->get()
                ->getRowArray();
    
            // Additional null check
            if (empty($detailPenjualan)) {
                throw new Exception("No detail penjualan found for ID: " . $idDetailPenjualan);
            }
    
            // Stock restoration
            $stockService = new \App\Services\StockService();
            $stockInput = [
                'id_tipe' => $detailPenjualan['id_tipe'],
                'barang_masuk' => $detailPenjualan['jumlah_keluar'], // Restore as incoming stock
                'barang_keluar' => 0 // No outgoing stock during restoration
            ];
            $stockService->updateOrCreateStock($stockInput);
    
            // Calculate totals
            $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
            $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
    
            // Find the precise Meity record for this specific pembelian and tipe
            $latestMeityRecord = $this->db->table('tbl_meity')
                ->where('id_tipe', $detailPenjualan['id_tipe'])
                ->where('id_pembelian', $detailPenjualan['id_pembelian'])
                ->get()
                ->getRowArray();
    
                if ($latestMeityRecord) {
                    // Prepare update data
                    $meityUpdateData = [];
        
                    // Subtract from terkumpul for all methods
                    $newTerkumpul = max(0, $latestMeityRecord['terkumpul'] - $totalHargaModal);
                    $meityUpdateData['terkumpul'] = $newTerkumpul;
        
                    // Handle specific method updates
                    switch ($detailPenjualan['id_method']) {
                        case 1: // Piutang
                            $newCurrentPiutang = max(0, $latestMeityRecord['current_piutang'] - $totalHargaModal);
                            $meityUpdateData['current_piutang'] = $newCurrentPiutang;
                            break;
                        case 2: // Transfer
                            $newCurrentTransfer = max(0, $latestMeityRecord['current_transfer'] - $totalHargaModal);
                            $meityUpdateData['current_transfer'] = $newCurrentTransfer;
                            break;
                    }
        
                    // Modify status logic
                    if ($newTerkumpul < $latestMeityRecord['hutang']) {
                        // If terkumpul becomes less than hutang, set status to waiting
                        $meityUpdateData['status'] = 0; 
                    } elseif ($newTerkumpul == $latestMeityRecord['hutang']) {
                        // If terkumpul exactly matches hutang, keep status as is or set to completed
                        // You might want to add additional logic here based on your specific requirements
                        $meityUpdateData['status'] = 0; // Assuming 1 means completed
                    }
        
                    // Update meity record
                    $this->db->table('tbl_meity')
                        ->where('id_meity', $latestMeityRecord['id_meity'])
                        ->update($meityUpdateData);
                }
    
            // Find and update penjualan record
            $penjualan = $this->db->table('tbl_penjualan')
                ->where('id_penjualan', $detailPenjualan['id_penjualan'])
                ->get()
                ->getRowArray();
    
            if ($penjualan) {
                $updatedTotalHargaJual = max(0, $penjualan['total_harga_jual'] - $totalHargaJual);
                $updatedTotalHargaModal = max(0, $penjualan['total_harga_modal'] - $totalHargaModal);
                $updatedTotalUntung = $updatedTotalHargaJual - $updatedTotalHargaModal;
                $updatedTotalBarangKeluar = max(0, $penjualan['total_barang_keluar'] - $detailPenjualan['jumlah_keluar']);
    
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
    
            // Check remaining detail penjualan
            $remainingDetailPenjualan = $this->db->table('tbl_detail_penjualan')
                ->where('id_penjualan', $detailPenjualan['id_penjualan'])
                ->countAllResults();
    
            // Delete penjualan if no remaining details
            if ($remainingDetailPenjualan === 0) {
                $this->db->table('tbl_penjualan')
                    ->where('id_penjualan', $detailPenjualan['id_penjualan'])
                    ->delete();
            }
    
            $this->db->transComplete();
    
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Detail penjualan deleted successfully.',
                'remainingDetails' => $remainingDetailPenjualan
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

    // public function deleteDetailPenjualan()
    // {
    //     $this->db->transStart();
    
    //     try {
    //         $idDetailPenjualan = $this->request->getPost('id_detail_penjualan');
    
    //         // Validate input
    //         if (empty($idDetailPenjualan)) {
    //             throw new Exception("Invalid detail penjualan ID");
    //         }
    
    //         // Fetch detail penjualan with payment method
    //         $detailPenjualan = $this->db->table('tbl_detail_penjualan')
    //             ->select('tbl_detail_penjualan.*, tdp.harga_modal_barang, tp.id_method')
    //             ->join('tbl_detail_pembelian tdp', 'tbl_detail_penjualan.id_pembelian = tdp.id_pembelian', 'left')
    //             ->join('tbl_payment tp', 'tbl_detail_penjualan.id_payment = tp.id_payment', 'left')
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->get()
    //             ->getRowArray();
    
    //         // Additional null check
    //         if (empty($detailPenjualan)) {
    //             throw new Exception("No detail penjualan found for ID: " . $idDetailPenjualan);
    //         }
    
    //         // Stock restoration
    //         $stockService = new \App\Services\StockService();
    //         $stockInput = [
    //             'id_tipe' => $detailPenjualan['id_tipe'],
    //             'stock_barang' => $detailPenjualan['jumlah_keluar'],
    //             'barang_masuk' => 0,
    //             'barang_keluar' => -$detailPenjualan['jumlah_keluar']
    //         ];
    //         $stockService->updateOrCreateStock($stockInput);
    
    //         // Calculate totals
    //         $totalHargaModal = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_modal_barang'];
    //         $totalHargaJual = $detailPenjualan['jumlah_keluar'] * $detailPenjualan['harga_jual'];
    
    //         // Find the precise Meity record for this specific pembelian and tipe
    //         $latestMeityRecord = $this->db->table('tbl_meity')
    //             ->where('id_tipe', $detailPenjualan['id_tipe'])
    //             ->where('id_pembelian', $detailPenjualan['id_pembelian'])
    //             ->get()
    //             ->getRowArray();
    
    //         if ($latestMeityRecord) {
    //             // Prepare update data
    //             $meityUpdateData = [];
    
    //             // Subtract from terkumpul for all methods
    //             $newTerkumpul = max(0, $latestMeityRecord['terkumpul'] - $totalHargaModal);
    //             $meityUpdateData['terkumpul'] = $newTerkumpul;
    
    //             // Handle specific method updates
    //             switch ($detailPenjualan['id_method']) {
    //                 case 1: // Piutang
    //                     $newCurrentPiutang = max(0, $latestMeityRecord['current_piutang'] - $totalHargaModal);
    //                     $meityUpdateData['current_piutang'] = $newCurrentPiutang;
    //                     break;
    //                 case 2: // Transfer
    //                     $newCurrentTransfer = max(0, $latestMeityRecord['current_transfer'] - $totalHargaModal);
    //                     $meityUpdateData['current_transfer'] = $newCurrentTransfer;
    //                     break;
    //             }
    
    //             // Update meity record
    //             $this->db->table('tbl_meity')
    //                 ->where('id_meity', $latestMeityRecord['id_meity'])
    //                 ->update($meityUpdateData);
    //         }
    
    //         // Find and update penjualan record
    //         $penjualan = $this->db->table('tbl_penjualan')
    //             ->where('id_penjualan', $detailPenjualan['id_penjualan'])
    //             ->get()
    //             ->getRowArray();
    
    //         if ($penjualan) {
    //             $updatedTotalHargaJual = max(0, $penjualan['total_harga_jual'] - $totalHargaJual);
    //             $updatedTotalHargaModal = max(0, $penjualan['total_harga_modal'] - $totalHargaModal);
    //             $updatedTotalUntung = $updatedTotalHargaJual - $updatedTotalHargaModal;
    //             $updatedTotalBarangKeluar = max(0, $penjualan['total_barang_keluar'] - $detailPenjualan['jumlah_keluar']);
    
    //             $this->db->table('tbl_penjualan')
    //                 ->where('id_penjualan', $penjualan['id_penjualan'])
    //                 ->update([
    //                     'total_harga_jual' => $updatedTotalHargaJual,
    //                     'total_harga_modal' => $updatedTotalHargaModal,
    //                     'total_untung' => $updatedTotalUntung,
    //                     'total_barang_keluar' => $updatedTotalBarangKeluar
    //                 ]);
    //         }
    
    //         // Delete associated payment
    //         if (!empty($detailPenjualan['id_payment'])) {
    //             $this->db->table('tbl_payment')
    //                 ->where('id_payment', $detailPenjualan['id_payment'])
    //                 ->delete();
    //         }
    
    //         // Delete the detail penjualan
    //         $this->db->table('tbl_detail_penjualan')
    //             ->where('id_detail_penjualan', $idDetailPenjualan)
    //             ->delete();
    
    //         // Check remaining detail penjualan
    //         $remainingDetailPenjualan = $this->db->table('tbl_detail_penjualan')
    //             ->where('id_penjualan', $detailPenjualan['id_penjualan'])
    //             ->countAllResults();
    
    //         // Delete penjualan if no remaining details
    //         if ($remainingDetailPenjualan === 0) {
    //             $this->db->table('tbl_penjualan')
    //                 ->where('id_penjualan', $detailPenjualan['id_penjualan'])
    //                 ->delete();
    //         }
    
    //         $this->db->transComplete();
    
    //         return $this->response->setJSON([
    //             'status' => 'success',
    //             'message' => 'Detail penjualan deleted successfully.',
    //             'remainingDetails' => $remainingDetailPenjualan
    //         ]);
    
    //     } catch (Exception $e) {
    //         $this->db->transRollback();
    //         log_message('error', 'Delete Detail Penjualan Error: ' . $e->getMessage());
    //         return $this->response->setStatusCode(500)->setJSON([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //     }
    // }
    
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
