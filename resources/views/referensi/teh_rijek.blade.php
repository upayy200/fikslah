@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Teh Rijek & Alokasi</h4>

    <div class="mb-3">
        <label for="tanggal">Tanggal:</label>
        <input type="date" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
        <button id="readBtn" class="btn btn-primary mt-2">R</button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered mt-3" id="tabel-rijek" style="display: none;">
            <thead class="table-dark">
                <tr>
                    <th>Kode Pabrik</th>
                    <th>Nama Pabrik</th>
                    <th>(%) Rijek</th>
                    <th>Nomor BA</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(document).ready(function () {
    // Tambahkan ini untuk pastikan CSRF token ikut
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#readBtn').click(function () {
    const tgl = $('#tanggal').val();
    if (!tgl) return alert('Pilih tanggal terlebih dahulu');

    $.get('/referensi/teh-rijek/load', { tanggal: tgl }, function (data) {
        let tbody = $('#tabel-rijek tbody');
        tbody.empty();

        if (data.length > 0) {
            data.forEach(row => {
                tbody.append(`
                    <tr>
                        <td>${row.Kdbrand}</td>
                        <td>${row.nmbranded}</td>
                        <td>
                            <input type="number" class="form-control rijek-input" 
                                   data-kd="${row.Kdbrand}" 
                                   value="${row.Rijek ?? 0}" 
                                   step="0.01" min="0" />
                        </td>
                        <td>${row.No_BA ?? ''}</td>
                    </tr>
                `);
            });
            $('#tabel-rijek').show();
        } else {
            $('#tabel-rijek').hide();
            alert('Data tidak ditemukan untuk tanggal tersebut.');
        }
    }).fail(function(xhr) {
        alert('Gagal mengambil data: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
    });
});


    $(document).on('change', '.rijek-input', function () {
        let input = $(this);
        let Rijek = input.val();
        let Kdbrand = input.data('kd');
        let Tanggal = $('#tanggal').val();

        $.post('/referensi/teh-rijek/update', {
            Rijek: Rijek,
            Kdbrand: Kdbrand,
            Tanggal: Tanggal
        }, function (res) {
            console.log(res.success);
        }).fail(function (xhr) {
            alert('Gagal update: ' + (xhr.responseJSON?.message ?? 'Terjadi kesalahan.'));
        });
    });
});
</script>
@endpush
