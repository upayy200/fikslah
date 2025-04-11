@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-3">Ref Kode Branded</h4>

    <div class="mb-3">
        <label for="kd_afd">Pilih Afdeling:</label>
        <select id="kd_afd" class="form-select">
            <option value="">Pilih KD_AFD</option>
            @foreach ($afdList as $afd)
                <option value="{{ $afd }}">{{ $afd }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="from_date">Dari Tanggal:</label>
        <input type="date" id="from_date" class="form-control">
    </div>

    <button id="btn-show-data" class="btn btn-primary mb-3">Tampilkan</button>

    <table class="table table-bordered" id="result-table" style="display: none;">
        <thead class="table-dark">
            <tr>
                <th>No.</th>
                <th>Reg. Mandor</th>
                <th>Register</th>
                <th>Status</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Kode Branded</th>
            </tr>
        </thead>
        <tbody id="result-body"></tbody>
    </table>

    <button id="btn-save" class="btn btn-success mt-3" style="display: none;">Simpan</button>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#btn-show-data').click(function () {
        let afd = $('#kd_afd').val();
        let tanggal = $('#from_date').val();

        if (!afd || !tanggal) {
            alert("Silakan pilih Afdeling dan Tanggal terlebih dahulu.");
            return;
        }

        $.get('/referensi/ref-kode-branded/data', { kd_afd: afd, tanggal: tanggal }, function (data) {
            let $body = $('#result-body');
            $body.empty();

            if (data.length === 0) {
                alert("Data tidak ditemukan.");
                $('#result-table').hide();
                $('#btn-save').hide();
                return;
            }

            data.forEach((item, index) => {
                $body.append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.regmdr ?? ''}</td>
                        <td>${item.register}</td>
                        <td>${item.sts}</td>
                        <td>${item.nama}</td>
                        <td>${item.jabatan}</td>
                        <td>
                            <input type="text" class="form-control form-control-sm kode-branded-input"
                                   data-register="${item.register}"
                                   data-original="${item.kdbranded ?? ''}"
                                   value="${item.kdbranded ?? ''}">
                        </td>
                    </tr>
                `);
            });

            $('#result-table').show();
            $('#btn-save').show();
        });
    });

    // Validasi dan highlight perubahan
    $(document).on('input', '.kode-branded-input', function () {
        const val = $(this).val();
        const original = $(this).attr('data-original');

        if (!/^\d+$/.test(val)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }

        if (val !== original) {
            $(this).addClass('bg-warning');
        } else {
            $(this).removeClass('bg-warning');
        }
    });

    // Simpan data
    $(document).on('click', '#btn-save', function () {
        let updates = [];

        $('.kode-branded-input').each(function () {
            const val = $(this).val();
            const original = $(this).attr('data-original');
            const register = $(this).data('register');

            if (val !== original) {
                updates.push({ register: register, kdbranded: val });
            }
        });

        if (updates.length === 0) {
            alert("Tidak ada perubahan.");
            return;
        }

        let isValid = updates.every(u => /^\d+$/.test(u.kdbranded));
        if (!isValid) {
            alert("Kode Branded tidak boleh kosong dan hanya boleh angka.");
            return;
        }

        $.ajax({
            url: '{{ url("referensi/ref-kode-branded/update") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                updates: updates
            },
            success: function () {
                alert("Data berhasil disimpan.");
                $('#btn-show-data').click();
            },
            error: function () {
                alert("Gagal menyimpan data.");
            }
        });
    });
});
</script>

<style>
    .is-invalid {
        border-color: red !important;
    }
    .bg-warning {
        background-color: #fff3cd !important;
    }
</style>
@endpush
