<?php

namespace App\Models;

use CodeIgniter\Model;

class ViewDetailPenjualanModel extends Model
{
    protected $table            = 'vw_detail_penjualan';
    protected $primaryKey       = 'id_penjualan';
    // protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_customer','id_detail_penjualan', 'jumlah_keluar', 'harga_modal_barang', 'total_harga_modal', 'harga_jual', 'total_harga_jual', 'untung_telur', 'jenis_barang','tipe_unit','jumlah','nama_method',  'status' ];
    public function getDetailByPenjualanId($id_penjualan)
    {
        return $this->where('id_penjualan', $id_penjualan)->findAll(); // Use findAll for multiple rows
    }

    public function getDetailByDetailPenjualanId($id_detail_penjualan)
    {
        return $this->where('id_detail_penjualan', $id_detail_penjualan)->first(); // Use first() for a single row
    }
    
    public function getDetailPenjualanWithMethod($id_detail_penjualan)
    {
        // Get detail from view
        $detailPenjualan = $this->getDetailByDetailPenjualanId($id_detail_penjualan);
        
        if (!$detailPenjualan) {
            return null;
        }
        
        // Get the id_payment from the original table
        $builder = $this->db->table('tbl_detail_penjualan');
        $query = $builder->select('id_payment')
                        ->where('id_detail_penjualan', $id_detail_penjualan)
                        ->get();
        
        $original = $query->getRowArray();
        
        if ($original && isset($original['id_payment'])) {
            // Add the id_payment to the detail object
            $detailPenjualan['id_payment'] = $original['id_payment'];
            
            // Get payment method
            $paymentModel = new PaymentModel();
            $payment = $paymentModel->where('id_payment', $original['id_payment'])->first();
            
            if ($payment) {
                $detailPenjualan['id_method'] = $payment['id_method'];
            }
        }
        
        return $detailPenjualan;
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
