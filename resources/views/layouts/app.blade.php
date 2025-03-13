<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPDK.net')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">SPDK.net</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
    
                    <!-- REFERENSI -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarReferensi" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Referensi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('referensi.mandor_panen') }}">Mandor | Tanaman yg membawahi Mandor Panen</a></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Mandor Karyawan
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="{{ route('referensi.mandor_karyawan_input') }}">Input Mandor Karyawan</a></li>
                                    <li><a class="dropdown-item" href="#">Pergantian Mandor Karyawan</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    TEH
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="#">Teh Rijek (Rijek & Alokasi)</a></li>
                                    <li><a class="dropdown-item" href="#">Ref Kode Branded</a></li>
                                    <li><a class="dropdown-item" href="#">Ref Karyawan PJTK</a></li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    KARET
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="#">Klasifikasi Karyawan Penderes </a></li>
                                    <li><a class="dropdown-item" href="#">Cuaca Tidak Normal</a></li>
                                    <li><a class="dropdown-item" href="#">Daftar Karyawan Penderes Serep</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    RKAP
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="#">TEH</a></li>
                                    <li><a class="dropdown-item" href="#">KARET</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Legalitas   
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="#">Legalitas (Kebun, Agrowisata & PKS)</a></li>
                                    <li><a class="dropdown-item" href="#">Legalitas IHT</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
    
                    <!-- HRD -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarHRD" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            HRD
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Mutasi Dan Update   
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="#">Mutasi Afdeling,Budidaya,Merk Pabrik</a></li>
                                    <li><a class="dropdown-item" href="#">Update Afdeling,Budidaya,Merk Pabrik</a></li>
                                    <li><a class="dropdown-item" href="#">Update Data Master Pribadi Karyawan</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="#">Mutasi Keluarga</a></li>
                            <li><a class="dropdown-item" href="#">Pendidikan Formal</a></li>
                            <li><a class="dropdown-item" href="#">Pelanggaran & Hukuman</a></li>
                            <li><a class="dropdown-item" href="#">Registrasi PKWT Baru</a></li>
                            <li><a class="dropdown-item" href="#">Update Identitas PKWT</a></li>
                            <li><a class="dropdown-item" href="#">Curriculum Vitae (CV)</a></li>
                        </ul>
                    </li>
    
                    <!-- CHECKROLL -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarCheckroll" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Checkroll
                        </a>
                        <ul class="dropdown-menu">
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Absensi Pestasi Kerja Kemandoran   
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="#">Komoditi Teh</a></li>
                                    <li><a class="dropdown-item" href="#">Komoditi Karet</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="#">Absensi Non Kemandoran</a></li>
                            <li><a class="dropdown-item" href="#">Teh Hasil sortasi Kering</a></li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Rapel/Lembur/Premi/Konduite/Sanksi 
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="#">Lembur</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Lembur Non Kemandoran</a></li>
                                            <li><a class="dropdown-item" href="#">Lembur Kemandoran</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="#">Premi Non Otomatis</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Premi Non Kemandoran</a></li>
                                            <li><a class="dropdown-item" href="#">Premi Kemandoran</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="#">Rapel</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Rapel Non Kemandoran</a></li>
                                            <li><a class="dropdown-item" href="#">Rapel Kemandoran</a></li>
                                            <li><a class="dropdown-item" href="#">Daftar Rapel Non Kemandoran</a></li>
                                            <li><a class="dropdown-item" href="#">Daftar Rapel Kemandoran</a></li>
                                        </ul>
                                    </li>
                                </ul>                                
                            </li>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Potongan Gaji (Kebun)  
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li><a class="dropdown-item" href="#">Non Kemandoran</a></li>
                                    <li><a class="dropdown-item" href="#">Kemandoran</a></li>
                                </ul>
                            </li><li class="nav-item dropdown">
                                <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Prestasi Kerja PJTK 
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="#">Input TEH</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">PJTK PANEN TEH BORONG MURNI</a></li>
                                            <li><a class="dropdown-item" href="#">PJTK PANEN TEH EKSTRA</a></li>
                                            <li><a class="dropdown-item" href="#">PJTK PEMEL/LAIN2</a></li>
                                            <li><a class="dropdown-item" href="#">PJTK PENGOLAHAN</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item dropdown-toggle" href="#">Input KARET</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">PJTK KARET TEH BORONG MURNI</a></li>
                                            <li><a class="dropdown-item" href="#">PJTK KARET TEH EKSTRA</a></li>
                                            <li><a class="dropdown-item" href="#">PJTK PANEN KARET SEREP</a></li>
                                            <li><a class="dropdown-item" href="#">PJTK PEMEL/LAIN2</a></li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item" href="#">Output Teh/Non inti</a></li>
                                    <li><a class="dropdown-item" href="#">Output Karet</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="#">POSTING</a></li>
                        </ul>
                    </li>
    
                    <!-- REPORTING -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarReporting" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Reporting
                        </a>
                        <ul class="dropdown-menu">
                        </li><li class="nav-item dropdown">
                            <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Report Absensi/Kehadiran/MPP
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                <li><a class="dropdown-item" href="#">Per Kebun<a></li>
                                <li><a class="dropdown-item" href="#">Per Afdeling</a></li>
                                <li><a class="dropdown-item" href="#">Daftar MPP Per Afdeling</a></li>
                                <li><a class="dropdown-item" href="#">Rekapitulasi Daftar MPP</a></li>
                                <li><a class="dropdown-item" href="#">Kontrol input Absensi</a></li>
                            </ul>
                        </li>
                        </li><li class="nav-item dropdown">
                            <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Report Premo Panen & Tekpol
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Teh</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Panen (Premi & Denda Karyawan)</a></li>
                                        <li><a class="dropdown-item" href="#">Panen (Premi & Denda Pengawas Panen)</a></li>
                                        <li><a class="dropdown-item" href="#">Daftar Premi Pok</a></li>
                                        <li><a class="dropdown-item" href="#">Pengolahan (Premi Karyawan)</a></li>
                                        <li><a class="dropdown-item" href="#">Pengolahan (Mandor dll)</a></li>
                                        <li><a class="dropdown-item" href="#">Teknik Pengolahan</a></li>
                                        <li><a class="dropdown-item" href="#">Cek Kode Pabrik Kary Pengolahan</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Karet</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Panen (Premi & Denda Karyawan Deres)</a></li>
                                        <li><a class="dropdown-item" href="#">Panen (Premi & Mandor Pengawas)</a></li>
                                        <li><a class="dropdown-item" href="#">Cuaca Tidak Normal</a></li>
                                        <li><a class="dropdown-item" href="#">Pengolahan (Premi Kuantitas)</a></li>
                                        <li><a class="dropdown-item" href="#">Pengolahan (Premi Kualitas)</a></li>
                                        <li><a class="dropdown-item" href="#">Teknik Pengolahan</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        </li><li class="nav-item dropdown">
                            <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Daftar Gaji/Upah
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Periode 1 (Transfer)</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Non Kemandoran</a></li>
                                        <li><a class="dropdown-item" href="#">Kemandoran</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#">Periode 2 (Real 1 Bulan)</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Non Kemandoran</a></li>
                                        <li><a class="dropdown-item" href="#">Kemandoran</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </li><li class="nav-item dropdown">
                        <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Laporan Harian (PB-10)
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                            <li><a class="dropdown-item" href="#">Per Afdeling<a></li>
                            <li><a class="dropdown-item" href="#">Per Mandor</a></li>
                            <li><a class="dropdown-item" href="#">Rekap</a></li>
                        </ul>
                    </li>
                    </li><li class="nav-item dropdown">
                        <a class="dropdown-item dropdown-toggle" href="#" id="tehDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Premi/Lembur/UM Upah & Pot Bulan ini
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="tehDropdown">
                            <li><a class="dropdown-item" href="#">Non Kemandoran<a></li>
                            <li><a class="dropdown-item" href="#">Kemandoran</a></li>
                        </ul>
                    </li>
                    <li><a class="dropdown-item" href="#">Daftar Kekuatan Karyawan (Final)</a></li>
                    <li><a class="dropdown-item" href="#">Daftar Kekuatan Karyawan (Draft)</a></li>

                        </ul>
                    </li>
    
                    <!-- EAP/ETL -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarEAP" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            EAP/ETL
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Slip EAP</a></li>
                            <li><a class="dropdown-item" href="#">Slip ETL</a></li>
                        </ul>
                    </li>
    
                    <!-- TOOLS -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarTools" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Tools
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Backup CSV</a></li>
                            <li><a class="dropdown-item" href="#">Set Default Printer</a></li>
                            <li><a class="dropdown-item" href="#">User Management</a></li>
                        </ul>
                    </li>
    
                    <!-- WINDOWS -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarWindows" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Windows
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">About/Release</a></li>
                            <li><a class="dropdown-item" href="#">Status</a></li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                            <li><a class="dropdown-item" href="#">Quit</a></li>
                        </ul>
                    </li>
    
                </ul>
            </div>
        </div>
    </nav>
    

    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
