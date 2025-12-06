<?php
namespace App\Services;

use App\Models\{PenjualanModel,BulekModel, DetailPenjualanModel,DetailPembelianModel,MeityModel,PembelianModel,SisaTerkumpulModel};
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
    protected $bulekModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->detailPembelianModel = new DetailPembelianModel();
        $this->meityModel = new MeityModel();
        $this->paymentService = new PaymentService();
        $this->pembelianModel = new PembelianModel();
        $this->sisaTerkumpulModel = new SisaTerkumpulModel();
        $this->bulekModel = new BulekModel();
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
        // Existing method implementation remains the same
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
                
                // Create bulek record if not exists
                $this->insertBulekPenjualan($existingRecord['id_penjualan'], $tgl_penjualan, $id_tipe, $existingRecord['total_untung']);
                
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
            
            // Create bulek record if not exists
            $this->insertBulekPenjualan($existingRecord['id_penjualan'], $tgl_penjualan, $id_tipe, $updateData['total_untung']);
            
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
        
        // Create bulek record if not exists
        $this->insertBulekPenjualan($newId, $tgl_penjualan, $id_tipe, $insertData['total_untung']);
        
        return $newId;
    }
    
    public function insertBulekPenjualan($id_penjualan, $tgl_penjualan, $id_tipe, $total_untung = 0)
    {
        // Check if a record already exists
        $existingRecord = $this->bulekModel
            ->where('id_penjualan', $id_penjualan)
            ->where('tgl_setor', $tgl_penjualan)
            ->where('id_tipe', $id_tipe)
            ->first();
        
        // If record exists, update the total_setor
        if ($existingRecord) {
            $updateData = [
                'total_setor' => $total_untung
            ];
    
            $this->bulekModel
                ->where('id_bulek', $existingRecord['id_bulek'])
                ->set($updateData)
                ->update();
            
            log_message('info', "Updated existing bulek record with id_bulek: {$existingRecord['id_bulek']}, new total_setor: $total_untung");
            
            return $existingRecord['id_bulek'];
        }
        
        // Prepare insert data for new record
        $insertData = [
            'id_penjualan' => $id_penjualan,
            'id_tipe' => $id_tipe,
            'tgl_setor' => $tgl_penjualan,
            'jumlah_setor' => 0,
            'total_setor' => $total_untung, // Set total_setor to total_untung
            'keterangan' => 'Pendapatan Penjualan'
        ];
        
        // Insert new record
        $newId = $this->bulekModel->insert($insertData);
        
        log_message('info', "Created new bulek record with id_penjualan: $id_penjualan, id_tipe: $id_tipe, total_setor: $total_untung");
        
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
            ->where('id_pembelian', $id_pembelian)
            ->where('status', 0)
            ->orderBy('id_meity', 'ASC')
            ->first();
    
        if (!$latestMeityRecord) {
            log_message('error', 'No Belum Lunas Meity record found for id_tipe: ' . $id_tipe);
            return false;
        }
    
        // Get the total_meity from the corresponding pembelian
        $pembelian = $this->pembelianModel->find($id_pembelian);
    
        if (!$pembelian) {
            log_message('error', 'No Pembelian record found for id_pembelian: ' . $id_pembelian);
            return false;
        }
    
        $total_meity = $pembelian['total_meity'];
        $current_terkumpul = $latestMeityRecord['terkumpul'] ?? 0;
        $new_terkumpul = $current_terkumpul + $totalHargaModal;
    
        // Prepare update data for current meity
        $updateData = [
            'terkumpul' => $new_terkumpul
        ];
    
        // Handle different payment methods for current meity
        switch ($paymentMethod) {
            case 1: // Piutang
                $updateData['current_piutang'] = ($latestMeityRecord['current_piutang'] ?? 0) + $totalHargaModal;
                break;
            case 2: // Transfer
                $updateData['current_transfer'] = ($latestMeityRecord['current_transfer'] ?? 0) + $totalHargaModal;
                break;
        }
    
        // If new_terkumpul exceeds total_meity, handle excess by splitting
        if ($new_terkumpul > $total_meity) {
            $excess = $new_terkumpul - $total_meity;
            
            // Get the latest detail penjualan record
            $latestDetailPenjualan = $this->detailPenjualanModel
                ->where('id_pembelian', $id_pembelian)
                ->orderBy('id_detail_penjualan', 'DESC')
                ->first();
            
            if ($latestDetailPenjualan) {
                // Calculate the proportion for splitting
                $totalAmount = $latestDetailPenjualan['jumlah_keluar'];
                $mainAmount = floor(($total_meity - $current_terkumpul) / ($totalHargaModal / $totalAmount));
                $excessAmount = $totalAmount - $mainAmount;
                
                // Calculate the modal value for excess amount
                $modalPerUnit = $totalHargaModal / $totalAmount;
                $excessModal = $modalPerUnit * $excessAmount;
                
                // Update the original detail penjualan with reduced amount
                $this->detailPenjualanModel->update($latestDetailPenjualan['id_detail_penjualan'], [
                    'jumlah_keluar' => $mainAmount
                ]);
                
                try {
                    // Get next available pembelian
                    $nextPembelianId = $this->getNextAvailablePembelian($id_tipe, $id_pembelian);
                    
                    // Create new detail penjualan for excess amount
                    $excessDetailData = [
                        'id_customer' => $latestDetailPenjualan['id_customer'],
                        'id_penjualan' => $latestDetailPenjualan['id_penjualan'],
                        'jumlah_keluar' => $excessAmount,
                        'harga_jual' => $latestDetailPenjualan['harga_jual'],
                        'id_pembelian' => $nextPembelianId,
                        'id_tipe' => $id_tipe,
                        'id_unit' => $latestDetailPenjualan['id_unit'],
                        'id_payment' => $latestDetailPenjualan['id_payment'],
                        'status' => $latestDetailPenjualan['status']
                    ];
                    
                    $this->detailPenjualanModel->insert($excessDetailData);
                    
                    // Handle the next meity record
                    $nextMeityRecord = $this->meityModel
                        ->where('id_pembelian', $nextPembelianId)
                        ->where('id_tipe', $id_tipe)
                        ->first();
                        
                    if (!$nextMeityRecord) {
                        throw new Exception('No Belum Lunas Meity record found for next pembelian.');
                    } else {
                        // Update existing next meity record
                        $nextMeityUpdate = [
                            'terkumpul' => ($nextMeityRecord['terkumpul'] ?? 0) + $excessModal
                        ];
                        
                        switch ($paymentMethod) {
                            case 1: // Piutang
                                $nextMeityUpdate['current_piutang'] = ($nextMeityRecord['current_piutang'] ?? 0) + $excessModal;
                                break;
                            case 2: // Transfer
                                $nextMeityUpdate['current_transfer'] = ($nextMeityRecord['current_transfer'] ?? 0) + $excessModal;
                                break;
                        }
                        
                        $this->meityModel->update($nextMeityRecord['id_meity'], $nextMeityUpdate);
                    }
                    
                    // Log successful split
                    log_message('info', "Split operation successful. Main amount: $mainAmount, " .
                              "Excess amount: $excessAmount, Excess Modal: $excessModal, " .
                              "Next Pembelian ID: $nextPembelianId");
                              
                } catch (Exception $e) {
                    log_message('error', 'Failed to process split operation: ' . $e->getMessage());
                    throw $e;
                }
            }
            
            // Set current meity terkumpul to exactly match total_meity
            $updateData['terkumpul'] = $total_meity;
        }
    
        // Update status for current meity
        $updateData['status'] = $this->calculateStatus($total_meity, $updateData['terkumpul'], $latestMeityRecord['status'] ?? null);
    
        // Update the current meity record
        return $this->meityModel->update($latestMeityRecord['id_meity'], $updateData);
    }

    private function getNextAvailablePembelian($id_tipe, $current_pembelian_id)
    {
        $nextPembelian = $this->detailPembelianModel
            ->select('tbl_detail_pembelian.*, tp.tgl_masuk, tp.id_supplier')
            ->join('tbl_pembelian tp', 'tbl_detail_pembelian.id_pembelian = tp.id_pembelian')
            ->join('tbl_meity tm', 'tp.id_pembelian = tm.id_pembelian', 'left')
            ->where('tbl_detail_pembelian.id_tipe', $id_tipe)
            ->where('tm.status', 0)
            ->where('tbl_detail_pembelian.id_pembelian >', $current_pembelian_id)
            ->orderBy('tbl_detail_pembelian.id_detail_pembelian', 'ASC')
            ->first();

        if (!$nextPembelian) {
            throw new Exception('No available next pembelian found for excess amount.');
        }

        return $nextPembelian['id_pembelian'];
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



}

