@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Absensi Komoditi Teh</h4>

    <form id="form-komoditi-teh">
        @csrf

        {{-- Tanggal --}}
        <div class="form-group mb-3">
            <label for="tanggal">Tanggal</label>
            <input type="text" class="form-control" name="tanggal" id="tanggal" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" readonly>
        </div>

        {{-- Dropdown Afdeling/Bagian --}}
        <div class="form-group mb-3">
            <label for="kd_afd">Afdeling/Bagian</label>
            <select name="kd_afd" id="kd_afd" class="form-control select2-afdeling">
                <option value="">-- Pilih Afdeling/Bagian --</option>
                @foreach($afdBagian as $item)
                <option value="{{ $item->KodeAfdeling }}">{{ $item->KodeAfdeling }} - {{ $item->NamaAfdeling }}</option>
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

        {{-- Tabel Detail Karyawan --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tabel-karyawan">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Register</th>
                        <th>Reg.SAP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Afd1</th>
                        <th>Kode Absen</th>
                        <th>Target Alokasi</th>
                        <th>Location Code</th>
                        <th>Tahun Tanam</th>
                        <th>Luas Jelajah(Ha)</th>
                        <th>Satuan Luas</th>
                        <th>Hasil Panen (TEH)</th>
                        <th>Kg Pikul</th>
                        <th>Sts Pikul</th>
                        <th>AMBB (%)</th>
                        <th>Mesin Petik</th>
                        <th>Jendangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="18" class="text-center">Silakan pilih Afdeling dan Mandor</td></tr>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-success mt-3">Simpan</button>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.select2-afdeling').select2({ placeholder: "-- Pilih Afdeling/Bagian --", allowClear: true });
    $('.select2-mandor').select2({ placeholder: "-- Pilih Mandor --", allowClear: true });

    $('#kd_afd').on('change', function () {
        let kd_afd = $(this).val();
        $('#reg_mandor').html('<option value="">-- Pilih Mandor --</option>');
        if (kd_afd) {
            $.post('{{ url("checkroll/get-mandor-by-afd") }}', { kd_afd: kd_afd }, function (data) {
                data.forEach(item => {
                    $('#reg_mandor').append(`<option value="${item.REG}">${item.REG} - ${item.NAMA}</option>`);
                });
            }).fail(function(xhr) {
                console.error("Gagal ambil mandor:", xhr.responseText);
            });
        }
        $('#tabel-karyawan tbody').html('<tr><td colspan="18" class="text-center">Silakan pilih Mandor</td></tr>');
    });

    $('#reg_mandor').on('change', function () {
    let reg_mandor = $(this).val();
    let kd_afd = $('#kd_afd').val();
    let tanggal = $('#tanggal').val();

    if (reg_mandor && kd_afd) {
        $.post('{{ url("checkroll/get-karyawan-by-mandor") }}', { 
            reg_mandor: reg_mandor, 
            kd_afd: kd_afd,
            tanggal: tanggal
        }, function (response) {
            let rows = '';
            if (response.success && response.data.length > 0) {
                response.data.forEach((item, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><input type="hidden" name="data[${index}][reg]" value="${item.REG}">${item.REG}</td>
                            <td>${item.REG_SAP}</td>
                            <td>${item.NAMA}</td>
                            <td>${item.NAMA_JAB}</td>
                            <td>${item.KD_AFD}</td>
                            <td>
                                <select name="data[${index}][absen]" class="form-control">
    <option value="">- Pilih -</option>
    <option value="1">1 - KERJA</option>
    <option value="2">2 - LIBUR</option>
    <option value="3">3 - SAKIT</option>
    <option value="4">4 - PERMISI DIBAYAR</option>
    <option value="5">5 - CUTI HAMIL</option>
    <option value="6">6 - CUTI TAHUNAN</option>
    <option value="7">7 - STARTING HK</option>
    <option value="8">8 - MANGKIR</option>
    <option value="9">9 - END KONTRAK</option>
    <option value="A">A - CUTI PANJANG</option>
    <option value="B">B - CUTI MBT</option>
</select>

                            </td>
                            <td><input type="text" name="data[${index}][pekerjaan]" class="form-control" value="${item.pekerjaan || ''}" placeholder="Isi pekerjaan"></td>
                            <td><input type="text" name="data[${index}][lokasi]" class="form-control" value="${item.lokasi || ''}"></td>
                        </tr>
                    `;
                });
            } else {
                rows = '<tr><td colspan="18" class="text-center">Tidak ada data karyawan</td></tr>';
            }
            $('#tabel-karyawan tbody').html(rows);
        }).fail(function(xhr) {
            console.error("Gagal ambil karyawan:", xhr.responseText);
            alert('Gagal memuat data karyawan');
        });
    }
});

    $('#form-komoditi-teh').on('submit', function (e) {
        e.preventDefault();

        if (!$('#reg_mandor').val()) {
            alert('Silakan pilih mandor terlebih dahulu');
            return;
        }

        const payload = {
            tanggal: $('#tanggal').val(),
            kd_afd: $('#kd_afd').val(),
            reg_mandor: $('#reg_mandor').val(),
            data: []
        };

        $('#tabel-karyawan tbody tr').each(function () {
            const row = $(this);
            const reg = row.find('input[name*="[reg]"]').val();
            const absen = row.find('select[name*="[absen]"]').val();
            const pekerjaan = row.find('input[name*="[pekerjaan]"]').val();

            if (absen || pekerjaan) {
                let obj = {
                    reg: reg,
                    absen: absen,
                    pekerjaan: pekerjaan,
                    lokasi: row.find('input[name*="[lokasi]"]').val(),
                    tahun_tanam: row.find('input[name*="[tahun_tanam]"]').val(),
                    luas_jelajah: row.find('input[name*="[luas_jelajah]"]').val(),
                    satuan_luas: row.find('input[name*="[satuan_luas]"]').val(),
                    hasil_panen: row.find('input[name*="[hasil_panen]"]').val(),
                    kg_pikul: row.find('input[name*="[kg_pikul]"]').val(),
                    sts_pikul: row.find('input[name*="[sts_pikul]"]').val(),
                    ambb: row.find('input[name*="[ambb]"]').val(),
                    mesin_petik: row.find('input[name*="[mesin_petik]"]').val(),
                    jendangan: row.find('input[name*="[jendangan]"]').val()
                };
                payload.data.push(obj);
            }
        });

        if (payload.data.length === 0) {
            alert('Tidak ada karyawan yang diisi. Minimal isi absen atau pekerjaan untuk satu karyawan.');
            return;
        }

        $.post('{{ url("checkroll/simpan-absensi") }}', payload)
        .done(function(response) {
            if (response.success) {
                alert(response.message);
                if (response.errors && response.errors.length > 0) {
                    console.error("Errors:", response.errors);
                }
            } else {
                alert('Gagal: ' + response.message);
            }
        })
        .fail(function(xhr) {
            let errorMsg = xhr.responseJSON?.message || 'Terjadi kesalahan jaringan';
            console.error("Full error:", xhr.responseJSON);
            alert('Error: ' + errorMsg);
        });
});
});
</script>
@endpush
