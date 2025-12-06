<?php

namespace App\Models;

use CodeIgniter\Model;

class SisaTerkumpulModel extends Model
{
    protected $table            = 'tbl_sisa_terkumpul';
    protected $primaryKey       = 'id_sisa_terkumpul';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_meity',
        'id_tipe', 
        'sisa_terkumpul', 
        'tgl_sisa_terkumpul'
    ];

    public function getSisaTerkumpulByTipe($id_tipe)
    {
        return $this->where('id_tipe', $id_tipe)
                    ->orderBy('tgl_sisa_terkumpul', 'ASC')
                    ->first();
    }

    public function useSisaTerkumpul($id_tipe, $amount)
    {
        $sisaTerkumpul = $this->getSisaTerkumpulByTipe($id_tipe);

        if (!$sisaTerkumpul) {
            return 0;
        }

        if ($sisaTerkumpul['sisa_terkumpul'] <= $amount) {
            // Use entire sisa terkumpul
            $usedAmount = $sisaTerkumpul['sisa_terkumpul'];
            $this->delete($sisaTerkumpul['id_sisa_terkumpul']);
            return $usedAmount;
        } else {
            // Partially use sisa terkumpul
            $this->update($sisaTerkumpul['id_sisa_terkumpul'], [
                'sisa_terkumpul' => $sisaTerkumpul['sisa_terkumpul'] - $amount
            ]);
            return $amount;
        }
    }
}
