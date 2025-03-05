<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kebun;

class KebunSeeder extends Seeder
{
    public function run()
    {
        $kebun = [
            ['nama_kebun' => '68 - PARAKAN SALAK'],
            ['nama_kebun' => '45 - CIBALUNG'],
            ['nama_kebun' => '30 - SUKABUMI'],
        ];

        Kebun::insert($kebun);
    }
}