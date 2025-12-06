// Immediately Invoked Function Expression (IIFE) for penjualan functionality
(function() {
    function calculateBarangKeluar(jumlahUang, hargaJualBarang) {
        return hargaJualBarang ? (jumlahUang / hargaJualBarang) : 0;
    }

    function calculateTotalHarga(barangKeluar, hargaModalBarang, hargaJualBarang) {
        const totalHargaModal = barangKeluar * hargaModalBarang;
        const totalHargaJual = barangKeluar * hargaJualBarang;
        const totalUntung = totalHargaJual - totalHargaModal;
        return { totalHargaModal, totalHargaJual, totalUntung };
    }

    function initPenjualan() {
        const jumlahUangInput = document.getElementById('jumlah-uang');
        const hargaJualInput = document.getElementById('harga-jual');
        const jumlahBarangKeluarInput = document.getElementById('jumlah-keluar');
        const hargaModalBarangInput = document.getElementById('harga-modal-barang');
        const totalHargaJualInput = document.getElementById('total-harga-jual');
        const totalHargaModalInput = document.getElementById('total-harga-modal');
        const totalUntungInput = document.getElementById('total-untung');

        // Hidden inputs
        const jumlahKeluarHidden = document.getElementById('jumlah-keluar-hidden');
        const totalHargaJualHidden = document.getElementById('total-harga-jual-hidden');
        const totalHargaModalHidden = document.getElementById('total-harga-modal-hidden');
        const totalUntungHidden = document.getElementById('total-untung-hidden');

        function updatePenjualan() {
            const jumlahUang = parseFloat(jumlahUangInput.value) || 0;
            const hargaJualBarang = parseFloat(hargaJualInput.value) || 0;
            const barangKeluar = calculateBarangKeluar(jumlahUang, hargaJualBarang);
            jumlahBarangKeluarInput.value = barangKeluar.toFixed(2);
            jumlahKeluarHidden.value = barangKeluar.toFixed(2);

            const hargaModalBarang = parseFloat(hargaModalBarangInput.value) || 0;
            const { totalHargaModal, totalHargaJual, totalUntung } = calculateTotalHarga(barangKeluar, hargaModalBarang, hargaJualBarang);
            totalHargaModalInput.value = totalHargaModal.toFixed(2);
            totalHargaJualInput.value = totalHargaJual.toFixed(2);
            totalUntungInput.value = totalUntung.toFixed(2);

            totalHargaModalHidden.value = totalHargaModal.toFixed(2);
            totalHargaJualHidden.value = totalHargaJual.toFixed(2);
            totalUntungHidden.value = totalUntung.toFixed(2);
        }

        jumlahUangInput.addEventListener('input', updatePenjualan);
        hargaJualInput.addEventListener('input', updatePenjualan);
        hargaModalBarangInput.addEventListener('input', updatePenjualan);
    }

    document.addEventListener('DOMContentLoaded', initPenjualan);
})();
