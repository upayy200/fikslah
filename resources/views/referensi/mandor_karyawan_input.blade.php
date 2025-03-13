
<div class="container mx-auto mt-4">
    <div class="bg-white shadow p-4">
        <form action="{{ route('referensi.mandor_karyawan_input.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block">Bulan</label>
                    <input type="date" name="bulan" class="border p-2 w-full" required>
                </div>
                <div>
                    <label class="block">Kd.Afd/Bagan:</label>
                    <select name="kd_afd" class="border p-2 w-full">
                        <option>Pilih</option>
                        <option value="A1">A1</option>
                        <option value="A2">A2</option>
                    </select>
                </div>
                <div>
                    <label class="block">Plant</label>
                    <input type="text" name="plant" class="border p-2 w-full">
                </div>
                <div>
                    <label class="block">Reg.Mandor:</label>
                    <div class="flex">
                        <input type="text" name="reg_mandor" class="border p-2 w-full">
                        <button class="bg-gray-500 text-white px-3 ml-2">R</button>
                    </div>
                </div>
                <div class="col-span-3">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2">Simpan</button>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white shadow mt-4">
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Register</th>
                    <th class="border p-2">Reg. SAP</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Jabatan</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataDik as $item)
                <tr>
                    <td class="border p-2">{{ $item->REG }}</td> <!-- Ganti id dengan REG -->
                    <td class="border p-2">{{ $item->REG_SAP }}</td>
                    <td class="border p-2">{{ $item->NAMA }}</td>
                    <td class="border p-2">{{ $item->NAMA_JAB }}</td>
                    <td class="border p-2">{{ $item->KD_AFD }}</td>
                    <td class="border p-2">
                        <a href="{{ route('referensi.mandor_karyawan_input.edit', $item->REG) }}" class="text-blue-500">Edit</a>
                        <form action="{{ route('referensi.mandor_karyawan_input.destroy', $item->REG) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>                      
        </table>
    </div>
</div>

