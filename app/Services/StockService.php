<?php
namespace App\Services;

use App\Models\{PenjualanModel, DetailPenjualanModel,DetailPembelianModel,BarangModel,StockBarangModel, UnitBarangModel};
use exception;

class StockService
{
    protected $penjualanModel;
    protected $detailPenjualanModel;
    protected $detailPembelianModel;
    protected $barangModel;
    protected $unitBarangModel;
    protected $stockBarangModel;
    protected $db;


    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->detailPembelianModel = new DetailPembelianModel();
        $this->barangModel = new BarangModel();
        $this->unitBarangModel = new UnitBarangModel();
        $this->stockBarangModel = new StockBarangModel();
        $this->db = \Config\Database::connect();

    }

   /**
     * Validate stock availability
     * 
     * @param int $idTipe Product type ID
     * @param float $requestedQuantity Quantity to be sold
     * @return bool
     * @throws Exception
     */
    public function validateStock($idTipe, $requestedQuantity)
    {
        $query = $this->db->table('tbl_stock')
            ->select('stock_barang')
            ->where('id_tipe', $idTipe)
            ->orderBy('tgl_stock', 'DESC')
            ->limit(1);
    
        $result = $query->get();
    
        if ($result === false) {
            log_message('error', 'Stock query failed for ID Tipe: ' . $idTipe);
            throw new Exception("Database query error for stock validation");
        }
    
        if ($result->getNumRows() === 0) {
            log_message('error', 'No stock record found for ID Tipe: ' . $idTipe);
            throw new Exception("No stock record found for the product");
        }
    
        $stockRow = $result->getRow();
        $availableStock = floatval($stockRow->stock_barang ?? 0);
    
        if ($requestedQuantity > $availableStock) {
            throw new Exception(sprintf(
                "Stok tidak cukup. Tersedia: %.2f, Diminta: %.2f", 
                $availableStock, 
                $requestedQuantity
            ));
        }
    
        return true;
    }

    /**
     * Get detailed stock information
     * 
     * @param int $idTipe Product type ID
     * @param float|null $requestedQuantity Quantity to be sold (optional)
     * @return array
     */
    public function getStockInfo($idTipe, $requestedQuantity = null)
    {
        $stockQuery = $this->db->table('view_stock_with_sisa_per_unit')
            ->select('sisa_stok, total_stock, total_pembelian, total_penjualan, tbl_tipe_barang.jenis_barang')
            ->join('tbl_tipe_barang', 'view_stock_with_sisa_per_unit.id_tipe = tbl_tipe_barang.id_tipe')
            ->where('view_stock_with_sisa_per_unit.id_tipe', $idTipe)
            ->get()
            ->getRowArray();

        if (!$stockQuery) {
            return [
                'available_stock' => 0,
                'product_name' => 'Unknown',
                'can_sell' => false
            ];
        }

        $stockInfo = [
            'available_stock' => $stockQuery['sisa_stok'] ?? 0,
            'total_stock' => $stockQuery['total_stock'] ?? 0,
            'total_pembelian' => $stockQuery['total_pembelian'] ?? 0,
            'total_penjualan' => $stockQuery['total_penjualan'] ?? 0,
            'product_name' => $stockQuery['jenis_barang'] ?? 'Unknown'
        ];

        // Add sell validation if requested quantity is provided
        if ($requestedQuantity !== null) {
            $stockInfo['requested_quantity'] = $requestedQuantity;
            $stockInfo['can_sell'] = $requestedQuantity <= $stockInfo['available_stock'];
        }

        return $stockInfo;
    }

    public function updateOrCreateStock($input)
    {
        // Validate input
        if (!isset($input['id_tipe'])) {
            throw new \Exception('Missing id_tipe for stock creation.');
        }
    
        // Calculate stock (keep original calculation logic)
        $stockData = $this->calculateStock($input);
    
        try {
            // Check if a stock record for this type and date already exists
            $existingStock = $this->stockBarangModel
                ->where('id_tipe', $input['id_tipe'])
                ->where('tgl_stock', date('Y-m-d'))
                ->first();
    
            if ($existingStock) {
                // Update existing stock record
                $this->stockBarangModel->update($existingStock['id_stock'], [
                    'stock_barang' => $stockData['current_stock'],
                    'barang_masuk' => $existingStock['barang_masuk'] + $stockData['barang_masuk'],
                    'barang_keluar' => $existingStock['barang_keluar'] + $stockData['barang_keluar']
                ]);
    
                return $existingStock['id_stock'];
            } else {
                // Insert new stock record
                return $this->stockBarangModel->insert([
                    'id_tipe' => $input['id_tipe'],
                    'tgl_stock' => date('Y-m-d'),
                    'stock_barang' => $stockData['current_stock'],
                    'barang_masuk' => $stockData['barang_masuk'],
                    'barang_keluar' => $stockData['barang_keluar']
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to create/update stock: ' . $e->getMessage());
            throw $e;
        }
    }
    
    // Modify the calculateStock method to handle restoration
    public function calculateStock($input)
    {
        // Check if input has required fields
        if (!isset($input['id_tipe']) || !isset($input['barang_masuk']) || !isset($input['barang_keluar'])) {
            throw new \Exception('Missing required input for stock calculation');
        }
    
        // Retrieve the latest stock for this product type
        $latestStock = $this->stockBarangModel
            ->where('id_tipe', $input['id_tipe'])
            ->orderBy('tgl_stock', 'DESC')
            ->first();
    
        // Calculate current stock
        $currentStock = $latestStock 
            ? $latestStock['stock_barang'] + $input['barang_masuk'] - $input['barang_keluar']
            : $input['barang_masuk'] - $input['barang_keluar'];
    
        return [
            'id_tipe' => $input['id_tipe'],
            'current_stock' => $currentStock,
            'barang_masuk' => $input['barang_masuk'],
            'barang_keluar' => $input['barang_keluar']
        ];
    }

    public function updateStockWithDifference($input)
    {
        // Validate input
        if (!isset($input['id_tipe']) || !isset($input['barang_masuk_diff'])) {
            throw new \Exception('Missing required input for stock update');
        }

        // Get the latest stock for this type
        $latestStock = $this->stockBarangModel
            ->where('id_tipe', $input['id_tipe'])
            ->orderBy('tgl_stock', 'DESC')
            ->first();

        if ($latestStock) {
            // Update existing stock record
            $newStock = $latestStock['stock_barang'] + $input['barang_masuk_diff'];
            
            $this->stockBarangModel->update($latestStock['id_stock'], [
                'stock_barang' => $newStock,
                'barang_masuk' => $latestStock['barang_masuk'] + $input['barang_masuk_diff']
            ]);
        } else {
            // If no previous stock exists, create a new record
            $this->stockBarangModel->insert([
                'id_tipe' => $input['id_tipe'],
                'tgl_stock' => date('Y-m-d'),
                'stock_barang' => $input['barang_masuk_diff'],
                'barang_masuk' => $input['barang_masuk_diff'],
                'barang_keluar' => 0
            ]);
        }
    }

    // public function updateStockWithSpecificDate($input)
    // {
    //     // Validate input
    //     if (!isset($input['id_tipe']) || !isset($input['tgl_stock'])) {
    //         throw new \Exception('Missing id_tipe or tgl_stock for stock creation.');
    //     }

    //     try {
    //         // Check if a stock record for this type and date already exists
    //         $existingStock = $this->stockBarangModel
    //             ->where('id_tipe', $input['id_tipe'])
    //             ->where('tgl_stock', $input['tgl_stock'])
    //             ->first();

    //         // Get the previous day's closing stock
    //         $previousStock = $this->stockBarangModel
    //             ->where('id_tipe', $input['id_tipe'])
    //             ->where('tgl_stock <', $input['tgl_stock'])
    //             ->orderBy('tgl_stock', 'DESC')
    //             ->first();

    //         $previousStockAmount = $previousStock ? $previousStock['stock_barang'] : 0;

    //         if ($existingStock) {
    //             // Update existing stock record
    //             $newStockBarang = $previousStockAmount + $input['barang_masuk'] - ($input['barang_keluar'] ?? 0);
                
    //             $this->stockBarangModel->update($existingStock['id_stock'], [
    //                 'stock_barang' => $newStockBarang,
    //                 'barang_masuk' => $existingStock['barang_masuk'] + $input['barang_masuk'],
    //                 'barang_keluar' => $existingStock['barang_keluar'] + ($input['barang_keluar'] ?? 0)
    //             ]);
    //         } else {
    //             // Insert new stock record
    //             $newStockBarang = $previousStockAmount + $input['barang_masuk'] - ($input['barang_keluar'] ?? 0);
                
    //             $this->stockBarangModel->insert([
    //                 'id_tipe' => $input['id_tipe'],
    //                 'tgl_stock' => $input['tgl_stock'],
    //                 'stock_barang' => $newStockBarang,
    //                 'barang_masuk' => $input['barang_masuk'],
    //                 'barang_keluar' => $input['barang_keluar'] ?? 0
    //             ]);
    //         }

    //         // Recalculate subsequent dates
    //         $this->recalculateSubsequentStock($input['id_tipe'], $input['tgl_stock']);

    //     } catch (\Exception $e) {
    //         log_message('error', 'Failed to create/update stock with specific date: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }


    //     public function updateStockWithSpecificDate($input)
    // {
    //     // Validate input
    //     if (!isset($input['id_tipe']) || !isset($input['tgl_stock'])) {
    //         throw new \Exception('Missing id_tipe or tgl_stock for stock creation.');
    //     }

    //     try {
    //         // Find existing stock records for this type and date
    //         $existingStocks = $this->stockBarangModel
    //             ->where('id_tipe', $input['id_tipe'])
    //             ->where('tgl_stock', $input['tgl_stock'])
    //             ->findAll();

    //         // Get the previous day's closing stock
    //         $previousStock = $this->stockBarangModel
    //             ->where('id_tipe', $input['id_tipe'])
    //             ->where('tgl_stock <', $input['tgl_stock'])
    //             ->orderBy('tgl_stock', 'DESC')
    //             ->first();

    //         $previousStockAmount = $previousStock ? $previousStock['stock_barang'] : 0;

    //         // Calculate total barang_masuk if multiple entries exist
    //         $totalBarangMasuk = $input['barang_masuk'];
    //         $existingBarangMasuk = 0;
    //         $existingBarangKeluar = 0;

    //         if (!empty($existingStocks)) {
    //             // Aggregate existing entries
    //             foreach ($existingStocks as $existingStock) {
    //                 $existingBarangMasuk += $existingStock['barang_masuk'];
    //                 $existingBarangKeluar += $existingStock['barang_keluar'];
    //             }
                
    //             // Remove existing entries to consolidate
    //             foreach ($existingStocks as $existingStock) {
    //                 $this->stockBarangModel->delete($existingStock['id_stock']);
    //             }
    //         }

    //         // Combine existing and new barang_masuk
    //         $totalBarangMasuk += $existingBarangMasuk;
    //         $totalBarangKeluar = $existingBarangKeluar + ($input['barang_keluar'] ?? 0);

    //         // Calculate new stock amount
    //         $newStockBarang = $previousStockAmount + $totalBarangMasuk - $totalBarangKeluar;

    //         // Insert consolidated stock record
    //         $this->stockBarangModel->insert([
    //             'id_tipe' => $input['id_tipe'],
    //             'tgl_stock' => $input['tgl_stock'],
    //             'stock_barang' => $newStockBarang,
    //             'barang_masuk' => $totalBarangMasuk,
    //             'barang_keluar' => $totalBarangKeluar
    //         ]);

    //         // Recalculate subsequent dates
    //         $this->recalculateSubsequentStock($input['id_tipe'], $input['tgl_stock']);

    //         // Logging
    //         log_message('info', 'Consolidated Stock Update: ' . json_encode([
    //             'id_tipe' => $input['id_tipe'],
    //             'tgl_stock' => $input['tgl_stock'],
    //             'previous_stock' => $previousStockAmount,
    //             'total_barang_masuk' => $totalBarangMasuk,
    //             'total_barang_keluar' => $totalBarangKeluar,
    //             'new_stock' => $newStockBarang
    //         ]));

    //     } catch (\Exception $e) {
    //         log_message('error', 'Failed to create/update consolidated stock: ' . $e->getMessage());
    //         throw $e;
    //     }
    // }

    public function updateStockWithSpecificDate($input)
    {
        if (!isset($input['id_tipe']) || !isset($input['tgl_stock'])) {
            throw new \Exception('Missing id_tipe or tgl_stock');
        }

        try {
            // 1. Get latest stock record
            $latestStock = $this->stockBarangModel
                ->where('id_tipe', $input['id_tipe'])
                ->orderBy('tgl_stock', 'DESC')
                ->first();

            if (!$latestStock) {
                throw new \Exception("No stock record found");
            }

            // 2. Check if we have enough stock
            if (isset($input['barang_keluar']) && $input['barang_keluar'] > $latestStock['stock_barang']) {
                throw new \Exception("Stok tidak cukup. Tersedia: {$latestStock['stock_barang']}, Diminta: {$input['barang_keluar']}");
            }

            // 3. Record transaction on the specific date
            $existingDateRecord = $this->stockBarangModel
                ->where('id_tipe', $input['id_tipe'])
                ->where('tgl_stock', $input['tgl_stock'])
                ->first();

            if ($existingDateRecord) {
                // Update existing record for that date
                $this->stockBarangModel->update($existingDateRecord['id_stock'], [
                    'barang_masuk' => $existingDateRecord['barang_masuk'] + ($input['barang_masuk'] ?? 0),
                    'barang_keluar' => $existingDateRecord['barang_keluar'] + ($input['barang_keluar'] ?? 0),
                    'stock_barang' => $existingDateRecord['stock_barang'] // Keep the historical stock value
                ]);
            } else {
                // Create new record for that date
                $this->stockBarangModel->insert([
                    'id_tipe' => $input['id_tipe'],
                    'tgl_stock' => $input['tgl_stock'],
                    'barang_masuk' => $input['barang_masuk'] ?? 0,
                    'barang_keluar' => $input['barang_keluar'] ?? 0,
                    'stock_barang' => $latestStock['stock_barang'] // Use latest stock as reference
                ]);
            }

            // 4. Update the latest stock record
            $newStock = $latestStock['stock_barang'] + ($input['barang_masuk'] ?? 0) - ($input['barang_keluar'] ?? 0);
            
            if ($latestStock['tgl_stock'] == $input['tgl_stock']) {
                // If the latest record is on the same date, just update it
                $this->stockBarangModel->update($latestStock['id_stock'], [
                    'stock_barang' => $newStock
                ]);
            } else {
                // If latest record is different date, update or create new latest record
                $todayRecord = $this->stockBarangModel
                    ->where('id_tipe', $input['id_tipe'])
                    ->where('tgl_stock', date('Y-m-d'))
                    ->first();

                if ($todayRecord) {
                    $this->stockBarangModel->update($todayRecord['id_stock'], [
                        'stock_barang' => $newStock
                    ]);
                } else {
                    $this->stockBarangModel->insert([
                        'id_tipe' => $input['id_tipe'],
                        'tgl_stock' => date('Y-m-d'),
                        'stock_barang' => $newStock,
                        'barang_masuk' => 0,
                        'barang_keluar' => 0
                    ]);
                }
            }

        } catch (\Exception $e) {
            log_message('error', 'Stock update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function recalculateSubsequentStock($id_tipe, $startDate)
    {
        // Get all stock records for this type after the specified date
        $subsequentStocks = $this->stockBarangModel
            ->where('id_tipe', $id_tipe)
            ->where('tgl_stock >', $startDate)
            ->orderBy('tgl_stock', 'ASC')
            ->findAll();

        // Get the stock value from the modified date
        $currentStock = $this->stockBarangModel
            ->where('id_tipe', $id_tipe)
            ->where('tgl_stock', $startDate)
            ->first();

        $runningStock = $currentStock ? $currentStock['stock_barang'] : 0;

        foreach ($subsequentStocks as $stock) {
            // Calculate new stock based on the day's activity
            $runningStock = $runningStock + $stock['barang_masuk'] - $stock['barang_keluar'];
            
            // Update only the stock_barang value, preserving barang_masuk and barang_keluar
            $this->stockBarangModel->update($stock['id_stock'], [
                'stock_barang' => $runningStock
            ]);
        }
    }

    public function deleteStockEntry($id_tipe, $date, $barang_masuk)
    {
        try {
            // Get the stock record for the specified date
            $stockRecord = $this->stockBarangModel
                ->where('id_tipe', $id_tipe)
                ->where('tgl_stock', $date)
                ->first();

            if ($stockRecord) {
                // Update the current day's record
                $newBarangMasuk = $stockRecord['barang_masuk'] - $barang_masuk;
                $newStockBarang = $stockRecord['stock_barang'] - $barang_masuk;

                $this->stockBarangModel->update($stockRecord['id_stock'], [
                    'barang_masuk' => $newBarangMasuk,
                    'stock_barang' => $newStockBarang
                ]);

                // Recalculate subsequent dates
                $this->recalculateSubsequentStock($id_tipe, $date);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete stock entry: ' . $e->getMessage());
            throw $e;
        }
    }

}