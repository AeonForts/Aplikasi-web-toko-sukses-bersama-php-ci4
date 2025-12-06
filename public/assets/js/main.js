// Immediately Invoked Function Expression for setting up event listeners
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const jumlahBarangInput = document.getElementById('jumlah-barang');
        const hargaModalBarangInput = document.getElementById('harga-modal-barang'); // Using the same name
        const totalBayarMeityInput = document.getElementById('total-bayar-meity');

        const jumlahUangInput = document.getElementById('jumlah-uang');
        const hargaJualInput = document.getElementById('harga-jual-barang');
        const jumlahBarangKeluarInput = document.getElementById('jumlah-keluar');
        const totalHargaJualInput = document.getElementById('total-harga-jual');
        const totalHargaModalInput = document.getElementById('total-harga-modal');
        const totalUntungInput = document.getElementById('total-untung');

        function updatePembelian() {
            const jumlahBarang = parseFloat(jumlahBarangInput.value) || 0;
            const hargaModalBarang = parseFloat(hargaModalBarangInput.value) || 0;
            const totalBayarMeity = window.calculateTotalBayarMeity(jumlahBarang, hargaModalBarang);
            totalBayarMeityInput.value = totalBayarMeity.toFixed(2);
        }

        function updatePenjualan() {
            const jumlahUang = parseFloat(jumlahUangInput.value) || 0;
            const hargaJualBarang = parseFloat(hargaJualInput.value) || 0;
            const barangKeluar = window.calculateBarangKeluar(jumlahUang, hargaJualBarang);
            jumlahBarangKeluarInput.value = barangKeluar.toFixed(2);

            const hargaModalBarang = parseFloat(hargaModalBarangInput.value) || 0;
            const { totalHargaModal, totalHargaJual, totalUntung } = window.calculateTotalHarga(barangKeluar, hargaModalBarang, hargaJualBarang);
            totalHargaModalInput.value = totalHargaModal.toFixed(2);
            totalHargaJualInput.value = totalHargaJual.toFixed(2);
            totalUntungInput.value = totalUntung.toFixed(2);
        }

        jumlahBarangInput.addEventListener('input', updatePembelian);
        hargaModalBarangInput.addEventListener('input', updatePembelian);

        jumlahUangInput.addEventListener('input', updatePenjualan);
        hargaJualInput.addEventListener('input', updatePenjualan);
        hargaModalBarangInput.addEventListener('input', updatePenjualan);
    });
})();
