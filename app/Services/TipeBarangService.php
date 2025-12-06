<?php
namespace App\Services;

use App\Models\{BarangModel,StockBarangModel, UnitBarangModel};
use exception;

class TipeBarangService
{
    protected $barangModel;
    protected $unitBarangModel;
    protected $stockBarangModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->unitBarangModel = new UnitBarangModel();
        $this->stockBarangModel = new StockBarangModel();
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
}