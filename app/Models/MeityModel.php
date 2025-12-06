<?php

namespace App\Models;

use CodeIgniter\Model;

class MeityModel extends Model
{
    protected $table            = 'tbl_meity';
    protected $primaryKey       = 'id_meity';
    protected $useAutoIncrement = true;
    protected $allowedFields    = [
        'id_pembelian',
        'id_tipe',
        'terkumpul',
        'current_piutang',
        'current_transfer',
        'current_sisa',
        'jumlah_cash',
        'hutang',
        'sudah_setor',
        'keterangan',
        'status',
        'is_cash'
    ];
    
    // Fetch the latest Meity record
    public function getLatestMeity()
    {
        return $this->orderBy('id_meity', 'DESC')->first();
    }

    // Update the terkumpul field using LIFO logic
    public function updateLatestTerkumpul($amount)
    {
        // Fetch the latest record
        $latestMeity = $this->getLatestMeity();

        if ($latestMeity) {
            // Calculate the new terkumpul value
            $newTerkumpul = $latestMeity['terkumpul'] + $amount;

            // Update the terkumpul value in the database
            $this->update($latestMeity['id_meity'], ['terkumpul' => $newTerkumpul]);
        } else {
            // Handle case where no Meity records exist
            throw new \Exception("No Meity records found to update");
        }
    }

    public function updateTerkumpul($id_meity, $new_terkumpul)
    {
        // Join with pembelian to get total_meity
        $meity = $this->select('tbl_meity.*, tp.total_meity')
            ->join('tbl_pembelian tp', 'tbl_meity.id_pembelian = tp.id_pembelian')
            ->where('tbl_meity.id_meity', $id_meity)
            ->first();
    
        if (!$meity) {
            throw new \Exception('Meity record not found');
        }
    
        $total_meity = $meity['total_meity'] ?? 0;
        $current_terkumpul = $meity['terkumpul'] ?? 0;
        $id_tipe = $meity['id_tipe'];
    
        // First, check for existing sisa terkumpul
        $sisaTerkumpulModel = new SisaTerkumpulModel();
        $sisaTerkumpulAmount = $sisaTerkumpulModel->useSisaTerkumpul($id_tipe, $new_terkumpul);
        
        // Adjust new_terkumpul after using sisa terkumpul
        $new_terkumpul += $sisaTerkumpulAmount;
    
        // Calculate final terkumpul
        $final_terkumpul = $current_terkumpul + $new_terkumpul;
    
        // If final_terkumpul is more than total_meity
        if ($final_terkumpul > $total_meity) {
            $sisa = $final_terkumpul - $total_meity;
            
            // Store excess in sisa_terkumpul
            $sisaTerkumpulModel->insert([
                'id_tipe' => $id_tipe,
                'sisa_terkumpul' => $sisa,
                'tgl_sisa_terkumpul' => date('Y-m-d')
            ]);
            
            // Set final_terkumpul to exactly match total_meity
            $final_terkumpul = $total_meity;
        }
    
        // Determine status
        $status = $this->calculateStatus($total_meity, $final_terkumpul);
    
        // Update the record
        $updateData = [
            'terkumpul' => $final_terkumpul,
            'status' => $status
        ];
    
        return $this->update($id_meity, $updateData);
    }
    
    private function calculateStatus($total_meity, $terkumpul, $currentStatus = null)
    {
        if ($terkumpul == 0) {
            return 0; // Belum Lunas
        } elseif ($terkumpul >= $total_meity) {
            // If the current status is already 2 (Lunas), maintain that status
            if ($currentStatus === 2) {
                return 2; // Lunas
            }
            return 1; // Menunggu Konfirmasi (automatically triggered)
        } elseif ($terkumpul > 0 && $terkumpul < $total_meity) {
            return 0; // Belum Lunas (partial payment)
        }
        
        return 0; // Default
    }

    
    public function getCashId($id)
    {
        return $this->where('id_payment', $id)->findAll();
    }

    public function getPembelianId($id)
    {
        return $this->where('id_pembelian', $id)->findAll();
    }
    public function getTotalId($id)
    {
        return $this->where('id_total', $id)->findAll();
    }
    public function getTipeBarangId($id)
    {
        return  $this->where('id_tipe', $id)->findAll();
    }

    // public function updateMeity($id_meity, $payment_data)
    // {
    //     $this->db->transStart();

    //     $meity = $this->find($id_meity);
    //     $pembelianModel = new PembelianModel();
    //     $pembelian = $pembelianModel->find($meity['id_pembelian']);

    //     $meity['terkumpul'] += $payment_data['jumlah'];

    //     switch ($payment_data['id_method']) {
    //         case 1: // Assuming 1 is piutang
    //             $meity['current_piutang'] += $payment_data['jumlah'];
    //             break;
    //         case 2: // Assuming 2 is transfer
    //             $meity['current_transfer'] += $payment_data['jumlah'];
    //             break;
    //     }

    //     $meity['hutang'] = $pembelian['total_meity'] - $meity['terkumpul'];

    //     $this->update($id_meity, $meity);

    //     // Update tbl_total_payment
    //     $paymentTotalModel = new PaymentTotalModel();
    //     $paymentTotalModel->updateTotals($meity['id_total'], $payment_data['jumlah'], $payment_data['id_method']);

    //     $this->db->transComplete();

    //     return $this->db->transStatus();
    // }

    // public function markAsCash($id_meity)
    // {
    //     $this->db->transStart();

    //     $meity = $this->find($id_meity);
    //     $updateData = [
    //         'is_cash' => true,
    //         'current_piutang' => 0,
    //         'current_transfer' => 0,
    //     ];

    //     $this->update($id_meity, $updateData);

    //     // Update tbl_total_payment
    //     $paymentTotalModel = new PaymentTotalModel();
    //     $paymentTotalModel->convertToCash($meity['id_total'], $meity['current_piutang'], $meity['current_transfer']);

    //     $this->db->transComplete();

    //     return $this->db->transStatus();
    // }

    // public function getCurrentTotals($id_pembelian)
    // {
    //     return $this->select('SUM(terkumpul) as total_terkumpul, SUM(current_piutang) as total_piutang, SUM(current_transfer) as total_transfer, SUM(hutang) as total_hutang')
    //                 ->where('id_pembelian', $id_pembelian)
    //                 ->where('is_cash', false)
    //                 ->get()
    //                 ->getRowArray();
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
