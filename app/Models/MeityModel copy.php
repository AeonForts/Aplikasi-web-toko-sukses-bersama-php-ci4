<?php

namespace App\Models;

use CodeIgniter\Model;

class MeityModel extends Model
{
    protected $table            = 'tbl_meity';
    protected $primaryKey       = 'id_meity';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_pembelian',
        'terkumpul',
        'sudah_setor',
        'keterangan',
        'id_total'
    ];
    
    public function getCashId($id)
    {
        return $this->where('id_payment', $id)->findAll();
    }

    public function getPembelianId($id)
    {
        return $this->where('id_pembelian', $id)->findAll();
    }
    public function getTotalId($id)
    {
        return $this->where('id_total', $id)->findAll();
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
