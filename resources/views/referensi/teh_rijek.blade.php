@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Teh Rijek & Alokasi</h2>

    <div class="mb-3">
        <label for="tanggal">Tanggal:</label>
        <input type="date" id="tanggal" class="form-control" value="{{ now()->format('Y-m-d') }}">
    </div>

    <button class="btn btn-primary mb-3" id="btnLoad">Tampilkan</button>

    <div class="table-responsive">
        <table class="table table-bordered" id="tabelRijek">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Kode Brand</th>
                    <th>Nama Brand</th>
                    <th>% Rijek</th>
                    <th>Nomor BA</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="4" class="text-center">Silakan pilih tanggal dan klik "Tampilkan"</td></tr>
            </tbody>
        </table>
    </div>

    <button class="btn btn-success mt-3" id="btnSimpan">Simpan</button>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btnLoad').addEventListener('click', function () {
    const tanggal = document.getElementById('tanggal').value;
    console.log("Memuat data untuk tanggal: " + tanggal); // LOG UNTUK CEK

    fetch(`/referensi/teh-rijek/load?tanggal=${tanggal}`)
        .then(response => response.json())
        .then(data => {
            console.log("Data diterima:", data); // LOG UNTUK CEK

            const tbody = document.querySelector('#tabelRijek tbody');
            tbody.innerHTML = '';

            if (!data.data || data.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>`;
                return;
            }

            data.data.forEach((item, index) => {
    const row = `
        <tr>
            <td>${index + 1}</td>
            <td>${item.KodeBranded}</td>
            <td>${item.NamaBranded}</td>
            <td>
                <input type="number" step="0.01" class="form-control input-rijke" 
                       data-kdbrand="${item.KodeBranded}" value="${item.Rijek ?? 0}">
            </td>
            <td>
                <input type="text" class="form-control input-ba" 
                       data-kdbrand="${item.KodeBranded}" value="${item.NomorBA ?? ''}">
            </td>
        </tr>
    `;
    tbody.innerHTML += row;
});

        })
        .catch(error => {
            console.error("Gagal fetch data:", error);
            alert('Gagal memuat data.');
        });
});

document.getElementById('btnSimpan').addEventListener('click', function () {
    const rows = document.querySelectorAll('#tabelRijek tbody tr');
    const tanggal = document.getElementById('tanggal').value;
    const data = [];

    rows.forEach(row => {
        const inputRijek = row.querySelector('.input-rijke');
        const inputBA = row.querySelector('.input-ba');

        if (!inputRijek || !inputBA) return;

        data.push({
            KodeBranded: inputRijek.dataset.kdbrand,
            Rijek: inputRijek.value,
            NomorBA: inputBA.value
        });
    });

    fetch('/referensi/teh-rijek/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            tanggal: tanggal,
            data: data
        })
    })
    .then(response => response.json())
    .then(res => {
        if (res.status === 'success') {
            alert(res.message || 'Data berhasil disimpan!');
        } else {
            alert('Gagal menyimpan data!');
        }
    })
    .catch(error => {
        console.error('Gagal mengirim data:', error);
        alert('Terjadi kesalahan saat menyimpan data.');
    });
});
</script>
@endpush
