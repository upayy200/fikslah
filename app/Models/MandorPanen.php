<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MandorPanen extends Model
{
    use HasFactory;

    protected $table = 'mandor_panen'; // Pastikan tabel sesuai dengan database

    protected $fillable = [
        'bulan',
        'kd_afd_bagian',
        'plant',
        'reg_mb',
        'regmdr',
        'regmdr_sap',
        'status',
        'nama',
        'jabatan'
    ];
}
