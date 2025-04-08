<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Mandor Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        
        h1, h2 {
            text-align: center;
            color: #333;
        }
        
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        button {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        
        button:hover {
            background: #218838;
        }
        
        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background: #28a745;
            color: white;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Input Mandor Karyawan</h1>
        <form id="filter-form">
            Bulan: <input type="date" id="bulan" name="bulan">
            Afdeling:
            <select id="kd_afd" name="kd_afd">
                <option value="">Pilih Afdeling</option>
                @foreach($afdeling as $afd)
                    <option value="{{ $afd->id }}">{{ $afd->nama }}</option>
                @endforeach
            </select>
            Plant: <input type="text" id="plant" name="plant" value="{{ $plant ?? 'Tidak Ditemukan' }}" readonly>
            Mandor:
            <select id="reg_mandor" name="reg_mandor">
                <option value="">Pilih Mandor</option>
            </select>
            <button type="button" id="cari">Cari</button>
        </form>

        <h2>Data Karyawan</h2>
        <table id="data-karyawan" style="display:none;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Register</th>
                    <th>Reg SAP</th>
                    <th>Status</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <h2>Tambah Karyawan</h2>
        <select id="new-karyawan">
            <option value="">Pilih Karyawan</option>
        </select>
        <button id="tambah-karyawan">Tambah</button>
    </div>

    
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk dropdown Tambah Karyawan
        $('#new-karyawan').select2({
            placeholder: "Cari Karyawan (Register/Nama)...",
            allowClear: true,
            width: '100%'
        });
    
        // Event saat Afdeling berubah -> Load daftar mandor
        $('#kd_afd').change(function() {
            let kdAfd = $(this).val();
            if (kdAfd) {
                $.get(`/referensi/get-mandor/${kdAfd}`, function(data) {
                    $('#reg_mandor').empty().append('<option value="">Pilih Mandor</option>');
                    data.forEach(mandor => {
                        $('#reg_mandor').append(`<option value="${mandor.Regmdr}">${mandor.NamaMandor}</option>`);
                    });
                });
            }
        });
    
        // Function untuk load data karyawan di tabel utama
        function loadKaryawan() {
            let tanggal = $('#bulan').val();
            let kdAfd = $('#kd_afd').val();
            let regMandor = $('#reg_mandor').val();
            
            if (!tanggal || !kdAfd || !regMandor) {
                alert('Harap pilih semua filter terlebih dahulu');
                return;
            }
    
            $.post('/referensi/get-karyawan', {
                tanggal: tanggal,
                kd_afd: kdAfd,
                reg_mandor: regMandor,
                _token: '{{ csrf_token() }}'
            }, function(data) {
                let tbody = $('#data-karyawan tbody');
                tbody.empty();
                data.forEach((row, index) => {
                    tbody.append(`<tr>
                        <td>${index + 1}</td>
                        <td>${row.Register}</td>
                        <td>${row.RegSAP}</td>
                        <td>${row.sts}</td>
                        <td>${row.Nama}</td>
                        <td>${row.NAMA_JAB}</td>
                        <td><button class="hapus-karyawan" data-id="${row.Register}">Hapus</button></td>
                    </tr>`);
                });
                $('#data-karyawan').show();
            });
        }
    
        // Event tombol "Cari" -> Load data tabel dan dropdown Tambah Karyawan
        $('#cari').click(function() {
            loadKaryawan();
            
            let regMandor = $('#reg_mandor').val();
            if (!regMandor) {
                alert("Pilih mandor terlebih dahulu!");
                return;
            }
    
            $.get(`/referensi/get-karyawan-tanpa-mandor/${regMandor}`, function(data) {
    let select = $('#new-karyawan');
    select.empty().append('<option value="">Pilih Karyawan</option>');

    // Gunakan Set untuk menghindari data duplikat berdasarkan Register
    let uniqueKaryawan = new Set();
    data.forEach(karyawan => {
        let formattedText = `(${karyawan.Register}) ${karyawan.Nama} - ${karyawan.sts}`; // Tambahkan status
        if (!uniqueKaryawan.has(formattedText)) {
            uniqueKaryawan.add(formattedText);
            select.append(`<option value="${karyawan.Register}">${formattedText}</option>`);
        }
    });
                // Refresh Select2 setelah update data
                select.trigger('change');
            }).fail(function(xhr) {
                console.error("Gagal mengambil data karyawan tanpa mandor:", xhr.responseText);
            });
        });
    
        // Event tombol "Tambah Karyawan"
        $('#tambah-karyawan').click(function() {
            let register = $('#new-karyawan').val();
            let regMandor = $('#reg_mandor').val();
            let tanggal = $('#bulan').val();
            let kdAfd = $('#kd_afd').val();
    
            if (!register || !regMandor || !tanggal || !kdAfd) {
                alert('Pastikan semua data sudah dipilih!');
                return;
            }
    
            $.ajax({
                url: '/referensi/tambah-karyawan',
                type: 'POST',
                data: {
                    tanggal: tanggal,
                    kd_afd: kdAfd,
                    reg_mandor: regMandor,
                    register: register,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.success);
                    loadKaryawan();
                },
                error: function(xhr) {
                    console.error("Gagal menambah karyawan:", xhr.responseText);
                }
            });
        });
    
        // Event tombol "Hapus Karyawan"
        $(document).on('click', '.hapus-karyawan', function() {
            let register = $(this).data('id');
    
            if (confirm('Apakah Anda yakin ingin menghapus karyawan ini?')) {
                $.ajax({
                    url: '/referensi/hapus-karyawan',
                    type: 'POST',
                    data: {
                        register: register,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert(response.success);
                        loadKaryawan();
                    },
                    error: function(xhr) {
                        console.error("Gagal menghapus karyawan:", xhr.responseText);
                    }
                });
            }
        });
    });
    
    </script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

</body>
</html>
