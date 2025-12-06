<?php

namespace App\Models;

use CodeIgniter\Model;

class ViewBulekTotalBiayaModel extends Model
{
    protected $table            = 'vw_bulek_totals_biaya';
    // protected $primaryKey       = 'id';
    // protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    protected $allowedFields    = ['id_tipe', 'total_keseluruhan', 'total_disetor', 'total_sisa_profit_before_pengeluaran', 'total_pengeluaran', 'total_sisa_profit'];

    public function getBulekData()
    {
        $builder = $this->db->table('vw_bulek_totals_biaya');
        $builder->select('vw_bulek_totals_biaya.id_tipe, vw_bulek_totals_biaya.total_keseluruhan, vw_bulek_totals_biaya.total_disetor, vw_bulek_totals_biaya.total_sisa_profit_before_pengeluaran,vw_bulek_totals_biaya.total_pengeluaran, vw_bulek_totals_biaya.total_sisa_profit, tbl_tipe_barang.jenis_barang');
        $builder->join('tbl_tipe_barang', 'vw_bulek_totals_biaya.id_tipe = tbl_tipe_barang.id_tipe');
        return $builder->get()->getResultArray();
    }
}
