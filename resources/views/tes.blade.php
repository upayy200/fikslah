<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inline Edit Grid Table</title>
  <!-- Link ke Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .editable-cell {
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h2>Grid Table with Inline Editing</h2>

  <table class="table table-bordered" id="editable-table">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Telepon</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="editable-cell">John Doe</td>
        <td class="editable-cell">john@example.com</td>
        <td class="editable-cell">123-456-7890</td>
      </tr>
      <tr>
        <td class="editable-cell">Jane Smith</td>
        <td class="editable-cell">jane@example.com</td>
        <td class="editable-cell">098-765-4321</td>
      </tr>
      <tr>
        <td class="editable-cell">Albert Brown</td>
        <td class="editable-cell">albert@example.com</td>
        <td class="editable-cell">234-567-8901</td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Link ke jQuery dan Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<!-- Skrip untuk mengaktifkan inline editing -->
<script>
    $(document).ready(function() {
      // Saat sel diklik, ubah menjadi input
      $('#editable-table').on('click', '.editable-cell', function() {
        var currentValue = $(this).text();
        var input = $('<input>', {
          type: 'text',
          value: currentValue,
          class: 'form-control'
        });
  
        $(this).html(input);
        input.focus();
  
        input.on('blur keydown', function(e) {
          if (e.type === 'blur' || e.key === 'Enter') {
            var newValue = $(this).val();
            $(this).parent().text(newValue);
          }
        });
      });
  
      // Menangani event paste hanya pada kolom Email (otomatis ubah Telepon jika ada)
      $('#editable-table').on('paste', '.editable-cell', function(e) {
        var cellIndex = $(this).index();
        if (cellIndex !== 1) return; // Hanya kolom Email (index 1)
  
        e.preventDefault();  // Mencegah paste default
  
        var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
        var pastedData = clipboardData.getData('Text').trim();
  
        var rows = pastedData.split('\n'); // Pisahkan berdasarkan baris
        var startRow = $(this).closest('tr'); // Baris tempat paste pertama dilakukan
        var currentRow = startRow;
  
        for (var i = 0; i < rows.length; i++) {
          var columns = rows[i].split('\t'); // Pisahkan berdasarkan tab (Excel)
  
          if (columns.length > 0) {
            currentRow.find('td').eq(1).text(columns[0].trim()); // Set Email
          }
          if (columns.length > 1) {
            currentRow.find('td').eq(2).text(columns[1].trim()); // Set Telepon jika ada
          }
  
          // Pindah ke baris berikutnya jika ada
          currentRow = currentRow.next();
          if (currentRow.length === 0) break; // Hentikan jika sudah tidak ada baris lagi
        }
      });
    });
  </script>
  
  
  
  

</body>
</html>
