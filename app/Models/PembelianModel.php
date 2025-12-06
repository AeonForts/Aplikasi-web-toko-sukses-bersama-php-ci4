<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table = 'tbl_pembelian';
    protected $primaryKey = 'id_pembelian';
    protected $allowedFields = ['tgl_masuk', 'id_supplier','tgl_masuk', 'total_meity'];

    public function getPembelianDetail($id_pembelian, $id_tipe = null)
    {
        $query = $this->db->table('tbl_pembelian tp')
                          ->select('tp.*, dp.*, m.*, sp.*, tb.*')
                          ->join('tbl_supplier sp', 'tp.id_supplier = sp.id_supplier')
                          ->join('tbl_detail_pembelian dp', 'tp.id_pembelian = dp.id_pembelian')
                          ->join('tbl_meity m', 'tp.id_pembelian = m.id_pembelian')
                          ->join('tbl_tipe_barang tb', 'dp.id_tipe = tb.id_tipe')
                          ->where('tp.id_pembelian', $id_pembelian);
    
        // Only add this condition if $id_tipe is provided
        if ($id_tipe !== null) {
            $query->where('dp.id_tipe', $id_tipe);
        }
    
        return $query->get()->getRowArray();
    }
    

    public function getTipeBarang()
    {
        return $this->db->table('tbl_tipe_barang')->get()->getResultArray(); // Fetch all types of barang
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
