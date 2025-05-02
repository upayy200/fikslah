@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <h4>Absensi Komoditi Teh</h4>

        <form id="form-komoditi-teh">
            @csrf

            @include('layouts.alert')
            {{-- Tanggal --}}
            <div class="form-group mb-3">
                <label for="tanggal">Tanggal</label>
                <input type="text" class="form-control" name="tanggal" id="tanggal"
                    value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
            </div>

            {{-- Dropdown Afdeling/Bagian --}}
            <div class="form-group mb-3">
                <label for="kd_afd">Afdeling/Bagian</label>
                <select name="kd_afd" id="kd_afd" class="form-control select2-afdeling">
                    <option value="">-- Pilih Afdeling/Bagian --</option>
                    @foreach ($afdBagian as $item)
                        <option value="{{ $item->KodeAfdeling }}">{{ $item->KodeAfdeling }} - {{ $item->NamaAfdeling }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Mandor --}}
            <div class="form-group mb-4">
                <label for="reg_mandor">Reg. Mandor</label>
                <select name="reg_mandor" id="reg_mandor" class="form-control select2-mandor">
                    <option value="">-- Pilih Mandor --</option>
                </select>
            </div>
            <div class="d-flex justify-content-end">
                <a href="javascript:;" onclick="test()" class="btn btn-primary">READ</a>
            </div>

            {{-- Tabel Detail Karyawan --}}
            @php
                $karyawan = session()->get('karyawan');
                $absen = session()->get('absen');
                $target_alokasi = session()->get('target_alokasi');
            @endphp
            
            @if(isset($karyawan) && count($karyawan) > 0)
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-bordered table-striped" id="tabel-karyawan">
                    <thead class="table-success text-center">
                        <tr>
                <th style="min-width: 50px;">No</th>
                <th style="min-width: 120px;">Register</th>
                <th style="min-width: 100px;">Reg.SAP</th>
                <th style="min-width: 120px;">Nama</th>
                <th style="min-width: 200px;">Jabatan</th>
                <th style="min-width: 80px;">Afd1</th>
                <th style="min-width: 100px;">Kode Absen</th> {{-- AMCO_KodeAbsen --}}
                <th style="min-width: 180px;">Target Alokasi</th> 
                <th style="min-width: 120px;">Location Code/CC</th>  {{--FF AMCO_BlokSAP --}} {{--CC AMCO_CostCenter --}}
                <th style="min-width: 120px;">Tahun Tanam</th>     {{--FF AMCO_BlokSAP --}} 
                <th style="min-width: 120px;">Aktifitas</th>        {{--FF AMCO_Aktifitas (SEMUA SAMA) (KC=TH)--}}
                <th style="min-width: 150px;">Luas Jelajah (Ha)</th> {{-- FF entri biasa --}}
                <th style="min-width: 100px;">Satuan</th> {{-- FF entri biasa --}}
                <th style="min-width: 120px;">Hasil Panen</th>{{-- FF entri biasa --}}
                <th style="min-width: 100px;">Kg Pikul</th>{{-- FF entri biasa --}}  {{-- AMCO_ArealPikul --}}
                <th style="min-width: 100px;">Sts Pikul</th>{{-- FF entri biasa --}}    {{-- AMCO_ArealPikul --}}
                <th style="min-width: 100px;">AMB (%)</th>{{-- FF entri biasa --}}
                <th style="min-width: 100px;">Mesin Petik</th> {{-- FF [Ref_MesinPetik]   53503 --}}
                <th style="min-width: 100px;">Jendangan</th>    {{-- AKTIFITAS 53501,53502,53503 = JD1,JD2,JD3 (teks biasa) --}}
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
                                    <select name="data[{{ $index }}][absen]" class="form-control absen-select" 
                        onchange="handleAbsenChange(this)">
                    <option value="">Pilih</option>
                    @foreach ($absen as $item2)
                        <option value="{{ $item2->KodeAbsen }}" @selected(old('data.'.$index.'.absen') == $item2->KodeAbsen)>
                            {{ $item2->Uraian }}
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
                        <select name="data[{{ $index }}][lokasi]" 
                                class="form-control select2-costcenter" 
                                data-placeholder="Pilih Cost Center..."
                                style="width: 100%">
                            <option value=""></option>
                            <!-- Options akan diisi via JavaScript -->
                        </select>
                    @else
                        <input type="" name="data[{{ $index }}][lokasi]" 
                               class="form-control lokasi-input" 
                               value="{{ old('data.'.$index.'.lokasi', $item->lokasi ?? '') }}"
                               style="width: 100%">
                    @endif
                    
                </td>
                <td><input type="text" name="data[{{ $index }}][thntnm]" class="form-control" value="{{ old('data.'.$index.'.thntnm') }}" readonly></td>

                <td class="aktifitas-column">
                    @if(isset($item->target_alokasi) && str_contains($item->target_alokasi, 'FF - Block Master'))
                        <select name="data[{{ $index }}][aktifitas]" class="form-control select2-aktifitas">
                            <option value=""></option>
                            <!-- Options akan diisi via JavaScript -->
                        </select>
                    @else
                        <input type="text" name="data[{{ $index }}][aktifitas]" 
                               class="form-control aktifitas-input" 
                               value="{{ old('data.'.$index.'.aktifitas', $item->aktifitas ?? '') }}">
                    @endif
                </td>
                  
                <td>
                    <input type="text" name="data[{{ $index }}][jelajahHA]" class="form-control jelajah-input" 
                           value="{{ old('data.'.$index.'.jelajahHA') }}" readonly>
                </td>
                <td>
                    <input type="text" name="data[{{ $index }}][satuan]" class="form-control satuan-input" 
                           value="{{ old('data.'.$index.'.satuan') }}" readonly>
                </td>
                <td>
                    <input type="text" name="data[{{ $index }}][hslpanen]" class="form-control panen-input" 
                           value="{{ old('data.'.$index.'.hslpanen') }}" readonly>
                </td>
                <td>
                    <input type="text" name="data[{{ $index }}][jmlkg]" class="form-control kg-input" 
                           value="{{ old('data.'.$index.'.jmlkg') }}" readonly>
                </td>
                <td>
                    <input type="text" name="data[{{ $index }}][stpikul]" class="form-control sts-input" 
                           value="{{ old('data.'.$index.'.stpikul') }}" readonly>
                </td>
                <td>
                    <input type="text" name="data[{{ $index }}][pct]" class="form-control amb-input" 
                           value="{{ old('data.'.$index.'.pct') }}" readonly>
                </td>

                <td><input type="text" name="data[{{ $index }}][ms]" class="form-control" value="{{ old('data.'.$index.'.ms') }}" readonly></td>
                <td><input type="text" name="data[{{ $index }}][jendangan]" class="form-control" value="{{ old('data.'.$index.'.jendangan') }}" readonly></td>
                </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success mt-3">Simpan</button>
            @endif
        </form>
    </div>
@endsection

@push('styles')
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
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function test() {
            var kd_afd = document.getElementById("kd_afd").value;
            var reg_mandor = document.getElementById("reg_mandor").value;
            if (!kd_afd || !reg_mandor) {
                alert('Silakan pilih Afdeling dan Mandor terlebih dahulu');
                return;
            }

            window.location.href = '/checkroll/komoditi_teh?kd=' + kd_afd + '&reg=' + reg_mandor;
        }

        function handleAbsenChange(selectElement) {
    const row = $(selectElement).closest('tr');
    const absenValue = $(selectElement).val();
    const targetSelect = row.find('.target-alokasi-select');
    

    }

$(document).ready(function() {
    $('.target-alokasi-select').select2({
        placeholder: "Pilih Target Alokasi",
        allowClear: false,
        width: '100%'
    });


    $('.absen-select').each(function() {
        handleAbsenChange(this); // Set status awal
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
       
    // Cek apakah ada data karyawan yang ditampilkan
    if ($('#tabel-karyawan tbody tr').length === 0) {
        alert('Tidak ada data karyawan yang ditampilkan');
        return;
    }

    // Show loading indicator
    $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    
    // Validasi minimal
    const kd_afd = $('#kd_afd').val();
    const reg_mandor = $('#reg_mandor').val();
    
    if (!kd_afd || !reg_mandor) {
        alert('Silakan pilih Afdeling dan Mandor terlebih dahulu');
        $('button[type="submit"]').prop('disabled', false).html('Simpan');
        return;
    }

    // Validasi untuk FF (Block Master)
    let isValid = true;
    $('select.target-alokasi-select').each(function() {
        const row = $(this).closest('tr');
        const targetValue = $(this).val();
        
        if (targetValue && targetValue.includes('FF - Block Master')) {
            const lokasi = row.find('select.select2-bloksap, input.lokasi-input').val();
            const aktifitas = row.find('select.select2-aktifitas, input.aktifitas-input').val();
            const jelajah = row.find('.jelajah-input').val();
            const satuan = row.find('.satuan-input').val();
            const panen = row.find('.panen-input').val();
            
            if (!lokasi || !aktifitas || !jelajah || !satuan || !panen) {
                isValid = false;
                row.css('background-color', '#ffdddd'); // Highlight row error
                alert('Harap lengkapi semua field yang diperlukan untuk FF - Block Master pada baris ' + (row.index() + 1));
                return false; // Keluar dari each loop
            } else {
                row.css('background-color', ''); // Reset highlight
            }
        }
    });

    if (!isValid) {
        $('button[type="submit"]').prop('disabled', false).html('Simpan');
        return;
    }

    // Jika semua validasi lolos, lanjutkan submit
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            // Handle success response
            alert('Data berhasil disimpan');
            window.location.reload();
        },
        error: function(xhr) {
            // Handle error
            alert('Terjadi kesalahan: ' + xhr.responseText);
        },
        complete: function() {
            $('button[type="submit"]').prop('disabled', false).html('Simpan');
        }
    });
});
    // Kumpulkan data karyawan yang memiliki input
var karyawanData = [];
$('#tabel-karyawan tbody tr').each(function(index) {
    var row = $(this);
    const reg = row.find('td:eq(1)').text().trim();
    const absen = row.find('select[name*="[absen]"]').val();
    const SAP_TargetAlokasi  = row.find('select[name*="[target_alokasi]"]').val();
    const kdblok = row.find('input[name*="[kdblok]"]').val();
    const thntnm = row.find('input[name*="[thntnm]"]').val();
    const jelajahHA = row.find('input[name*="[jelajahHA]"]').val();
    const satuan = row.find('input[name*="[satuan]"]').val();
    const hslpanen = row.find('input[name*="[hslpanen]"]').val();
    const jmlkg = row.find('input[name*="[jmlkg]"]').val();
    const stpikul = row.find('input[name*="[stpikul]"]').val();
    const pct = row.find('input[name*="[pct]"]').val();
    const ms = row.find('input[name*="[ms]"]').val();
    const jendangan = row.find('input[name*="[jendangan]"]').val();

    // Cek jika ada minimal satu field yang diisi (termasuk absen)
    if (absen || SAP_TargetAlokasi || kdblok || thntnm || jelajahHA || satuan || hslpanen || jmlkg || stpikul || pct || ms || jendangan) {
        karyawanData.push({
            reg: reg,
            absen: absen || null,
            SAP_TargetAlokasi: SAP_TargetAlokasi || null,
            kdblok: kdblok || null,
            thntnm: thntnm || null,
            jelajahHA: jelajahHA || null,
            satuan: satuan || null,
            hslpanen: hslpanen || null,
            jmlkg: jmlkg || null,
            stpikul: stpikul || null,
            pct: pct || null,
            ms: ms || null,
            jendangan: jendangan || null
        });
    }
});


    // Jika tidak ada data absen sama sekali
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
        data: JSON.stringify(karyawanData) // Ubah menjadi string JSON
    },
        success: function(response) {
            alert(response.message);
            if (response.success) {
                // Optional: reload page or clear form
                // location.reload();
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
    const select = row.find('select.select2-costcenter, select.select2-bloksap');
    if (select.length) {
        const inputHtml = `<input type="text" name="${select.attr('name')}" class="form-control lokasi-input" value="${select.val()}">`;
        select.replaceWith(inputHtml);
    }
    // Reset tahun tanam
    row.find('input[name*="[thntnm]"]').val('');
}

// Fungsi untuk menghancurkan Select2 sebelum membuat yang baru
function destroySelect2IfExists(row) {
    const select = row.find('select.select2-hidden-accessible');
    if (select.length) {
        select.each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
    }
    row.find('.select2-container').remove();
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
        const select = row.find(`.${className}`);
        
        select.empty().append('<option value=""></option>');
        
        data.forEach(item => {
            const displayText = type === 'bloksap' 
                ? `${item.NamaBlok} - ${item.Uraian}`
                : `${item.CostCenter} - ${item.Uraian}`;
                
            const value = type === 'bloksap' ? item.Blok_SAP : item.CostCenter;
            
            select.append(new Option(
                displayText, 
                value,
                false, 
                currentValue == value
            ));
        });
        // Konfigurasi Select2 yang sama untuk CC dan FF
        select.select2({
            placeholder,
            allowClear: false,
            width: '100%',
            dropdownAutoWidth: true,
            templateResult: type === 'bloksap' ? formatBlokSAP : formatCostCenter,
            templateSelection: type === 'bloksap' ? formatBlokSAPSelection : formatCostCenterSelection,
            escapeMarkup: m => m
        });

        if (type === 'bloksap') {
            select.off('change').on('change', function() {
                const selectedItem = data.find(b => b.Blok_SAP == $(this).val());
                tahunTanamInput.val(selectedItem ? selectedItem.ThnTnm : '');
            });
        } else {
            tahunTanamInput.val('');
        }
        
    }).fail(function(xhr) {
        console.error(`Gagal memuat ${type}:`, xhr.responseText);
        alert(`Gagal memuat data ${type === 'bloksap' ? 'Blok SAP' : 'Cost Center'}`);
    });
}

function initAktifitasDropdown(row, currentValue = '') {
    const inputName = row.find('.aktifitas-input').attr('name');
    
    const selectHtml = `
        <select name="${inputName}" class="form-control select2-aktifitas">
            <option value=""></option>
        </select>
    `;

    row.find('.aktifitas-input').replaceWith(selectHtml);

    $.get('/checkroll/get-aktifitas-th', function(data) {
        const select = row.find('.select2-aktifitas');
        
        data.forEach(item => {
            select.append(new Option(
                `${item.Aktifitas} - ${item.Uraian}`,
                item.Aktifitas,
                false,
                currentValue == item.Aktifitas
            ));
        });
        
        select.select2({
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
    const currentValue = row.find('.lokasi-input').val();
    const currentAktifitas = row.find('.aktifitas-input').val();
    resetAktifitasToInput(row);
    
    if (targetValue && targetValue.includes('CC - Cost Center')) {
        initDropdown(row, 'costcenter', currentValue);
        resetAktifitasToInput(row);
        toggleInputFields(row, false);
    } 
    else if (targetValue && targetValue.includes('FF - Block Master')) {
        initDropdown(row, 'bloksap', currentValue);
        initAktifitasDropdown(row, currentAktifitas);
        toggleInputFields(row, true);
    }
    else {
        destroySelect2IfExists(row);
        resetAktifitasToInput(row);
        row.find('input[name*="[thntnm]"]').val('');
        toggleInputFields(row, false);
    }
});

// Inisialisasi saat load halaman
$(document).ready(function() {
    $('.target-alokasi-select').each(function() {
        const row = $(this).closest('tr');
        const targetValue = $(this).val();
        const currentValue = row.find('.lokasi-input').val();
        const currentAktifitas = row.find('.aktifitas-input').val();
        
        if (targetValue && targetValue.includes('CC - Cost Center')) {
            initDropdown(row, 'costcenter', currentValue);
            resetAktifitasToInput(row);
            toggleInputFields(row, false);
        }
        else if (targetValue && targetValue.includes('FF - Block Master')) {
            initDropdown(row, 'bloksap', currentValue);
            if (currentAktifitas) {
                initAktifitasDropdown(row, currentAktifitas);
            }
            toggleInputFields(row, true);
            initAktifitasDropdown(row, currentAktifitas);
        }
    });
});


</script>

@endpush
