<?php
namespace App\Services;

use App\Models\{PenjualanModel, CustomerModel, DetailPenjualanModel};
use exception;

class CustomerService
{
    protected $penjualanModel;
    protected $customerModel;
    protected $detailPenjualanModel;


    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->customerModel = new CustomerModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();

    }

    public function getOrCreateCustomer($nama_customer)
    {
        $customer = $this->customerModel->where('nama_customer', $nama_customer)->first();
        if (!$customer) {
            $this->customerModel->insert(['nama_customer' => $nama_customer]);
            $customer = ['id_customer' => $this->customerModel->getInsertID()];
        }
        return $customer;
    }
}