<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mandor Panen</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-5 bg-gray-100">
    <div class="bg-white p-5 shadow-md rounded-lg">
        <!-- Header dengan tombol aksi -->
        <div class="flex gap-2 mb-4">
            <button class="px-4 py-2 bg-blue-500 text-white rounded">Simpan</button>
            <button class="px-4 py-2 bg-gray-500 text-white rounded">Cetak</button>
            <button class="px-4 py-2 bg-yellow-500 text-white rounded">Batal</button>
            <button class="px-4 py-2 bg-green-500 text-white rounded">Kalkulator</button>
            <button class="px-4 py-2 bg-red-500 text-white rounded">Hapus Record</button>
            <button onclick="window.history.back()" class="px-4 py-2 bg-red-500 text-white rounded">Kembali</button>
        </div>

        <!-- Form Tambah Data -->
        <form action="{{ route('mandor_panen.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium">Bulan</label>
                    <input type="date" name="bulan" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Kd.Afd/Bagian</label>
                    <input type="text" name="kd_afd_bagian" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Plant</label>
                    <input type="text" name="plant" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Reg.MB</label>
                    <input type="text" name="reg_mb" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Regmdr</label>
                    <input type="text" name="regmdr" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Regmdr.SAP</label>
                    <input type="text" name="regmdr_sap" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Status</label>
                    <input type="text" name="status" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Nama</label>
                    <input type="text" name="nama" class="w-full px-2 py-1 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Jabatan</label>
                    <input type="text" name="jabatan" class="w-full px-2 py-1 border rounded" required>
                </div>
            </div>
        
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Simpan</button>
        </form>
        

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">Regmdr</th>
                        <th class="border px-4 py-2">Regmdr.SAP</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">Jabatan</th>
                        <th class="border px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($mandorPanen as $data)
                    <tr>
                        <td class="border px-4 py-2">{{ $data->regmdr }}</td>
                        <td class="border px-4 py-2">{{ $data->regmdr_sap }}</td>
                        <td class="border px-4 py-2">{{ $data->status }}</td>
                        <td class="border px-4 py-2">{{ $data->nama }}</td>
                        <td class="border px-4 py-2">{{ $data->jabatan }}</td>
                        <td class="border px-4 py-2">
                            <a href="{{ route('mandor_panen.edit', $data->id) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
                            <form action="{{ route('mandor_panen.destroy', $data->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
