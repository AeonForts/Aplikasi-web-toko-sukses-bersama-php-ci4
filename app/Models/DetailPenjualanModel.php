<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPenjualanModel extends Model
{
    protected $table            = 'tbl_detail_penjualan';
    protected $primaryKey       = 'id_detail_penjualan';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_penjualan',
        'id_pembelian',
        'id_customer',
        'id_tipe',
        'id_payment',
        'id_unit',
        'jumlah_keluar',
        'harga_jual',
        'status'
    ];

    public function updateDetailStatusToLunas($id_detail_penjualan)
    {
        return $this->where('id_detail_penjualan', $id_detail_penjualan)
                    ->set(['status' => 1]) // Set status to lunas
                    ->update();
    }

    public function getDetailPenjualanByPenjualanId($id)
    {
        return $this->where('id_penjualan', $id)->findAll();
    }
    public function getDetailPenjualanByCustomerId($id)
    {
        return $this->where('id_customer', $id)->findAll();
    }
    public function getDetailPenjualanByPembelianId($id)
    {
        return $this->where('id_pembelian', $id)->findAll();
    }
    public function getDetailPenjualanByPaymentId($id)
    {
        return $this->where('id_payment', $id)->findAll();
    }
    public function getTipeBarangId($id)
    {
        return $this->where('id_tipe', $id)->findAll(); // This should work as expected
    }
    

    
    // protected bool $allowEmptyInserts = false;
    // protected bool $updateOnlyChanged = true;

    // protected array $casts = [];
    // protected array $castHandlers = [];

    // // Dates
    // protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // // Validation
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = false;
    // protected $cleanValidationRules = true;

    // // Callbacks
    // protected $allowCallbacks = true;
    // protected $beforeInsert   = [];
    // protected $afterInsert    = [];
    // protected $beforeUpdate   = [];
    // protected $afterUpdate    = [];
    // protected $beforeFind     = [];
    // protected $afterFind      = [];
    // protected $beforeDelete   = [];
    // protected $afterDelete    = [];
}
