const jumlahBarangInput = document.getElementById('jumlah-barang');
const jumlahBarangKeluarInput = document.getElementById('jumlah-keluar');
const jumlahUangInput = document.getElementById('jumlah-uang');
const hargaModalBarangInput = document.getElementById('harga-modal-barang');
const hargaJualInput = document.getElementById('harga-jual-barang');
// const totalBayarMeityInput = document.getElementById('total-bayar-meity');
const totalBayarMeityInput = document.getElementById('total-bayar-meity');
const totalHargaJualInput = document.getElementById('total-harga-jual');
const totalHargaModalInput = document.getElementById('total-harga-modal');
const totalUntungInput = document.getElementById('total-untung');

// Event listeners
jumlahUangInput.addEventListener('input', calculateBarangKeluar);
hargaJualInput.addEventListener('input', calculateBarangKeluar);
hargaModalBarangInput.addEventListener('input', calculateTotalHarga);
jumlahBarangKeluarInput.addEventListener('input', calculateTotalHarga);
jumlahBarangInput.addEventListener('input', calculateTotalBayarMeity);

// Function to calculate Barang Keluar
function calculateBarangKeluar() {
    const jumlahUang = parseFloat(jumlahUangInput.value) || 0;
    const hargaJualBarang = parseFloat(hargaJualInput.value) || 0;
    const barangKeluar = jumlahUang / hargaJualBarang;

    jumlahBarangKeluarInput.value = barangKeluar.toFixed(2);

    // Trigger calculation of total harga
    calculateTotalHarga();
}

function calculateTotalBayarMeity(){
    const jumlahBarang = parseFloat(jumlahBarangInput.value);
    const hargaModalBarang = parseFloat(hargaModalBarangInput.value);
    const totalBayarMeity = jumlahBarang * hargaModalBarang;
    totalBayarMeityInput.value = totalBayarMeity.toFixed(2);
}

// Function to calculate Total Harga Modal, Total Harga Jual, and Estimasi Untung
function calculateTotalHarga() {
    const barangKeluar = parseFloat(jumlahBarangKeluarInput.value) || 0;
    const hargaModalBarang = parseFloat(hargaModalBarangInput.value) || 0;
    const hargaJualBarang = parseFloat(hargaJualInput.value) || 0;

    const totalHargaModal = barangKeluar * hargaModalBarang;
    const totalHargaJual = barangKeluar * hargaJualBarang;
    const totalUntung = totalHargaJual - totalHargaModal;

    totalHargaModalInput.value = totalHargaModal.toFixed(2);
    totalHargaJualInput.value = totalHargaJual.toFixed(2);
    totalUntungInput.value = totalUntung.toFixed(2);
}

// function calculateTotalBayarMeity() {
//     const jumlahBarang = parseFloat(jumlahBarangInput.value) || 0;
//     const hargaModalBarang = parseFloat(hargaModalBarangInput.value) || 0;
//     const totalBayarMeity = jumlahBarang * hargaModalBarang;

//     totalBayarMeityInput.value = totalBayarMeity.toFixed(2);

//     // Trigger calculation of total harga
//     calculateTotalHarga();
// }

