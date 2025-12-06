<?php

namespace App\Models;

use CodeIgniter\Model;

class ViewMeityModel extends Model
{
    protected $table            = 'vw_meity';
    protected $primaryKey       = 'id_pembelian';
    // protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    protected $allowedFields = [
        'tgl_masuk', 'barang_masuk', 'harga_modal_barang', 'total_meity', 'terkumpul', 'hutang', 'jenis_barang', 'satuan_dasar', 'sudah_setor', 'keterangan', 'status', 'current_piutang', 'current_sisa','current_transfer', 'is_cash', 'total_cash', 'jumlah_cash'
    ];
    
    public function getMeityByPembelian($id_pembelian)
    {
        return $this->where('id_pembelian', $id_pembelian)->first(); // Change from findAll() to first()
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
