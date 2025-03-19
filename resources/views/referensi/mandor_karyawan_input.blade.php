<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<div class="container mx-auto mt-6 p-4">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Form Data Mandor</h2>
        <form id="form-mandor">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Input Bulan -->
                <div>
                    <label class="block font-medium text-gray-600">Tanggal</label>
                    <input type="datetime-local" name="tanggal" id="tanggal"
                        class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Dropdown Kd.Afd/Bagan -->
                <div>
                    <label class="block font-medium text-gray-600">Kd.Afd/Bagan:</label>
                    <select name="kd_afd" id="kd_afd"
                        class="border p-2 w-full rounded-lg bg-white focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Pilih</option>
                        {{-- @foreach ($dataAfdeling as $afdeling)
                        <option value="{{ $afdeling->KodeAfdeling }}">{{ $afdeling->NamaAfdeling }}</option>
                        @endforeach --}}
                        @php
                            $query = DB::connection("AMCO")->TABLE("AMCO_Afdeling")->WHERE("KodeKebun", session()->get('selected_kebun'))->get();
                        @endphp

                        @foreach ($query as $item)
                            <option value="{{ $item->KodeAfdeling }}">{{ $item->NamaAfdeling }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Plant -->
                <div>
                    <label class="block font-medium text-gray-600">Plant</label>
                    <input type="text" id="plant" class="border p-2 w-full rounded-lg bg-gray-100"
                        value="{{ session()->get("selected_kebun") }}" readonly>
                </div>

                <!-- Dropdown Reg. Mandor -->
                <div>
                    <label class="block font-medium text-gray-600">Reg. Mandor</label>
                    <select name="reg_mandor" id="reg_mandor"
                        class="border p-2 w-full rounded-lg bg-white focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Pilih Kd.Afd terlebih dahulu</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="mt-6 text-right">
                <button type="button" id="btn-simpan"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Tampilkan Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Hasil -->
<div class="container mx-auto mt-6">
    <div class="bg-white shadow-lg rounded-lg p-4">
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Hasil Pencarian</h2>
        <table id="tabel-hasil" class="w-full border-collapse border border-gray-300 rounded-lg">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="border p-2">Register</th>
                    <th class="border p-2">Reg. SAP</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Nama</th>
                    <th class="border p-2">Jabatan</th>
                </tr>
            </thead>
            <tbody class="bg-gray-50">
                <!-- Data akan muncul di sini -->
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const kdAfdDropdown = document.getElementById("kd_afd");
        const regMandorDropdown = document.getElementById("reg_mandor");
        const plantField = document.getElementById("plant");
        const btnSimpan = document.getElementById("btn-simpan");
        const bulanField = document.getElementById("bulan");

        async function fetchMandor(kdAfd) {
            if (!kdAfd) return;
            regMandorDropdown.innerHTML = '<option value="">Memuat...</option>';
            try {
                const response = await fetch(`/referensi/get-mandor/${kdAfd}`);
                const data = await response.json();
                regMandorDropdown.innerHTML = '<option value="">Pilih Mandor</option>';
                if (Array.isArray(data) && data.length > 0) {
                    console.log(data);
                    data.forEach(mandor => {
                        let option = document.createElement("option"); // <option value=""
                        option.value = mandor.REG;
                        option.textContent = mandor.NAMA;
                        regMandorDropdown.appendChild(option);
                    });

                    //foreach($query as $item)
                } else {
                    regMandorDropdown.innerHTML = '<option value="">Tidak ada data mandor.</option>';
                }
            } catch (error) {
                console.error("Error:", error);
                regMandorDropdown.innerHTML = '<option>Gagal memuat data</option>';
            }
        }

        async function fetchPlant(kdAfd) {
            if (!kdAfd) return;
            try {
                const response = await fetch(`/referensi/get-plant/${kdAfd}`);
                const data = await response.json();
                plantField.value = data && data.Plant ? data.Plant : "Data tidak ditemukan";
                await fetchMandor(kdAfd);
            } catch (error) {
                console.error("Error:", error);
                //plantField.value = "Gagal memuat data";
            }
        }

        kdAfdDropdown.addEventListener("change", async function () {
            const kdAfd = this.value;
            await fetchPlant(kdAfd);
        });

        btnSimpan.addEventListener("click", async function () {
            const tanggalValue = document.getElementById("tanggal").value;
            const kdAfd = kdAfdDropdown.value;
            const regMandor = regMandorDropdown.value;

            if (!tanggalValue || !kdAfd || !regMandor) {
                alert("Harap lengkapi semua field wajib!");
                return;
            }

            const tanggal = new Date(tanggalValue);
            if (isNaN(tanggal)) {
                alert("Format tanggal tidak valid!");
                return;
            }

            const formattedDate = tanggal.toISOString().slice(0, 19).replace("T", " ");

            try {
                const response = await fetch("/mandor-karyawan", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        tanggal: formattedDate,
                        kd_afd: kdAfd,
                        reg_mandor: regMandor
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Server error: ${errorText}`);
                }

                const data = await response.json();

                const tableBody = document.querySelector("#tabel-hasil tbody");
                tableBody.innerHTML = data.map(row => `
                <tr>
                    <td class="border p-2">${row.register}</td>
                    <td class="border p-2">${row.RegSAP}</td>
                    <td class="border p-2">${row.sts}</td>
                    <td class="border p-2">${row.Nama}</td>
                </tr>
            `).join("");

            } catch (error) {
                console.error("Error:", error);
                alert("Terjadi kesalahan: " + error.message);
            }
        });

    });
</script>