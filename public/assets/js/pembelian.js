// const jumlahBarangInput = document.getElementById('jumlah-barang');
// // const jumlahBarangKeluarInput = document.getElementById('jumlah-keluar');
// // const jumlahUangInput = document.getElementById('jumlah-uang');
// const hargaModalBarangInput = document.getElementById('harga-modal-barang');
// // const HargaJualInput = document.getElementById('harga-jual-barang');
// const totalBayarMeityInput = document.getElementById('total-bayar-meity');
// // const totalHargaJualInput = document.getElementById('total-harga-jual');
// // const totalHargaModalInput = document.getElementById('');
// // const totalUntung = document.getElementById('total-untung');

// jumlahBarangInput.addEventListener('input', calculateTotalBayarMeity);
// // jumlahBarangKeluarInput.addEventListener('input')
// hargaModalBarangInput.addEventListener('input', calculateTotalBayarMeity);
// // HargaJualInput.addEventListener('input')



// function calculateTotalBayarMeity() {
//     const jumlahBarang = parseFloat(jumlahBarangInput.value);
//     const hargaModalBarang = parseFloat(hargaModalBarangInput.value);
//     const totalBayarMeity = jumlahBarang * hargaModalBarang;
//     totalBayarMeityInput.value = totalBayarMeity.toFixed(2);
// }

// // pembelian.js
// export function calculateTotalBayarMeity(jumlahBarang, hargaModalAwalBarang) {
//     return jumlahBarang * hargaModalAwalBarang;
// }

// Immediately Invoked Function Expression (IIFE) for pembelian functionality
(function() {
    function calculateTotalBayarMeity(jumlahBarang, hargaModalBarang) {
        return jumlahBarang * hargaModalBarang;
    }

    function initPembelian() {
        const jumlahBarangInput = document.getElementById('jumlah-barang');
        const hargaModalBarangInput = document.getElementById('harga-modal-barang');
        const totalBayarMeityInput = document.getElementById('total-harga-meity');

        function updatePembelian() {
            const jumlahBarang = parseFloat(jumlahBarangInput.value) || 0;
            const hargaModalBarang = parseFloat(hargaModalBarangInput.value) || 0;
            const totalBayarMeity = calculateTotalBayarMeity(jumlahBarang, hargaModalBarang);
            totalBayarMeityInput.value = totalBayarMeity.toFixed(2);
        }

        jumlahBarangInput.addEventListener('input', updatePembelian);
        hargaModalBarangInput.addEventListener('input', updatePembelian);
    }

    document.addEventListener('DOMContentLoaded', initPembelian);
})();




