<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{StockBarangModel,BarangModel,UnitBarangModel}
use CodeIgniter\HTTP\ResponseInterface;

class StockController extends BaseController
{
    protected $stockBarangModel;
    protected $unitBarangModel;
    protected $barangModel;

    public function __construct()
    {
        $this->stockBarangModel = new StockBarangModel()
        $this->barangModel = new BarangModel();
        $this->unitBarangModel = new UnitBarangModel();
    }

    public function list()
    {
        return view('pages/admin/barang/list_raw_stock');
    }

    public function getDatatables()
    {

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

    public function save()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function delete()
    {
        
    }
}
