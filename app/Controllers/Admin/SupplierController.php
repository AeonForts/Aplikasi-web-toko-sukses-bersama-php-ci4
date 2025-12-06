<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{SupplierModel, PembelianModel};

class SupplierController extends BaseController
{
    protected $supplierModel;
    protected $pembelianModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->pembelianModel = new PembelianModel();
    }

    public function list()
    {
        return view('pages/admin/supplier/list');
    }

    public function get($id = null)
    {
        // Validate input
        if (!$id || !is_numeric($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid supplier ID'
            ])->setStatusCode(400);
        }
    
        try {
            // Retrieve supplier by ID
            $supplier = $this->supplierModel->find($id);
    
            // Check if supplier exists
            if (!$supplier) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'supplier not found'
                ])->setStatusCode(404);
            }
    
            // Return supplier data
            return $this->response->setJSON($supplier);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error retrieving supplier: ' . $e->getMessage()
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

        // Use query builder directly
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_supplier');

        // Total records
        $totalRecords = $builder->countAll();

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('nama_supplier', $search)
                ->groupEnd();
        }

        // Count filtered records
        $totalFilteredRecords = $builder->countAllResults(false);

        // Get paginated results
        $suppliers = $builder
            ->select('id_supplier, nama_supplier, COALESCE(alamat, "-") as alamat, COALESCE(no_telp, "-") as no_telp')
            ->limit($length, $start)
            ->get()
            ->getResult();

        $data = [];
        $no = $start + 1;
        foreach ($suppliers as $supplier) {
            $row = [
                'no' => $no++,
                'id_supplier' => $supplier->id_supplier,
                'nama_supplier' => $supplier->nama_supplier,
                'alamat' => $supplier->alamat,
                'no_telp' => $supplier->no_telp
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
            'nama_supplier' => 'required|min_length[3]|max_length[100]',
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
            'nama_supplier' => $this->request->getPost('nama_supplier'),
            'alamat' => $this->request->getPost('alamat'),
            'no_telp' => $this->request->getPost('no_telp')
        ];

        try {
            // Save supplier
            $this->supplierModel->insert($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'supplier berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan supplier: ' . $e->getMessage()
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
            'id_supplier' => 'required|numeric|is_natural_no_zero',
            'nama_supplier' => 'required|min_length[3]|max_length[100]',
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
        $id = $this->request->getPost('id_supplier');
        $data = [
            'nama_supplier' => $this->request->getPost('nama_supplier'),
            'alamat' => $this->request->getPost('alamat'),
            'no_telp' => $this->request->getPost('no_telp')
        ];

        try {
            // Update supplier
            $this->supplierModel->update($id, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'supplier berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengupdate supplier: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete supplier with validation
     */
    public function delete($id = null)
    {
        // Validate input
        if (!$id || !is_numeric($id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid supplier ID'
            ])->setStatusCode(400);
        }

        try {
            // Check if supplier exists in detail_penjualan
            $existingTransactions = $this->pembelianModel
                ->where('id_supplier', $id)
                ->countAllResults();

            if ($existingTransactions > 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Tidak dapat menghapus supplier. Terdapat {$existingTransactions} transaksi yang terkait."
                ])->setStatusCode(400);
            }

            // Delete supplier
            $result = $this->supplierModel->delete($id);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'supplier berhasil dihapus'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus supplier'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus supplier: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

}