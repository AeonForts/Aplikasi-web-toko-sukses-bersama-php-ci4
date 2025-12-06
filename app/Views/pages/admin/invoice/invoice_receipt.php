<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?= $invoice['id_invoice'] ?></title>
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            width: 58mm;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            width: 58mm;
            margin: 0 auto;
            padding: 0 2mm;
        }

        .receipt-header, .receipt-footer {
            text-align: center;
            border-bottom: 0.5px dashed #000;
            padding: 2px 0;
        }

        .receipt-header h2 {
            margin: 3px 0;
            font-size: 14px;
        }

        .receipt-details {
            margin: 3px 0;
        }

        .receipt-items {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .receipt-items th, .receipt-items td {
            padding: 2px 1px;
            text-align: left;
            border-bottom: 0.5px dashed #000;
            vertical-align: top;
        }

        .receipt-items th {
            font-size: 14px;
        }

        .item-name {
            font-weight: bold;
        }

        .item-detail {
            display: block;
            font-size: 14px;
            margin-top: 1px;
        }

        .text-right {
            text-align: right;
        }

        .receipt-total {
            margin: 3px 0;
            text-align: right;
            font-size: 14px;
        }

        .receipt-total p:first-child {
            text-align: left; /* This will specifically target the payment method line */
        }

        .receipt-total p {
            margin: 2px 0;
        }

        .receipt-footer {
            margin-top: 5px;
            padding: 2px 0;
            font-size: 14px;
            border-top: 0.5px dashed #000;
        }

        p {
            margin: 2px 0;
        }

        @media print {
            body {
                transform: scale(0.95);
                transform-origin: top left;
            }
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <div class="receipt-header">
        <h2><?= $storeName ?></h2>
        <p><?= $storeAddress ?></p>
        <p>Telp: <?= $storePhone ?></p>
        <p>Invoice #<?= $invoice['id_invoice'] ?></p>
        <p><?= date('d/m/Y H:i:s', strtotime($invoice['invoice_date'])) ?></p>
    </div>

    <div class="receipt-details">
        <p><?= $invoice['customer_name'] ?></p>
        <table class="receipt-items">
            <thead>
                <tr>
                    <th>Barang</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <span class="item-name"><?= $item['jenis_barang'] ?></span>
                            <small class="item-detail">
                                <?= $item['quantity'] ?> <?= $item['tipe_unit'] ?> 
                                @ Rp. <?= number_format($item['price'], 2) ?>
                            </small>
                        </td>
                        <td class="text-right">
                            Rp. <?= number_format($item['total'], 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="receipt-total">
            <?php if(isset($invoice['payment_method'])): ?>
                <p>Pembayaran: <?= $invoice['payment_method'] ?></p>
            <?php endif; ?>
            <p>Total: Rp. <?= number_format($invoice['total_amount'], 2) ?></p>
            <p>Bayar: Rp. <?= number_format($invoice['payment_amount'], 2) ?></p>
            <p>Kembali: Rp. <?= number_format($invoice['change_amount'], 2) ?></p>
        </div>
    </div>

    <div class="receipt-footer">
        <p>Terima Kasih Telah Berbelanja di</p>
        <p>Toko Kami</p>
        <p>Semoga Sehat Selalu</p>
        <p>No Rek: 1140292849</p>
        <p>Septian Cahyadi</p>
        <p>Bank BCA</p>
    </div>
</div>
</body>
</html>