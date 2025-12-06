<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'tbl_penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'id_tipe',
        'tgl_penjualan',
        'total_barang_keluar',
        'total_harga_jual',
        'total_harga_modal',
        'total_untung'
    ];


    
}
