<?php

namespace App\Models;

use CodeIgniter\Model;

class ViewBulekModel extends Model
{
    protected $table            = 'vw_bulek_totals';
    // protected $primaryKey       = 'id';
    // protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    protected $allowedFields    = ['id_tipe','total_keseluruhan', 'total_disetor','total_sisa_profit'];

    public function getBulekData()
    {
        $builder = $this->db->table('vw_bulek_totals');
        $builder->select('vw_bulek_totals.id_tipe, vw_bulek_totals.total_keseluruhan, vw_bulek_totals.total_disetor, vw_bulek_totals.total_sisa_profit, tbl_tipe_barang.jenis_barang');
        $builder->join('tbl_tipe_barang', 'vw_bulek_totals.id_tipe = tbl_tipe_barang.id_tipe');
        return $builder->get()->getResultArray();
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
