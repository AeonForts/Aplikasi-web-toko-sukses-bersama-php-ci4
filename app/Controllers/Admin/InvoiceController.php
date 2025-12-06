<?php

namespace App\Controllers\Admin;

use App\Models\{InvoiceModel,InvoiceItemModel, UnitBarangModel};
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class InvoiceController extends BaseController
{
    protected $invoiceModel;
    protected $invoiceItemModel;
    protected $unitBarangModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->invoiceItemModel = new InvoiceItemModel();
        $this->unitBarangModel = new UnitBarangModel();
    }

    public function list()
    {
        return view('pages/admin/invoice/list');
    }

    public function detail($invoiceId)
    {
        // Get invoice details
        $invoice = $this->invoiceModel->find($invoiceId);

        if (!$invoice) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Invoice not found');
        }

        // Get invoice items with additional details
        $invoiceItems = $this->invoiceItemModel->select('tbl_invoice_items.*, tb.jenis_barang')
            ->join('tbl_tipe_barang tb', 'tbl_invoice_items.id_tipe = tb.id_tipe')
            ->where('id_invoice', $invoiceId)
            ->findAll();

        return view('pages/admin/invoice/detail', [
            'invoice' => $invoice,
            'invoice_items' => $invoiceItems
        ]);
    }

    public function getDatatables()
    {
        try {
            $request = service('request');

            // Datatable parameters
            $draw = $request->getPost('draw');
            
            // Explicitly convert to integers
            $start = intval($request->getPost('start'));
            $length = intval($request->getPost('length'));
            
            $search = $request->getPost('search')['value'] ?? '';
            $order = $request->getPost('order')[0] ?? ['column' => 0, 'dir' => 'desc'];
            $startDate = $request->getPost('start_date');
            $endDate = $request->getPost('end_date');

            // Build query
            $query = $this->invoiceModel->select('*');

            // Apply date filter if dates are provided
            if (!empty($startDate) && !empty($endDate)) {
                $query->where('invoice_date >=', $startDate)
                    ->where('invoice_date <=', $endDate);
            }

            // Apply search
            if (!empty($search)) {
                $query->groupStart()
                    ->like('customer_name', $search)
                    ->orLike('total_amount', $search)
                    ->groupEnd();
            }

            // Clone the query for total records before pagination
            $totalQuery = clone $query;
            $totalRecords = $totalQuery->countAllResults(false);

            // Apply ordering
            $columns = ['id_invoice', 'customer_name', 'invoice_date', 'total_amount', 'payment_amount', 'change_amount'];
            $query->orderBy($columns[$order['column']], $order['dir']);

            // Apply pagination with explicit integer casting
            $query->limit($length, $start);

            // Get results
            $results = $query->get()->getResultArray();

            // Prepare response
            $data = [
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $results
            ];

            return $this->response->setJSON($data);

        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'DataTables Error: ' . $e->getMessage());
            
            // Return error response
            return $this->response->setStatusCode(500)
                ->setJSON([
                    'error' => true,
                    'message' => 'Internal Server Error',
                    'details' => $e->getMessage()
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
            $receiptHtml = view('pages/admin/invoice/invoice_receipt', $receiptData);
    
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

    public function delete($invoiceId)
    {
        try {
            // Start a database transaction
            $this->invoiceModel->db->transStart();
    
            // First, check if the invoice exists
            $invoice = $this->invoiceModel->find($invoiceId);
            if (!$invoice) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invoice not found'
                ])->setStatusCode(404);
            }
    
            // Delete associated invoice items first (cascading delete)
            $this->invoiceItemModel->where('id_invoice', $invoiceId)->delete();
    
            // Then delete the invoice
            $this->invoiceModel->delete($invoiceId);
    
            // Complete the transaction
            $this->invoiceModel->db->transComplete();
    
            // Check if the transaction was successful
            if ($this->invoiceModel->db->transStatus() === FALSE) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to delete invoice'
                ])->setStatusCode(500);
            }
    
            // Return success response
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Invoice and its items deleted successfully'
            ]);
    
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Delete Invoice Error: ' . $e->getMessage());
    
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'An error occurred while deleting the invoice',
                'details' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // // Helper method to get payment method name 
    // private function getPaymentMethodName($methodId) 
    // { 
    //     switch ($methodId) { 
    //         case 1: 
    //             return 'Piutang'; 
    //         case 2: 
    //             return 'Transfer'; 
    //         default: 
    //             return 'Cash'; 
    //     } 
    // }
}