<?php

namespace App\Models;

use CodeIgniter\Model;

class ViewSummayMargineBarangModel extends Model
{
    protected $table            = 'vw_summary_per_barang';
    // protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    // protected $useSoftDeletes   = false;
    // protected $protectFields    = true;
    protected $allowedFields    = ['tgl_penjualan', 'margine', 'jumlah_transaksi', 'jumlah_cash', 'jumlah_transfer', 'jumlah_piutang', 'jenis_barang'];

}
