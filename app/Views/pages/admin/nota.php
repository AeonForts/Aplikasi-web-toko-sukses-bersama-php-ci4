<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NOTA PEMBAYARAN</title>
    <style>
        @media print {
            .page-break {
                display: block;
                page-break-before: always;
            }
        }
        #invoice-POS {
            box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
            padding: 10mm; /* Tambahkan padding untuk memberikan ruang lebih */
            margin: 0 auto;
            width: 44mm;
            background: #FFF;
            border: 1px solid #EEE; /* Tambahkan border agar terlihat lebih rapi */
            border-radius: 5px; /* Tambahkan border-radius untuk sudut yang lebih halus */
        }

        #invoice-POS h1 {
            font-size: 1.5em;
            color: #222;
        }

        #invoice-POS h2 {
            font-size: .9em;
            margin-bottom: 5px; /* Tambahkan margin bawah untuk memberi jarak */
        }

        #invoice-POS h3 {
            font-size: 1.2em;
            font-weight: 300;
            line-height: 2em;
        }

        #invoice-POS p {
            font-size: .7em;
            color: #666;
            line-height: 1.2em;
            margin: 0; /* Pastikan margin nol untuk p */
        }

        #invoice-POS #top,
        #invoice-POS #mid,
        #invoice-POS #bot {
            border-bottom: 1px solid #EEE;
            padding-bottom: 5mm; /* Tambahkan padding bawah */
            margin-bottom: 5mm; /* Tambahkan margin bawah untuk memberi ruang antara bagian */
        }

        #invoice-POS table {
            width: 100%;
            border-collapse: collapse;
        }

        #invoice-POS .tabletitle {
            font-size: .5em;
            background: #EEE;
            padding: 5px; /* Tambahkan padding untuk ruang lebih */
            text-align: left; /* Rata kiri agar terlihat lebih rapih */
        }

        #invoice-POS .service {
            border-bottom: 1px solid #EEE;
        }

        #invoice-POS .item {
            width: 24mm;
        }

        #invoice-POS .itemtext {
            font-size: .6em; /* Naikkan sedikit ukuran font */
            padding: 5px; /* Tambahkan padding untuk ruang lebih */
        }

        #invoice-POS #legalcopy {
            margin-top: 5mm;
        }
    </style>
</head>
<body translate="no">
    <div id="invoice-POS">
        <center id="top">
            <div class="info">
                <h2>Toko Sukses Bersama</h2>
                <p>Toko Agen Telur dan Minyak</p>
                <p>Jl. Bantarkemang <br>
                    Email: -</p>
            </div>
        </center>
        <div id="mid">
            <div class="info">
                <p>
                    No Nota     :-  </br>
                    Tanggal     :-  </br>
                    Telephone   :-  </br>
                </p>
            </div>
        </div>
        <div id="bot">
            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="item">
                            <h2>Barang</h2>
                        </td>
                        <td class="Hours">
                            <h2>Qty</h2>
                        </td>
                        <td class="Rate">
                            <h2>Sub Total</h2>
                        </td>
                    </tr>

                    <tr class="service">
                        <td class="tableitem">
                            <p class="itemtext">Telur</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">2 kg</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">54.000</p>
                        </td>
                    </tr>

                    <tr class="tabletitle">
                        <td></td>
                        <td class="tableitem">
                            <p class="itemtext">Total</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">: 54.000</p>
                        </td>
                    </tr>
                    <tr class="tabletitle">
                        <td></td>
                        <td class="tableitem">
                            <p class="itemtext">Bayar</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">: 100.000</p>
                        </td>
                    </tr>
                    <tr class="tabletitle">
                        <td></td>
                        <td class="tableitem">
                            <p class="itemtext">Kembali</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">: 46.000</p>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="legalcopy">
                <p class="legal"><strong>Terimakasih Telah Berbelanja!</strong> Barang yang sudah dibeli tidak dapat dikembalikan. Jangan lupa berkunjung kembali</p>
            </div>
        </div>
    </div>
</body>
<script>
    window.addEventListener("load", window.print());
</script>
</html>
