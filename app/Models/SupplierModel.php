<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'tbl_supplier';
    protected $primaryKey = 'id_supplier';
    protected $useAutoIncrement = true;

    protected $allowedFields = ['nama_supplier','alamat','no_telp'];

    public function getIdSupplierByName($nama_supplier)
    {
        return $this->where('nama_supplier', $nama_supplier)->first()['id_supplier'] ?? null;
    }

    public function insertSupplier($nama_supplier)
    {
        $this->insert(['nama_supplier' => $nama_supplier]);
        return $this->insertID();
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
