<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceItemModel extends Model
{
    protected $table = 'tbl_invoice_items';
    protected $primaryKey = 'id_invoice_item';
    protected $allowedFields = [
        'id_invoice',
        'id_tipe',
        'id_method',
        'id_unit',
        'quantity',
        'price',
        'total'
    ];
}