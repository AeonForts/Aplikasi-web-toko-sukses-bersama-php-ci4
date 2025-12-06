<?php

namespace App\Models;

use CodeIgniter\Model;

class StockBarangModel extends Model
{
    protected $table            = 'tbl_stock';
    protected $primaryKey       = 'id_stock';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_tipe','stock_barang','barang_masuk','barang_keluar','tgl_stock'];

    
}
