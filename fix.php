<?php

// on pejualan Controller


// Reduce stock 
                    $stockInput = [ 
                        'id_tipe' => $input['id_tipe'], 
                        'barang_masuk' => 0, 
                        'barang_keluar' => $input['jumlah_keluar'] 
                    ]; 
    
                    $this->stockService->updateOrCreateStock($stockInput); 
    


// On Penjualan Service


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

// On Stock Service


// On Pembelian Controller

private function createStock($input) 
{
    try {
        $stockService = new \App\Services\StockService();
        
        // Prepare input for stock service
        $stockInput = [
            'id_tipe' => $input['id_tipe'],
            'barang_masuk' => $input['barang_masuk'],
            'barang_keluar' => 0  // No barang keluar during purchase
        ];
        
        // Use the stock service to update or create stock
        $stockService->updateOrCreateStock($stockInput);
    } catch (\Exception $e) {
        log_message('error', 'Failed to create stock: ' . $e->getMessage());
        throw $e;
    }
}