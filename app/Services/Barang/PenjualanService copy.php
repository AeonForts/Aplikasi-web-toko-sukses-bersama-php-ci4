<?php
namespace App\Services;

use App\Models\{PenjualanModel, DetailPenjualanModel,DetailPembelianModel,MeityModel,PembelianModel,SisaTerkumpulModel};
use App\Services\PaymentService;
use exception;

class PenjualanService
{
    protected $penjualanModel;
    protected $detailPenjualanModel;
    protected $detailPembelianModel;
    protected $meityModel;
    protected $paymentService;
    protected $pembelianModel;
    protected $sisaTerkumpulModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->detailPembelianModel = new DetailPembelianModel();
        $this->meityModel = new MeityModel();
        $this->paymentService = new PaymentService();
        $this->pembelianModel = new PembelianModel();
        $this->sisaTerkumpulModel = new SisaTerkumpulModel();
    }

    public function getLatestPembelian($id_tipe)
    {
        // Debugging: Log the id_tipe being processed
        log_message('debug', 'Processing id_tipe: ' . $id_tipe);
    
        // First, try to find the latest pembelian associated with a Belum Lunas (status 0) Meity record
        $pembelian = $this->detailPembelianModel
            ->select('tbl_detail_pembelian.*, tp.tgl_masuk, tp.id_supplier')
            ->join('tbl_pembelian tp', 'tbl_detail_pembelian.id_pembelian = tp.id_pembelian')
            ->join('tbl_meity tm', 'tp.id_pembelian = tm.id_pembelian', 'left')
            ->where('tbl_detail_pembelian.id_tipe', $id_tipe)
            ->where('tm.status', 0) // Only select records associated with Belum Lunas meity
            ->orderBy('tbl_detail_pembelian.id_detail_pembelian', 'ASC')
            ->first();
    
        // If no record found with Belum Lunas status, fall back to the latest record
        if (!$pembelian) {
            $pembelian = $this->detailPembelianModel
                ->select('tbl_detail_pembelian.*, tp.tgl_masuk, tp.id_supplier')
                ->join('tbl_pembelian tp', 'tbl_detail_pembelian.id_pembelian = tp.id_pembelian')
                ->where('tbl_detail_pembelian.id_tipe', $id_tipe)
                ->orderBy('tbl_detail_pembelian.id_detail_pembelian', 'ASC')
                ->first();
        }
    
        // Additional logging for debugging
        if (!$pembelian) {
            log_message('error', 'No pembelian record found for id_tipe: ' . $id_tipe);
            throw new \Exception("No stock available for this item type.");
        }
    
        return $pembelian;
    }

    public function calculateTotals($jumlah_keluar, $harga_jual, $harga_modal_barang)
    {
        function roundToNearestFiveHundred($value) {
            // If the remainder is less than 250, round down
            // If the remainder is 250 or more, round up
            $remainder = $value % 500;
            
            if ($remainder < 250) {
                // Round down to nearest 500
                return floor($value / 500) * 500;
            } else {
                // Round up to nearest 500
                return ceil($value / 500) * 500;
            }
        }
    
        $total_harga_jual = roundToNearestFiveHundred($harga_jual * $jumlah_keluar);
        $total_harga_modal = roundToNearestFiveHundred($harga_modal_barang * $jumlah_keluar);
        $total_untung = roundToNearestFiveHundred($total_harga_jual - $total_harga_modal);
    
        return [
            'total_harga_jual' => $total_harga_jual,
            'total_harga_modal' => $total_harga_modal,
            'total_untung' => $total_untung
        ];
    }

    public function updateOrCreatePenjualan($id_tipe, $jumlah_keluar, $totals, $status, $tgl_penjualan)
    {
        // Log the incoming parameters
        log_message('info', "updateOrCreatePenjualan called with id_tipe: $id_tipe, jumlah_keluar: $jumlah_keluar, status: $status");
    
        // Find existing record for today and this type
        $existingRecord = $this->penjualanModel
        ->where('id_tipe', $id_tipe)
        ->where('tgl_penjualan', $tgl_penjualan)
        ->first();
    
        if ($existingRecord) {
            log_message('info', "Found existing penjualan record with id_penjualan: {$existingRecord['id_penjualan']}");
    
            // If status is 0 (belum lunas), return the existing penjualan ID without updating totals
            if ($status === 0) {
                log_message('info', "Status is 0 (belum lunas). Returning existing id_penjualan: {$existingRecord['id_penjualan']}");
                return $existingRecord['id_penjualan'];
            }
    
            // Prepare update data
            $updateData = [
                'total_harga_jual' => $existingRecord['total_harga_jual'] + $totals['total_harga_jual'],
                'total_harga_modal' => $existingRecord['total_harga_modal'] + $totals['total_harga_modal'],
                'total_untung' => $existingRecord['total_untung'] + $totals['total_untung'],
                'total_barang_keluar' => $existingRecord['total_barang_keluar'] + $jumlah_keluar
            ];
    
            // Update using the correct primary key
            $this->penjualanModel
                ->where('id_penjualan', $existingRecord['id_penjualan'])
                ->set($updateData)
                ->update();
    
            log_message('info', "Updated existing penjualan record with id_penjualan: {$existingRecord['id_penjualan']}. New totals: " . json_encode($updateData));
            return $existingRecord['id_penjualan']; // Return the existing penjualan ID
        }
    
        // Create new record if no existing record
        if ($status === 0) {
            $insertData = [
                'tgl_penjualan' => $tgl_penjualan,
                'id_tipe' => $id_tipe,
                'total_harga_jual' => 0, // No totals for belum lunas
                'total_harga_modal' => 0,
                'total_untung' => 0,
                'total_barang_keluar' => 0
            ];
            log_message('info', "No existing record found. Creating new penjualan record for piutang.");
        } else {
            $insertData = [
                'tgl_penjualan' => $tgl_penjualan,
                'id_tipe' => $id_tipe,
                'total_harga_jual' => $totals['total_harga_jual'],
                'total_harga_modal' => $totals['total_harga_modal'],
                'total_untung' => $totals['total_untung'],
                'total_barang_keluar' => $jumlah_keluar
            ];
            log_message('info', "Creating new penjualan record with totals: " . json_encode($insertData));
        }
    
        $newId = $this->penjualanModel->insert($insertData); // Insert and return the new penjualan ID
        log_message('info', "New penjualan record created with id_penjualan: $newId");
    
        return $newId;
    }
    
    public function insertDetailPenjualan($id_penjualan, $id_customer, $input, $id_pembelian)
    {
        // Insert payment record first
        $id_payment = $this->paymentService->insertPayment(
            $input['jumlah'], 
            $input['id_method'], 
            $id_customer, 
            $id_penjualan
        ); 
    
        // Determine status
        $status = $input['id_method'] == 1 ? 0 : 1; // 0 for Piutang, 1 for Lunas
    
        $detailPenjualanData = [
            'id_customer' => $id_customer,
            'id_penjualan' => $id_penjualan, // This will always be set
            'jumlah_keluar' => $input['jumlah_keluar'],
            'harga_jual' => $input['harga_jual'],
            'id_pembelian' => $id_pembelian,
            'id_tipe' => $input['id_tipe'],
            'id_unit' => $input['id_unit'], 
            'id_payment' => $id_payment,
            'status' => $status  // 0 for Piutang, 1 for other payment methods
        ];
    
        $this->detailPenjualanModel->insert($detailPenjualanData);
    
        return $id_payment;
    }

    public function updateLatestTerkumpul($id_pembelian, $totalHargaModal, $id_tipe)
    {
        // Find the latest detail penjualan for this type
        $latestDetailPenjualan = $this->detailPenjualanModel
            ->select('tbl_detail_penjualan.*, tp.id_method')
            ->join('tbl_payment tp', 'tbl_detail_penjualan.id_payment = tp.id_payment', 'left')
            ->where('tbl_detail_penjualan.id_tipe', $id_tipe)
            ->orderBy('id_detail_penjualan', 'DESC')
            ->first();
    
        // Log input parameters for debugging
        log_message('debug', 'updateLatestTerkumpul Input: ' . json_encode([
            'id_pembelian' => $id_pembelian,
            'totalHargaModal' => $totalHargaModal,
            'id_tipe' => $id_tipe,
            'payment_method' => $latestDetailPenjualan['id_method'] ?? 'No record found',
            'transaction_status' => $latestDetailPenjualan['status'] ?? 'No record found'
        ]));
    
        // Get the payment method from the latest detail penjualan
        $paymentMethod = $latestDetailPenjualan['id_method'] ?? null;
    
        // Only proceed with terkumpul update if the transaction is LUNAS (status = 1)
        if (!$latestDetailPenjualan || $latestDetailPenjualan['status'] == 0) {
            log_message('info', 'Cannot update terkumpul: Transaction not Lunas');
            return false;
        }
        
        // Find the latest meity record for this type
        $latestMeityRecord = $this->meityModel
            ->where('id_tipe', $id_tipe)
            ->orderBy('id_meity', 'DESC')
            ->first();
    
        // Get the total_meity from the corresponding pembelian
        $pembelianModel = new PembelianModel();
        $pembelian = $pembelianModel->find($id_pembelian);
    
        if (!$pembelian) {
            log_message('error', 'No Pembelian record found for id_pembelian: ' . $id_pembelian);
            return false;
        }
    
        $total_meity = $pembelian['total_meity'];
    
        // Calculate current terkumpul
        $current_terkumpul = $latestMeityRecord['terkumpul'] ?? 0;
        $new_terkumpul = $current_terkumpul + $totalHargaModal;
    
        // Prepare update data
        $updateData = [
            'terkumpul' => $new_terkumpul
        ];
    
        // Handle different payment methods
        switch ($paymentMethod) {
            case 1: // Piutang
                $updateData['current_piutang'] = ($latestMeityRecord['current_piutang'] ?? 0) + $totalHargaModal;
                break;
            case 2: // Transfer
                $updateData['current_transfer'] = ($latestMeityRecord['current_transfer'] ?? 0) + $totalHargaModal;
                break;
            default: // Cash or other methods
                // No additional handling needed as terkumpul is already updated
                break;
        }
    
        // If new_terkumpul exceeds total_meity, handle excess
        if ($new_terkumpul > $total_meity) {
            $sisaTerkumpulModel = new SisaTerkumpulModel();
            $sisa = $new_terkumpul - $total_meity;
            
            $sisaData = [
                'id_meity' => $latestMeityRecord['id_meity'], 
                'id_tipe' => $id_tipe,
                'sisa_terkumpul' => $sisa,
                'tgl_sisa_terkumpul' => date('Y-m-d')
            ];
    
            try {
                $sisaTerkumpulModel->insert($sisaData);
            } catch (\Exception $e) {
                log_message('error', 'Failed to insert Sisa Terkumpul: ' . $e->getMessage());
            }
    
            // Set terkumpul to exactly match total_meity
            $updateData['terkumpul'] = $total_meity;
        }
    
        // Determine status
        $updateData['status'] = $this->calculateStatus($total_meity, $updateData['terkumpul'], $latestMeityRecord['status'] ?? null);
    
        // Update the meity record
        return $this->meityModel->update($latestMeityRecord['id_meity'], $updateData);
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

}

