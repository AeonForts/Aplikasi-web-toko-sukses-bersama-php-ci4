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

    public function updateStockWithSpecificDate($input)
    {
        // Validate input
        if (!isset($input['id_tipe']) || !isset($input['tgl_stock'])) {
            throw new \Exception('Missing id_tipe or tgl_stock for stock creation.');
        }

        // Retrieve the latest stock for this product type before the specified date
        $latestPreviousStock = $this->stockBarangModel
            ->where('id_tipe', $input['id_tipe'])
            ->where('tgl_stock <=', $input['tgl_stock'])
            ->orderBy('tgl_stock', 'DESC')
            ->first();

        // Calculate current stock
        $currentStock = $latestPreviousStock 
            ? $latestPreviousStock['stock_barang'] + $input['barang_masuk'] - $input['barang_keluar']
            : $input['barang_masuk'] - $input['barang_keluar'];

        try {
            // Check if a stock record for this type and date already exists
            $existingStock = $this->stockBarangModel
                ->where('id_tipe', $input['id_tipe'])
                ->where('tgl_stock', $input['tgl_stock'])
                ->first();

            if ($existingStock) {
                // Update existing stock record
                $this->stockBarangModel->update($existingStock['id_stock'], [
                    'stock_barang' => $currentStock,
                    'barang_masuk' => $existingStock['barang_masuk'] + $input['barang_masuk'],
                    'barang_keluar' => $existingStock['barang_keluar'] + $input['barang_keluar']
                ]);

                return $existingStock['id_stock'];
            } else {
                // Insert new stock record
                return $this->stockBarangModel->insert([
                    'id_tipe' => $input['id_tipe'],
                    'tgl_stock' => $input['tgl_stock'],
                    'stock_barang' => $currentStock,
                    'barang_masuk' => $input['barang_masuk'],
                    'barang_keluar' => $input['barang_keluar']
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to create/update stock with specific date: ' . $e->getMessage());
            throw $e;
        }
    }

}