@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-12 col-xl-10 mx-auto">
            <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                <h4 class="mb-4" style="border-bottom:2px solid #4CAF50;padding-bottom:8px;">Absensi Komoditi Teh</h4>
                <form id="form-komoditi-teh">
                    @csrf
                    @include('layouts.alert')
                    <!-- Form input -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal">Tanggal</label>
                            <input type="text" class="form-control datepicker" name="tanggal" id="tanggal" autocomplete="off">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kd_afd">Afdeling/Bagian</label>
                            <select name="kd_afd" id="kd_afd" class="form-control select2-afdeling">
                                <option value="">-- Pilih Afdeling/Bagian --</option>
                                @foreach ($afdBagian as $item)
                                <option value="{{ $item->KodeAfdeling }}">{{ $item->KodeAfdeling }} - {{ $item->NamaAfdeling }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="reg_mandor">Reg. Mandor</label>
                            <select name="reg_mandor" id="reg_mandor" class="form-control select2-mandor">
                                <option value="">-- Pilih Mandor --</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mb-2">
                        <a href="#" class="btn btn-primary" id="btn-read">READ</a>
                    </div>
                    <!-- Tabel Karyawan -->
                    @php
                        $karyawan = session()->get('karyawan');
                        $absen = session()->get('absen');
                        $target_alokasi = session()->get('target_alokasi');
                    @endphp
                    @if(isset($karyawan) && count($karyawan) > 0)
                    <div class="table-responsive" style="max-height: 80vh;">
                        <table class="table table-bordered table-striped" id="tabel-karyawan" style="min-width:1800px;">
                            <thead class="table-success text-center">
                                <tr>
                                    <th style="min-width: 50px;">No</th>
                                    <th style="min-width: 120px;">Register</th>
                                    <th style="min-width: 100px;">Reg.SAP</th>
                                    <th style="min-width: 120px;">Nama</th>
                                    <th style="min-width: 200px;">Jabatan</th>
                                    <th style="min-width: 80px;">Afdeling</th>
                                    <th style="min-width: 100px;">Kode Absen</th>
                                    <th style="min-width: 180px;">Target Alokasi</th>
                                    <th style="min-width: 120px;">Location Code/CC</th>
                                    <th style="min-width: 120px;">Tahun Tanam</th>
                                    <th style="min-width: 120px;">Aktifitas</th>
                                    <th style="min-width: 150px;">Luas Jelajah (Ha)</th>
                                    <th style="min-width: 100px;">Satuan</th>
                                    <th style="min-width: 120px;">Hasil Panen</th>
                                    <th style="min-width: 100px;">Kg Pikul</th>
                                    <th style="min-width: 100px;">Sts Pikul</th>
                                    <th style="min-width: 100px;">AMB (%)</th>
                                    <th style="min-width: 100px;">Mesin Petik</th>
                                    <th style="min-width: 100px;">Jendangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($karyawan as $index => $item)
                                <tr>
                                  <td>{{ $index + 1 }}</td>
                                  <td>{{ $item->REG }}</td>
                                  <td>{{ $item->REG_SAP }}</td>
                                  <td>{{ $item->NAMA }}</td>
                                  <td>{{ $item->NAMA_JAB }}</td>
                                  <td>{{ $item->KD_AFD }}</td>
                                  <td>
                                    <select name="data[{{ $index }}][absen]" class="form-control absen-select" onchange="handleAbsenChange(this)">
                                      <option value="">Pilih</option>
                                      @foreach ($absen as $item2)
                                        <option value="{{ $item2->KodeAbsen }}" @selected(old('data.'.$index.'.absen')==$item2->KodeAbsen)>
                                    {{ $item2->KodeAbsen }} - {{ $item2->Uraian }} 
                                      </option>
                                      @endforeach
                                    </select>
                                  </td>
                                  <td>
                                    <select name="data[{{ $index }}][target_alokasi]" class="form-control target-alokasi-select">
                                      <option value="">Pilih Target</option>
                                      @foreach ($target_alokasi as $item2)
                                      <option value="{{ $item2->TargetAlokasi }} - {{ $item2->Uraian }}">
                                        {{ $item2->TargetAlokasi }} - {{ $item2->Uraian }}
                                      </option>
                                      @endforeach
                                    </select>
                                  </td>
                                  <td style="min-width: 200px;">
                                    @if(isset($item->target_alokasi) && str_contains($item->target_alokasi, 'CC'))
                                      <select name="data[{{ $index }}][lokasi]" class="form-control select2-costcenter" data-placeholder="Pilih Cost Center..." style="width: 100%">
                                        <option value=""></option>
                                        <!-- Options akan diisi via JavaScript -->
                                      </select>
                                    @else
                                      <input type="text" name="data[{{ $index }}][lokasi]" class="form-control lokasi-input" value="{{ old('data.'.$index.'.lokasi', $item->lokasi ?? '') }}" style="width: 100%">
                                    @endif
                                  </td>
                                  <td>
                                    <input type="text" name="data[{{ $index }}][thntnm]" class="form-control" value="{{ old('data.'.$index.'.thntnm') }}" readonly>
                                  </td>
                                  <td class="aktifitas-column">
                                    <select name="data[{{ $index }}][aktifitas]" class="form-control select2-aktifitas">
                                        <option value="">Pilih Aktifitas</option>
                                        @if(isset($aktifitas))
                                            @foreach ($aktifitas as $item)
                                                <option value="{{ $item->Aktifitas }}">{{ $item->Aktifitas }} - {{ $item->Uraian }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                  </td>
                                  <td>
                                    <input type="text" name="data[{{ $index }}][jelajahHA]" class="form-control jelajah-input" value="{{ old('data.'.$index.'.jelajahHA') }}" readonly>
                                  </td>
                                  <td>
                                    <input type="text" name="data[{{ $index }}][satuan]" class="form-control satuan-input" value="{{ old('data.'.$index.'.satuan') }}" readonly>
                                  </td>
                                  <td>
                                    <input type="text" name="data[{{ $index }}][hslpanen]" class="form-control panen-input" value="{{ old('data.'.$index.'.hslpanen') }}" readonly>
                                  </td>
                                  <td>
                                    <input type="text" name="data[{{ $index }}][jmlkg]" class="form-control kg-input" value="{{ old('data.'.$index.'.jmlkg') }}" readonly>
                                  </td>
                                  <td>
                                    <input type="text" name="data[{{ $index }}][stpikul]" class="form-control sts-input" value="{{ old('data.'.$index.'.stpikul') }}" readonly>
                                  </td>
                                  <td>
                                    <input type="text" name="data[{{ $index }}][pct]" class="form-control amb-input" value="{{ old('data.'.$index.'.pct') }}" readonly>
                                  </td>
                                  <td>
                                    <select name="data[{{ $index }}][ms]" class="form-control select2-mesin-petik" style="width: 100%">
                                      <option value="">Pilih Mesin Petik</option>
                                    </select>
                                  </td>
                                  <td>
                                    <select name="data[{{ $index }}][jendangan]" class="form-control select2-jendangan" style="width: 100%" disabled>
                                        <option value="">Pilih Jendangan</option>
                                    </select>
                                </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Simpan</button> @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Gaya dasar tabel */
        .table th, .table td {
            vertical-align: middle;
        }
        
        /* Gaya dasar untuk semua dropdown Select2 */
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 4px;
            border: 1px solid #ced4da;
            transition: border-color 0.15s ease-in-out;
        }
        
        /* Fokus state untuk dropdown */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Gaya untuk dropdown yang terbuka */
        .select2-container--default .select2-results__option {
            padding: 8px 12px;
            font-size: 14px;
        }
        
        /* Highlight saat hover */
        .select2-container--default .select2-results__option--highlighted {
            background-color: #f8f9fa;
            color: #495057;
        }
        
        /* Gaya untuk item yang dipilih */
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e9ecef;
        }
        
        /* Gaya khusus untuk semua dropdown di tabel */
        td .select2-container {
            width: 100% !important;
            min-width: 200px;
        }
        
        /* Memastikan teks tidak terpotong */
        .select2-results__option {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Gaya khusus untuk dropdown Blok SAP */
        .select2-bloksap + .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #0080ff;
            color: white;
        }
        
        .select2-bloksap + .select2-container--default .select2-results__option--highlighted {
            background-color: #0069d9;
            color: white;
        }
        
        /* Gaya khusus untuk dropdown Aktifitas */
        .select2-aktifitas + .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #28a745;
            color: white;
        }
        
        .select2-aktifitas + .select2-container--default .select2-results__option--highlighted {
            background-color: #218838;
            color: white;
        }
        
        /* Gaya khusus untuk dropdown Cost Center */
        .select2-costcenter + .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #6c757d;
            color: white;
        }
        
        .select2-costcenter + .select2-container--default .select2-results__option--highlighted {
            background-color: #5a6268;
            color: white;
        }
        
        /* Penyesuaian tinggi dropdown */
        .select2-container--open .select2-dropdown {
            min-width: 200px;
        }
        
        /* Penyesuaian untuk tampilan mobile */
        @media (max-width: 768px) {
            td .select2-container {
                min-width: 150px !important;
            }
        }

        /* Untuk styling datepicker agar lebih konsisten */
        input[type="date"] {
    -webkit-appearance: none;
    padding: 8px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
    </style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
<script>

$(document).ready(function() {
    // Inisialisasi datepicker (sebaiknya di luar event click)
    $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        language: 'id'
    });
    
    // Set tanggal default jika kosong
    if (!$('#tanggal').val()) {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        $('#tanggal').val(`${day}-${month}-${year}`);
    }

    // Handler tombol READ
    $('#btn-read').on('click', function(e) {
        e.preventDefault();
        
        const kd_afd = $('#kd_afd').val();
        const reg_mandor = $('#reg_mandor').val();
        const tanggal = $('#tanggal').val(); // Ambil nilai tanggal
        
        if (!kd_afd || !reg_mandor) {
            alert('Silakan pilih Afdeling dan Mandor terlebih dahulu');
            return;
        }
        
        // Redirect dengan parameter tambahan tanggal
        window.location.href = `/checkroll/komoditi_teh?kd=${encodeURIComponent(kd_afd)}&reg=${encodeURIComponent(reg_mandor)}&tgl=${encodeURIComponent(tanggal)}`;
    });
});

    function handleAbsenChange(selectElement) {
        const row = $(selectElement).closest('tr');
        const absenValue = $(selectElement).val();
        const targetSelect = row.find('.target-alokasi-select');
        const aktifitasSelect = row.find('.select2-aktifitas');
        
        // Reset dan nonaktifkan kolom aktifitas jika kode absen belum dipilih
        if (!absenValue) {
            aktifitasSelect.val('').prop('disabled', true);
            targetSelect.val('').prop('disabled', true);
        } else {
            aktifitasSelect.prop('disabled', false);
            targetSelect.prop('disabled', false);
        }
    }

    $(document).ready(function() {
        // Inisialisasi Select2 untuk target alokasi
        $('.target-alokasi-select').select2({
            placeholder: "Pilih Target Alokasi",
            allowClear: false,
            width: '100%'
        }).prop('disabled', true); // Nonaktifkan di awal

        // Inisialisasi Select2 untuk aktifitas
        $('.select2-aktifitas').select2({
            placeholder: "Pilih Aktifitas",
            allowClear: false,
            width: '100%'
        }).prop('disabled', true); // Nonaktifkan di awal

        // Pasang event handler untuk perubahan kode absen
        $(document).on('change', '.absen-select', function() {
            handleAbsenChange(this);
        });

        // Jalankan handleAbsenChange untuk setiap baris saat halaman dimuat
        $('.absen-select').each(function() {
            handleAbsenChange(this);
        });
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.select2-afdeling').select2({
        placeholder: "-- Pilih Afdeling/Bagian --",
        allowClear: false
    });

    $('.select2-mandor').select2({
        placeholder: "-- Pilih Mandor --",
        allowClear: false
    });

    $('#kd_afd').on('change', function() {
        let kd_afd = $(this).val();
        $('#reg_mandor').html('<option value="">-- Pilih Mandor --</option>');

        if (kd_afd) {
            $.post('{{ url("checkroll/get-mandor-by-afd") }}', {
                kd_afd: kd_afd
            }, function(data) {
                data.forEach(item => {
                    $('#reg_mandor').append(
                        `<option value="${item.REG}">${item.REG} - ${item.NAMA}</option>`
                    );
                });
            }).fail(function(xhr) {
                console.error("Gagal ambil mandor:", xhr.responseText);
                alert('Gagal memuat data mandor');
            });
        }
    });


    // Handle form submission
    $('#form-komoditi-teh').on('submit', function(e) {
        e.preventDefault();

        // Validasi semua karyawan harus memiliki kode absen
        let allKaryawanHasAbsen = true;
        let emptyAbsenRows = [];
        
        $('tbody tr').each(function(index) {
            const absenValue = $(this).find('.absen-select').val();
            if (!absenValue) {
                allKaryawanHasAbsen = false;
                emptyAbsenRows.push(index + 1);
                $(this).css('background-color', '#ffdddd');
            } else {
                $(this).css('background-color', '');
            }
        });
        
        if (!allKaryawanHasAbsen) {
            alert('Silahkan isi absensi semua karyawan. Baris yang belum terisi: ' + emptyAbsenRows.join(', '));
            $('button[type="submit"]').prop('disabled', false).html('Simpan');
            return;
        }

        // Validasi jendangan untuk aktifitas tertentu
        let isValidJendangan = true;
        $('.select2-aktifitas').each(function() {
            const aktifitas = $(this).val();
            const row = $(this).closest('tr');
            
            if (['53501', '53502', '53503'].includes(aktifitas)) {
                const jendangan = row.find('.select2-jendangan').val();
                // Kolom Jendangan menjadi opsional
                // Tidak perlu validasi untuk kolom Jendangan
            }
        });

        if (!isValidJendangan) {
            $('button[type="submit"]').prop('disabled', false).html('Simpan');
            return;
        }

        // Validasi untuk FF (Block Master)
        let isValidFF = true;
        let emptyAktifitasRows = [];
        $('select.target-alokasi-select').each(function() {
            const row = $(this).closest('tr');
            const targetValue = $(this).val();

            if (targetValue && targetValue.includes('FF - Block Master')) {
                const aktifitas = row.find('select.select2-aktifitas, input.aktifitas-input').val();
                if (!aktifitas) {
                    isValidFF = false;
                    emptyAktifitasRows.push(row.index() + 1);
                    row.css('background-color', '#ffdddd');
                } else {
                    row.css('background-color', '');
                }
            }
        });

        if (!isValidFF) {
            alert('Harap isi Aktifitas pada baris ' + emptyAktifitasRows.join(', '));
            $('button[type="submit"]').prop('disabled', false).html('Simpan');
            return;
        }

        // Validasi untuk CC (Cost Center)
        let isValidCC = true;
        let emptyCCRows = [];
        $('select.target-alokasi-select').each(function() {
            const row = $(this).closest('tr');
            const targetValue = $(this).val();

            if (targetValue && targetValue.includes('CC - Cost Center')) {
                const lokasi = row.find('select.select2-costcenter, input.lokasi-input').val();
                if (!lokasi) {
                    isValidCC = false;
                    emptyCCRows.push(row.index() + 1);
                    row.css('background-color', '#ffdddd');
                } else {
                    row.css('background-color', '');
                }
            }
        });

        if (!isValidCC) {
            alert('Harap isi Location Code/CC pada baris ' + emptyCCRows.join(', '));
            $('button[type="submit"]').prop('disabled', false).html('Simpan');
            return;
        }

        // Validasi untuk Target Alokasi
        let isValidTargetAlokasi = true;
        let emptyTargetAlokasiRows = [];
        $('select.target-alokasi-select').each(function() {
            const row = $(this).closest('tr');
            const targetValue = $(this).val();

            if (!targetValue) {
                isValidTargetAlokasi = false;
                emptyTargetAlokasiRows.push(row.index() + 1);
                row.css('background-color', '#ffdddd');
            } else {
                row.css('background-color', '');
            }
        });

        if (!isValidTargetAlokasi) {
            alert('Harap pilih Target Alokasi pada baris ' + emptyTargetAlokasiRows.join(', '));
            $('button[type="submit"]').prop('disabled', false).html('Simpan');
            return;
        }

        // Kumpulkan data karyawan
        var karyawanData = [];
        $('tbody tr').each(function(index) {
            const row = $(this);
            const absenValue = row.find('.absen-select').val();

            if (absenValue) {
                const aktifitasSelect = row.find('select[name^="data[' + index + '][aktifitas]"]');
                const aktifitasValue = aktifitasSelect.length ? aktifitasSelect.val() : row.find('input[name^="data[' + index + '][aktifitas]"]').val();
                
                // Tambahkan console.log untuk debug
                console.log('Aktifitas Select:', aktifitasSelect);
                console.log('Aktifitas Value:', aktifitasValue);
                
                const data = {
                    register: row.find('td:eq(1)').text().trim().substring(0, 50),
                    mandor: row.find('td:eq(2)').text().trim().substring(0, 50),
                    Referensi: row.find('td:eq(3)').text().trim().substring(0, 50),
                    Keterangan: row.find('td:eq(4)').text().trim().substring(0, 500),
                    Afdeling: row.find('td:eq(5)').text().trim().substring(0, 50),
                    Kehadiran: absenValue.substring(0, 50),
                    TargetAlokasiBiaya: (row.find('.target-alokasi-select').val() || '').split(' - ')[0].substring(0, 50),
                    LocationCode: row.find('select[name^="data[' + index + '][lokasi]"], input[name^="data[' + index + '][lokasi]"]').val() ? row.find('select[name^="data[' + index + '][lokasi]"], input[name^="data[' + index + '][lokasi]"]').val().split(' - ')[0].substring(0, 50) : '',
                    thntnm: (row.find('input[name^="data[' + index + '][thntnm]"]').val() || '').substring(0, 50),
                    Aktifitas: aktifitasValue ? aktifitasValue.substring(0, 50) : '',
                    Luasan: parseFloat(row.find('input[name^="data[' + index + '][jelajahHA]"]').val()) || 0,
                    kgpikul: parseFloat(row.find('input[name^="data[' + index + '][jmlkg]"]').val()) || 0,
                    stpikul: (row.find('input[name^="data[' + index + '][stpikul]"]').val() || '').substring(0, 1),
                    grup: (row.find('select[name^="data[' + index + '][ms]"]').val() || '').substring(0, 50),
                    Jendangan: (row.find('select[name^="data[' + index + '][jendangan]"]').val() || '').substring(0, 50),
                    Tanggal: $('#tanggal').val(),
                    plant: '{{ session("selected_kebun") }}'.substring(0, 50)
                };

                // Debug log untuk memeriksa data
                console.log('Data lengkap:', data);
                console.log('Nilai aktifitas:', aktifitasValue);
                console.log('Element aktifitas:', aktifitasSelect);

                karyawanData.push(data);
            }
        });

        if (karyawanData.length === 0) {
            alert('Silakan isi kode absen untuk minimal 1 karyawan');
            $('button[type="submit"]').prop('disabled', false).html('Simpan');
            return;
        }

        // Kirim data ke server
        $.ajax({
            url: '{{ route("checkroll.simpan-absensi") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tanggal: $('#tanggal').val(),
                kd_afd: $('#kd_afd').val(),
                reg_mandor: $('#reg_mandor').val(),
                data: JSON.stringify(karyawanData)
            },
            success: function(response) {
                alert(response.message);
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                let errorMsg = 'Terjadi kesalahan: ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += xhr.responseJSON.message;
                } else {
                    errorMsg += xhr.statusText;
                }
                alert(errorMsg);
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('Simpan');
            }
        });
    });


    // Event handler untuk perubahan aktifitas - DIPERBAIKI
    function handleAktifitasChange(selectElement) {
        const aktifitas = $(selectElement).val();
        const row = $(selectElement).closest('tr');
        const kodeAfdFull = row.find('td:eq(5)').text().trim();
        const kodeAfd = kodeAfdFull.replace(/\D/g, '');
        const selectedKebun = '<?php echo session("selected_kebun") ?>';
        const jendanganSelect = row.find('.select2-jendangan');
        const mesinSelect = row.find('.select2-mesin-petik');
        const aktifitasJendangan = ['53501', '53502', '53503'];
        const targetValue = row.find('.target-alokasi-select').val();

        if (!targetValue.includes('FF - Block Master')) {
            mesinSelect.val('').trigger('change').prop('disabled', true);
            jendanganSelect.val('').trigger('change').prop('disabled', true);
            return;
        }
        
        // Reset nilai ketika aktifitas berubah
        if (aktifitas !== '53503') {
            mesinSelect.val('').trigger('change');
        }
        
        if (!['53501', '53502', '53503'].includes(aktifitas)) {
            jendanganSelect.val('').trigger('change');
        }

        if (aktifitas === '53503') {
            const kodeAfdFull = row.find('td:eq(5)').text().trim();
            const kodeAfd = kodeAfdFull.replace(/\D/g, '');
            const selectedKebun = '<?php echo session("selected_kebun") ?>';
            
            loadMesinPetik(row, selectedKebun, kodeAfd);
            mesinSelect.prop('disabled', false);
        } else {
            mesinSelect.prop('disabled', true);
        }

        // Logika untuk jendangan
        if (['53501', '53502', '53503'].includes(aktifitas)) {
            jendanganSelect.prop('disabled', false);
        } else {
            jendanganSelect.prop('disabled', true);
        }

        if (aktifitasJendangan.includes(aktifitas)) {
            // Aktifkan dropdown jendangan
            jendanganSelect.prop('disabled', false);
            
            // Isi opsi jendangan jika belum ada
            if (jendanganSelect.find('option').length <= 1) {
                jendanganSelect.empty()
                    .append('<option value="">Pilih Jendangan</option>')
                    .append('<option value="JD1">JD1</option>')
                    .append('<option value="JD2">JD2</option>')
                    .append('<option value="JD3">JD3</option>');
                
                // Inisialisasi Select2 jika belum
                if (!jendanganSelect.hasClass('select2-hidden-accessible')) {
                    jendanganSelect.select2({
                        placeholder: "Pilih Jendangan",
                        allowClear: false,
                        width: '100%'
                    });
                }
            }
        } else {
            // Nonaktifkan dan kosongkan jika bukan aktifitas yang ditentukan
            jendanganSelect.val('').prop('disabled', true);
        }

        console.log('Kode Afd (full):', kodeAfdFull, 'Kode Afd (clean):', kodeAfd);
        
        if (aktifitas === '53503') {
            loadMesinPetik(row, selectedKebun, kodeAfd);
            row.find('.select2-mesin-petik').prop('disabled', false);
        } else {
            row.find('.select2-mesin-petik').val('').prop('disabled', true);
        }
    }

    $(document).ready(function() {
        // Inisialisasi Select2 untuk target alokasi
        $('.target-alokasi-select').select2({
            placeholder: "Pilih Target Alokasi",
            allowClear: false,
            width: '100%'
        });

        // Inisialisasi Select2 untuk mesin petik
        $('.select2-mesin-petik').select2({
            placeholder: "Pilih Mesin Petik",
            allowClear: false,
            width: '100%'
        }).prop('disabled', true); // Pastikan awal dalam keadaan disabled

        // Pasang event handler untuk perubahan aktifitas
        $(document).on('change', '.select2-aktifitas, .aktifitas-input', function() {
            handleAktifitasChange(this);
        });

        // Periksa aktifitas yang sudah dipilih saat load halaman
        $('.select2-aktifitas').each(function() {
            if ($(this).val() === '53503') {
                const row = $(this).closest('tr');
                const kodeAfd = row.find('td:eq(5)').text().trim();
                const selectedKebun = '<?php echo session("selected_kebun") ?>';
                loadMesinPetik(row, selectedKebun, kodeAfd);
                row.find('.select2-mesin-petik').prop('disabled', false);
            }
        });
    });

    // Fungsi untuk memformat tampilan dropdown Blok SAP
    function formatBlokSAP(blok) {
        if (!blok.id) return blok.text;

        const parts = blok.text.split(' - ');
        if (parts.length < 2) return blok.text;

        return $('<span>').append(
            $('<b>').text(parts[0] + ' - '),
            parts[1].length > 30 ? parts[1].substring(0, 30) + '...' : parts[1]
        );

    }

    function formatBlokSAPSelection(blok) {
        if (!blok.id) return blok.text;

        const parts = blok.text.split(' - ');
        if (parts.length < 2) return blok.text;

        return parts[0] + ' - ' + parts[1];
    }

    // Format functions for Cost Center dropdown
    function formatCostCenter(center) {
        if (!center.id) return center.text;

        const parts = center.text.split(' - ');
        if (parts.length < 2) return center.text;

        return $('<span>').append(
            $('<b>').text(parts[0] + ' - '),
            parts[1].length > 30 ? parts[1].substring(0, 30) + '...' : parts[1]
        );
    }

    function formatCostCenterSelection(center) {
        if (!center.id) return center.text;

        const parts = center.text.split(' - ');
        if (parts.length < 2) return center.text;

        return parts[0] + ' - ' + parts[1];
    }

    function resetToTextInput(row) {
        const selectCostCenter = row.find('select.select2-costcenter');
        if (selectCostCenter.length) {
            const inputHtml = `<input type="text" name="${selectCostCenter.attr('name')}" class="form-control lokasi-input" value="${selectCostCenter.val()}">`;
            selectCostCenter.replaceWith(inputHtml);
        }
        const selectBlokSap = row.find('select.select2-bloksap');
        if (selectBlokSap.length) {
            const inputHtml = `<input type="text" name="${selectBlokSap.attr('name')}" class="form-control lokasi-input" value="${selectBlokSap.val()}">`;
            selectBlokSap.replaceWith(inputHtml);
        }
        // Reset tahun tanam
        row.find('input[name*="[thntnm]"]').val('');
    }

    // Fungsi untuk menghancurkan Select2 sebelum membuat yang baru
    function destroySelect2IfExists(row) {
        const select = row.find('.select2-hidden-accessible');
        if (select.length) {
            select.each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
        }
        row.find('.select2-container').remove();
    }

    function loadMesinPetik(row, kodeUnit, kodeAfd) {
    const mesinSelect = row.find('.select2-mesin-petik');
    
    // Validasi parameter
    if (!kodeUnit || !kodeAfd) {
        console.error('Parameter tidak valid:', {kodeUnit, kodeAfd});
        return;
    }

    // Pastikan kodeAfd hanya angka
    kodeAfd = kodeAfd.toString().replace(/\D/g, '');
    
    console.log('Memuat mesin petik dengan parameter:', {
        kodeUnit: kodeUnit,
        kodeAfd: kodeAfd
    });

    // Set loading state
    mesinSelect.prop('disabled', true).empty().append('<option value="">Memuat data...</option>');

    $.ajax({
        url: '{{ route("get.mesin.petik") }}',
        method: 'GET',
        data: {
            kode_unit: kodeUnit,
            kode_afd: kodeAfd // Pastikan hanya angka
        },
        success: function(data) {
            console.log('Response data:', data);
            
            mesinSelect.empty();
            
            if (data && data.length > 0) {
                mesinSelect.append('<option value="">Pilih Mesin Petik</option>');
                
                data.forEach(item => {
                    mesinSelect.append(new Option(
                        `${item.KodeMesin} - ${item.NamaAfd}`,
                        item.KodeMesin
                    ));
                });
                
                mesinSelect.prop('disabled', false);
                console.log('Data berhasil dimuat:', data.length, 'item');
            } else {
                mesinSelect.append('<option value="">Data tidak ditemukan</option>');
                console.warn('Data kosong untuk parameter:', {kodeUnit, kodeAfd});
            }
            
            // Re-init Select2
            mesinSelect.select2({
                placeholder: "Pilih Mesin Petik",
                allowClear: false,
                width: '100%'
            });
        },
        error: function(xhr) {
            console.error('Gagal memuat data:', xhr.responseText);
            mesinSelect.empty().append('<option value="">Gagal memuat data</option>');
        }
    });
}


    // Fungsi untuk mengubah input menjadi dropdown
    function initDropdown(row, type, currentValue) {
        destroySelect2IfExists(row);
        resetToTextInput(row);

        const inputName = row.find('.lokasi-input').attr('name');
        const tahunTanamInput = row.find('input[name*="[thntnm]"]');
        const endpoint = type === 'bloksap' ? '/checkroll/get-bloksap' : '/checkroll/get-costcenter';
        const className = `select2-${type}`;
        const placeholder = type === 'bloksap' ? 'Pilih Blok SAP...' : 'Pilih Cost Center...';

        // Buat elemen select baru
        const selectHtml = `
            <select name="${inputName}" class="form-control ${className}">
                <option value=""></option>
            </select>
        `;

        // Ganti input dengan select
        row.find('.lokasi-input').replaceWith(selectHtml);

        // Load data via AJAX
        $.get(endpoint, function(data) {
            const selectElement = row.find(`.${className}`);

            selectElement.empty().append('<option value=""></option>');

            data.forEach(item => {
                const displayText = type === 'bloksap'
                    ? `${item.Blok_SAP} - ${item.Uraian}`
                    : `${item.CostCenter} - ${item.Uraian}`;

                const value = type === 'bloksap' ? item.Blok_SAP : item.CostCenter;

                selectElement.append(new Option(
                    displayText,
                    value,
                    false,
                    currentValue == value
                ));
            });
            // Konfigurasi Select2 yang sama untuk CC dan FF
            selectElement.select2({
                placeholder,
                allowClear: false,
                width: '100%',
                dropdownAutoWidth: true,
                templateResult: type === 'bloksap' ? formatBlokSAP : formatCostCenter,
                templateSelection: type === 'bloksap' ? formatBlokSAPSelection : formatCostCenterSelection,
                escapeMarkup: function(m) {
                    return m;
                }
            });
            // Dalam inisialisasi awal, periksa apakah aktifitas sudah 53503
            $('.select2-aktifitas').each(function() {
                const aktifitas = $(this).val();
                const row = $(this).closest('tr');

                if (aktifitas === '53503') {
                    const kodeAfd = row.find('td:eq(5)').text().trim();
                    const selectedKebun = '<?php echo session("selected_kebun") ?>';
                    loadMesinPetik(row, selectedKebun, kodeAfd);
                    row.find('.select2-mesin-petik').prop('disabled', false);
                }
            });

                if (type === 'bloksap') {
                    selectElement.off('change').on('change', function() {
                        const selectedItem = data.find(b => b.Blok_SAP == $(this).val());
                        tahunTanamInput.val(selectedItem ? selectedItem.ThnTnm : '');
                    });
                } else {
                    tahunTanamInput.val('');
                }
                // Paksa tampilkan nilai yang dipilih jika ada
                if (currentValue) {
                    selectElement.val(currentValue).trigger('change');
                }

        }).fail(function(xhr) {
            console.error(`Gagal memuat ${type}:`, xhr.responseText);
            alert(`Gagal memuat data ${type === 'bloksap' ? 'Blok SAP' : 'Cost Center'}`);
        });

        // Tambahkan dalam fungsi initAktifitasDropdown
        row.find(`.${className}`).off('change').on('change', function() {
            const aktifitas = $(this).val();
            console.log('Aktifitas berubah:', aktifitas);
            const row = $(this).closest('tr');
            const kodeAfd = row.find('td:eq(5)').text().trim(); // Ambil kode afd dari kolom Afd1
            console.log('Kode Afd:', kodeAfd); // Debug 2

            if (aktifitas === '53503') {
                const selectedKebun = '<?php echo session("selected_kebun") ?>';
                console.log('Selected Kebun:', selectedKebun); // Debug 3
                loadMesinPetik(row, selectedKebun, kodeAfd);
                row.find('.select2-mesin-petik').prop('disabled', false);
            } else {
                row.find('.select2-mesin-petik').val('').prop('disabled', true);
            }
        });
    }

    function initAktifitasDropdown(row, currentValue = '') {
        const inputName = row.find('.aktifitas-input').attr('name');

        const selectHtml = `<select name="${inputName}" class="form-control select2-aktifitas">
                <option value=""></option>
            </select>
        `;

        row.find('.aktifitas-input').replaceWith(selectHtml);

        $.get('/checkroll/get-aktifitas-th', function(data) {
            const selectElement = row.find('.select2-aktifitas');

            data.forEach(item => {
                selectElement.append(new Option(
                    `${item.Aktifitas} - ${item.Uraian}`,
                    item.Aktifitas,
                    false,
                    currentValue == item.Aktifitas
                ));
            });

            selectElement.select2({
                placeholder: "Pilih Aktifitas...",
                allowClear: false,
                width: '100%',
                dropdownAutoWidth: true
            });
        }).fail(function(xhr) {
            console.error("Gagal memuat aktifitas:", xhr.responseText);
            alert('Gagal memuat data aktifitas');
        });
    }

    function toggleInputFields(row, isFF) {
        const fields = [
            '.jelajah-input',
            '.satuan-input',
            '.panen-input',
            '.kg-input',
            '.sts-input',
            '.amb-input'
        ];

        fields.forEach(selector => {
            const input = row.find(selector);
            if (isFF) {
                input.prop('readonly', false);
            } else {
                input.prop('readonly', true);
                input.val(''); // Kosongkan nilai jika bukan FF
            }
        });
    }

    function resetAktifitasToInput(row) {
        const select = row.find('select.select2-aktifitas');
        const currentInput = row.find('.aktifitas-input');

        // Hancurkan Select2 jika ada
        if (select.length && select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
        }

        // Hapus elemen Select2 container jika ada
        row.find('.select2-container').remove();

        // Buat input baru yang readonly
        const inputName = select.length ? select.attr('name') : 'aktifitas[]';
        const inputHtml = `<input type="text" name="${inputName}" class="form-control aktifitas-input" value="" readonly>`;

        // Ganti select dengan input atau update input yang ada
        if (select.length) {
            select.replaceWith(inputHtml);
        } else if (currentInput.length) {
            currentInput.replaceWith(inputHtml);
        } else {
            row.find('.col-aktifitas').append(inputHtml);
        }
    }


    // Handle perubahan Target Alokasi
    $(document).on('change', '.target-alokasi-select', function() {
        const row = $(this).closest('tr');
        const targetValue = $(this).val();

        if (!targetValue.includes('FF - Block Master')) {
        row.find('.select2-mesin-petik').val('').trigger('change').prop('disabled', true);
        row.find('.select2-jendangan').val('').trigger('change').prop('disabled', true);
    }

    let currentValue = row.find('.lokasi-input').val();
    const currentAktifitas = row.find('.aktifitas-input').val();
    // Jika FF, pastikan hanya kode Blok SAP saja yang dikirim ke initDropdown
    let valueToSet = currentValue;
    if (targetValue && targetValue.includes('FF - Block Master') && currentValue && currentValue.includes(' - ')) {
        valueToSet = currentValue.split(' - ')[0].trim();
    }

        destroySelect2IfExists(row);
        resetAktifitasToInput(row);

        if (targetValue && targetValue.includes('CC - Cost Center')) {
            initDropdown(row, 'costcenter', valueToSet);
            resetAktifitasToInput(row);
            toggleInputFields(row, false);
        } else if (targetValue && targetValue.includes('FF - Block Master')) {
            initDropdown(row, 'bloksap', valueToSet);
            initAktifitasDropdown(row, currentAktifitas);
            toggleInputFields(row, true);
        }
    });

    // Inisialisasi saat load halaman
    $(document).ready(function() {

        $('.target-alokasi-select').select2({
            placeholder: "Pilih Target Alokasi",
            allowClear: false,
            width: '100%'
        });

        // Tambahkan dalam document.ready
        $('.select2-mesin-petik').select2({
            placeholder: "Pilih Mesin Petik",
            allowClear: false,
            width: '100%',
            dropdownAutoWidth: true
        });
        
        $('.select2-jendangan').select2({
        placeholder: "Pilih Jendangan",
        allowClear: false,
        width: '100%'
        }).prop('disabled', true);

        // Tambahkan ini di document.ready
     $(document).on('change', '.select2-aktifitas, .aktifitas-input', function() {
        handleAktifitasChange(this);
    const row = $(this).closest('tr');
    const kodeAfd = row.find('td:eq(5)').text().trim(); // Ambil kode afd dari kolom ke-6
    const selectedKebun = '<?php echo session("selected_kebun") ?>';
    const aktifitas = $(this).val();

    console.log('Kode Afd:', kodeAfd, 'Kebun:', selectedKebun); // Debug

    if (aktifitas === '53503') {
        // Hapus Select2 sebelum inisialisasi ulang
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
        
        loadMesinPetik(row, selectedKebun, kodeAfd);
        row.find('.select2-mesin-petik').prop('disabled', false);
    } else {
        row.find('.select2-mesin-petik').val('').prop('disabled', true);
    }
});

        $('.target-alokasi-select').each(function() {
            const row = $(this).closest('tr');
            const targetValue = $(this).val();
            let currentValue = row.find('.lokasi-input').val();
            let valueToSet = currentValue;
            if (targetValue && targetValue.includes('FF - Block Master') && currentValue && currentValue.includes(' - ')) {
                valueToSet = currentValue.split(' - ')[0].trim();
            }
            const currentAktifitas = row.find('.aktifitas-input').val();

            if (targetValue && targetValue.includes('CC - Cost Center')) {
                initDropdown(row, 'costcenter', valueToSet);
                resetAktifitasToInput(row);
                toggleInputFields(row, false);
            } else if (targetValue && targetValue.includes('FF - Block Master')) {
                initDropdown(row, 'bloksap', valueToSet);
                if (currentAktifitas) {
                    initAktifitasDropdown(row, currentAktifitas);
                }
                toggleInputFields(row, true);
                initAktifitasDropdown(row, currentAktifitas);
            }
        });

 // Periksa aktifitas yang sudah dipilih saat load halaman
 $('.select2-aktifitas').each(function() {
        const aktifitas = $(this).val();
        if (aktifitas === '53503') {
            const row = $(this).closest('tr');
            const kodeAfd = row.find('td:eq(5)').text().trim();
            const selectedKebun = '<?php echo session("selected_kebun") ?>';
            loadMesinPetik(row, selectedKebun, kodeAfd);
            row.find('.select2-mesin-petik').prop('disabled', false);
        }
    });

    });

// Fungsi untuk mengatur warna input berdasarkan kondisi
function updateInputColors() {
    $('tbody tr').each(function() {
        const row = $(this);
        const targetValue = row.find('.target-alokasi-select').val();
        const aktifitas = row.find('.select2-aktifitas').val();
        
        // Reset semua input ke readonly (merah)
        row.find('input[type="text"]').addClass('readonly').removeClass('editable');
        
        if (targetValue && targetValue.includes('FF - Block Master')) {
            // Untuk FF - Block Master, semua input bisa diisi (hijau)
            row.find('.jelajah-input, .satuan-input, .panen-input, .kg-input, .sts-input, .amb-input')
               .removeClass('readonly')
               .addClass('editable');
            
            // Khusus untuk aktifitas 53503, mesin petik bisa diisi
            if (aktifitas === '53503') {
                row.find('.select2-mesin-petik').closest('.select2-container')
                   .removeClass('readonly')
                   .addClass('editable');
            }
            
            // Untuk aktifitas 53501, 53502, 53503, jendangan bisa diisi
            if (['53501', '53502', '53503'].includes(aktifitas)) {
                row.find('.select2-jendangan').closest('.select2-container')
                   .removeClass('readonly')
                   .addClass('editable');
            }
        }
    });
}

// Panggil fungsi saat halaman dimuat
$(document).ready(function() {
    updateInputColors();
    
    // Panggil fungsi saat ada perubahan pada target alokasi atau aktifitas
    $(document).on('change', '.target-alokasi-select, .select2-aktifitas', function() {
        updateInputColors();
    });
});
</script>
@endpush