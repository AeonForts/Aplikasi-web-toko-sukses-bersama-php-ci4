<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{CustomerModel, DetailPenjualanModel};

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $detailPenjualanModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
    }

    public function list()
    {
        return view('pages/admin/customer/list');
    }

    public function get($id = null)
    {
        // Validate input
        if (!$id || !is_numeric($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid customer ID'
            ])->setStatusCode(400);
        }
    
        try {
            // Retrieve customer by ID
            $customer = $this->customerModel->find($id);
    
            // Check if customer exists
            if (!$customer) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Customer not found'
                ])->setStatusCode(404);
            }
    
            // Return customer data
            return $this->response->setJSON($customer);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error retrieving customer: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function datatables()
    {
        $request = service('request');
        
        // Ensure type conversion to integers
        $draw = (int)$request->getPost('draw');
        $start = (int)$request->getPost('start');
        $length = (int)$request->getPost('length');
        $search = $request->getPost('search')['value'] ?? '';
    
        // Fallback to default values if conversion fails
        $start = $start > 0 ? $start : 0;
        $length = $length > 0 ? $length : 10;
    
        // Total records
        $totalRecords = $this->customerModel->countAll();
    
        // Filtered records
        $customerQuery = $this->customerModel->select('id_customer, nama_customer, 
            COALESCE(alamat, "-") as alamat, COALESCE(no_telp, "-") as no_telp');
        
        if (!empty($search)) {
            $customerQuery->groupStart()
                ->like('nama_customer', $search)
                ->orLike('alamat', $search)
                ->orLike('no_telp', $search)
                ->groupEnd();
        }
    
        $totalFilteredRecords = $customerQuery->countAllResults(false);
        
        $customers = $customerQuery
            ->limit($length, $start)
            ->get()
            ->getResult();
    
        $data = [];
        $no = $start + 1;
        foreach ($customers as $customer) {
            $row = [
                'no' => $no++,
                'id_customer' => $customer->id_customer,
                'nama_customer' => $customer->nama_customer,
                'alamat' => $customer->alamat,
                'no_telp' => $customer->no_telp
            ];
            $data[] = $row;
        }
    
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilteredRecords,
            'data' => $data
        ];
    
        return $this->response->setJSON($response);
    }

    public function save()
    {
        // Validate input
        $validationRules = [
            'nama_customer' => 'required|min_length[3]|max_length[100]',
            'alamat' => 'required|min_length[2]|max_length[255]',
            'no_telp' => 'required|min_length[3]|max_length[15]|numeric'
        ];

        if (!$this->validate($validationRules)) {
            // Return validation errors
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        // Prepare data
        $data = [
            'nama_customer' => $this->request->getPost('nama_customer'),
            'alamat' => $this->request->getPost('alamat'),
            'no_telp' => $this->request->getPost('no_telp')
        ];

        try {
            // Save customer
            $this->customerModel->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Customer berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan customer: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update existing customer
     */
    public function update()
    {
        // Validate input
        $validationRules = [
            'id_customer' => 'required|numeric|is_natural_no_zero',
            'nama_customer' => 'required|min_length[3]|max_length[100]',
            'alamat' => 'required|min_length[2]|max_length[255]',
            'no_telp' => 'required|min_length[3]|max_length[15]|numeric'
        ];

        if (!$this->validate($validationRules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $this->validator->getErrors()
            ])->setStatusCode(400);
        }

        // Prepare data
        $id = $this->request->getPost('id_customer');
        $data = [
            'nama_customer' => $this->request->getPost('nama_customer'),
            'alamat' => $this->request->getPost('alamat'),
            'no_telp' => $this->request->getPost('no_telp')
        ];

        try {
            // Update customer
            $this->customerModel->update($id, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Customer berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate customer: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete customer with validation
     */
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
            // Check if customer exists in detail_penjualan
            $existingTransactions = $this->detailPenjualanModel
                ->where('id_customer', $id)
                ->countAllResults();

            if ($existingTransactions > 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Tidak dapat menghapus customer. Terdapat {$existingTransactions} transaksi yang terkait."
                ])->setStatusCode(400);
            }

            // Delete customer
            $result = $this->customerModel->delete($id);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Customer berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus customer'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus customer: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }


}