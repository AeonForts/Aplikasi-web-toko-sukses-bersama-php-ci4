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
        // **Primary Search: Belum Lunas Status**
        $pembelian = $this->detailPembelianModel
            ->select('tbl_detail_pembelian.*, tp.tgl_masuk, tp.id_supplier')
            ->join('tbl_pembelian tp', 'tbl_detail_pembelian.id_pembelian = tp.id_pembelian')
            ->join('tbl_meity tm', 'tp.id_pembelian = tm.id_pembelian', 'left')
            ->where('tbl_detail_pembelian.id_tipe', $id_tipe)
            ->where('tm.status', 0) // Only select records associated with Belum Lunas meity
            ->orderBy('tbl_detail_pembelian.id_detail_pembelian', 'ASC')
            ->first();
    
        // **Error Handling: No Belum Lunas Stock**
        if (!$pembelian) {
            throw new \Exception(
                "Stok untuk jenis barang ini sudah sepenuhnya dialokasikan. " . 
                "Silakan masukkan stok baru dan dokumentasikan transaksi terlebih dahulu."
            );
        }
    
        return $pembelian;
    }

    public function calculateTotals($jumlah_keluar, $harga_jual, $harga_modal_barang)
    {
        $total_harga_jual = $harga_jual * $jumlah_keluar;
        $total_harga_modal = $harga_modal_barang * $jumlah_keluar;
        $total_untung = $total_harga_jual - $total_harga_modal;
    
        return [
            'total_harga_jual' => $total_harga_jual,
            'total_harga_modal' => $total_harga_modal,
            'total_untung' => $total_untung
        ];
    }
    
    // private function roundToNearestFiveHundred($value)
    // {
    //     $remainder = $value % 500;
        
    //     // Toleransi yang lebih ketat
    //     if ($remainder < 50 || $remainder > 450) {
    //         return round($value / 500) * 500;
    //     }
        
    //     // Prioritaskan nilai asli dengan sedikit penyesuaian
    //     if ($remainder >= 50 && $remainder <= 450) {
    //         // Pertahankan nilai asli atau sedikit penyesuaian
    //         return $value;
    //     }
        
    //     // Fallback
    //     return $value;
    // }

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

    public function updateLatestTerkumpul($id_pembelian, $totalHargaModal, $id_tipe, $paymentMethod = null)
    {
        // Log input parameters for debugging
        log_message('debug', 'updateLatestTerkumpul Input: ' . json_encode([
            'id_pembelian' => $id_pembelian,
            'totalHargaModal' => $totalHargaModal,
            'id_tipe' => $id_tipe,
            'payment_method' => $paymentMethod
        ]));
    
        // Find the latest meity record for this type, only with status 0 (Belum Lunas)
        $latestMeityRecord = $this->meityModel
            ->where('id_tipe', $id_tipe)
            ->where('id_pembelian', $id_pembelian) // Add this condition
            ->where('status', 0) // Only select records that are Belum Lunas
            ->orderBy('id_meity', 'ASC')
            ->first();
    
        // If no incomplete record exists, return false
        if (!$latestMeityRecord) {
            log_message('error', 'No Belum Lunas Meity record found for id_tipe: ' . $id_tipe);
            return false;
        }
    
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
    
        // Determine status (using the original method)
        $updateData['status'] = $this->calculateStatus($total_meity, $updateData['terkumpul'], $latestMeityRecord['status'] ?? null);
    
        // Update the meity record
        return $this->meityModel->update($latestMeityRecord['id_meity'], $updateData);
    }

    
    // Helper method to calculate status
    private function calculateStatus($total_meity, $terkumpul, $currentStatus = null)
    {
        // Debug logging
        log_message('debug', 'Status Calculation: ' . json_encode([
            'total_meity' => $total_meity,
            'terkumpul' => $terkumpul,
            'current_status' => $currentStatus
        ]));
    
        if ($terkumpul == 0) {
            return 0; // Belum Lunas
        } elseif ($terkumpul >= $total_meity) {
            // If current status is already confirmed (2), keep it confirmed
            if ($currentStatus === 2) {
                return 2; // Lunas
            }
            
            // New logic: If not yet confirmed, set to waiting confirmation
            return 1; // Menunggu Konfirmasi
        } elseif ($terkumpul > 0 && $terkumpul < $total_meity) {
            return 0; // Belum Lunas (partial payment)
        }
        
        return 0; // Default
    }

    // public function updateLatestTerkumpul($id_pembelian, $totalHargaModal, $id_tipe, $paymentMethod = null)
    // {
    //     // Comprehensive logging
    //     log_message('debug', 'updateLatestTerkumpul Input: ' . json_encode([
    //         'id_pembelian' => $id_pembelian,
    //         'totalHargaModal' => $totalHargaModal,
    //         'id_tipe' => $id_tipe,
    //         'payment_method' => $paymentMethod
    //     ]));
    
    //     // Find ALL unpaid records for this type, ordered chronologically
    //     $unpaidMeityRecords = $this->meityModel
    //         ->where('id_tipe', $id_tipe)
    //         ->whereIn('status', [0]) // Only status 0 (Belum Lunas)
    //         ->orderBy('id_meity', 'ASC')
    //         ->findAll();
    
    //     // If no unpaid records exist, log and return false
    //     if (empty($unpaidMeityRecords)) {
    //         log_message('error', 'No unpaid Meity records found for id_tipe: ' . $id_tipe);
    //         return false;
    //     }
    
    //     // Get the total_meity from the corresponding pembelian
    //     $pembelianModel = new PembelianModel();
    //     $pembelian = $pembelianModel->find($id_pembelian);
    
    //     if (!$pembelian) {
    //         log_message('error', 'No Pembelian record found for id_pembelian: ' . $id_pembelian);
    //         return false;
    //     }
    
    //     $total_meity = $pembelian['total_meity'];
    
    //     // Process each unpaid record
    //     foreach ($unpaidMeityRecords as $latestMeityRecord) {
    //         // Calculate current terkumpul
    //         $current_terkumpul = $latestMeityRecord['terkumpul'] ?? 0;
    //         $new_terkumpul = $current_terkumpul + $totalHargaModal;
    
    //         // Prepare update data
    //         $updateData = [
    //             'terkumpul' => $new_terkumpul
    //         ];
    
    //         // Handle different payment methods
    //         switch ($paymentMethod) {
    //             case 1: // Piutang
    //                 $updateData['current_piutang'] = ($latestMeityRecord['current_piutang'] ?? 0) + $totalHargaModal;
    //                 break;
    //             case 2: // Transfer
    //                 $updateData['current_transfer'] = ($latestMeityRecord['current_transfer'] ?? 0) + $totalHargaModal;
    //                 break;
    //         }
    
    //         // If new_terkumpul exceeds total_meity, handle excess
    //         if ($new_terkumpul > $total_meity) {
    //             $sisaTerkumpulModel = new SisaTerkumpulModel();
    //             $sisa = $new_terkumpul - $total_meity;
                
    //             $sisaData = [
    //                 'id_meity' => $latestMeityRecord['id_meity'], 
    //                 'id_tipe' => $id_tipe,
    //                 'sisa_terkumpul' => $sisa,
    //                 'tgl_sisa_terkumpul' => date('Y-m-d')
    //             ];
    
    //             try {
    //                 $sisaTerkumpulModel->insert($sisaData);
    //             } catch (\Exception $e) {
    //                 log_message('error', 'Failed to insert Sisa Terkumpul: ' . $e->getMessage());
    //             }
    
    //             // Set terkumpul to exactly match total_meity
    //             $updateData['terkumpul'] = $total_meity;
    //         }
    
    //         // Determine status with enhanced logic
    //         $updateData['status'] = $this->calculateStatus($total_meity, $updateData['terkumpul'], $latestMeityRecord['status']);
    
    //         // Detailed logging before update
    //         log_message('debug', 'Meity Record Update Details', [
    //             'id_meity' => $latestMeityRecord['id_meity'],
    //             'update_data' => $updateData,
    //             'current_status' => $latestMeityRecord['status']
    //         ]);
    
    //         // Update the meity record
    //         $this->meityModel->update($latestMeityRecord['id_meity'], $updateData);
    
    //         // Return after first successful update
    //         return true;
    //     }
    
    //     // If no update could be performed
    //     log_message('error', 'Could not update any Meity records for id_tipe: ' . $id_tipe);
    //     return false;
    // }
    
    // // Enhanced status calculation method
    // private function calculateStatus($total_meity, $terkumpul, $currentStatus = null)
    // {
    //     // Comprehensive debug logging
    //     log_message('debug', 'Status Calculation Inputs', [
    //         'total_meity' => $total_meity,
    //         'terkumpul' => $terkumpul,
    //         'current_status' => $currentStatus
    //     ]);
    
    //     // If current status is already 2 (Lunas), keep it 2
    //     if ($currentStatus === 2) {
    //         return 2;
    //     }
    
    //     // If terkumpul is zero, always return 0
    //     if ($terkumpul == 0) {
    //         return 0; // Belum Lunas
    //     }
    
    //     // If terkumpul meets or exceeds total_meity
    //     if ($terkumpul >= $total_meity) {
    //         // If not already confirmed, set to waiting confirmation
    //         return $currentStatus === 1 ? 1 : 1; // Menunggu Konfirmasi
    //     }
    
    //     // Partial payment scenario
    //     if ($terkumpul > 0 && $terkumpul < $total_meity) {
    //         return 0; // Belum Lunas
    //     }
    
    //     // Default fallback
    //     return 0;
    // }
    
    // public function updateLatestTerkumpul($id_pembelian, $totalHargaModal, $id_tipe, $paymentMethod = null)
    // {
    //     // Log input parameters for debugging
    //     log_message('debug', 'updateLatestTerkumpul Input: ' . json_encode([
    //         'id_pembelian' => $id_pembelian,
    //         'totalHargaModal' => $totalHargaModal,
    //         'id_tipe' => $id_tipe,
    //         'payment_method' => $paymentMethod
    //     ]));
    
    //     // Find the latest meity record for this type, only with status 0 (Belum Lunas)
    //     $latestMeityRecord = $this->meityModel
    //         ->where('id_tipe', $id_tipe)
    //         ->where('status', 0) // Only select records that are Belum Lunas
    //         ->orderBy('id_meity', 'ASC')
    //         ->first();
    
    //     // If no incomplete record exists, return false
    //     if (!$latestMeityRecord) {
    //         log_message('error', 'No Belum Lunas Meity record found for id_tipe: ' . $id_tipe);
    //         return false;
    //     }
    
    //     // Get the total_meity from the corresponding pembelian
    //     $pembelianModel = new PembelianModel();
    //     $pembelian = $pembelianModel->find($id_pembelian);
    
    //     if (!$pembelian) {
    //         log_message('error', 'No Pembelian record found for id_pembelian: ' . $id_pembelian);
    //         return false;
    //     }
    
    //     $total_meity = $pembelian['total_meity'];
    
    //     // Calculate current terkumpul
    //     $current_terkumpul = $latestMeityRecord['terkumpul'] ?? 0;
    //     $new_terkumpul = $current_terkumpul + $totalHargaModal;
    
    //     // Prepare update data
    //     $updateData = [
    //         'terkumpul' => $new_terkumpul
    //     ];
    
    //     // Handle different payment methods
    //     switch ($paymentMethod) {
    //         case 1: // Piutang
    //             $updateData['current_piutang'] = ($latestMeityRecord['current_piutang'] ?? 0) + $totalHargaModal;
    //             break;
    //         case 2: // Transfer
    //             $updateData['current_transfer'] = ($latestMeityRecord['current_transfer'] ?? 0) + $totalHargaModal;
    //             break;
    //         default: // Cash or other methods
    //             // No additional handling needed as terkumpul is already updated
    //             break;
    //     }
    
    //     // If new_terkumpul exceeds total_meity, handle excess
    //     if ($new_terkumpul > $total_meity) {
    //         $sisaTerkumpulModel = new SisaTerkumpulModel();
    //         $sisa = $new_terkumpul - $total_meity;
            
    //         $sisaData = [
    //             'id_meity' => $latestMeityRecord['id_meity'], 
    //             'id_tipe' => $id_tipe,
    //             'sisa_terkumpul' => $sisa,
    //             'tgl_sisa_terkumpul' => date('Y-m-d')
    //         ];
    
    //         try {
    //             $sisaTerkumpulModel->insert($sisaData);
    //         } catch (\Exception $e) {
    //             log_message('error', 'Failed to insert Sisa Terkumpul: ' . $e->getMessage());
    //         }
    
    //         // Set terkumpul to exactly match total_meity
    //         $updateData['terkumpul'] = $total_meity;
    //     }
    
    //     // Determine status (using the original method)
    //     $updateData['status'] = $this->calculateStatus($total_meity, $updateData['terkumpul'], $latestMeityRecord['status'] ?? null);
    
    //     // Update the meity record
    //     return $this->meityModel->update($latestMeityRecord['id_meity'], $updateData);
    // }

    
    // Helper method to calculate status
    // private function calculateStatus($total_meity, $terkumpul, $currentStatus = null)
    // {
    //     // Debug logging
    //     log_message('debug', 'Status Calculation: ' . json_encode([
    //         'total_meity' => $total_meity,
    //         'terkumpul' => $terkumpul,
    //         'current_status' => $currentStatus
    //     ]));
    
    //     if ($terkumpul == 0) {
    //         return 0; // Belum Lunas
    //     } elseif ($terkumpul >= $total_meity) {
    //         // If current status is already confirmed (2), keep it confirmed
    //         if ($currentStatus === 2) {
    //             return 2; // Lunas
    //         }
            
    //         // New logic: If not yet confirmed, set to waiting confirmation
    //         return 1; // Menunggu Konfirmasi
    //     } elseif ($terkumpul > 0 && $terkumpul < $total_meity) {
    //         return 0; // Belum Lunas (partial payment)
    //     }
        
    //     return 0; // Default
    // }

}

