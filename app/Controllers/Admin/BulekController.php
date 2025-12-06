<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{BulekModel, PenjualanModel, ViewBulekModel, ViewBulekDetailModel,BarangModel, ViewBulekTotalBiayaModel, ViewBulekDetailBiayaModel, PengeluaranModel};
use CodeIgniter\HTTP\ResponseInterface;

class BulekController extends BaseController
{

    protected $bulekModel;
    protected $penjualanModel;
    protected $viewBulekModel;
    protected $viewBulekTotalBiayaModel;
    protected $viewBulekDetailModel;
    protected $viewBulekDetailBiayaModel;
    protected $pengeluaranModel;
    
    protected $barangModel;


    public function __construct()
    {
        $this->bulekModel = new BulekModel();
        $this->penjualanModel = new PenjualanModel();
        $this->viewBulekModel = new ViewBulekModel();
        $this->viewBulekTotalBiayaModel = new ViewBulekTotalBiayaModel();
        $this->viewBulekDetailBiayaModel = new ViewBulekDetailBiayaModel();
        $this->viewBulekDetailModel = new ViewBulekDetailModel();
        $this->pengeluaranModel = new PengeluaranModel();
    }

    public function list()
    {
        // Fetch all jenis barang for dropdown
        $barangModel = new BarangModel();
        $jenisBarang = $barangModel->findAll();
    
        return view('pages/admin/bulek/list', [
            'jenisBarang' => $jenisBarang
        ]);
    }

    public function getTipeBarang() 
    {
        try {
            $barangModel = new BarangModel();
            $barangList = $barangModel->findAll();
            
            $options = [];
            
            foreach ($barangList as $item) {
                $options[] = [
                    'id' => (int) $item['id_tipe'],
                    'jenis_barang' => $item['jenis_barang']
                ];
            }
            
            return $this->response->setJSON($options);
        } catch (\Exception $e) {
            log_message('error', 'Tipe Barang Error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function datatables()
    {
        $request = service('request');
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        
        // Get total records
        $totalRecords = $this->viewBulekModel->countAllResults();
    
        // Fetch data with pagination
        $data = $this->viewBulekModel->getBulekData();
    
        // Fetch total biaya
        $pengeluaranModel = new PengeluaranModel();
        $totalBiaya = $pengeluaranModel->selectSum('total_biaya')->get()->getRowArray()['total_biaya'];
    
        // Reduce total sisa profit from sums of biaya for id_tipe 7
        foreach ($data as &$row) {
            if ($row['id_tipe'] == 7) {
                $row['total_sisa_profit'] -= $totalBiaya;
            }
        }
    
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ];
    
        return $this->response->setJSON($response);
    }

    public function datatableDetail()
    {
        $request = service('request');
        
        // Pagination parameters
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'];
        
        // Filter parameters with date formatting
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        $jenisBarang = $request->getPost('jenis_barang');
    
        // Log incoming parameters
        log_message('debug', 'Raw Filter Params: ' . json_encode([
            'startDate' => $startDate,
            'endDate' => $endDate,
            'jenisBarang' => $jenisBarang
        ]));
    
        // Start the query builder
        $builder = $this->viewBulekDetailModel->builder();
        
        // Base query to ensure we're getting all fields
        $builder->select('
            id_penjualan,
            tgl_penjualan,
            id_tipe,
            total_harga_modal,
            total_harga_jual,
            total_untung,
            id_bulek,
            tgl_setor,
            jumlah_setor,
            total_setor,
            keterangan,
            sisa_profit
        ');
        $builder->orderBy('tgl_penjualan', 'DESC');

        // Apply filters
        if (!empty($startDate)) {
            $builder->where('tgl_penjualan >=', $startDate);
            log_message('debug', 'Applied start date filter: ' . $startDate);
        }
        
        if (!empty($endDate)) {
            $builder->where('tgl_penjualan <=', $endDate);
            log_message('debug', 'Applied end date filter: ' . $endDate);
        }
        
        if (!empty($jenisBarang)) {
            $builder->where('id_tipe', (int)$jenisBarang);
            log_message('debug', 'Applied jenis barang filter: ' . $jenisBarang);
        }
    
        // Log the SQL query before counting
        log_message('debug', 'Generated SQL: ' . $builder->getCompiledSelect(false));
        
        // Get total records before filtering
        $totalRecords = $this->viewBulekDetailModel->countAllResults(false);
        
        // Get filtered records count
        $totalFiltered = $builder->countAllResults(false);
        
        // Get the actual data with limit and offset
        $builder->limit($length, $start);
        $results = $builder->get()->getResultArray();
        
        // Add jenis_barang information
        $barangModel = new BarangModel();
        foreach ($results as &$row) {
            $barang = $barangModel->find($row['id_tipe']);
            $row['jenis_barang'] = $barang ? $barang['jenis_barang'] : '-';
            
            // Format numeric values
            $row['total_harga_modal'] = number_format($row['total_harga_modal'], 2);
            $row['total_harga_jual'] = number_format($row['total_harga_jual'], 2);
            $row['total_untung'] = number_format($row['total_untung'], 2);
            $row['sisa_profit'] = number_format($row['sisa_profit'], 2);
            $row['jumlah_setor'] = number_format($row['jumlah_setor'], 2);
            
            // Ensure dates are formatted consistently
            $row['tgl_penjualan'] = date('Y-m-d', strtotime($row['tgl_penjualan']));
            if ($row['tgl_setor']) {
                $row['tgl_setor'] = date('Y-m-d', strtotime($row['tgl_setor']));
            }
        }
    
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $results,
            'query' => $builder->getCompiledSelect(false), // DEBUG: Remove this in production
            'filters' => [  // DEBUG: Remove this in production
                'startDate' => $startDate,
                'endDate' => $endDate,
                'jenisBarang' => $jenisBarang
            ]
        ];
    
        return $this->response->setJSON($response);
    }
    
    public function datatableWithBiaya()
    {
        $request = service('request');
        
        // Pagination parameters
        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $search = $request->getPost('search')['value'];
        
        // Filter parameters with date formatting
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        $jenisBarang = $request->getPost('jenis_barang');
        
        // Start the query builder
        $builder = $this->viewBulekDetailBiayaModel->builder();
        
        // Base query to select all relevant fields
        $builder->select('
            id_penjualan,
            tgl_penjualan,
            id_tipe,
            total_harga_modal,
            total_harga_jual,
            total_untung,
            id_bulek,
            tgl_setor,
            jumlah_setor,
            total_setor,
            keterangan,
            id_pengeluaran,
            tgl_pengeluaran,
            total_biaya,
            sisa_profit,
            sisa_profit_after_biaya
        ');
        $builder->orderBy('tgl_penjualan', 'DESC');
        
        // Apply date filters
        if (!empty($startDate)) {
            $builder->where('tgl_penjualan >=', $startDate);
        }
        
        if (!empty($endDate)) {
            $builder->where('tgl_penjualan <=', $endDate);
        }
        
        // Apply jenis barang filter
        if (!empty($jenisBarang)) {
            $builder->where('id_tipe', (int)$jenisBarang);
        }
        
        // Apply filter for id_tipe 7
        $builder->where('id_tipe', 7);
        
        // Apply search if provided
        if (!empty($search)) {
            $builder->groupStart()
                ->like('id_penjualan', $search)
                ->orLike('keterangan', $search)
                ->orLike('total_harga_jual', $search)
                ->groupEnd();
        }
        
        // Get total records before filtering
        $totalRecords = $this->viewBulekDetailBiayaModel->countAllResults(false);
        
        // Get filtered records count
        $totalFiltered = $builder->countAllResults(false);
        
        // Get the actual data with limit and offset
        $builder->limit($length, $start);
        $results = $builder->get()->getResultArray();
        
        // Add jenis_barang information and format values
        $barangModel = new BarangModel();
        foreach ($results as &$row) {
            // Get jenis barang
            $barang = $barangModel->find($row['id_tipe']);
            $row['jenis_barang'] = $barang ? $barang['jenis_barang'] : '-';
            
            // Format numeric values
            $row['total_harga_modal'] = number_format($row['total_harga_modal'], 2, ',', '.');
            $row['total_harga_jual'] = number_format($row['total_harga_jual'], 2, ',', '.');
            $row['total_untung'] = number_format($row['total_untung'], 2, ',', '.');
            $row['total_biaya'] = number_format($row['total_biaya'], 2, ',', '.');
            $row['sisa_profit'] = number_format($row['sisa_profit'], 2, ',', '.');
            $row['jumlah_setor'] = number_format($row['jumlah_setor'], 2);
            $row['sisa_profit_after_biaya'] = number_format($row['sisa_profit_after_biaya'], 2, ',', '.');
            $row['keterangan'] = $row['keterangan'] ?: '-';
            
            // Format dates
            $row['tgl_penjualan'] = date('d-m-Y', strtotime($row['tgl_penjualan']));
            $row['tgl_setor'] = !empty($row['tgl_setor']) ? date('d-m-Y', strtotime($row['tgl_setor'])) : '-';
            $row['tgl_pengeluaran'] = !empty($row['tgl_pengeluaran']) ? date('d-m-Y', strtotime($row['tgl_pengeluaran'])) : '-';
        }
        
        // Prepare response
        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $results
        ];
        
        return $this->response->setJSON($response);
    }

    public function edit($id_penjualan = null)
    {
        if ($id_penjualan === null) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'ID tidak valid'
            ])->setStatusCode(400);
        }
    
        try {
            // Find the bulek record using id_penjualan
            $data = $this->bulekModel
                ->where('id_penjualan', $id_penjualan)
                ->first();
    
            if (!$data) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }
    
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function update()
    {
        $id = $this->request->getPost('id_bulek');
        // $tgl_setor = $this->request->getPost('tgl_setor');
        $jumlah_setor = $this->request->getPost('jumlah_setor');
        $id_tipe = $this->request->getPost('id_tipe');
        $keterangan = $this->request->getPost('keterangan');

        try
        {
            $this->bulekModel->update($id,
                [
                    // 'tgl_setor' => $tgl_setor,
                    'jumlah_setor' => $jumlah_setor,
                    'id_tipe' => $id_tipe,
                    'keterangan' => $keterangan
                ]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data setor berhasil diupdate']);
        } 
            catch (\Exception $e)
        {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete($id_penjualan)
    {
        try {
            // Find the bulek record using id_penjualan
            $bulekRecord = $this->bulekModel
                ->where('id_penjualan', $id_penjualan)
                ->first();
    
            if (!$bulekRecord) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Data bulek tidak ditemukan'
                ]);
            }
    
            // Delete the bulek record
            $this->bulekModel
                ->where('id_penjualan', $id_penjualan)
                ->delete();
    
            return $this->response->setJSON([
                'status' => 'success', 
                'message' => 'Data setor berhasil dihapus'
            ]);
        }
        catch (\Exception $e)
        {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }
}
