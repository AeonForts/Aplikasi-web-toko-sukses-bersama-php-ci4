<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPembelianModel extends Model
{
    protected $table            = 'tbl_detail_pembelian';
    protected $primaryKey       = 'id_detail_pembelian';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_pembelian',
        'barang_masuk',
        'harga_modal_barang',
        'id_tipe'
    ];
    public function getTipeBarangId($id)
    {
        return $this->where('id_tipe', $id)->findAll(); // This should work as expected
    }

    public function getPembelianId($id)
    {
        return $this->where('id_pembelian', $id)->findAll();
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
