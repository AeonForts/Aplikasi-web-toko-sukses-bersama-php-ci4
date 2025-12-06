<?php

namespace App\Models;

use CodeIgniter\Model;

class ViewStockModel extends Model
{
    protected $table = 'view_stock_with_sisa_per_unit';

    // Views are read-only, so no primary key or timestamps are required.
    protected $primaryKey = null; // Set to null because the view has no explicit primary key
    protected $useTimestamps = false;

    // Define fields (optional, based on columns in the view)
    protected $allowedFields = [
        'id_tipe', 
        'tgl_stock', 
        'total_stock', 
        'total_pembelian', 
        'total_penjualan', 
        'sisa_stok'
    ];
}
