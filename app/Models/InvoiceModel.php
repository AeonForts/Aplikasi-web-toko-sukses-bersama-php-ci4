<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table = 'tbl_invoices';
    protected $primaryKey = 'id_invoice';
    protected $allowedFields = [
        'customer_name',
        'total_amount',
        'payment_amount',
        'change_amount',

        'invoice_date'
    ];

    // Add methods for retrieving and manipulating invoices
    public function getInvoiceWithItems($invoiceId)
    {
        return $this->select('tbl_invoices.*, ii.*, tb.jenis_barang')
            ->join('tbl_invoice_items ii', 'ii.id_invoice = tbl_invoices.id_invoice')
            ->join('tbl_tipe_barang tb', 'ii.id_tipe = tb.id_tipe')
            ->where('tbl_invoices.id_invoice', $invoiceId)
            ->findAll();
    }
}