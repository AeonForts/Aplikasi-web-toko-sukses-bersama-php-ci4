<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitBarangModel extends Model
{
    protected $table            = 'tbl_unit_barang';
    protected $primaryKey       = 'id_unit';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_tipe', 
        'tipe_unit',
        'standar_jumlah_barang', 
        'standar_harga_jual',
        'tanggal'
    ];

    // Relationship with TipeBarangModel
    public function tipe()
    {
        return $this->belongsTo(TipeBarangModel::class, 'id_tipe', 'id_tipe');
    }

    // Method to get units by type
    public function getUnitsByTipeId($id_tipe)
    {
        return $this->where('id_tipe', $id_tipe)->findAll();
    }

    // Method to get default unit for a specific type
    public function getDefaultUnit($id_tipe)
    {
        return $this->where('id_tipe', $id_tipe)
                    ->orderBy('standar_jumlah_barang', 'ASC')
                    ->first();
    }

    // Method to get unit details with type information
    public function getUnitDetailsWithType($id_unit)
    {
        return $this->select('tbl_unit_barang.*, tbl_tipe_barang.jenis_barang, tbl_tipe_barang.satuan_dasar')
                    ->join('tbl_tipe_barang', 'tbl_tipe_barang.id_tipe = tbl_unit_barang.id_tipe')
                    ->where('tbl_unit_barang.id_unit', $id_unit)
                    ->first();
    }
}