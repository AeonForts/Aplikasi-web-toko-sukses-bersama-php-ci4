<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
/* Reset margin and padding for all elements */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

@page {
    size: 58mm auto; /* Fixed width, dynamic height */
    margin: 0;
}

html, body {
    width: 58mm;        /* Fixed width */
    height: auto;       /* Allow height to grow based on content */
    overflow: hidden;   /* Prevent overflow */
    max-width: 58mm;    /* Ensure width doesn't expand */
    margin: 0;
    padding: 0;
}

.receipt {
    width: 58mm;        /* Ensure receipt is within fixed width */
    max-width: 58mm;    /* Prevent it from growing wider */
    font-family: monospace;
    font-size: 8px;
    line-height: 1.2;
    padding: 2mm;
    overflow: visible;  /* Allow content to grow vertically */
    page-break-inside: avoid; /* Prevent content splitting between pages */
}

.header {
    text-align: center;
    margin-bottom: 2mm;
}

.store-name {
    font-size: 10px;
    font-weight: bold;
    margin-bottom: 1mm;
}

.separator {
    border-top: 1px dashed #000;
    margin: 2mm 0;
}

.item-row {
    display: flex;
    justify-content: space-between;
    width: 100%;
    margin-bottom: 1mm;
}

.footer {
    text-align: center;
    font-size: 8px;
    margin-top: 2mm;
}

/* Print Media Query */
@media print {
    body {
        width: 58mm;      /* Ensure body width is constrained for printing */
        max-width: 58mm;  /* Prevent it from becoming too wide */
        margin: 0;
        padding: 0;
        page-break-inside: avoid; /* Ensure content doesn't break oddly */
    }

    .receipt {
        margin: 0 auto;  /* Center the receipt for printing */
        page-break-inside: avoid; /* Prevent content from splitting on different pages */
    }
}

    </style>
</head>
<body>
    <div class="receipt">
        <!-- Store Header -->
        <div class="header">
            <div class="store-name"><?= $storeName ?></div>
            <div><?= $address ?></div>
            <div><?= $phone ?></div>
        </div>

        <!-- Separator -->
        <div class="separator"></div>

        <!-- Transaction Details -->
        <div class="transaction-details">
            <div><?= $date ?></div>
        </div>

        <!-- Separator -->
        <div class="separator"></div>

        <!-- Items -->
        <div class="items">
            <?php foreach ($items as $item): ?>
                <div class="item-row">
                    <span><?= htmlspecialchars($item['name']) ?> (<?= $item['quantity'] ?> @ Rp<?= number_format($item['price'], 0, ',', '.') ?>)</span>
                    <span>Rp<?= number_format($item['total'], 0, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Separator -->
        <div class="separator"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="item-row">
                <span>Sub Total</span>
                <span>Rp<?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div class="item-row">
                <span>TOTAL</span>
                <span>Rp<?= number_format($total, 0, ',', '.') ?></span>
            </div>
            <div class="item-row">
                <span>BAYAR</span>
                <span>Rp<?= number_format($payment, 0, ',', '.') ?></span>
            </div>
            <div class="item-row">
                <span>KEMBALI</span>
                <span>Rp<?= number_format($change, 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Separator -->
        <div class="separator"></div>

        <!-- Footer -->
        <div class="footer">
            <div>Terima Kasih Telah Berbelanja di</div>
            <div>Toko Kami</div>
            <div>Semoga Sehat Selalu</div>
            <div>No Rek: <?= $bankInfo['accountNumber'] ?></div>
            <div><?= $bankInfo['accountName'] ?></div>
            <div><?= $bankInfo['bankName'] ?></div>
        </div>
    </div>
</body>
</html>
