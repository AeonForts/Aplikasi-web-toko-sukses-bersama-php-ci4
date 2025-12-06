<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{DetailPenjualanModel,StockBarangModel,BarangModel,UnitBarangModel,ViewStockModel};
use CodeIgniter\HTTP\ResponseInterface;

class BarangController extends BaseController
{
    protected $barangModel;
    protected $unitBarangModel;
    protected $stockBarangModel;
    protected $viewStockModel;
    protected $detailPenjualanModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->unitBarangModel = new UnitBarangModel();
        $this->stockBarangModel = new StockBarangModel();
        $this->viewStockModel = new ViewStockModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();


    }


    public function listStock()
    {
        // Fetch all stocks with pagination
        $data['stocks'] = $this->viewStockModel
            ->select('view_stock_with_sisa_per_unit.*, tbl_tipe_barang.jenis_barang, tbl_tipe_barang.satuan_dasar')
            ->join('tbl_tipe_barang', 'view_stock_with_sisa_per_unit.id_tipe = tbl_tipe_barang.id_tipe')
            ->paginate(10); // Paginate 10 items per page

        // Add pagination links
        $data['pager'] = $this->viewStockModel->pager;

        // Render the stock view
        return view('pages/admin/barang/list_stock_barang', $data);
    }

    public function list($view = 'list')
    {
        // Use the custom method to fetch paginated data with the JOIN
        $data['barang'] = $this->barangModel->getBarangWithUnit();
        $data['pager'] = $this->barangModel->pager;
        $data['tipeBarangList'] = $this->barangModel->findAll();

        // Dynamically choose the view based on the parameter
        $viewPath = 'pages/admin/barang/' . ($view === 'list_barang' ? 'list_barang' : 'list');
        
        return view($viewPath, $data);
    }

    // Optional: Create specific methods for each view if needed
    public function showList()
    {
        return $this->list('list');
    }

    public function showListBarang()
    {
        return $this->list('list_barang');
    }


    public function getDatatables()
{
    $request = service('request');

    $draw = $request->getPost('draw');
    $start = $request->getPost('start');
    $length = $request->getPost('length');
    $searchValue = $request->getPost('search')['value'];
    
    $startDate = $request->getPost('start_date');
    $endDate = $request->getPost('end_date');

    $query = $this->viewStockModel
        ->select('view_stock_with_sisa_per_unit.*, tbl_tipe_barang.jenis_barang, tbl_tipe_barang.satuan_dasar')
        ->join('tbl_tipe_barang', 'view_stock_with_sisa_per_unit.id_tipe = tbl_tipe_barang.id_tipe')
        ->orderBy('tgl_stock', 'DESC');

    if (!empty($startDate) && !empty($endDate)) {
        $query->where('tgl_stock >=', $startDate)
              ->where('tgl_stock <=', $endDate);
    }

    if (!empty($searchValue)) {
        $query->groupStart()
              ->like('tgl_stock', $searchValue)
              ->orLike('jenis_barang', $searchValue)
              ->orLike('satuan_dasar', $searchValue)
              ->groupEnd();
    }

    $totalRecords = $this->viewStockModel->countAllResults(false);
    $filteredRecords = $query->countAllResults(false);
    $data = $query->findAll($length, $start);

    $response = [
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => array_map(function($item) {
            return [
                'no' => '',
                'tgl_stock' => date('d-m-Y', strtotime($item['tgl_stock'])),
                'tgl_stock_sort' => $item['tgl_stock'],
                'jenis_barang' => $item['jenis_barang'],
                'satuan_dasar' => $item['satuan_dasar'],
                'total_stock' => number_format($item['total_stock'], 2, ',', '.'),
                'total_pembelian' => number_format($item['total_pembelian'], 2, ',', '.'),
                'total_penjualan' => number_format($item['total_penjualan'], 2, ',', '.'),
                'sisa_stok' => number_format($item['sisa_stok'], 2, ',', '.')
            ];
        }, $data)
    ];

    return $this->response->setJSON($response);
}
    
    public function save()
    {
        // Get data from request
        $jenis_barang = strtolower($this->request->getPost('jenis_barang'));
        $satuan_dasar = $this->request->getPost('satuan_dasar');
        $tipe_unit = $this->request->getPost('tipe_unit');
        // Sanitize input for jumlah and harga
        $standar_jumlah_barang = str_replace('.', '', $this->request->getPost('standar_jumlah_barang'));
        $standar_jumlah_barang = str_replace(',', '.', $standar_jumlah_barang);
        $standar_jumlah_barang = (float)$standar_jumlah_barang;

        $standar_harga_jual = str_replace('.', '', $this->request->getPost('standar_harga_jual'));
        $standar_harga_jual = str_replace(',', '.', $standar_harga_jual);
        $standar_harga_jual = (float)$standar_harga_jual;
    
        // Validation
        if (empty($jenis_barang)) {
            return $this->response->setJSON(['error' => 'Nama barang tidak boleh kosong!']);
        }
    
        // Start transaction
        $this->barangModel->transStart();
    
        try {
            // Check if the jenis_barang already exists in tbl_tipe_barang
            $existingBarang = $this->barangModel
                ->where('LOWER(jenis_barang)', $jenis_barang)
                ->first();
    
            if ($existingBarang) {
                // Use existing id_tipe
                $id_tipe = $existingBarang['id_tipe'];
    
                // Check if the exact unit combination already exists
                $existingUnit = $this->unitBarangModel
                    ->where('id_tipe', $id_tipe)
                    ->where('tipe_unit', $tipe_unit)
                    ->first();
    
                if ($existingUnit) {
                    throw new \Exception('Unit untuk barang ini sudah ada.');
                }
            } else {
                // Insert new barang if it doesn't exist
                $barangData = [
                    'jenis_barang' => $jenis_barang,
                    'satuan_dasar' => $satuan_dasar
                ];
                $this->barangModel->insert($barangData);
                $id_tipe = $this->barangModel->getInsertID();
            }
    
            // Prepare unit barang data
            $unitBarangData = [
                'id_tipe' => $id_tipe,
                'tipe_unit' => $tipe_unit,
                'standar_jumlah_barang' => $standar_jumlah_barang,
                'standar_harga_jual' => $standar_harga_jual,
                'tanggal' => date('Y-m-d') // Add current date
            ];
    
            // Insert unit barang data
            $this->unitBarangModel->insert($unitBarangData);
    
            // Check if transaction was successful
            if ($this->barangModel->transStatus() === FALSE) {
                throw new \Exception('Gagal menyimpan data');
            }
    
            // Commit transaction
            $this->barangModel->transComplete();
            
            return $this->response->setJSON(['message' => 'Data berhasil disimpan!']);
    
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            $this->barangModel->transRollback();
            return $this->response->setJSON(['error' => $e->getMessage()]);
        }
    }
    
    public function getTipeBarang()
    {
        $data = $this->barangModel->findAll();
    
        // Return an empty array if no data is found
        $options = [];
        foreach ($data as $item) {
            $options[] = [
                'id' => (int) $item['id_tipe'], // Ensure type consistency
                'jenis_barang' => $item['jenis_barang'],
                'satuan_dasar' => $item['satuan_dasar'],
            ];
        }
    
        return $this->response->setJSON($options);
    }
    

    public function edit()
    {
        // Get both IDs from the request
        $id_tipe = $this->request->getPost('id_tipe');
        $id_unit = $this->request->getPost('id_unit');
        
        // Debug log
        log_message('debug', 'Received id_tipe: ' . $id_tipe . ' and id_unit: ' . $id_unit);
        
        // Use the model method to get the specific record
        $data = $this->barangModel->getSpecificUnit($id_tipe, $id_unit);
        
        // Debug log
        log_message('debug', 'Retrieved data: ' . json_encode($data));
        
        if ($data) {
            return $this->response->setJSON($data);
        } else {
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Record not found'
            ])->setStatusCode(404);
        }
    }
    
    public function update()
    {
        // Get data from the request
        $id = $this->request->getPost('id_tipe');
        $jenis_barang = $this->request->getPost('jenis_barang');
        $satuan_dasar = $this->request->getPost('satuan_dasar');
        $tipe_unit = $this->request->getPost('tipe_unit');
        // Sanitize input for jumlah and harga
        $standar_jumlah_barang = str_replace('.', '', $this->request->getPost('standar_jumlah_barang'));
        $standar_jumlah_barang = str_replace(',', '.', $standar_jumlah_barang);
        $standar_jumlah_barang = (float)$standar_jumlah_barang;

        $standar_harga_jual = str_replace('.', '', $this->request->getPost('standar_harga_jual'));
        $standar_harga_jual = str_replace(',', '.', $standar_harga_jual);
        $standar_harga_jual = (float)$standar_harga_jual;
    
        $unit_id = $this->request->getPost('id_unit'); // Ensure this matches your form input
    
        try {
            // Explicitly use where clause for update
            $this->barangModel->where('id_tipe', $id)->set([
                'jenis_barang' => $jenis_barang,
                'satuan_dasar' => $satuan_dasar
            ])->update();
    
            // Use where clause for unit barang update
            $this->unitBarangModel->where('id_unit', $unit_id)->set([
                'tipe_unit' => $tipe_unit,
                'standar_jumlah_barang' => $standar_jumlah_barang,
                'standar_harga_jual' => $standar_harga_jual,
                'tanggal' => date('Y-m-d') // Update date on modification
            ])->update();
    
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }


    public function delete()
    {
        $id_tipe = $this->request->getPost('id_tipe');
        $id_unit = $this->request->getPost('id_unit'); // Optional parameter
    
        try {
            // If id_unit is provided, check for related sales details
            if ($id_unit) {
                // Check if this specific unit is used in any sales details
                $relatedSales = $this->detailPenjualanModel
                    ->where('id_tipe', $id_tipe)
                    ->where('id_unit', $id_unit)
                    ->countAllResults();
    
                if ($relatedSales > 0) {
                    return $this->response->setJSON([
                        'error' => 'Unit tidak dapat dihapus karena sudah digunakan dalam transaksi penjualan'
                    ])->setStatusCode(400);
                }
    
                // Proceed with deletion if no related sales
                $deleted = $this->unitBarangModel
                    ->where('id_tipe', $id_tipe)
                    ->where('id_unit', $id_unit)
                    ->delete();
                
                if (!$deleted) {
                    return $this->response->setJSON([
                        'error' => 'Unit tidak ditemukan atau sudah dihapus'
                    ])->setStatusCode(404);
                }
    
                return $this->response->setJSON([
                    'message' => 'Unit berhasil dihapus!'
                ]);
            }
    
            // If no id_unit, check for any related sales for this item type
            $relatedSales = $this->detailPenjualanModel
                ->where('id_tipe', $id_tipe)
                ->countAllResults();
    
            if ($relatedSales > 0) {
                return $this->response->setJSON([
                    'error' => 'Barang tidak dapat dihapus karena sudah digunakan dalam transaksi penjualan'
                ])->setStatusCode(400);
            }
    
            // Proceed with deletion of all units and the main item
            $this->unitBarangModel->where('id_tipe', $id_tipe)->delete();
            $this->barangModel->delete($id_tipe);
    
            return $this->response->setJSON([
                'message' => 'Barang dan unit terkait berhasil dihapus!'
            ]);
    
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // public function get($id)
    // {
    //     // Use $this->barangModel to retrieve data by ID
    //     $data = $this->barangModel->find($id);

    //     return $this->response->setJSON($data);
    // }
}
