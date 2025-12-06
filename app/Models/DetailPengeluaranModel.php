<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPengeluaranModel extends Model
{
    protected $table            = 'tbl_detail_pengeluaran';
    protected $primaryKey       = 'id_detail_pengeluaran';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_pengeluaran',
        'jumlah_biaya',
        'keterangan'
    ];
    
    public function getDetailByPengeluaranId($id)
    {
        return $this->where('id_pengeluaran', $id)->findAll();
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
