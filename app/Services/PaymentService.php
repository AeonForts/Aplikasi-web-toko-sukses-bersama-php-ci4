<?php
namespace App\Services;

use App\Models\{PenjualanModel, DetailPenjualanModel, PaymentModel, PaymentMethodModel};
use Exception;

class PaymentService
{
    protected $penjualanModel;
    protected $detailPenjualanModel;
    protected $paymentModel;
    protected $paymentMethodModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailPenjualanModel = new DetailPenjualanModel();
        $this->paymentModel = new PaymentModel();
        $this->paymentMethodModel = new PaymentMethodModel();
    }

    public function insertPayment($jumlah, $id_method, $id_customer, $id_penjualan)
    {
        $this->paymentModel->insert([
            'id_customer' => $id_customer,
            'id_method' => $id_method,
            'tgl' => date('Y-m-d'),
            'jumlah' => $jumlah,
            'id_penjualan' => $id_penjualan // Added this line to link payment to penjualan
        ]);
        return $this->paymentModel->getInsertID();
    }

    /**
     * Delete a payment record by its ID
     * 
     * @param int $id_payment Payment ID to delete
     * @return bool True if deletion was successful, false otherwise
     */
    public function deletePayment($id_payment)
    {
        // Validate payment ID
        if (!$id_payment) {
            return false;
        }

        try {
            // Check if payment record exists
            $paymentRecord = $this->paymentModel->find($id_payment);
            
            if (!$paymentRecord) {
                return false;
            }

            // Attempt to delete the payment record
            $deleteResult = $this->paymentModel->delete($id_payment);

            return $deleteResult ? true : false;

        } catch (Exception $e) {
            // Log the error if needed
            log_message('error', 'Payment deletion error: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePayment($id_payment, $data)
    {
        // Validate payment ID
        if (!$id_payment) {
            return false;
        }

        try {
            // Check if payment record exists
            $paymentRecord = $this->paymentModel->find($id_payment);
            
            if (!$paymentRecord) {
                return false;
            }

            // Attempt to update the payment record
            $updateResult = $this->paymentModel->update($id_payment, $data);

            return $updateResult ? true : false;

        } catch (Exception $e) {
            // Log the error if needed
            log_message('error', 'Payment update error: ' . $e->getMessage());
            return false;
        }
    }
}