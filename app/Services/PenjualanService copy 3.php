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
                'total_barang_keluar' => $jumlah_keluar
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
        // Initial checks and calculations remain the same
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
    
        $pembelian = $this->pembelianModel->find($id_pembelian);
        if (!$pembelian) {
            log_message('error', 'No Pembelian record found for id_pembelian: ' . $id_pembelian);
            return false;
        }
    
        $total_meity = $pembelian['total_meity'];
        $current_terkumpul = $latestMeityRecord['terkumpul'] ?? 0;
        $new_terkumpul = $current_terkumpul + $totalHargaModal;
    
        $updateData = [
            'terkumpul' => $new_terkumpul
        ];
    
        switch ($paymentMethod) {
            case 1:
                $updateData['current_piutang'] = ($latestMeityRecord['current_piutang'] ?? 0) + $totalHargaModal;
                break;
            case 2:
                $updateData['current_transfer'] = ($latestMeityRecord['current_transfer'] ?? 0) + $totalHargaModal;
                break;
        }
    
        if ($new_terkumpul > $total_meity) {
            $excess = $new_terkumpul - $total_meity;
    
            $latestDetailPenjualan = $this->detailPenjualanModel
                ->where('id_pembelian', $id_pembelian)
                ->orderBy('id_detail_penjualan', 'DESC')
                ->first();

            
    
            if ($latestDetailPenjualan) {
                $totalAmount = $latestDetailPenjualan['jumlah_keluar'];
                $mainAmount = floor(($total_meity - $current_terkumpul) / ($totalHargaModal / $totalAmount));
                $excessAmount = $totalAmount - $mainAmount;
    
                // Update original detail penjualan
                $this->detailPenjualanModel->update($latestDetailPenjualan['id_detail_penjualan'], [
                    'jumlah_keluar' => $mainAmount
                ]);
    
                try {
                    // Get next pembelian ID using the original method
                    $nextPembelianId = $this->getNextAvailablePembelian($id_tipe, $id_pembelian);
    
                    // Get the modal price from detail pembelian
                    $nextDetailPembelian = $this->detailPembelianModel
                        ->where('id_pembelian', $nextPembelianId)
                        ->where('id_tipe', $id_tipe)
                        ->first();
    
                    if (!$nextDetailPembelian) {
                        throw new Exception('Could not find detail pembelian record for next pembelian.');
                    }
    
                    // Calculate new modal value for excess using next pembelian's price
                    $newExcessModal = $excessAmount * $nextDetailPembelian['harga_modal_barang'];
    
                    // Create new payment record for the excess amount
                    $excessPaymentAmount = $excessAmount * $latestDetailPenjualan['harga_jual'];
                    
                    $new_payment_id = $this->paymentService->insertPayment(
                        $excessPaymentAmount,
                        $paymentMethod,
                        $latestDetailPenjualan['id_customer'],
                        $latestDetailPenjualan['id_penjualan']
                    );
    
                    // Create new detail penjualan for excess with new payment ID
                    $excessDetailData = [
                        'id_customer' => $latestDetailPenjualan['id_customer'],
                        'id_penjualan' => $latestDetailPenjualan['id_penjualan'],
                        'jumlah_keluar' => $excessAmount,
                        'harga_jual' => $latestDetailPenjualan['harga_jual'],
                        'id_pembelian' => $nextPembelianId,
                        'id_tipe' => $id_tipe,
                        'id_unit' => $latestDetailPenjualan['id_unit'],
                        'id_payment' => $new_payment_id, // Use the new payment ID
                        'status' => $latestDetailPenjualan['status']
                    ];
    
                    $this->detailPenjualanModel->insert($excessDetailData);

                    $this->recalculatePenjualanTotals(
                        $latestDetailPenjualan['id_penjualan'], 
                        $id_tipe
                    );
    
                    // Handle next meity record
                    $nextMeityRecord = $this->meityModel
                        ->where('id_pembelian', $nextPembelianId)
                        ->where('id_tipe', $id_tipe)
                        ->first();
    
                    if (!$nextMeityRecord) {
                        throw new Exception('No Belum Lunas Meity record found for next pembelian.');
                    }
    
                    $nextMeityUpdate = [
                        'terkumpul' => ($nextMeityRecord['terkumpul'] ?? 0) + $newExcessModal
                    ];
    
                    switch ($paymentMethod) {
                        case 1:
                            $nextMeityUpdate['current_piutang'] = ($nextMeityRecord['current_piutang'] ?? 0) + $newExcessModal;
                            break;
                        case 2:
                            $nextMeityUpdate['current_transfer'] = ($nextMeityRecord['current_transfer'] ?? 0) + $newExcessModal;
                            break;
                    }
    
                    // Calculate status for the next meity record
                    $nextMeityUpdate['status'] = $this->calculateStatus(
                        $nextMeityRecord['total_meity'] ?? $total_meity,
                        $nextMeityUpdate['terkumpul'],
                        $nextMeityRecord['status'] ?? null
                    );
    
                    $this->meityModel->update($nextMeityRecord['id_meity'], $nextMeityUpdate);
    
                } catch (Exception $e) {
                    log_message('error', 'Failed to process split operation: ' . $e->getMessage());
                    throw $e;
                }
    
                $updateData['terkumpul'] = $total_meity;
            }
        }
    
        // Calculate status for the current meity record
        $updateData['status'] = $this->calculateStatus($total_meity, $updateData['terkumpul'], $latestMeityRecord['status'] ?? null);
        return $this->meityModel->update($latestMeityRecord['id_meity'], $updateData);
    }
    
    // Keep using the original working method
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

    public function recalculatePenjualanTotals($id_penjualan, $id_tipe)
    {
        // Fetch all detail penjualan records for this penjualan and type
        $detailRecords = $this->detailPenjualanModel
            ->where('id_penjualan', $id_penjualan)
            ->where('id_tipe', $id_tipe)
            ->findAll();
    
        if (empty($detailRecords)) {
            log_message('error', "No detail penjualan records found for id_penjualan: $id_penjualan and id_tipe: $id_tipe");
            return false;
        }
    
        // Initialize totals
        $totalHargaJual = 0;
        $totalHargaModal = 0;
        $totalUntung = 0;
        $totalBarangKeluar = 0;
    
        foreach ($detailRecords as $record) {
            // Calculate totals for each detail record
            $hargaJual = $record['harga_jual'];
            $jumlahKeluar = $record['jumlah_keluar'];
            
            $recordTotalHargaJual = $hargaJual * $jumlahKeluar;
            
            // Get the corresponding detail pembelian to get modal price
            $detailPembelian = $this->detailPembelianModel
                ->where('id_pembelian', $record['id_pembelian'])
                ->where('id_tipe', $id_tipe)
                ->first();
    
            if (!$detailPembelian) {
                log_message('error', "No detail pembelian found for id_pembelian: {$record['id_pembelian']} and id_tipe: $id_tipe");
                continue;
            }
    
            // Use the actual modal price from the specific pembelian record
            $hargaModal = $detailPembelian['harga_modal_barang'];
            $recordTotalHargaModal = $hargaModal * $jumlahKeluar;
            $recordTotalUntung = $recordTotalHargaJual - $recordTotalHargaModal;
    
            // Accumulate totals
            $totalHargaJual += $recordTotalHargaJual;
            $totalHargaModal += $recordTotalHargaModal;
            $totalUntung += $recordTotalUntung;
            $totalBarangKeluar += $jumlahKeluar;
        }
    
        // Update the penjualan record with recalculated totals
        $updateData = [
            'total_harga_jual' => $totalHargaJual,
            'total_harga_modal' => $totalHargaModal,
            'total_untung' => $totalUntung,
            'total_barang_keluar' => $totalBarangKeluar
        ];
    
        $result = $this->penjualanModel
            ->where('id_penjualan', $id_penjualan)
            ->set($updateData)
            ->update();
    
        log_message('info', "Recalculated totals for id_penjualan: $id_penjualan - " . json_encode($updateData));
    
        return $result;
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

