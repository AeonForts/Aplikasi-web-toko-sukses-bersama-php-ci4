<?php
namespace App\Models;

use CodeIgniter\Model;

class ViewSummaryModel extends Model
{
    protected $table            = 'vw_summary';
    protected $primaryKey       = 'id_penjualan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'tgl_penjualan',
        'margine',
        'biaya',
        'margine_bersih',
        'jumlah_transaksi',
        'jumlah_cash',
        'jumlah_transfer',
        'jumlah_piutang',
        'jenis_barang'
    ];

    // Optional: Add a method to check data
    public function checkData()
    {
        $data = $this->findAll();
        return [
            'total_rows' => count($data),
            'sample_row' => $data ? $data[0] : null
        ];
    }
}