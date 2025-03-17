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
                    <label class="block font-medium text-gray-600">Bulan</label>
                    <input type="month" name="bulan" id="bulan" class="border p-2 w-full rounded-lg focus:ring-2 focus:ring-blue-400" required>
                </div>

                <!-- Dropdown Kd.Afd/Bagan -->
                <div>
                    <label class="block font-medium text-gray-600">Kd.Afd/Bagan:</label>
                    <select name="kd_afd" id="kd_afd" class="border p-2 w-full rounded-lg bg-white focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Pilih</option>
                        @foreach ($dataAfdeling as $afdeling)
                            <option value="{{ $afdeling->KodeAfdeling }}">{{ $afdeling->NamaAfdeling }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Plant -->
                <div>
                    <label class="block font-medium text-gray-600">Plant</label>
                    <input type="text" id="plant" class="border p-2 w-full rounded-lg bg-gray-100" readonly>
                </div>

                <!-- Dropdown Reg. Mandor -->
                <div>
                    <label class="block font-medium text-gray-600">Reg. Mandor</label>
                    <select name="reg_mandor" id="reg_mandor" class="border p-2 w-full rounded-lg bg-white focus:ring-2 focus:ring-blue-400" required>
                        <option value="">Pilih Kd.Afd terlebih dahulu</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="mt-6 text-right">
                <button type="button" id="btn-simpan" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition">
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
document.addEventListener("DOMContentLoaded", function() {
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
                data.forEach(mandor => {
                    let option = document.createElement("option");
                    option.value = mandor.REG;
                    option.textContent = mandor.NAMA;
                    regMandorDropdown.appendChild(option);
                });
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
            plantField.value = data ? data.Plant : "";
            await fetchMandor(kdAfd);
        } catch (error) {
            console.error("Error:", error);
            plantField.value = "Gagal memuat data";
        }
    }

    kdAfdDropdown.addEventListener("change", async function() {
        const kdAfd = this.value;
        await fetchPlant(kdAfd);
    });

    btnSimpan.addEventListener("click", async function() {
        const bulan = bulanField.value;
        const kdAfd = kdAfdDropdown.value;
        const regMandor = regMandorDropdown.value;

        if (!bulan) {
            alert("Silakan pilih bulan terlebih dahulu.");
            return;
        }

        try {
            const response = await fetch("/mandor-karyawan/get-data", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({ bulan, kd_afd: kdAfd, reg_mandor: regMandor })
            });

            const data = await response.json();
            console.log(data);
            const tableBody = document.querySelector("#tabel-hasil tbody");
            tableBody.innerHTML = "";

            if (data.length > 0) {
                data.forEach(row => {
                    const newRow = tableBody.insertRow();
                    newRow.innerHTML = `
                        <td class="border p-2">${row.REG}</td>
                        <td class="border p-2">${row.REG_SAP}</td>
                        <td class="border p-2">${row.SIPIL}</td>
                        <td class="border p-2">${row.NAMA}</td>
                        <td class="border p-2">${row.NAMA_JAB}</td>
                    `;
                });
            } else {
                tableBody.innerHTML = `
                    <tr class="bg-red-100 text-red-600">
                        <td class="border p-2 text-center" colspan="5">Tidak ada data ditemukan.</td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error("Error:", error);
        }
    });
});
</script>
