    // Your custom script that uses Numeral.js
    document.addEventListener('DOMContentLoaded', function() {
        function formatNumbers() {
          const totalBiaya = numeral(document.getElementById('total_biaya').textContent);
          const jumlahBiaya = numeral(document.getElementById('jumlah_biaya').textContent);
          
          document.getElementById('total_biaya').textContent = totalBiaya.format('$0,0.00');
          document.getElementById('jumlah_biaya').textContent = jumlahBiaya.format('$0,0.00');
        }
  
        function formatInput() {
          const input = document.getElementById('input_biaya');
          input.addEventListener('input', function() {
            this.value = numeral(this.value).format('$0,0.00');
          });
        }
  
        formatNumbers();
        formatInput();
      });