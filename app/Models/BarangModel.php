<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'tbl_tipe_barang';
    protected $primaryKey       = 'id_tipe';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['jenis_barang','satuan_dasar'];


    public function units()
    {
        return $this->hasMany(UnitBarangModel::class, 'id_tipe', 'id_tipe');
    }
    
    public function getAllStockWithJenisBarang()
    {
        // Use Query Builder to join view with tbl_tipe_barang
        return $this->db->table($this->table)
            ->select('view_stock_with_sisa_per_unit.*, tbl_tipe_barang.jenis_barang')
            ->join('tbl_tipe_barang', 'view_stock_with_sisa_per_unit.id_tipe = tbl_tipe_barang.id_tipe')
            ->get()
            ->getResultArray();
    }

    public function getBarangWithUnit($id = null, $unit_id = null, $perPage = 10)
    {
        // Base query with JOIN between tbl_tipe_barang and tbl_unit_barang
        $builder = $this->select('tbl_tipe_barang.id_tipe, 
                                 tbl_tipe_barang.jenis_barang, 
                                 tbl_tipe_barang.satuan_dasar, 
                                 tbl_unit_barang.id_unit,
                                 tbl_unit_barang.tanggal,
                                 tbl_unit_barang.tipe_unit, 
                                 tbl_unit_barang.standar_jumlah_barang, 
                                 tbl_unit_barang.standar_harga_jual')
                        ->join('tbl_unit_barang', 'tbl_unit_barang.id_tipe = tbl_tipe_barang.id_tipe', 'left');
        
        // If both IDs are provided, fetch a specific record
        if ($id !== null && $unit_id !== null) {
            return $builder->where('tbl_tipe_barang.id_tipe', $id)
                          ->where('tbl_unit_barang.id_unit', $unit_id)
                          ->first();
        }
        // If only tipe_id is provided
        else if ($id !== null) {
            $builder->where('tbl_tipe_barang.id_tipe', $id);
            return $builder->first();
        }
        
        // Add a flag to control deduplication
        $deduplicateResults = true;
        
        // Paginate results if no specific IDs are provided
        $results = $builder->findAll();
        
        // Optional deduplication with a flag
        if ($deduplicateResults) {
            $uniqueResults = [];
            $seenCombinations = [];
            
            foreach ($results as $result) {
                $key = $result['id_tipe'] . '_' . $result['id_unit'];
                
                if (!isset($seenCombinations[$key])) {
                    $uniqueResults[] = $result;
                    $seenCombinations[$key] = true;
                }
            }
            
            $results = $uniqueResults;
        }
        
        return $results;
    }
    
    // Add a specific method for getting a single unit record
    public function getSpecificUnit($id_tipe, $id_unit)
    {
        return $this->select('tbl_tipe_barang.id_tipe, 
                             tbl_tipe_barang.jenis_barang, 
                             tbl_tipe_barang.satuan_dasar, 
                             tbl_unit_barang.id_unit,
                             tbl_unit_barang.tipe_unit, 
                             tbl_unit_barang.standar_jumlah_barang, 
                             tbl_unit_barang.standar_harga_jual')
                    ->join('tbl_unit_barang', 'tbl_unit_barang.id_tipe = tbl_tipe_barang.id_tipe', 'left')
                    ->where('tbl_tipe_barang.id_tipe', $id_tipe)
                    ->where('tbl_unit_barang.id_unit', $id_unit)
                    ->first();
    }
    

    
    // public function getAllTipeBarang()
    // {
    //     return $this->findAll();
    // }
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
